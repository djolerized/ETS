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
    private $option_state = 'us_tax_calculator_states_2025';
    private $states_info = [
        ['code' => 'AL', 'name' => 'Alabama'],
        ['code' => 'AR', 'name' => 'Arkansas'],
        ['code' => 'AZ', 'name' => 'Arizona'],
        ['code' => 'CA', 'name' => 'California'],
        ['code' => 'CO', 'name' => 'Colorado'],
        ['code' => 'CT', 'name' => 'Connecticut'],
        ['code' => 'DC', 'name' => 'District of Columbia'],
        ['code' => 'DE', 'name' => 'Delaware'],
        ['code' => 'GE', 'name' => 'Georgia'],
        ['code' => 'HI', 'name' => 'Hawaii'],
        ['code' => 'IA', 'name' => 'Iowa'],
        ['code' => 'KY', 'name' => 'Kentucky'],
        ['code' => 'LA', 'name' => 'Louisiana'],
        ['code' => 'ME', 'name' => 'Maine'],
        ['code' => 'MD', 'name' => 'Maryland'],
        ['code' => 'MA', 'name' => 'Massachusetts'],
        ['code' => 'MI', 'name' => 'Michigan'],
        ['code' => 'MN', 'name' => 'Minnesota'],
        ['code' => 'MO', 'name' => 'Missouri'],
        ['code' => 'NC', 'name' => 'North Carolina'],
        ['code' => 'ND', 'name' => 'North Dakota'],
        ['code' => 'NJ', 'name' => 'New Jersey'],
        ['code' => 'NY', 'name' => 'New York'],
        ['code' => 'OR', 'name' => 'Oregon'],
        ['code' => 'PA', 'name' => 'Pennsylvania'],
        ['code' => 'RI', 'name' => 'Rhode Island'],
        ['code' => 'SC', 'name' => 'South Carolina'],
        ['code' => 'UT', 'name' => 'Utah'],
        ['code' => 'VA', 'name' => 'Virginia'],
        ['code' => 'WI', 'name' => 'Wisconsin'],
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
            'AL' => [
                'state_deduction' => 3000,
                'personal_credit' => 1500,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 500, 'base_tax' => 0, 'rate' => 2],
                    ['min_income' => 500, 'max_income' => 3000, 'base_tax' => 10, 'rate' => 4],
                    ['min_income' => 3000, 'max_income' => '', 'base_tax' => 110, 'rate' => 5],
                ],
            ],
            'AR' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'ar_tax_credits' => 89,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 4500, 'base_tax' => 0, 'rate' => 2],
                    ['min_income' => 4500, 'max_income' => '', 'base_tax' => 0, 'rate' => 3.9],
                ],
            ],
            'AZ' => [
                'state_deduction' => 15600,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 4.4,
                'brackets' => [],
            ],
            'CA' => [
                'state_deduction' => 3250,
                'personal_credit' => 153,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 11079, 'base_tax' => 0, 'rate' => 1],
                    ['min_income' => 11079, 'max_income' => 26264, 'base_tax' => 110.79, 'rate' => 2],
                    ['min_income' => 26264, 'max_income' => 41452, 'base_tax' => 414.49, 'rate' => 4],
                    ['min_income' => 41452, 'max_income' => 57542, 'base_tax' => 1022.01, 'rate' => 6],
                    ['min_income' => 57542, 'max_income' => 72724, 'base_tax' => 1987.41, 'rate' => 8],
                    ['min_income' => 72724, 'max_income' => 371479, 'base_tax' => 3201.97, 'rate' => 9.3],
                    ['min_income' => 371479, 'max_income' => 445771, 'base_tax' => 30986.19, 'rate' => 10.3],
                    ['min_income' => 445771, 'max_income' => 742953, 'base_tax' => 38638.27, 'rate' => 11.3],
                    ['min_income' => 742953, 'max_income' => '', 'base_tax' => 72219.84, 'rate' => 12.3],
                ],
            ],
            'CO' => [
                'state_deduction' => 15600,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 4.4,
                'brackets' => [],
            ],
            'CT' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'ct_deduction' => 15000,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 10000, 'base_tax' => 0, 'rate' => 2],
                    ['min_income' => 10000, 'max_income' => 50000, 'base_tax' => 200, 'rate' => 4.5],
                    ['min_income' => 50000, 'max_income' => 100000, 'base_tax' => 2000, 'rate' => 5.5],
                    ['min_income' => 100000, 'max_income' => 200000, 'base_tax' => 4750, 'rate' => 6],
                    ['min_income' => 200000, 'max_income' => '', 'base_tax' => 10750, 'rate' => 6],
                ],
            ],
            'DC' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 0,
                'brackets' => [],
            ],
            'GE' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'ge_deduction' => 12000,
                'ge_credit' => 300,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 5.19,
                'brackets' => [],
            ],
            'HI' => [
                'state_deduction' => 4400,
                'personal_credit' => 1144,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 9600, 'base_tax' => 0, 'rate' => 1.4],
                    ['min_income' => 9600, 'max_income' => 14400, 'base_tax' => 134.4, 'rate' => 3.2],
                    ['min_income' => 14400, 'max_income' => 19200, 'base_tax' => 288, 'rate' => 5.5],
                    ['min_income' => 19200, 'max_income' => 24000, 'base_tax' => 552, 'rate' => 6.4],
                    ['min_income' => 24000, 'max_income' => 36000, 'base_tax' => 859.2, 'rate' => 6.8],
                    ['min_income' => 36000, 'max_income' => 48000, 'base_tax' => 1675.2, 'rate' => 7.2],
                    ['min_income' => 48000, 'max_income' => 125000, 'base_tax' => 2539.2, 'rate' => 7.6],
                    ['min_income' => 125000, 'max_income' => '', 'base_tax' => 8391.2, 'rate' => 7.9],
                ],
            ],
            'ID' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 5.3,
                'id_deduction' => 14600,
                'permanent_building_fund_tax' => 10,
                'brackets' => [],
            ],
            'IA' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 3.8,
                'ia_personal_credit' => 40,
                'brackets' => [],
            ],
            'KY' => [
                'state_deduction' => 3270,
                'personal_credit' => 3270,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 4,
                'brackets' => [],
            ],
            'LA' => [
                'state_deduction' => 12500,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 3,
                'brackets' => [],
            ],
            'DE' => [
                'state_deduction' => 3250,
                'personal_credit' => 110,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'full_refund_threshold' => 9400,
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 2000, 'base_tax' => 0, 'rate' => 0],
                    ['min_income' => 2000, 'max_income' => 5000, 'base_tax' => 0, 'rate' => 2.2],
                    ['min_income' => 5000, 'max_income' => 10000, 'base_tax' => 66, 'rate' => 3.9],
                    ['min_income' => 10000, 'max_income' => 20000, 'base_tax' => 261, 'rate' => 4.8],
                    ['min_income' => 20000, 'max_income' => 25000, 'base_tax' => 741, 'rate' => 5.2],
                    ['min_income' => 25000, 'max_income' => 60000, 'base_tax' => 1001, 'rate' => 5.55],
                    ['min_income' => 60000, 'max_income' => '', 'base_tax' => 2943.5, 'rate' => 6.6],
                ],
            ],
            'ME' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'deduction_resident' => 20150,
                'deduction_nonresident' => 5150,
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 26800, 'base_tax' => 0, 'rate' => 5.8],
                    ['min_income' => 26800, 'max_income' => 63450, 'base_tax' => 1554, 'rate' => 6.75],
                    ['min_income' => 63450, 'max_income' => '', 'base_tax' => 4028, 'rate' => 7.15],
                ],
            ],
            'MD' => [
                'state_deduction' => 6550,
                'personal_credit' => 0,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 1000, 'base_tax' => 0, 'rate' => 2],
                    ['min_income' => 1000, 'max_income' => 2000, 'base_tax' => 20, 'rate' => 3],
                    ['min_income' => 2000, 'max_income' => 3000, 'base_tax' => 50, 'rate' => 4],
                    ['min_income' => 3000, 'max_income' => 100000, 'base_tax' => 90, 'rate' => 4.75],
                    ['min_income' => 100000, 'max_income' => 125000, 'base_tax' => 4568.25, 'rate' => 5],
                    ['min_income' => 125000, 'max_income' => 150000, 'base_tax' => 5818.25, 'rate' => 5.25],
                    ['min_income' => 150000, 'max_income' => 250000, 'base_tax' => 7156.5, 'rate' => 5.5],
                    ['min_income' => 250000, 'max_income' => 500000, 'base_tax' => 12606.5, 'rate' => 5.75],
                    ['min_income' => 500000, 'max_income' => 1000000, 'base_tax' => 26681.25, 'rate' => 6.25],
                    ['min_income' => 1000000, 'max_income' => '', 'base_tax' => 57806.25, 'rate' => 6.5],
                ],
            ],
            'MA' => [
                'state_deduction' => 1000,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 5,
                'brackets' => [],
            ],
            'MI' => [
                'state_deduction' => 5800,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 4.25,
                'brackets' => [],
            ],
            'MN' => [
                'state_deduction' => 14950,
                'personal_credit' => 0,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 32570, 'base_tax' => 0, 'rate' => 5.35],
                    ['min_income' => 32570, 'max_income' => 106990, 'base_tax' => 0, 'rate' => 6.8],
                    ['min_income' => 106990, 'max_income' => 198630, 'base_tax' => 0, 'rate' => 7.85],
                    ['min_income' => 198630, 'max_income' => '', 'base_tax' => 0, 'rate' => 9.85],
                ],
            ],
            'MO' => [
                'state_deduction' => 15750,
                'personal_credit' => 0,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 1313, 'base_tax' => 0, 'rate' => 0],
                    ['min_income' => 1313, 'max_income' => 2626, 'base_tax' => 0, 'rate' => 2],
                    ['min_income' => 2626, 'max_income' => 3939, 'base_tax' => 26.26, 'rate' => 2.5],
                    ['min_income' => 3939, 'max_income' => 5252, 'base_tax' => 58.44, 'rate' => 3],
                    ['min_income' => 5252, 'max_income' => 6565, 'base_tax' => 97.89, 'rate' => 3.5],
                    ['min_income' => 6565, 'max_income' => 7878, 'base_tax' => 144.91, 'rate' => 4],
                    ['min_income' => 7878, 'max_income' => 9191, 'base_tax' => 196.91, 'rate' => 4.5],
                    ['min_income' => 9191, 'max_income' => '', 'base_tax' => 251.66, 'rate' => 4.7],
                ],
                'income_range_brackets' => [
                    ['min_income' => 0, 'max_income' => 25000, 'rate' => 35],
                    ['min_income' => 25000, 'max_income' => 50000, 'rate' => 25],
                    ['min_income' => 50000, 'max_income' => 100000, 'rate' => 15],
                    ['min_income' => 100000, 'max_income' => 125000, 'rate' => 5],
                    ['min_income' => 125000, 'max_income' => '', 'rate' => 0],
                ],
            ],
            'NJ' => [
                'state_deduction' => 1000,
                'personal_credit' => 0,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 10000, 'base_tax' => 0, 'rate' => 0],
                    ['min_income' => 10000, 'max_income' => 20000, 'base_tax' => 0, 'rate' => 1.4],
                    ['min_income' => 20000, 'max_income' => 35000, 'base_tax' => 280, 'rate' => 1.75],
                    ['min_income' => 35000, 'max_income' => 40000, 'base_tax' => 542.5, 'rate' => 3.5],
                    ['min_income' => 40000, 'max_income' => 75000, 'base_tax' => 717.5, 'rate' => 5.525],
                    ['min_income' => 75000, 'max_income' => 500000, 'base_tax' => 2651.25, 'rate' => 6.37],
                    ['min_income' => 500000, 'max_income' => 1000000, 'base_tax' => 29723.75, 'rate' => 8.97],
                    ['min_income' => 1000000, 'max_income' => '', 'base_tax' => 74573.75, 'rate' => 10.75],
                ],
            ],
            'NY' => [
                'state_deduction' => 9000,
                'personal_credit' => 0,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 8500, 'base_tax' => 0, 'rate' => 4],
                    ['min_income' => 8500, 'max_income' => 11700, 'base_tax' => 340, 'rate' => 4.5],
                    ['min_income' => 11700, 'max_income' => 13900, 'base_tax' => 484, 'rate' => 5.25],
                    ['min_income' => 13900, 'max_income' => 80650, 'base_tax' => 600, 'rate' => 5.5],
                    ['min_income' => 80650, 'max_income' => 215400, 'base_tax' => 4271, 'rate' => 6],
                    ['min_income' => 215400, 'max_income' => 1077550, 'base_tax' => 12356, 'rate' => 6.85],
                    ['min_income' => 1077550, 'max_income' => 5000000, 'base_tax' => 71413, 'rate' => 9.65],
                    ['min_income' => 5000000, 'max_income' => 25000000, 'base_tax' => 449929, 'rate' => 10.3],
                    ['min_income' => 25000000, 'max_income' => '', 'base_tax' => 2509929, 'rate' => 10.9],
                ],
            ],
            'NC' => [
                'state_deduction' => 12750,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 4.25,
                'brackets' => [],
            ],
            'ND' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 55975, 'base_tax' => 0, 'rate' => 0],
                    ['min_income' => 55975, 'max_income' => 252325, 'base_tax' => 0, 'rate' => 1.95],
                    ['min_income' => 252325, 'max_income' => '', 'base_tax' => 3828.83, 'rate' => 2.5],
                ],
            ],
            'OR' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 4400, 'base_tax' => 0, 'rate' => 4.25],
                    ['min_income' => 4400, 'max_income' => 11050, 'base_tax' => 187, 'rate' => 6.75],
                    ['min_income' => 11050, 'max_income' => 125000, 'base_tax' => 635.875, 'rate' => 8.75],
                    ['min_income' => 125000, 'max_income' => '', 'base_tax' => 10606.5, 'rate' => 9.9],
                ],
            ],
            'PA' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 3.07,
                'brackets' => [],
            ],
            'RI' => [
                'state_deduction' => 16000,
                'personal_credit' => 0,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 79900, 'base_tax' => 0, 'rate' => 3.75],
                    ['min_income' => 79900, 'max_income' => 181650, 'base_tax' => 2996.25, 'rate' => 4.75],
                    ['min_income' => 181650, 'max_income' => '', 'base_tax' => 7829.38, 'rate' => 5.99],
                ],
            ],
            'SC' => [
                'state_deduction' => 14600,
                'personal_credit' => 0,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 3560, 'base_tax' => 0, 'rate' => 0],
                    ['min_income' => 3560, 'max_income' => 17830, 'base_tax' => 0, 'rate' => 3],
                    ['min_income' => 17830, 'max_income' => '', 'base_tax' => 428.1, 'rate' => 6],
                ],
            ],
            'UT' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 4.5,
                'brackets' => [],
            ],
            'VA' => [
                'state_deduction' => 8750,
                'personal_credit' => 0,
                'va_personal_deduction' => 930,
                'va_low_income_credit' => 300,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 3000, 'base_tax' => 0, 'rate' => 2],
                    ['min_income' => 3000, 'max_income' => 5000, 'base_tax' => 60, 'rate' => 3],
                    ['min_income' => 5000, 'max_income' => 17000, 'base_tax' => 120, 'rate' => 5],
                    ['min_income' => 17000, 'max_income' => '', 'base_tax' => 720, 'rate' => 5.75],
                ],
            ],
            'WI' => [
                'state_deduction' => 14260,
                'personal_credit' => 0,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 14680, 'base_tax' => 0, 'rate' => 3.5],
                    ['min_income' => 14680, 'max_income' => 50480, 'base_tax' => 513.8, 'rate' => 4.4],
                    ['min_income' => 50480, 'max_income' => 323290, 'base_tax' => 2089, 'rate' => 5.3],
                    ['min_income' => 323290, 'max_income' => '', 'base_tax' => 16547.93, 'rate' => 7.65],
                ],
            ],
        ];
    }

    public function enqueue_assets()
    {
        $handle = 'ustc2025-styles';
        $css = '.ustc2025-wrapper{font-family:Arial,sans-serif;background:#f8f7fb;padding:8px;border-radius:10px}.ustc2025-card{background:#fff;border:1px solid #e6e2ed;border-radius:10px;box-shadow:0 4px 16px rgba(17,16,62,0.07);padding:20px;margin-bottom:18px}.ustc2025-form-header{font-size:20px;font-weight:600;margin:0 0 12px;color:#2b2341}.ustc2025-form-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px}.ustc2025-field label{display:block;font-size:16px;font-weight:600;margin-bottom:6px;color:#3a3352}.ustc2025-field input[type=number],.ustc2025-field select{width:100%;padding:12px 14px;border:1px solid #d6d1df;border-radius:8px;font-size:16px;color:#2b2341;background:#fff;box-sizing:border-box;transition:border-color .2s,box-shadow .2s}.ustc2025-field input[type=number]:focus,.ustc2025-field select:focus{outline:none;border-color:#5f2f88;box-shadow:0 0 0 2px rgba(95,47,136,0.12)}.ustc2025-actions{margin-top:8px;display:flex;gap:10px;align-items:center}.ustc2025-button{background:#5f2f88;color:#fff;border:none;padding:12px 18px;border-radius:8px;cursor:pointer;font-weight:600;transition:transform .1s ease,box-shadow .2s}.ustc2025-button:hover{transform:translateY(-1px);box-shadow:0 4px 10px rgba(95,47,136,0.25)}.ustc2025-button:disabled{opacity:.6;cursor:not-allowed}.ustc2025-reset{background:transparent;color:#5f2f88;border:1px solid #c9c2d7;padding:12px 18px;border-radius:8px;cursor:pointer}.ustc2025-results{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:12px}.ustc2025-column h3{margin-top:0;color:#2b2341;font-size:16px}.ustc2025-column p{margin:6px 0;font-size:14px;color:#4a425c}.ustc2025-owe{color:#e53935;font-weight:700}.ustc2025-refund{color:#2e8b57;font-weight:700}.ustc2025-tab-nav{display:flex;flex-wrap:nowrap;gap:10px;overflow-x:auto;padding:6px 2px;margin:12px 0 16px;scrollbar-width:thin;scrollbar-color:#c9c2d7 transparent}.ustc2025-tab-nav::-webkit-scrollbar{height:8px}.ustc2025-tab-nav::-webkit-scrollbar-thumb{background:#c9c2d7;border-radius:10px}.ustc2025-tab-nav::-webkit-scrollbar-track{background:transparent}.ustc2025-tab-nav a{display:inline-flex;align-items:center;justify-content:center;white-space:nowrap;text-decoration:none;padding:10px 14px;border-radius:8px;border:1px solid #c9c2d7;background:#f5f5f5;box-shadow:0 1px 2px rgba(0,0,0,0.06);font-weight:600;color:#2b2341}.ustc2025-tab-nav a.active{background:#5f2f88;color:#fff;border-color:#5f2f88;box-shadow:0 2px 6px rgba(95,47,136,0.25)}.ustc2025-breakdown{background:#fafafa;border:1px solid #e0e0e0;border-radius:8px;padding:12px;margin-top:12px}.ustc2025-header{font-size:18px;font-weight:600;margin-bottom:8px;color:#5f2f88}.ustc2025-row{display:flex;gap:10px;flex-wrap:wrap}.ustc2025-row .ustc2025-col{flex:1;min-width:200px}';
        wp_register_style($handle, false);
        wp_enqueue_style($handle);
        wp_add_inline_style($handle, $css);

        $script_handle = 'ustc2025-scripts';
        wp_register_script($script_handle, false, [], false, true);
        wp_enqueue_script($script_handle);
        $script = <<<'JS'
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.ustc2025-form form');
    if (!form) {
        return;
    }

    const resetBtn = form.querySelector('.ustc2025-reset');
    if (!resetBtn) {
        return;
    }

    resetBtn.addEventListener('click', function (event) {
        event.preventDefault();

        form.reset();

        const numberInputs = form.querySelectorAll('input[type="number"]');
        numberInputs.forEach(function (input) {
            input.value = '';
        });

        const stateSelect = form.querySelector('select[name="state"]');
        if (stateSelect) {
            stateSelect.selectedIndex = 0;
        }

        const results = document.querySelector('.ustc2025-results');
        if (results) {
            results.remove();
        }

        const disclaimers = document.querySelectorAll('.ustc2025-disclaimer');
        if (disclaimers.length) {
            disclaimers.forEach(function (element) {
                element.remove();
            });
        }
    });
});
JS;
        wp_add_inline_script($script_handle, $script);

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
        register_setting('ustc2025_state_group', $this->option_state, ['sanitize_callback' => [$this, 'sanitize_state_settings']]);
    }

    private function get_federal_settings()
    {
        $saved = get_option($this->option_federal, []);
        return wp_parse_args($saved, $this->defaults_federal());
    }

    private function get_state_settings()
    {
        $saved = get_option($this->option_state, []);
        $defaults = $this->defaults_states();
        $merged = [];
        foreach ($this->states_info as $state) {
            $code = $state['code'];
            $merged[$code] = isset($saved[$code]) ? wp_parse_args($saved[$code], $defaults[$code]) : $defaults[$code];
        }
        return $merged;
    }

    public function sanitize_state_settings($input)
    {
        $defaults = $this->defaults_states();
        $clean = [];
        foreach ($this->states_info as $state) {
            $code = $state['code'];
            $state_input = isset($input[$code]) ? $input[$code] : [];
            $clean[$code] = [
                'state_deduction' => isset($state_input['state_deduction']) ? floatval($state_input['state_deduction']) : floatval($defaults[$code]['state_deduction']),
                'personal_credit' => isset($state_input['personal_credit']) ? floatval($state_input['personal_credit']) : floatval($defaults[$code]['personal_credit']),
                'calculation_mode' => isset($state_input['calculation_mode']) && in_array($state_input['calculation_mode'], ['progressive_brackets', 'flat_rate'], true) ? $state_input['calculation_mode'] : $defaults[$code]['calculation_mode'],
                'flat_rate' => isset($state_input['flat_rate']) ? floatval($state_input['flat_rate']) : ($defaults[$code]['flat_rate'] === '' ? '' : floatval($defaults[$code]['flat_rate'])),
                'brackets' => [],
            ];

            if (isset($defaults[$code]['income_range_brackets'])) {
                $clean[$code]['income_range_brackets'] = [];
            }

            if (isset($defaults[$code]['deduction_resident'])) {
                $clean[$code]['deduction_resident'] = isset($state_input['deduction_resident']) ? floatval($state_input['deduction_resident']) : floatval($defaults[$code]['deduction_resident']);
            }

            if (isset($defaults[$code]['deduction_nonresident'])) {
                $clean[$code]['deduction_nonresident'] = isset($state_input['deduction_nonresident']) ? floatval($state_input['deduction_nonresident']) : floatval($defaults[$code]['deduction_nonresident']);
            }

            if (isset($defaults[$code]['id_deduction'])) {
                $clean[$code]['id_deduction'] = isset($state_input['id_deduction']) ? floatval($state_input['id_deduction']) : floatval($defaults[$code]['id_deduction']);
            }

            if (isset($defaults[$code]['permanent_building_fund_tax'])) {
                $clean[$code]['permanent_building_fund_tax'] = isset($state_input['permanent_building_fund_tax']) ? floatval($state_input['permanent_building_fund_tax']) : floatval($defaults[$code]['permanent_building_fund_tax']);
            }

            if (isset($defaults[$code]['ia_personal_credit'])) {
                $clean[$code]['ia_personal_credit'] = isset($state_input['ia_personal_credit']) ? floatval($state_input['ia_personal_credit']) : floatval($defaults[$code]['ia_personal_credit']);
            }

            if (isset($defaults[$code]['va_personal_deduction'])) {
                $clean[$code]['va_personal_deduction'] = isset($state_input['va_personal_deduction']) ? floatval($state_input['va_personal_deduction']) : floatval($defaults[$code]['va_personal_deduction']);
            }

            if (isset($defaults[$code]['va_low_income_credit'])) {
                $clean[$code]['va_low_income_credit'] = isset($state_input['va_low_income_credit']) ? floatval($state_input['va_low_income_credit']) : floatval($defaults[$code]['va_low_income_credit']);
            }

            if (!empty($defaults[$code]['full_refund_threshold'])) {
                $clean[$code]['full_refund_threshold'] = floatval($defaults[$code]['full_refund_threshold']);
            }

            if (isset($defaults[$code]['ar_tax_credits'])) {
                $clean[$code]['ar_tax_credits'] = isset($state_input['ar_tax_credits']) ? floatval($state_input['ar_tax_credits']) : floatval($defaults[$code]['ar_tax_credits']);
            }

            if (isset($state_input['brackets']) && is_array($state_input['brackets'])) {
                foreach ($state_input['brackets'] as $row) {
                    if ($row === null) {
                        continue;
                    }
                    $min = isset($row['min_income']) ? floatval($row['min_income']) : 0;
                    $max = isset($row['max_income']) && $row['max_income'] !== '' ? floatval($row['max_income']) : '';
                    $base = isset($row['base_tax']) ? floatval($row['base_tax']) : 0;
                    $rate = isset($row['rate']) ? floatval($row['rate']) : 0;
                    $clean[$code]['brackets'][] = [
                        'min_income' => $min,
                        'max_income' => $max,
                        'base_tax' => $base,
                        'rate' => $rate,
                    ];
                }
            } else {
                $clean[$code]['brackets'] = isset($defaults[$code]['brackets']) ? $defaults[$code]['brackets'] : [];
            }

            if (isset($defaults[$code]['income_range_brackets'])) {
                if (isset($state_input['income_range_brackets']) && is_array($state_input['income_range_brackets'])) {
                    foreach ($state_input['income_range_brackets'] as $row) {
                        if ($row === null) {
                            continue;
                        }
                        $min = isset($row['min_income']) ? floatval($row['min_income']) : 0;
                        $max = isset($row['max_income']) && $row['max_income'] !== '' ? floatval($row['max_income']) : '';
                        $rate = isset($row['rate']) ? floatval($row['rate']) : 0;
                        $clean[$code]['income_range_brackets'][] = [
                            'min_income' => $min,
                            'max_income' => $max,
                            'rate' => $rate,
                        ];
                    }
                } else {
                    $clean[$code]['income_range_brackets'] = $defaults[$code]['income_range_brackets'];
                }
            }
        }
        return $clean;
    }

    private function get_state_code_by_name($name)
    {
        foreach ($this->states_info as $state) {
            if ($state['name'] === $name) {
                return $state['code'];
            }
        }
        return '';
    }

    private function get_state_settings_by_name($name, $all_settings)
    {
        $code = $this->get_state_code_by_name($name);
        return isset($all_settings[$code]) ? $all_settings[$code] : [];
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
            $state_tab = isset($_GET['state_tab']) ? sanitize_text_field($_GET['state_tab']) : $this->states_info[0]['code'];
            echo '<div class="ustc2025-card">';
            echo '<div class="ustc2025-header">' . esc_html__('State settings', 'ustc2025') . '</div>';
            echo '<nav class="ustc2025-tab-nav">';
            foreach ($this->states_info as $state) {
                $active = $state_tab === $state['code'] ? 'active' : '';
                $url = add_query_arg(['page' => 'ustc2025', 'tab' => 'states', 'state_tab' => $state['code']], admin_url('admin.php'));
                echo '<a class="' . esc_attr($active) . '" href="' . esc_url($url) . '">' . esc_html($state['name']) . '</a>';
            }
            echo '</nav>';
            echo '<form method="post" action="options.php">';
            settings_fields('ustc2025_state_group');

            foreach ($this->states_info as $state) {
                $code = $state['code'];
                $state_settings = isset($settings[$code]) ? $settings[$code] : [];
                $style = $state_tab === $code ? '' : 'style="display:none;"';
                echo '<div class="ustc2025-state-tab" id="ustc-tab-' . esc_attr($code) . '" ' . $style . '>';
                echo '<h3>' . esc_html($state['name']) . '</h3>';
                echo '<div class="ustc2025-row">';
                $income_ranges = [];
                if ($code === 'ME') {
                    $resident_deduction = isset($state_settings['deduction_resident']) ? $state_settings['deduction_resident'] : '';
                    $nonresident_deduction = isset($state_settings['deduction_nonresident']) ? $state_settings['deduction_nonresident'] : '';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Resident deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][deduction_resident]" value="' . esc_attr($resident_deduction) . '" /></div>';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Non-resident deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][deduction_nonresident]" value="' . esc_attr($nonresident_deduction) . '" /></div>';
                } elseif ($code === 'ID') {
                    $id_deduction = isset($state_settings['id_deduction']) ? $state_settings['id_deduction'] : '';
                    $pbf_tax = isset($state_settings['permanent_building_fund_tax']) ? $state_settings['permanent_building_fund_tax'] : '';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Idaho deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][id_deduction]" value="' . esc_attr($id_deduction) . '" /></div>';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Permanent building fund tax (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][permanent_building_fund_tax]" value="' . esc_attr($pbf_tax) . '" /></div>';
                } elseif ($code === 'IA') {
                    $ia_personal_credit = isset($state_settings['ia_personal_credit']) ? $state_settings['ia_personal_credit'] : '';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Iowa personal credit (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][ia_personal_credit]" value="' . esc_attr($ia_personal_credit) . '" /></div>';
                } elseif ($code === 'CT') {
                    $ct_deduction = isset($state_settings['ct_deduction']) ? $state_settings['ct_deduction'] : '';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Connecticut deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][ct_deduction]" value="' . esc_attr($ct_deduction) . '" /></div>';
                    echo '<input type="hidden" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][state_deduction]" value="' . esc_attr($state_settings['state_deduction']) . '" />';
                    echo '<input type="hidden" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][personal_credit]" value="' . esc_attr($state_settings['personal_credit']) . '" />';
                } elseif ($code === 'AR') {
                    $ar_tax_credits = isset($state_settings['ar_tax_credits']) ? $state_settings['ar_tax_credits'] : '';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Arkansas tax credits (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][ar_tax_credits]" value="' . esc_attr($ar_tax_credits) . '" /></div>';
                } elseif ($code === 'GE') {
                    $ge_deduction = isset($state_settings['ge_deduction']) ? $state_settings['ge_deduction'] : '';
                    $ge_credit = isset($state_settings['ge_credit']) ? $state_settings['ge_credit'] : '';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Georgia deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][ge_deduction]" value="' . esc_attr($ge_deduction) . '" /></div>';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Georgia credit (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][ge_credit]" value="' . esc_attr($ge_credit) . '" /></div>';
                    echo '<input type="hidden" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][state_deduction]" value="' . esc_attr($state_settings['state_deduction']) . '" />';
                    echo '<input type="hidden" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][personal_credit]" value="' . esc_attr($state_settings['personal_credit']) . '" />';
                } elseif ($code === 'MO') {
                    $income_ranges = isset($state_settings['income_range_brackets']) ? $state_settings['income_range_brackets'] : [];
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Missouri deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][state_deduction]" value="' . esc_attr($state_settings['state_deduction']) . '" /></div>';
                    echo '<input type="hidden" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][personal_credit]" value="' . esc_attr($state_settings['personal_credit']) . '" />';
                    echo '</div>';
                    echo '<div class="ustc2025-row">';
                    echo '<div class="ustc2025-col full-width"><h4>' . esc_html__('Resident tax brackets', 'ustc2025') . '</h4></div>';
                } elseif ($code === 'VA') {
                    $va_personal_deduction = isset($state_settings['va_personal_deduction']) ? $state_settings['va_personal_deduction'] : '';
                    $va_low_income_credit = isset($state_settings['va_low_income_credit']) ? $state_settings['va_low_income_credit'] : '';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Virginia deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][state_deduction]" value="' . esc_attr($state_settings['state_deduction']) . '" /></div>';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Virginia personal deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][va_personal_deduction]" value="' . esc_attr($va_personal_deduction) . '" /></div>';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Virginia low income credit (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][va_low_income_credit]" value="' . esc_attr($va_low_income_credit) . '" /></div>';
                    echo '<input type="hidden" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][personal_credit]" value="' . esc_attr($state_settings['personal_credit']) . '" />';
                } elseif ($code === 'KY') {
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Kentucky standard deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][state_deduction]" value="' . esc_attr($state_settings['state_deduction']) . '" /></div>';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Kentucky personal credit (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][personal_credit]" value="' . esc_attr($state_settings['personal_credit']) . '" /></div>';
                } elseif ($code === 'LA') {
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Louisiana standard deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][state_deduction]" value="' . esc_attr($state_settings['state_deduction']) . '" /></div>';
                    echo '<input type="hidden" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][personal_credit]" value="' . esc_attr($state_settings['personal_credit']) . '" />';
                } else {
                    echo '<div class="ustc2025-col"><label>' . esc_html__('State deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][state_deduction]" value="' . esc_attr($state_settings['state_deduction']) . '" /></div>';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Personal credit (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][personal_credit]" value="' . esc_attr($state_settings['personal_credit']) . '" /></div>';
                }
                echo '</div>';
                echo '<div class="ustc2025-row">';
                echo '<div class="ustc2025-col"><label>' . esc_html__('Calculation mode', 'ustc2025') . '</label><select class="ustc-calculation-mode" data-target="' . esc_attr($code) . '" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][calculation_mode]">';
                echo '<option value="progressive_brackets"' . selected($state_settings['calculation_mode'], 'progressive_brackets', false) . '>' . esc_html__('Progressive brackets', 'ustc2025') . '</option>';
                echo '<option value="flat_rate"' . selected($state_settings['calculation_mode'], 'flat_rate', false) . '>' . esc_html__('Flat rate', 'ustc2025') . '</option>';
                echo '</select></div>';
                echo '<div class="ustc2025-col ustc-flat-rate" data-state="' . esc_attr($code) . '"><label>' . esc_html__('Flat rate (%)', 'ustc2025') . '</label><input type="number" step="0.0001" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][flat_rate]" value="' . esc_attr($state_settings['flat_rate']) . '" /></div>';
                echo '</div>';

                echo '<div class="ustc2025-brackets" data-state="' . esc_attr($code) . '">';
                $bracket_title = $code === 'MO' ? esc_html__('Tax brackets (resident)', 'ustc2025') : esc_html__('Tax brackets', 'ustc2025');
                echo '<h4>' . $bracket_title . '</h4>';
                echo '<table class="widefat fixed" cellspacing="0">';
                echo '<thead><tr><th>' . esc_html__('Min income', 'ustc2025') . '</th><th>' . esc_html__('Max income', 'ustc2025') . '</th><th>' . esc_html__('Base tax', 'ustc2025') . '</th><th>' . esc_html__('Rate (%)', 'ustc2025') . '</th><th>' . esc_html__('Actions', 'ustc2025') . '</th></tr></thead>';
                echo '<tbody>';
                $index = 0;
                foreach ($state_settings['brackets'] as $row) {
                    echo $this->render_bracket_row($code, $index, $row);
                    $index++;
                }
                echo '</tbody>';
                echo '</table>';
                echo '<p><button type="button" class="button ustc-add-bracket" data-state="' . esc_attr($code) . '">' . esc_html__('Add bracket', 'ustc2025') . '</button></p>';
                echo '</div>';

                if ($code === 'MO') {
                    $income_ranges = !empty($income_ranges) ? $income_ranges : (isset($state_settings['income_range_brackets']) ? $state_settings['income_range_brackets'] : []);
                    echo '<div class="ustc2025-brackets ustc2025-income-ranges" data-state="' . esc_attr($code) . '">';
                    echo '<h4>' . esc_html__('Non-resident income range brackets', 'ustc2025') . '</h4>';
                    echo '<table class="widefat fixed" cellspacing="0">';
                    echo '<thead><tr><th>' . esc_html__('Min income', 'ustc2025') . '</th><th>' . esc_html__('Max income', 'ustc2025') . '</th><th>' . esc_html__('Rate (%)', 'ustc2025') . '</th><th>' . esc_html__('Actions', 'ustc2025') . '</th></tr></thead>';
                    echo '<tbody>';
                    $idx = 0;
                    foreach ($income_ranges as $row) {
                        echo $this->render_income_range_row($code, $idx, $row);
                        $idx++;
                    }
                    echo '</tbody>';
                    echo '</table>';
                    echo '<p><button type="button" class="button ustc-add-income-range" data-state="' . esc_attr($code) . '">' . esc_html__('Add income range', 'ustc2025') . '</button></p>';
                    echo '</div>';
                }
                echo '</div>';
            }
            submit_button();
            echo '</form>';
            ob_start();
            ?>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    function updateMode(state) {
                        const select = document.querySelector('.ustc-calculation-mode[data-target="' + state + '"]');
                        const flat = document.querySelector('.ustc-flat-rate[data-state="' + state + '"]');
                        const brackets = document.querySelector('.ustc2025-brackets[data-state="' + state + '"]');
                        if (!select) return;
                        const mode = select.value;
                        if (mode === 'flat_rate') {
                            if (flat) flat.style.display = 'block';
                            if (brackets) brackets.style.display = 'none';
                        } else {
                            if (flat) flat.style.display = 'none';
                            if (brackets) brackets.style.display = 'block';
                        }
                    }

                    document.querySelectorAll('.ustc-calculation-mode').forEach(function (sel) {
                        updateMode(sel.dataset.target);
                        sel.addEventListener('change', function () {
                            updateMode(this.dataset.target);
                        });
                    });

                    document.body.addEventListener('click', function (e) {
                        if (e.target.classList.contains('ustc-add-bracket')) {
                            const state = e.target.dataset.state;
                            const table = document.querySelector('.ustc2025-brackets[data-state="' + state + '"] tbody');
                            if (!table) return;
                            const idx = table.querySelectorAll('tr').length;
                            const row = document.createElement('tr');
                            row.innerHTML = '<td><input type="number" step="0.01" name="<?php echo esc_js($this->option_state); ?>[' + state + '][brackets][' + idx + '][min_income]" /></td>' +
                                '<td><input type="number" step="0.01" name="<?php echo esc_js($this->option_state); ?>[' + state + '][brackets][' + idx + '][max_income]" /></td>' +
                                '<td><input type="number" step="0.01" name="<?php echo esc_js($this->option_state); ?>[' + state + '][brackets][' + idx + '][base_tax]" /></td>' +
                                '<td><input type="number" step="0.0001" name="<?php echo esc_js($this->option_state); ?>[' + state + '][brackets][' + idx + '][rate]" /></td>' +
                                '<td><button type="button" class="button link-delete ustc-remove-bracket"><?php echo esc_js(__('Remove', 'ustc2025')); ?></button></td>';
                            table.appendChild(row);
                        }
                        if (e.target.classList.contains('ustc-add-income-range')) {
                            const state = e.target.dataset.state;
                            const table = document.querySelector('.ustc2025-income-ranges[data-state="' + state + '"] tbody');
                            if (!table) return;
                            const idx = table.querySelectorAll('tr').length;
                            const row = document.createElement('tr');
                            row.innerHTML = '<td><input type="number" step="0.01" name="<?php echo esc_js($this->option_state); ?>[' + state + '][income_range_brackets][' + idx + '][min_income]" /></td>' +
                                '<td><input type="number" step="0.01" name="<?php echo esc_js($this->option_state); ?>[' + state + '][income_range_brackets][' + idx + '][max_income]" /></td>' +
                                '<td><input type="number" step="0.0001" name="<?php echo esc_js($this->option_state); ?>[' + state + '][income_range_brackets][' + idx + '][rate]" /></td>' +
                                '<td><button type="button" class="button link-delete ustc-remove-income-range"><?php echo esc_js(__('Remove', 'ustc2025')); ?></button></td>';
                            table.appendChild(row);
                        }
                        if (e.target.classList.contains('ustc-remove-bracket')) {
                            const row = e.target.closest('tr');
                            if (row) row.remove();
                        }
                        if (e.target.classList.contains('ustc-remove-income-range')) {
                            const row = e.target.closest('tr');
                            if (row) row.remove();
                        }
                    });
                });
            </script>
            <?php
            echo ob_get_clean();
            echo '</div>';
        } else {
            $this->render_control_tool();
        }
        echo '</div>';
    }

    private function render_bracket_row($state_code, $index, $row)
    {
        $min = isset($row['min_income']) ? $row['min_income'] : '';
        $max = isset($row['max_income']) ? $row['max_income'] : '';
        $base = isset($row['base_tax']) ? $row['base_tax'] : '';
        $rate = isset($row['rate']) ? $row['rate'] : '';
        $html = '<tr>';
        $html .= '<td><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($state_code) . '][brackets][' . esc_attr($index) . '][min_income]" value="' . esc_attr($min) . '" /></td>';
        $html .= '<td><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($state_code) . '][brackets][' . esc_attr($index) . '][max_income]" value="' . esc_attr($max) . '" /></td>';
        $html .= '<td><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($state_code) . '][brackets][' . esc_attr($index) . '][base_tax]" value="' . esc_attr($base) . '" /></td>';
        $html .= '<td><input type="number" step="0.0001" name="' . esc_attr($this->option_state) . '[' . esc_attr($state_code) . '][brackets][' . esc_attr($index) . '][rate]" value="' . esc_attr($rate) . '" /></td>';
        $html .= '<td><button type="button" class="button link-delete ustc-remove-bracket">' . esc_html__('Remove', 'ustc2025') . '</button></td>';
        $html .= '</tr>';
        return $html;
    }

    private function render_income_range_row($state_code, $index, $row)
    {
        $min = isset($row['min_income']) ? $row['min_income'] : '';
        $max = isset($row['max_income']) ? $row['max_income'] : '';
        $rate = isset($row['rate']) ? $row['rate'] : '';
        $html = '<tr>';
        $html .= '<td><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($state_code) . '][income_range_brackets][' . esc_attr($index) . '][min_income]" value="' . esc_attr($min) . '" /></td>';
        $html .= '<td><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($state_code) . '][income_range_brackets][' . esc_attr($index) . '][max_income]" value="' . esc_attr($max) . '" /></td>';
        $html .= '<td><input type="number" step="0.0001" name="' . esc_attr($this->option_state) . '[' . esc_attr($state_code) . '][income_range_brackets][' . esc_attr($index) . '][rate]" value="' . esc_attr($rate) . '" /></td>';
        $html .= '<td><button type="button" class="button link-delete ustc-remove-income-range">' . esc_html__('Remove', 'ustc2025') . '</button></td>';
        $html .= '</tr>';
        return $html;
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
        foreach ($this->states_info as $st) {
            echo '<option value="' . esc_attr($st['name']) . '"' . selected($state, $st['name'], false) . '>' . esc_html($st['name']) . '</option>';
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
            $state_result = $this->calculate_state($state, $gross, $swh, $residency, $this->get_state_settings_by_name($state, $state_settings), $federal);
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
        echo '<div class="ustc2025-form-header">' . esc_html__('US Tax Refund Calculator', 'ustc2025') . '</div>';
        echo '<form method="post">';
        echo '<div class="ustc2025-form-grid">';
        echo '<div class="ustc2025-field"><label>' . esc_html__('Total income (USD)', 'ustc2025') . '</label>';
        echo '<input type="number" name="GrossIncome" step="0.01" min="0" required value="' . esc_attr($gross) . '" /></div>';
        echo '<div class="ustc2025-field"><label>' . esc_html__('Federal withholding (USD)', 'ustc2025') . '</label>';
        echo '<input type="number" name="FederalWithholding" step="0.01" min="0" required value="' . esc_attr($fwh) . '" /></div>';
        echo '<div class="ustc2025-field"><label>' . esc_html__('State', 'ustc2025') . '</label>';
        echo '<select name="state" required>';
        echo '<option value="" disabled ' . selected('', $state, false) . '>' . esc_html__('— Choose a state —', 'ustc2025') . '</option>';
        foreach ($this->states_info as $st) {
            echo '<option value="' . esc_attr($st['name']) . '"' . selected($state, $st['name'], false) . '>' . esc_html($st['name']) . '</option>';
        }
        echo '</select></div>';
        echo '<div class="ustc2025-field"><label>' . esc_html__('State withholding (USD)', 'ustc2025') . '</label>';
        echo '<input type="number" name="StateWithholding" step="0.01" min="0" value="' . esc_attr($swh) . '" /></div>';
        echo '</div>';
        if ($state === 'New York') {
            echo '<div class="ustc2025-disclaimer">' . esc_html__('If you paid New York local tax, please contact us for more detailed calculation.', 'ustc2025') . '</div>';
        }
        echo '<div class="ustc2025-actions">';
        echo '<button class="ustc2025-button" type="submit" name="ustc_calculate" value="1">' . esc_html__('Calculate', 'ustc2025') . '</button>';
        echo '<button class="ustc2025-reset" type="reset">' . esc_html__('Reset', 'ustc2025') . '</button>';
        echo '</div>';
        echo '</form>';
        echo '</div>';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ustc_calculate'])) {
            $federal_resident = $this->calculate_federal($gross, $fwh, 'resident', $federal_settings);
            $federal_nonresident = $this->calculate_federal($gross, $fwh, 'nonresident', $federal_settings);
            $selected_state_settings = $this->get_state_settings_by_name($state, $state_settings);
            $state_resident = $this->calculate_state($state, $gross, $swh, 'resident', $selected_state_settings, $federal_resident);
            $state_nonresident = $this->calculate_state($state, $gross, $swh, 'nonresident', $selected_state_settings, $federal_nonresident);

            $state_resident = !empty($state_resident) ? $state_resident : $state_nonresident;
            $state_nonresident = !empty($state_nonresident) ? $state_nonresident : $state_resident;

            $state_code = $this->get_state_code_by_name($state);

            echo '<div class="ustc2025-results">';
            echo $this->render_result_column(__('Non resident', 'ustc2025'), $federal_nonresident, $state_nonresident);
            echo $this->render_result_column(__('Resident', 'ustc2025'), $federal_resident, $state_resident);
            echo '</div>';
        }

        echo '</div>';

        return ob_get_clean();
    }

    private function render_result_column($title, $federal, $state)
    {
        $html = '<div class="ustc2025-card ustc2025-column">';
        $html .= '<h3>' . esc_html($title) . '</h3>';
        $html .= '<p><strong>' . esc_html($this->get_result_label('federal', $federal['tax_diff'])) . '</strong> ' . $this->format_result_message($federal['tax_diff']) . '</p>';
        $html .= '<p><strong>' . esc_html($this->get_result_label('state', $state['tax_diff'])) . '</strong> ' . $this->format_result_message($state['tax_diff'], $state['na'] ?? false) . '</p>';
        $html .= '</div>';
        return $html;
    }

    private function get_result_label($type, $tax_diff)
    {
        $is_refund = $tax_diff < 0;

        if ($type === 'federal') {
            return $is_refund ? __('Federal Tax Refund:', 'ustc2025') : __('Federal Tax Owed:', 'ustc2025');
        }

        return $is_refund ? __('State Tax Refund:', 'ustc2025') : __('State Tax Owed:', 'ustc2025');
    }

    private function format_result_message($tax_diff, $is_na = false)
    {
        if ($is_na) {
            return esc_html__('N/A', 'ustc2025');
        }
        $class = $tax_diff > 0 ? 'ustc2025-owe' : 'ustc2025-refund';
        $message = $tax_diff > 0 ? __('', 'ustc2025') : __('', 'ustc2025');
        $amount = number_format(ceil(abs($tax_diff)), 0);
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

    private function calculate_state($state, $gross, $withholding, $residency, $settings, $federal_result = null)
    {
        $breakdown = [];
        $code = $this->get_state_code_by_name($state);
        if ($code === 'AL') {
            return $this->alabama_tax($gross, $withholding, $residency, $settings, $federal_result);
        }
        if ($code === 'AR') {
            return $this->arkansas_tax($gross, $withholding, $settings, $residency);
        }
        if ($code === 'CT') {
            return $this->connecticut_tax($gross, $withholding, $settings, $residency);
        }
        if ($code === 'HI') {
            return $this->hawaii_tax($gross, $withholding, $residency, $settings);
        }
        if ($code === 'OR') {
            return $this->oregon_tax($gross, $withholding, $residency, $settings, $federal_result);
        }
        if ($code === 'MN') {
            return $this->minnesota_tax($gross, $withholding, $residency, $settings);
        }
        if ($code === 'GE') {
            return $this->georgia_tax($gross, $withholding, $residency, $settings);
        }
        if ($code === 'DC') {
            $breakdown[] = __('Full refund of state withholding applied.', 'ustc2025');
            return ['tax' => 0, 'tax_diff' => -$withholding, 'breakdown' => $breakdown];
        }
        if ($code === 'ME') {
            return $this->maine_tax($gross, $withholding, $residency, $settings, $breakdown);
        }
        if ($code === 'ID') {
            return $this->idaho_tax($gross, $withholding, $residency, $settings, $breakdown);
        }
        if ($code === 'IA') {
            return $this->iowa_tax($gross, $withholding, $residency, $settings, $breakdown);
        }
        if ($code === 'KY') {
            return $this->kentucky_tax($gross, $withholding, $settings, $breakdown);
        }
        if ($code === 'LA') {
            return $this->louisiana_tax($gross, $withholding, $settings, $breakdown);
        }
        if ($code === 'ND') {
            return $this->north_dakota_tax($gross, $withholding, $residency, $settings, $breakdown);
        }
        if ($code === 'PA') {
            return $this->pennsylvania_tax($gross, $withholding, $settings, $breakdown);
        }
        if ($code === 'MO') {
            return $this->missouri_tax($gross, $withholding, $residency, $settings, $federal_result, $breakdown);
        }
        if ($code === 'VA') {
            return $this->virginia_tax($gross, $withholding, $residency, $settings, $breakdown);
        }

        $deduction = isset($settings['state_deduction']) ? floatval($settings['state_deduction']) : 0;
        $personal_credit = isset($settings['personal_credit']) ? floatval($settings['personal_credit']) : 0;
        $taxable = $gross - $deduction;
        $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - state_deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($deduction, 2), number_format($taxable, 2));
        if ($taxable < 0) {
            $taxable = 0;
        }

        if (!empty($settings['full_refund_threshold']) && $taxable < floatval($settings['full_refund_threshold'])) {
            $breakdown[] = __('Full refund threshold met; state tax set to 0.', 'ustc2025');
            return ['tax' => 0, 'tax_diff' => -$withholding, 'breakdown' => $breakdown];
        }

        $mode = isset($settings['calculation_mode']) ? $settings['calculation_mode'] : 'progressive_brackets';
        if ($mode === 'flat_rate') {
            $rate = isset($settings['flat_rate']) ? floatval($settings['flat_rate']) : 0;
            $tax = $taxable * ($rate / 100);
            $breakdown[] = sprintf(__('State tax = %s * %s%% = %s', 'ustc2025'), number_format($taxable, 2), $rate, number_format($tax, 2));
        } else {
            $brackets = isset($settings['brackets']) ? $settings['brackets'] : [];
            $tax = $this->apply_brackets($taxable, $brackets, $breakdown);
        }

        $tax_diff = $tax - $withholding - $personal_credit;
        $breakdown[] = sprintf(__('Tax - withholding - credit = %s - %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($personal_credit, 2), number_format($tax_diff, 2));
        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function arkansas_tax($gross, $withholding, $settings, $residency)
    {
        $breakdown = [];
        $taxable = $gross;
        $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s)', 'ustc2025'), number_format($taxable, 2));

        $brackets = isset($settings['brackets']) && is_array($settings['brackets']) && !empty($settings['brackets'])
            ? $settings['brackets']
            : [
                ['min_income' => 0, 'max_income' => 4500, 'rate' => 2],
                ['min_income' => 4500, 'max_income' => '', 'rate' => 3.9],
            ];

        usort($brackets, function ($a, $b) {
            return floatval($a['min_income']) <=> floatval($b['min_income']);
        });

        $tax = 0;
        foreach ($brackets as $row) {
            $min = floatval($row['min_income']);
            $max = $row['max_income'] === '' ? null : floatval($row['max_income']);
            $rate = floatval($row['rate']);

            if ($taxable <= $min) {
                continue;
            }

            $upper = $max === null ? $taxable : min($max, $taxable);
            $portion = max(0, $upper - $min);
            $segment_tax = $portion * ($rate / 100);
            $tax += $segment_tax;

            $range_label = $max === null ? sprintf(__('above %s', 'ustc2025'), number_format($min, 2)) : sprintf(__('between %s and %s', 'ustc2025'), number_format($min, 2), number_format($max, 2));
            $breakdown[] = sprintf(__('Bracket %s: (%s - %s) * %s%% = %s', 'ustc2025'), $range_label, number_format($upper, 2), number_format($min, 2), $rate, number_format($segment_tax, 2));

            if ($max !== null && $taxable <= $max) {
                break;
            }
        }

        $breakdown[] = sprintf(__('Arkansas tax before credits: %s', 'ustc2025'), number_format($tax, 2));
        $credits = isset($settings['ar_tax_credits']) ? floatval($settings['ar_tax_credits']) : 0;
        $final_tax = $tax - $credits;
        $breakdown[] = sprintf(__('Arkansas tax credits applied: %s', 'ustc2025'), number_format($credits, 2));
        $breakdown[] = sprintf(__('AR final tax = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($credits, 2), number_format($final_tax, 2));

        $tax_diff = $final_tax - $withholding;
        $breakdown[] = sprintf(__('Final tax - withholding = %s - %s = %s', 'ustc2025'), number_format($final_tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));

        return ['tax' => $final_tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function connecticut_tax($gross, $withholding, $settings, $residency)
    {
        $breakdown = [];

        if ($gross <= 15000) {
            $breakdown[] = sprintf(__('Total income (%s) at or below Connecticut refund threshold; refunding state withholding.', 'ustc2025'), number_format($gross, 2));
            return ['tax' => 0, 'tax_diff' => -$withholding, 'breakdown' => $breakdown];
        }

        $deduction = isset($settings['ct_deduction']) ? floatval($settings['ct_deduction']) : 0;
        $taxable = $gross - $deduction;
        $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - CT deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($deduction, 2), number_format($taxable, 2));
        if ($taxable < 0) {
            $taxable = 0;
        }

        $brackets = isset($settings['brackets']) && is_array($settings['brackets']) && !empty($settings['brackets'])
            ? $settings['brackets']
            : [
                ['min_income' => 0, 'max_income' => 10000, 'base_tax' => 0, 'rate' => 2],
                ['min_income' => 10000, 'max_income' => 50000, 'base_tax' => 200, 'rate' => 4.5],
                ['min_income' => 50000, 'max_income' => 100000, 'base_tax' => 2000, 'rate' => 5.5],
                ['min_income' => 100000, 'max_income' => 200000, 'base_tax' => 4750, 'rate' => 6],
                ['min_income' => 200000, 'max_income' => '', 'base_tax' => 10750, 'rate' => 6],
            ];

        $tax = $this->apply_brackets($taxable, $brackets, $breakdown);

        $tax_diff = $tax - $withholding;
        $result_label = $tax_diff < 0 ? __('State Tax Return', 'ustc2025') : __('State Tax Owed', 'ustc2025');
        $breakdown[] = sprintf(__('Connecticut tax - withholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));
        $breakdown[] = sprintf(__('%s %s', 'ustc2025'), $result_label, number_format($tax_diff, 2));

        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function georgia_tax($gross, $withholding, $residency, $settings)
    {
        $breakdown = [];
        $deduction = isset($settings['ge_deduction']) ? floatval($settings['ge_deduction']) : 0;
        $credit = isset($settings['ge_credit']) ? floatval($settings['ge_credit']) : 0;
        $rate = isset($settings['flat_rate']) ? floatval($settings['flat_rate']) : 0;

        if ($residency === 'resident') {
            $taxable = $gross - $deduction;
            $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - GE deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($deduction, 2), number_format($taxable, 2));
            if ($taxable < 0) {
                $taxable = 0;
            }

            $tax = $taxable * ($rate / 100);
            $breakdown[] = sprintf(__('Georgia resident tax = %s * %s%% = %s', 'ustc2025'), number_format($taxable, 2), $rate, number_format($tax, 2));
            $tax_diff = $tax - $withholding;
            $breakdown[] = sprintf(__('Resident tax - withholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));
        } else {
            $taxable = $gross - $withholding;
            $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - StateWithholding (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($withholding, 2), number_format($taxable, 2));
            if ($taxable < 0) {
                $taxable = 0;
            }

            $tax = $taxable * ($rate / 100);
            $breakdown[] = sprintf(__('Georgia non-resident tax = %s * %s%% = %s', 'ustc2025'), number_format($taxable, 2), $rate, number_format($tax, 2));
            $breakdown[] = sprintf(__('Georgia credit applied for non-resident: %s', 'ustc2025'), number_format($credit, 2));
            $tax_diff = $tax - $withholding - $credit;
            $breakdown[] = sprintf(__('Non-resident tax - withholding - credit = %s - %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($credit, 2), number_format($tax_diff, 2));
        }

        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function hawaii_tax($gross, $withholding, $residency, $settings)
    {
        $breakdown = [];
        $state_deduction = isset($settings['state_deduction']) ? floatval($settings['state_deduction']) : 0;
        $personal_exemption = isset($settings['personal_credit']) ? floatval($settings['personal_credit']) : 0;

        if ($residency === 'resident') {
            $taxable = $gross - $state_deduction - $personal_exemption;
            $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - HI deduction (%s) - HI personal exemption (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($state_deduction, 2), number_format($personal_exemption, 2), number_format($taxable, 2));
        } else {
            $taxable = $gross - $personal_exemption;
            $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - HI personal exemption (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($personal_exemption, 2), number_format($taxable, 2));
        }

        if ($taxable < 0) {
            $taxable = 0;
        }

        $brackets = [
            ['min_income' => 0, 'max_income' => 9600, 'rate' => 1.4],
            ['min_income' => 9600, 'max_income' => 14400, 'rate' => 3.2],
            ['min_income' => 14400, 'max_income' => 19200, 'rate' => 5.5],
            ['min_income' => 19200, 'max_income' => 24000, 'rate' => 6.4],
            ['min_income' => 24000, 'max_income' => 36000, 'rate' => 6.8],
            ['min_income' => 36000, 'max_income' => 48000, 'rate' => 7.2],
            ['min_income' => 48000, 'max_income' => 125000, 'rate' => 7.6],
            ['min_income' => 125000, 'max_income' => null, 'rate' => 7.9],
        ];

        $tax = 0;
        foreach ($brackets as $row) {
            $min = floatval($row['min_income']);
            $max = $row['max_income'] === '' ? null : $row['max_income'];
            $rate = floatval($row['rate']);

            if ($taxable <= $min) {
                continue;
            }

            $upper = $max === null ? $taxable : min($max, $taxable);
            $portion = max(0, $upper - $min);
            $segment_tax = $portion * ($rate / 100);
            $tax += $segment_tax;

            $range_label = $max === null ? sprintf(__('above %s', 'ustc2025'), number_format($min, 2)) : sprintf(__('between %s and %s', 'ustc2025'), number_format($min, 2), number_format($max, 2));
            $breakdown[] = sprintf(__('Bracket %s: (%s - %s) * %s%% = %s', 'ustc2025'), $range_label, number_format($upper, 2), number_format($min, 2), $rate, number_format($segment_tax, 2));

            if ($max !== null && $taxable <= $max) {
                break;
            }
        }

        $tax_diff = $tax - $withholding;
        $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));

        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function oregon_tax($gross, $withholding, $residency, $settings, $federal_result)
    {
        $breakdown = [];
        $federal_tax = isset($federal_result['tax']) ? floatval($federal_result['tax']) : 0;
        $state_deduction = isset($settings['state_deduction']) ? floatval($settings['state_deduction']) : 0;
        $personal_exemption = isset($settings['personal_credit']) ? floatval($settings['personal_credit']) : 0;

        $taxable = $gross - $federal_tax;
        $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - FederalTax (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($federal_tax, 2), number_format($taxable, 2));
        if ($taxable < 0) {
            $taxable = 0;
        }

        $mode = isset($settings['calculation_mode']) ? $settings['calculation_mode'] : 'progressive_brackets';
        if ($mode === 'flat_rate') {
            $rate = isset($settings['flat_rate']) ? floatval($settings['flat_rate']) : 0;
            $tax = $taxable * ($rate / 100);
            $breakdown[] = sprintf(__('State tax = %s * %s%% = %s', 'ustc2025'), number_format($taxable, 2), $rate, number_format($tax, 2));
        } else {
            $brackets = isset($settings['brackets']) ? $settings['brackets'] : [];
            if (empty($brackets)) {
                $tax = 0;
                $breakdown[] = __('No brackets configured; state tax set to 0.', 'ustc2025');
            } else {
                usort($brackets, function ($a, $b) {
                    return floatval($a['min_income']) <=> floatval($b['min_income']);
                });

                $tax = 0;
                foreach ($brackets as $index => $row) {
                    $min = floatval($row['min_income']);
                    $max = $row['max_income'] === '' ? null : floatval($row['max_income']);
                    $rate = floatval($row['rate']);

                    if ($taxable <= $min) {
                        continue;
                    }

                    $upper = $max === null ? $taxable : min($max, $taxable);
                    $portion = max(0, $upper - $min);
                    $segment_tax = $portion * ($rate / 100);
                    $tax += $segment_tax;

                    $range_label = $max === null ? sprintf(__('above %s', 'ustc2025'), number_format($min, 2)) : sprintf(__('between %s and %s', 'ustc2025'), number_format($min, 2), number_format($max, 2));
                    $breakdown[] = sprintf(__('Bracket %s: (%s - %s) * %s%% = %s', 'ustc2025'), $range_label, number_format($upper, 2), number_format($min, 2), $rate, number_format($segment_tax, 2));

                    if ($max !== null && $taxable <= $max) {
                        break;
                    }
                }
            }
        }

        $tax_diff = $tax - $withholding - $personal_exemption;
        $breakdown[] = sprintf(__('Tax - withholding - personal exemption = %s - %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($personal_exemption, 2), number_format($tax_diff, 2));
        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function minnesota_tax($gross, $withholding, $residency, $settings)
    {
        $breakdown = [];
        $deduction = isset($settings['state_deduction']) ? floatval($settings['state_deduction']) : 0;

        $taxable = $gross - $deduction;
        $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - MN deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($deduction, 2), number_format($taxable, 2));
        if ($taxable < 0) {
            $taxable = 0;
        }

        $brackets = isset($settings['brackets']) ? $settings['brackets'] : [];
        if (empty($brackets)) {
            $tax = 0;
            $breakdown[] = __('No brackets configured; state tax set to 0.', 'ustc2025');
        } else {
            usort($brackets, function ($a, $b) {
                return floatval($a['min_income']) <=> floatval($b['min_income']);
            });

            $tax = 0;
            foreach ($brackets as $row) {
                $min = floatval($row['min_income']);
                $max = $row['max_income'] === '' ? null : floatval($row['max_income']);
                $rate = floatval($row['rate']);

                if ($taxable <= $min) {
                    continue;
                }

                $upper = $max === null ? $taxable : min($max, $taxable);
                $portion = max(0, $upper - $min);
                $segment_tax = $portion * ($rate / 100);
                $tax += $segment_tax;

                $range_label = $max === null ? sprintf(__('above %s', 'ustc2025'), number_format($min, 2)) : sprintf(__('between %s and %s', 'ustc2025'), number_format($min, 2), number_format($max, 2));
                $breakdown[] = sprintf(__('Bracket %s: (%s - %s) * %s%% = %s', 'ustc2025'), $range_label, number_format($upper, 2), number_format($min, 2), $rate, number_format($segment_tax, 2));

                if ($max !== null && $taxable <= $max) {
                    break;
                }
            }
        }

        $tax_diff = $tax - $withholding;
        $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));
        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function alabama_tax($gross, $withholding, $residency, $settings, $federal_result)
    {
        $breakdown = [];
        $state_deduction = isset($settings['state_deduction']) ? floatval($settings['state_deduction']) : 0;
        $personal_exemption = isset($settings['personal_credit']) ? floatval($settings['personal_credit']) : 0;
        $federal_refund = 0;

        if ($residency === 'resident') {
            $taxable = $gross - $state_deduction - $personal_exemption;
            $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - AL deduction (%s) - AL personal exemption (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($state_deduction, 2), number_format($personal_exemption, 2), number_format($taxable, 2));
        } else {
            if ($federal_result !== null && isset($federal_result['tax_diff'])) {
                $federal_refund = max(0, -floatval($federal_result['tax_diff']));
            }
            $taxable = $gross - $state_deduction - $personal_exemption - $federal_refund;
            $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - AL deduction (%s) - AL personal exemption (%s) - Federal refund (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($state_deduction, 2), number_format($personal_exemption, 2), number_format($federal_refund, 2), number_format($taxable, 2));
        }

        if ($taxable < 0) {
            $taxable = 0;
        }

        $brackets = [
            ['min_income' => 0, 'max_income' => 500, 'rate' => 2],
            ['min_income' => 500, 'max_income' => 3000, 'rate' => 4],
            ['min_income' => 3000, 'max_income' => null, 'rate' => 5],
        ];

        $tax = 0;
        foreach ($brackets as $row) {
            $min = floatval($row['min_income']);
            $max = $row['max_income'] === '' ? null : $row['max_income'];
            $rate = floatval($row['rate']);

            if ($taxable <= $min) {
                continue;
            }

            $upper = $max === null ? $taxable : min($max, $taxable);
            $portion = max(0, $upper - $min);
            $segment_tax = $portion * ($rate / 100);
            $tax += $segment_tax;

            $range_label = $max === null ? sprintf(__('above %s', 'ustc2025'), number_format($min, 2)) : sprintf(__('between %s and %s', 'ustc2025'), number_format($min, 2), number_format($max, 2));
            $breakdown[] = sprintf(__('Bracket %s: (%s - %s) * %s%% = %s', 'ustc2025'), $range_label, number_format($upper, 2), number_format($min, 2), $rate, number_format($segment_tax, 2));

            if ($max !== null && $taxable <= $max) {
                break;
            }
        }

        $tax_diff = $tax - $withholding;
        $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));

        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function apply_brackets($taxable, $brackets, &$breakdown)
    {
        if ($taxable <= 0 || empty($brackets)) {
            return 0;
        }

        usort($brackets, function ($a, $b) {
            return floatval($a['min_income']) <=> floatval($b['min_income']);
        });

        foreach ($brackets as $row) {
            $min = floatval($row['min_income']);
            $max = $row['max_income'] === '' ? null : floatval($row['max_income']);
            $base = floatval($row['base_tax']);
            $rate = floatval($row['rate']);
            if ($taxable < $min) {
                continue;
            }
            if ($max !== null && $taxable > $max) {
                continue;
            }
            $tax = $base + ($taxable - $min) * ($rate / 100);
            $range = $max ? sprintf(__('between %s and %s', 'ustc2025'), number_format($min, 2), number_format($max, 2)) : sprintf(__('above %s', 'ustc2025'), number_format($min, 2));
            $breakdown[] = sprintf(__('Bracket %s: base %s + (%s - %s) * %s%% = %s', 'ustc2025'), $range, number_format($base, 2), number_format($taxable, 2), number_format($min, 2), $rate, number_format($tax, 2));
            return $tax;
        }

        $last = end($brackets);
        $min = floatval($last['min_income']);
        $base = floatval($last['base_tax']);
        $rate = floatval($last['rate']);
        $tax = $base + ($taxable - $min) * ($rate / 100);
        $breakdown[] = sprintf(__('Top bracket: base %s + (%s - %s) * %s%% = %s', 'ustc2025'), number_format($base, 2), number_format($taxable, 2), number_format($min, 2), $rate, number_format($tax, 2));
        return $tax;
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

    private function missouri_tax($gross, $withholding, $residency, $settings, $federal_result, &$breakdown)
    {
        $deduction = isset($settings['state_deduction']) ? floatval($settings['state_deduction']) : 0;
        $taxable = 0;

        if ($residency === 'resident') {
            $taxable = $gross - $deduction;
            $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - MO deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($deduction, 2), number_format($taxable, 2));
        } else {
            $federal_tax = isset($federal_result['tax']) ? floatval($federal_result['tax']) : 0;
            $income_ranges = isset($settings['income_range_brackets']) ? $settings['income_range_brackets'] : [];
            $range_rate = 0;

            usort($income_ranges, function ($a, $b) {
                return floatval($a['min_income']) <=> floatval($b['min_income']);
            });

            foreach ($income_ranges as $row) {
                $min = floatval($row['min_income']);
                $max = $row['max_income'] === '' ? null : floatval($row['max_income']);
                $rate = floatval($row['rate']);

                if ($federal_tax < $min) {
                    continue;
                }

                if ($max === null || $federal_tax <= $max) {
                    $range_rate = $rate;
                    $label = $max === null ? sprintf(__('above %s', 'ustc2025'), number_format($min, 2)) : sprintf(__('between %s and %s', 'ustc2025'), number_format($min, 2), number_format($max, 2));
                    $breakdown[] = sprintf(__('Income range bracket %s at %s%% selected from federal tax %s', 'ustc2025'), $label, $rate, number_format($federal_tax, 2));
                    break;
                }
            }

            $mo_income_range = $federal_tax * ($range_rate / 100);
            $breakdown[] = sprintf(__('MO income range factor = FederalTax (%s) * %s%% = %s', 'ustc2025'), number_format($federal_tax, 2), $range_rate, number_format($mo_income_range, 2));
            $taxable = $gross - $mo_income_range;
            $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - MO income range (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($mo_income_range, 2), number_format($taxable, 2));
        }

        if ($taxable < 0) {
            $taxable = 0;
        }

        $brackets = isset($settings['brackets']) ? $settings['brackets'] : [];
        $tax = $this->apply_brackets($taxable, $brackets, $breakdown);
        $tax_diff = $tax - $withholding;
        $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));
        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function virginia_tax($gross, $withholding, $residency, $settings, &$breakdown)
    {
        $state_deduction = isset($settings['state_deduction']) ? floatval($settings['state_deduction']) : 0;
        $personal_deduction = isset($settings['va_personal_deduction']) ? floatval($settings['va_personal_deduction']) : 0;
        $low_income_credit = isset($settings['va_low_income_credit']) ? floatval($settings['va_low_income_credit']) : 0;
        $brackets = isset($settings['brackets']) ? $settings['brackets'] : [];

        if ($residency === 'resident') {
            $taxable = $gross - $state_deduction - $personal_deduction;
            $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - VA deduction (%s) - VA personal deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($state_deduction, 2), number_format($personal_deduction, 2), number_format($taxable, 2));
        } else {
            $taxable = $gross - $personal_deduction;
            $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - VA personal deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($personal_deduction, 2), number_format($taxable, 2));
        }

        if ($taxable < 0) {
            $taxable = 0;
        }

        if (empty($brackets)) {
            $tax = 0;
            $breakdown[] = __('No brackets configured; state tax set to 0.', 'ustc2025');
        } else {
            $tax = $this->apply_brackets($taxable, $brackets, $breakdown);
        }

        if ($gross <= 15060) {
            $final_tax = $tax - $low_income_credit;
            $breakdown[] = sprintf(__('Low income credit applied: VA tax (%s) - credit (%s) = %s', 'ustc2025'), number_format($tax, 2), number_format($low_income_credit, 2), number_format($final_tax, 2));
        } else {
            $final_tax = $tax;
            $breakdown[] = __('Income above low-income credit threshold; no credit applied.', 'ustc2025');
        }

        $tax_diff = $final_tax - $withholding;
        $breakdown[] = sprintf(__('Final VA tax - withholding = %s - %s = %s', 'ustc2025'), number_format($final_tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));

        return ['tax' => $final_tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
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

    private function idaho_tax($gross, $withholding, $residency, $settings, &$breakdown)
    {
        $flat_rate = isset($settings['flat_rate']) ? floatval($settings['flat_rate']) : 0;
        $deduction = isset($settings['id_deduction']) ? floatval($settings['id_deduction']) : 0;
        $pbf_tax = isset($settings['permanent_building_fund_tax']) ? floatval($settings['permanent_building_fund_tax']) : 0;

        if ($residency === 'resident') {
            $taxable = max(0, $gross - $deduction);
            $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - ID deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($deduction, 2), number_format($taxable, 2));
        } else {
            $taxable = $gross;
            $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s)', 'ustc2025'), number_format($gross, 2));
        }

        $rate_decimal = $flat_rate / 100;
        $id_tax = $taxable * $rate_decimal;
        $breakdown[] = sprintf(__('Idaho tax = %s * %s%% = %s', 'ustc2025'), number_format($taxable, 2), $flat_rate, number_format($id_tax, 2));

        $final_tax = $id_tax + $pbf_tax;
        $breakdown[] = sprintf(__('Final Idaho tax = Idaho tax (%s) + Permanent building fund tax (%s) = %s', 'ustc2025'), number_format($id_tax, 2), number_format($pbf_tax, 2), number_format($final_tax, 2));

        $tax_diff = $final_tax - $withholding;
        $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($final_tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));

        return ['tax' => $final_tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function iowa_tax($gross, $withholding, $residency, $settings, &$breakdown)
    {
        $federal_settings = $this->get_federal_settings();
        $federal_deduction = isset($federal_settings['std_deduction']) ? floatval($federal_settings['std_deduction']) : 0;
        $flat_rate = isset($settings['flat_rate']) ? floatval($settings['flat_rate']) : 0;
        $personal_credit = isset($settings['ia_personal_credit']) ? floatval($settings['ia_personal_credit']) : 0;

        $taxable = max(0, $gross - $federal_deduction);
        $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - Federal deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($federal_deduction, 2), number_format($taxable, 2));

        $ia_tax = $taxable * ($flat_rate / 100);
        $breakdown[] = sprintf(__('Iowa tax = %s * %s%% = %s', 'ustc2025'), number_format($taxable, 2), $flat_rate, number_format($ia_tax, 2));

        $final_tax = $ia_tax - $personal_credit;
        $breakdown[] = sprintf(__('Final Iowa tax = Iowa tax (%s) - Iowa personal credit (%s) = %s', 'ustc2025'), number_format($ia_tax, 2), number_format($personal_credit, 2), number_format($final_tax, 2));

        $tax_diff = $final_tax - $withholding;
        $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($final_tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));

        return ['tax' => $final_tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function louisiana_tax($gross, $withholding, $settings, &$breakdown)
    {
        $standard_deduction = isset($settings['state_deduction']) ? floatval($settings['state_deduction']) : 0;
        $flat_rate = isset($settings['flat_rate']) ? floatval($settings['flat_rate']) : 0;

        $taxable = max(0, $gross - $standard_deduction);
        $breakdown[] = sprintf(__('TaxableIncome = Total income (%s) - LA standard deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($standard_deduction, 2), number_format($taxable, 2));

        $la_tax = $taxable * ($flat_rate / 100);
        $breakdown[] = sprintf(__('Louisiana tax = %s * %s%% = %s', 'ustc2025'), number_format($taxable, 2), $flat_rate, number_format($la_tax, 2));

        $tax_diff = $la_tax - $withholding;
        $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($la_tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));

        return ['tax' => $la_tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function north_dakota_tax($gross, $withholding, $residency, $settings, &$breakdown)
    {
        $federal_settings = $this->get_federal_settings();
        $federal_deduction = isset($federal_settings['std_deduction']) ? floatval($federal_settings['std_deduction']) : 0;

        if ($residency === 'resident') {
            $taxable = $gross - $federal_deduction;
            $breakdown[] = sprintf(__('ND taxable income (resident) = Total income (%s) - Federal deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($federal_deduction, 2), number_format($taxable, 2));
        } else {
            $taxable = $gross - $withholding;
            $breakdown[] = sprintf(__('ND taxable income (non-resident) = Total income (%s) - State withholding (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($withholding, 2), number_format($taxable, 2));
        }

        if ($taxable < 0) {
            $taxable = 0;
        }

        $brackets = isset($settings['brackets']) && !empty($settings['brackets']) ? $settings['brackets'] : [
            ['min_income' => 0, 'max_income' => 55975, 'rate' => 0],
            ['min_income' => 55975, 'max_income' => 252325, 'rate' => 1.95],
            ['min_income' => 252325, 'max_income' => null, 'rate' => 2.5],
        ];

        usort($brackets, function ($a, $b) {
            return floatval($a['min_income']) <=> floatval($b['min_income']);
        });

        $tax = 0;
        foreach ($brackets as $row) {
            $min = floatval($row['min_income']);
            $max = $row['max_income'] === '' ? null : $row['max_income'];
            $rate = floatval($row['rate']);

            if ($taxable <= $min) {
                continue;
            }

            $upper = $max === null ? $taxable : min($max, $taxable);
            $portion = max(0, $upper - $min);
            $segment_tax = $portion * ($rate / 100);
            $tax += $segment_tax;

            $range_label = $max === null ? sprintf(__('above %s', 'ustc2025'), number_format($min, 2)) : sprintf(__('between %s and %s', 'ustc2025'), number_format($min, 2), number_format($max, 2));
            $breakdown[] = sprintf(__('Bracket %s: (%s - %s) * %s%% = %s', 'ustc2025'), $range_label, number_format($upper, 2), number_format($min, 2), $rate, number_format($segment_tax, 2));

            if ($max !== null && $taxable <= $max) {
                break;
            }
        }

        $tax_diff = $tax - $withholding;
        $breakdown[] = sprintf(__('ND tax - withholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));

        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function pennsylvania_tax($gross, $withholding, $settings, &$breakdown)
    {
        $flat_rate = isset($settings['flat_rate']) ? floatval($settings['flat_rate']) : 0;

        $taxable = max(0, $gross);
        $breakdown[] = sprintf(__('TaxableIncome = Total income (%s)', 'ustc2025'), number_format($taxable, 2));

        $pa_tax = $taxable * ($flat_rate / 100);
        $breakdown[] = sprintf(__('Pennsylvania tax = %s * %s%% = %s', 'ustc2025'), number_format($taxable, 2), $flat_rate, number_format($pa_tax, 2));

        $tax_diff = $pa_tax - $withholding;
        $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($pa_tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));

        return ['tax' => $pa_tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function kentucky_tax($gross, $withholding, $settings, &$breakdown)
    {
        $standard_deduction = isset($settings['state_deduction']) ? floatval($settings['state_deduction']) : 0;
        $flat_rate = isset($settings['flat_rate']) ? floatval($settings['flat_rate']) : 0;

        $taxable = max(0, $gross - $standard_deduction);
        $breakdown[] = sprintf(__('TaxableIncome = Total income (%s) - KY standard deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($standard_deduction, 2), number_format($taxable, 2));

        $ky_tax = $taxable * ($flat_rate / 100);
        $breakdown[] = sprintf(__('Kentucky tax = %s * %s%% = %s', 'ustc2025'), number_format($taxable, 2), $flat_rate, number_format($ky_tax, 2));

        if ($taxable <= 15650) {
            $personal_credit = $ky_tax;
            $breakdown[] = sprintf(__('KY personal credit at 100%% of tax because taxable income <= 15,650: %s', 'ustc2025'), number_format($personal_credit, 2));
        } elseif ($taxable < 20865) {
            $personal_credit = $ky_tax * 0.8;
            $breakdown[] = sprintf(__('KY personal credit at 80%% of tax because taxable income between 15,650 and 20,865: %s', 'ustc2025'), number_format($personal_credit, 2));
        } else {
            $personal_credit = 0;
            $breakdown[] = __('KY personal credit not applied because taxable income exceeds 20,865.', 'ustc2025');
        }

        $final_tax = $ky_tax - $personal_credit;
        $breakdown[] = sprintf(__('Final Kentucky tax = KY tax (%s) - KY personal credit (%s) = %s', 'ustc2025'), number_format($ky_tax, 2), number_format($personal_credit, 2), number_format($final_tax, 2));

        $tax_diff = $final_tax - $withholding;
        $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($final_tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));

        return ['tax' => $final_tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function maine_tax($gross, $withholding, $residency, $settings, &$breakdown)
    {
        $resident_deduction = isset($settings['deduction_resident']) ? floatval($settings['deduction_resident']) : 0;
        $nonresident_deduction = isset($settings['deduction_nonresident']) ? floatval($settings['deduction_nonresident']) : 0;
        $deduction = $residency === 'resident' ? $resident_deduction : $nonresident_deduction;
        $taxable = max(0, $gross - $deduction);
        $breakdown[] = sprintf(__('TaxableIncome = GrossIncome (%s) - deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($deduction, 2), number_format($taxable, 2));

        $brackets = isset($settings['brackets']) ? $settings['brackets'] : [];
        if (empty($brackets)) {
            $tax = 0;
            $breakdown[] = __('No brackets configured; state tax set to 0.', 'ustc2025');
        } else {
            usort($brackets, function ($a, $b) {
                return floatval($a['min_income']) <=> floatval($b['min_income']);
            });

            $tax = 0;
            foreach ($brackets as $row) {
                $min = floatval($row['min_income']);
                $max = $row['max_income'] === '' ? null : floatval($row['max_income']);
                $rate = floatval($row['rate']);

                if ($taxable <= $min) {
                    continue;
                }

                $upper = $max === null ? $taxable : min($max, $taxable);
                $portion = max(0, $upper - $min);
                $segment_tax = $portion * ($rate / 100);
                $tax += $segment_tax;

                $range_label = $max === null ? sprintf(__('above %s', 'ustc2025'), number_format($min, 2)) : sprintf(__('between %s and %s', 'ustc2025'), number_format($min, 2), number_format($max, 2));
                $breakdown[] = sprintf(__('Bracket %s: (%s - %s) * %s%% = %s', 'ustc2025'), $range_label, number_format($upper, 2), number_format($min, 2), $rate, number_format($segment_tax, 2));

                if ($max !== null && $taxable <= $max) {
                    break;
                }
            }
        }

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
