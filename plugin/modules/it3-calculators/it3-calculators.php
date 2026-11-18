<?php
/*
Plugin Name: iLoveTool3 Calculators
Plugin URI: https://ilovetool3.com
Description: 18 free online tools: Loan, Currency, SIP, Tax, Calorie, Water, Ideal Weight, Body Fat, CGPA, Grade, Date, Time Zone, Discount, Tip, Unit Converter, Random Number, Password Generator, Word Counter. Auto-creates pages from WP Admin > Tools.
Version: 2.0.0
Author: iLoveTool3
Author URI: https://ilovetool3.com
License: GPL v2 or later
Text Domain: it3-calculators
*/

if (!defined('ABSPATH')) {
    exit;
}
define('IT3CALC_VERSION','2.1.15');
define('IT3CALC_URL', plugin_dir_url(__FILE__));

/* ================= ASSETS ================= */
add_action('wp_enqueue_scripts', 'it3calc_assets');
function it3calc_assets() {
    wp_enqueue_style('it3calc-style', IT3CALC_URL . 'assets/css/calculators.css', array(), IT3CALC_VERSION);
    wp_enqueue_script('it3calc-script', IT3CALC_URL . 'assets/js/calculators.js', array(), IT3CALC_VERSION, true);
}

/* ================= TOOLS MAP (used by admin auto-create) ================= */
function it3calc_tools_map() {
    return array(
        'loan-calculator'         => array('Loan Calculator', '[loan_calculator]'),
        'currency-converter'      => array('Currency Converter', '[currency_converter]'),
        'sip-calculator'          => array('SIP Calculator', '[sip_calculator]'),
        'tax-calculator'          => array('Income Tax Calculator', '[tax_calculator]'),
        'calorie-calculator'      => array('Calorie Calculator', '[calorie_calculator]'),
        'water-intake-calculator' => array('Water Intake Calculator', '[water_calculator]'),
        'ideal-weight-calculator' => array('Ideal Weight Calculator', '[ideal_weight_calculator]'),
        'body-fat-calculator'     => array('Body Fat Calculator', '[body_fat_calculator]'),
        'cgpa-to-percentage'      => array('CGPA to Percentage Calculator', '[cgpa_calculator]'),
        'grade-calculator'        => array('Grade Calculator', '[grade_calculator]'),
        'date-calculator'         => array('Date Calculator', '[date_calculator]'),
        'time-zone-converter'     => array('Time Zone Converter', '[timezone_converter]'),
        'discount-calculator'     => array('Discount Calculator', '[discount_calculator]'),
        'tip-calculator'          => array('Tip Calculator', '[tip_calculator]'),
        'unit-converter'          => array('Unit Converter', '[unit_converter]'),
        'marla-kanal-converter' => array('Marla Kanal Converter','[marla_converter]'),
        'tola-converter' => array('Tola Masha Ratti Converter','[tola_converter]'),
		'solar-calculator'   => array('Solar Calculator', '[solar_calculator]'),
		'gold-price-calculator' => array('Gold Price Calculator', '[gold_calculator]'),
        'random-number-generator' => array('Random Number Generator', '[random_number_generator]'),
        'password-generator'      => array('Password Generator', '[password_generator]'),
        'word-counter'            => array('Word Counter', '[word_counter]'),
		'qr-code-generator' => array('QR Code Generator', '[qr_generator]'),
    );
}

/* ================= ADMIN: TOOLS DASHBOARD ================= */
add_action('admin_menu', 'it3calc_admin_menu', 10); // FIX (consolidation): now a submenu of the shared "iLoveTool3 Suite" parent menu instead of its own top-level entry
function it3calc_admin_menu() {
    add_submenu_page('imei-master', 'Tools Dashboard', 'IT3 Tools', 'manage_options', 'it3-tools', 'it3calc_admin_page');
}

function it3calc_admin_page() {
    $created = isset($_GET['created']) ? intval($_GET['created']) : -1;
    $tools = it3calc_tools_map();
    ?>
    <div class="wrap">
        <h1>&#128202; iLoveTool3 — Tools Dashboard</h1>

        <?php if ($created === 0) : ?>
            <div class="notice notice-warning"><p>Saare pages pehle se exist karte hain. Koi naya page nahi bana.</p></div>
        <?php elseif ($created > 0) : ?>
            <div class="notice notice-success"><p><strong><?php echo $created; ?> pages ban gaye!</strong> Ab <a href="<?php echo esc_url(home_url('/')); ?>" target="_blank">site</a> pe check karo.</p></div>
        <?php endif; ?>

        <div style="background:#fff;border:1px solid #ccd0d4;padding:20px;margin-top:15px;border-radius:6px;">
            <h2 style="margin-top:0;">&#9889; One-Click Pages</h2>
            <p>Neeche button dabao — saare <?php echo count($tools); ?> tools ke <strong>alag-alag pages automatically ban jayenge</strong> (jo pages pehle se hain wo skip ho jayenge).</p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="it3_create_pages" />
                <?php wp_nonce_field('it3_create_pages'); ?>
                <button type="submit" class="button button-primary button-hero">&#128194; Create All Tool Pages</button>
            </form>
        </div>

        <h2 style="margin-top:25px;">&#128203; All 18 Tools &amp; Shortcodes</h2>
        <table class="widefat striped">
            <thead><tr><th>#</th><th>Tool</th><th>Page Slug</th><th>Shortcode</th><th>Status</th></tr></thead>
            <tbody>
            <?php $i = 1; foreach ($tools as $slug => $data) :
                $exists = get_page_by_path($slug); ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><strong><?php echo esc_html($data[0]); ?></strong></td>
                    <td><code>/<?php echo esc_html($slug); ?>/</code></td>
                    <td><code><?php echo esc_html($data[1]); ?></code></td>
                    <td>
                        <?php if ($exists) : ?>
                            <span style="color:green;">&#9989; Page exists</span> —
                            <a href="<?php echo esc_url(get_permalink($exists)); ?>" target="_blank">view</a>
                        <?php else : ?>
                            <span style="color:#999;">Not created yet</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div style="background:#fff;border:1px solid #ccd0d4;padding:20px;margin:20px 0;border-radius:6px;">
            <h2>&#128230; Manual Shortcode Usage</h2>
            <p>Kisi bhi page/post mein shortcode paste karo:</p>
            <textarea readonly rows="10" style="width:100%;" onclick="this.select()"><?php
                foreach ($tools as $slug => $data) {
                    echo '<h2>' . $data[0] . "</h2>\n" . $data[1] . "\n\n";
                }
                echo '[it3_calculators]';
            ?></textarea>
        </div>
    </div>
    <?php
}

/* ================= ADMIN: AUTO CREATE PAGES HANDLER ================= */
add_action('admin_post_it3_create_pages', 'it3calc_create_pages_handler');
function it3calc_create_pages_handler() {
    if (!current_user_can('manage_options')) {
        wp_die('Permission denied.');
    }
    check_admin_referer('it3_create_pages');

    $count = 0;
    foreach (it3calc_tools_map() as $slug => $data) {
        $existing = get_page_by_path($slug);
        if (!$existing) {
            $result = wp_insert_post(array(
                'post_title'   => $data[0],
                'post_name'    => $slug,
                'post_content' => $data[1],
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'comment_status' => 'closed',
            ));
            if ($result) { $count++; }
        }
    }

    wp_redirect(admin_url('admin.php?page=it3-tools&created=' . $count));
    exit;
}

/* ================= ALL-IN-ONE SHORTCODE ================= */
add_shortcode('it3_calculators', 'it3calc_all');
function it3calc_all($atts) {
    $atts = shortcode_atts(array(
        'show' => 'loan,currency,sip,tax,calorie,water,weight,bodyfat,cgpa,grade,date,timezone,discount,tip,units,random,password,words'
    ), $atts);

    $map = array(
        'loan'     => array('it3_loan_calculator', 'Loan EMI Calculator'),
        'currency' => array('it3_currency_converter', 'Currency Converter'),
        'sip'      => array('it3_sip_calculator', 'SIP Calculator'),
        'tax'      => array('it3_tax_calculator', 'Tax Calculator'),
        'calorie'  => array('it3_calorie_calculator', 'Calorie Calculator'),
        'water'    => array('it3_water_calculator', 'Water Intake Calculator'),
        'weight'   => array('it3_weight_calculator', 'Ideal Weight Calculator'),
        'bodyfat'  => array('it3_bodyfat_calculator', 'Body Fat Calculator'),
        'cgpa'     => array('it3_cgpa_calculator', 'CGPA to Percentage'),
        'grade'    => array('it3_grade_calculator', 'Grade Calculator'),
        'date'     => array('it3_date_calculator', 'Date Difference Calculator'),
        'timezone' => array('it3_timezone_converter', 'Time Zone Converter'),
        'discount' => array('it3_discount_calculator', 'Discount Calculator'),
        'tip'      => array('it3_tip_calculator', 'Tip Calculator'),
        'units'    => array('it3_unit_converter', 'Unit Converter'),
        'random'   => array('it3_random_calculator', 'Random Number Generator'),
        'password' => array('it3_password_generator', 'Password Generator'),
        'words'    => array('it3_word_counter', 'Word Counter'),
    );

    $show_list = array_map('trim', explode(',', $atts['show']));

    ob_start();
    echo '<div class="it3-wrap">';
    foreach ($show_list as $key) {
        if (isset($map[$key])) {
            echo '<div class="it3-calc-section" id="calc-' . esc_attr($key) . '">';
            $fn = $map[$key][0];
            if (function_exists($fn)) { echo call_user_func($fn); }
            echo '</div>';
        }
    }
    echo '</div>';
    return ob_get_clean();
}

/* ============ 1. LOAN / EMI CALCULATOR ============ */
// [merged: duplicate of quick-calculators' 'loan_calculator'] add_shortcode('loan_calculator', 'it3_loan_calculator');
function it3_loan_calculator() {
    ob_start(); ?>
    <div class="it3-calc" data-calc="loan">
        <h3 class="it3-calc-title"><span class="it3-emoji">&#128176;</span> Loan EMI Calculator</h3>
        <div class="it3-field"><label>Loan Amount</label><input type="number" id="it3-loan-amount" placeholder="e.g. 25000" min="1" /></div>
        <div class="it3-field"><label>Interest Rate (% per year)</label><input type="number" id="it3-loan-rate" placeholder="e.g. 8.5" step="0.01" min="0" /></div>
        <div class="it3-field"><label>Loan Tenure (years)</label><input type="number" id="it3-loan-years" placeholder="e.g. 5" min="1" /></div>
        <button type="button" class="it3-btn" data-action="loan">Calculate EMI</button>
        <div class="it3-result" style="display:none;"></div>
        <div class="it3-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 2. CURRENCY CONVERTER (LIVE RATES) ============ */
add_shortcode('currency_converter', 'it3_currency_converter');
function it3_currency_converter() {
    $currencies = array(
        'USD'=>'US Dollar','EUR'=>'Euro','GBP'=>'British Pound','INR'=>'Indian Rupee','PKR'=>'Pakistani Rupee',
        'AED'=>'UAE Dirham','SAR'=>'Saudi Riyal','BDT'=>'Bangladeshi Taka','CAD'=>'Canadian Dollar','AUD'=>'Australian Dollar',
        'JPY'=>'Japanese Yen','CNY'=>'Chinese Yuan','TRY'=>'Turkish Lira','MYR'=>'Malaysian Ringgit','SGD'=>'Singapore Dollar',
        'ZAR'=>'South African Rand','BRL'=>'Brazilian Real','RUB'=>'Russian Ruble','CHF'=>'Swiss Franc','SEK'=>'Swedish Krona',
        'NOK'=>'Norwegian Krone','DKK'=>'Danish Krone','NZD'=>'New Zealand Dollar','HKD'=>'Hong Kong Dollar','KRW'=>'South Korean Won',
        'THB'=>'Thai Baht','IDR'=>'Indonesian Rupiah','PHP'=>'Philippine Peso','MXN'=>'Mexican Peso','EGP'=>'Egyptian Pound',
    );

    $options = '';
    foreach ($currencies as $code => $name) {
        $options .= '<option value="' . esc_attr($code) . '">' . esc_html($code) . ' — ' . esc_html($name) . '</option>';
    }

    ob_start(); ?>
    <div class="it3-calc" data-calc="currency">
        <h3 class="it3-calc-title"><span class="it3-emoji">&#128176;</span> Currency Converter</h3>
        <p class="it3-note">&#127760; Live exchange rates &#8226; Updated daily</p>
        <div class="it3-field"><label>Amount</label><input type="number" id="it3-cur-amount" placeholder="e.g. 100" step="any" value="100" /></div>
        <div class="it3-row2">
            <div class="it3-field"><label>From</label><select id="it3-cur-from"><?php echo $options; ?></select></div>
            <div class="it3-field"><label>To</label><select id="it3-cur-to"><?php
                // Put USD first selected, INR second for convenience
                foreach ($currencies as $code => $name) {
                    $sel = ($code === 'PKR') ? ' selected' : '';
                    echo '<option value="' . esc_attr($code) . '"' . $sel . '>' . esc_html($code) . ' — ' . esc_html($name) . '</option>';
                } ?></select></div>
        </div>
        <button type="button" class="it3-btn" data-action="currency">Convert Now</button>
        <div class="it3-result" style="display:none;"></div>
        <div class="it3-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 3. SIP CALCULATOR ============ */
add_shortcode('sip_calculator', 'it3_sip_calculator');
function it3_sip_calculator() {
    ob_start(); ?>
    <div class="it3-calc" data-calc="sip">
        <h3 class="it3-calc-title"><span class="it3-emoji">&#128200;</span> SIP Calculator</h3>
        <p class="it3-note">Systematic Investment Plan returns estimate</p>
        <div class="it3-field"><label>Monthly Investment</label><input type="number" id="it3-sip-monthly" placeholder="e.g. 5000" min="1" /></div>
        <div class="it3-field"><label>Expected Return Rate (% per year)</label><input type="number" id="it3-sip-rate" placeholder="e.g. 12" step="0.01" min="0" /></div>
        <div class="it3-field"><label>Investment Duration (years)</label><input type="number" id="it3-sip-years" placeholder="e.g. 10" min="1" /></div>
        <button type="button" class="it3-btn" data-action="sip">Calculate Returns</button>
        <div class="it3-result" style="display:none;"></div>
        <div class="it3-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 4. TAX CALCULATOR ============ */
add_shortcode('tax_calculator', 'it3_tax_calculator');
function it3_tax_calculator() {
    ob_start(); ?>
    <div class="it3-calc" data-calc="tax">
        <h3 class="it3-calc-title"><span class="it3-emoji">&#129534;</span> Income Tax Calculator</h3>
        <div class="it3-field">
            <label>Country / System</label>
            <select id="it3-tax-country">
                <option value="pk">Pakistan (Salaried 2026-27)</option>
                <option value="in">India (New Regime FY 2026-27)</option>
                <option value="us">USA (Federal Single 2026)</option>
            </select>
        </div>
        <div class="it3-field"><label>Annual Income</label><input type="number" id="it3-tax-income" placeholder="e.g. 1200000" min="0" /></div>
        <button type="button" class="it3-btn" data-action="tax">Calculate Tax</button>
        <div class="it3-result" style="display:none;"></div>
        <div class="it3-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 5. CALORIE CALCULATOR ============ */
add_shortcode('calorie_calculator', 'it3_calorie_calculator');
function it3_calorie_calculator() {
    ob_start(); ?>
    <div class="it3-calc" data-calc="calorie">
        <h3 class="it3-calc-title"><span class="it3-emoji">&#127831;</span> Daily Calorie Calculator</h3>
        <div class="it3-row2">
            <div class="it3-field"><label>Age (years)</label><input type="number" id="it3-cal-age" placeholder="25" min="10" max="100" /></div>
            <div class="it3-field"><label>Gender</label>
                <select id="it3-cal-gender"><option value="male">Male</option><option value="female">Female</option></select></div>
        </div>
        <div class="it3-row2">
            <div class="it3-field"><label>Height (cm)</label><input type="number" id="it3-cal-height" placeholder="170" min="50" /></div>
            <div class="it3-field"><label>Weight (kg)</label><input type="number" id="it3-cal-weight" placeholder="65" min="20" /></div>
        </div>
        <div class="it3-field"><label>Activity Level</label>
            <select id="it3-cal-activity">
                <option value="1.2">Sedentary (little/no exercise)</option>
                <option value="1.375">Light (1-3 days/week)</option>
                <option value="1.55" selected>Moderate (3-5 days/week)</option>
                <option value="1.725">Very Active (6-7 days/week)</option>
                <option value="1.9">Super Active (physical job)</option>
            </select></div>
        <button type="button" class="it3-btn" data-action="calorie">Calculate Calories</button>
        <div class="it3-result" style="display:none;"></div>
        <div class="it3-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 6. WATER INTAKE CALCULATOR ============ */
add_shortcode('water_calculator', 'it3_water_calculator');
function it3_water_calculator() {
    ob_start(); ?>
    <div class="it3-calc" data-calc="water">
        <h3 class="it3-calc-title"><span class="it3-emoji">&#128167;</span> Water Intake Calculator</h3>
        <div class="it3-field"><label>Weight (kg)</label><input type="number" id="it3-wat-weight" placeholder="e.g. 70" min="20" /></div>
        <div class="it3-field"><label>Exercise Time (minutes/day)</label><input type="number" id="it3-wat-exercise" placeholder="e.g. 30" min="0" /></div>
        <div class="it3-field"><label>Climate</label>
            <select id="it3-wat-climate">
                <option value="normal" selected>Normal</option>
                <option value="hot">Hot / Humid</option>
                <option value="cold">Cold</option>
            </select></div>
        <button type="button" class="it3-btn" data-action="water">Calculate</button>
        <div class="it3-result" style="display:none;"></div>
        <div class="it3-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 7. IDEAL WEIGHT CALCULATOR ============ */
// [merged: duplicate of quick-calculators' 'ideal_weight_calculator'] add_shortcode('ideal_weight_calculator', 'it3_weight_calculator');
function it3_weight_calculator() {
    ob_start(); ?>
    <div class="it3-calc" data-calc="weight">
        <h3 class="it3-calc-title"><span class="it3-emoji">&#9878;</span> Ideal Weight Calculator</h3>
        <div class="it3-field"><label>Gender</label>
            <select id="it3-iw-gender"><option value="male">Male</option><option value="female">Female</option></select></div>
        <div class="it3-field"><label>Height (cm)</label><input type="number" id="it3-iw-height" placeholder="e.g. 170" min="100" /></div>
        <button type="button" class="it3-btn" data-action="weight">Calculate</button>
        <div class="it3-result" style="display:none;"></div>
        <div class="it3-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 8. BODY FAT CALCULATOR (NAVY METHOD) ============ */
add_shortcode('body_fat_calculator', 'it3_bodyfat_calculator');
function it3_bodyfat_calculator() {
    ob_start(); ?>
    <div class="it3-calc" data-calc="bodyfat">
        <h3 class="it3-calc-title"><span class="it3-emoji">&#128170;</span> Body Fat Calculator (Navy Method)</h3>
        <div class="it3-row2">
            <div class="it3-field"><label>Gender</label>
                <select id="it3-bf-gender"><option value="male">Male</option><option value="female">Female</option></select></div>
            <div class="it3-field"><label>Age (years)</label><input type="number" id="it3-bf-age" placeholder="25" min="10" max="100" /></div>
        </div>
        <div class="it3-field"><label>Height (cm)</label><input type="number" id="it3-bf-height" placeholder="e.g. 175" min="100" /></div>
        <div class="it3-field"><label>Neck Circumference (cm)</label><input type="number" id="it3-bf-neck" placeholder="e.g. 38" step="0.1" min="20" /></div>
        <div class="it3-field"><label>Waist Circumference (cm)</label><input type="number" id="it3-bf-waist" placeholder="e.g. 85" step="0.1" min="40" /></div>
        <div class="it3-field" id="it3-bf-hip-wrap"><label>Hip Circumference (cm) — women only</label><input type="number" id="it3-bf-hip" placeholder="e.g. 95" step="0.1" min="50" /></div>
        <button type="button" class="it3-btn" data-action="bodyfat">Calculate Body Fat</button>
        <div class="it3-result" style="display:none;"></div>
        <div class="it3-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 9. CGPA TO PERCENTAGE ============ */
add_shortcode('cgpa_calculator', 'it3_cgpa_calculator');
function it3_cgpa_calculator() {
    ob_start(); ?>
    <div class="it3-calc" data-calc="cgpa">
        <h3 class="it3-calc-title"><span class="it3-emoji">&#127891;</span> CGPA to Percentage Calculator</h3>
        <div class="it3-row2">
            <div class="it3-field"><label>Your CGPA</label><input type="number" id="it3-cg-value" placeholder="e.g. 8.5" step="0.01" min="0" /></div>
            <div class="it3-field"><label>CGPA Scale</label>
                <select id="it3-cg-scale">
                    <option value="10">Out of 10</option>
                    <option value="4">Out of 4</option>
                    <option value="5">Out of 5</option>
                </select></div>
        </div>
        <button type="button" class="it3-btn" data-action="cgpa">Convert</button>
        <div class="it3-result" style="display:none;"></div>
        <div class="it3-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 10. GRADE CALCULATOR ============ */
add_shortcode('grade_calculator', 'it3_grade_calculator');
function it3_grade_calculator() {
    ob_start(); ?>
    <div class="it3-calc" data-calc="grade">
        <h3 class="it3-calc-title"><span class="it3-emoji">&#128394;</span> Final Grade Calculator</h3>
        <p class="it3-note">Final exam mein kitne marks chahiye desired grade ke liye?</p>
        <div class="it3-field"><label>Current Grade (%)</label><input type="number" id="it3-gr-current" placeholder="e.g. 78" min="0" max="100" step="0.01" /></div>
        <div class="it3-field"><label>Final Exam Weight (% of total grade)</label><input type="number" id="it3-gr-weight" placeholder="e.g. 40" min="1" max="100" /></div>
        <div class="it3-field"><label>Desired Overall Grade (%)</label><input type="number" id="it3-gr-desired" placeholder="e.g. 85" min="0" max="100" step="0.01" /></div>
        <button type="button" class="it3-btn" data-action="grade">What Do I Need?</button>
        <div class="it3-result" style="display:none;"></div>
        <div class="it3-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 11. DATE DIFFERENCE CALCULATOR ============ */
add_shortcode('date_calculator', 'it3_date_calculator');
function it3_date_calculator() {
    ob_start(); ?>
    <div class="it3-calc" data-calc="date">
        <h3 class="it3-calc-title"><span class="it3-emoji">&#128197;</span> Date Difference Calculator</h3>
        <div class="it3-row2">
            <div class="it3-field"><label>Start Date</label><input type="date" id="it3-date-start" /></div>
            <div class="it3-field"><label>End Date</label><input type="date" id="it3-date-end" /></div>
        </div>
        <button type="button" class="it3-btn" data-action="date">Calculate Difference</button>
        <div class="it3-result" style="display:none;"></div>
        <div class="it3-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 12. TIME ZONE CONVERTER ============ */
add_shortcode('timezone_converter', 'it3_timezone_converter');
function it3_timezone_converter() {
    ob_start(); ?>
    <div class="it3-calc" data-calc="timezone">
        <h3 class="it3-calc-title"><span class="it3-emoji">&#127463;</span> Time Zone Converter</h3>
        <div class="it3-row2">
            <div class="it3-field"><label>Date</label><input type="date" id="it3-tz-date" /></div>
            <div class="it3-field"><label>Time</label><input type="time" id="it3-tz-time" /></div>
        </div>
        <div class="it3-row2">
            <div class="it3-field"><label>From Time Zone</label><select id="it3-tz-from"></select></div>
            <div class="it3-field"><label>To Time Zone</label><select id="it3-tz-to"></select></div>
        </div>
        <button type="button" class="it3-btn" data-action="timezone">Convert Time</button>
        <button type="button" class="it3-btn it3-btn-light" data-action="timezone-now">Use Current Time</button>
        <div class="it3-result" style="display:none;"></div>
        <div class="it3-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 13. DISCOUNT CALCULATOR ============ */
// [merged: duplicate of quick-calculators' 'discount_calculator'] add_shortcode('discount_calculator', 'it3_discount_calculator');
function it3_discount_calculator() {
    ob_start(); ?>
    <div class="it3-calc" data-calc="discount">
        <h3 class="it3-calc-title"><span class="it3-emoji">&#127872;</span> Discount Calculator</h3>
        <div class="it3-field"><label>Original Price</label><input type="number" id="it3-disc-price" placeholder="e.g. 100" min="0" step="any" /></div>
        <div class="it3-field"><label>Discount (%)</label><input type="number" id="it3-disc-percent" placeholder="e.g. 25" min="0" max="100" /></div>
        <button type="button" class="it3-btn" data-action="discount">Calculate</button>
        <div class="it3-result" style="display:none;"></div>
        <div class="it3-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 14. TIP CALCULATOR ============ */
// [merged: duplicate of quick-calculators' 'tip_calculator'] add_shortcode('tip_calculator', 'it3_tip_calculator');
function it3_tip_calculator() {
    ob_start(); ?>
    <div class="it3-calc" data-calc="tip">
        <h3 class="it3-calc-title"><span class="it3-emoji">&#127869;</span> Tip Calculator</h3>
        <div class="it3-field"><label>Bill Amount</label><input type="number" id="it3-tip-bill" placeholder="e.g. 85" min="0" step="any" /></div>
        <div class="it3-field"><label>Tip Percentage</label>
            <select id="it3-tip-percent">
                <option value="10">10%</option>
                <option value="15" selected>15% (Standard)</option>
                <option value="18">18% (Good)</option>
                <option value="20">20% (Great)</option>
                <option value="25">25% (Excellent)</option>
            </select></div>
        <div class="it3-field"><label>Split Between People</label><input type="number" id="it3-tip-people" placeholder="e.g. 4" min="1" value="1" /></div>
        <button type="button" class="it3-btn" data-action="tip">Calculate Tip</button>
        <div class="it3-result" style="display:none;"></div>
        <div class="it3-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 15. UNIT CONVERTER ============ */
add_shortcode('unit_converter', 'it3_unit_converter');
function it3_unit_converter() {
    ob_start(); ?>
    <div class="it3-calc" data-calc="units">
        <h3 class="it3-calc-title"><span class="it3-emoji">&#128260;</span> Unit Converter</h3>
        <div class="it3-field"><label>Category</label>
            <select id="it3-unit-cat">
                <option value="length">Length</option>
                <option value="weight">Weight</option>
                <option value="temperature">Temperature</option>
            </select></div>
        <div class="it3-field"><label>Value</label><input type="number" id="it3-unit-value" placeholder="Enter value" step="any" /></div>
        <div class="it3-row2">
            <div class="it3-field"><label>From</label><select id="it3-unit-from"></select></div>
            <div class="it3-field"><label>To</label><select id="it3-unit-to"></select></div>
        </div>
        <button type="button" class="it3-btn" data-action="units">Convert</button>
        <div class="it3-result" style="display:none;"></div>
        <div class="it3-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 16. RANDOM NUMBER GENERATOR ============ */
add_shortcode('random_number_generator', 'it3_random_calculator');
function it3_random_calculator() {
    ob_start(); ?>
    <div class="it3-calc" data-calc="random">
        <h3 class="it3-calc-title"><span class="it3-emoji">&#127922;</span> Random Number Generator</h3>
        <div class="it3-row2">
            <div class="it3-field"><label>Minimum</label><input type="number" id="it3-rand-min" placeholder="1" value="1" /></div>
            <div class="it3-field"><label>Maximum</label><input type="number" id="it3-rand-max" placeholder="100" value="100" /></div>
        </div>
        <div class="it3-field"><label>How Many Numbers?</label><input type="number" id="it3-rand-count" value="1" min="1" max="100" /></div>
        <div class="it3-checks">
            <label class="it3-check"><input type="checkbox" id="it3-rand-unique" checked /> No duplicates</label>
            <label class="it3-check"><input type="checkbox" id="it3-rand-sort" /> Sort results</label>
        </div>
        <button type="button" class="it3-btn" data-action="random">Generate Numbers</button>
        <div class="it3-result" style="display:none;"></div>
        <div class="it3-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 17. PASSWORD GENERATOR ============ */
// [merged: duplicate of quick-calculators' 'password_generator'] add_shortcode('password_generator', 'it3_password_generator');
function it3_password_generator() {
    ob_start(); ?>
    <div class="it3-calc" data-calc="password">
        <h3 class="it3-calc-title"><span class="it3-emoji">&#128273;</span> Password Generator</h3>
        <div class="it3-field">
            <label>Password Length: <strong id="it3-pw-len-val">16</strong></label>
            <input type="range" id="it3-pw-length" min="6" max="64" value="16" />
        </div>
        <div class="it3-checks">
            <label class="it3-check"><input type="checkbox" id="it3-pw-upper" checked /> Uppercase (A-Z)</label>
            <label class="it3-check"><input type="checkbox" id="it3-pw-lower" checked /> Lowercase (a-z)</label>
            <label class="it3-check"><input type="checkbox" id="it3-pw-numbers" checked /> Numbers (0-9)</label>
            <label class="it3-check"><input type="checkbox" id="it3-pw-symbols" checked /> Symbols (!@#$)</label>
        </div>
        <button type="button" class="it3-btn" data-action="password">Generate Password</button>
        <div class="it3-result" style="display:none;"></div>
        <div class="it3-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 18. WORD COUNTER ============ */
// [merged: duplicate of quick-calculators' 'word_counter'] add_shortcode('word_counter', 'it3_word_counter');
function it3_word_counter() {
    ob_start(); ?>
    <div class="it3-calc" data-calc="words">
        <h3 class="it3-calc-title"><span class="it3-emoji">&#9997;</span> Word Counter</h3>
        <div class="it3-field"><label>Paste your text below (counts live):</label>
            <textarea id="it3-wc-text" rows="7" placeholder="Type or paste your text here..."></textarea></div>
        <button type="button" class="it3-btn" data-action="words">Count Words</button>
        <button type="button" class="it3-btn it3-btn-light" data-action="words-clear">Clear</button>
        <div class="it3-stats-grid" style="display:none;" id="it3-wc-stats">
            <div class="it3-stat-box"><strong id="it3-wc-words">0</strong><span>Words</span></div>
            <div class="it3-stat-box"><strong id="it3-wc-chars">0</strong><span>Characters</span></div>
            <div class="it3-stat-box"><strong id="it3-wc-nospace">0</strong><span>No Spaces</span></div>
            <div class="it3-stat-box"><strong id="it3-wc-sentences">0</strong><span>Sentences</span></div>
            <div class="it3-stat-box"><strong id="it3-wc-paras">0</strong><span>Paragraphs</span></div>
            <div class="it3-stat-box"><strong id="it3-wc-read">0</strong><span>Min Read</span></div>
        </div>
        <div class="it3-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}


/* ==== 19. MARLA CONVERTER ==== */
add_shortcode('marla_converter', 'it3_marla_converter');
function it3_marla_converter() {
    ob_start(); ?>
    <div class="it3-calc" data-calc="marla">
        <h3 class="it3-calc-title"><span class="it3-emoji">📐</span> Marla Kanal Converter</h3>
        <div class="it3-field"><label>Amount</label><input type="number" id="it3-marla-amount" step="any" placeholder="e.g. 5" /></div>
        <div class="it3-field"><label>From Unit</label><select id="it3-marla-from"><option value="marla" selected>Marla</option><option value="kanal">Kanal</option><option value="sqft">Square Feet</option><option value="sqyd">Square Yards (Gaj)</option><option value="sqm">Square Meters</option><option value="acre">Acre</option><option value="murabba">Murabba</option></select></div>
        <div class="it3-field"><label>Marla Standard</label><select id="it3-marla-standard"><option value="272.25" selected>Traditional — 1 marla = 272.25 sq ft</option><option value="270">Punjab — 1 marla = 270 sq ft</option><option value="225">Housing Society (DHA / Bahria) — 1 marla = 225 sq ft</option></select></div>
        <button type="button" class="it3-btn" data-action="marla">Convert</button>
        <div class="it3-result" style="display:none;"></div>
        <div class="it3-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ==== 20. TOLA CONVERTER ==== */
add_shortcode('tola_converter', 'it3_tola_converter');
function it3_tola_converter() {
    ob_start(); ?>
    <div class="it3-calc" data-calc="tola">
        <h3 class="it3-calc-title"><span class="it3-emoji">⚖️</span> Tola Masha Ratti Converter</h3>
        <div class="it3-field"><label>Amount</label><input type="number" id="it3-tola-amount" step="any" placeholder="e.g. 2" /></div>
        <div class="it3-field"><label>From Unit</label><select id="it3-tola-from"><option value="tola" selected>Tola</option><option value="masha">Masha</option><option value="ratti">Ratti</option><option value="gram">Grams</option><option value="kg">Kilograms</option><option value="ozt">Troy Ounces</option></select></div>
        <button type="button" class="it3-btn" data-action="tola">Convert</button>
        <div class="it3-result" style="display:none;"></div>
        <div class="it3-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}
/* ==== 21. SOLAR CALCULATOR ==== */ add_shortcode('solar_calculator', 'it3_solar_calculator'); function it3_solar_calculator() { ob_start(); ?><div class="it3-calc" data-calc="solar">
		<h3 class="it3-calc-title">
			Solar Calculator
		</h3>
<div class="it3-field">
	<label>Average monthly electricity use (kWh)</label>
	<input type="number" id="it3-solar-usage" step="any" placeholder="e.g. 1000" />
		</div>
		<div class="it3-field">
			<label>Peak sun hours per day</label>
			<select id="it3-solar-sun">
				<option value="3.0">3.0</option>
				<option value="3.5">3.5</option>
				<option value="4.0">4.0</option>
				<option value="4.5">4.5</option>
				<option value="5.0" selected>5.0</option>
				<option value="5.5">5.5</option>
				<option value="6.0">6.0</option>
				<option value="6.5">6.5</option>
			</select>
			
		</div>
		<div class="it3-field">
			<label>Panel wattage (W)</label>
			<select id="it3-solar-panel">
				<option value="450">450</option>
				<option value="550" selected>550</option>
				<option value="580">580</option>
				<option value="620">620</option>
			</select>
			
		</div>
		<div class="it3-field">
			<label>Electricity price per kWh (optional)</label>
			<input type="number" id="it3-solar-price" step="any" placeholder="e.g. 0.30" />
		</div>
		<div class="it3-field">
			<label>Installed cost per watt (optional)</label>
			<input type="number" id="it3-solar-cost" step="any" placeholder="e.g. 1.00" />
		</div>
		<div class="it3-field">
			<label>Currency</label>
			<select id="it3-solar-currency">
				<option value="$" selected>USD $</option>
				<option value="Rs">PKR Rs</option>
				<option value="£">GBP £</option>
				<option value="€">EUR €</option>
				<option value="₹">INR ₹</option>
				<option value="AED">AED</option>
			</select>
			
		</div>
		<button type="button" class="it3-btn" data-action="solar">
			Calculate
		</button>
		<div class="it3-result" style="display:none;">
			
		</div>
		<div class="it3-error" style="display:none;">
			
		</div>
</div>
<?php return ob_get_clean();
																																}
/* ==== 22. GOLD PRICE CALCULATOR ==== */
add_shortcode('gold_calculator', 'it3_gold_calculator');
function it3_gold_calculator() {
    ob_start(); ?>
    <div class="it3-calc" data-calc="gold">
		
        <h3 class="it3-calc-title">
			Gold Price Calculator
		</h3>
        <div class="it3-field">
			<label>Weight</label>
			<input type="number" id="it3-gold-weight" step="any" placeholder="e.g. 10" />
		</div>		
        <div class="it3-field">
			<label>Weight unit</label>
			<select id="it3-gold-unit">
				<option value="1" selected>Gram</option>
				<option value="11.6638">Tola</option>
				<option value="31.1034768">Troy ounce</option>
				<option value="1.555174">Pennyweight (dwt)</option>
				
			</select>
			
		</div>		
        <div class="it3-field">
			<label>Current gold price</label>
			<input type="number" id="it3-gold-rate" step="any" placeholder="e.g. 4000" />
		</div>		
        <div class="it3-field">
			<label>Price is per</label>
			<select id="it3-gold-rate-unit">
				<option value="ozt" selected>Per troy ounce</option>
				<option value="gram">Per gram</option>
				<option value="tola">Per tola</option>
				
			</select>
			
		</div>		
        <div class="it3-field">
			<label>Karat</label>
			<select id="it3-gold-karat">
				<option value="1" selected>24K (pure)</option>
				<option value="0.916">22K</option>
				<option value="0.875">21K</option>
				<option value="0.75">18K</option>
				
			</select>
			
		</div>		
        <div class="it3-field">
			<label>Making charges % (optional)</label>
			<input type="number" id="it3-gold-making" step="any" value="0" placeholder="e.g. 10" />
		</div>		
        <div class="it3-field">
			<label>Currency</label>
			<select id="it3-gold-currency">
				<option value="$" selected>USD $</option>
				<option value="Rs">PKR Rs</option>
				<option value="£">GBP £</option>
				<option value="€">EUR €</option>
				<option value="₹">INR ₹</option>
				<option value="AED">AED</option>
				
			</select>
			
		</div>		
        <button type="button" class="it3-btn" data-action="gold">
			Calculate
		</button>		
        <div class="it3-result" style="display:none;">
			
		</div>		
        <div class="it3-error" style="display:none;">
			
		</div>		
</div>
<?php return ob_get_clean();
	
		
}
		
		










/* ============ 23. QR CODE GENERATOR ============ */
add_action('wp_enqueue_scripts', 'it3_qr_assets');
function it3_qr_assets() {
    wp_enqueue_script('it3-qrcode-lib', 'https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js', array(), '1.4.4', true);
}
add_shortcode('qr_generator', 'it3_qr_generator');
function it3_qr_generator() {
    ob_start();?>
    <div class="it3-calc" data-calc="qr">
        <h3 class="it3-calc-title"><span class="it3-emoji">&#11035;</span>QR Code Generator</h3>
        <div class="it3-field"><label>Text or URL</label><textarea id="it3-qr-text" rows="4" placeholder="Type or paste text, or a website address..."></textarea></div>
        <div class="it3-row2">
            <div class="it3-field"><label>Size (pixels)</label><select id="it3-qr-size"><option value="256">256</option><option value="512" selected>512</option><option value="1024">1024</option></select></div>
            <div class="it3-field"><label>Code colour</label><input type="color" id="it3-qr-fg" value="#000000" /></div>
            <div class="it3-field"><label>Background colour</label><input type="color" id="it3-qr-bg" value="#ffffff" /></div>
        </div>
        <button type="button" class="it3-btn" data-action="qr" id="it3-qr-generate">Generate QR Code</button>
        <button type="button" class="it3-btn it3-btn-light" id="it3-qr-download" style="display:none;">Download PNG</button>
        <div class="it3-error" id="it3-qr-error" style="display:none;"></div>
        <div id="it3-qr-preview" style="display:none; text-align:center; margin-top:15px;"><canvas id="it3-qr-canvas" style="max-width:100%; height:auto;"></canvas></div>
    </div>
    <?php return ob_get_clean();
}

/* ==== 26. SALARY CALCULATOR ==== */
add_shortcode('salary_calculator', 'it3_salary_calculator');
function it3_salary_calculator() {
    ob_start();?>
<div class="it3-calc" data-calc="salary-calculator">
<h3 class="it3-calc-title">Salary Calculator</h3>
<label>Pay amount</label>
<input class="it3-field" type="number" id="it3-salary-amount" step="any" min="0" />
<label>Pay period</label>
<select class="it3-field" id="it3-salary-period">
<option value="hourly">Hourly</option>
<option value="daily">Daily</option>
<option value="weekly">Weekly</option>
<option value="monthly">Monthly</option>
<option value="yearly">Yearly</option>
</select>
<label>Hours per week</label>
<input class="it3-field" type="number" id="it3-salary-hours" step="any" min="0" value="40" />
<label>Weeks per year</label>
<input class="it3-field" type="number" id="it3-salary-weeks" step="any" min="0" value="52" />
<label>Currency</label>
<select class="it3-field" id="it3-salary-currency">
<option value="USD">USD ($)</option>
<option value="PKR">PKR (Rs)</option>
<option value="GBP">GBP (£)</option>
<option value="EUR">EUR (€)</option>
<option value="INR">INR (₹)</option>
<option value="AED">AED</option>
</select>
<button type="button" class="it3-btn" id="it3-salary-calculate">Calculate Salary</button>
<div class="it3-error" id="it3-salary-error" style="display:none;"></div>
<div class="it3-result" id="it3-salary-result" style="display:none;">
<p>Hourly pay: <strong id="it3-salary-hourly"></strong></p>
<p>Daily pay: <strong id="it3-salary-daily"></strong></p>
<p>Weekly pay: <strong id="it3-salary-weekly"></strong></p>
<p>Monthly pay: <strong id="it3-salary-monthly"></strong></p>
<p>Yearly pay: <strong id="it3-salary-yearly"></strong></p>
</div>
</div>
<?php return ob_get_clean();
}

/* ==== 27. VAT / GST CALCULATOR ==== */
add_shortcode('vat_gst_calculator', 'it3_vat_gst_calculator');
function it3_vat_gst_calculator() {
    ob_start();?>
<div class="it3-calc" data-calc="vat-gst-calculator">
<h3 class="it3-calc-title">VAT / GST Calculator</h3>
<label>Amount</label>
<input class="it3-field" type="number" id="it3-vat-amount" step="any" min="0" />
<label>Tax rate preset</label>
<select class="it3-field" id="it3-vat-preset">
<option value="18" selected>Pakistan 18% (goods)</option>
<option value="16">Punjab services 16%</option>
<option value="15">Sindh / KP / Balochistan / Islamabad 15%</option>
<option value="20">UK 20%</option>
<option value="5">UAE 5%</option>
<option value="5">India 5%</option>
<option value="18">India 18%</option>
<option value="40">India 40%</option>
<option value="custom">Custom rate</option>
</select>
<label>Tax rate (%)</label>
<input class="it3-field" type="number" id="it3-vat-rate" step="any" min="0" value="18" />
<label>Mode</label>
<select class="it3-field" id="it3-vat-mode">
<option value="add">Add tax to a net price</option>
<option value="remove">Remove tax from a gross price</option>
</select>
<label>Currency</label>
<select class="it3-field" id="it3-vat-currency">
<option value="USD">USD ($)</option>
<option value="PKR">PKR (Rs)</option>
<option value="GBP">GBP (£)</option>
<option value="EUR">EUR (€)</option>
<option value="INR">INR (₹)</option>
<option value="AED">AED</option>
</select>
<button type="button" class="it3-btn" id="it3-vat-calculate">Calculate VAT / GST</button>
<div class="it3-error" id="it3-vat-error" style="display:none;"></div>
<div class="it3-result" id="it3-vat-result" style="display:none;">
<p>Net amount (before tax): <strong id="it3-vat-net"></strong></p>
<p>Tax amount: <strong id="it3-vat-tax"></strong></p>
<p>Total (including tax): <strong id="it3-vat-gross"></strong></p>
</div>
</div>
<?php return ob_get_clean();
}

/* ==== 28. TIME CARD CALCULATOR ==== */
add_shortcode('time_card_calculator', 'it3_time_card_calculator');
function it3_time_card_calculator() {
    ob_start();?>
<div class="it3-calc" data-calc="time-card-calculator">
<h3 class="it3-calc-title">Time Card Calculator</h3>
<p>Day - Start - End - Unpaid break (minutes) - Hours</p>
<p>Monday <input type="time" id="it3-timecard-monday-start" data-timecard-input> <input type="time" id="it3-timecard-monday-end" data-timecard-input> <input type="number" id="it3-timecard-monday-break" value="30" data-timecard-input data-default="30"> <span id="it3-timecard-monday-hours">0.00</span></p>
<p>Tuesday <input type="time" id="it3-timecard-tuesday-start" data-timecard-input> <input type="time" id="it3-timecard-tuesday-end" data-timecard-input> <input type="number" id="it3-timecard-tuesday-break" value="30" data-timecard-input data-default="30"> <span id="it3-timecard-tuesday-hours">0.00</span></p>
<p>Wednesday <input type="time" id="it3-timecard-wednesday-start" data-timecard-input> <input type="time" id="it3-timecard-wednesday-end" data-timecard-input> <input type="number" id="it3-timecard-wednesday-break" value="30" data-timecard-input data-default="30"> <span id="it3-timecard-wednesday-hours">0.00</span></p>
<p>Thursday <input type="time" id="it3-timecard-thursday-start" data-timecard-input> <input type="time" id="it3-timecard-thursday-end" data-timecard-input> <input type="number" id="it3-timecard-thursday-break" value="30" data-timecard-input data-default="30"> <span id="it3-timecard-thursday-hours">0.00</span></p>
<p>Friday <input type="time" id="it3-timecard-friday-start" data-timecard-input> <input type="time" id="it3-timecard-friday-end" data-timecard-input> <input type="number" id="it3-timecard-friday-break" value="30" data-timecard-input data-default="30"> <span id="it3-timecard-friday-hours">0.00</span></p>
<p>Saturday <input type="time" id="it3-timecard-saturday-start" data-timecard-input> <input type="time" id="it3-timecard-saturday-end" data-timecard-input> <input type="number" id="it3-timecard-saturday-break" value="30" data-timecard-input data-default="30"> <span id="it3-timecard-saturday-hours">0.00</span></p>
<p>Sunday <input type="time" id="it3-timecard-sunday-start" data-timecard-input> <input type="time" id="it3-timecard-sunday-end" data-timecard-input> <input type="number" id="it3-timecard-sunday-break" value="30" data-timecard-input data-default="30"> <span id="it3-timecard-sunday-hours">0.00</span></p>
<label>Hourly rate</label>
<input class="it3-field" type="number" id="it3-timecard-rate" step="any" min="0" data-timecard-input>
<label>Currency</label>
<select class="it3-field" id="it3-timecard-currency">
<option value="USD">USD ($)</option>
<option value="PKR">PKR (Rs)</option>
<option value="GBP">GBP (£)</option>
<option value="EUR">EUR (€)</option>
<option value="INR">INR (₹)</option>
<option value="AED">AED</option>
</select>
<label>Overtime threshold (hours per week)</label>
<input class="it3-field" type="number" id="it3-timecard-threshold" step="any" min="0" value="40">
<label>Overtime multiplier</label>
<input class="it3-field" type="number" id="it3-timecard-multiplier" step="any" min="1" value="1.5">
<button type="button" class="it3-btn" id="it3-timecard-calc">Calculate</button>
<button type="button" class="it3-btn" id="it3-timecard-clear">Clear</button>
<div class="it3-error" id="it3-timecard-error" style="display:none;"></div>
<div class="it3-result" id="it3-timecard-result" style="display:none;">
<p>Total hours: <strong id="it3-timecard-total"></strong></p>
<p>Regular hours: <strong id="it3-timecard-regular"></strong></p>
<p>Overtime hours: <strong id="it3-timecard-overtime"></strong></p>
<p>Regular pay: <strong id="it3-timecard-regular-pay"></strong></p>
<p>Overtime pay: <strong id="it3-timecard-overtime-pay"></strong></p>
<p>Gross pay: <strong id="it3-timecard-gross"></strong></p>
</div>
</div>
<?php return ob_get_clean();
}

/* ============ 29. FREELANCE HOURLY RATE CALCULATOR ============ */
add_shortcode('freelance_rate_calculator', 'it3_freelance_rate_calculator');
function it3_freelance_rate_calculator() {
    ob_start();?>
<div class="it3-calc" data-calc="freelance-rate-calculator">
<h3 class="it3-calc-title">Freelance Hourly Rate Calculator</h3>
<label>Target monthly take-home pay</label>
<input class="it3-field" type="number" id="it3-freelance-target" step="any" min="0" />
<label>Monthly business expenses</label>
<input class="it3-field" type="number" id="it3-freelance-expenses" step="any" min="0" value="0" />
<label>Billable hours per month</label>
<input class="it3-field" type="number" id="it3-freelance-hours" step="any" min="0" value="100" />
<label>Platform fee</label>
<select class="it3-field" id="it3-freelance-fee-preset">
<option value="0">No platform (0%)</option>
<option value="10">Upwork (10%)</option>
<option value="20">Fiverr (20%)</option>
<option value="custom">Custom rate</option>
</select>
<div id="it3-freelance-custom-wrap" style="display:none;">
<label>Custom fee (%)</label>
<input class="it3-field" type="number" id="it3-freelance-fee-custom" step="any" min="0" />
</div>
<label>Currency</label>
<select class="it3-field" id="it3-freelance-currency">
<option value="USD">USD ($)</option>
<option value="PKR">PKR (Rs)</option>
<option value="GBP">GBP (£)</option>
<option value="EUR">EUR (€)</option>
<option value="INR">INR (₹)</option>
<option value="AED">AED</option>
</select>
<button type="button" class="it3-btn" id="it3-freelance-calculate">Calculate</button>
<div class="it3-error" id="it3-freelance-error" style="display:none;"></div>
<div class="it3-result" id="it3-freelance-result" style="display:none;">
<p>Gross monthly needed: <strong id="it3-freelance-gross"></strong></p>
<p>Minimum hourly rate: <strong id="it3-freelance-hourly"></strong></p>
<p>Day rate (8 hours): <strong id="it3-freelance-day"></strong></p>
</div>
</div>
<?php return ob_get_clean();
}

/* ==== 30. STOCK AVERAGE CALCULATOR ==== */
add_shortcode('stock_average_calculator', 'it3_stock_average_calculator');
function it3_stock_average_calculator() {
    ob_start();?>
<div class="it3-calc" data-calc="stock-average-calculator">
<h3 class="it3-calc-title">Stock Average Calculator</h3>
<label>Buy 1 — Shares</label>
<input class="it3-field" type="number" id="it3-stock-shares-1" step="any" min="0" />
<label>Buy 1 — Price per share</label>
<input class="it3-field" type="number" id="it3-stock-price-1" step="any" min="0" />
<label>Buy 2 — Shares</label>
<input class="it3-field" type="number" id="it3-stock-shares-2" step="any" min="0" />
<label>Buy 2 — Price per share</label>
<input class="it3-field" type="number" id="it3-stock-price-2" step="any" min="0" />
<label>Buy 3 — Shares</label>
<input class="it3-field" type="number" id="it3-stock-shares-3" step="any" min="0" />
<label>Buy 3 — Price per share</label>
<input class="it3-field" type="number" id="it3-stock-price-3" step="any" min="0" />
<label>Buy 4 — Shares</label>
<input class="it3-field" type="number" id="it3-stock-shares-4" step="any" min="0" />
<label>Buy 4 — Price per share</label>
<input class="it3-field" type="number" id="it3-stock-price-4" step="any" min="0" />
<label>Buy 5 — Shares</label>
<input class="it3-field" type="number" id="it3-stock-shares-5" step="any" min="0" />
<label>Buy 5 — Price per share</label>
<input class="it3-field" type="number" id="it3-stock-price-5" step="any" min="0" />
<label>Additional fees</label>
<input class="it3-field" type="number" id="it3-stock-fees" step="any" min="0" />
<label>Current price per share</label>
<input class="it3-field" type="number" id="it3-stock-current" step="any" min="0" />
<label>Target average price (optional)</label>
<input class="it3-field" type="number" id="it3-stock-target" step="any" min="0" />
<label>Currency</label>
<select class="it3-field" id="it3-stock-currency">
<option value="USD">USD ($)</option>
<option value="PKR">PKR (Rs)</option>
<option value="GBP">GBP (£)</option>
<option value="EUR">EUR (€)</option>
<option value="INR">INR (₹)</option>
<option value="AED">AED</option>
</select>
<button type="button" class="it3-btn" id="it3-stock-calculate">Calculate Average</button>
<div class="it3-error" id="it3-stock-error" style="display:none;"></div>
<div class="it3-result" id="it3-stock-result" style="display:none;">
<p>Total shares: <strong id="it3-stock-total-shares"></strong></p>
<p>Total invested: <strong id="it3-stock-total-invested"></strong></p>
<p>Average cost per share: <strong id="it3-stock-average"></strong></p>
<p>Current value: <strong id="it3-stock-current-value"></strong></p>
<p>Gain / Loss: <strong id="it3-stock-gain"></strong></p>
<p>Breakeven price: <strong id="it3-stock-breakeven"></strong></p>
<div id="it3-stock-target-row" style="display:none;">
<p>Shares to buy to reach your target: <strong id="it3-stock-target-shares"></strong></p>
<p id="it3-stock-target-note"></p>
</div>
</div>
</div>
<?php return ob_get_clean();
}

/* ==== 31. PASSWORD STRENGTH CHECKER ==== */
add_shortcode('password_strength_checker', 'it3_password_strength_checker');
function it3_password_strength_checker() { ob_start();?>
<div class="it3-calc" data-calc="password-strength-checker">
<h3 class="it3-calc-title">Password Strength Checker</h3>
<p class="it3-calc-note">This checker runs entirely in your browser. Nothing is sent anywhere - your password never leaves this page.</p>
<label>Password</label>
<div style="display:flex;gap:8px;"><input type="password" class="it3-field" id="it3-password-input" placeholder="Type a password" autocomplete="off"><button type="button" class="it3-btn" id="it3-password-toggle">Show</button></div>
<p>Strength: <strong id="it3-password-label">-</strong></p>
<div style="height:10px;background:#eee;border-radius:5px;overflow:hidden;"><div id="it3-password-meter-fill" style="height:100%;width:0%;background:#e53935;"></div></div>
<p>Entropy: <strong id="it3-password-entropy">0 bits</strong></p>
<p>Estimated crack time: <strong id="it3-password-crack">-</strong></p>
<p class="it3-calc-note">Crack-time estimates assume an attacker trying about 10 billion guesses per second offline.</p>
<ul id="it3-password-checklist">
<li id="it3-password-check-length">x At least 12 characters</li>
<li id="it3-password-check-lowerupper">x Lowercase and uppercase letters</li>
<li id="it3-password-check-number">x A number</li>
<li id="it3-password-check-symbol">x A symbol</li>
<li id="it3-password-check-repeat">x No repeated characters in a row</li>
<li id="it3-password-check-common">x Not a common password</li>
</ul>
</div>
<?php return ob_get_clean();}

/* ==== 32. IBAN VALIDATOR ==== */
add_shortcode('iban_validator', 'it3_iban_validator');
function it3_iban_validator() { ob_start();?>
<div class="it3-calc" data-calc="iban-validator">
<h3 class="it3-calc-title">IBAN Validator</h3>
<p class="it3-calc-note">This validator runs entirely in your browser. Nothing is sent anywhere - your IBAN never leaves this page.</p>
<p class="it3-calc-note"><strong>What "valid" means:</strong> a valid result means the IBAN is well-formed - the country code is recognised, the length is correct for that country, the structure is correct, and the MOD-97 check digit gives a remainder of 1. It does <strong>not</strong> mean the account exists or is active.</p>
<label>IBAN</label>
<input type="text" class="it3-field" id="it3-iban-input" placeholder="e.g. GB29 NWBK 6016 1331 9268 19" autocomplete="off">
<button type="button" class="it3-btn" id="it3-iban-validate">Validate</button>
<div id="it3-iban-result" style="display:none;margin-top:12px;">
<p>Verdict: <strong id="it3-iban-verdict">-</strong></p>
<p id="it3-iban-reason"></p>
<p>Country: <strong id="it3-iban-country">-</strong></p>
<p>Country code: <strong id="it3-iban-code">-</strong></p>
<p>Expected length: <strong id="it3-iban-expected">-</strong></p>
<p>Entered length: <strong id="it3-iban-entered">-</strong></p>
<p>BBAN: <strong id="it3-iban-bban">-</strong></p>
<p>MOD-97 result: <strong id="it3-iban-mod97">-</strong></p>
</div>
<p class="it3-calc-note" id="it3-iban-error" style="display:none;color:#e53935;"></p>
</div>
<?php return ob_get_clean();}




/* ==== 33. INVOICE GENERATOR ==== */
add_shortcode('invoice_generator', 'it3_invoice_generator');
function it3_invoice_generator(){ ob_start(); ?>
<div class="it3-calc" data-calc="invoice-generator">
<h3 class="it3-calc-title">Invoice Generator</h3>
<p class="it3-calc-note">Create a professional invoice in your browser. Nothing is sent anywhere - your details never leave this page.</p>
<style>.it3-invoice-table{width:100%;border-collapse:collapse;margin-top:12px;}.it3-invoice-table th,.it3-invoice-table td{border:1px solid #ddd;padding:8px;text-align:left;}.it3-invoice-table th{background:#f5f5f5;}.it3-invoice-totals{margin-top:12px;text-align:right;}#it3-invoice-preview{border:1px solid #ddd;padding:20px;margin-top:16px;background:#fff;}@media print{body *{visibility:hidden !important;}#it3-invoice-preview,#it3-invoice-preview *{visibility:visible !important;}#it3-invoice-preview{position:absolute;left:0;top:0;width:100%;border:none;}}</style>
<label>Your business name</label>
<input type="text" class="it3-field" id="it3-invoice-business" placeholder="e.g. Acme Design Studio">
<label>Business address / email (optional)</label>
<textarea class="it3-field" id="it3-invoice-business-info" rows="2" placeholder="e.g. 123 Main Street, Karachi - hello@example.com"></textarea>
<label>Client name</label>
<input type="text" class="it3-field" id="it3-invoice-client" placeholder="e.g. ABC Traders">
<label>Client address (optional)</label>
<textarea class="it3-field" id="it3-invoice-client-info" rows="2" placeholder="e.g. 45 Market Road, Lahore"></textarea>
<label>Invoice number</label>
<input type="text" class="it3-field" id="it3-invoice-number" value="INV-001">
<label>Invoice date</label>
<input type="date" class="it3-field" id="it3-invoice-date">
<label>Due date (optional)</label>
<input type="date" class="it3-field" id="it3-invoice-due">
<label>Line items</label>
<div style="display:flex;gap:6px;margin-bottom:6px;"><input type="text" class="it3-field" id="it3-invoice-desc-1" placeholder="Description" style="flex:2;"><input type="number" class="it3-field" id="it3-invoice-qty-1" placeholder="Qty" min="0" step="any" style="flex:1;"><input type="number" class="it3-field" id="it3-invoice-price-1" placeholder="Unit price" min="0" step="any" style="flex:1;"><span class="it3-invoice-line" id="it3-invoice-line-1" style="flex:1;align-self:center;"></span></div>
<div style="display:flex;gap:6px;margin-bottom:6px;"><input type="text" class="it3-field" id="it3-invoice-desc-2" placeholder="Description" style="flex:2;"><input type="number" class="it3-field" id="it3-invoice-qty-2" placeholder="Qty" min="0" step="any" style="flex:1;"><input type="number" class="it3-field" id="it3-invoice-price-2" placeholder="Unit price" min="0" step="any" style="flex:1;"><span class="it3-invoice-line" id="it3-invoice-line-2" style="flex:1;align-self:center;"></span></div>
<div style="display:flex;gap:6px;margin-bottom:6px;"><input type="text" class="it3-field" id="it3-invoice-desc-3" placeholder="Description" style="flex:2;"><input type="number" class="it3-field" id="it3-invoice-qty-3" placeholder="Qty" min="0" step="any" style="flex:1;"><input type="number" class="it3-field" id="it3-invoice-price-3" placeholder="Unit price" min="0" step="any" style="flex:1;"><span class="it3-invoice-line" id="it3-invoice-line-3" style="flex:1;align-self:center;"></span></div>
<div style="display:flex;gap:6px;margin-bottom:6px;"><input type="text" class="it3-field" id="it3-invoice-desc-4" placeholder="Description" style="flex:2;"><input type="number" class="it3-field" id="it3-invoice-qty-4" placeholder="Qty" min="0" step="any" style="flex:1;"><input type="number" class="it3-field" id="it3-invoice-price-4" placeholder="Unit price" min="0" step="any" style="flex:1;"><span class="it3-invoice-line" id="it3-invoice-line-4" style="flex:1;align-self:center;"></span></div>
<div style="display:flex;gap:6px;margin-bottom:6px;"><input type="text" class="it3-field" id="it3-invoice-desc-5" placeholder="Description" style="flex:2;"><input type="number" class="it3-field" id="it3-invoice-qty-5" placeholder="Qty" min="0" step="any" style="flex:1;"><input type="number" class="it3-field" id="it3-invoice-price-5" placeholder="Unit price" min="0" step="any" style="flex:1;"><span class="it3-invoice-line" id="it3-invoice-line-5" style="flex:1;align-self:center;"></span></div>
<label>Discount %</label>
<input type="number" class="it3-field" id="it3-invoice-discount" value="0" min="0" max="100" step="any">
<label>Tax rate %</label>
<input type="number" class="it3-field" id="it3-invoice-tax" value="0" min="0" step="any">
<label>Currency</label>
<select class="it3-field" id="it3-invoice-currency"><option value="USD" selected>USD ($)</option><option value="PKR">PKR (Rs)</option><option value="GBP">GBP (&pound;)</option><option value="EUR">EUR (&euro;)</option><option value="INR">INR (&#8377;)</option><option value="AED">AED</option></select>
<button class="it3-btn" id="it3-invoice-generate">Generate Invoice</button>
<button class="it3-btn" id="it3-invoice-print" style="margin-left:8px;">Print / Save as PDF</button>
<p class="it3-calc-note" id="it3-invoice-error" style="display:none;color:#e53935;"></p>
<div id="it3-invoice-preview" style="display:none;">
<h4 style="margin:0;">INVOICE</h4>
<p><strong id="it3-invoice-p-business">-</strong><br><span id="it3-invoice-p-business-info"></span></p>
<p><strong>Billed to:</strong> <span id="it3-invoice-p-client">-</span><br><span id="it3-invoice-p-client-info"></span></p>
<p>Invoice number: <strong id="it3-invoice-p-number">-</strong><br>Invoice date: <strong id="it3-invoice-p-date">-</strong><br>Due date: <strong id="it3-invoice-p-due">-</strong></p>
<table class="it3-invoice-table"><thead><tr><th>Description</th><th>Quantity</th><th>Unit price</th><th>Line total</th></tr></thead><tbody id="it3-invoice-p-items"></tbody></table>
<div class="it3-invoice-totals"><p>Subtotal: <strong id="it3-invoice-p-subtotal">-</strong></p><p>Discount: <strong id="it3-invoice-p-discount">-</strong></p><p>Taxable amount: <strong id="it3-invoice-p-taxable">-</strong></p><p>Tax: <strong id="it3-invoice-p-tax">-</strong></p><p>Grand total: <strong id="it3-invoice-p-total">-</strong></p></div>
</div>
<p class="it3-calc-note">Print / Save as PDF uses your browser's print dialog and prints only the invoice preview above. No sign-up and no watermark.</p>
</div>
<?php return ob_get_clean();}

/* ==== 34. IMAGE RESIZER ==== */
add_shortcode('image_resizer','it3_image_resizer');
function it3_image_resizer(){ ob_start();?>
<div class="it3-calc" data-calc="image-resizer">
<h3 class="it3-calc-title">Image Resizer</h3>
<p class="it3-calc-note">Your image never leaves your browser. Resizing happens entirely on this page with JavaScript - no upload to any server, no sign-up.</p>
<style>.it3-rs-row{display:flex;gap:12px;}.it3-rs-row>div{flex:1;}.it3-rs-row .it3-field{width:100%;}#it3-rs-preview{max-width:100%;height:auto;border:1px solid #dfe3e8;border-radius:6px;}</style>
<label>Choose an image (JPG, PNG or WebP)</label>
<input type="file" id="it3-rs-file" accept="image/jpeg,image/png,image/webp">
<p class="it3-calc-note" id="it3-rs-original" style="display:none;"></p>
<label>Resize mode</label>
<select class="it3-field" id="it3-rs-preset">
<option value="custom">Custom - exact dimensions</option>
<option value="413x531">Passport photo - 35 x 45 mm (413 x 531 px at 300 dpi)</option>
<option value="1080x1080">Instagram square - 1080 x 1080</option>
<option value="1080x1350">Instagram portrait - 1080 x 1350</option>
<option value="820x312">Facebook cover - 820 x 312</option>
<option value="1280x720">YouTube thumbnail - 1280 x 720</option>
<option value="1500x500">X header - 1500 x 500</option>
</select>
<div class="it3-rs-row">
<div><label>Width (px)</label><input type="number" class="it3-field" id="it3-rs-width" min="1" max="10000" placeholder="e.g. 600"></div>
<div><label>Height (px)</label><input type="number" class="it3-field" id="it3-rs-height" min="1" max="10000" placeholder="e.g. 400"></div>
</div>
<label><input type="checkbox" id="it3-rs-ratio" checked> Keep aspect ratio</label>
<label>Output format</label>
<select class="it3-field" id="it3-rs-format">
<option value="image/jpeg">JPEG</option>
<option value="image/webp">WebP</option>
<option value="image/png">PNG</option>
</select>
<label>Quality: <span id="it3-rs-quality-value">80</span>%</label>
<input type="range" id="it3-rs-quality" min="10" max="100" value="80" style="width:100%;">
<p class="it3-calc-note">PNG is lossless: the quality slider has no effect on PNG output. It applies to JPEG and WebP only.</p>
<label>Target file size (KB) - optional</label>
<input type="number" class="it3-field" id="it3-rs-target" min="1" placeholder="e.g. 50">
<p class="it3-calc-note">Leave blank for no size limit. When set, quality is lowered in steps until the output is under the target, never below quality 10. It does not apply to PNG, because PNG is lossless.</p>
<button class="it3-btn" id="it3-rs-resize">Resize image</button>
<p class="it3-calc-note" id="it3-rs-error" style="display:none;color:#e53935;"></p>
<div id="it3-rs-output" style="display:none;margin-top:14px;">
<p class="it3-calc-note" id="it3-rs-result"></p>
<img id="it3-rs-preview" alt="Resized image preview" style="display:none;">
<p><a class="it3-btn" id="it3-rs-download" href="#" download="ilovetool3-resized-image.jpg" style="display:none;">Download resized image</a></p>
</div>
</div>
<?php return ob_get_clean();}
/* ==== 35. PAINT & TILE CALCULATOR ==== */
add_shortcode('paint_tile_calculator', 'it3_paint_tile_calculator');
function it3_paint_tile_calculator(){ ob_start();?>
<div class="it3-calc" data-calc="paint-tile-calculator">
<h3 class="it3-calc-title"><span class="it3-emoji">&#127912;</span> Paint &amp; Tile Calculator</h3>
<p class="it3-calc-note">Work out how much paint or how many tiles you need for a room, in metric or US units. Everything runs in your browser - nothing is uploaded.</p>
<div class="it3-field"><label>Mode</label><select class="it3-field" id="it3-pt-mode"><option value="paint">Paint</option><option value="tile">Tile</option></select></div>
<div class="it3-field"><label>Unit system</label><select class="it3-field" id="it3-pt-units"><option value="metric">Metric (metres, cm, litres)</option><option value="us">US (feet, inches, gallons)</option></select></div>
<div id="it3-pt-paint">
<div class="it3-field"><label>Total area to paint</label><input type="number" class="it3-field" id="it3-pt-area" min="0" step="any" placeholder="e.g. 50"></div>
<div class="it3-field"><label>Area unit</label><select class="it3-field" id="it3-pt-area-unit"><option value="m2">m&sup2;</option><option value="ft2">ft&sup2;</option></select></div>
<div class="it3-field"><label>Number of coats</label><select class="it3-field" id="it3-pt-coats"><option value="1">1</option><option value="2" selected>2</option><option value="3">3</option></select></div>
<div class="it3-field"><label id="it3-pt-coverage-label">Coverage (m&sup2; per litre)</label><input type="number" class="it3-field" id="it3-pt-coverage" min="0" step="any" value="10"></div>
<div class="it3-field"><label>Tin size</label><select class="it3-field" id="it3-pt-tin"><option value="1">1 L</option><option value="2.5">2.5 L</option><option value="5" selected>5 L</option><option value="10">10 L</option></select></div>
<div class="it3-field"><label>Price per tin (optional)</label><input type="number" class="it3-field" id="it3-pt-paint-price" min="0" step="any" placeholder="Leave blank if you do not know the price"></div>
<div class="it3-field" id="it3-pt-paint-currency-wrap" style="display:none;"><label>Currency</label><select class="it3-field" id="it3-pt-paint-currency"><option value="USD" selected>$ - USD</option><option value="PKR">Rs - PKR</option><option value="GBP">&pound; - GBP</option><option value="EUR">&euro; - EUR</option><option value="INR">&#8377; - INR</option><option value="AED">AED - AED</option></select></div>
<p class="it3-calc-note">Coverage varies by paint brand and surface - the defaults are typical values, and you should check your paint tin.</p>
</div>
<div id="it3-pt-tile" style="display:none;">
<div class="it3-field"><label>Floor length</label><input type="number" class="it3-field" id="it3-pt-length" min="0" step="any" placeholder="e.g. 4"></div>
<div class="it3-field"><label>Floor width</label><input type="number" class="it3-field" id="it3-pt-width" min="0" step="any" placeholder="e.g. 5"></div>
<p class="it3-calc-note">Floor area: <strong id="it3-pt-floor-area">-</strong></p>
<div class="it3-field"><label>Tile size</label><select class="it3-field" id="it3-pt-tile-size"><option value="30x30">30 &times; 30 cm</option><option value="60x60" selected>60 &times; 60 cm</option><option value="30x60">30 &times; 60 cm</option><option value="45x45">45 &times; 45 cm</option><option value="custom">Custom</option></select></div>
<div id="it3-pt-custom-tile" style="display:none;">
<div class="it3-field"><label>Custom tile width (<span id="it3-pt-custom-unit">cm</span>)</label><input type="number" class="it3-field" id="it3-pt-custom-w" min="0" step="any" placeholder="e.g. 40"></div>
<div class="it3-field"><label>Custom tile height (<span id="it3-pt-custom-unit2">cm</span>)</label><input type="number" class="it3-field" id="it3-pt-custom-h" min="0" step="any" placeholder="e.g. 40"></div>
</div>
<div class="it3-field"><label>Layout</label><select class="it3-field" id="it3-pt-layout"><option value="10" selected>Straight - 10% waste</option><option value="15">Diagonal - 15% waste</option></select></div>
<div class="it3-field"><label>Tiles per box</label><input type="number" class="it3-field" id="it3-pt-per-box" min="1" step="1" value="4"></div>
<div class="it3-field"><label>Price per box (optional)</label><input type="number" class="it3-field" id="it3-pt-box-price" min="0" step="any" placeholder="Leave blank if you do not know the price"></div>
<div class="it3-field" id="it3-pt-tile-currency-wrap" style="display:none;"><label>Currency</label><select class="it3-field" id="it3-pt-tile-currency"><option value="USD" selected>$ - USD</option><option value="PKR">Rs - PKR</option><option value="GBP">&pound; - GBP</option><option value="EUR">&euro; - EUR</option><option value="INR">&#8377; - INR</option><option value="AED">AED - AED</option></select></div>
</div>
<button class="it3-btn" id="it3-pt-calc">Calculate</button>
<p class="it3-calc-note" id="it3-pt-error" style="display:none;color:#e53935;"></p>
<div id="it3-pt-output" style="display:none;margin-top:14px;">
<div id="it3-pt-paint-results"><p>Paint needed: <strong id="it3-pt-paint-needed">-</strong></p><p>Tins to buy: <strong id="it3-pt-tins">-</strong></p><p id="it3-pt-paint-cost-row" style="display:none;">Estimated cost: <strong id="it3-pt-paint-cost">-</strong></p></div>
<div id="it3-pt-tile-results" style="display:none;"><p>Floor area: <strong id="it3-pt-tile-floor-area">-</strong></p><p>Area including waste: <strong id="it3-pt-waste-area">-</strong></p><p>Tiles needed: <strong id="it3-pt-tiles">-</strong></p><p>Boxes to buy: <strong id="it3-pt-boxes">-</strong></p><p id="it3-pt-tile-cost-row" style="display:none;">Estimated cost: <strong id="it3-pt-tile-cost">-</strong></p></div>
</div>
</div>
<?php return ob_get_clean();}

/* ==== 36. PROFIT MARGIN & MARKUP CALCULATOR ==== */
add_shortcode('profit_margin_calculator','it3_profit_margin_calculator');
function it3_profit_margin_calculator(){ob_start();?>
<div class="it3-calc" data-calc="profit-margin-calculator">
<h3 class="it3-calc-title"><span class="it3-emoji">&#128176;</span> Profit Margin &amp; Markup Calculator</h3>
<p class="it3-calc-note">Work out your profit, profit margin and markup from cost and selling price, or find the selling price you need for a target margin or markup. Everything runs in your browser - nothing is uploaded.</p>
<div class="it3-field"><label>Mode</label><select class="it3-field" id="it3-pm-mode"><option value="price" selected>Cost + selling price</option><option value="margin">Cost + target margin</option><option value="markup">Cost + target markup</option></select></div>
<div class="it3-field"><label>Currency</label><select class="it3-field" id="it3-pm-currency"><option value="USD" selected>$ - USD</option><option value="PKR">Rs - PKR</option><option value="GBP">&pound; - GBP</option><option value="EUR">&euro; - EUR</option><option value="INR">&#8377; - INR</option><option value="AED">AED - AED</option></select></div>
<div class="it3-field"><label>Cost</label><input type="number" class="it3-field" id="it3-pm-cost" min="0" step="any" placeholder="e.g. 40"></div>
<div class="it3-field" id="it3-pm-price-wrap"><label>Selling price</label><input type="number" class="it3-field" id="it3-pm-price" min="0" step="any" placeholder="e.g. 60"></div>
<div class="it3-field" id="it3-pm-margin-wrap" style="display:none;"><label>Target margin %</label><input type="number" class="it3-field" id="it3-pm-margin" min="0" step="any" placeholder="e.g. 40"></div>
<div class="it3-field" id="it3-pm-markup-wrap" style="display:none;"><label>Target markup %</label><input type="number" class="it3-field" id="it3-pm-markup" min="0" step="any" placeholder="e.g. 25"></div>
<button class="it3-btn" id="it3-pm-calc">Calculate</button>
<p class="it3-calc-note" id="it3-pm-error" style="display:none;color:#e53935;"></p>
<div id="it3-pm-output" style="display:none;margin-top:14px;">
<div id="it3-pm-price-results"><p>Profit: <strong id="it3-pm-profit">-</strong></p><p>Profit margin: <strong id="it3-pm-margin-out">-</strong></p><p>Markup: <strong id="it3-pm-markup-out">-</strong></p></div>
<div id="it3-pm-target-results" style="display:none;"><p>Selling price: <strong id="it3-pm-price-out">-</strong></p><p>Profit: <strong id="it3-pm-profit2">-</strong></p></div>
</div>
<p class="it3-calc-note">Margin can never reach 100%: margin is profit divided by the selling price, so a 100% margin would mean the cost was zero.</p>
</div>
<?php return ob_get_clean();}