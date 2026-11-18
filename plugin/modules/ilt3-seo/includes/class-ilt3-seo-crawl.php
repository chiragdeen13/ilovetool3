<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Crawlability + indexability: robots.txt and the XML sitemap.
 *
 * The sitemap used here is the one WordPress core generates at
 * /wp-sitemap.xml (WP 5.5+). Nothing custom is invented — core's sitemap is
 * already split into indexed sub-sitemaps, already uses permalinks (which
 * are our canonical URLs), and is already discovered by Google. This class
 * only trims what shouldn't be in it.
 */
class ILT3_SEO_Crawl {

	public function __construct() {
		add_filter( 'robots_txt', array( $this, 'filter_robots_txt' ), 20, 2 );

		// Sitemap tuning (core sitemaps only — if an SEO plugin takes over
		// sitemap duty, these filters simply never fire).
		add_filter( 'wp_sitemaps_add_provider', array( $this, 'filter_providers' ), 10, 2 );
		add_filter( 'wp_sitemaps_post_types', array( $this, 'filter_post_types' ) );
		add_filter( 'wp_sitemaps_posts_query_args', array( $this, 'filter_posts_query' ), 10, 2 );
		add_filter( 'wp_sitemaps_taxonomies', array( $this, 'filter_taxonomies' ) );
	}

	/**
	 * A clean robots.txt.
	 *
	 * Critically, nothing here blocks /wp-content/ or /wp-includes/ — those
	 * hold the CSS and JavaScript every tool page needs, and blocking them
	 * stops Google rendering the page at all. Only the admin area and
	 * parameterised/search URLs that produce duplicate thin pages are
	 * disallowed, and admin-ajax.php is explicitly re-allowed because some
	 * front-end tool actions go through it.
	 */
	public function filter_robots_txt( $output, $public ) {
		if ( ! $public ) {
			// Site is set to discourage search engines in Settings → Reading.
			// Leave WordPress's own output alone rather than contradicting it.
			return $output;
		}

		$rules = array(
			'User-agent: *',
			'Disallow: /wp-admin/',
			'Allow: /wp-admin/admin-ajax.php',
			'Disallow: /wp-login.php',
			// Internal site-search result pages: thin, infinite, and never
			// something we want in the index.
			'Disallow: /*?s=',
			'Disallow: /search/',
			// Comment reply links create near-duplicate URLs.
			'Disallow: /*?replytocom=',
			'',
			// Assets stay explicitly crawlable.
			'Allow: /wp-content/uploads/',
			'Allow: /*.css$',
			'Allow: /*.js$',
			'',
		);

		$sitemap = $this->sitemap_url();
		if ( $sitemap ) {
			$rules[] = 'Sitemap: ' . $sitemap;
		}

		return implode( "\n", $rules ) . "\n";
	}

	public function sitemap_url() {
		if ( function_exists( 'wp_sitemaps_get_server' ) ) {
			$server = wp_sitemaps_get_server();
			if ( $server && method_exists( $server, 'is_enabled' ) && ! $server->is_enabled() ) {
				return '';
			}
			return home_url( '/wp-sitemap.xml' );
		}
		return '';
	}

	/**
	 * Drop the users sitemap. On this site every page is published by the
	 * same admin account, so author archives are duplicate listings with no
	 * unique content — and ILT3_SEO already marks them noindex.
	 */
	public function filter_providers( $provider, $name ) {
		if ( 'users' === $name ) {
			return false;
		}
		return $provider;
	}

	/** Only pages and posts belong in the sitemap. */
	public function filter_post_types( $post_types ) {
		foreach ( array_keys( $post_types ) as $type ) {
			if ( ! in_array( $type, array( 'page', 'post' ), true ) ) {
				unset( $post_types[ $type ] );
			}
		}
		return $post_types;
	}

	/**
	 * Exclude pages that shouldn't be advertised to crawlers: the default
	 * "Sample Page", anything password-protected, and anything explicitly
	 * marked noindex with the _ilt3_noindex meta key.
	 *
	 * Note what is NOT excluded: every tool page, every category landing
	 * anchor target, About/Contact/Privacy/Terms. Those are all legitimate
	 * indexable pages.
	 */
	public function filter_posts_query( $args, $post_type ) {
		if ( 'page' !== $post_type ) {
			return $args;
		}

		$excluded = array();

		foreach ( array( 'sample-page' ) as $slug ) {
			$page = get_page_by_path( $slug );
			if ( $page ) {
				$excluded[] = (int) $page->ID;
			}
		}

		$flagged = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'fields'         => 'ids',
				'posts_per_page' => 200,
				'meta_key'       => '_ilt3_noindex',
				'meta_value'     => '1',
				'no_found_rows'  => true,
			)
		);
		if ( ! empty( $flagged ) ) {
			$excluded = array_merge( $excluded, array_map( 'intval', $flagged ) );
		}

		$excluded = array_unique( array_filter( $excluded ) );
		if ( ! empty( $excluded ) ) {
			$args['post__not_in'] = isset( $args['post__not_in'] )
				? array_merge( (array) $args['post__not_in'], $excluded )
				: $excluded;
		}

		$args['has_password'] = false;

		return $args;
	}

	/**
	 * Categories/tags are only used by the blog here and mostly hold one or
	 * two posts each. Keeping them out of the sitemap avoids advertising
	 * near-empty archive pages; they remain crawlable via internal links.
	 */
	public function filter_taxonomies( $taxonomies ) {
		unset( $taxonomies['post_tag'] );
		return $taxonomies;
	}
}
