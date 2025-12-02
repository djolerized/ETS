<?php
/**
 * Plugin Name: US Tax Calculator 2025
 * Description: Provides a shortcode [us_tax_calculator_2025] to calculate federal and state tax outcomes for resident and non-resident scenarios for tax year 2025.
 * Version: 1.0.0
 * Author: OpenAI ChatGPT
 */

if (!defined('ABSPATH')) {
    exit;
}

class USTaxCalculator2025
{
    private $option_federal = 'ustc2025_federal_settings';
    private $option_state = 'ustc2025_state_settings';
    private $states = [
        'Arizona',
        'California',
        'Colorado',
        'Delaware',
        'District of Columbia',
        'Maine',
        'Maryland',
        'Massachusetts',
        'Michigan',
        'Missouri',
        'New Jersey',
        'New York',
        'North Carolina',
        'Rhode Island',
        'South Carolina',
        'Virginia',
        'Wisconsin'
    ];

    public function __construct()
    {
        add_shortcode('us_tax_calculator_2025', [$this, 'render_shortcode']);
        add_action('admin_menu', [$this, 'register_admin_pages']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    private function defaults_federal()
    {
        return [
            'std_deduction' => 15750,
        ];
    }

    private function defaults_states()
    {
        return [
            'Maryland' => ['deduction' => 6550],
            'New Jersey' => ['exemption' => 1000],
            'Massachusetts' => ['exemption' => 1000, 'rate' => 0.05],
            'New York' => ['deduction' => 9000],
            'Delaware' => ['deduction' => 3250, 'personal_tax_credit' => 110, 'full_refund_threshold' => 9400],
            'California' => ['deduction' => 3250, 'personal_tax_credit' => 153],
            'Michigan' => ['deduction' => 5800, 'rate' => 0.0425],
            'Missouri' => ['deduction' => 15750],
            'North Carolina' => ['deduction' => 12750, 'rate' => 0.0425],
            'South Carolina' => ['deduction' => 14600],
            'Wisconsin' => ['deduction_resident' => 14260, 'deduction_nonresident' => 700],
            'Colorado' => ['deduction' => 15600, 'rate' => 0.044],
            'Maine' => ['deduction_resident' => 20150, 'deduction_nonresident' => 5150],
            'Arizona' => ['deduction' => 15600, 'rate' => 0.044],
            'Virginia' => ['deduction' => 15600],
            'Rhode Island' => ['deduction_resident' => 16000, 'deduction_nonresident' => 5100],
            'District of Columbia' => [],
        ];
    }

    public function enqueue_assets()
    {
        $handle = 'ustc2025-styles';
        $css = '.ustc2025-wrapper{font-family:Arial,sans-serif;background:#f8f7fb;padding:8px;border-radius:10px}.ustc2025-card{background:#fff;border:1px solid #e6e2ed;border-radius:10px;box-shadow:0 4px 16px rgba(17,16,62,0.07);padding:20px;margin-bottom:18px}.ustc2025-form-header{font-size:20px;font-weight:600;margin:0 0 12px;color:#2b2341}.ustc2025-form-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px}.ustc2025-field label{display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:#3a3352}.ustc2025-field input[type=number],.ustc2025-field select{width:100%;padding:12px 14px;border:1px solid #d6d1df;border-radius:8px;font-size:14px;color:#2b2341;background:#fff;box-sizing:border-box;transition:border-color .2s,box-shadow .2s}.ustc2025-field input[type=number]:focus,.ustc2025-field select:focus{outline:none;border-color:#5f2f88;box-shadow:0 0 0 2px rgba(95,47,136,0.12)}.ustc2025-actions{margin-top:8px;display:flex;gap:10px;align-items:center}.ustc2025-button{background:#5f2f88;color:#fff;border:none;padding:12px 18px;border-radius:8px;cursor:pointer;font-weight:600;transition:transform .1s ease,box-shadow .2s}.ustc2025-button:hover{transform:translateY(-1px);box-shadow:0 4px 10px rgba(95,47,136,0.25)}.ustc2025-button:disabled{opacity:.6;cursor:not-allowed}.ustc2025-reset{background:transparent;color:#5f2f88;border:1px solid #c9c2d7;padding:12px 18px;border-radius:8px;cursor:pointer}.ustc2025-results{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px}.ustc2025-column h3{margin-top:0;color:#2b2341;font-size:16px}.ustc2025-column p{margin:6px 0;font-size:14px;color:#4a425c}.ustc2025-owe{color:#e53935;font-weight:700}.ustc2025-refund{color:#2e8b57;font-weight:700}.ustc2025-tab-nav a{margin-right:12px;text-decoration:none;padding:8px 12px;border-radius:6px;border:1px solid #ccc;background:#f5f5f5}.ustc2025-tab-nav a.active{background:#5f2f88;color:#fff;border-color:#5f2f88}.ustc2025-breakdown{background:#fafafa;border:1px solid #e0e0e0;border-radius:8px;padding:12px;margin-top:12px}.ustc2025-header{font-size:18px;font-weight:600;margin-bottom:8px;color:#5f2f88}.ustc2025-row{display:flex;gap:10px;flex-wrap:wrap}.ustc2025-row .ustc2025-col{flex:1;min-width:200px}';
        wp_register_style($handle, false);
        wp_enqueue_style($handle);
        wp_add_inline_style($handle, $css);
    }

    public function register_admin_pages()
    {
        add_menu_page(
            __('US Tax Calculator 2025', 'ustc2025'),
            __('US Tax Calculator 2025', 'ustc2025'),
            'manage_options',
            'ustc2025',
            [$this, 'render_admin_page'],
            'dashicons-calculator',
            65
        );
    }

    public function register_settings()
    {
        register_setting('ustc2025_federal_group', $this->option_federal);
        register_setting('ustc2025_state_group', $this->option_state);
    }

    private function get_federal_settings()
    {
        $saved = get_option($this->option_federal, []);
        return wp_parse_args($saved, $this->defaults_federal());
    }

    private function get_state_settings()
    {
        $saved = get_option($this->option_state, []);
        return wp_parse_args($saved, $this->defaults_states());
    }

    public function render_admin_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'federal';
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('US Tax Calculator 2025', 'ustc2025') . '</h1>';
        echo '<nav class="ustc2025-tab-nav">';
        echo '<a class="' . ($active_tab === 'federal' ? 'active' : '') . '" href="?page=ustc2025&tab=federal">' . esc_html__('Federal settings', 'ustc2025') . '</a>';
        echo '<a class="' . ($active_tab === 'states' ? 'active' : '') . '" href="?page=ustc2025&tab=states">' . esc_html__('State settings', 'ustc2025') . '</a>';
        echo '<a class="' . ($active_tab === 'control' ? 'active' : '') . '" href="?page=ustc2025&tab=control">' . esc_html__('Control / Manual Check', 'ustc2025') . '</a>';
        echo '</nav>';

        if ($active_tab === 'federal') {
            $settings = $this->get_federal_settings();
            echo '<div class="ustc2025-card">';
            echo '<form method="post" action="options.php">';
            settings_fields('ustc2025_federal_group');
            echo '<div class="ustc2025-header">' . esc_html__('Federal settings', 'ustc2025') . '</div>';
            echo '<p><label>' . esc_html__('Standard deduction (USD)', 'ustc2025') . '</label><br />';
            echo '<input type="number" name="' . esc_attr($this->option_federal) . '[std_deduction]" value="' . esc_attr($settings['std_deduction']) . '" step="0.01" min="0"></p>';
            submit_button();
            echo '</form>';
            echo '</div>';
        } elseif ($active_tab === 'states') {
            $settings = $this->get_state_settings();
            echo '<div class="ustc2025-card">';
            echo '<form method="post" action="options.php">';
            settings_fields('ustc2025_state_group');
            echo '<div class="ustc2025-header">' . esc_html__('State settings', 'ustc2025') . '</div>';
            foreach ($this->states as $state) {
                $state_settings = isset($settings[$state]) ? $settings[$state] : [];
                echo '<h3>' . esc_html($state) . '</h3>';
                foreach ($state_settings as $key => $value) {
                    echo '<p><label>' . esc_html(ucwords(str_replace('_', ' ', $key))) . '</label><br />';
                    echo '<input type="number" step="0.0001" name="' . esc_attr($this->option_state) . '[' . esc_attr($state) . '][' . esc_attr($key) . ']" value="' . esc_attr($value) . '" /></p>';
                }
            }
            submit_button();
            echo '</form>';
            echo '</div>';
        } else {
            $this->render_control_tool();
        }
        echo '</div>';
    }

    private function render_control_tool()
    {
        $state_settings = $this->get_state_settings();
        $federal_settings = $this->get_federal_settings();

        $gross = isset($_POST['ustc_control_gross']) ? floatval($_POST['ustc_control_gross']) : '';
        $fwh = isset($_POST['ustc_control_fwh']) ? floatval($_POST['ustc_control_fwh']) : '';
        $swh = isset($_POST['ustc_control_swh']) ? floatval($_POST['ustc_control_swh']) : '';
        $state = isset($_POST['ustc_control_state']) ? sanitize_text_field($_POST['ustc_control_state']) : 'Arizona';
        $residency = isset($_POST['ustc_control_residency']) ? sanitize_text_field($_POST['ustc_control_residency']) : 'resident';

        echo '<div class="ustc2025-card">';
        echo '<div class="ustc2025-header">' . esc_html__('Control / Manual Check', 'ustc2025') . '</div>';
        echo '<form method="post">';
        echo '<div class="ustc2025-row">';
        echo '<div class="ustc2025-col"><label>' . esc_html__('Gross income', 'ustc2025') . '</label><input type="number" name="ustc_control_gross" step="0.01" required value="' . esc_attr($gross) . '" /></div>';
        echo '<div class="ustc2025-col"><label>' . esc_html__('Federal withholding', 'ustc2025') . '</label><input type="number" name="ustc_control_fwh" step="0.01" required value="' . esc_attr($fwh) . '" /></div>';
        echo '<div class="ustc2025-col"><label>' . esc_html__('State withholding', 'ustc2025') . '</label><input type="number" name="ustc_control_swh" step="0.01" required value="' . esc_attr($swh) . '" /></div>';
        echo '</div>';
        echo '<div class="ustc2025-row">';
        echo '<div class="ustc2025-col"><label>' . esc_html__('State', 'ustc2025') . '</label><select name="ustc_control_state">';
        foreach ($this->states as $st) {
            echo '<option value="' . esc_attr($st) . '"' . selected($state, $st, false) . '>' . esc_html($st) . '</option>';
        }
        echo '</select></div>';
        echo '<div class="ustc2025-col"><label>' . esc_html__('Residency', 'ustc2025') . '</label><select name="ustc_control_residency">';
        echo '<option value="resident"' . selected($residency, 'resident', false) . '>' . esc_html__('Resident', 'ustc2025') . '</option>';
        echo '<option value="nonresident"' . selected($residency, 'nonresident', false) . '>' . esc_html__('Non-resident', 'ustc2025') . '</option>';
        echo '</select></div>';
        echo '</div>';
        submit_button(__('Run calculation', 'ustc2025'));
        echo '</form>';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ustc_control_gross'])) {
            $federal = $this->calculate_federal($gross, $fwh, $residency, $federal_settings);
            $state_result = $this->calculate_state($state, $gross, $swh, $residency, $state_settings[$state]);
            echo '<div class="ustc2025-breakdown">';
            echo '<h4>' . esc_html__('Federal breakdown', 'ustc2025') . '</h4>';
            echo wp_kses_post($this->format_breakdown($federal['breakdown']));
            echo '<p><strong>' . esc_html__('Tax', 'ustc2025') . ':</strong> ' . esc_html(number_format($federal['tax'], 2)) . '</p>';
            echo '<p><strong>' . esc_html__('Difference', 'ustc2025') . ':</strong> ' . esc_html(number_format($federal['tax_diff'], 2)) . '</p>';
            echo '<h4>' . esc_html__('State breakdown', 'ustc2025') . '</h4>';
            echo wp_kses_post($this->format_breakdown($state_result['breakdown']));
            echo '<p><strong>' . esc_html__('Tax', 'ustc2025') . ':</strong> ' . esc_html(number_format($state_result['tax'], 2)) . '</p>';
            echo '<p><strong>' . esc_html__('Difference', 'ustc2025') . ':</strong> ' . esc_html(number_format($state_result['tax_diff'], 2)) . '</p>';
            echo '</div>';
        }
        echo '</div>';
    }

    private function format_breakdown($lines)
    {
        if (empty($lines)) {
            return '<p>' . esc_html__('No breakdown available.', 'ustc2025') . '</p>';
        }
        $html = '<ul>';
        foreach ($lines as $line) {
            $html .= '<li>' . esc_html($line) . '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    public function render_shortcode($atts)
    {
        $state_settings = $this->get_state_settings();
        $federal_settings = $this->get_federal_settings();
        $gross = isset($_POST['GrossIncome']) ? floatval($_POST['GrossIncome']) : '';
        $fwh = isset($_POST['FederalWithholding']) ? floatval($_POST['FederalWithholding']) : '';
        $swh = isset($_POST['StateWithholding']) ? floatval($_POST['StateWithholding']) : '';
        $state = isset($_POST['state']) ? sanitize_text_field($_POST['state']) : '';

        ob_start();
        echo '<div class="ustc2025-wrapper">';
        echo '<div class="ustc2025-card ustc2025-form">';
        echo '<div class="ustc2025-form-header">' . esc_html__('US Tax Refund kalkulator', 'ustc2025') . '</div>';
        echo '<form method="post">';
        echo '<div class="ustc2025-form-grid">';
        echo '<div class="ustc2025-field"><label>' . esc_html__('Total income (USD)', 'ustc2025') . '</label>';
        echo '<input type="number" name="GrossIncome" step="0.01" min="0" required value="' . esc_attr($gross) . '" /></div>';
        echo '<div class="ustc2025-field"><label>' . esc_html__('Federal withholding (USD)', 'ustc2025') . '</label>';
        echo '<input type="number" name="FederalWithholding" step="0.01" min="0" required value="' . esc_attr($fwh) . '" /></div>';
        echo '<div class="ustc2025-field"><label>' . esc_html__('State', 'ustc2025') . '</label>';
        echo '<select name="state" required>';
        echo '<option value="" disabled ' . selected('', $state, false) . '>' . esc_html__('— Odaberite državu —', 'ustc2025') . '</option>';
        foreach ($this->states as $st) {
            echo '<option value="' . esc_attr($st) . '"' . selected($state, $st, false) . '>' . esc_html($st) . '</option>';
        }
        echo '</select></div>';
        echo '<div class="ustc2025-field"><label>' . esc_html__('State withholding (USD)', 'ustc2025') . '</label>';
        echo '<input type="number" name="StateWithholding" step="0.01" min="0" value="' . esc_attr($swh) . '" /></div>';
        echo '</div>';
        echo '<div class="ustc2025-actions">';
        echo '<button class="ustc2025-button" type="submit" name="ustc_calculate" value="1">' . esc_html__('Izračunaj povrat', 'ustc2025') . '</button>';
        echo '<button class="ustc2025-reset" type="reset">' . esc_html__('Reset', 'ustc2025') . '</button>';
        echo '</div>';
        echo '</form>';
        echo '</div>';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ustc_calculate'])) {
            $federal_resident = $this->calculate_federal($gross, $fwh, 'resident', $federal_settings);
            $federal_nonresident = $this->calculate_federal($gross, $fwh, 'nonresident', $federal_settings);
            $state_resident = $this->calculate_state($state, $gross, $swh, 'resident', $state_settings[$state]);
            $state_nonresident = $this->calculate_state($state, $gross, $swh, 'nonresident', $state_settings[$state]);
            echo '<div class="ustc2025-results">';
            echo $this->render_result_column(__('Rezident', 'ustc2025'), $federal_resident, $state_resident);
            echo $this->render_result_column(__('Nerezident', 'ustc2025'), $federal_nonresident, $state_nonresident);
            echo '</div>';
        }

        echo '</div>';

        return ob_get_clean();
    }

    private function render_result_column($title, $federal, $state)
    {
        $html = '<div class="ustc2025-card ustc2025-column">';
        $html .= '<h3>' . esc_html($title) . '</h3>';
        $html .= '<p><strong>' . esc_html__('Federal:', 'ustc2025') . '</strong> ' . $this->format_result_message($federal['tax_diff']) . '</p>';
        $html .= '<p><strong>' . esc_html__('State:', 'ustc2025') . '</strong> ' . $this->format_result_message($state['tax_diff'], $state['na'] ?? false) . '</p>';
        $html .= '</div>';
        return $html;
    }

    private function format_result_message($tax_diff, $is_na = false)
    {
        if ($is_na) {
            return esc_html__('N/A', 'ustc2025');
        }
        $class = $tax_diff > 0 ? 'ustc2025-owe' : 'ustc2025-refund';
        $message = $tax_diff > 0 ? __('Dugujete', 'ustc2025') : __('Imate povrat', 'ustc2025');
        $amount = number_format(abs($tax_diff), 2);
        return '<span class="' . esc_attr($class) . '">' . esc_html($message . ' ' . $amount . ' USD') . '</span>';
    }

    private function calculate_federal($gross, $withholding, $residency, $settings)
    {
        $breakdown = [];
        $std_deduction = floatval($settings['std_deduction']);
        if ($residency === 'resident') {
            $agi = $gross - $std_deduction;
            $breakdown[] = sprintf(__('AGI = GrossIncome (%s) - StdDeduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($std_deduction, 2), number_format($agi, 2));
        } else {
            $agi = $gross - $withholding;
            $breakdown[] = sprintf(__('AGI = GrossIncome (%s) - FederalWithholding (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($withholding, 2), number_format($agi, 2));
        }
        if ($agi < 0) {
            $agi = 0;
        }
        $brackets = [
            ['limit' => 11925, 'rate' => 0.10],
            ['limit' => 48475, 'rate' => 0.12],
            ['limit' => 103350, 'rate' => 0.22],
            ['limit' => 197300, 'rate' => 0.24],
            ['limit' => 250525, 'rate' => 0.32],
            ['limit' => 626350, 'rate' => 0.35],
            ['limit' => null, 'rate' => 0.37],
        ];
        $tax_data = $this->progressive_tax($agi, $brackets);
        $breakdown = array_merge($breakdown, $tax_data['breakdown']);
        $tax = $tax_data['tax'];
        $tax_diff = $tax - $withholding;
        $breakdown[] = sprintf(__('Tax - FederalWithholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));
        return [
            'tax' => $tax,
            'tax_diff' => $tax_diff,
            'breakdown' => $breakdown,
        ];
    }

    private function progressive_tax($income, $brackets)
    {
        $tax = 0;
        $prev_limit = 0;
        $lines = [];
        foreach ($brackets as $index => $bracket) {
            $limit = $bracket['limit'];
            $rate = $bracket['rate'];
            if ($limit !== null && $income > $limit) {
                $portion = $limit - $prev_limit;
                $tax += $portion * $rate;
                $lines[] = sprintf(__('Bracket %s: (%s - %s) * %s = %s', 'ustc2025'), $index + 1, number_format($limit, 2), number_format($prev_limit, 2), $rate, number_format($portion * $rate, 2));
                $prev_limit = $limit;
            } else {
                $portion = max(0, $income - $prev_limit);
                $tax += $portion * $rate;
                $lines[] = sprintf(__('Bracket %s: (%s - %s) * %s = %s', 'ustc2025'), $index + 1, number_format($income, 2), number_format($prev_limit, 2), $rate, number_format($portion * $rate, 2));
                break;
            }
        }
        return ['tax' => $tax, 'breakdown' => $lines];
    }

    private function calculate_state($state, $gross, $withholding, $residency, $settings)
    {
        $breakdown = [];
        $tax = 0;
        $na = false;
        switch ($state) {
            case 'Maryland':
                $deduction = floatval($settings['deduction']);
                $taxable = $gross - $deduction;
                $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($deduction, 2), number_format($taxable, 2));
                $tax = $this->maryland_tax($taxable, $breakdown);
                break;
            case 'New Jersey':
                $exemption = floatval($settings['exemption']);
                $tax = $this->new_jersey_tax($gross, $exemption, $breakdown);
                break;
            case 'Massachusetts':
                $exemption = floatval($settings['exemption']);
                $rate = floatval($settings['rate']);
                $taxable = $gross - $exemption - $withholding;
                $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - exemption (%s) - withholding (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($exemption, 2), number_format($withholding, 2), number_format($taxable, 2));
                $tax = $taxable * $rate;
                $breakdown[] = sprintf(__('State tax = TaxableIncome * rate = %s * %s', 'ustc2025'), number_format($taxable, 2), $rate);
                break;
            case 'New York':
                $deduction = floatval($settings['deduction']);
                $taxable = $gross - $deduction;
                $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($deduction, 2), number_format($taxable, 2));
                $tax = $this->new_york_tax($taxable, $breakdown);
                break;
            case 'Delaware':
                $deduction = floatval($settings['deduction']);
                $credit = floatval($settings['personal_tax_credit']);
                $refund_threshold = floatval($settings['full_refund_threshold']);
                $taxable = $gross - $deduction;
                $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($deduction, 2), number_format($taxable, 2));
                if ($taxable < $refund_threshold) {
                    $tax = 0;
                    $breakdown[] = __('Full refund threshold met; state tax set to 0.', 'ustc2025');
                    $tax_diff = -$withholding;
                    return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
                }
                if ($taxable < 0) {
                    $taxable = 0;
                }
                $tax = $this->delaware_tax($taxable, $breakdown);
                $tax_diff = $tax - $withholding - $credit;
                $breakdown[] = sprintf(__('Tax - withholding - credit = %s - %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($credit, 2), number_format($tax_diff, 2));
                return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
            case 'California':
                $deduction = floatval($settings['deduction']);
                $credit = floatval($settings['personal_tax_credit']);
                $taxable = $gross - $deduction;
                $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($deduction, 2), number_format($taxable, 2));
                $tax = $this->california_tax($taxable, $breakdown);
                $tax_diff = $tax - $withholding - $credit;
                $breakdown[] = sprintf(__('Tax - withholding - credit = %s - %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($credit, 2), number_format($tax_diff, 2));
                return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
            case 'Michigan':
                $deduction = floatval($settings['deduction']);
                $rate = floatval($settings['rate']);
                $taxable = $gross - $deduction;
                $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($deduction, 2), number_format($taxable, 2));
                $tax = $taxable * $rate;
                $breakdown[] = sprintf(__('State tax = %s * %s = %s', 'ustc2025'), number_format($taxable, 2), $rate, number_format($tax, 2));
                break;
            case 'Missouri':
                $tax = $this->missouri_tax($gross, $withholding, $residency, $settings, $breakdown);
                return $tax;
            case 'North Carolina':
                $deduction = floatval($settings['deduction']);
                $rate = floatval($settings['rate']);
                $taxable = $gross - $deduction;
                $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($deduction, 2), number_format($taxable, 2));
                $tax = $taxable * $rate;
                $breakdown[] = sprintf(__('State tax = %s * %s = %s', 'ustc2025'), number_format($taxable, 2), $rate, number_format($tax, 2));
                break;
            case 'South Carolina':
                $tax = $this->south_carolina_tax($gross, $withholding, $residency, $settings, $breakdown);
                return $tax;
            case 'Wisconsin':
                $tax = $this->wisconsin_tax($gross, $withholding, $residency, $settings, $breakdown);
                return $tax;
            case 'Colorado':
                $tax = $this->colorado_tax($gross, $withholding, $residency, $settings, $breakdown);
                return $tax;
            case 'Maine':
                $tax = $this->maine_tax($gross, $withholding, $residency, $settings, $breakdown);
                return $tax;
            case 'Arizona':
                $deduction = floatval($settings['deduction']);
                $rate = floatval($settings['rate']);
                $taxable = $gross - $deduction;
                $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($deduction, 2), number_format($taxable, 2));
                $tax = $taxable * $rate;
                $breakdown[] = sprintf(__('State tax = %s * %s = %s', 'ustc2025'), number_format($taxable, 2), $rate, number_format($tax, 2));
                break;
            case 'Virginia':
                $tax = $this->virginia_tax($gross, $withholding, $settings, $breakdown);
                return $tax;
            case 'Rhode Island':
                $tax = $this->rhode_island_tax($gross, $withholding, $residency, $settings, $breakdown);
                return $tax;
            case 'District of Columbia':
                $breakdown[] = __('Full refund of state withholding applied.', 'ustc2025');
                $tax_diff = -$withholding;
                return ['tax' => 0, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
            default:
                $na = true;
                break;
        }

        $tax_diff = $tax - $withholding;
        $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));
        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown, 'na' => $na];
    }

    private function maryland_tax($taxable, &$breakdown)
    {
        if ($taxable <= 0) {
            $breakdown[] = __('Taxable income is zero or below; tax is 0.', 'ustc2025');
            return 0;
        } elseif ($taxable <= 1000) {
            $tax = 0.02 * $taxable;
        } elseif ($taxable <= 2000) {
            $tax = 20 + 0.03 * ($taxable - 1000);
        } elseif ($taxable <= 3000) {
            $tax = 50 + 0.04 * ($taxable - 2000);
        } elseif ($taxable <= 100000) {
            $tax = 90 + 0.0475 * ($taxable - 3000);
        } elseif ($taxable <= 125000) {
            $tax = 4568.25 + 0.05 * ($taxable - 100000);
        } elseif ($taxable <= 150000) {
            $tax = 5818.25 + 0.0525 * ($taxable - 125000);
        } elseif ($taxable <= 250000) {
            $tax = 7156.50 + 0.055 * ($taxable - 150000);
        } elseif ($taxable <= 500000) {
            $tax = 12606.50 + 0.0575 * ($taxable - 250000);
        } elseif ($taxable <= 1000000) {
            $tax = 26681.25 + 0.0625 * ($taxable - 500000);
        } else {
            $tax = 57806.25 + 0.065 * ($taxable - 1000000);
        }
        $breakdown[] = sprintf(__('Maryland tax computed: %s', 'ustc2025'), number_format($tax, 2));
        return $tax;
    }

    private function new_jersey_tax($gross, $exemption, &$breakdown)
    {
        if ($gross <= 10000) {
            $breakdown[] = __('Income <= 10,000; tax is 0.', 'ustc2025');
            return 0;
        }
        $taxable = $gross - $exemption;
        $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - exemption (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($exemption, 2), number_format($taxable, 2));
        if ($taxable <= 0) {
            return 0;
        }
        $I = $taxable;
        $state_tax = 0;
        $state_tax += 0.014 * min($I, 20000);
        if ($I > 20000) {
            $state_tax += 0.0175 * (min($I, 35000) - 20000);
        }
        if ($I > 35000) {
            $state_tax += 0.035 * (min($I, 40000) - 35000);
        }
        if ($I > 40000) {
            $state_tax += 0.05525 * (min($I, 75000) - 40000);
        }
        if ($I > 75000) {
            $state_tax += 0.0637 * (min($I, 500000) - 75000);
        }
        if ($I > 500000) {
            $state_tax += 0.0897 * (min($I, 1000000) - 500000);
        }
        if ($I > 1000000) {
            $state_tax += 0.1075 * ($I - 1000000);
        }
        $breakdown[] = sprintf(__('New Jersey tax computed: %s', 'ustc2025'), number_format($state_tax, 2));
        return $state_tax;
    }

    private function new_york_tax($taxable, &$breakdown)
    {
        if ($taxable <= 8500) {
            $tax = 0.04 * $taxable;
        } elseif ($taxable <= 11700) {
            $tax = 340 + 0.045 * ($taxable - 8500);
        } elseif ($taxable <= 13900) {
            $tax = 484 + 0.0525 * ($taxable - 11700);
        } elseif ($taxable <= 80650) {
            $tax = 600 + 0.055 * ($taxable - 13900);
        } elseif ($taxable <= 215400) {
            $tax = 4271 + 0.06 * ($taxable - 80650);
        } elseif ($taxable <= 1077550) {
            $tax = 12356 + 0.0685 * ($taxable - 215400);
        } elseif ($taxable <= 5000000) {
            $tax = 71413 + 0.0965 * ($taxable - 1077550);
        } elseif ($taxable <= 25000000) {
            $tax = 449929 + 0.103 * ($taxable - 5000000);
        } else {
            $tax = 2509929 + 0.109 * ($taxable - 25000000);
        }
        $breakdown[] = sprintf(__('New York tax computed: %s', 'ustc2025'), number_format($tax, 2));
        return $tax;
    }

    private function delaware_tax($taxable, &$breakdown)
    {
        if ($taxable <= 2000) {
            $tax = 0;
        } elseif ($taxable <= 5000) {
            $tax = 0.022 * ($taxable - 2000);
        } elseif ($taxable <= 10000) {
            $tax = 66 + 0.039 * ($taxable - 5000);
        } elseif ($taxable <= 20000) {
            $tax = 261 + 0.048 * ($taxable - 10000);
        } elseif ($taxable <= 25000) {
            $tax = 741 + 0.052 * ($taxable - 20000);
        } elseif ($taxable <= 60000) {
            $tax = 1001 + 0.0555 * ($taxable - 25000);
        } else {
            $tax = 2943.50 + 0.066 * ($taxable - 60000);
        }
        $breakdown[] = sprintf(__('Delaware tax computed: %s', 'ustc2025'), number_format($tax, 2));
        return $tax;
    }

    private function california_tax($taxable, &$breakdown)
    {
        if ($taxable <= 11079) {
            $tax = 0.01 * $taxable;
        } elseif ($taxable <= 26264) {
            $tax = 110.79 + 0.02 * ($taxable - 11079);
        } elseif ($taxable <= 41452) {
            $tax = 414.49 + 0.04 * ($taxable - 26264);
        } elseif ($taxable <= 57542) {
            $tax = 1022.01 + 0.06 * ($taxable - 41452);
        } elseif ($taxable <= 72724) {
            $tax = 1987.41 + 0.08 * ($taxable - 57542);
        } elseif ($taxable <= 371479) {
            $tax = 3201.97 + 0.093 * ($taxable - 72724);
        } elseif ($taxable <= 445771) {
            $tax = 30986.19 + 0.103 * ($taxable - 371479);
        } elseif ($taxable <= 742953) {
            $tax = 38638.27 + 0.113 * ($taxable - 445771);
        } else {
            $tax = 72219.84 + 0.123 * ($taxable - 742953);
        }
        $breakdown[] = sprintf(__('California tax computed: %s', 'ustc2025'), number_format($tax, 2));
        return $tax;
    }

    private function missouri_tax($gross, $withholding, $residency, $settings, &$breakdown)
    {
        $deduction = floatval($settings['deduction']);
        if ($residency === 'resident') {
            $taxable = $gross - $deduction;
            $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($deduction, 2), number_format($taxable, 2));
            $base = $taxable;
        } else {
            $base = $gross;
            $breakdown[] = sprintf(__('Non-resident uses GrossIncome directly: %s', 'ustc2025'), number_format($gross, 2));
        }
        if ($base <= 1313) {
            $tax = 0 * $base;
        } elseif ($base <= 2626) {
            $tax = 0 + 0.02 * ($base - 1313);
        } elseif ($base <= 3939) {
            $tax = 26.26 + 0.025 * ($base - 2626);
        } elseif ($base <= 5252) {
            $tax = 58.44 + 0.03 * ($base - 3939);
        } elseif ($base <= 6565) {
            $tax = 97.89 + 0.035 * ($base - 5252);
        } elseif ($base <= 7878) {
            $tax = 144.91 + 0.04 * ($base - 6565);
        } elseif ($base <= 9191) {
            $tax = 196.91 + 0.045 * ($base - 7878);
        } else {
            $tax = 251.66 + 0.047 * ($residency === 'resident' ? ($base - 9191) : $base);
        }
        $breakdown[] = sprintf(__('Missouri tax computed: %s', 'ustc2025'), number_format($tax, 2));
        $tax_diff = $tax - $withholding;
        $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));
        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function south_carolina_tax($gross, $withholding, $residency, $settings, &$breakdown)
    {
        $deduction = floatval($settings['deduction']);
        $taxable = $residency === 'resident' ? ($gross - $deduction) : $gross;
        $breakdown[] = sprintf(__('TaxableIncome = %s', 'ustc2025'), number_format($taxable, 2));
        if ($taxable <= 3560) {
            $tax = 0 * $taxable;
        } elseif ($taxable <= 17830) {
            $tax = 0 + 0.03 * ($taxable - 3560);
        } else {
            $tax = (0 + 0.03 * (17830 - 3560)) + 0.06 * ($taxable - 17830);
        }
        $breakdown[] = sprintf(__('South Carolina tax computed: %s', 'ustc2025'), number_format($tax, 2));
        $tax_diff = $tax - $withholding;
        $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));
        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function wisconsin_tax($gross, $withholding, $residency, $settings, &$breakdown)
    {
        $deduction = $residency === 'resident' ? floatval($settings['deduction_resident']) : floatval($settings['deduction_nonresident']);
        $taxable = $gross - $deduction;
        $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($deduction, 2), number_format($taxable, 2));
        if ($taxable <= 14680) {
            $tax = 0.035 * $taxable;
        } elseif ($taxable <= 50480) {
            $tax = 14680 * 0.035 + 0.044 * ($taxable - 14680);
        } elseif ($taxable <= 323290) {
            $tax = 14680 * 0.035 + (50480 - 14680) * 0.044 + 0.053 * ($taxable - 50480);
        } else {
            $tax = 14680 * 0.035 + (50480 - 14680) * 0.044 + (323290 - 50480) * 0.053 + 0.0765 * ($taxable - 323290);
        }
        $breakdown[] = sprintf(__('Wisconsin tax computed: %s', 'ustc2025'), number_format($tax, 2));
        $tax_diff = $tax - $withholding;
        $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));
        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function colorado_tax($gross, $withholding, $residency, $settings, &$breakdown)
    {
        $deduction = floatval($settings['deduction']);
        $rate = floatval($settings['rate']);
        $taxable = $residency === 'resident' ? ($gross - $deduction) : $gross;
        $breakdown[] = sprintf(__('TaxableIncome = %s', 'ustc2025'), number_format($taxable, 2));
        $tax = $taxable * $rate;
        $breakdown[] = sprintf(__('Colorado tax = %s * %s = %s', 'ustc2025'), number_format($taxable, 2), $rate, number_format($tax, 2));
        $tax_diff = $tax - $withholding;
        $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));
        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function maine_tax($gross, $withholding, $residency, $settings, &$breakdown)
    {
        $deduction = $residency === 'resident' ? floatval($settings['deduction_resident']) : floatval($settings['deduction_nonresident']);
        $taxable = $gross - $deduction;
        $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($deduction, 2), number_format($taxable, 2));
        if ($taxable <= 26800) {
            $tax = 0.058 * $taxable;
        } elseif ($taxable <= 63450) {
            $tax = 1554 + 0.0675 * ($taxable - 26800);
        } else {
            $tax = 4028 + 0.0715 * ($taxable - 63450);
        }
        $breakdown[] = sprintf(__('Maine tax computed: %s', 'ustc2025'), number_format($tax, 2));
        $tax_diff = $tax - $withholding;
        $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));
        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function virginia_tax($gross, $withholding, $settings, &$breakdown)
    {
        $deduction = floatval($settings['deduction']);
        $taxable = $gross - $deduction;
        $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($deduction, 2), number_format($taxable, 2));
        if ($taxable <= 0) {
            $tax = 0;
        } elseif ($taxable <= 3000) {
            $tax = 0.02 * $taxable;
        } elseif ($taxable <= 5000) {
            $tax = 60 + 0.03 * ($taxable - 3000);
        } elseif ($taxable <= 17000) {
            $tax = 120 + 0.05 * ($taxable - 5000);
        } else {
            $tax = 720 + 0.0575 * ($taxable - 17000);
        }
        $breakdown[] = sprintf(__('Virginia tax computed: %s', 'ustc2025'), number_format($tax, 2));
        $tax_diff = $tax - $withholding;
        $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));
        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function rhode_island_tax($gross, $withholding, $residency, $settings, &$breakdown)
    {
        $deduction = $residency === 'resident' ? floatval($settings['deduction_resident']) : floatval($settings['deduction_nonresident']);
        $taxable = $gross - $deduction;
        $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($deduction, 2), number_format($taxable, 2));
        if ($taxable <= 79900) {
            $tax = 0.0375 * $taxable;
        } elseif ($taxable <= 181650) {
            $tax = 2996.25 + 0.0475 * ($taxable - 79900);
        } else {
            $tax = 7829.38 + 0.0599 * ($taxable - 181650);
        }
        $breakdown[] = sprintf(__('Rhode Island tax computed: %s', 'ustc2025'), number_format($tax, 2));
        $tax_diff = $tax - $withholding;
        $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));
        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }
}

new USTaxCalculator2025();
