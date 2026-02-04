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
        ['code' => 'AK', 'name' => 'Alaska'],
        ['code' => 'AR', 'name' => 'Arkansas'],
        ['code' => 'AZ', 'name' => 'Arizona'],
        ['code' => 'CA', 'name' => 'California'],
        ['code' => 'CO', 'name' => 'Colorado'],
        ['code' => 'CT', 'name' => 'Connecticut'],
        ['code' => 'DC', 'name' => 'District of Columbia'],
        ['code' => 'DE', 'name' => 'Delaware'],
        ['code' => 'FL', 'name' => 'Florida'],
        ['code' => 'GE', 'name' => 'Georgia'],
        ['code' => 'HI', 'name' => 'Hawaii'],
        ['code' => 'IA', 'name' => 'Iowa'],
        ['code' => 'ID', 'name' => 'Idaho'],
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
        ['code' => 'NH', 'name' => 'New Hampshire'],
        ['code' => 'NV', 'name' => 'Nevada'],
        ['code' => 'NJ', 'name' => 'New Jersey'],
        ['code' => 'NY', 'name' => 'New York'],
        ['code' => 'OR', 'name' => 'Oregon'],
        ['code' => 'PA', 'name' => 'Pennsylvania'],
        ['code' => 'RI', 'name' => 'Rhode Island'],
        ['code' => 'SC', 'name' => 'South Carolina'],
        ['code' => 'SD', 'name' => 'South Dakota'],
        ['code' => 'TN', 'name' => 'Tennessee'],
        ['code' => 'TX', 'name' => 'Texas'],
        ['code' => 'UT', 'name' => 'Utah'],
        ['code' => 'VT', 'name' => 'Vermont'],
        ['code' => 'VA', 'name' => 'Virginia'],
        ['code' => 'WA', 'name' => 'Washington'],
        ['code' => 'WI', 'name' => 'Wisconsin'],
        ['code' => 'WY', 'name' => 'Wyoming'],
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
            'AK' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 0,
                'brackets' => [],
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
                'state_deduction' => 5706,
                'personal_credit' => 153,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 10756, 'base_tax' => 0, 'rate' => 1],
                    ['min_income' => 10756, 'max_income' => 25499, 'base_tax' => 107.56, 'rate' => 2],
                    ['min_income' => 25499, 'max_income' => 40245, 'base_tax' => 402.42, 'rate' => 4],
                    ['min_income' => 40245, 'max_income' => 55866, 'base_tax' => 992.26, 'rate' => 6],
                    ['min_income' => 55866, 'max_income' => 70606, 'base_tax' => 1929.52, 'rate' => 8],
                    ['min_income' => 70606, 'max_income' => 360659, 'base_tax' => 3108.72, 'rate' => 9.3],
                    ['min_income' => 360659, 'max_income' => '', 'base_tax' => 30077.65, 'rate' => 9.3],
                ],
            ],
            'CO' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 4.4,
                'colorado_deduction' => 15600,
                'resident_flat_rate' => 4.4,
                'non_resident_flat_rate' => 4.25,
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
                'id_deduction' => 15000,
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
            'FL' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 0,
                'brackets' => [],
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
                'poverty_credit' => 15650,
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
                    ['min_income' => 215400, 'max_income' => '', 'base_tax' => 12356, 'rate' => 6],
                ],
                'personal_credit_brackets' => [
                    ['min_income' => 0, 'max_income' => 10000, 'credit' => 65],
                    ['min_income' => 10001, 'max_income' => 20000, 'credit' => 50],
                    ['min_income' => 20001, 'max_income' => 28000, 'credit' => 30],
                    ['min_income' => 28001, 'max_income' => '', 'credit' => 0],
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
            'NH' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 0,
                'brackets' => [],
            ],
            'NV' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 0,
                'brackets' => [],
            ],
            'OR' => [
                'or_resident_deduction' => 2835,
                'or_personal_exemption' => 256,
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
                'state_deduction' => 0,
                'personal_credit' => 0,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'rh_personal_deduction_res' => 16000,
                'rh_personal_deduction_nonres' => 5100,
                'deduction_resident' => 16000,
                'deduction_nonresident' => 5100,
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 79900, 'base_tax' => 0, 'rate' => 3.75],
                    ['min_income' => 79900, 'max_income' => 181650, 'base_tax' => 2996.25, 'rate' => 4.75],
                    ['min_income' => 181650, 'max_income' => '', 'base_tax' => 7829.38, 'rate' => 5.99],
                ],
            ],
            'SC' => [
                'state_deduction' => 15750,
                'personal_credit' => 0,
                'sc_resident_deduction' => 15750,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 3560, 'base_tax' => 0, 'rate' => 0],
                    ['min_income' => 3560, 'max_income' => 17830, 'base_tax' => 0, 'rate' => 3],
                    ['min_income' => 17830, 'max_income' => '', 'base_tax' => 428.1, 'rate' => 6.2],
                ],
            ],
            'SD' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 0,
                'brackets' => [],
            ],
            'TN' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 0,
                'brackets' => [],
            ],
            'TX' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 0,
                'brackets' => [],
            ],
            'UT' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'utah_deduction' => 18213,
                'utah_flat_rate' => 4.5,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 4.5,
                'brackets' => [],
            ],
            'VT' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'vermont_deduction' => 12950,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 3825, 'base_tax' => 0, 'rate' => 0],
                    ['min_income' => 3825, 'max_income' => 53225, 'base_tax' => 0, 'rate' => 3.35],
                    ['min_income' => 53225, 'max_income' => 123525, 'base_tax' => 1654.90, 'rate' => 6.60],
                    ['min_income' => 123525, 'max_income' => 253525, 'base_tax' => 6293.70, 'rate' => 7.60],
                    ['min_income' => 253525, 'max_income' => '', 'base_tax' => 16173.70, 'rate' => 8.75],
                ],
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
            'WA' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 0,
                'brackets' => [],
            ],
            'WI' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'calculation_mode' => 'progressive_brackets',
                'flat_rate' => '',
                'wisconsin_deduction' => 14260,
                'non_resident_wisconsin_deduction' => 700,
                'deduction_resident' => 14260,
                'deduction_nonresident' => 700,
                'brackets' => [
                    ['min_income' => 0, 'max_income' => 14680, 'base_tax' => 0, 'rate' => 3.5],
                    ['min_income' => 14680, 'max_income' => 29370, 'base_tax' => 513.8, 'rate' => 4.4],
                    ['min_income' => 29370, 'max_income' => 323290, 'base_tax' => 1160.16, 'rate' => 5.3],
                    ['min_income' => 323290, 'max_income' => '', 'base_tax' => 16737.92, 'rate' => 7.65],
                ],
            ],
            'WY' => [
                'state_deduction' => 0,
                'personal_credit' => 0,
                'calculation_mode' => 'flat_rate',
                'flat_rate' => 0,
                'brackets' => [],
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

            if (isset($defaults[$code]['personal_credit_brackets'])) {
                $clean[$code]['personal_credit_brackets'] = [];
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

            if (isset($defaults[$code]['poverty_credit'])) {
                $clean[$code]['poverty_credit'] = isset($state_input['poverty_credit']) ? floatval($state_input['poverty_credit']) : floatval($defaults[$code]['poverty_credit']);
            }

            if (!empty($defaults[$code]['full_refund_threshold'])) {
                $clean[$code]['full_refund_threshold'] = floatval($defaults[$code]['full_refund_threshold']);
            }

            if (isset($defaults[$code]['ar_tax_credits'])) {
                $clean[$code]['ar_tax_credits'] = isset($state_input['ar_tax_credits']) ? floatval($state_input['ar_tax_credits']) : floatval($defaults[$code]['ar_tax_credits']);
            }

            if (isset($defaults[$code]['colorado_deduction'])) {
                $clean[$code]['colorado_deduction'] = isset($state_input['colorado_deduction']) ? floatval($state_input['colorado_deduction']) : floatval($defaults[$code]['colorado_deduction']);
            }

            if (isset($defaults[$code]['resident_flat_rate'])) {
                $clean[$code]['resident_flat_rate'] = isset($state_input['resident_flat_rate']) ? floatval($state_input['resident_flat_rate']) : floatval($defaults[$code]['resident_flat_rate']);
            }

            if (isset($defaults[$code]['non_resident_flat_rate'])) {
                $clean[$code]['non_resident_flat_rate'] = isset($state_input['non_resident_flat_rate']) ? floatval($state_input['non_resident_flat_rate']) : floatval($defaults[$code]['non_resident_flat_rate']);
            }

            if (isset($defaults[$code]['rh_personal_deduction_res'])) {
                $clean[$code]['rh_personal_deduction_res'] = isset($state_input['rh_personal_deduction_res']) ? floatval($state_input['rh_personal_deduction_res']) : floatval($defaults[$code]['rh_personal_deduction_res']);
            }

            if (isset($defaults[$code]['rh_personal_deduction_nonres'])) {
                $clean[$code]['rh_personal_deduction_nonres'] = isset($state_input['rh_personal_deduction_nonres']) ? floatval($state_input['rh_personal_deduction_nonres']) : floatval($defaults[$code]['rh_personal_deduction_nonres']);
            }

            if (isset($defaults[$code]['wisconsin_deduction'])) {
                $clean[$code]['wisconsin_deduction'] = isset($state_input['wisconsin_deduction']) ? floatval($state_input['wisconsin_deduction']) : floatval($defaults[$code]['wisconsin_deduction']);
            }

            if (isset($defaults[$code]['non_resident_wisconsin_deduction'])) {
                $clean[$code]['non_resident_wisconsin_deduction'] = isset($state_input['non_resident_wisconsin_deduction']) ? floatval($state_input['non_resident_wisconsin_deduction']) : floatval($defaults[$code]['non_resident_wisconsin_deduction']);
            }

            // Force Wisconsin personal_credit to 0 to prevent double-deduction
            if ($code === 'WI') {
                $clean[$code]['personal_credit'] = 0;
            }

            if (isset($defaults[$code]['vermont_deduction'])) {
                $clean[$code]['vermont_deduction'] = isset($state_input['vermont_deduction']) ? floatval($state_input['vermont_deduction']) : floatval($defaults[$code]['vermont_deduction']);
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

            if (isset($defaults[$code]['personal_credit_brackets'])) {
                if (isset($state_input['personal_credit_brackets']) && is_array($state_input['personal_credit_brackets'])) {
                    foreach ($state_input['personal_credit_brackets'] as $row) {
                        if ($row === null) {
                            continue;
                        }
                        $min = isset($row['min_income']) ? floatval($row['min_income']) : 0;
                        $max = isset($row['max_income']) && $row['max_income'] !== '' ? floatval($row['max_income']) : '';
                        $credit = isset($row['credit']) ? floatval($row['credit']) : 0;
                        $clean[$code]['personal_credit_brackets'][] = [
                            'min_income' => $min,
                            'max_income' => $max,
                            'credit' => $credit,
                        ];
                    }
                } else {
                    $clean[$code]['personal_credit_brackets'] = $defaults[$code]['personal_credit_brackets'];
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
                } elseif ($code === 'CO') {
                    $colorado_deduction = isset($state_settings['colorado_deduction']) ? $state_settings['colorado_deduction'] : '';
                    $resident_flat_rate = isset($state_settings['resident_flat_rate']) ? $state_settings['resident_flat_rate'] : '';
                    $non_resident_flat_rate = isset($state_settings['non_resident_flat_rate']) ? $state_settings['non_resident_flat_rate'] : '';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('State deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][state_deduction]" value="' . esc_attr($state_settings['state_deduction']) . '" /></div>';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Personal credit (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][personal_credit]" value="' . esc_attr($state_settings['personal_credit']) . '" /></div>';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Colorado deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][colorado_deduction]" value="' . esc_attr($colorado_deduction) . '" /></div>';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Resident flat rate (%)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][resident_flat_rate]" value="' . esc_attr($resident_flat_rate) . '" /><small>' . esc_html__('Enter percentage (example: 4.40 for 4.40%).', 'ustc2025') . '</small></div>';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Non-resident flat rate (%)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][non_resident_flat_rate]" value="' . esc_attr($non_resident_flat_rate) . '" /><small>' . esc_html__('Enter percentage (example: 4.25 for 4.25%).', 'ustc2025') . '</small></div>';
                } elseif ($code === 'RI') {
                    $rh_personal_deduction_res = isset($state_settings['rh_personal_deduction_res']) ? $state_settings['rh_personal_deduction_res'] : '';
                    $rh_personal_deduction_nonres = isset($state_settings['rh_personal_deduction_nonres']) ? $state_settings['rh_personal_deduction_nonres'] : '';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Rhode Island resident personal deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][rh_personal_deduction_res]" value="' . esc_attr($rh_personal_deduction_res) . '" /></div>';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Rhode Island non-resident personal deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][rh_personal_deduction_nonres]" value="' . esc_attr($rh_personal_deduction_nonres) . '" /></div>';
                    echo '<input type="hidden" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][state_deduction]" value="' . esc_attr($state_settings['state_deduction']) . '" />';
                    echo '<input type="hidden" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][personal_credit]" value="' . esc_attr($state_settings['personal_credit']) . '" />';
                    echo '<input type="hidden" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][deduction_resident]" value="' . esc_attr($rh_personal_deduction_res) . '" />';
                    echo '<input type="hidden" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][deduction_nonresident]" value="' . esc_attr($rh_personal_deduction_nonres) . '" />';
                } elseif ($code === 'WI') {
                    $wisconsin_deduction = isset($state_settings['wisconsin_deduction']) ? $state_settings['wisconsin_deduction'] : '';
                    $non_resident_wisconsin_deduction = isset($state_settings['non_resident_wisconsin_deduction']) ? $state_settings['non_resident_wisconsin_deduction'] : '';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Wisconsin resident deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][wisconsin_deduction]" value="' . esc_attr($wisconsin_deduction) . '" /></div>';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Wisconsin non-resident deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][non_resident_wisconsin_deduction]" value="' . esc_attr($non_resident_wisconsin_deduction) . '" /></div>';
                    echo '<input type="hidden" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][state_deduction]" value="' . esc_attr($state_settings['state_deduction']) . '" />';
                    echo '<input type="hidden" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][personal_credit]" value="' . esc_attr($state_settings['personal_credit']) . '" />';
                    echo '<input type="hidden" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][deduction_resident]" value="' . esc_attr($wisconsin_deduction) . '" />';
                    echo '<input type="hidden" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][deduction_nonresident]" value="' . esc_attr($non_resident_wisconsin_deduction) . '" />';
                } elseif ($code === 'NY') {
                    $personal_credit_brackets = isset($state_settings['personal_credit_brackets']) ? $state_settings['personal_credit_brackets'] : [];
                    echo '<div class="ustc2025-col"><label>' . esc_html__('New York deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][state_deduction]" value="' . esc_attr($state_settings['state_deduction']) . '" /></div>';
                    echo '<input type="hidden" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][personal_credit]" value="' . esc_attr($state_settings['personal_credit']) . '" />';
                } elseif ($code === 'SC') {
                    $sc_resident_deduction = isset($state_settings['sc_resident_deduction']) ? $state_settings['sc_resident_deduction'] : '';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('South Carolina resident deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][sc_resident_deduction]" value="' . esc_attr($sc_resident_deduction) . '" /></div>';
                    echo '<input type="hidden" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][state_deduction]" value="' . esc_attr($state_settings['state_deduction']) . '" />';
                    echo '<input type="hidden" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][personal_credit]" value="' . esc_attr($state_settings['personal_credit']) . '" />';
                } elseif ($code === 'VT') {
                    $vermont_deduction = isset($state_settings['vermont_deduction']) ? $state_settings['vermont_deduction'] : '';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Vermont deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][vermont_deduction]" value="' . esc_attr($vermont_deduction) . '" /></div>';
                    echo '<input type="hidden" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][state_deduction]" value="' . esc_attr($state_settings['state_deduction']) . '" />';
                    echo '<input type="hidden" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][personal_credit]" value="' . esc_attr($state_settings['personal_credit']) . '" />';
                } else {
                    echo '<div class="ustc2025-col"><label>' . esc_html__('State deduction (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][state_deduction]" value="' . esc_attr($state_settings['state_deduction']) . '" /></div>';
                    echo '<div class="ustc2025-col"><label>' . esc_html__('Personal credit (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][personal_credit]" value="' . esc_attr($state_settings['personal_credit']) . '" /></div>';
                    if ($code === 'MD') {
                        echo '<div class="ustc2025-col"><label>' . esc_html__('Poverty credit threshold (USD)', 'ustc2025') . '</label><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($code) . '][poverty_credit]" value="' . esc_attr($state_settings['poverty_credit']) . '" /></div>';
                    }
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
                if ($code === 'NY') {
                    $personal_credit_brackets = !empty($personal_credit_brackets) ? $personal_credit_brackets : (isset($state_settings['personal_credit_brackets']) ? $state_settings['personal_credit_brackets'] : []);
                    echo '<div class="ustc2025-brackets ustc2025-personal-credit-brackets" data-state="' . esc_attr($code) . '">';
                    echo '<h4>' . esc_html__('Personal credit brackets (based on total income)', 'ustc2025') . '</h4>';
                    echo '<table class="widefat fixed" cellspacing="0">';
                    echo '<thead><tr><th>' . esc_html__('Min income', 'ustc2025') . '</th><th>' . esc_html__('Max income', 'ustc2025') . '</th><th>' . esc_html__('Personal credit (USD)', 'ustc2025') . '</th><th>' . esc_html__('Actions', 'ustc2025') . '</th></tr></thead>';
                    echo '<tbody>';
                    $idx = 0;
                    foreach ($personal_credit_brackets as $row) {
                        echo $this->render_personal_credit_row($code, $idx, $row);
                        $idx++;
                    }
                    echo '</tbody>';
                    echo '</table>';
                    echo '<p><button type="button" class="button ustc-add-personal-credit" data-state="' . esc_attr($code) . '">' . esc_html__('Add personal credit bracket', 'ustc2025') . '</button></p>';
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
                        if (e.target.classList.contains('ustc-add-personal-credit')) {
                            const state = e.target.dataset.state;
                            const table = document.querySelector('.ustc2025-personal-credit-brackets[data-state="' + state + '"] tbody');
                            if (!table) return;
                            const idx = table.querySelectorAll('tr').length;
                            const row = document.createElement('tr');
                            row.innerHTML = '<td><input type="number" step="0.01" name="<?php echo esc_js($this->option_state); ?>[' + state + '][personal_credit_brackets][' + idx + '][min_income]" /></td>' +
                                '<td><input type="number" step="0.01" name="<?php echo esc_js($this->option_state); ?>[' + state + '][personal_credit_brackets][' + idx + '][max_income]" /></td>' +
                                '<td><input type="number" step="0.01" name="<?php echo esc_js($this->option_state); ?>[' + state + '][personal_credit_brackets][' + idx + '][credit]" /></td>' +
                                '<td><button type="button" class="button link-delete ustc-remove-personal-credit"><?php echo esc_js(__('Remove', 'ustc2025')); ?></button></td>';
                            table.appendChild(row);
                        }
                        if (e.target.classList.contains('ustc-remove-personal-credit')) {
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

    private function render_personal_credit_row($state_code, $index, $row)
    {
        $min = isset($row['min_income']) ? $row['min_income'] : '';
        $max = isset($row['max_income']) ? $row['max_income'] : '';
        $credit = isset($row['credit']) ? $row['credit'] : '';
        $html = '<tr>';
        $html .= '<td><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($state_code) . '][personal_credit_brackets][' . esc_attr($index) . '][min_income]" value="' . esc_attr($min) . '" /></td>';
        $html .= '<td><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($state_code) . '][personal_credit_brackets][' . esc_attr($index) . '][max_income]" value="' . esc_attr($max) . '" /></td>';
        $html .= '<td><input type="number" step="0.01" name="' . esc_attr($this->option_state) . '[' . esc_attr($state_code) . '][personal_credit_brackets][' . esc_attr($index) . '][credit]" value="' . esc_attr($credit) . '" /></td>';
        $html .= '<td><button type="button" class="button link-delete ustc-remove-personal-credit">' . esc_html__('Remove', 'ustc2025') . '</button></td>';
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
            $federal = $this->calculate_federal($gross, $fwh, $swh, $residency, $federal_settings);
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
            $federal_resident = $this->calculate_federal($gross, $fwh, $swh, 'resident', $federal_settings);
            $federal_nonresident = $this->calculate_federal($gross, $fwh, $swh, 'nonresident', $federal_settings);
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

    private function calculate_federal($gross, $federal_withholding, $state_withholding, $residency, $settings)
    {
        $breakdown = [];
        $std_deduction = floatval($settings['std_deduction']);
        if ($residency === 'resident') {
            $agi = $gross - $std_deduction;
            $breakdown[] = sprintf(__('AGI = GrossIncome (%s) - StdDeduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($std_deduction, 2), number_format($agi, 2));
        } else {
            $agi = $gross - $state_withholding;
            $breakdown[] = sprintf(__('TaxableIncome = TotalIncome (%s) - StateWithholding (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($state_withholding, 2), number_format($agi, 2));
        }
        if ($agi < 0) {
            $agi = 0;
        }
        if ($residency === 'resident') {
            $brackets = [
                ['limit' => 11925, 'rate' => 0.10],
                ['limit' => 48475, 'rate' => 0.12],
                ['limit' => 103350, 'rate' => 0.22],
                ['limit' => 197300, 'rate' => 0.24],
                ['limit' => 250525, 'rate' => 0.32],
                ['limit' => 626350, 'rate' => 0.35],
                ['limit' => null, 'rate' => 0.37],
            ];
        } else {
            $brackets = [
                ['limit' => 11925, 'rate' => 0.10],
                ['limit' => 48475, 'rate' => 0.12],
                ['limit' => 103350, 'rate' => 0.22],
                ['limit' => 197300, 'rate' => 0.24],
            ];
        }
        $tax_data = $this->progressive_tax($agi, $brackets);
        $breakdown = array_merge($breakdown, $tax_data['breakdown']);
        $tax = $tax_data['tax'];
        $tax_diff = $tax - $federal_withholding;
        $breakdown[] = sprintf(__('Tax - FederalWithholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($federal_withholding, 2), number_format($tax_diff, 2));
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
        if ($code === 'CA') {
            return $this->california_tax($gross, $withholding, $settings, $breakdown);
        }
        if ($code === 'CT') {
            return $this->connecticut_tax($gross, $withholding, $settings, $residency);
        }
        if ($code === 'CO') {
            return $this->colorado_tax($gross, $withholding, $residency, $settings);
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
        if (in_array($code, ['AK', 'FL', 'NV', 'NH', 'SD', 'TN', 'TX', 'WA', 'WY'], true)) {
            $breakdown[] = __('No state income tax; full refund of state withholding applied.', 'ustc2025');
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
        if ($code === 'RI') {
            return $this->rhode_island_tax($gross, $withholding, $residency, $settings, $breakdown);
        }
        if ($code === 'WI') {
            return $this->wisconsin_tax($gross, $withholding, $residency, $settings, $breakdown);
        }
        if ($code === 'NY') {
            return $this->new_york_tax($gross, $withholding, $settings, $breakdown);
        }
        if ($code === 'MD') {
            return $this->maryland_tax_calculation($gross, $withholding, $residency, $settings);
        }
        if ($code === 'DE') {
            return $this->delaware_tax($gross, $withholding, $settings, $breakdown);
        }
        if ($code === 'UT') {
            return $this->utah_tax($gross, $withholding, $residency, $settings, $federal_result);
        }
        if ($code === 'SC') {
            return $this->south_carolina_tax($gross, $withholding, $residency, $settings);
        }
        if ($code === 'VT') {
            return $this->vermont_tax($gross, $withholding, $residency, $settings);
        }

        $personal_deduction = 0;
        $personal_credit = isset($settings['personal_credit']) ? floatval($settings['personal_credit']) : 0;
        $resident_deduction = isset($settings['deduction_resident']) ? floatval($settings['deduction_resident']) : 0;
        $nonresident_deduction = isset($settings['deduction_nonresident']) ? floatval($settings['deduction_nonresident']) : 0;
        $state_deduction = isset($settings['state_deduction']) ? floatval($settings['state_deduction']) : 0;

        if ($residency === 'resident' && $resident_deduction > 0) {
            $personal_deduction = $resident_deduction;
        } elseif ($residency === 'nonresident' && $nonresident_deduction > 0) {
            $personal_deduction = $nonresident_deduction;
        } elseif ($state_deduction > 0) {
            $personal_deduction = $state_deduction;
        }

        $taxable = max(0, $gross - $personal_deduction);
        if ($personal_deduction > 0) {
            $breakdown[] = sprintf(__('Taxable income = Total income (%s) - Deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($personal_deduction, 2), number_format($taxable, 2));
        } else {
            $breakdown[] = sprintf(__('Taxable income = Total income (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($taxable, 2));
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
        if ($personal_credit > 0) {
            $breakdown[] = sprintf(__('Tax - withholding - credit = %s - %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($personal_credit, 2), number_format($tax_diff, 2));
        } else {
            $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));
        }
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

    private function colorado_tax($gross, $withholding, $residency, $settings)
    {
        $breakdown = [];
        $colorado_deduction = isset($settings['colorado_deduction']) ? floatval($settings['colorado_deduction']) : 15600;
        $resident_flat_rate = isset($settings['resident_flat_rate']) ? floatval($settings['resident_flat_rate']) : 4.4;
        $non_resident_flat_rate = isset($settings['non_resident_flat_rate']) ? floatval($settings['non_resident_flat_rate']) : 4.25;

        if ($residency === 'resident') {
            $taxable = $gross - $colorado_deduction;
            $breakdown[] = sprintf(__('Taxable income = Total income (%s) - Colorado deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($colorado_deduction, 2), number_format($taxable, 2));

            if ($taxable < 0) {
                $taxable = 0;
            }

            $tax = $taxable * ($resident_flat_rate / 100);
            $breakdown[] = sprintf(__('Colorado resident state tax = %s * %s%% = %s', 'ustc2025'), number_format($taxable, 2), $resident_flat_rate, number_format($tax, 2));
        } else {
            $tax = $gross * ($non_resident_flat_rate / 100);
            $breakdown[] = sprintf(__('Colorado non-resident state tax = Total income (%s) * %s%% = %s', 'ustc2025'), number_format($gross, 2), $non_resident_flat_rate, number_format($tax, 2));
        }

        $tax_diff = $tax - $withholding;
        $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));

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
        $federal_withholding = isset($federal_result['tax']) ? floatval($federal_result['tax']) : 0;
        $or_resident_deduction = isset($settings['or_resident_deduction']) ? floatval($settings['or_resident_deduction']) : 2835;
        $personal_exemption = isset($settings['or_personal_exemption']) ? floatval($settings['or_personal_exemption']) : 256;

        if ($residency === 'resident') {
            $taxable = $gross - $or_resident_deduction - $federal_withholding;
            $breakdown[] = sprintf(__('Oregon resident: Taxable income = Total income (%s) - OR resident deduction (%s) - Federal withholding (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($or_resident_deduction, 2), number_format($federal_withholding, 2), number_format($taxable, 2));
        } else {
            $taxable = $gross - $federal_withholding;
            $breakdown[] = sprintf(__('Oregon non-resident: Taxable income = Total income (%s) - Federal withholding (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($federal_withholding, 2), number_format($taxable, 2));
        }

        if ($taxable < 0) {
            $taxable = 0;
        }

        $brackets = isset($settings['brackets']) ? $settings['brackets'] : [];
        if (empty($brackets)) {
            $state_tax = 0;
            $breakdown[] = __('No brackets configured; state tax set to 0.', 'ustc2025');
        } else {
            usort($brackets, function ($a, $b) {
                return floatval($a['min_income']) <=> floatval($b['min_income']);
            });

            $state_tax = 0;
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
                $state_tax += $segment_tax;

                $range_label = $max === null ? sprintf(__('above %s', 'ustc2025'), number_format($min, 2)) : sprintf(__('between %s and %s', 'ustc2025'), number_format($min, 2), number_format($max, 2));
                $breakdown[] = sprintf(__('Bracket %s: (%s - %s) * %s%% = %s', 'ustc2025'), $range_label, number_format($upper, 2), number_format($min, 2), $rate, number_format($segment_tax, 2));

                if ($max !== null && $taxable <= $max) {
                    break;
                }
            }
        }

        $breakdown[] = sprintf(__('State tax from brackets = %s', 'ustc2025'), number_format($state_tax, 2));

        $tax_after_exemption = $state_tax - $personal_exemption;
        $breakdown[] = sprintf(__('State tax - Personal exemption = %s - %s = %s', 'ustc2025'), number_format($state_tax, 2), number_format($personal_exemption, 2), number_format($tax_after_exemption, 2));

        if ($tax_after_exemption < 0) {
            $tax_diff = -$withholding;
            $breakdown[] = sprintf(__('Tax after exemption is negative, State Tax Refund = %s', 'ustc2025'), number_format($withholding, 2));
        } else {
            $final_state_tax = $tax_after_exemption - $withholding;
            $breakdown[] = sprintf(__('Final state tax = Tax after exemption (%s) - State withholding (%s) = %s', 'ustc2025'), number_format($tax_after_exemption, 2), number_format($withholding, 2), number_format($final_state_tax, 2));

            if ($final_state_tax > 0) {
                $breakdown[] = sprintf(__('State Tax Owed = %s', 'ustc2025'), number_format($final_state_tax, 2));
            } else {
                $breakdown[] = sprintf(__('State Tax Refund = %s', 'ustc2025'), number_format(abs($final_state_tax), 2));
            }
            $tax_diff = $final_state_tax;
        }

        return ['tax' => $state_tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function utah_tax($gross, $withholding, $residency, $settings, $federal_result)
    {
        $breakdown = [];
        $utah_deduction = isset($settings['utah_deduction']) ? floatval($settings['utah_deduction']) : 18213;
        $flat_rate = isset($settings['utah_flat_rate']) ? floatval($settings['utah_flat_rate']) : 4.5;
        $personal_exemption = isset($settings['personal_credit']) ? floatval($settings['personal_credit']) : 0;

        // Get federal standard deduction for residents
        $federal_settings = get_option($this->option_federal, []);
        $federal_standard_deduction = isset($federal_settings['std_deduction']) ? floatval($federal_settings['std_deduction']) : 15750;

        // Calculate ut_tax (same for both)
        $ut_tax = $gross * ($flat_rate / 100);
        $breakdown[] = sprintf(__('Utah tax = Total income (%s) × %s%% = %s', 'ustc2025'), number_format($gross, 2), $flat_rate, number_format($ut_tax, 2));

        // Calculate initial_credit_before_phase_out based on residency
        if ($residency === 'resident') {
            $initial_credit_before_phase_out = $federal_standard_deduction * 0.06;
            $breakdown[] = sprintf(__('Utah resident: Initial credit before phase out = Federal standard deduction (%s) × 6%% = %s', 'ustc2025'), number_format($federal_standard_deduction, 2), number_format($initial_credit_before_phase_out, 2));
        } else {
            $initial_credit_before_phase_out = $withholding * 0.06;
            $breakdown[] = sprintf(__('Utah non-resident: Initial credit before phase out = State withholding (%s) × 6%% = %s', 'ustc2025'), number_format($withholding, 2), number_format($initial_credit_before_phase_out, 2));
        }

        // Calculate phaseout_amount
        $phaseout_amount = ($gross - $utah_deduction) * 0.013;
        if ($phaseout_amount < 0) {
            $phaseout_amount = 0;
        }
        $breakdown[] = sprintf(__('Phaseout amount = (Total income (%s) - Utah deduction (%s)) × 1.3%% = %s', 'ustc2025'), number_format($gross, 2), number_format($utah_deduction, 2), number_format($phaseout_amount, 2));

        // Calculate tax_payer_credit
        $credit_diff = $initial_credit_before_phase_out - $phaseout_amount;
        if ($credit_diff < 0) {
            $tax_payer_credit = 0;
            $breakdown[] = sprintf(__('Taxpayer credit = Initial credit (%s) - Phaseout (%s) = %s (set to 0 as negative)', 'ustc2025'), number_format($initial_credit_before_phase_out, 2), number_format($phaseout_amount, 2), number_format($credit_diff, 2));
        } else {
            $tax_payer_credit = $credit_diff;
            $breakdown[] = sprintf(__('Taxpayer credit = Initial credit (%s) - Phaseout (%s) = %s', 'ustc2025'), number_format($initial_credit_before_phase_out, 2), number_format($phaseout_amount, 2), number_format($tax_payer_credit, 2));
        }

        // Calculate state_tax
        $state_tax = $ut_tax - $tax_payer_credit;
        $breakdown[] = sprintf(__('State tax = Utah tax (%s) - Taxpayer credit (%s) = %s', 'ustc2025'), number_format($ut_tax, 2), number_format($tax_payer_credit, 2), number_format($state_tax, 2));

        if ($state_tax > 0) {
            // final_ut_tax = state_tax - state_withholding
            $final_ut_tax = $state_tax - $withholding;
            $breakdown[] = sprintf(__('Final Utah tax = State tax (%s) - State withholding (%s) = %s', 'ustc2025'), number_format($state_tax, 2), number_format($withholding, 2), number_format($final_ut_tax, 2));

            if ($final_ut_tax > 0) {
                $breakdown[] = sprintf(__('State Tax Owed = %s', 'ustc2025'), number_format($final_ut_tax, 2));
            } else {
                $breakdown[] = sprintf(__('State Tax Refund = %s', 'ustc2025'), number_format(abs($final_ut_tax), 2));
            }
            $tax_diff = $final_ut_tax;
        } else {
            // state_tax <= 0: Full refund of withholding
            $check_value = $state_tax - $personal_exemption;
            if ($check_value < 0) {
                $breakdown[] = sprintf(__('State tax (%s) is negative; State Tax Refund = %s', 'ustc2025'), number_format($state_tax, 2), number_format($withholding, 2));
                $tax_diff = -$withholding;
            } else {
                $tax_diff = $state_tax - $withholding;
                $breakdown[] = sprintf(__('Final Utah tax = State tax (%s) - State withholding (%s) = %s', 'ustc2025'), number_format($state_tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));
            }
        }

        return ['tax' => $ut_tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
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

    private function maryland_tax_calculation($gross, $withholding, $residency, $settings)
    {
        $breakdown = [];
        $poverty_credit = isset($settings['poverty_credit']) ? floatval($settings['poverty_credit']) : 15650;

        // Check if total income qualifies for poverty credit (full refund)
        if ($gross < $poverty_credit) {
            $breakdown[] = sprintf(__('Total income (%s) is below poverty credit threshold (%s). Full refund of state withholding.', 'ustc2025'), number_format($gross, 2), number_format($poverty_credit, 2));
            $tax_diff = -$withholding;
            $breakdown[] = sprintf(__('Tax difference = 0 - State withholding (%s) = %s', 'ustc2025'), number_format($withholding, 2), number_format($tax_diff, 2));
            return ['tax' => 0, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
        }

        $state_deduction = isset($settings['state_deduction']) ? floatval($settings['state_deduction']) : 6550;

        $taxable = $gross - $state_deduction;
        if ($taxable < 0) {
            $taxable = 0;
        }

        $breakdown[] = sprintf(__('Taxable income = Total income (%s) - Maryland deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($state_deduction, 2), number_format($taxable, 2));

        $state_tax_md = $this->maryland_tax($taxable, $breakdown);

        if ($residency === 'nonresident') {
            $local_tax = 0.0225 * $taxable;
            $breakdown[] = sprintf(__('Local tax (2.25%% of taxable income): %s', 'ustc2025'), number_format($local_tax, 2));
            $total_tax = $state_tax_md + $local_tax;
            $breakdown[] = sprintf(__('Total state tax (nonresident) = State tax (%s) + Local tax (%s) = %s', 'ustc2025'), number_format($state_tax_md, 2), number_format($local_tax, 2), number_format($total_tax, 2));
            $tax_diff = $total_tax - $withholding;
            $breakdown[] = sprintf(__('Tax difference = Total state tax (%s) - State withholding (%s) = %s', 'ustc2025'), number_format($total_tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));
            return ['tax' => $total_tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
        } else {
            $local_tax = 0.0225 * $taxable;
            $breakdown[] = sprintf(__('Local tax (2.25%% of taxable income): %s', 'ustc2025'), number_format($local_tax, 2));
            $total_tax = $state_tax_md + $local_tax;
            $breakdown[] = sprintf(__('Total state tax (resident) = State tax (%s) + Local tax (%s) = %s', 'ustc2025'), number_format($state_tax_md, 2), number_format($local_tax, 2), number_format($total_tax, 2));
            $tax_diff = $total_tax - $withholding;
            $breakdown[] = sprintf(__('Tax difference = Total state tax (%s) - State withholding (%s) = %s', 'ustc2025'), number_format($total_tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));
            return ['tax' => $total_tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
        }
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
        } else {
            $tax = 4697.75 + 0.05 * ($taxable - 100000);
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

    private function new_york_tax($gross, $withholding, $settings, &$breakdown)
    {
        // New York state deduction
        $ny_deduction = isset($settings['state_deduction']) ? floatval($settings['state_deduction']) : 0;

        // Calculate taxable income
        $taxable = max(0, $gross - $ny_deduction);

        if ($ny_deduction > 0) {
            $breakdown[] = sprintf(__('Taxable income = Total income (%s) - NY Deduction (%s) = %s', 'ustc2025'),
                number_format($gross, 2), number_format($ny_deduction, 2), number_format($taxable, 2));
        } else {
            $breakdown[] = sprintf(__('Taxable income = Total income (%s) = %s', 'ustc2025'),
                number_format($gross, 2), number_format($taxable, 2));
        }

        // Progressive tax brackets for New York
        // $0 to $8,500 -> 4%
        // $8,501 to $11,700 -> 4.5%
        // $11,701 to $13,900 -> 5.25%
        // $13,901 to $80,650 -> 5.50%
        // $80,651 to $215,400 -> 6.00%
        // Above $215,400 -> 6.00%

        $tax = 0;

        if ($taxable <= 8500) {
            $tax = 0.04 * $taxable;
            $breakdown[] = sprintf(__('Tax = %s * 4%% = %s', 'ustc2025'),
                number_format($taxable, 2), number_format($tax, 2));
        } elseif ($taxable <= 11700) {
            $tax = 340 + 0.045 * ($taxable - 8500);
            $breakdown[] = sprintf(__('Tax = $340 + (%s - $8,500) * 4.5%% = %s', 'ustc2025'),
                number_format($taxable, 2), number_format($tax, 2));
        } elseif ($taxable <= 13900) {
            $tax = 484 + 0.0525 * ($taxable - 11700);
            $breakdown[] = sprintf(__('Tax = $484 + (%s - $11,700) * 5.25%% = %s', 'ustc2025'),
                number_format($taxable, 2), number_format($tax, 2));
        } elseif ($taxable <= 80650) {
            $tax = 600 + 0.055 * ($taxable - 13900);
            $breakdown[] = sprintf(__('Tax = $600 + (%s - $13,900) * 5.5%% = %s', 'ustc2025'),
                number_format($taxable, 2), number_format($tax, 2));
        } elseif ($taxable <= 215400) {
            $tax = 4271 + 0.06 * ($taxable - 80650);
            $breakdown[] = sprintf(__('Tax = $4,271 + (%s - $80,650) * 6%% = %s', 'ustc2025'),
                number_format($taxable, 2), number_format($tax, 2));
        } else {
            // Above $215,400 continues at 6%
            $tax = 12356 + 0.06 * ($taxable - 215400);
            $breakdown[] = sprintf(__('Tax = $12,356 + (%s - $215,400) * 6%% = %s', 'ustc2025'),
                number_format($taxable, 2), number_format($tax, 2));
        }

        // Calculate personal credit based on total income
        $personal_credit = 0;
        $personal_credit_brackets = isset($settings['personal_credit_brackets']) ? $settings['personal_credit_brackets'] : [];

        if (!empty($personal_credit_brackets)) {
            foreach ($personal_credit_brackets as $bracket) {
                $min = floatval($bracket['min_income']);
                $max = $bracket['max_income'] === '' ? null : floatval($bracket['max_income']);
                $credit = floatval($bracket['credit']);

                if ($gross >= $min && ($max === null || $gross <= $max)) {
                    $personal_credit = $credit;
                    $breakdown[] = sprintf(__('Personal credit based on total income (%s): $%s', 'ustc2025'),
                        number_format($gross, 2), number_format($personal_credit, 2));
                    break;
                }
            }
        }

        // Calculate tax after personal credit
        $tax_after_credit = $tax - $personal_credit;

        if ($personal_credit > 0) {
            $breakdown[] = sprintf(__('Tax after personal credit = Tax (%s) - Personal credit (%s) = %s', 'ustc2025'),
                number_format($tax, 2), number_format($personal_credit, 2), number_format($tax_after_credit, 2));
        }

        // Apply refund logic
        if ($tax_after_credit <= 0) {
            // Full refund of state withholding
            $breakdown[] = sprintf(__('Tax after credit is zero or negative; full refund of state withholding: %s', 'ustc2025'),
                number_format($withholding, 2));
            return ['tax' => 0, 'tax_diff' => -$withholding, 'breakdown' => $breakdown];
        }

        // Calculate final tax owed or refund
        $tax_diff = $tax_after_credit - $withholding;

        $breakdown[] = sprintf(__('Tax difference = Tax after credit (%s) - Withholding (%s) = %s', 'ustc2025'),
            number_format($tax_after_credit, 2), number_format($withholding, 2), number_format($tax_diff, 2));

        return ['tax' => $tax_after_credit, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function delaware_tax($gross, $withholding, $settings, &$breakdown)
    {
        // Check for full refund threshold based on total income
        if ($gross <= 9400) {
            $breakdown[] = sprintf(__('Total income (%s) at or below Delaware full refund threshold ($9,400); full refund of state withholding.', 'ustc2025'),
                number_format($gross, 2));
            return ['tax' => 0, 'tax_diff' => -$withholding, 'breakdown' => $breakdown];
        }

        // Delaware deduction and personal tax credit
        $delaware_deduction = 3250;
        $personal_tax_credit = 110;

        // Calculate taxable income
        $taxable = max(0, $gross - $delaware_deduction);

        $breakdown[] = sprintf(__('Taxable income = Total income (%s) - Delaware Deduction (%s) = %s', 'ustc2025'),
            number_format($gross, 2), number_format($delaware_deduction, 2), number_format($taxable, 2));

        // Progressive tax brackets for Delaware
        // $0 to $2,000 -> 0.00%
        // $2,001 to $5,000 -> 2.20%
        // $5,001 to $10,000 -> 3.90%
        // $10,001 to $20,000 -> 4.80%
        // $20,001 to $25,000 -> 5.20%
        // $25,001 to $60,000 -> 5.55%
        // $60,001 or more -> 6.60%

        $state_tax = 0;

        if ($taxable <= 2000) {
            $state_tax = 0;
            $breakdown[] = sprintf(__('Tax = %s * 0%% = %s', 'ustc2025'),
                number_format($taxable, 2), number_format($state_tax, 2));
        } elseif ($taxable <= 5000) {
            $state_tax = 0.022 * ($taxable - 2000);
            $breakdown[] = sprintf(__('Tax = (%s - $2,000) * 2.2%% = %s', 'ustc2025'),
                number_format($taxable, 2), number_format($state_tax, 2));
        } elseif ($taxable <= 10000) {
            $state_tax = 66 + 0.039 * ($taxable - 5000);
            $breakdown[] = sprintf(__('Tax = $66 + (%s - $5,000) * 3.9%% = %s', 'ustc2025'),
                number_format($taxable, 2), number_format($state_tax, 2));
        } elseif ($taxable <= 20000) {
            $state_tax = 261 + 0.048 * ($taxable - 10000);
            $breakdown[] = sprintf(__('Tax = $261 + (%s - $10,000) * 4.8%% = %s', 'ustc2025'),
                number_format($taxable, 2), number_format($state_tax, 2));
        } elseif ($taxable <= 25000) {
            $state_tax = 741 + 0.052 * ($taxable - 20000);
            $breakdown[] = sprintf(__('Tax = $741 + (%s - $20,000) * 5.2%% = %s', 'ustc2025'),
                number_format($taxable, 2), number_format($state_tax, 2));
        } elseif ($taxable <= 60000) {
            $state_tax = 1001 + 0.0555 * ($taxable - 25000);
            $breakdown[] = sprintf(__('Tax = $1,001 + (%s - $25,000) * 5.55%% = %s', 'ustc2025'),
                number_format($taxable, 2), number_format($state_tax, 2));
        } else {
            $state_tax = 2943.50 + 0.066 * ($taxable - 60000);
            $breakdown[] = sprintf(__('Tax = $2,943.50 + (%s - $60,000) * 6.6%% = %s', 'ustc2025'),
                number_format($taxable, 2), number_format($state_tax, 2));
        }

        // Apply personal tax credit
        $tax_after_credit = $state_tax - $personal_tax_credit;

        $breakdown[] = sprintf(__('Tax after personal credit = Tax (%s) - Personal Tax Credit (%s) = %s', 'ustc2025'),
            number_format($state_tax, 2), number_format($personal_tax_credit, 2), number_format($tax_after_credit, 2));

        // If tax after credit is <= 0, full refund of withholding
        if ($tax_after_credit <= 0) {
            $breakdown[] = __('Tax after credit is at or below zero; full refund of state withholding.', 'ustc2025');
            return ['tax' => 0, 'tax_diff' => -$withholding, 'breakdown' => $breakdown];
        }

        // Calculate tax difference: tax_after_credit - withholding
        $tax_diff = $tax_after_credit - $withholding;

        $breakdown[] = sprintf(__('State tax difference = Tax after credit (%s) - Withholding (%s) = %s', 'ustc2025'),
            number_format($tax_after_credit, 2), number_format($withholding, 2), number_format($tax_diff, 2));

        return ['tax' => $tax_after_credit, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function california_tax($gross, $withholding, $settings, &$breakdown)
    {
        // Get California deduction and personal tax credit
        $california_deduction = isset($settings['state_deduction']) ? floatval($settings['state_deduction']) : 5706;
        $personal_tax_credit = isset($settings['personal_credit']) ? floatval($settings['personal_credit']) : 153;

        // Calculate taxable income
        $taxable_income = $gross - $california_deduction;
        if ($taxable_income < 0) {
            $taxable_income = 0;
        }
        $breakdown[] = sprintf(__('Taxable income = Total income (%s) - California deduction (%s) = %s', 'ustc2025'),
            number_format($gross, 2), number_format($california_deduction, 2), number_format($taxable_income, 2));

        // Apply tax brackets
        $brackets = isset($settings['brackets']) ? $settings['brackets'] : [];
        $state_tax = $this->apply_brackets($taxable_income, $brackets, $breakdown);

        // Calculate povracaj_za_korisnika_state = state_tax - personal_tax_credit
        $povracaj_za_korisnika_state = $state_tax - $personal_tax_credit;
        $breakdown[] = sprintf(__('Tax after personal credit = State tax (%s) - Personal tax credit (%s) = %s', 'ustc2025'),
            number_format($state_tax, 2), number_format($personal_tax_credit, 2), number_format($povracaj_za_korisnika_state, 2));

        // If povracaj_za_korisnika_state <= 0, full refund of state withholding
        if ($povracaj_za_korisnika_state <= 0) {
            $breakdown[] = __('Tax after credit is at or below zero; full refund of state withholding.', 'ustc2025');
            return ['tax' => 0, 'tax_diff' => -$withholding, 'breakdown' => $breakdown];
        }

        // Calculate total_tax_owned = povracaj_za_korisnika_state - state_withholding
        $total_tax_owned = $povracaj_za_korisnika_state - $withholding;
        $breakdown[] = sprintf(__('Total tax difference = Tax after credit (%s) - State withholding (%s) = %s', 'ustc2025'),
            number_format($povracaj_za_korisnika_state, 2), number_format($withholding, 2), number_format($total_tax_owned, 2));

        // Return the result
        return ['tax' => $povracaj_za_korisnika_state, 'tax_diff' => $total_tax_owned, 'breakdown' => $breakdown];
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

    private function south_carolina_tax($gross, $withholding, $residency, $settings)
    {
        $breakdown = [];
        $sc_resident_deduction = isset($settings['sc_resident_deduction']) ? floatval($settings['sc_resident_deduction']) : 15750;

        // Residents get the deduction, non-residents do not
        if ($residency === 'resident') {
            $taxable = max(0, $gross - $sc_resident_deduction);
            $breakdown[] = sprintf(__('South Carolina resident: Taxable income = Total income (%s) - SC resident deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($sc_resident_deduction, 2), number_format($taxable, 2));
        } else {
            $taxable = $gross;
            $breakdown[] = sprintf(__('South Carolina non-resident: Taxable income = Total income (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($taxable, 2));
        }

        // Apply South Carolina tax brackets
        // $0 to $3,560 -> 0%
        // $3,561 to $17,830 -> 3%
        // $17,831 or more -> 6.2%
        $b1 = 3560;
        $b2 = 17830;
        $r1 = 0.00;
        $r2 = 0.03;
        $r3 = 0.062;

        if ($taxable <= $b1) {
            $tax = $taxable * $r1;
            $breakdown[] = sprintf(__('Tax bracket: $0-$3,560 at 0%%: %s', 'ustc2025'), number_format($tax, 2));
        } elseif ($taxable <= $b2) {
            $tax = ($taxable - $b1) * $r2;
            $breakdown[] = sprintf(__('Tax bracket: $3,561-$17,830 at 3%%: %s', 'ustc2025'), number_format($tax, 2));
        } else {
            $tax_bracket2 = ($b2 - $b1) * $r2;
            $tax_bracket3 = ($taxable - $b2) * $r3;
            $tax = $tax_bracket2 + $tax_bracket3;
            $breakdown[] = sprintf(__('Tax bracket: $3,561-$17,830 at 3%%: %s', 'ustc2025'), number_format($tax_bracket2, 2));
            $breakdown[] = sprintf(__('Tax bracket: $17,831+ at 6.2%%: %s', 'ustc2025'), number_format($tax_bracket3, 2));
        }

        $breakdown[] = sprintf(__('South Carolina state tax computed: %s', 'ustc2025'), number_format($tax, 2));

        $tax_diff = $tax - $withholding;
        if ($tax_diff < 0) {
            $breakdown[] = sprintf(__('State Tax Return: %s - %s = %s', 'ustc2025'), number_format($withholding, 2), number_format($tax, 2), number_format(abs($tax_diff), 2));
        } else {
            $breakdown[] = sprintf(__('State Tax Owed: %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));
        }

        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function vermont_tax($gross, $withholding, $residency, $settings)
    {
        $breakdown = [];
        $vermont_deduction = isset($settings['vermont_deduction']) ? floatval($settings['vermont_deduction']) : 12950;

        // Residents get the deduction, non-residents do not
        if ($residency === 'resident') {
            $taxable = max(0, $gross - $vermont_deduction);
            $breakdown[] = sprintf(__('Vermont resident: Taxable income = Total income (%s) - Vermont deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($vermont_deduction, 2), number_format($taxable, 2));
        } else {
            $taxable = $gross;
            $breakdown[] = sprintf(__('Vermont non-resident: Taxable income = Total income (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($taxable, 2));
        }

        // Apply Vermont tax brackets
        // $0 to $3,825 -> 0.00%
        // $3,826 to $53,225 -> 3.35%
        // $53,226 to $123,525 -> 6.60%
        // $123,526 to $253,525 -> 7.60%
        // $253,526 or more -> 8.75%
        $b1 = 3825;
        $b2 = 53225;
        $b3 = 123525;
        $b4 = 253525;
        $r1 = 0.0000;
        $r2 = 0.0335;
        $r3 = 0.0660;
        $r4 = 0.0760;
        $r5 = 0.0875;

        $tax = 0;

        if ($taxable <= $b1) {
            $tax = $taxable * $r1;
            $breakdown[] = sprintf(__('Tax bracket: $0 to $3,825 at 0.00%%: %s', 'ustc2025'), number_format($tax, 2));
        } elseif ($taxable <= $b2) {
            $tax_bracket2 = ($taxable - $b1) * $r2;
            $tax = $tax_bracket2;
            $breakdown[] = sprintf(__('Tax bracket: $0 to $3,825 at 0.00%%: %s', 'ustc2025'), number_format(0, 2));
            $breakdown[] = sprintf(__('Tax bracket: $3,826 to $53,225 at 3.35%%: %s', 'ustc2025'), number_format($tax_bracket2, 2));
        } elseif ($taxable <= $b3) {
            $tax_bracket2 = ($b2 - $b1) * $r2;
            $tax_bracket3 = ($taxable - $b2) * $r3;
            $tax = $tax_bracket2 + $tax_bracket3;
            $breakdown[] = sprintf(__('Tax bracket: $0 to $3,825 at 0.00%%: %s', 'ustc2025'), number_format(0, 2));
            $breakdown[] = sprintf(__('Tax bracket: $3,826 to $53,225 at 3.35%%: %s', 'ustc2025'), number_format($tax_bracket2, 2));
            $breakdown[] = sprintf(__('Tax bracket: $53,226 to $123,525 at 6.60%%: %s', 'ustc2025'), number_format($tax_bracket3, 2));
        } elseif ($taxable <= $b4) {
            $tax_bracket2 = ($b2 - $b1) * $r2;
            $tax_bracket3 = ($b3 - $b2) * $r3;
            $tax_bracket4 = ($taxable - $b3) * $r4;
            $tax = $tax_bracket2 + $tax_bracket3 + $tax_bracket4;
            $breakdown[] = sprintf(__('Tax bracket: $0 to $3,825 at 0.00%%: %s', 'ustc2025'), number_format(0, 2));
            $breakdown[] = sprintf(__('Tax bracket: $3,826 to $53,225 at 3.35%%: %s', 'ustc2025'), number_format($tax_bracket2, 2));
            $breakdown[] = sprintf(__('Tax bracket: $53,226 to $123,525 at 6.60%%: %s', 'ustc2025'), number_format($tax_bracket3, 2));
            $breakdown[] = sprintf(__('Tax bracket: $123,526 to $253,525 at 7.60%%: %s', 'ustc2025'), number_format($tax_bracket4, 2));
        } else {
            $tax_bracket2 = ($b2 - $b1) * $r2;
            $tax_bracket3 = ($b3 - $b2) * $r3;
            $tax_bracket4 = ($b4 - $b3) * $r4;
            $tax_bracket5 = ($taxable - $b4) * $r5;
            $tax = $tax_bracket2 + $tax_bracket3 + $tax_bracket4 + $tax_bracket5;
            $breakdown[] = sprintf(__('Tax bracket: $0 to $3,825 at 0.00%%: %s', 'ustc2025'), number_format(0, 2));
            $breakdown[] = sprintf(__('Tax bracket: $3,826 to $53,225 at 3.35%%: %s', 'ustc2025'), number_format($tax_bracket2, 2));
            $breakdown[] = sprintf(__('Tax bracket: $53,226 to $123,525 at 6.60%%: %s', 'ustc2025'), number_format($tax_bracket3, 2));
            $breakdown[] = sprintf(__('Tax bracket: $123,526 to $253,525 at 7.60%%: %s', 'ustc2025'), number_format($tax_bracket4, 2));
            $breakdown[] = sprintf(__('Tax bracket: $253,526 or more at 8.75%%: %s', 'ustc2025'), number_format($tax_bracket5, 2));
        }

        $breakdown[] = sprintf(__('Vermont state tax computed: %s', 'ustc2025'), number_format($tax, 2));

        $tax_diff = $withholding - $tax;
        if ($tax_diff > 0) {
            $breakdown[] = sprintf(__('State Tax Return: %s - %s = %s', 'ustc2025'), number_format($withholding, 2), number_format($tax, 2), number_format($tax_diff, 2));
        } else {
            $breakdown[] = sprintf(__('State Tax Owed: %s - %s = %s', 'ustc2025'), number_format($withholding, 2), number_format($tax, 2), number_format(abs($tax_diff), 2));
        }

        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function wisconsin_tax($gross, $withholding, $residency, $settings, &$breakdown)
    {
        $wisconsin_deduction = isset($settings['wisconsin_deduction']) ? floatval($settings['wisconsin_deduction']) : 14260;
        $non_resident_wisconsin_deduction = isset($settings['non_resident_wisconsin_deduction']) ? floatval($settings['non_resident_wisconsin_deduction']) : 700;

        if ($residency === 'resident') {
            $personal_deduction = $wisconsin_deduction;
            $breakdown[] = sprintf(__('Wisconsin resident: Taxable income = Total income (%s) - Wisconsin deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($personal_deduction, 2), number_format($gross - $personal_deduction, 2));
        } else {
            $personal_deduction = $non_resident_wisconsin_deduction;
            $breakdown[] = sprintf(__('Wisconsin non-resident: Taxable income = Total income (%s) - Non-resident deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($personal_deduction, 2), number_format($gross - $personal_deduction, 2));
        }

        $taxable = max(0, $gross - $personal_deduction);

        // Apply Wisconsin tax brackets
        $b1 = 14680;
        $b2 = 29370;
        $b3 = 323290;
        $r1 = 0.035;
        $r2 = 0.044;
        $r3 = 0.053;
        $r4 = 0.0765;

        if ($taxable <= $b1) {
            $tax = $taxable * $r1;
            $breakdown[] = sprintf(__('Tax bracket: $0 to $14,680 at 3.50%% = %s * 3.50%% = %s', 'ustc2025'), number_format($taxable, 2), number_format($tax, 2));
        } elseif ($taxable <= $b2) {
            $tax = ($b1 * $r1) + (($taxable - $b1) * $r2);
            $breakdown[] = sprintf(__('Tax bracket: $0 to $14,680 at 3.50%% = %s', 'ustc2025'), number_format($b1 * $r1, 2));
            $breakdown[] = sprintf(__('Tax bracket: $14,680 to $29,370 at 4.40%% = (%s - %s) * 4.40%% = %s', 'ustc2025'), number_format($taxable, 2), number_format($b1, 2), number_format(($taxable - $b1) * $r2, 2));
            $breakdown[] = sprintf(__('Total Wisconsin state tax = %s', 'ustc2025'), number_format($tax, 2));
        } elseif ($taxable <= $b3) {
            $tax = ($b1 * $r1) + (($b2 - $b1) * $r2) + (($taxable - $b2) * $r3);
            $breakdown[] = sprintf(__('Tax bracket: $0 to $14,680 at 3.50%% = %s', 'ustc2025'), number_format($b1 * $r1, 2));
            $breakdown[] = sprintf(__('Tax bracket: $14,680 to $29,370 at 4.40%% = %s', 'ustc2025'), number_format(($b2 - $b1) * $r2, 2));
            $breakdown[] = sprintf(__('Tax bracket: $29,370 to $323,290 at 5.30%% = (%s - %s) * 5.30%% = %s', 'ustc2025'), number_format($taxable, 2), number_format($b2, 2), number_format(($taxable - $b2) * $r3, 2));
            $breakdown[] = sprintf(__('Total Wisconsin state tax = %s', 'ustc2025'), number_format($tax, 2));
        } else {
            $tax = ($b1 * $r1) + (($b2 - $b1) * $r2) + (($b3 - $b2) * $r3) + (($taxable - $b3) * $r4);
            $breakdown[] = sprintf(__('Tax bracket: $0 to $14,680 at 3.50%% = %s', 'ustc2025'), number_format($b1 * $r1, 2));
            $breakdown[] = sprintf(__('Tax bracket: $14,680 to $29,370 at 4.40%% = %s', 'ustc2025'), number_format(($b2 - $b1) * $r2, 2));
            $breakdown[] = sprintf(__('Tax bracket: $29,370 to $323,290 at 5.30%% = %s', 'ustc2025'), number_format(($b3 - $b2) * $r3, 2));
            $breakdown[] = sprintf(__('Tax bracket: $323,290 or more at 7.65%% = (%s - %s) * 7.65%% = %s', 'ustc2025'), number_format($taxable, 2), number_format($b3, 2), number_format(($taxable - $b3) * $r4, 2));
            $breakdown[] = sprintf(__('Total Wisconsin state tax = %s', 'ustc2025'), number_format($tax, 2));
        }

        $tax = round($tax, 2);
        $tax_diff = $tax - $withholding;
        $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));

        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }

    private function idaho_tax($gross, $withholding, $residency, $settings, &$breakdown)
    {
        $flat_rate = isset($settings['flat_rate']) ? floatval($settings['flat_rate']) : 5.3;
        $id_deduction = isset($settings['id_deduction']) ? floatval($settings['id_deduction']) : 15000;
        $pbf_tax = isset($settings['permanent_building_fund_tax']) ? floatval($settings['permanent_building_fund_tax']) : 10;
        $non_taxable_amount = 4811;

        if ($residency === 'resident') {
            $taxable = max(0, $gross - $id_deduction - $non_taxable_amount);
            $breakdown[] = sprintf(__('Idaho resident: Taxable income = Total income (%s) - Idaho deduction (%s) - Non-taxable amount (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($id_deduction, 2), number_format($non_taxable_amount, 2), number_format($taxable, 2));
        } else {
            $taxable = max(0, $gross - $non_taxable_amount);
            $breakdown[] = sprintf(__('Idaho non-resident: Taxable income = Total income (%s) - Non-taxable amount (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($non_taxable_amount, 2), number_format($taxable, 2));
        }

        $rate_decimal = $flat_rate / 100;
        $id_tax = $taxable * $rate_decimal;
        $breakdown[] = sprintf(__('Idaho tax = %s * %s%% = %s', 'ustc2025'), number_format($taxable, 2), $flat_rate, number_format($id_tax, 2));

        $final_tax = $id_tax + $pbf_tax;
        $breakdown[] = sprintf(__('Idaho final tax = Idaho tax (%s) + Permanent building fund tax (%s) = %s', 'ustc2025'), number_format($id_tax, 2), number_format($pbf_tax, 2), number_format($final_tax, 2));

        $tax_diff = $withholding - $final_tax;
        if ($tax_diff > 0) {
            $breakdown[] = sprintf(__('State Tax Return = State withholding (%s) - Idaho final tax (%s) = %s', 'ustc2025'), number_format($withholding, 2), number_format($final_tax, 2), number_format($tax_diff, 2));
        } else {
            $breakdown[] = sprintf(__('State Tax Owed = State withholding (%s) - Idaho final tax (%s) = %s', 'ustc2025'), number_format($withholding, 2), number_format($final_tax, 2), number_format($tax_diff, 2));
        }

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
        $rh_personal_deduction_res = isset($settings['rh_personal_deduction_res']) ? floatval($settings['rh_personal_deduction_res']) : 16000;
        $rh_personal_deduction_nonres = isset($settings['rh_personal_deduction_nonres']) ? floatval($settings['rh_personal_deduction_nonres']) : 5100;

        if ($residency === 'resident') {
            $personal_deduction = $rh_personal_deduction_res;
            $breakdown[] = sprintf(__('Rhode Island resident: Taxable income = Total income (%s) - Personal deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($personal_deduction, 2), number_format($gross - $personal_deduction, 2));
        } else {
            $personal_deduction = $rh_personal_deduction_nonres;
            $breakdown[] = sprintf(__('Rhode Island non-resident: Taxable income = Total income (%s) - Personal deduction (%s) = %s', 'ustc2025'), number_format($gross, 2), number_format($personal_deduction, 2), number_format($gross - $personal_deduction, 2));
        }

        $taxable = max(0, $gross - $personal_deduction);

        // Apply Rhode Island tax brackets
        $b1 = 79900;
        $b2 = 181650;
        $r1 = 0.0375;
        $r2 = 0.0475;
        $r3 = 0.0599;

        if ($taxable <= $b1) {
            $tax = $taxable * $r1;
            $breakdown[] = sprintf(__('Tax bracket: $0 to $79,900 at 3.75%% = %s * 3.75%% = %s', 'ustc2025'), number_format($taxable, 2), number_format($tax, 2));
        } elseif ($taxable <= $b2) {
            $tax = ($b1 * $r1) + (($taxable - $b1) * $r2);
            $breakdown[] = sprintf(__('Tax bracket: $0 to $79,900 at 3.75%% = %s', 'ustc2025'), number_format($b1 * $r1, 2));
            $breakdown[] = sprintf(__('Tax bracket: $79,900 to $181,650 at 4.75%% = (%s - %s) * 4.75%% = %s', 'ustc2025'), number_format($taxable, 2), number_format($b1, 2), number_format(($taxable - $b1) * $r2, 2));
            $breakdown[] = sprintf(__('Total Rhode Island state tax = %s', 'ustc2025'), number_format($tax, 2));
        } else {
            $tax = ($b1 * $r1) + (($b2 - $b1) * $r2) + (($taxable - $b2) * $r3);
            $breakdown[] = sprintf(__('Tax bracket: $0 to $79,900 at 3.75%% = %s', 'ustc2025'), number_format($b1 * $r1, 2));
            $breakdown[] = sprintf(__('Tax bracket: $79,900 to $181,650 at 4.75%% = %s', 'ustc2025'), number_format(($b2 - $b1) * $r2, 2));
            $breakdown[] = sprintf(__('Tax bracket: $181,650 or more at 5.99%% = (%s - %s) * 5.99%% = %s', 'ustc2025'), number_format($taxable, 2), number_format($b2, 2), number_format(($taxable - $b2) * $r3, 2));
            $breakdown[] = sprintf(__('Total Rhode Island state tax = %s', 'ustc2025'), number_format($tax, 2));
        }

        $tax = round($tax, 2);
        $tax_diff = $tax - $withholding;
        $breakdown[] = sprintf(__('Tax - withholding = %s - %s = %s', 'ustc2025'), number_format($tax, 2), number_format($withholding, 2), number_format($tax_diff, 2));

        return ['tax' => $tax, 'tax_diff' => $tax_diff, 'breakdown' => $breakdown];
    }
}

new USTaxCalculator2025();
