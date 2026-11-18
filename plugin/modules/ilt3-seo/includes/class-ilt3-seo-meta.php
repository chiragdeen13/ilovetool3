<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Outputs <title>, meta description, canonical, robots, Open Graph and
 * Twitter Card tags — exactly once each, on every front-end request.
 *
 * The title is set through the pre_get_document_title filter rather than by
 * echoing a <title> element into wp_head. That is the difference between the
 * site having one authoritative title and having two competing ones.
 */
class ILT3_SEO_Meta {

	public function __construct() {
		// Some older themes never call wp_head's title-tag support. Adding
		// it is harmless when already present and guarantees our filtered
		// title actually reaches the page.
		add_action( 'after_setup_theme', array( $this, 'ensure_title_tag_support' ), 20 );

		// add_filter( 'pre_get_document_title', array( $this, 'filter_title' ), 20 );

		// We emit a single canonical for every view (WordPress core only
		// does singular views), so core's version is removed to avoid two.
		remove_action( 'wp_head', 'rel_canonical' );

		add_filter( 'wp_robots', array( $this, 'filter_robots' ) );
		add_action( 'wp_head', array( $this, 'render_head' ), 2 );
	}

	public function ensure_title_tag_support() {
		if ( ! current_theme_supports( 'title-tag' ) ) {
			add_theme_support( 'title-tag' );
		}
	}

	public function filter_title( $title ) {
		if ( is_admin() || is_feed() ) {
			return $title;
		}
		$data = ILT3_SEO::current();
		return $data['title'] ? $data['title'] : $title;
	}

	/**
	 * Robots directives via the core wp_robots API (WP 5.7+), which merges
	 * cleanly instead of printing a competing <meta name="robots">.
	 *
	 * Note: tool pages and every other public page stay indexable. Only
	 * genuinely non-indexable views (search results, 404, thin date/author
	 * archives, unpublished posts) get noindex.
	 */
	public function filter_robots( $robots ) {
		$data = ILT3_SEO::current();

		if ( $data['noindex'] ) {
			$robots['noindex']  = true;
			$robots['follow']   = true;
			unset( $robots['index'] );
			return $robots;
		}

		unset( $robots['noindex'] );
		$robots['index']              = true;
		$robots['follow']             = true;
		$robots['max-image-preview']  = 'large';
		$robots['max-snippet']        = -1;
		$robots['max-video-preview']  = -1;

		return $robots;
	}

	public function render_head() {
		if ( is_admin() || is_feed() || is_embed() ) {
			return;
		}

		$data      = ILT3_SEO::current();
		$site      = get_bloginfo( 'name' );
		$image     = ILT3_SEO::og_image();
		$twitter   = trim( (string) ILT3_SEO::option( 'twitter_site' ) );

		echo "\n<!-- iLoveTool3 SEO -->\n";

		if ( $data['description'] ) {
			echo '<meta name="description" content="' . esc_attr( $data['description'] ) . '" />' . "\n";
		}

		if ( $data['canonical'] && ! $data['noindex'] ) {
			echo '<link rel="canonical" href="' . esc_url( $data['canonical'] ) . '" />' . "\n";
		}

		/* ---------- Open Graph ---------- */
		echo '<meta property="og:site_name" content="' . esc_attr( $site ) . '" />' . "\n";
		echo '<meta property="og:locale" content="' . esc_attr( str_replace( '-', '_', get_bloginfo( 'language' ) ) ) . '" />' . "\n";
		echo '<meta property="og:type" content="' . esc_attr( $data['og_type'] ) . '" />' . "\n";

		if ( $data['title'] ) {
			echo '<meta property="og:title" content="' . esc_attr( $data['title'] ) . '" />' . "\n";
		}
		if ( $data['description'] ) {
			echo '<meta property="og:description" content="' . esc_attr( $data['description'] ) . '" />' . "\n";
		}
		if ( $data['canonical'] ) {
			echo '<meta property="og:url" content="' . esc_url( $data['canonical'] ) . '" />' . "\n";
		}

		// Only ever printed when a real image exists — an og:image pointing
		// at a URL that 404s is worse than having none at all.
		if ( $image ) {
			echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
			echo '<meta property="og:image:alt" content="' . esc_attr( $site ) . '" />' . "\n";
		}

		if ( 'article' === $data['og_type'] && $data['post'] ) {
			$published = get_the_date( 'c', $data['post'] );
			$modified  = get_the_modified_date( 'c', $data['post'] );
			if ( $published ) {
				echo '<meta property="article:published_time" content="' . esc_attr( $published ) . '" />' . "\n";
			}
			if ( $modified ) {
				echo '<meta property="article:modified_time" content="' . esc_attr( $modified ) . '" />' . "\n";
			}
		}

		/* ---------- Twitter / X ---------- */
		echo '<meta name="twitter:card" content="' . ( $image ? 'summary_large_image' : 'summary' ) . '" />' . "\n";
		if ( $twitter ) {
			echo '<meta name="twitter:site" content="' . esc_attr( $twitter ) . '" />' . "\n";
		}
		if ( $data['title'] ) {
			echo '<meta name="twitter:title" content="' . esc_attr( $data['title'] ) . '" />' . "\n";
		}
		if ( $data['description'] ) {
			echo '<meta name="twitter:description" content="' . esc_attr( $data['description'] ) . '" />' . "\n";
		}
		if ( $image ) {
			echo '<meta name="twitter:image" content="' . esc_url( $image ) . '" />' . "\n";
			echo '<meta name="twitter:image:alt" content="' . esc_attr( $site ) . '" />' . "\n";
		}

		echo "<!-- /iLoveTool3 SEO -->\n";
	}
}
