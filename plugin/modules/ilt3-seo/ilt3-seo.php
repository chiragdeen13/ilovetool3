<?php
/**
 * Module: iLoveTool3 SEO
 *
 * Centralised technical-SEO layer for the whole site. This module does NOT
 * change any tool behaviour, markup of the tools themselves, routing, or
 * content of existing pages. It only controls what goes into <head>, what
 * search engines are told (robots/sitemap/canonical), and adds JSON-LD.
 *
 * WHY THIS MODULE EXISTS
 * ----------------------
 * Before this, SEO meta was emitted by modules/mt-tool-articles/includes/
 * tool-seo-meta.php, which printed a raw <title> tag inside wp_head. Because
 * WordPress (and every modern theme, including Astra) already prints its own
 * <title> via the title-tag theme support, every tool page was shipping TWO
 * <title> elements. Browsers/crawlers keep the first one, so the carefully
 * written SEO titles in that file were mostly being ignored, and audits flag
 * it as a duplicate-title error.
 *
 * The hand-written title/description table in tool-seo-meta.php is still the
 * source of truth — this module reads it (mta_seo_meta()) and applies it the
 * correct WordPress way, via the pre_get_document_title filter.
 *
 * SAFETY
 * ------
 * If a dedicated SEO plugin (Yoast, Rank Math, All in One SEO, SEOPress, The
 * SEO Framework) is ever activated on this site, this module steps aside
 * completely (see ILT3_SEO::seo_plugin_active) so the site never ends up with
 * two sets of meta tags again.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ILT3_SEO_PATH', plugin_dir_path( __FILE__ ) );
define( 'ILT3_SEO_URL', plugin_dir_url( __FILE__ ) );
define( 'ILT3_SEO_VERSION', '1.1.0' );

require_once ILT3_SEO_PATH . 'includes/class-ilt3-seo.php';
require_once ILT3_SEO_PATH . 'includes/class-ilt3-seo-meta.php';
require_once ILT3_SEO_PATH . 'includes/class-ilt3-seo-schema.php';
require_once ILT3_SEO_PATH . 'includes/class-ilt3-seo-crawl.php';
require_once ILT3_SEO_PATH . 'includes/class-ilt3-seo-performance.php';
require_once ILT3_SEO_PATH . 'includes/class-ilt3-seo-headings.php';
require_once ILT3_SEO_PATH . 'includes/class-ilt3-seo-settings.php';
require_once ILT3_SEO_PATH . 'includes/class-ilt3-seo-social.php';
require_once ILT3_SEO_PATH . 'includes/class-ilt3-seo-analytics.php';
require_once ILT3_SEO_PATH . 'includes/class-ilt3-seo-llms.php';

function ilt3_seo_init() {
	// Performance + crawl directives are safe to run even if another SEO
	// plugin owns the meta tags, so they load unconditionally.
	new ILT3_SEO_Performance();
	new ILT3_SEO_Crawl();
	new ILT3_SEO_Settings();

	/* The duplicate-H1 fixer was previously gated behind the same
	   seo_plugin_active() check below, which was a mistake: it exists to
	   fix a theme problem (Astra prints its own <h1> in addition to the
	   page's real heading), not a meta-tag problem. Yoast/Rank Math/etc.
	   don't touch H1 markup at all, so there is no risk of duplicating
	   anything by running this fixer even when one of them is active — and
	   on this site (Yoast SEO is installed and active) leaving it gated
	   here meant it silently never ran at all. It is instantiated
	   unconditionally now. */
	new ILT3_SEO_Headings();

	// Independent of any SEO plugin: social profile links / sameAs (it hooks
	// into Yoast's schema filter when Yoast is active), the optional GA4
	// tag, and the llms.txt guide for AI assistants.
	new ILT3_SEO_Social();
	new ILT3_SEO_Analytics();
	new ILT3_SEO_LLMs();

	if ( ILT3_SEO::seo_plugin_active() ) {
		return;
	}

	new ILT3_SEO_Meta();
	new ILT3_SEO_Schema();
	/* v1.1: a second `new ILT3_SEO_Headings()` used to sit here. When no
	   SEO plugin was active it registered the output buffer twice for every
	   page view. The instance above is created unconditionally and is the
	   only one needed. */
}
add_action( 'plugins_loaded', 'ilt3_seo_init', 20 );
