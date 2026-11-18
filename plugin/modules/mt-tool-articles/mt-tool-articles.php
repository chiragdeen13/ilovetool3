<?php
/*
Plugin Name: MT Tool Articles
Plugin URI: https://ilovetool3.com
Description: Auto-adds SEO articles, banner images and FAQs below every tool page. Includes FAQ Schema for Google. Works with ANY tool page automatically!
Version: 1.0.0
Author: iLoveTool3
Author URI: https://ilovetool3.com
License: GPL v2 or later
Text Domain: mt-articles
*/

if (!defined('ABSPATH')) {
    exit;
}

define('MTA_VERSION', '1.0.0');
define('MTA_URL', plugin_dir_url(__FILE__));
define('MTA_PATH', plugin_dir_path(__FILE__));

require_once MTA_PATH . 'includes/tool-articles-data.php';
require_once MTA_PATH . 'includes/tool-seo-meta.php';

/* Long-form articles load karo */
function mta_get_longform() {
    static $longform = null;
    if ($longform !== null) { return $longform; }

    $longform = array();
    $dir = MTA_PATH . 'includes/longform/';
    if (is_dir($dir)) {
        foreach (glob($dir . '*.php') as $file) {
            $data = include $file;
            if (is_array($data)) { $longform = array_merge($longform, $data); }
        }
    }
    return $longform;
}

/* ================= ASSETS ================= */
add_action('wp_enqueue_scripts', 'mta_assets');
function mta_assets() {
    wp_enqueue_style('mta-style', MTA_URL . 'assets/css/style.css', array(), MTA_VERSION);
}

/* ============================================================
   SVG BANNER GENERATOR — har tool ka apna image
   ============================================================ */
function mta_generate_banner($title, $emoji) {
    $site = get_bloginfo('name');

    /* XML safe title */
    $safeTitle = htmlspecialchars($title, ENT_XML1, 'UTF-8');
    $safeSite  = htmlspecialchars($site, ENT_XML1, 'UTF-8');

    /* Title ko 2 lines mein todo agar lamba hai */
    $words = explode(' ', $title);
    $line1 = '';
    $line2 = '';
    if (strlen($title) > 22 && count($words) > 1) {
        $half = ceil(count($words) / 2);
        $line1 = htmlspecialchars(implode(' ', array_slice($words, 0, $half)), ENT_XML1, 'UTF-8');
        $line2 = htmlspecialchars(implode(' ', array_slice($words, $half)), ENT_XML1, 'UTF-8');
        $tY1 = 195; $tY2 = 240; $sY = 272;
    } else {
        $line1 = $safeTitle;
        $line2 = '';
        $tY1 = 215; $tY2 = 0; $sY = 258;
    }

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="800" height="300" viewBox="0 0 800 300">
  <defs>
    <linearGradient id="bg' . md5($title) . '" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#4f46e5"/>
      <stop offset="55%" stop-color="#7c3aed"/>
      <stop offset="100%" stop-color="#06b6d4"/>
    </linearGradient>
  </defs>
  <rect width="800" height="300" rx="24" fill="url(#bg' . md5($title) . ')"/>
  <circle cx="90" cy="60" r="110" fill="rgba(255,255,255,0.07)"/>
  <circle cx="720" cy="250" r="130" fill="rgba(255,255,255,0.07)"/>
  <circle cx="640" cy="40" r="45" fill="rgba(255,255,255,0.10)"/>
  <text x="400" y="128" font-size="76" text-anchor="middle">' . $emoji . '</text>
  <text x="400" y="' . $tY1 . '" font-size="' . ($line2 ? 34 : 40) . '" font-weight="800" fill="#ffffff" text-anchor="middle" font-family="Segoe UI, Arial, sans-serif">' . $line1 . '</text>'
  . ($line2 ? '<text x="400" y="' . $tY2 . '" font-size="34" font-weight="800" fill="#ffffff" text-anchor="middle" font-family="Segoe UI, Arial, sans-serif">' . $line2 . '</text>' : '') .
  '<text x="400" y="' . $sY . '" font-size="16" fill="rgba(255,255,255,0.85)" text-anchor="middle" font-family="Segoe UI, Arial, sans-serif">Free Online Tool &#8226; ' . $safeSite . '</text>
</svg>';

    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

/* ============================================================
   ARTICLE MATCHER — slug/title se article dhundo
   ============================================================ */
function mta_find_article($slug, $title) {
    global $mta_articles;

    $key = strtolower(trim($slug));

    /* 1. Exact slug match */
    if (isset($mta_articles[$key])) {
        return $mta_articles[$key];
    }

    /* 2. Keyword contains match (e.g. "my-imei-checker" contains "imei") */
    foreach ($mta_articles as $articleSlug => $data) {
        if (strlen($articleSlug) > 4 && strpos($key, $articleSlug) !== false) {
            return $data;
        }
    }

    /* Title keyword match */
    $tl = strtolower($title);
    $titleMap = array(
        'imei valid' => 'imei-validator',
        'imei generat' => 'imei-generator',
        'validator' => 'imei-validator',
        'imei' => 'imei-check',
        'age calculator' => 'age-calculator', 'age' => 'age-calculator',
        'bmi' => 'bmi-calculator',
        'percentage' => 'percentage-calculator',
        'gpa' => 'gpa-calculator',
        'loan' => 'loan-calculator', 'emi' => 'loan-calculator',
        'currency' => 'currency-converter',
        'sip' => 'sip-calculator',
        'income tax' => 'tax-calculator',
        'calorie' => 'calorie-calculator',
        'water intake' => 'water-intake-calculator',
        'ideal weight' => 'ideal-weight-calculator',
        'body fat' => 'body-fat-calculator',
        'cgpa' => 'cgpa-to-percentage',
        'grade' => 'grade-calculator',
        'date difference' => 'date-calculator', 'days between' => 'date-calculator',
        'time zone' => 'time-zone-converter',
        'discount' => 'discount-calculator',
        'tip' => 'tip-calculator',
        'unit converter' => 'unit-converter',
        'random number' => 'random-number-generator',
        'password' => 'password-generator',
        'word counter' => 'word-counter',
        'case converter' => 'case-converter',
        'number to words' => 'number-to-words',
        'duplicate line' => 'duplicate-line-remover',
        'lorem ipsum' => 'lorem-ipsum-generator',
        'reverse' => 'text-reverser',
        'line break' => 'remove-line-breaks',
        'compound interest' => 'compound-interest-calculator',
        'zakat' => 'zakat-calculator',
        'electricity bill' => 'electricity-bill-calculator',
        'love calculator' => 'love-calculator',
        'birthday' => 'birthday-countdown',
        'json' => 'json-formatter',
        'base64' => 'base64-encoder-decoder',
        'url encoder' => 'url-encoder-decoder',
        'hash' => 'hash-generator',
        'uuid' => 'uuid-generator',
        'color converter' => 'color-converter',
    );

    foreach ($titleMap as $keyword => $articleSlug) {
        if (strpos($tl, $keyword) !== false && isset($mta_articles[$articleSlug])) {
            return $mta_articles[$articleSlug];
        }
    }

    /* 3. Category-based fallback (unknown tools like Claude's) */
    return mta_fallback_article($slug, $title);
}

function mta_fallback_article($slug, $title) {
    /* Reuse category detection from essential pages plugin if available */
    if (function_exists('mtp_tool_category_key')) {
        $cat = mtp_tool_category_key($title, $slug);
    } else {
        $cat = 'general';
    }

    global $mta_category_templates;

    if (isset($mta_category_templates[$cat])) {
        $tpl = $mta_category_templates[$cat];
    } else {
        $tpl = $mta_category_templates['general'];
    }

    return array(
        'intro'    => str_replace('{TOOL}', $title, $tpl['intro']),
        'howto'    => $tpl['howto'],
        'features' => $tpl['features'],
        'faqs'     => array_map(function($f) use ($title) {
            return array(
                'q' => str_replace('{TOOL}', $title, $f['q']),
                'a' => str_replace('{TOOL}', $title, $f['a']),
            );
        }, $tpl['faqs']),
        'auto'     => true,
    );
}

/* ============================================================
   EXPANSION ENGINE — generic-but-valuable extra sections
   ============================================================ */
function mta_expansion_sections($title, $article) {
    /* FIX: several closing paragraphs below used to be 100% identical
       text on every single tool page (only the tool name substituted
       in) — verified duplicate across dozens of pages. Google's quality
       guidelines specifically flag exact repeated boilerplate like this
       as thin/auto-generated content. This picks one of several
       differently-worded variants per tool, stable per tool (same tool
       always gets the same variant) but spread out across tools. */
    $pick = function ($variants) use ($title) {
        $idx = abs(crc32($title)) % count($variants);
        return $variants[$idx];
    };

    $cat = isset($article['template']) ? $article['template'] : '';
    if (!$cat && function_exists('mtp_tool_category_key')) {
        $slugGuess = sanitize_title($title);
        $cat = mtp_tool_category_key($title, $slugGuess);
    }

    $t = esc_html($title);
    $sections = array();

    /* Benefits section — features ko paragraphs mein expand karo */
    if (!empty($article['features'])) {
        $featureList = array_map('esc_html', array_slice($article['features'], 0, 5));
        $sections[] = array(
            'h' => 'Key Benefits of Using Our ' . $t,
            'p' => array(
                'Users choose our ' . strtolower($t) . ' over alternatives for several compelling reasons. ' . implode('. ', array_slice($featureList, 0, 2)) . '. These advantages translate directly into time saved and frustration avoided during your daily workflow.',
                $pick(array(
                    'Beyond raw functionality, the experience matters equally. The interface loads instantly, responds smoothly on mobile devices, and never interrupts your work with popups or forced registrations. Everything works exactly as expected from the very first click — a rarity among free online tools today.',
                    'The experience matters just as much as the output — this loads fast, works properly on a phone screen, and never throws a popup or forced sign-up in your way. It just does the job, immediately.',
                    'Speed and a clean interface were the priority here just as much as correctness — fast load, no popups, works the same on mobile as on desktop. That combination is rarer than it should be among free tools.',
                )),
                ($cat === 'finance' || $cat === 'health' || $cat === 'education'
                    ? 'Accuracy receives special attention: calculations follow established formulas used by professionals, and results include contextual breakdowns so you understand not just the answer but why it is correct. This transparency builds confidence in decisions that matter.'
                    : 'Reliability gets engineered in from the start: inputs validate gracefully, edge cases handle predictably, and output formats copy cleanly into whatever destination you need next. Small details compound into tools people bookmark and return to daily.'),
            ),
        );
    }

    /* Common mistakes */
    $mistakes = array(
        'finance' => array(
            'Rounding intermediate values instead of calculating with full precision until the final step — small errors compound significantly in financial math.',
            'Mixing annual and monthly rates without converting properly. An 12% annual rate equals roughly 0.949% monthly when compounded, not simply 1%.',
            'Ignoring fees and taxes that materially change real-world outcomes compared to theoretical calculator outputs.',
        ),
        'health' => array(
            'Entering measurements in mixed units — pounds entered as kilograms (or vice versa) produces wildly wrong results. Always verify unit labels.',
            'Taking single readings as gospel instead of tracking trends across consistent conditions, same time of day.',
            'Comparing your numbers against inappropriate reference groups; age, gender, and activity level all shift healthy ranges meaningfully.',
        ),
        'education' => array(
            'Using rounded grades mid-calculation when official transcripts carry precise values — final GPAs shift at scholarship-deciding margins.',
            'Forgetting credit-hour weighting and computing simple grade averages instead of weighted ones.',
            'Assuming your institution follows standard scales without checking the registrar’s published conversion tables first.',
        ),
        'developer' => array(
            'Pasting sensitive production data into untrusted web tools. Ours processes everything client-side, but verify this claim anywhere you paste credentials or tokens.',
            'Fixing only the first reported error when validators list multiple issues — later errors often cascade from earlier structural problems.',
            'Skipping validation after "obviously safe" manual edits; one stray comma breaks entire payloads silently.',
        ),
    );
    $mKey = isset($mistakes[$cat]) ? $cat : null;

    if ($mKey) {
        $sections[] = array(
            'h' => 'Common Mistakes to Avoid',
            'p' => $mistakes[$mKey],
        );
    } else {
        $sections[] = array(
            'h' => 'Common Mistakes to Avoid',
            'p' => array(
                $pick(array(
                    'Rushing input without double-checking values remains the top error source — a single transposed digit or missed decimal point invalidates otherwise perfect tool logic.',
                    'The most common mistake is not reviewing what you typed before hitting the button — one wrong character changes the whole result even though the tool itself worked correctly.',
                    'Most errors here come from the input, not the tool — a mistyped value or wrong field slips through easily if you do not glance back over it before submitting.',
                )),
                $pick(array(
                    'Ignoring available options that tailor output to your specific situation. Default settings suit general cases; exploring dropdowns and checkboxes frequently reveals exactly what you needed.',
                    'Sticking with default settings without checking what the other options do — the dropdowns and toggles usually exist because different situations genuinely need different settings.',
                    'Skipping past the extra options because the default already "works" — often one of those settings matches your exact case better than the default does.',
                )),
                $pick(array(
                    'Forgetting to copy results before navigating away. Our tools keep nothing stored by design for privacy protection, so capture important outputs immediately.',
                    'Navigating away before saving the result — nothing is stored on our end by design (that is the privacy trade-off), so copy what you need before you leave the page.',
                    'Closing the tab right after getting a result without copying it first. Since privacy means we do not save anything server-side, that result only exists on your screen until you copy it.',
                )),
            ),
        );
    }

    /* Who should use */
    $audiences = array(
        'finance'   => 'Students learning financial concepts, working professionals planning major purchases, families budgeting household expenses, freelancers managing irregular income, and small business owners evaluating financing options all find practical value here daily.',
        'health'    => 'Fitness beginners establishing baselines, weight-management patients tracking doctor-recommended metrics, athletes monitoring training effects, and health-curious individuals understanding their bodies better all benefit from instant private calculations.',
        'education' => 'High school and university students verifying grades, parents helping children plan academics, teachers estimating class statistics, and academic advisors guiding course-load decisions rely on quick accurate educational math.',
        'developer' => 'Frontend developers debugging API responses, backend engineers validating payloads, QA testers crafting edge-case inputs, students learning data formats, and technical writers documenting examples all streamline workflows with browser-based utilities.',
        'text'      => 'Content writers polishing drafts, editors enforcing style consistency, social media managers formatting posts, students preparing submissions, and developers normalizing identifiers all process text faster with purpose-built helpers.',
    );
    $sections[] = array(
        'h' => 'Who Should Use This Tool?',
        'p' => array(
            isset($audiences[$cat]) ? $audiences[$cat] : 'Anyone needing quick reliable answers without software installs benefits here — students, professionals, hobbyists, and curious minds alike. The barrier-free design welcomes complete beginners while delivering precision experienced users demand.',
            $pick(array(
                'Because everything runs locally in your browser, privacy-conscious users get professional capability without uploading personal information anywhere. Public WiFi, shared computers, restricted networks — the tool works wherever browsers work.',
                'Since nothing gets uploaded to a server, this works just as well on a shared office computer as it does on your personal phone over public WiFi — there is nothing sensitive to intercept in the first place.',
                'The local-only processing means it works the same everywhere — locked-down work networks, shared library computers, personal devices — without any of them ever seeing your actual data.',
            )),
            $pick(array(
                'Best of all, there is zero learning curve. If you can type numbers and click buttons, you already possess every skill required. Bookmark it once and consider the job permanently handled.',
                'There is nothing to learn here — if you can fill in a field and press a button, you already know how to use this. Bookmark it once and you are set for good.',
                'No tutorial needed: the whole tool is self-explanatory within a few seconds of looking at it. Save the page once and it is ready whenever you need it again.',
            )),
        ),
    );

    /* Conclusion */
    $sections[] = array(
        'h' => 'Final Thoughts',
        'p' => array(
            $pick(array(
                'Free online tools succeed when they respect three things: your time, your privacy, and your intelligence. This ' . strtolower($t) . ' delivers all three — instant accurate results, local-only processing, and transparent methodology you can verify against any authoritative source.',
                'A good browser-based tool should feel invisible — you think about your task, not the tool itself. That is the goal behind this ' . strtolower($t) . ': fast enough to forget it is loading, accurate enough to trust without double-checking, and private enough that you never worry about what happens to your input.',
                'What separates a genuinely useful ' . strtolower($t) . ' from a gimmick is consistency — the same reliable output every time, with nothing hidden about how it works. That is exactly what this tool is built around.',
            )),
            $pick(array(
                'We continuously refine our tools based on user feedback. Found something confusing or have a feature suggestion? Reach out through our contact page — real humans read every message, and many of our best improvements started as user suggestions.',
                'This tool keeps improving over time based on how people actually use it. If something feels off or you have an idea for a feature it is missing, our contact page reaches a real person, not a ticket queue.',
                'Feedback shapes every update we make. If this tool could do something better for your workflow, let us know through the contact page — that is genuinely how most of our improvements get decided.',
            )),
            $pick(array(
                'Ready to try it? Scroll back up, enter your details, and see why thousands of users keep this page bookmarked as their go-to ' . strtolower($t) . ' resource.',
                'Go ahead and give it a try — scroll up, enter your details, and see the result for yourself. Most people bookmark this page after the first use.',
                'The best way to judge a tool is to use it once. Scroll back up and try it with your own numbers or text — that is usually all it takes to see why people come back to this ' . strtolower($t) . '.',
            )),
        ),
    );

    return $sections;
}

/* ============================================================
   WORD COUNT HELPER
   ============================================================ */
function mta_count_words($text) {
    return str_word_count(strip_tags($text));
}
// add_filter('the_content', 'mta_append_article', 90);
function mta_append_article($content) {
    if (is_admin() || is_feed() || is_embed()) { return $content; }
    if (!is_singular('page')) { return $content; }
    if (is_front_page()) { return $content; }
    if (!in_the_loop() || !is_main_query()) { return $content; }

    global $post;
    if (!$post) { return $content; }

    /* Essential pages skip */
    $excluded = array(
        'home', 'homepage', 'front-page',
        'about', 'contact', 'contact-us',
        'privacy-policy', 'privacy',
        'terms', 'terms-and-conditions', 'terms-of-service',
        'blog', 'news', 'sample-page',
        'cart', 'checkout', 'my-account', 'shop',
    );
    $excluded = apply_filters('mta_excluded_pages', $excluded);

    if (in_array($post->post_name, $excluded, true)) { return $content; }
    if (strpos($content, '[mt_all_tools]') !== false) { return $content; }

    $article = mta_find_article($post->post_name, $post->post_title);
    if (!$article) { return $content; }

    $emoji = '&#128295;';
    if (function_exists('mtp_tool_emoji')) {
        $emoji = mtp_tool_emoji($post->post_title, $post->post_name);
    }

    $bannerUrl = mta_generate_banner($post->post_title, html_entity_decode($emoji, ENT_QUOTES, 'UTF-8'));
    $site = get_bloginfo('name');

    ob_start();
    ?>
    <div class="mta-article">

        <!-- Banner Image -->
        <div class="mta-banner">
            <img src="<?php echo esc_attr($bannerUrl); ?>" alt="<?php echo esc_attr($post->post_title . ' - Free Online Tool | ' . $site); ?>" width="800" height="300" loading="lazy" />
        </div>

        <!-- About Section -->
        <h2 class="mta-heading">&#128218; About <?php echo esc_html($post->post_title); ?></h2>
        <p><?php echo esc_html($article['intro']); ?></p>
        <p><?php
            // FIX: this line was 100% identical on every single tool page —
            // rotating a few phrasings per tool (stable per tool via hash)
            // instead of one fixed sentence repeated site-wide.
            $aboutVariants = array(
                'Our ' . strtolower($post->post_title) . ' works directly in your browser — no downloads, no sign-up, and no data leaves your device. It is completely free forever, whether you use it once a year or a hundred times a day.',
                'This ' . strtolower($post->post_title) . ' runs entirely client-side — nothing you type or upload is sent anywhere. No account, no install, no cost, now or later.',
                'Everything happens locally in your browser when you use this ' . strtolower($post->post_title) . '. There is no sign-up wall and no hidden fee — it stays free whether this is your first visit or your hundredth.',
            );
            $idx = abs(crc32($post->post_title)) % count($aboutVariants);
            echo esc_html($aboutVariants[$idx]);
        ?></p>

        <!-- How to Use -->
        <h2 class="mta-heading">&#128221; How to Use</h2>
        <ol class="mta-steps">
            <?php foreach ($article['howto'] as $step) : ?>
            <li><?php echo esc_html($step); ?></li>
            <?php endforeach; ?>
        </ol>

        <!-- Key Features -->
        <h2 class="mta-heading">&#11088; Key Features</h2>
        <ul class="mta-features">
            <?php foreach ($article['features'] as $feature) : ?>
            <li><?php echo esc_html($feature); ?></li>
            <?php endforeach; ?>
        </ul>

        <!-- LONG-FORM SEO SECTIONS -->
        <?php
        /* 1. Custom long-form (agar available hai) */
        $longform = mta_get_longform();
        $slugKey = strtolower($post->post_name);
        $customLong = isset($longform[$slugKey]) ? $longform[$slugKey]['sections'] : null;

        if (!$customLong) {
            foreach ($longform as $lfKey => $lfData) {
                if (strlen($lfKey) > 4 && strpos($slugKey, $lfKey) !== false) {
                    $customLong = $lfData['sections'];
                    break;
                }
            }
        }

        /* 2. Expansion engine — hamesha extra sections add karo */
        $expansion = mta_expansion_sections($post->post_title, $article);

        $allSections = array();
        if ($customLong) { $allSections = $customLong; }
        $allSections = array_merge($allSections, $expansion);

        foreach ($allSections as $sec) : ?>
        <h2 class="mta-heading"><?php echo esc_html($sec['h']); ?></h2>
        <?php foreach ($sec['p'] as $para) : ?>
        <p><?php echo esc_html($para); ?></p>
        <?php endforeach; ?>
        <?php endforeach; ?>

        <!-- Why Us -->
        <h2 class="mta-heading">&#10024; Why Choose Our <?php echo esc_html($post->post_title); ?>?</h2>
        <p>There are many online tools available, but here is why thousands of users trust <strong><?php echo esc_html($site); ?></strong>:</p>
        <ul class="mta-features">
            <li><strong>Instant Results:</strong> No waiting — results appear the moment you click.</li>
            <li><strong>No Registration:</strong> Start using immediately without creating an account.</li>
            <li><strong>Cross-Platform:</strong> Works perfectly on mobile, tablet, and desktop.</li>
            <li><strong>Regular Updates:</strong> We continuously improve our tools based on user feedback.</li>
        </ul>

        <!-- FAQ -->
        <h2 class="mta-heading">&#10067; Frequently Asked Questions</h2>
        <div class="mta-faqs">
            <?php foreach ($article['faqs'] as $faq) : ?>
            <details class="mta-faq-item">
                <summary><?php echo esc_html($faq['q']); ?></summary>
                <div class="mta-faq-answer"><p><?php echo esc_html($faq['a']); ?></p></div>
            </details>
            <?php endforeach; ?>
        </div>

        <p class="mta-disclaimer">* This tool is provided for informational purposes. Always verify important results with official sources.</p>
    </div>

    <?php /* FAQ Schema for Google Rich Results */ ?>
    <script type="application/ld+json">
    <?php
    $schemaFaq = array();
    foreach ($article['faqs'] as $faq) {
        $schemaFaq[] = array(
            '@type'          => 'Question',
            'name'           => wp_strip_all_tags($faq['q']),
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => wp_strip_all_tags($faq['a']),
            ),
        );
    }
    echo wp_json_encode(array(
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => $schemaFaq,
    ));
    ?>
    </script>
    <?php
    $html = ob_get_clean();

    return $content . $html;
}

/* ================= ADMIN INFO PAGE ================= */
add_action('admin_menu', 'mta_admin_menu', 10);
function mta_admin_menu() {
    /* FIX: this pointed to parent slug 'mtp-pages', which no module in
       this plugin actually registers — the page was silently missing
       from the admin sidebar entirely, and the "Open" link on the
       Master Dashboard pointed at a dead URL. Now attached to the
       shared "iLoveTool3 Suite" parent menu like every other module. */
    add_submenu_page('imei-master', 'Tool Articles', 'Tool Articles', 'manage_options', 'mta-info', 'mta_admin_page');
}

function mta_admin_page() {
    global $wpdb;
    $pages = get_pages(array('sort_column' => 'post_title', 'hierarchical' => 0, 'number' => 999));
    $customCount = 0;
    $autoCount = 0;
    $rows = '';

    $excluded = array('home','homepage','front-page','about','contact','contact-us','privacy-policy','privacy','terms','terms-and-conditions','terms-of-service','blog','news','sample-page','cart','checkout','my-account','shop');

    foreach ($pages as $page) {
        if (in_array($page->post_name, $excluded, true)) { continue; }
        $art = mta_find_article($page->post_name, $page->post_title);
        $isCustom = empty($art['auto']);
        if ($isCustom) { $customCount++; } else { $autoCount++; }

        /* Word count estimate */
        $words = mta_count_words($art['intro'] ?? '');
        foreach (($art['howto'] ?? array()) as $s) { $words += str_word_count($s); }
        foreach (($art['features'] ?? array()) as $f) { $words += str_word_count($f); }
        foreach (($art['faqs'] ?? array()) as $f) { $words += str_word_count($f['q']) + str_word_count($f['a']); }
        foreach (mta_expansion_sections($page->post_title, $art) as $sec) {
            $words += str_word_count($sec['h']);
            foreach ($sec['p'] as $pp) { $words += str_word_count($pp); }
        }
        /* Long-form check */
        $longform = mta_get_longform();
        $sk = strtolower($page->post_name);
        if (isset($longform[$sk])) {
            foreach ($longform[$sk]['sections'] as $sec) {
                $words += str_word_count($sec['h']);
                foreach ($sec['p'] as $pp) { $words += str_word_count($pp); }
            }
        }

        $wcColor = $words >= 1800 ? '#059669' : ($words >= 1000 ? '#d97706' : '#dc2626');
        $badge = $isCustom
            ? '<span style="color:green;">&#9989; Custom</span>'
            : '<span style="color:#b45309;">&#128260; Auto</span>';
        $rows .= '<tr><td><strong>' . esc_html($page->post_title) . '</strong></td><td><code>/' . esc_html($page->post_name) . '/</code></td><td>' . $badge . '</td><td><strong style="color:' . $wcColor . ';">~' . number_format($words) . ' words</strong></td><td><a href="' . esc_url(get_permalink($page->ID)) . '" target="_blank">view</a></td></tr>';
    }
    ?>
    <div class="wrap">
        <h1>&#128221; Tool Articles Status</h1>
        <p>Har tool page ke niche automatic long-form SEO article + image + FAQ add hota hai.</p>

        <div style="display:flex;gap:15px;margin:20px 0;">
            <div style="background:#ecfdf5;border:1px solid #a7f3d0;border-radius:10px;padding:20px 30px;">
                <strong style="font-size:26px;color:#047857;"><?php echo $customCount; ?></strong><br/>Custom Data Articles
            </div>
            <div style="background:#fff8e5;border:1px solid #fde68a;border-radius:10px;padding:20px 30px;">
                <strong style="font-size:26px;color:#b45309;"><?php echo $autoCount; ?></strong><br/>Auto Templates
            </div>
        </div>

        <table class="widefat striped">
            <thead><tr><th>Page</th><th>URL</th><th>Type</th><th>Article Length</th><th>&nbsp;</th></tr></thead>
            <tbody><?php echo $rows; ?></tbody>
        </table>

        <div style="background:#fff8e5;border:1px solid #ffd97d;padding:15px 20px;border-radius:6px;margin-top:20px;">
            <h3>&#128161; Info</h3>
            <ul style="list-style:disc;margin-left:20px;">
                <li><strong>Custom Article</strong> = us tool ke liye specially likha gaya unique article</li>
                <li><strong>Auto Template</strong> = category ke hisab se smart generated article (naye/unknown tools)</li>
                <li>Har article ke saath automatic banner image + FAQ Schema (Google rich results) bhi jaata hai</li>
                <li>Koi bhi naya page banao — article khud lag jayega!</li>
            </ul>
        </div>
    </div>
    <?php
}
