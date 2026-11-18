<?php
/*
Plugin Name: MultyTools
Plugin URI: https://ilovetool3.com
Description: 20 free online tools: Case Converter, Number to Words, Duplicate Line Remover, Lorem Ipsum, Text Reverser, Remove Line Breaks, Compound Interest, Zakat Calculator, Electricity Bill, Love Calculator, Birthday Countdown, JSON Formatter, Base64, URL Encoder, Hash Generator, UUID Generator, Color Converter, Fancy Text Generator, Stylish Text Generator, QR Code Generator. Auto-creates pages from WP Admin > MultyTools.
Version: 1.0.0
Author: iLoveTool3
Author URI: https://ilovetool3.com
License: GPL v2 or later
Text Domain: multytools
*/

if (!defined('ABSPATH')) {
    exit;
}

define('MT_VERSION', '1.0.1');
define('MT_URL', plugin_dir_url(__FILE__));

/* ================= ASSETS ================= */
add_action('wp_enqueue_scripts', 'mt_assets');
function mt_assets() {
    wp_enqueue_style('mt-style', MT_URL . 'assets/css/style.css', array(), MT_VERSION);
    wp_enqueue_script('mt-qrcode-engine', MT_URL . 'assets/js/qrcode-engine.js', array(), MT_VERSION, true);
    wp_enqueue_script('mt-script', MT_URL . 'assets/js/script.js', array('mt-qrcode-engine'), MT_VERSION, true);
}

/* ================= TOOLS MAP ================= */
function mt_tools_map() {
    return array(
        'case-converter'              => array('Case Converter', '[case_converter]'),
        'number-to-words'             => array('Number to Words Converter', '[number_to_words]'),
        'duplicate-line-remover'      => array('Duplicate Line Remover', '[duplicate_line_remover]'),
        'lorem-ipsum-generator'       => array('Lorem Ipsum Generator', '[lorem_ipsum_generator]'),
        'text-reverser'               => array('Text Reverser', '[text_reverser]'),
        'remove-line-breaks'          => array('Remove Line Breaks', '[remove_line_breaks]'),
        'compound-interest-calculator'=> array('Compound Interest Calculator', '[compound_interest_calculator]'),
        'zakat-calculator'            => array('Zakat Calculator', '[zakat_calculator]'),
        'electricity-bill-calculator' => array('Electricity Bill Calculator (Pakistan)', '[electricity_bill_calculator]'),
        'love-calculator'             => array('Love Calculator', '[love_calculator]'),
        'birthday-countdown'          => array('Birthday Countdown & Age Calculator', '[birthday_countdown]'),
        'json-formatter'              => array('JSON Formatter', '[json_formatter]'),
        'base64-encoder-decoder'      => array('Base64 Encoder / Decoder', '[base64_tool]'),
        'url-encoder-decoder'         => array('URL Encoder / Decoder', '[url_encoder_decoder]'),
        'hash-generator'              => array('Hash Generator (MD5, SHA)', '[hash_generator]'),
        'uuid-generator'              => array('UUID Generator', '[uuid_generator]'),
        'color-converter'             => array('Color Converter (HEX, RGB, HSL)', '[color_converter]'),
        'fancy-text-generator'        => array('Fancy Text Generator', '[fancy_text_generator]'),
        'stylish-text-generator'      => array('Stylish Text Generator', '[stylish_text_generator]'),
        'qr-code-generator'           => array('QR Code Generator', '[qr_code_generator]'),
    );
}

/* FIX: a real, human sentence for each tool's meta description instead
   of leaving it blank — several SEO plugins fall back to raw post
   content (i.e. the shortcode text itself) when nothing else is set,
   which is exactly the broken "[shortcode...]" Google snippet issue
   found on this site's PDF tool pages. Applying the same fix here. */
function mt_tool_description($title) {
    return 'Use the free ' . $title . ' online — no sign-up, no software install. Fast, private, and works right in your browser.';
}

/* ================= ADMIN DASHBOARD ================= */
add_action('admin_menu', 'mt_admin_menu', 10); // FIX (consolidation): now a submenu of the shared "iLoveTool3 Suite" parent menu instead of its own top-level entry
function mt_admin_menu() {
    add_submenu_page('imei-master', 'MultyTools', 'MultyTools', 'manage_options', 'mt-tools', 'mt_admin_page');
}

function mt_admin_page() {
    $created = isset($_GET['created']) ? intval($_GET['created']) : -1;
    $tools = mt_tools_map();
    ?>
    <div class="wrap">
        <h1>&#129514; MultyTools Dashboard</h1>

        <?php if ($created === 0) : ?>
            <div class="notice notice-warning"><p>Saare pages pehle se exist karte hain.</p></div>
        <?php elseif ($created > 0) : ?>
            <div class="notice notice-success"><p><strong><?php echo $created; ?> pages ban gaye!</strong></p></div>
        <?php endif; ?>
        <?php if (isset($_GET['repaired'])) : ?>
            <div class="notice notice-success"><p><strong><?php echo intval($_GET['repaired']); ?> existing page(s) repaired</strong> — blank/broken descriptions fixed.</p></div>
        <?php endif; ?>

        <div style="background:#fff;border:1px solid #ccd0d4;padding:20px;margin-top:15px;border-radius:6px;">
            <h2 style="margin-top:0;">&#9889; One-Click Pages</h2>
            <p>Saare <?php echo count($tools); ?> tools ke alag-alag pages automatically ban jayenge.</p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="mt_create_pages" />
                <?php wp_nonce_field('mt_create_pages'); ?>
                <button type="submit" class="button button-primary button-hero">&#128194; Create All Tool Pages</button>
            </form>
        </div>

        <div style="background:#fff;border:1px solid #ccd0d4;padding:20px;margin-top:15px;border-radius:6px;">
            <h2 style="margin-top:0;">&#128295; Repair Existing Pages</h2>
            <p>If any of these pages were created before descriptions were added, this fixes their Google search snippet (only touches pages with a blank or broken description).</p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="mt_repair_pages" />
                <?php wp_nonce_field('mt_repair_pages'); ?>
                <button type="submit" class="button button-secondary">Repair Existing Pages</button>
            </form>
        </div>

        <h2 style="margin-top:25px;">&#128203; All <?php echo count($tools); ?> Tools</h2>
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
    <?php
}

/* ================= AUTO CREATE PAGES ================= */
add_action('admin_post_mt_create_pages', 'mt_create_pages_handler');
function mt_create_pages_handler() {
    if (!current_user_can('manage_options')) { wp_die('Permission denied.'); }
    check_admin_referer('mt_create_pages');

    $count = 0;
    foreach (mt_tools_map() as $slug => $data) {
        if (!get_page_by_path($slug)) {
            $description = mt_tool_description($data[0]);
            $result = wp_insert_post(array(
                'post_title'     => $data[0],
                'post_name'      => $slug,
                'post_content'   => $data[1],
                'post_excerpt'   => $description, // FIX: was missing — see mt_tool_description() note above.
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'comment_status' => 'closed',
            ));
            if ($result) {
                $count++;
                if (function_exists('mt_set_seo_description')) {
                    mt_set_seo_description($result, $description);
                }
            }
        }
    }
    wp_redirect(admin_url('admin.php?page=mt-tools&created=' . $count));
    exit;
}

/* FIX: one-click repair for tool pages created before the description
   fix above — same pattern used for the PDF tools module. Only touches
   pages with a blank excerpt or one that still contains raw shortcode
   text, so a description you've customized by hand is left untouched. */
add_action('admin_post_mt_repair_pages', 'mt_repair_pages_handler');
function mt_repair_pages_handler() {
    if (!current_user_can('manage_options')) { wp_die('Permission denied.'); }
    check_admin_referer('mt_repair_pages');

    $fixed = 0;
    foreach (mt_tools_map() as $slug => $data) {
        $page = get_page_by_path($slug);
        if (!$page) { continue; }
        if (trim($page->post_excerpt) === '' || strpos($page->post_excerpt, '[') !== false) {
            $description = mt_tool_description($data[0]);
            wp_update_post(array('ID' => $page->ID, 'post_excerpt' => $description));
            if (function_exists('mt_set_seo_description')) {
                mt_set_seo_description($page->ID, $description);
            }
            $fixed++;
        }
    }
    wp_redirect(admin_url('admin.php?page=mt-tools&repaired=' . $fixed));
    exit;
}

/* ================= ALL-IN-ONE SHORTCODE ================= */
add_shortcode('mt_all', 'mt_all_shortcode');
function mt_all_shortcode($atts) {
    $atts = shortcode_atts(array(
        'show' => 'case,numwords,duplines,lorem,reverse,linebreaks,fancytext,stylishtext,compound,zakat,electric,love,birthday,json,base64,url,hash,uuid,color'
    ), $atts);

    $map = array(
        'case'       => array('mt_sc_case_converter', 'Case Converter'),
        'numwords'   => array('mt_sc_number_to_words', 'Number to Words'),
        'duplines'   => array('mt_sc_duplicate_lines', 'Duplicate Line Remover'),
        'lorem'      => array('mt_sc_lorem_ipsum', 'Lorem Ipsum Generator'),
        'reverse'    => array('mt_sc_text_reverser', 'Text Reverser'),
        'linebreaks' => array('mt_sc_remove_linebreaks', 'Remove Line Breaks'),
        'fancytext'  => array('mt_sc_fancy_text', 'Fancy Text Generator'),
        'stylishtext'=> array('mt_sc_stylish_text', 'Stylish Text Generator'),
        'compound'   => array('mt_sc_compound_interest', 'Compound Interest'),
        'zakat'      => array('mt_sc_zakat', 'Zakat Calculator'),
        'electric'   => array('mt_sc_electricity', 'Electricity Bill Calculator'),
        'love'       => array('mt_sc_love', 'Love Calculator'),
        'birthday'   => array('mt_sc_birthday', 'Birthday Countdown'),
        'json'       => array('mt_sc_json_formatter', 'JSON Formatter'),
        'base64'     => array('mt_sc_base64', 'Base64 Encoder/Decoder'),
        'url'        => array('mt_sc_url_encoder', 'URL Encoder/Decoder'),
        'hash'       => array('mt_sc_hash_generator', 'Hash Generator'),
        'uuid'       => array('mt_sc_uuid', 'UUID Generator'),
        'color'      => array('mt_sc_color_converter', 'Color Converter'),
    );

    ob_start();
    echo '<div class="mt-wrap">';
    foreach (array_map('trim', explode(',', $atts['show'])) as $key) {
        if (isset($map[$key])) {
            echo '<div class="mt-tool-section" id="mt-' . esc_attr($key) . '">';
            echo call_user_func($map[$key][0]);
            echo '</div>';
        }
    }
    echo '</div>';
    return ob_get_clean();
}

/* ============ 1. CASE CONVERTER ============ */
add_shortcode('case_converter', 'mt_sc_case_converter');
function mt_sc_case_converter() {
    ob_start(); ?>
    <div class="mt-tool" data-tool="case">
        <h3 class="mt-title"><span class="mt-emoji">&#128221;</span> Case Converter</h3>
        <p class="mt-note">Text paste karo aur koi bhi case convert karo — instantly!</p>
        <div class="mt-field"><label>Your Text</label>
            <textarea id="mt-case-input" rows="6" placeholder="Type or paste your text here..."></textarea></div>
        <div class="mt-btn-group">
            <button type="button" class="mt-btn-sm" data-case="upper">UPPERCASE</button>
            <button type="button" class="mt-btn-sm" data-case="lower">lowercase</button>
            <button type="button" class="mt-btn-sm" data-case="title">Title Case</button>
            <button type="button" class="mt-btn-sm" data-case="sentence">Sentence case</button>
            <button type="button" class="mt-btn-sm" data-case="camel">camelCase</button>
            <button type="button" class="mt-btn-sm" data-case="pascal">PascalCase</button>
            <button type="button" class="mt-btn-sm" data-case="snake">snake_case</button>
            <button type="button" class="mt-btn-sm" data-case="kebab">kebab-case</button>
            <button type="button" class="mt-btn-sm" data-case="alternate">aLtErNaTe</button>
        </div>
        <div class="mt-field"><label>Result</label>
            <textarea id="mt-case-output" rows="6" readonly placeholder="Result appears here..."></textarea></div>
        <div class="mt-btn-group">
            <button type="button" class="mt-copy-btn" data-copy-target="mt-case-output">&#128203; Copy Result</button>
            <button type="button" class="mt-btn-light" data-action="case-clear">Clear</button>
        </div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 2. NUMBER TO WORDS ============ */
add_shortcode('number_to_words', 'mt_sc_number_to_words');
function mt_sc_number_to_words() {
    ob_start(); ?>
    <div class="mt-tool" data-tool="numwords">
        <h3 class="mt-title"><span class="mt-emoji">&#128290;</span> Number to Words Converter</h3>
        <p class="mt-note">Numbers ko words mein convert karo — cheque likhne ke liye perfect!</p>
        <div class="mt-row2">
            <div class="mt-field"><label>Number</label>
                <input type="number" id="mt-nw-number" placeholder="e.g. 125000" min="0" /></div>
            <div class="mt-field"><label>Currency / System</label>
                <select id="mt-nw-currency">
                    <option value="pkr">PKR — Rupees (Lakh/Crore)</option>
                    <option value="inr">INR — Rupees (Lakh/Crore)</option>
                    <option value="usd">USD — Dollars (Million/Billion)</option>
                    <option value="plain">Plain Numbers Only</option>
                </select></div>
        </div>
        <button type="button" class="mt-btn" data-action="numwords">Convert to Words</button>
        <div class="mt-result" style="display:none;"></div>
        <div class="mt-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 3. DUPLICATE LINE REMOVER ============ */
add_shortcode('duplicate_line_remover', 'mt_sc_duplicate_lines');
function mt_sc_duplicate_lines() {
    ob_start(); ?>
    <div class="mt-tool" data-tool="duplines">
        <h3 class="mt-title"><span class="mt-emoji">&#128203;</span> Duplicate Line Remover</h3>
        <p class="mt-note">Text ke duplicate lines hatao — unique lines bachao.</p>
        <div class="mt-field"><label>Your Text (one item per line)</label>
            <textarea id="mt-dup-input" rows="8" placeholder="line one&#10;line two&#10;line one"></textarea></div>
        <div class="mt-checks">
            <label class="mt-check"><input type="checkbox" id="mt-dup-trim" checked /> Trim whitespace</label>
            <label class="mt-check"><input type="checkbox" id="mt-dup-insensitive" checked /> Ignore letter case</label>
            <label class="mt-check"><input type="checkbox" id="mt-dup-removeempty" checked /> Remove empty lines</label>
            <label class="mt-check"><input type="checkbox" id="mt-dup-sort" /> Sort result A-Z</label>
        </div>
        <button type="button" class="mt-btn" data-action="duplines">Remove Duplicates</button>
        <div class="mt-field"><label>Result</label>
            <textarea id="mt-dup-output" rows="8" readonly></textarea></div>
        <div class="mt-result" style="display:none;" id="mt-dup-stats"></div>
        <div class="mt-error" style="display:none;"></div>
        <div class="mt-btn-group" style="margin-top:10px;">
            <button type="button" class="mt-copy-btn" data-copy-target="mt-dup-output">&#128203; Copy Result</button>
        </div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 4. LOREM IPSUM GENERATOR ============ */
add_shortcode('lorem_ipsum_generator', 'mt_sc_lorem_ipsum');
function mt_sc_lorem_ipsum() {
    ob_start(); ?>
    <div class="mt-tool" data-tool="lorem">
        <h3 class="mt-title"><span class="it3-emoji mt-emoji">&#128366;</span> Lorem Ipsum Generator</h3>
        <p class="mt-note">Placeholder text for designs and mockups.</p>
        <div class="mt-row2">
            <div class="mt-field"><label>Type</label>
                <select id="mt-li-type">
                    <option value="paragraphs" selected>Paragraphs</option>
                    <option value="sentences">Sentences</option>
                    <option value="words">Words</option>
                </select></div>
            <div class="mt-field"><label>Count (1-100)</label>
                <input type="number" id="mt-li-count" value="3" min="1" max="100" /></div>
        </div>
        <button type="button" class="mt-btn" data-action="lorem">Generate Text</button>
        <div class="mt-field"><label>Generated Lorem Ipsum</label>
            <textarea id="mt-li-output" rows="9" readonly></textarea></div>
        <div class="mt-btn-group">
            <button type="button" class="mt-copy-btn" data-copy-target="mt-li-output">&#128203; Copy Text</button>
        </div>
        <div class="mt-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 5. TEXT REVERSER ============ */
add_shortcode('text_reverser', 'mt_sc_text_reverser');
function mt_sc_text_reverser() {
    ob_start(); ?>
    <div class="mt-tool" data-tool="reverse">
        <h3 class="mt-title"><span class="mt-emoji">&#128260;</span> Text Reverser</h3>
        <p class="mt-note">Text ko ulata do — letters, words ya order.</p>
        <div class="mt-field"><label>Your Text</label>
            <textarea id="mt-rev-input" rows="5" placeholder="Hello World"></textarea></div>
        <div class="mt-btn-group">
            <button type="button" class="mt-btn-sm" data-rev="letters">Reverse Letters (dlroW olleH)</button>
            <button type="button" class="mt-btn-sm" data-rev="eachword">Reverse Each Word (olleH dlroW)</button>
            <button type="button" class="mt-btn-sm" data-rev="wordorder">Reverse Word Order (World Hello)</button>
        </div>
        <div class="mt-field"><label>Result</label>
            <textarea id="mt-rev-output" rows="5" readonly></textarea></div>
        <div class="mt-btn-group">
            <button type="button" class="mt-copy-btn" data-copy-target="mt-rev-output">&#128203; Copy Result</button>
        </div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 6. REMOVE LINE BREAKS ============ */
add_shortcode('remove_line_breaks', 'mt_sc_remove_linebreaks');
function mt_sc_remove_linebreaks() {
    ob_start(); ?>
    <div class="mt-tool" data-tool="linebreaks">
        <h3 class="mt-title"><span class="mt-emoji">&#9986;</span> Remove Line Breaks</h3>
        <p class="mt-note">Line breaks hatayein aur ek line mein text payein.</p>
        <div class="mt-field"><label>Your Text</label>
            <textarea id="mt-lb-input" rows="7" placeholder="Paste text with line breaks..."></textarea></div>
        <div class="mt-field"><label>Replace Line Breaks With</label>
            <select id="mt-lb-replace">
                <option value="space" selected>Single Space</option>
                <option value="none">Nothing (join directly)</option>
                <option value="comma">Comma + Space</option>
                <option value="custom">Custom text...</option>
            </select></div>
        <div class="mt-field" id="mt-lb-custom-wrap" style="display:none;"><label>Custom Replacement</label>
            <input type="text" id="mt-lb-custom" placeholder="e.g. | or - or any text" /></div>
        <button type="button" class="mt-btn" data-action="linebreaks">Remove Line Breaks</button>
        <div class="mt-field"><label>Result</label>
            <textarea id="mt-lb-output" rows="4" readonly></textarea></div>
        <div class="mt-btn-group">
            <button type="button" class="mt-copy-btn" data-copy-target="mt-lb-output">&#128203; Copy Result</button>
        </div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 7. FANCY TEXT GENERATOR ============ */
add_shortcode('fancy_text_generator', 'mt_sc_fancy_text');
function mt_sc_fancy_text() {
    ob_start(); ?>
    <div class="mt-tool" data-tool="fancytext">
        <h3 class="mt-title"><span class="mt-emoji">&#10024;</span> Fancy Text Generator</h3>
        <p class="mt-note">Normal text ko 12 alag Unicode fonts/styles mein badlein — copy karke kahin bhi paste karein (Instagram bio, Facebook, WhatsApp, etc).</p>
        <div class="mt-field"><label>Your Text</label>
            <textarea id="mt-fancy-input" rows="3" placeholder="Type something..." maxlength="200"></textarea></div>
        <div id="mt-fancy-output" class="mt-fancy-list"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 8. STYLISH TEXT GENERATOR ============ */
add_shortcode('stylish_text_generator', 'mt_sc_stylish_text');
function mt_sc_stylish_text() {
    ob_start(); ?>
    <div class="mt-tool" data-tool="stylishtext">
        <h3 class="mt-title"><span class="mt-emoji">&#127912;</span> Stylish Text Generator</h3>
        <p class="mt-note">Text ko decorative symbols/borders ke sath style karein — bio, banners aur captions ke liye.</p>
        <div class="mt-field"><label>Your Text</label>
            <textarea id="mt-stylish-input" rows="3" placeholder="Type something..." maxlength="200"></textarea></div>
        <div id="mt-stylish-output" class="mt-fancy-list"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 20. QR CODE GENERATOR ============ */
add_shortcode('qr_code_generator', 'mt_sc_qrcode');
function mt_sc_qrcode() {
    ob_start(); ?>
    <div class="mt-tool" data-tool="qrcode">
        <h3 class="mt-title"><span class="mt-emoji">&#128241;</span> QR Code Generator</h3>
        <p class="mt-note">URL, text, WiFi, contact card aur bhi bohat kuch ke liye QR code banayein — free, no sign-up, sab kuch browser mein hi hota hai.</p>

        <div class="mt-field"><label>QR Code Type</label>
            <select id="mt-qr-type">
                <option value="url">Website URL</option>
                <option value="text">Plain Text</option>
                <option value="wifi">WiFi Network</option>
                <option value="email">Email</option>
                <option value="phone">Phone Number</option>
                <option value="sms">SMS</option>
                <option value="vcard">Contact Card (vCard)</option>
            </select>
        </div>

        <div class="mt-qr-fields" data-qr-fields="url">
            <div class="mt-field"><label>URL</label><input type="text" id="mt-qr-url" placeholder="https://example.com" /></div>
        </div>
        <div class="mt-qr-fields" data-qr-fields="text" style="display:none">
            <div class="mt-field"><label>Text</label><textarea id="mt-qr-text" rows="3" placeholder="Any text..." maxlength="250"></textarea></div>
        </div>
        <div class="mt-qr-fields" data-qr-fields="wifi" style="display:none">
            <div class="mt-field"><label>Network Name (SSID)</label><input type="text" id="mt-qr-wifi-ssid" /></div>
            <div class="mt-field"><label>Password</label><input type="text" id="mt-qr-wifi-pass" /></div>
            <div class="mt-field"><label>Security</label>
                <select id="mt-qr-wifi-sec"><option value="WPA">WPA/WPA2</option><option value="WEP">WEP</option><option value="nopass">None (open network)</option></select></div>
        </div>
        <div class="mt-qr-fields" data-qr-fields="email" style="display:none">
            <div class="mt-field"><label>Email Address</label><input type="text" id="mt-qr-email-to" placeholder="name@example.com" /></div>
            <div class="mt-field"><label>Subject</label><input type="text" id="mt-qr-email-subject" /></div>
            <div class="mt-field"><label>Message</label><textarea id="mt-qr-email-body" rows="2"></textarea></div>
        </div>
        <div class="mt-qr-fields" data-qr-fields="phone" style="display:none">
            <div class="mt-field"><label>Phone Number</label><input type="text" id="mt-qr-phone" placeholder="+923001234567" /></div>
        </div>
        <div class="mt-qr-fields" data-qr-fields="sms" style="display:none">
            <div class="mt-field"><label>Phone Number</label><input type="text" id="mt-qr-sms-phone" placeholder="+923001234567" /></div>
            <div class="mt-field"><label>Message</label><textarea id="mt-qr-sms-body" rows="2"></textarea></div>
        </div>
        <div class="mt-qr-fields" data-qr-fields="vcard" style="display:none">
            <div class="mt-row2">
                <div class="mt-field"><label>Full Name</label><input type="text" id="mt-qr-vc-name" /></div>
                <div class="mt-field"><label>Phone</label><input type="text" id="mt-qr-vc-phone" /></div>
            </div>
            <div class="mt-row2">
                <div class="mt-field"><label>Email</label><input type="text" id="mt-qr-vc-email" /></div>
                <div class="mt-field"><label>Organization</label><input type="text" id="mt-qr-vc-org" /></div>
            </div>
        </div>

        <div class="mt-subtitle">Customize</div>
        <div class="mt-row2">
            <div class="mt-field"><label>Size: <span id="mt-qr-size-val">300px</span></label>
                <input type="range" id="mt-qr-size" min="150" max="600" step="10" value="300" /></div>
            <div class="mt-field"><label>Error Correction</label>
                <select id="mt-qr-ec">
                    <option value="L">Low (~7% recovery)</option>
                    <option value="M" selected>Medium (~15% recovery)</option>
                    <option value="Q">Quartile (~25% recovery)</option>
                    <option value="H">High (~30% recovery)</option>
                </select></div>
        </div>
        <div class="mt-row2">
            <div class="mt-field"><label>Foreground Color</label><input type="color" id="mt-qr-fg" value="#000000" /></div>
            <div class="mt-field"><label>Background Color</label><input type="color" id="mt-qr-bg" value="#ffffff" /></div>
        </div>

        <div class="mt-qr-preview-wrap">
            <canvas id="mt-qr-canvas"></canvas>
        </div>
        <div class="mt-btn-group" style="justify-content:center">
            <button type="button" class="mt-btn-sm" id="mt-qr-download-png">&#11015;&#65039; Download PNG</button>
            <button type="button" class="mt-btn-sm" id="mt-qr-download-svg">&#11015;&#65039; Download SVG</button>
        </div>
        <div class="mt-error" id="mt-qr-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 9. COMPOUND INTEREST CALCULATOR ============ */
// [merged: duplicate of quick-calculators' 'compound_interest_calculator'] add_shortcode('compound_interest_calculator', 'mt_sc_compound_interest');
function mt_sc_compound_interest() {
    ob_start(); ?>
    <div class="mt-tool" data-tool="compound">
        <h3 class="mt-title"><span class="mt-emoji">&#128176;</span> Compound Interest Calculator</h3>
        <div class="mt-row2">
            <div class="mt-field"><label>Principal Amount</label>
                <input type="number" id="mt-ci-principal" placeholder="e.g. 100000" min="1" /></div>
            <div class="mt-field"><label>Interest Rate (% per year)</label>
                <input type="number" id="mt-ci-rate" placeholder="e.g. 10" step="0.01" min="0" /></div>
        </div>
        <div class="mt-row2">
            <div class="mt-field"><label>Time Period (years)</label>
                <input type="number" id="mt-ci-years" placeholder="e.g. 10" min="1" step="any" /></div>
            <div class="mt-field"><label>Compounding Frequency</label>
                <select id="mt-ci-freq">
                    <option value="1">Yearly</option>
                    <option value="2">Half-Yearly</option>
                    <option value="4" selected>Quarterly</option>
                    <option value="12">Monthly</option>
                    <option value="365">Daily</option>
                </select></div>
        </div>
        <div class="mt-field"><label>Monthly Additional Deposit (optional)</label>
            <input type="number" id="mt-ci-monthly" placeholder="0" min="0" /></div>
        <button type="button" class="mt-btn" data-action="compound">Calculate</button>
        <div class="mt-result" style="display:none;"></div>
        <div class="mt-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 8. ZAKAT CALCULATOR ============ */
add_shortcode('zakat_calculator', 'mt_sc_zakat');
function mt_sc_zakat() {
    ob_start(); ?>
    <div class="mt-tool" data-tool="zakat">
        <h3 class="mt-title"><span class="mt-emoji">&#128722;</span> Zakat Calculator</h3>
        <p class="mt-note">Nisab: 87.48g Gold ya 612.36g Silver ki current qeemat. Aaj ke rates khud dalein.</p>
        <h4 class="mt-subtitle">Current Rates (per gram)</h4>
        <div class="mt-row2">
            <div class="mt-field"><label>Gold Rate (per gram)</label>
                <input type="number" id="mt-zk-goldrate" placeholder="e.g. 24000" min="0" /></div>
            <div class="mt-field"><label>Silver Rate (per gram)</label>
                <input type="number" id="mt-zk-silverrate" placeholder="e.g. 280" min="0" /></div>
        </div>
        <h4 class="mt-subtitle">Your Assets (value in your currency)</h4>
        <div class="mt-row2">
            <div class="mt-field"><label>Gold Value</label>
                <input type="number" id="mt-zk-gold" placeholder="0" min="0" /></div>
            <div class="mt-field"><label>Silver Value</label>
                <input type="number" id="mt-zk-silver" placeholder="0" min="0" /></div>
        </div>
        <div class="mt-row2">
            <div class="mt-field"><label>Cash in Hand &amp; Bank</label>
                <input type="number" id="mt-zk-cash" placeholder="0" min="0" /></div>
            <div class="mt-field"><label>Investments / Savings Certificates</label>
                <input type="number" id="mt-zk-investments" placeholder="0" min="0" /></div>
        </div>
        <div class="mt-row2">
            <div class="mt-field"><label>Business Assets / Stock Value</label>
                <input type="number" id="mt-zk-business" placeholder="0" min="0" /></div>
            <div class="mt-field"><label>Money Lent to Others (recoverable)</label>
                <input type="number" id="mt-zk-lent" placeholder="0" min="0" /></div>
        </div>
        <div class="mt-field"><label>Debts / Liabilities (deduct)</label>
            <input type="number" id="mt-zk-debts" placeholder="0" min="0" /></div>
        <div class="mt-field"><label>Nisab Standard</label>
            <select id="mt-zk-standard">
                <option value="silver" selected>Silver Nisab (recommended for ease)</option>
                <option value="gold">Gold Nisab</option>
            </select></div>
        <button type="button" class="mt-btn" data-action="zakat">Calculate Zakat</button>
        <div class="mt-result" style="display:none;"></div>
        <div class="mt-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 9. ELECTRICITY BILL CALCULATOR (PAKISTAN) ============ */
add_shortcode('electricity_bill_calculator', 'mt_sc_electricity');
function mt_sc_electricity() {
    ob_start(); ?>
    <div class="mt-tool" data-tool="electric">
        <h3 class="mt-title"><span class="mt-emoji">&#9889;</span> Electricity Bill Calculator (Pakistan)</h3>
        <p class="mt-note">Estimate only — actual rates apne latest bill/tariff se verify karein.</p>
        <div class="mt-row2">
            <div class="mt-field"><label>Units Consumed (kWh)</label>
                <input type="number" id="mt-eb-units" placeholder="e.g. 350" min="1" /></div>
            <div class="mt-field"><label>Consumer Type</label>
                <select id="mt-eb-type">
                    <option value="unprotected" selected>Domestic — Unprotected</option>
                    <option value="protected">Domestic — Protected (&le;200 units)</option>
                </select></div>
        </div>
        <details class="mt-details">
            <summary>Advanced Options (rates edit karo agar chahiye)</summary>
            <div class="mt-row2" style="margin-top:12px;">
                <div class="mt-field"><label>FPA (per unit)</label>
                    <input type="number" id="mt-eb-fpa" value="0" step="0.01" min="0" /></div>
                <div class="mt-field"><label>Fixed Charges</label>
                    <input type="number" id="mt-eb-fixed" value="240" min="0" /></div>
            </div>
            <div class="mt-row2">
                <div class="mt-field"><label>GST (%)</label>
                    <input type="number" id="mt-eb-gst" value="18" step="0.01" min="0" /></div>
                <div class="mt-field"><label>Elec. Duty (%)</label>
                    <input type="number" id="mt-eb-duty" value="1.5" step="0.01" min="0" /></div>
            </div>
            <div class="mt-checks">
                <label class="mt-check"><input type="checkbox" id="mt-eb-tvfee" checked /> TV Fee (Rs 35)</label>
                <label class="mt-check"><input type="checkbox" id="mt-eb-meter" checked /> Meter Rent (Rs 15)</label>
            </div>
        </details>
        <button type="button" class="mt-btn" data-action="electric">Calculate Bill</button>
        <div class="mt-result" style="display:none;"></div>
        <div class="mt-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 10. LOVE CALCULATOR ============ */
add_shortcode('love_calculator', 'mt_sc_love');
function mt_sc_love() {
    ob_start(); ?>
    <div class="mt-tool" data-tool="love">
        <h3 class="mt-title"><span class="mt-emoji">&#10084;</span> Love Calculator</h3>
        <p class="mt-note">Do naam likho aur compatibility check karo! (Fun tool)</p>
        <div class="mt-row2">
            <div class="mt-field"><label>Your Name</label>
                <input type="text" id="mt-love-name1" placeholder="e.g. Ali" /></div>
            <div class="mt-field"><label>Partner Name</label>
                <input type="text" id="mt-love-name2" placeholder="e.g. Sara" /></div>
        </div>
        <button type="button" class="mt-btn" data-action="love">Calculate Love &#128140;</button>
        <div class="mt-result" style="display:none;"></div>
        <div class="mt-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 11. BIRTHDAY COUNTDOWN ============ */
add_shortcode('birthday_countdown', 'mt_sc_birthday');
function mt_sc_birthday() {
    ob_start(); ?>
    <div class="mt-tool" data-tool="birthday">
        <h3 class="mt-title"><span class="mt-emoji">&#127874;</span> Birthday Countdown &amp; Age Calculator</h3>
        <div class="mt-field"><label>Date of Birth</label>
            <input type="date" id="mt-bd-date" /></div>
        <button type="button" class="mt-btn" data-action="birthday">Calculate</button>
        <div class="mt-result" style="display:none;"></div>
        <div class="mt-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 12. JSON FORMATTER ============ */
add_shortcode('json_formatter', 'mt_sc_json_formatter');
function mt_sc_json_formatter() {
    ob_start(); ?>
    <div class="mt-tool" data-tool="json">
        <h3 class="mt-title"><span class="mt-emoji">{ }</span> JSON Formatter</h3>
        <p class="mt-note">JSON beautify, minify ya validate karo.</p>
        <div class="mt-field"><label>JSON Input</label>
            <textarea id="mt-json-input" rows="7" placeholder='{"name":"Ali","age":25,"city":"Karachi"}'></textarea></div>
        <div class="mt-btn-group">
            <button type="button" class="mt-btn-sm" data-json="beautify">Beautify</button>
            <button type="button" class="mt-btn-sm" data-json="minify">Minify</button>
            <button type="button" class="mt-btn-sm" data-json="validate">Validate Only</button>
        </div>
        <div class="mt-field" style="margin-top:12px;"><label>Indent Spaces</label>
            <select id="mt-json-indent" style="max-width:120px;">
                <option value="2" selected>2 spaces</option>
                <option value="4">4 spaces</option>
                <option value="tab">Tab</option>
            </select></div>
        <div class="mt-field"><label>Output</label>
            <textarea id="mt-json-output" rows="9" readonly></textarea></div>
        <div class="mt-result" style="display:none;" id="mt-json-status"></div>
        <div class="mt-error" style="display:none;"></div>
        <div class="mt-btn-group" style="margin-top:10px;">
            <button type="button" class="mt-copy-btn" data-copy-target="mt-json-output">&#128203; Copy Output</button>
        </div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 13. BASE64 ENCODER / DECODER ============ */
add_shortcode('base64_tool', 'mt_sc_base64');
function mt_sc_base64() {
    ob_start(); ?>
    <div class="mt-tool" data-tool="base64">
        <h3 class="mt-title"><span class="mt-emoji">&#128274;</span> Base64 Encoder / Decoder</h3>
        <p class="mt-note">Unicode-safe Base64 conversion.</p>
        <div class="mt-field"><label>Input</label>
            <textarea id="mt-b64-input" rows="5" placeholder="Enter text or base64 string..."></textarea></div>
        <div class="mt-btn-group">
            <button type="button" class="mt-btn-sm" data-b64="encode">Encode &rarr;</button>
            <button type="button" class="mt-btn-sm" data-b64="decode">&larr; Decode</button>
        </div>
        <div class="mt-field"><label>Output</label>
            <textarea id="mt-b64-output" rows="5" readonly></textarea></div>
        <div class="mt-btn-group">
            <button type="button" class="mt-copy-btn" data-copy-target="mt-b64-output">&#128203; Copy Output</button>
        </div>
        <div class="mt-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 14. URL ENCODER / DECODER ============ */
add_shortcode('url_encoder_decoder', 'mt_sc_url_encoder');
function mt_sc_url_encoder() {
    ob_start(); ?>
    <div class="mt-tool" data-tool="url">
        <h3 class="mt-title"><span class="mt-emoji">&#128279;</span> URL Encoder / Decoder</h3>
        <div class="mt-field"><label>Input</label>
            <textarea id="mt-url-input" rows="5" placeholder="https://example.com/?name=Ali Khan&city=Lahore"></textarea></div>
        <div class="mt-btn-group">
            <button type="button" class="mt-btn-sm" data-url="encode">Encode &rarr;</button>
            <button type="button" class="mt-btn-sm" data-url="decode">&larr; Decode</button>
            <button type="button" class="mt-btn-sm" data-url="encode-full">Encode Full URL</button>
        </div>
        <div class="mt-field"><label>Output</label>
            <textarea id="mt-url-output" rows="5" readonly></textarea></div>
        <div class="mt-btn-group">
            <button type="button" class="mt-copy-btn" data-copy-target="mt-url-output">&#128203; Copy Output</button>
        </div>
        <div class="mt-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 15. HASH GENERATOR ============ */
add_shortcode('hash_generator', 'mt_sc_hash_generator');
function mt_sc_hash_generator() {
    ob_start(); ?>
    <div class="mt-tool" data-tool="hash">
        <h3 class="mt-title"><span class="mt-emoji">#</span> Hash Generator</h3>
        <p class="mt-note">MD5, SHA-1, SHA-256, SHA-384, SHA-512 hashes generate karo.</p>
        <div class="mt-field"><label>Input Text</label>
            <textarea id="mt-hash-input" rows="4" placeholder="Enter text to hash..."></textarea></div>
        <div class="mt-checks">
            <label class="mt-check"><input type="checkbox" id="mt-hash-md5" checked /> MD5</label>
            <label class="mt-check"><input type="checkbox" id="mt-hash-sha1" checked /> SHA-1</label>
            <label class="mt-check"><input type="checkbox" id="mt-hash-sha256" checked /> SHA-256</label>
            <label class="mt-check"><input type="checkbox" id="mt-hash-sha384" /> SHA-384</label>
            <label class="mt-check"><input type="checkbox" id="mt-hash-sha512" /> SHA-512</label>
        </div>
        <button type="button" class="mt-btn" data-action="hash">Generate Hashes</button>
        <div id="mt-hash-results" style="display:none;"></div>
        <div class="mt-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 16. UUID GENERATOR ============ */
add_shortcode('uuid_generator', 'mt_sc_uuid');
function mt_sc_uuid() {
    ob_start(); ?>
    <div class="mt-tool" data-tool="uuid">
        <h3 class="mt-title"><span class="mt-emoji">&#127922;</span> UUID Generator</h3>
        <p class="mt-note">Random UUID v4 identifiers generate karo.</p>
        <div class="mt-field"><label>How many UUIDs? (1-100)</label>
            <input type="number" id="mt-uuid-count" value="5" min="1" max="100" /></div>
        <div class="mt-checks">
            <label class="mt-check"><input type="checkbox" id="mt-uuid-uppercase" /> UPPERCASE</label>
            <label class="mt-check"><input type="checkbox" id="mt-uuid-braces" /> Wrap in {} braces</label>
        </div>
        <button type="button" class="mt-btn" data-action="uuid">Generate UUIDs</button>
        <div class="mt-field"><label>Results</label>
            <textarea id="mt-uuid-output" rows="7" readonly></textarea></div>
        <div class="mt-btn-group">
            <button type="button" class="mt-copy-btn" data-copy-target="mt-uuid-output">&#128203; Copy All</button>
        </div>
        <div class="mt-error" style="display:none;"></div>
    </div>
    <?php return ob_get_clean();
}

/* ============ 17. COLOR CONVERTER ============ */
add_shortcode('color_converter', 'mt_sc_color_converter');
function mt_sc_color_converter() {
    ob_start(); ?>
    <div class="mt-tool" data-tool="color">
        <h3 class="mt-title"><span class="mt-emoji">&#127912;</span> Color Converter</h3>
        <p class="mt-note">Color pick karo — HEX, RGB, HSL sab formats milegi.</p>
        <div class="mt-color-layout">
            <div class="mt-color-picker-side">
                <input type="color" id="mt-color-wheel" value="#4f46e5" />
                <div class="mt-color-preview" id="mt-color-preview"></div>
            </div>
            <div class="mt-color-values">
                <div class="mt-field"><label>HEX</label>
                    <div class="mt-inline-copy"><input type="text" id="mt-color-hex" value="#4F46E5" /><button type="button" class="mt-copy-btn" data-copy-target="mt-color-hex">&#128203;</button></div></div>
                <div class="mt-field"><label>RGB</label>
                    <div class="mt-inline-copy"><input type="text" id="mt-color-rgb" readonly /><button type="button" class="mt-copy-btn" data-copy-target="mt-color-rgb">&#128203;</button></div></div>
                <div class="mt-field"><label>HSL</label>
                    <div class="mt-inline-copy"><input type="text" id="mt-color-hsl" readonly /><button type="button" class="mt-copy-btn" data-copy-target="mt-color-hsl">&#128203;</button></div></div>
            </div>
        </div>
    </div>
    <?php return ob_get_clean();
}
