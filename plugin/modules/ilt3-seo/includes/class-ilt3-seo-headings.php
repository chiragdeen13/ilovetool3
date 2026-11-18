<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Guarantees exactly one <h1> per rendered page.
 *
 * WHY THIS IS DONE AT THE OUTPUT LEVEL
 * ------------------------------------
 * The second H1 on this site does not come from this plugin — it comes from
 * the active theme, which prints the page title as an <h1> in its entry
 * header. On the homepage that collides with the hero H1 rendered by
 * [tools_homepage]; on some tool pages it collides with an H1 inside a
 * shortcode. The plugin cannot edit the theme's template, and hiding the
 * theme title with CSS would leave the duplicate H1 in the HTML (which is
 * what crawlers read) while removing it visually — the worst of both.
 *
 * So: a single, narrowly scoped output filter demotes the *extra* H1s to
 * H2. Class names, ids and inner markup are preserved exactly, so existing
 * CSS that targets e.g. `.qh-hero-title` or the theme's `.entry-title`
 * keeps applying — only the tag name changes.
 *
 * WHICH H1 SURVIVES
 * -----------------
 * If the page contains the homepage hero (`qh-hero-title`), that one is
 * kept, because it is the descriptive, keyword-relevant heading. Otherwise
 * the first H1 in the document wins — on a tool page that is the theme's
 * page title, which is exactly the heading that should be the H1.
 *
 * Can be switched off in iLoveTool3 Suite → SEO Settings if the theme is
 * ever changed to one that already handles this.
 *
 * OUTLINE FIX (v1.3): a demoted H1 that sits BEFORE the kept H1 used to
 * become an <h2>. On the homepage that made the theme's "Home" title the
 * first heading in the document — an H2 ahead of the H1 — which SEO audits
 * report as "first heading is H2 rather than H1". Extra H1s that come
 * before the kept one are now turned into a plain <div> (same class/id, so
 * the theme's styling still applies) and only the ones AFTER it become H2.
 *
 * MISSING ALT (v1.3): the same output pass adds an alt attribute to any
 * <img> that has none at all (theme logo, ad pixel, etc.). Images that
 * already have alt="" are left alone — that is a deliberate "decorative"
 * marker. Script/style/comment blocks are never touched.
 */
class ILT3_SEO_Headings {

	/**
	 * Some hosts / server configs flush PHP's output buffer in chunks
	 * partway through the page (a caching or optimization layer calling
	 * flush()). When that happens, ob_start()'s callback is invoked once
	 * per chunk — not once for the whole page — so a naive filter can see
	 * only the FIRST half of the page (with the theme's H1 in it) in one
	 * call and the SECOND half (with the hero H1) in another call. Neither
	 * call ever sees both H1s together, so nothing ever gets demoted. This
	 * is exactly what happened on ilovetool3.com: the theme's "Home" H1
	 * kept surviving untouched.
	 *
	 * The fix: every chunk is accumulated here instead of being sent to the
	 * browser immediately. Nothing is output at all until PHP signals this
	 * is the FINAL chunk for this buffer — at that point the fixer has the
	 * complete page in one string, exactly as before, and sends the whole
	 * (corrected) page in one go.
	 */
	private static $accumulated = '';

	public function __construct() {
		add_action( 'template_redirect', array( $this, 'start_buffer' ), 1 );
	}

	public function start_buffer() {
		if ( ! ILT3_SEO::option( 'fix_duplicate_h1' ) && ! ILT3_SEO::option( 'fix_missing_alt' ) ) {
			return;
		}

		// Only ordinary front-end HTML page views.
		if ( is_admin() || is_feed() || is_embed() || is_robots() || is_trackback() ) {
			return;
		}
		if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return;
		}
		if ( ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			return;
		}
		// WordPress core sitemaps are XML, not HTML.
		if ( get_query_var( 'sitemap' ) ) {
			return;
		}
		if ( ! apply_filters( 'ilt3_seo_fix_h1', true ) ) {
			return;
		}

		self::$accumulated = '';
		ob_start( array( $this, 'filter_output' ) );
	}

	/**
	 * $phase is PHP's own output-handler bitmask, passed automatically by
	 * ob_start() — every previous version of this method only declared
	 * $html and silently ignored it, which is exactly what let the
	 * chunked-flush bug through unnoticed.
	 */
	public function filter_output( $html, $phase = null ) {
		self::$accumulated .= (string) $html;

		$is_final = ( null === $phase )
			|| ( 0 !== ( $phase & ( PHP_OUTPUT_HANDLER_FINAL | PHP_OUTPUT_HANDLER_END ) ) );

		if ( ! $is_final ) {
			// Hold this chunk back — it is captured in self::$accumulated
			// above and will go out, transformed, with the final chunk.
			// Returning '' here means nothing reaches the browser yet.
			return '';
		}

		$full               = self::$accumulated;
		self::$accumulated = '';

		return $this->process( $full );
	}

	/**
	 * Runs every enabled output fix on the complete page. Each step returns
	 * its input untouched on any doubt, so a failure can never blank a page.
	 */
	private function process( $html ) {
		if ( ! is_string( $html ) || '' === $html || false === stripos( $html, '</html>' ) ) {
			return $html;
		}
		if ( ILT3_SEO::option( 'fix_duplicate_h1' ) ) {
			$html = $this->fix_headings( $html );
		}
		if ( ILT3_SEO::option( 'fix_missing_alt' ) ) {
			$html = $this->fix_missing_alts( $html );
		}
		return $html;
	}

	private function fix_headings( $html ) {
		// Bail on anything that isn't a complete HTML document (partials,
		// JSON responses, file downloads, fatal-error output). Returning the
		// buffer untouched is always safe.
		if ( ! is_string( $html ) || '' === $html ) {
			return $html;
		}
		if ( false === stripos( $html, '</html>' ) ) {
			return $html;
		}

		if ( ! preg_match_all( '/<h1\b[^>]*>/i', $html, $opens ) ) {
			return $html;
		}
		if ( count( $opens[0] ) < 2 ) {
			return $html;
		}

		// If opening and closing H1 tags don't pair up (malformed markup
		// from a theme or another plugin), the "which one do we keep" index
		// can't be trusted. Leave the page exactly as it was.
		$closes = preg_match_all( '/<\/h1\s*>/i', $html );
		if ( $closes !== count( $opens[0] ) ) {
			return $html;
		}

		// Decide which H1 to keep.
		$keep_index = 0;
		foreach ( $opens[0] as $i => $tag ) {
			if ( false !== stripos( $tag, 'qh-hero-title' ) ) {
				$keep_index = $i;
				break;
			}
		}

		$index   = -1;
		$demoted = 0;

		// Rewrite <h1 ...> ... </h1> pairs. The inner content is captured
		// non-greedily and re-emitted byte-for-byte.
		$result = preg_replace_callback(
			'/<h1\b([^>]*)>(.*?)<\/h1\s*>/is',
			function ( $m ) use ( &$index, &$demoted, $keep_index ) {
				$index++;
				if ( $index === $keep_index ) {
					return $m[0];
				}
				$demoted++;
				// Before the kept H1 -> plain <div> (an H2 ahead of the H1
				// breaks the outline). After it -> a normal H2.
				$tag = ( $index < $keep_index ) ? 'div' : 'h2';
				return '<' . $tag . $m[1] . ' data-ilt3-demoted="h1">' . $m[2] . '</' . $tag . '>';
			},
			$html
		);

		if ( null === $result ) {
			// preg failure (e.g. backtrack limit on a very large page) —
			// keep the original output rather than serving nothing.
			return $html;
		}

		if ( $demoted > 0 ) {
			$result = str_replace(
				'</body>',
				"<!-- iLoveTool3 SEO: demoted {$demoted} duplicate H1 heading(s) to H2 -->\n</body>",
				$result
			);
		}

		return $result;
	}

	/**
	 * Adds alt text to <img> tags that have no alt attribute at all.
	 * Only markup outside <script>, <style>, <noscript>, <template> and
	 * HTML comments is edited, so JavaScript string templates that happen to
	 * contain "<img" are never rewritten.
	 */
	private function fix_missing_alts( $html ) {
		$parts = preg_split(
			'/(<script\b.*?<\/script\s*>|<style\b.*?<\/style\s*>|<noscript\b.*?<\/noscript\s*>|<template\b.*?<\/template\s*>|<!--.*?-->)/is',
			$html,
			-1,
			PREG_SPLIT_DELIM_CAPTURE
		);
		if ( ! is_array( $parts ) ) {
			return $html;
		}

		$changed = 0;
		foreach ( $parts as $i => $part ) {
			// Odd indexes are the captured script/style/comment blocks.
			if ( 1 === ( $i % 2 ) || false === stripos( $part, '<img' ) ) {
				continue;
			}
			$edited = preg_replace_callback(
				'/<img\b([^>]*)>/i',
				function ( $m ) use ( &$changed ) {
					$attrs = $m[1];
					if ( preg_match( '/\balt\s*=/i', $attrs ) ) {
						return $m[0];
					}
					$changed++;
					$alt     = esc_attr( $this->guess_alt( $attrs ) );
					$closing = '>';
					if ( preg_match( '/\s*\/\s*$/', $attrs ) ) {
						// Self-closing <img ... /> — keep the slash at the end.
						$attrs   = preg_replace( '/\s*\/\s*$/', '', $attrs );
						$closing = ' />';
					}
					return '<img' . $attrs . ' alt="' . $alt . '"' . $closing;
				},
				$part
			);
			if ( null !== $edited ) { // null = PCRE failure; keep the original segment.
				$parts[ $i ] = $edited;
			}
		}

		if ( 0 === $changed ) {
			return $html;
		}

		$result = implode( '', $parts );
		return ( '' === $result ) ? $html : $result;
	}

	/**
	 * Best available alt text for an image that has none: its own title
	 * attribute, else a readable version of its file name, else the site
	 * name. Tracking pixels (1x1) are marked decorative with an empty alt.
	 */
	private function guess_alt( $attrs ) {
		$is_pixel = preg_match( '/\bwidth\s*=\s*["\']?1["\']?(\s|\/|$)/i', $attrs )
			&& preg_match( '/\bheight\s*=\s*["\']?1["\']?(\s|\/|$)/i', $attrs );
		if ( $is_pixel ) {
			return '';
		}

		if ( preg_match( '/\btitle\s*=\s*"([^"]+)"/i', $attrs, $t ) && '' !== trim( $t[1] ) ) {
			return trim( html_entity_decode( $t[1], ENT_QUOTES ) );
		}

		if ( preg_match( '/\bsrc\s*=\s*["\']([^"\']+)["\']/i', $attrs, $s ) ) {
			$path = (string) wp_parse_url( html_entity_decode( $s[1] ), PHP_URL_PATH );
			$file = pathinfo( $path, PATHINFO_FILENAME );
			$file = preg_replace( '/-\d+x\d+$/', '', (string) $file ); // WP thumbnail suffix.
			$file = trim( preg_replace( '/[\s_\-.]+/', ' ', $file ) );
			if ( preg_match( '/[A-Za-z]{3,}/', $file ) && ! preg_match( '/^[0-9a-f\s]{16,}$/i', $file ) ) {
				return ucfirst( $file );
			}
		}

		return get_bloginfo( 'name' );
	}
}
