<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Conservative front-end performance work. Nothing here removes a feature
 * a visitor can see or use.
 *
 * THE EMOJI SCRIPT (this is the "102 images" in the audit)
 * -------------------------------------------------------
 * WordPress ships wp-emoji-release.min.js on every page. It scans the DOM
 * and swaps native emoji characters for <img> tags served from s.w.org.
 * This site's UI is full of emoji — every category heading, every card icon,
 * every badge — so an audit crawling the homepage sees ~100 external images
 * it can't attribute, plus a render-blocking inline script and a
 * dns-prefetch to a third-party domain.
 *
 * Disabling it removes all of that. The emoji themselves still display
 * perfectly: every browser and OS this site supports renders them natively
 * from the system font. Nothing in the UI changes visually on any modern
 * device.
 */
class ILT3_SEO_Performance {

	public function __construct() {
		add_action( 'init', array( $this, 'maybe_disable_emojis' ) );
		add_action( 'init', array( $this, 'maybe_clean_head' ) );
		add_action( 'wp_head', array( $this, 'resource_hints' ), 1 );
		add_filter( 'the_content', array( $this, 'improve_content_images' ), 99 );
	}

	public function maybe_disable_emojis() {
		if ( ! ILT3_SEO::option( 'disable_emojis' ) ) {
			return;
		}

		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		add_filter( 'emoji_svg_url', '__return_false' );

		// Drop the matching dns-prefetch to s.w.org.
		add_filter(
			'wp_resource_hints',
			function ( $urls, $relation ) {
				if ( 'dns-prefetch' !== $relation ) {
					return $urls;
				}
				return array_filter(
					$urls,
					function ( $url ) {
						$value = is_array( $url ) && isset( $url['href'] ) ? $url['href'] : $url;
						return false === strpos( (string) $value, 's.w.org' );
					}
				);
			},
			10,
			2
		);
	}

	/**
	 * Remove head tags that serve no purpose on this site and only add
	 * bytes (and, in the generator tag's case, advertise the WP version).
	 * Deliberately does NOT touch RSS feed links, shortlinks or oEmbed
	 * discovery, which can be genuinely useful.
	 */
	public function maybe_clean_head() {
		if ( ! ILT3_SEO::option( 'clean_head' ) ) {
			return;
		}
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'rsd_link' );
	}

	/**
	 * Preconnect to the AdSense origin only when an AdSense client is
	 * actually configured. This shaves the TLS handshake off the critical
	 * path for the ad script that's already being loaded anyway — it does
	 * not add any new third-party request.
	 */
	public function resource_hints() {
		if ( is_admin() ) {
			return;
		}

		$ads = get_option( 'ilt3_ad_settings', array() );
		if ( is_array( $ads ) && ! empty( $ads['adsense_client'] ) ) {
			echo '<link rel="preconnect" href="https://pagead2.googlesyndication.com" crossorigin />' . "\n";
		}
	}

	/**
	 * Adds decoding="async" and, where missing, loading="lazy" to images
	 * inside post content. Both are inert attributes — they cannot break
	 * an image that already works, and they keep image decoding off the
	 * main thread.
	 */
	public function improve_content_images( $content ) {
		if ( is_admin() || is_feed() || empty( $content ) || false === strpos( $content, '<img' ) ) {
			return $content;
		}

		return preg_replace_callback(
			'/<img\b([^>]*)>/i',
			function ( $matches ) {
				$attrs = $matches[1];

				if ( false === stripos( $attrs, 'decoding=' ) ) {
					$attrs .= ' decoding="async"';
				}
				if ( false === stripos( $attrs, 'loading=' ) ) {
					$attrs .= ' loading="lazy"';
				}

				return '<img' . $attrs . '>';
			},
			$content
		);
	}

}
