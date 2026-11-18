<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Google Analytics 4 (gtag.js), off until a Measurement ID is entered.
 *
 * The site already has no analytics code of its own, so audits report
 * "no analytics tool detected". Paste a GA4 Measurement ID (looks like
 * G-XXXXXXXXXX) into iLoveTool3 Suite → SEO Settings and the standard
 * Google tag is added to every public page.
 *
 * Logged-in administrators are not tracked, so your own visits and
 * editing sessions don't pollute the numbers.
 *
 * IMPORTANT: if analytics is already being loaded by another plugin
 * (Site Kit, MonsterInsights, a header/footer snippet, ...) leave the
 * field empty — adding it twice would double-count every visit.
 */
class ILT3_SEO_Analytics {

	public function __construct() {
		add_action( 'wp_head', array( $this, 'render' ), 4 );
	}

	/** A GA4 ID is "G-" followed by letters/digits. Anything else is ignored. */
	public static function valid_id( $id ) {
		return (bool) preg_match( '/^G-[A-Z0-9]{6,20}$/', (string) $id );
	}

	public function render() {
		if ( is_admin() || is_feed() || is_embed() || is_customize_preview() ) {
			return;
		}
		if ( current_user_can( 'manage_options' ) ) {
			return;
		}

		$id = strtoupper( trim( (string) ILT3_SEO::option( 'ga4_id' ) ) );
		if ( ! self::valid_id( $id ) ) {
			return;
		}

		echo "\n<!-- Google tag (gtag.js) -->\n";
		echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . esc_attr( $id ) . '"></script>' . "\n";
		echo '<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}'
			. "gtag('js',new Date());gtag('config'," . wp_json_encode( $id ) . ');</script>' . "\n";
	}
}
