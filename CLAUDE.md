# CLAUDE.md - AI Assistant Guide for ETS (US Tax Calculator 2025)

## Project Overview

A WordPress plugin that calculates US federal and state income tax for tax year 2025. It supports all 50 states plus DC, with both resident and non-resident tax scenarios. The plugin provides a frontend calculator via the `[us_tax_calculator_2025]` shortcode and a WordPress admin interface for managing tax settings.

## Repository Structure

```
ETS/
├── us-tax-calculator-2025.php   # Entire plugin (single file, ~3000 lines)
├── placeholder                  # Empty placeholder file
└── CLAUDE.md                    # This file
```

This is a **single-file WordPress plugin**. All logic — admin UI, frontend form, tax calculations, CSS, and JavaScript — lives in `us-tax-calculator-2025.php`.

## Architecture

### Single Class: `USTaxCalculator2025`

The plugin is one PHP class with these sections:

| Section | Lines (approx.) | Purpose |
|---------|-----------------|---------|
| Properties & constructor | 1–71 | State list, WP hook registration |
| Default settings | 72–560 | `defaults_federal()`, `defaults_states()` — hardcoded defaults for all states |
| WP integration | 560–655 | Asset enqueue, admin menu, settings registration |
| Admin UI | 655–1160 | `render_admin_page()` — tabbed settings interface |
| Admin helpers | 1160–1270 | Bracket/row rendering helpers, control tool |
| Frontend shortcode | 1270–1360 | `render_shortcode()` — form + result display |
| Federal tax calc | 1364–1430 | `calculate_federal()`, `progressive_tax()` |
| State dispatch | 1431–1574 | `calculate_state()` — routes to state-specific methods |
| State tax methods | 1576–3016 | 31 individual `*_tax()` methods |

### Key Methods

- **`render_shortcode()`** — Frontend form that accepts gross income, withholding amounts, state, and residency status
- **`render_admin_page()`** — Admin settings with tabs: Federal, States (52 sub-tabs), Control/Manual Check
- **`calculate_federal()`** — Computes federal tax using 2025 progressive brackets
- **`calculate_state()`** — Dispatcher that routes to the correct state-specific method
- **`progressive_tax()`** — Generic progressive bracket calculator (federal)
- **`apply_brackets()`** — Generic bracket calculator used by state methods
- **Individual state methods** — e.g., `california_tax()`, `new_york_tax()`, `ohio_tax()`

### Data Flow

1. User submits: gross income, federal withholding, state withholding, state, residency
2. `calculate_federal()` computes federal tax (different brackets for resident vs non-resident)
3. `calculate_state()` dispatches to the correct state method
4. Each method returns `['tax' => float, 'tax_diff' => float, 'breakdown' => array]`
5. `tax_diff` = calculated tax - withholding (positive = owed, negative = refund)

### State Tax Calculation Patterns

States use one of these approaches:

1. **No income tax** — AK, FL, NV, NH, SD, TN, TX, WA, WY (returns full refund of withholding)
2. **Flat rate** — e.g., CO, IL, PA, KY (single percentage of taxable income)
3. **Progressive brackets** — e.g., CA, NY, OR (income split across escalating rate brackets)
4. **Custom logic** — Some states have unique rules (AL uses federal tax deduction, UT has credits based on federal tax, MO uses federal tax deduction, etc.)

### WordPress Settings Storage

- **Federal settings:** `ustc2025_federal_settings` option (standard deduction)
- **State settings:** `us_tax_calculator_states_2025` option (per-state config keyed by state code)

### State Settings Structure

Each state has these base fields (some states add custom fields):
```php
[
    'state_deduction' => float,
    'personal_credit' => float,
    'calculation_mode' => 'progressive_brackets' | 'flat_rate',
    'flat_rate' => float,
    'brackets' => [
        ['min_income' => float, 'max_income' => float|'', 'base_tax' => float, 'rate' => float],
        ...
    ],
]
```

Some states have additional custom fields like `ct_deduction`, `ge_credit`, `id_deduction`, `colorado_deduction`, `ar_tax_credits`, `oh_tax_addon`, etc.

## Development Workflow

### No Build System

There is no build step, no bundler, and no package manager. The plugin is pure PHP with inline CSS and JavaScript.

### Installation (for testing)

1. Copy `us-tax-calculator-2025.php` into a WordPress installation's `/wp-content/plugins/` directory
2. Activate the plugin in the WordPress admin dashboard
3. Configure tax settings via the **US Tax Calculator 2025** admin menu
4. Add `[us_tax_calculator_2025]` shortcode to any page or post

### No Tests

There are no automated tests (no PHPUnit, no test directory, no CI/CD). All testing is manual through the WordPress frontend and admin interface. The plugin includes a "Control/Manual Check" tab in admin for testing calculations.

### No Linting/Formatting Config

No `.editorconfig`, `phpcs.xml`, `.eslintrc`, or other code style configuration exists.

## Conventions to Follow

### Code Style

- PSR-12 style PHP: opening braces on new lines for classes/functions, 4-space indentation
- WordPress coding conventions for hooks/filters: `add_shortcode()`, `add_action()`, `get_option()`, etc.
- Security: use `esc_html()`, `esc_attr()`, `wp_kses_post()` for output escaping; `sanitize_text_field()` and type-casting for input sanitization
- Translatable strings: wrap user-facing text in `__('string', 'ustc2025')` or `sprintf(__(...))` for the `ustc2025` text domain

### Adding a New State Tax Calculation

When adding support for a new state:

1. **Add a private method** named `{state_name}_tax()` following the pattern of existing methods. The method must return:
   ```php
   ['tax' => float, 'tax_diff' => float, 'breakdown' => array]
   ```
2. **Add routing** in `calculate_state()` with an `if ($code === 'XX')` block that calls the new method
3. **Set defaults** in `defaults_states()` with the state's default brackets, deduction, and calculation mode
4. **Verify** the state is already in the `$states_info` array (all 50 states + DC are listed)
5. **Include a breakdown** — every calculation step should append a human-readable string to the `$breakdown` array

### State Method Signatures

State tax methods have inconsistent signatures (historical). Common patterns:

```php
// Most common (newer states)
private function state_tax($gross, $withholding, $residency, $settings)

// States needing federal result (AL, OR, UT, MO)
private function state_tax($gross, $withholding, $residency, $settings, $federal_result)

// Some older states use a &$breakdown reference parameter
private function state_tax($gross, $withholding, $settings, &$breakdown)
private function state_tax($gross, $withholding, $residency, $settings, &$breakdown)
```

Newer state methods (SC, VT, MT, OH, IL) initialize `$breakdown` internally and do not use a reference parameter. Follow this pattern for new states.

### Commit Message Style

Based on the project history, commit messages follow this format:
- `Add {State} ({code}) state tax calculation support` — for new states
- `Fix {State} tax calculation ...` — for bug fixes
- `Update {State} tax calculation with {feature}` — for enhancements

### State Code Note

Georgia uses `GE` instead of the standard `GA` FIPS code. This is an established convention in this codebase — do not change it.

## Supported States

All 50 states + DC are listed in `$states_info`. States with **custom tax methods** (31 total):

AL, AR, CA, CO, CT, DE, GE, HI, IA, ID, IL, KY, LA, MD, ME, MN, MO, MT, NC, ND, NJ, NY, OH, OR, PA, RI, SC, UT, VA, VT, WI

States handled by the **generic fallback** in `calculate_state()`: AZ, MA, MI, NC

**No income tax states** (handled inline): AK, DC, FL, NV, NH, SD, TN, TX, WA, WY

## Key Gotchas

- **Monolithic file**: All 3000+ lines are in one file. When making changes, be precise about line locations.
- **No autoloading**: No Composer, no PSR-4. Everything is in one class.
- **Inline assets**: CSS and JS are rendered inline via `wp_enqueue_scripts` using `wp_add_inline_style` and `wp_add_inline_script`.
- **Settings sanitization**: `sanitize_state_settings()` handles input validation with type-casting and preserves custom fields per state.
- **Rate format**: State bracket rates are stored as percentages (e.g., `5.3` for 5.3%), while federal rates are decimals (e.g., `0.10` for 10%). Be careful with this difference.
- **Breakdown arrays**: Every calculation method must populate a `$breakdown` array with human-readable step-by-step strings showing the math.
