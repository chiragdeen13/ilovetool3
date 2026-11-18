<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared helpers + the single place that decides, for the current request,
 * what the title / description / canonical / OG image should be.
 *
 * Everything else in this module reads from ILT3_SEO::current() so the
 * <title>, the meta description, the Open Graph tags, the Twitter tags and
 * the JSON-LD can never disagree with each other.
 */
class ILT3_SEO {

	const OPTION_NAME = 'ilt3_seo_options';

	/** Pages that are real site pages but not "tools". */
	const NON_TOOL_SLUGS = array(
		'about', 'about-us',
		'contact', 'contact-us',
		'privacy', 'privacy-policy',
		'terms', 'terms-and-conditions', 'terms-of-service', 'terms-conditions',
		'home', 'homepage', 'front-page',
		'blog', 'news', 'sample-page',
		'cart', 'checkout', 'my-account', 'shop',
	);

	/**
	 * True when a dedicated SEO plugin is handling meta tags. In that case
	 * this module must not output its own, or the site gets two of
	 * everything (exactly the problem this module was written to fix).
	 */
	public static function seo_plugin_active() {
		$markers = array(
			'WPSEO_VERSION',          // Yoast SEO
			'RANK_MATH_VERSION',      // Rank Math
			'AIOSEO_VERSION',         // All in One SEO
			'SEOPRESS_VERSION',       // SEOPress
			'THE_SEO_FRAMEWORK_VERSION', // The SEO Framework
		);
		foreach ( $markers as $marker ) {
			if ( defined( $marker ) ) {
				return true;
			}
		}
		/** Allows forcing this module off/on from wp-config or a snippet. */
		return (bool) apply_filters( 'ilt3_seo_plugin_active', false );
	}

	public static function options() {
		return wp_parse_args(
			get_option( self::OPTION_NAME, array() ),
			array(
				'home_title'       => '',
				'home_description' => '',
				'og_image'         => '',
				'twitter_site'     => '',
				'fix_duplicate_h1' => 1,
				'fix_missing_alt'  => 1,
				'clean_head'       => 1,
				'disable_emojis'   => 1,
				'llms_txt'         => 1,
				'ga4_id'           => '',
				'social_facebook'  => '',
				'social_x'         => '',
				'social_instagram' => '',
				'social_linkedin'  => '',
				'social_youtube'   => '',
			)
		);
	}

	public static function option( $key ) {
		$opts = self::options();
		return isset( $opts[ $key ] ) ? $opts[ $key ] : '';
	}

	/**
	 * Absolute, canonical-form site URL for a path. Always built from
	 * home_url() so the scheme (https) and the www/non-www choice made in
	 * Settings → General is the only version this site ever advertises —
	 * this is what stops http/https and www/non-www duplicate URLs from
	 * appearing in canonicals, OG tags and JSON-LD.
	 */
	public static function url( $path = '/' ) {
		return home_url( $path );
	}

	/**
	 * The social sharing image. Priority:
	 *   1. Explicit URL set in iLoveTool3 Suite → SEO Settings
	 *   2. The WordPress Site Icon (Appearance → Customize), which WP
	 *      already serves as a real PNG at a known size
	 *   3. Nothing — we output NO og:image rather than a broken URL.
	 */
	public static function og_image() {
		$custom = trim( (string) self::option( 'og_image' ) );
		if ( '' !== $custom ) {
			$image = $custom;
		} elseif ( function_exists( 'get_site_icon_url' ) && has_site_icon() ) {
			$image = get_site_icon_url( 512 );
		} else {
			$image = '';
		}

		/** Lets a future OG banner be plugged in without touching this file. */
		$image = apply_filters( 'ilt3_seo_og_image', $image );

		return $image ? esc_url_raw( $image ) : '';
	}

	/** Is this page one of the public tool pages? */
	public static function is_tool_page( $post = null ) {
		if ( ! is_singular( 'page' ) || is_front_page() ) {
			return false;
		}
		$post = $post ? $post : get_post();
		if ( ! $post ) {
			return false;
		}
		return ! in_array( $post->post_name, self::NON_TOOL_SLUGS, true );
	}

	/**
	 * Look up a hand-written title/description for a tool page from the
	 * existing MT Tool Articles SEO table. Kept as the source of truth so
	 * none of that copywriting is lost.
	 */
	public static function curated_meta( $post ) {
		if ( ! $post || ! function_exists( 'mta_seo_meta' ) ) {
			return null;
		}

		$all  = mta_seo_meta();
		$slug = strtolower( $post->post_name );

		if ( isset( $all[ $slug ] ) ) {
			return $all[ $slug ];
		}

		foreach ( $all as $key => $meta ) {
			if ( strlen( $key ) > 4 && false !== strpos( $slug, $key ) ) {
				return $meta;
			}
		}

		// Fall back to the article database (mta_find_article carries its
		// own ['seo'] block for auto-generated articles).
		if ( function_exists( 'mta_find_article' ) ) {
			$article = mta_find_article( $slug, $post->post_title );
			if ( ! empty( $article['seo']['title'] ) && ! empty( $article['seo']['meta'] ) ) {
				return $article['seo'];
			}
			if ( ! empty( $article['intro'] ) ) {
				return array(
					'title' => $post->post_title . ' — Free Online Tool | ' . get_bloginfo( 'name' ),
					'meta'  => wp_trim_words( wp_strip_all_tags( $article['intro'] ), 30, '' ),
				);
			}
		}

		return null;
	}

	/**
	 * Build a description from a post's own content when no curated one
	 * exists. Shortcodes are stripped first, otherwise descriptions come
	 * out looking like "[bmi_calculator]".
	 */
	public static function description_from_post( $post ) {
		if ( ! $post ) {
			return '';
		}

		$raw = $post->post_excerpt ? $post->post_excerpt : $post->post_content;
		$raw = strip_shortcodes( $raw );
		$raw = wp_strip_all_tags( $raw );
		$raw = preg_replace( '/\s+/u', ' ', (string) $raw );
		$raw = trim( $raw );

		if ( '' === $raw ) {
			return '';
		}

		return self::trim_description( $raw );
	}

	/** Keep descriptions in the ~155-160 char range without cutting words. */
	public static function trim_description( $text, $limit = 158 ) {
		$text = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( (string) $text ) ) );
		if ( '' === $text ) {
			return '';
		}
		// Note: only mb_strlen() and mb_substr() are used here. WordPress
		// polyfills those two in wp-includes/compat.php when the mbstring
		// extension is missing; mb_strrpos() is NOT polyfilled, so the
		// word boundary is found with a regex instead of mb_strrpos().
		if ( mb_strlen( $text ) <= $limit ) {
			return $text;
		}

		$cut = mb_substr( $text, 0, $limit );

		// Drop a trailing partial word, but only if that still leaves a
		// usefully long description.
		if ( preg_match( '/^(.*)\s\S*$/u', $cut, $m ) && mb_strlen( $m[1] ) > 60 ) {
			$cut = $m[1];
		}

		return rtrim( $cut, " \t\n,.;:-" ) . '…';
	}

	/** Default homepage title — descriptive, not keyword-stuffed. */
	public static function default_home_title() {
		$custom = trim( (string) self::option( 'home_title' ) );
		if ( '' !== $custom ) {
			return $custom;
		}
		return get_bloginfo( 'name' ) . ' — Free Online Tools, Calculators & Converters';
	}

	public static function default_home_description() {
		$custom = trim( (string) self::option( 'home_description' ) );
		if ( '' !== $custom ) {
			return self::trim_description( $custom );
		}
		return self::trim_description(
			'Free online tools that run in your browser — IMEI and phone checks, finance and health calculators, PDF converters, text and developer utilities. No signup, no installs.'
		);
	}

	/**
	 * Everything the rest of the module needs about the current request.
	 * Computed once per request.
	 */
	public static function current() {
		static $cache = null;
		if ( null !== $cache ) {
			return $cache;
		}

		$site = get_bloginfo( 'name' );
		$data = array(
			'title'       => '',
			'description' => '',
			'canonical'   => '',
			'og_type'     => 'website',
			'noindex'     => false,
			'is_tool'     => false,
			'post'        => null,
		);

		if ( is_front_page() ) {
			$data['title']       = self::default_home_title();
			$data['description'] = self::default_home_description();
			$data['canonical']   = self::url( '/' );

		} elseif ( is_home() ) {
			$blog_id             = (int) get_option( 'page_for_posts' );
			$blog_page           = $blog_id ? get_post( $blog_id ) : null;
			$data['title']       = ( $blog_page ? $blog_page->post_title : 'Blog' ) . ' | ' . $site;
			$data['description'] = $blog_page && $blog_page->post_excerpt
				? self::trim_description( $blog_page->post_excerpt )
				: self::trim_description( 'Guides, explanations and updates about the free online tools on ' . $site . '.' );
			$data['canonical']   = $blog_id ? get_permalink( $blog_id ) : self::url( '/' );

		} elseif ( is_singular() ) {
			$post         = get_post();
			$data['post'] = $post;

			if ( $post ) {
				$data['canonical'] = get_permalink( $post );
				$data['is_tool']   = self::is_tool_page( $post );

				if ( 'post' === $post->post_type ) {
					$data['og_type'] = 'article';
				}

				$curated = $data['is_tool'] ? self::curated_meta( $post ) : null;

				if ( $curated && ! empty( $curated['title'] ) ) {
					$data['title'] = $curated['title'];
				} elseif ( $data['is_tool'] ) {
					$data['title'] = $post->post_title . ' — Free Online Tool | ' . $site;
				} else {
					$data['title'] = $post->post_title . ' | ' . $site;
				}

				if ( $curated && ! empty( $curated['meta'] ) ) {
					$data['description'] = self::trim_description( $curated['meta'] );
				} else {
					$data['description'] = self::description_from_post( $post );
				}

				// Last resort so no important page ships without a description.
				if ( '' === $data['description'] && $data['is_tool'] ) {
					$data['description'] = self::trim_description(
						'Use the free ' . $post->post_title . ' on ' . $site . '. Runs in your browser, gives instant results, and needs no signup or install.'
					);
				}

				if ( 'publish' !== $post->post_status || ! empty( $post->post_password ) ) {
					$data['noindex'] = true;
				}
			}

		} elseif ( is_search() ) {
			$data['title']     = 'Search results | ' . $site;
			$data['noindex']   = true;
			$data['canonical'] = '';

		} elseif ( is_404() ) {
			$data['title']     = 'Page not found | ' . $site;
			$data['noindex']   = true;
			$data['canonical'] = '';

		} elseif ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			if ( $term && ! is_wp_error( $term ) && isset( $term->name ) ) {
				$data['title']       = $term->name . ' | ' . $site;
				$data['description'] = self::trim_description( term_description( $term ) );
				$data['canonical']   = get_term_link( $term );
				if ( is_wp_error( $data['canonical'] ) ) {
					$data['canonical'] = '';
				}
			}

		} elseif ( is_author() || is_date() ) {
			// Thin archives — crawlable but not worth indexing on a tools site.
			$data['noindex'] = true;
		}

		// Paginated views get their own canonical (never point page 2 at
		// page 1 — that hides real content from crawlers).
		$paged = max( (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
		if ( $paged > 1 ) {
			if ( $data['canonical'] ) {
				$data['canonical'] = trailingslashit( $data['canonical'] ) . 'page/' . $paged . '/';
			}
			if ( $data['title'] ) {
				$data['title'] = $data['title'] . ' — Page ' . $paged;
			}
		}

		if ( ! get_option( 'blog_public' ) ) {
			$data['noindex'] = true;
		}

		$cache = apply_filters( 'ilt3_seo_current', $data );
		return $cache;
	}
}
