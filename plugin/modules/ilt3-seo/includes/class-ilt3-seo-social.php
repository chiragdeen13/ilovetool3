<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Social profile links.
 *
 * Nothing is invented: a network only appears when its URL has been typed
 * into iLoveTool3 Suite → SEO Settings. Once filled in, the profile is
 *   1. linked from the site footer (visible, crawlable link), and
 *   2. added to the Organization JSON-LD as `sameAs`, which is how search
 *      engines and AI assistants connect the website to its official
 *      profiles.
 *
 * Works whether or not Yoast SEO (or another SEO plugin) is active:
 *   - Yoast active   -> merged in through Yoast's own Organization filter
 *   - Yoast inactive -> merged into this module's own JSON-LD graph
 */
class ILT3_SEO_Social {

	/** option key => label shown to visitors. */
	const NETWORKS = array(
		'social_facebook'  => 'Facebook',
		'social_x'         => 'X',
		'social_instagram' => 'Instagram',
		'social_linkedin'  => 'LinkedIn',
		'social_youtube'   => 'YouTube',
	);

	public function __construct() {
		add_filter( 'ilt3_seo_schema_graph', array( $this, 'add_same_as_to_graph' ) );
		add_filter( 'wpseo_schema_organization', array( $this, 'add_same_as_to_piece' ) );
	}

	/** @return array label => url, only for networks that are filled in. */
	public static function profiles() {
		$out = array();
		foreach ( self::NETWORKS as $key => $label ) {
			$url = trim( (string) ILT3_SEO::option( $key ) );
			if ( '' !== $url ) {
				$out[ $label ] = $url;
			}
		}
		return $out;
	}

	/** Footer markup, or an empty string when no profile is configured. */
	public static function footer_html() {
		$profiles = self::profiles();
		if ( empty( $profiles ) ) {
			return '';
		}

		$links = array();
		foreach ( $profiles as $label => $url ) {
			$links[] = '<a href="' . esc_url( $url ) . '" rel="me noopener" target="_blank">' . esc_html( $label ) . '</a>';
		}

		return '<p class="qh-footer-social">Follow us: ' . implode( ' · ', $links ) . '</p>';
	}

	private function merge_same_as( $node ) {
		$profiles = array_values( self::profiles() );
		if ( empty( $profiles ) || ! is_array( $node ) ) {
			return $node;
		}
		$existing        = ( isset( $node['sameAs'] ) && is_array( $node['sameAs'] ) ) ? $node['sameAs'] : array();
		$node['sameAs'] = array_values( array_unique( array_merge( $existing, $profiles ) ) );
		return $node;
	}

	public function add_same_as_to_graph( $json ) {
		if ( empty( $json['@graph'] ) || ! is_array( $json['@graph'] ) ) {
			return $json;
		}
		foreach ( $json['@graph'] as $i => $node ) {
			if ( is_array( $node ) && isset( $node['@type'] ) && 'Organization' === $node['@type'] ) {
				$json['@graph'][ $i ] = $this->merge_same_as( $node );
			}
		}
		return $json;
	}

	public function add_same_as_to_piece( $data ) {
		return $this->merge_same_as( $data );
	}
}
