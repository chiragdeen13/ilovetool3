<?php
/*
Plugin Name: iLoveTool3 Suite
Plugin URI: https://ilovetool3.com
Description: Merged plugin for Ilovetool3.com — combines IMEI/Phone tools, iLoveTool3 Calculators, MultyTools, MT Tool Articles (SEO), Quick Calculators (homepage/catalog/related-tools engine), and MT PDF Tools into a single plugin. Replaces the 5 separate plugins that used to run these features.
Version: 1.3.0
Author: iLoveTool3
Author URI: https://ilovetool3.com
License: GPL v2 or later
Text Domain: ilovetool3-suite

------------------------------------------------------------------
CHANGES IN 1.3.0 (fixes from the SEOptimer audit of 19 Sep 2026)
------------------------------------------------------------------
- JavaScript error fixed: imei-check-services/assets/js/script.js used the
  octal literal `01`, a SyntaxError in strict mode. Because that script loads
  on every page, the whole file failed to run everywhere. (IMEI_VERSION 3.0.1
  so browsers/CDNs fetch the fixed file.)
- Heading outline: the theme's duplicate "Home" title used to become an H2
  ABOVE the H1. Extra H1s that come before the kept H1 are now plain <div>s;
  homepage category headings are now H3s under the "Browse All" H2, and the
  About section gained H3 sub-headings + an FAQ. Homepage text is ~1,060
  words (was ~645).
- Alt text: images with no alt attribute get one automatically (output pass).
- New in SEO Settings: social profile URLs (footer links + sameAs schema,
  works with Yoast), optional GA4 Measurement ID, llms.txt on/off, alt-text
  on/off. Nothing is output for fields left blank.
- New /llms.txt guide for AI assistants, generated from the real tool list.
- Removed a duplicate ILT3_SEO_Headings instance that ran when no SEO plugin
  was active.
------------------------------------------------------------------
MERGE NOTES (read this before activating on a live site)
------------------------------------------------------------------
This plugin combines these 6 previously-separate plugins, unchanged
in behaviour except for one fix described below:
  - imei-check-services   (IMEI tools + master dashboard)
  - it3-calculators        (18 finance/education/misc calculators)
  - multytools              (17 text/dev/fun tools)
  - mt-tool-articles        (SEO articles injected under tool pages)
  - quick-calculators       (homepage, tool catalog, view counter,
                              related tools, and its own 25 calculators —
                              this is the plugin driving your homepage)
  - mt-pdf-tools             (20-in-1 PDF converter, previously fixed)

DUPLICATE SHORTCODE FIX
------------------------------------------------------------------
Before merging, these 6 plugins were ALL active together and 11
shortcode names were registered by two different plugins each:
  age_calculator, bmi_calculator, percentage_calculator,
  gpa_calculator, ideal_weight_calculator, loan_calculator,
  discount_calculator, tip_calculator, compound_interest_calculator,
  word_counter, password_generator

Because quick-calculators registers its shortcodes on the
`plugins_loaded` hook (which fires after every plugin file has
already been read) while the other plugins registered theirs
immediately at load time, quick-calculators' versions were ALWAYS
the ones actually rendering on your site — the versions in
it3-calculators / multytools / imei-check-services for those 11
tools were dead code, never shown to visitors, regardless of which
plugin "looked" like it owned that shortcode.

In this merged plugin, those 11 duplicate registrations have been
commented out (search for "[merged: duplicate" in the module files)
so there is exactly one implementation per shortcode — the
quick-calculators one, matching what was actually live before. No
tool page's behavior should change. The underlying functions were
left in place (not deleted) because it3-calculators' [it3_calculators]
and multytools' [mt_all] combined shortcodes still call them directly.

WHAT TO DO AFTER UPLOADING
------------------------------------------------------------------
1. Upload/install this plugin.
2. Deactivate the 6 old plugins (imei-check-services, it3-calculators,
   multytools, mt-tool-articles, quick-calculators, mt-pdf-tools).
3. Activate "iLoveTool3 Suite".
4. Spot-check a handful of tool pages, especially the 11 shortcodes
   listed above and your homepage.
5. Only delete the old plugin folders after you've confirmed the
   site looks right — deactivating first (not deleting) means you
   can roll back instantly if something looks off.
------------------------------------------------------------------
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ILT3_SUITE_PATH', plugin_dir_path( __FILE__ ) );
define( 'ILT3_SUITE_URL', plugin_dir_url( __FILE__ ) );
define( 'ILT3_SUITE_VERSION', '1.3.0' );

/* Each module keeps its own folder structure (its own assets/, includes/,
   and its own plugin_dir_path(__FILE__)-based constants), so nothing about
   how it loads its CSS/JS/sub-files needs to change — only where the
   top-level file itself lives changed, from wp-content/plugins/<name>/
   to wp-content/plugins/ilovetool3-suite/modules/<name>/. */
require_once ILT3_SUITE_PATH . 'modules/ad-manager/ad-manager.php';
require_once ILT3_SUITE_PATH . 'modules/quick-calculators/quick-calculators.php';
require_once ILT3_SUITE_PATH . 'modules/imei-check-services/imei-check-services.php';
require_once ILT3_SUITE_PATH . 'modules/it3-calculators/it3-calculators.php';
require_once ILT3_SUITE_PATH . 'modules/multytools/multytools.php';
require_once ILT3_SUITE_PATH . 'modules/mt-tool-articles/mt-tool-articles.php';
require_once ILT3_SUITE_PATH . 'modules/mt-pdf-tools/mt-pdf-tools.php';
require_once ILT3_SUITE_PATH . 'modules/ilt3-blog-generator/ilt3-blog-generator.php';

/* Technical SEO layer: titles, meta descriptions, canonicals, robots
   directives, Open Graph / Twitter cards, JSON-LD structured data,
   robots.txt, XML sitemap tuning, and the single-H1 guarantee.

   Loaded last on purpose — it reads the hand-written SEO copy from
   mt-tool-articles (mta_seo_meta) and the category rules from
   quick-calculators (QCalc_Catalog), so both must be defined first. */
require_once ILT3_SUITE_PATH . 'modules/ilt3-seo/ilt3-seo.php';

/* imei-check-services used to flush rewrite rules on its own activation
   hook (register_activation_hook against its own __FILE__). That hook
   no longer fires because that file isn't a standalone plugin anymore,
   so it's replicated here against the merged plugin's real file. */
register_activation_hook( __FILE__, function () {
	flush_rewrite_rules();
	ilt3_suite_ensure_contact_page();
} );

/* FIX: the site had no "Contact Us" page at all — About dropdown and
   footer only ever showed 2 links (About, Privacy) because there was
   nothing to find for Contact. This creates a simple one automatically
   if it's missing (checked on every admin page load, not just on
   activation, so it also self-heals after updating this plugin without
   needing a manual deactivate/reactivate). The page's content can be
   edited normally afterwards in Pages → Contact Us like any other page. */
function ilt3_suite_ensure_contact_page() {
	if ( get_page_by_path( 'contact-us' ) || get_page_by_path( 'contact' ) ) {
		return;
	}
	$email = get_option( 'admin_email' );
	wp_insert_post( array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => 'Contact Us',
		'post_name'    => 'contact-us',
		'post_content' => '<p>Have a question, found a bug, or want to suggest a new tool? Email us at '
			. '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a> '
			. 'and we will get back to you.</p>',
	) );
}
add_action( 'admin_init', 'ilt3_suite_ensure_contact_page' );
