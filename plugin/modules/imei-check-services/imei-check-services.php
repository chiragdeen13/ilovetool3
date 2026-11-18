<?php
/*
Plugin Name: IMEI Check Services
Plugin URI: https://ilovetool3.com
Description: Master Tools Plugin - IMEI Check + IMEI Validator + IMEI Generator + Age, BMI, Percentage, GPA Calculators. Includes MASTER DASHBOARD for all site plugins.
Version: 3.0.0
Author: iLoveTool3
Author URI: https://ilovetool3.com
License: GPL v2 or later
Text Domain: imei-check
*/

if (!defined('ABSPATH')) exit;

define('IMEI_VERSION', '3.0.1'); /* bumped: script.js had a strict-mode SyntaxError (octal literal 01) — bump forces browsers/CDN to drop the cached broken file */
define('IMEI_URL', plugin_dir_url(__FILE__));
define('IMEI_PATH', plugin_dir_path(__FILE__));

/* ================= ACTIVATION ================= */
register_activation_hook(__FILE__, 'imei_activate');
function imei_activate() {
    flush_rewrite_rules();
}

/* ================= CPT ================= */
add_action('init', 'imei_post_types');
function imei_post_types() {
    register_post_type('imei_service', array(
        'labels' => array('name' => 'IMEI Services', 'singular_name' => 'Service', 'add_new_item' => 'Add New Service', 'edit_item' => 'Edit Service', 'all_items' => 'All Services'),
        'public' => true, 'has_archive' => true, 'menu_icon' => 'dashicons-smartphone',
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
    ));
}

/* ================= SCRIPTS ================= */
add_action('wp_enqueue_scripts', 'imei_scripts');
function imei_scripts() {
    wp_enqueue_style('imei-style', IMEI_URL . 'assets/css/style.css', array(), IMEI_VERSION);
    wp_enqueue_script('imei-script', IMEI_URL . 'assets/js/script.js', array('jquery'), IMEI_VERSION, true);
    wp_localize_script('imei-script', 'imeiAjax', array(
        'url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('imei_nonce')
    ));
}

/* ============================================================
   MASTER ADMIN DASHBOARD — saare plugins ek jagah
   ============================================================ */
add_action('admin_menu', 'imei_admin_menu', 9); // FIX: priority 9 so this parent menu is registered before the other modules' submenus attach to it (add_menu_page must run first)
function imei_admin_menu() {
    /* FIX (consolidation): this used to be its own standalone top-level
       menu, and MultyTools, IT3 Tools, MT PDF Tools, Tool Articles and
       Ad Manager each had THEIR OWN separate top-level/settings menu
       too — 5-6 different places in the sidebar for one plugin suite.
       This is now the single parent menu ("iLoveTool3 Suite") and every
       other module attaches to it as a submenu instead (see each
       module's admin_menu function — they now call add_submenu_page()
       with 'imei-master' as the parent slug). */
    add_menu_page('iLoveTool3 Suite', 'iLoveTool3 Suite', 'manage_options', 'imei-master', 'imei_master_dashboard', 'dashicons-admin-tools', 25);
    add_submenu_page('imei-master', 'Master Dashboard', 'Master Dashboard', 'manage_options', 'imei-master', 'imei_master_dashboard');
    /* FIX: was mislabeled "AdSense Settings" — this page has nothing to
       do with Google AdSense, it's the IMEI lookup API configuration.
       That naming could easily lead to pasting an IMEI provider key
       into the wrong place, or vice versa. */
    add_submenu_page('imei-master', 'IMEI API Settings', 'IMEI API Settings', 'manage_options', 'imei-settings', 'imei_settings_page');
}

function imei_master_dashboard() {
    $created = isset($_GET['pages_created']) ? intval($_GET['pages_created']) : -1;

    /* Detect other plugins */
    $hasCalc   = function_exists('it3_loan_calculator');
    $hasMulti  = function_exists('mt_sc_case_converter');
    $hasPages  = function_exists('mtp_all_tools_shortcode');
    $hasArticles = function_exists('mta_append_article');

    $toolCount = 0;
    if (function_exists('mtp_get_tool_pages')) {
        $toolCount = count(mtp_get_tool_pages());
    }

    /* This plugin's tools. FIX: "IMEI Check" (real device lookup) needs
       a paid third-party API key that isn't set up — removed from the
       page-creation list per your decision. IMEI Validator and IMEI
       Number Generator are pure client-side tools (no API needed at
       all) and keep working exactly as before. */
    $myTools = array(
        'imei-validator'       => array('IMEI Validator', '[imei_validator]'),
        'imei-generator'       => array('IMEI Number Generator', '[imei_generator]'),
        'age-calculator'       => array('Age Calculator', '[age_calculator]'),
        'bmi-calculator'       => array('BMI Calculator', '[bmi_calculator]'),
        'percentage-calculator'=> array('Percentage Calculator', '[percentage_calculator]'),
        'gpa-calculator'       => array('GPA Calculator', '[gpa_calculator]'),
    );
    ?>
    <div class="wrap">
        <h1>&#127760; Master Dashboard — iLoveTool3</h1>

        <?php if ($created > 0) : ?>
            <div class="notice notice-success"><p><strong><?php echo $created; ?> tool pages created!</strong> Homepage pe automatic aa gaye honge.</p></div>
        <?php elseif ($created === 0) : ?>
            <div class="notice notice-warning"><p>Saare pages pehle se exist karte hain.</p></div>
        <?php endif; ?>

        <!-- STATS ROW -->
        <div style="display:flex;gap:15px;flex-wrap:wrap;margin:20px 0;">
            <div style="background:linear-gradient(135deg,#4f46e5,#6366f1);color:#fff;border-radius:12px;padding:20px 30px;flex:1;min-width:180px;">
                <strong style="font-size:32px;"><?php echo $toolCount; ?>+</strong><br/>Total Tool Pages on Site
            </div>
            <div style="background:#fff;border:1px solid #ccd0d4;border-radius:12px;padding:20px 30px;flex:1;min-width:180px;">
                <strong style="font-size:26px;color:#059669;">7</strong> Tools<br/>
                <small style="color:#64748b;">in this plugin</small>
            </div>
            <div style="background:#fff;border:1px solid #ccd0d4;border-radius:12px;padding:20px 30px;flex:1;min-width:180px;">
                <strong style="font-size:26px;color:<?php echo ($hasCalc && $hasMulti && $hasPages && $hasArticles) ? '#059669' : '#d97706'; ?>;"><?php echo ($hasCalc ? 1 : 0) + ($hasMulti ? 1 : 0) + ($hasPages ? 1 : 0) + ($hasArticles ? 1 : 0) + 1; ?>/5</strong> Plugins Active<br/>
                <small style="color:#64748b;">including this one</small>
            </div>
        </div>

        <!-- THIS PLUGIN TOOLS -->
        <div style="background:#fff;border:1px solid #ccd0d4;padding:20px;border-radius:8px;">
            <h2 style="margin-top:0;">&#128241; This Plugin's Tools (7)</h2>
            <p>Pehle "Create Tool Pages" dabao — phir ye sab homepage pe automatic dikhenge!</p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-bottom:15px;">
                <input type="hidden" name="action" value="imei_create_pages" />
                <?php wp_nonce_field('imei_create_pages'); ?>
                <button type="submit" class="button button-primary button-hero">&#128193; Create Tool Pages</button>
            </form>
            <table class="widefat striped">
                <thead><tr><th>Tool</th><th>Shortcode</th><th>Page</th></tr></thead>
                <tbody>
                <?php foreach ($myTools as $slug => $data) :
                    $exists = get_page_by_path($slug); ?>
                    <tr>
                        <td><strong><?php echo esc_html($data[0]); ?></strong></td>
                        <td><code><?php echo esc_html($data[1]); ?></code></td>
                        <td>
                            <?php if ($exists) : ?>
                                <span style="color:green;">&#9989;</span> <a href="<?php echo esc_url(get_permalink($exists)); ?>" target="_blank">view</a>
                            <?php else : ?>
                                <span style="color:#999;">Not created</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- OTHER PLUGINS STATUS -->
        <h2 style="margin-top:25px;">&#128230; Other Plugins Status</h2>
        <table class="widefat striped">
            <thead><tr><th>Plugin</th><th>Status</th><th>Dashboard</th></tr></thead>
            <tbody>
            <tr>
                <td><strong>&#129514; MultyTools</strong> (17 tools)</td>
                <td><?php echo $hasMulti ? '<span style="color:green;">&#9989; Active</span>' : '<span style="color:red;">Not active</span>'; ?></td>
                <td><?php echo $hasMulti ? '<a href="' . admin_url('admin.php?page=mt-tools') . '" class="button button-small">Open</a>' : ''; ?></td>
            </tr>
            <tr>
                <td><strong>&#128202; iLoveTool3 Calculators</strong> (18 tools)</td>
                <td><?php echo $hasCalc ? '<span style="color:green;">&#9989; Active</span>' : '<span style="color:red;">Not active</span>'; ?></td>
                <td><?php echo $hasCalc ? '<a href="' . admin_url('admin.php?page=it3-tools') . '" class="button button-small">Open</a>' : ''; ?></td>
            </tr>
            <tr>
                <td><strong>&#128196; MT Essential Pages</strong> (Homepage + Pages)</td>
                <td><?php echo $hasPages ? '<span style="color:green;">&#9989; Active</span>' : '<span style="color:red;">Not active</span>'; ?></td>
                <td><?php echo $hasPages ? '<a href="' . admin_url('admin.php?page=mta-info') . '" class="button button-small">Open</a>' : ''; ?></td>
            </tr>
            <tr>
                <td><strong>&#128221; MT Tool Articles</strong> (Auto SEO Articles)</td>
                <td><?php echo $hasArticles ? '<span style="color:green;">&#9989; Active</span>' : '<span style="color:red;">Not active</span>'; ?></td>
                <td><?php echo $hasArticles ? '<em>Automatic — no setup needed</em>' : ''; ?></td>
            </tr>
            </tbody>
        </table>

        <!-- QUICK LINKS -->
        <div style="background:#fff8e5;border:1px solid #ffd97d;padding:15px 20px;border-radius:6px;margin-top:20px;">
            <h3 style="margin-top:0;">&#9889; Quick Actions</h3>
            <a href="<?php echo esc_url(admin_url('admin.php?page=imei-settings')); ?>" class="button">⚙ IMEI API Settings</a>
            <a href="<?php echo esc_url(home_url('/')); ?>" target="_blank" class="button">🏠 View Site Homepage</a>
            <a href="<?php echo esc_url(admin_url('edit.php?post_type=imei_service')); ?>" class="button">📱 Manage IMEI Services</a>
        </div>
    </div>
    <?php
}

/* Create this plugin's tool pages handler */
add_action('admin_post_imei_create_pages', 'imei_create_pages_handler');
function imei_create_pages_handler() {
    if (!current_user_can('manage_options')) { wp_die('Permission denied.'); }
    check_admin_referer('imei_create_pages');

    $map = array(
        'imei-validator'        => array('IMEI Validator', '[imei_validator]'),
        'imei-generator'        => array('IMEI Number Generator', '[imei_generator]'),
        'age-calculator'        => array('Age Calculator', '[age_calculator]'),
        'bmi-calculator'        => array('BMI Calculator', '[bmi_calculator]'),
        'percentage-calculator' => array('Percentage Calculator', '[percentage_calculator]'),
        'gpa-calculator'        => array('GPA Calculator', '[gpa_calculator]'),
    );

    $count = 0;
    foreach ($map as $slug => $data) {
        if (!get_page_by_path($slug)) {
            $r = wp_insert_post(array(
                'post_title' => $data[0], 'post_name' => $slug,
                'post_content' => $data[1], 'post_status' => 'publish',
                'post_type' => 'page', 'comment_status' => 'closed',
            ));
            if ($r) { $count++; }
        }
    }

    wp_redirect(admin_url('admin.php?page=imei-master&pages_created=' . $count));
    exit;
}

/* API settings page. FIX: this used to also have a "Google AdSense
   Publisher ID" field/tab that looked functional but did nothing —
   the value was saved and never read anywhere to output a script tag.
   That's confusing (looks configured, isn't). Removed it and pointed
   to the real one (Settings → Ad Manager) instead. */
function imei_settings_page() {
    ?>
    <div class="wrap">
        <h1>&#9881; IMEI API Settings</h1>

        <div style="background:#eef2fd;border:1px solid #c7d2fe;padding:14px 18px;border-radius:8px;margin:14px 0;">
            Looking for AdSense / Adsterra ad settings? Those moved to <a href="<?php echo esc_url( admin_url( 'options-general.php?page=ilt3-ad-manager' ) ); ?>"><strong>Settings → Ad Manager</strong></a> — one place for the whole site's ads.
        </div>

        <div id="api">
            <form method="post" action="options.php">
                <?php settings_fields('imei_opts'); ?>
                <table class="form-table">
                    <tr>
                        <th>IMEI API URL</th>
                        <td>
                            <input type="url" name="imei_api_url" value="<?php echo esc_attr(get_option('imei_api_url', '')); ?>" class="large-text" placeholder="https://api.provider.com/v1/imei/check" />
                            <p class="description"><strong>Khali chhodo = Demo Mode</strong> (sample data dikhega, clearly labeled as demo to visitors). Real provider (imeicheck.com, imei.info etc.) ka endpoint yahan dalo.</p>
                        </td>
                    </tr>
                    <tr>
                        <th>IMEI API Key</th>
                        <td>
                            <input type="text" name="imei_api_key" value="<?php echo esc_attr(get_option('imei_api_key', '')); ?>" class="regular-text" placeholder="sk_live_xxxxxxxxxxxx" />
                            <p class="description">Aapke API provider se mila secret key.</p>
                        </td>
                    </tr>
                    <tr>
                        <th>Auth Header Name</th>
                        <td>
                            <input type="text" name="imei_api_auth_header" value="<?php echo esc_attr(get_option('imei_api_auth_header', 'Authorization')); ?>" />
                            <p class="description">Most providers use "Authorization". Kuch "X-API-Key" use karte hain.</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save API Settings'); ?>
            </form>

            <div style="background:#fff;border:1px solid #ccd0d4;padding:16px 20px;border-radius:6px;margin-top:15px;">
                <strong>&#128161; Kaam kaise karta hai:</strong>
                <p style="margin:8px 0;">Agar API URL + Key dono set hain to plugin har check ko aapke provider ko bhejega (JSON POST: <code>{service_type, imei}</code>) aur response ka <code>data</code> object dikhayega. Response format alag ho to <code>imei_ajax_handler</code> mein parse adjust karo. Agar request fail ho ya khali ho — Demo Mode (mock data, clearly labeled) chalega.</p>
                <strong>&#128273; Popular providers:</strong>
                <ul style="list-style:disc;margin-left:20px;">
                    <li><a href="https://imeicheck.com" target="_blank">imeicheck.com</a> — DHRU &amp; JSON APIs</li>
                    <li><a href="https://imei.info" target="_blank">imei.info</a> — API partners program</li>
                </ul>
            </div>
        </div>
    </div>
    <?php
}

add_action('admin_init', 'imei_register_settings');
function imei_register_settings() {
    register_setting('imei_opts', 'imei_api_url', 'esc_url_raw');
    register_setting('imei_opts', 'imei_api_key', 'sanitize_text_field');
    register_setting('imei_opts', 'imei_api_auth_header', 'sanitize_text_field');
}

/* ============================================================
   AJAX — IMEI CHECK
   ============================================================ */
add_action('wp_ajax_imei_check', 'imei_ajax_handler');
add_action('wp_ajax_nopriv_imei_check', 'imei_ajax_handler');
function imei_ajax_handler() {
    check_ajax_referer('imei_nonce', 'nonce');

    $imei = isset($_POST['imei']) ? preg_replace('/[^0-9]/', '', sanitize_text_field($_POST['imei'])) : '';
    $type = isset($_POST['service_type']) ? sanitize_text_field($_POST['service_type']) : 'apple_basic';

    if (strlen($imei) !== 15) {
        wp_send_json_error(array('message' => 'Please enter valid 15-digit IMEI number.'));
    }

    /* ---------- REAL API MODE (agar API URL + Key set hain) ---------- */
    $apiUrl = get_option('imei_api_url', '');
    $apiKey = get_option('imei_api_key', '');

    if ($apiUrl && $apiKey) {
        $authHeader = get_option('imei_api_auth_header', 'Authorization');
        $authValue  = ($authHeader === 'Authorization') ? 'Bearer ' . $apiKey : $apiKey;

        $response = wp_remote_post($apiUrl, array(
            'timeout' => 25,
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                $authHeader     => $authValue,
            ),
            'body' => wp_json_encode(array(
                'service_type' => $type,
                'device_id'    => $imei,
                'imei'         => $imei,
            )),
        ));

        if (!is_wp_error($response)) {
            $code = intval(wp_remote_retrieve_response_code($response));
            $body = json_decode(wp_remote_retrieve_body($response), true);

            if ($code >= 200 && $code < 300) {
                /* Provider formats differ: try common keys */
                $dataOut = null;
                foreach (array('data', 'result', 'response', 'properties', 'object') as $k) {
                    if (isset($body[$k]) && is_array($body[$k])) { $dataOut = $body[$k]; break; }
                }
                if ($dataOut === null && is_array($body)) {
                    /* Flat response: use as-is minus meta keys */
                    unset($body['status'], $body['success'], $body['message'], $body['error']);
                    if (!empty($body)) { $dataOut = $body; }
                }

                if (is_array($dataOut) && !empty($dataOut)) {
                    if (is_user_logged_in()) {
                        $uid = get_current_user_id();
                        $history = get_user_meta($uid, 'imei_history', true);
                        if (!is_array($history)) { $history = array(); }
                        array_unshift($history, array('imei' => $imei, 'type' => $type, 'date' => current_time('mysql')));
                        update_user_meta($uid, 'imei_history', array_slice($history, 0, 50));
                    }
                    wp_send_json_success(array(
                        'service_type' => $type,
                        'timestamp'    => current_time('mysql'),
                        'data'         => $dataOut,
                        'source'       => 'api',
                    ));
                }

                wp_send_json_error(array('message' => 'API response format unexpected. Check provider docs.'));
            } else {
                $errMsg = isset($body['message']) ? $body['message'] : ('API error HTTP ' . $code);
                /* API fail — demo mode fallback with notice */
                $data = imei_mock_data($imei, $type);
                $data['_notice'] = 'Live API unavailable (' . sanitize_text_field($errMsg) . ') — showing DEMO data.';
                wp_send_json_success(array('service_type' => $type, 'timestamp' => current_time('mysql'), 'data' => $data, 'source' => 'demo-fallback'));
            }
        }
        /* is_wp_error: fall through to demo */
    }

    /* ---------- DEMO MODE ---------- */
    $data = imei_mock_data($imei, $type);

    if (is_user_logged_in()) {
        $uid = get_current_user_id();
        $history = get_user_meta($uid, 'imei_history', true);
        if (!is_array($history)) { $history = array(); }
        array_unshift($history, array('imei' => $imei, 'type' => $type, 'date' => current_time('mysql')));
        update_user_meta($uid, 'imei_history', array_slice($history, 0, 50));
    }

    wp_send_json_success(array('service_type' => $type, 'timestamp' => current_time('mysql'), 'data' => $data, 'source' => 'demo'));
}

/* Mock data generator (Demo Mode) */
function imei_mock_data($imei, $type) {
    $responses = array(
        'apple_basic' => array('Model' => 'iPhone 14 Pro 256GB Space Black', 'Model Number' => 'A2894', 'Serial' => 'F2LXKU' . substr($imei, -5), 'iCloud Lock' => 'OFF', 'Blacklist' => 'Clean', 'Country' => 'United States', 'SIM-Lock' => 'Unlocked'),
        'apple_warranty' => array('Model' => 'iPhone 14 128GB Blue', 'Activation' => 'Activated', 'Coverage Start' => '2024-01-15', 'Warranty End' => '2025-01-14', 'AppleCare' => 'Yes'),
        'apple_carrier' => array('Device' => 'iPhone 14 Pro Max Deep Purple', 'Carrier' => 'UK Vodafone', 'Country' => 'United Kingdom', 'SIM-Lock' => 'Locked'),
        'icloud_check' => array('Model' => 'iPhone 13 128GB Midnight', 'iCloud Lock' => 'OFF', 'Status' => 'Clean', 'Find My iPhone' => 'Disabled'),
        'samsung' => array('Model' => 'Galaxy S24 Ultra SM-S928B', 'Warranty' => 'In Warranty', 'Production' => 'Korea SEC', 'Carrier' => 'Open'),
        'blacklist' => array(),
    );

    if ($type === 'blacklist') {
        $bl = (substr($imei, -1) % 3 == 0);
        $responses['blacklist'] = array('Model' => 'iPhone 14 (A2882)', 'Status' => $bl ? 'BLACKLISTED' : 'CLEAN', 'By' => $bl ? 'O2 UK Limited' : '-');
    }

    return isset($responses[$type]) ? $responses[$type] : $responses['apple_basic'];
}

/* ============================================================
   SHORTCODES
   ============================================================ */

/* ---------- 1. IMEI CHECK FORM ---------- */
/* DISABLED per your decision — no real IMEI lookup API key configured,
   so this only ever showed demo/sample data. IMEI Validator and IMEI
   Number Generator below are unaffected (they need no API at all).
   The imei_sc_form() function itself is left intact below in case you
   add a real API key later and want to re-enable this with:
   add_shortcode('imei_check_form', 'imei_sc_form'); */
// add_shortcode('imei_check_form', 'imei_sc_form');

/* FIX: redirect the old /imei-check/ URL (already indexed by Google,
   per Search Console) to a real, fully-working page instead of letting
   it 404 or sit there as a dead/demo page — this consolidates whatever
   ranking signal that URL already had onto a page that actually works. */
add_action('template_redirect', 'imei_redirect_disabled_check_page');
function imei_redirect_disabled_check_page() {
    if (is_page('imei-check')) {
        wp_redirect(home_url('/imei-validator/'), 301);
        exit;
    }
}

function imei_sc_form() {
    ob_start(); ?>
    <div id="imei-check-form" class="imei-wrap">
        <div class="imei-box">
            <div class="imei-header">
                <div class="imei-big-icon">&#128241;</div>
                <h2>IMEI Number Check</h2>
                <p>Enter your IMEI to get detailed device information</p>
            </div>
            <form id="imeiCheckForm" class="imei-form">
                <label class="imei-label">IMEI Number</label>
                <div class="imei-input-row">
                    <input type="text" id="imei_number" placeholder="Enter 15 digit IMEI" maxlength="15" inputmode="numeric" required />
                    <button type="submit" class="imei-btn">Check Now</button>
                </div>
                <small class="imei-hint">Dial *#06# to find your IMEI</small>
                <label class="imei-label" style="margin-top:16px;">Select Service:</label>
                <div class="imei-services">
                    <?php
                    $svcs = array('apple_basic' => array('Apple Basic Info', 'FREE'), 'apple_warranty' => array('Apple Warranty', 'FREE'), 'apple_carrier' => array('Apple Carrier', 'FREE'), 'icloud_check' => array('iCloud Check', 'FREE'), 'samsung' => array('Samsung Check', 'FREE'), 'blacklist' => array('Blacklist Check', 'FREE'));
                    foreach ($svcs as $k => $v) : ?>
                    <label class="imei-svc">
                        <input type="radio" name="service_type" value="<?php echo $k; ?>" <?php checked($k, 'apple_basic'); ?> />
                        <span><?php echo $v[0]; ?></span>
                        <span class="imei-price<?php echo $v[1] === 'FREE' ? ' free' : ''; ?>"><?php echo $v[1]; ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </form>
            <div id="imeiLoading" class="imei-loading" style="display:none;"><div class="imei-spinner"></div><p>Checking...</p></div>
            <div id="imeiError" class="imei-error" style="display:none;"><p id="imeiErrorMsg"></p></div>
            <div id="imeiResult" class="imei-result" style="display:none;">
                <div class="imei-result-head"><h3>Result</h3><button type="button" id="imeiNewCheck" class="imei-btn-new">New Check</button></div>
                <div id="resultContent" class="imei-result-body"></div>
            </div>
        </div>
    </div>
    <?php return ob_get_clean();
}

/* ---------- 2. AGE CALCULATOR ---------- */
// [merged: duplicate of quick-calculators' 'age_calculator'] add_shortcode('age_calculator', 'imei_sc_age');
function imei_sc_age() {
    ob_start(); ?>
    <div class="imei-wrap" id="age-tool">
        <div class="imei-box">
            <div class="imei-header"><div class="imei-big-icon">&#127874;</div><h2>Age Calculator</h2><p>Exact age in years, months &amp; days</p></div>
            <div class="imei-form">
                <label class="imei-label">Date of Birth</label>
                <div class="imei-input-row"><input type="date" id="ag-dob" /></div>
                <button type="button" class="imei-btn" data-t3="age" style="margin-top:12px;">Calculate Age</button>
                <div class="imei-result t3-result" style="display:none;"></div>
                <div class="imei-error t3-error" style="display:none;"></div>
            </div>
        </div>
    </div>
    <?php return ob_get_clean();
}

/* ---------- 3. BMI CALCULATOR ---------- */
// [merged: duplicate of quick-calculators' 'bmi_calculator'] add_shortcode('bmi_calculator', 'imei_sc_bmi');
function imei_sc_bmi() {
    ob_start(); ?>
    <div class="imei-wrap" id="bmi-tool">
        <div class="imei-box">
            <div class="imei-header"><div class="imei-big-icon">&#9878;</div><h2>BMI Calculator</h2><p>Body Mass Index with health category</p></div>
            <div class="imei-form">
                <div class="imei-row2">
                    <div><label class="imei-label">Weight (kg)</label><input type="number" id="bmi-weight" placeholder="70" min="10" /></div>
                    <div><label class="imei-label">Height (cm)</label><input type="number" id="bmi-height" placeholder="170" min="50" /></div>
                </div>
                <button type="button" class="imei-btn" data-t3="bmi" style="margin-top:12px;">Calculate BMI</button>
                <div class="imei-result t3-result" style="display:none;"></div>
                <div class="imei-error t3-error" style="display:none;"></div>
            </div>
        </div>
    </div>
    <?php return ob_get_clean();
}

/* ---------- 4. PERCENTAGE CALCULATOR ---------- */
// [merged: duplicate of quick-calculators' 'percentage_calculator'] add_shortcode('percentage_calculator', 'imei_sc_percentage');
function imei_sc_percentage() {
    ob_start(); ?>
    <div class="imei-wrap" id="percent-tool">
        <div class="imei-box">
            <div class="imei-header"><div class="imei-big-icon">&#128290;</div><h2>Percentage Calculator</h2><p>Three modes — sab kuch calculate karo</p></div>
            <div class="imei-form">
                <label class="imei-label">Mode</label>
                <select id="pc-mode" style="width:100%;padding:11px;border:2px solid #e2e8f0;border-radius:8px;font-size:15px;">
                    <option value="of">What is X% of Y?</option>
                    <option value="isWhat">X is what percent of Y?</option>
                    <option value="change">Percentage change from X to Y</option>
                </select>
                <div class="imei-row2" style="margin-top:12px;">
                    <div><label class="imei-label" id="pc-label1">X (%)</label><input type="number" id="pc-val1" step="any" placeholder="25" /></div>
                    <div><label class="imei-label" id="pc-label2">Y</label><input type="number" id="pc-val2" step="any" placeholder="200" /></div>
                </div>
                <button type="button" class="imei-btn" data-t3="percentage" style="margin-top:12px;">Calculate</button>
                <div class="imei-result t3-result" style="display:none;"></div>
                <div class="imei-error t3-error" style="display:none;"></div>
            </div>
        </div>
    </div>
    <?php return ob_get_clean();
}

/* ---------- 5. GPA CALCULATOR ---------- */
// [merged: duplicate of quick-calculators' 'gpa_calculator'] add_shortcode('gpa_calculator', 'imei_sc_gpa');
function imei_sc_gpa() {
    ob_start(); ?>
    <div class="imei-wrap" id="gpa-tool">
        <div class="imei-box">
            <div class="imei-header"><div class="imei-big-icon">&#127891;</div><h2>GPA Calculator</h2><p>College GPA on 4.0 scale</p></div>
            <div class="imei-form">
                <div id="gpa-rows"></div>
                <button type="button" class="imei-btn-new" id="gpa-add">+ Add Course</button>
                <button type="button" class="imei-btn" data-t3="gpa" style="margin-left:8px;margin-top:12px;">Calculate GPA</button>
                <div class="imei-result t3-result" style="display:none;"></div>
                <div class="imei-error t3-error" style="display:none;"></div>
            </div>
        </div>
    </div>
    <?php return ob_get_clean();
}

/* ---------- 6. IMEI VALIDATOR ---------- */
add_shortcode('imei_validator', 'imei_sc_imei_validator');
function imei_sc_imei_validator() {
    ob_start(); ?>
    <div class="imei-wrap" id="iv-tool">
        <div class="imei-box">
            <div class="imei-header"><div class="imei-big-icon">&#9989;</div><h2>IMEI Validator</h2><p>Luhn algorithm se IMEI verify karo</p></div>
            <div class="imei-form">
                <label class="imei-label">IMEI Number (15 digits)</label>
                <div class="imei-input-row">
                    <input type="text" id="iv-imei" maxlength="15" inputmode="numeric" placeholder="354599032344620" />
                    <button type="button" class="imei-btn" data-t3="imeivalidate">Validate</button>
                </div>
                <div class="imei-result t3-result" style="display:none;"></div>
                <div class="imei-error t3-error" style="display:none;"></div>
            </div>
        </div>
    </div>
    <?php return ob_get_clean();
}

/* ---------- 7. IMEI GENERATOR ---------- */
add_shortcode('imei_generator', 'imei_sc_imei_generator');
function imei_sc_imei_generator() {
    ob_start(); ?>
    <div class="imei-wrap" id="ig-tool">
        <div class="imei-box">
            <div class="imei-header"><div class="imei-big-icon">&#127922;</div><h2>IMEI Number Generator</h2><p>Valid test IMEIs generate karo (Luhn-valid)</p></div>
            <div class="imei-form">
                <div class="imei-row2">
                    <div><label class="imei-label">TAC Prefix (8 digits, optional)</label><input type="text" id="ig-tac" maxlength="8" inputmode="numeric" placeholder="35459903" /></div>
                    <div><label class="imei-label">How many?</label><input type="number" id="ig-count" value="5" min="1" max="20" /></div>
                </div>
                <button type="button" class="imei-btn" data-t3="imeigen">Generate</button>
                <div class="imei-result t3-result" style="display:none;"></div>
                <div class="imei-error t3-error" style="display:none;"></div>
            </div>
        </div>
    </div>
    <?php return ob_get_clean();
}

new IMEI_Check_Plugin();

/* Placeholder class kept for compatibility */
class IMEI_Check_Plugin {
    public function __construct() {}
}
