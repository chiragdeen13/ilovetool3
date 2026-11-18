<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Serves /llms.txt — a plain-text, Markdown-formatted site guide for AI
 * assistants and LLM crawlers (the llms.txt convention: a title, a short
 * summary, then sections of "- [name](url): description" links).
 *
 * It is generated from what the site really contains — the same tool list
 * that builds the homepage grid, grouped by the same categories — so it can
 * never drift out of date or promise tools that don't exist. The result is
 * cached for an hour and cleared whenever a page is saved or deleted.
 *
 * If a real llms.txt file is ever uploaded to the site root, the web server
 * serves that file directly and this class is never reached.
 * Can be switched off in iLoveTool3 Suite → SEO Settings.
 */
class ILT3_SEO_LLMs {

	const TRANSIENT = 'ilt3_llms_txt';

	public function __construct() {
		add_action( 'template_redirect', array( $this, 'maybe_serve' ), 0 );
		add_action( 'save_post', array( $this, 'flush_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_cache' ) );
		add_action( 'update_option_' . ILT3_SEO::OPTION_NAME, array( $this, 'flush_cache' ) );
	}

	public function flush_cache() {
		delete_transient( self::TRANSIENT );
	}

	private function is_llms_request() {
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}
		$uri  = (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH );
		$want = (string) wp_parse_url( home_url( '/llms.txt' ), PHP_URL_PATH );
		return untrailingslashit( $uri ) === untrailingslashit( $want );
	}

	public function maybe_serve() {
		if ( ! ILT3_SEO::option( 'llms_txt' ) || ! get_option( 'blog_public' ) ) {
			return;
		}
		if ( is_admin() || ! $this->is_llms_request() ) {
			return;
		}

		$body = get_transient( self::TRANSIENT );
		if ( ! is_string( $body ) || '' === $body ) {
			$body = $this->build();
			set_transient( self::TRANSIENT, $body, HOUR_IN_SECONDS );
		}

		status_header( 200 );
		header( 'Content-Type: text/plain; charset=utf-8' );
		header( 'Cache-Control: public, max-age=3600' );
		echo $body; // Plain-text response (Markdown), not HTML.
		exit;
	}

	/** Strip the leading emoji from a category name like "💰 Finance & Money Tools". */
	private function clean_label( $label ) {
		$label = preg_replace( '/^[^\p{L}\p{N}]+/u', '', (string) $label );
		return trim( wp_strip_all_tags( $label ) );
	}

	/** One-line text: no newlines, no Markdown-breaking brackets. */
	private function one_line( $text ) {
		$text = wp_strip_all_tags( (string) $text );
		$text = preg_replace( '/\s+/u', ' ', $text );
		return trim( str_replace( array( '[', ']' ), array( '(', ')' ), $text ) );
	}

	private function build() {
		$site = $this->one_line( get_bloginfo( 'name' ) );
		$out  = array();

		$out[] = '# ' . $site;
		$out[] = '';
		$out[] = '> ' . $this->one_line( ILT3_SEO::default_home_description() );
		$out[] = '';
		$out[] = 'Every tool runs in the browser, is free to use, and needs no account. Each tool page also explains the method or formula behind its result.';
		$out[] = '';

		// Hand-written descriptions, where they exist.
		$curated = function_exists( 'mta_seo_meta' ) ? mta_seo_meta() : array();

		if ( class_exists( 'QCalc_Catalog' ) ) {
			$pages   = QCalc_Catalog::get_categorized_pages();
			$grouped = array();
			foreach ( $pages as $page ) {
				$grouped[ $page['category'] ][] = $page;
			}

			foreach ( QCalc_Catalog::get_category_icons() as $cat_name ) {
				if ( empty( $grouped[ $cat_name ] ) ) {
					continue;
				}
				$out[] = '## ' . $this->clean_label( $cat_name );
				$out[] = '';
				foreach ( $grouped[ $cat_name ] as $tool ) {
					$line = '- [' . $this->one_line( $tool['name'] ) . '](' . esc_url_raw( home_url( '/' . $tool['slug'] . '/' ) ) . ')';
					if ( ! empty( $curated[ $tool['slug'] ]['meta'] ) ) {
						$line .= ': ' . $this->one_line( $curated[ $tool['slug'] ]['meta'] );
					}
					$out[] = $line;
				}
				$out[] = '';
			}
		}

		// Site information pages, only the ones that exist.
		$info  = array();
		$slots = array(
			'About'   => array( 'about-us', 'about' ),
			'Contact' => array( 'contact-us', 'contact' ),
			'Privacy' => array( 'privacy', 'privacy-policy' ),
			'Terms'   => array( 'terms-and-conditions', 'terms-of-service', 'terms' ),
		);
		foreach ( $slots as $label => $slugs ) {
			foreach ( $slugs as $slug ) {
				$page = get_page_by_path( $slug );
				if ( $page && 'publish' === $page->post_status ) {
					$info[] = '- [' . $label . '](' . esc_url_raw( get_permalink( $page ) ) . ')';
					break;
				}
			}
		}
		if ( ! empty( $info ) ) {
			$out[] = '## Site information';
			$out[] = '';
			$out   = array_merge( $out, $info );
			$out[] = '';
		}

		return implode( "\n", $out ) . "\n";
	}
}
