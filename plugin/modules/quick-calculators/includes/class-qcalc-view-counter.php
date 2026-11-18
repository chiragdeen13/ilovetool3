<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Increments a view counter (post meta) every time a page is visited on
 * the frontend. Powers the "Popular Tools" section on the homepage.
 * Lightweight — no separate table, just one meta value per page.
 */
class QCalc_ViewCounter {

	const META_KEY = '_qcalc_views';

	public function __construct() {
		add_action( 'wp', array( $this, 'maybe_count_view' ) );
	}

	public function maybe_count_view() {
		if ( is_admin() || ! is_page() ) {
			return;
		}
		$page_id = get_queried_object_id();
		if ( ! $page_id ) {
			return;
		}
		$views = (int) get_post_meta( $page_id, self::META_KEY, true );
		update_post_meta( $page_id, self::META_KEY, $views + 1 );
	}

	/**
	 * Return up to $limit pages sorted by view count, descending.
	 * Only pages that have at least 1 view are included.
	 */
	public static function get_popular_pages( $limit = 6, $exclude_id = 0 ) {
		$pages = QCalc_Catalog::get_categorized_pages( $exclude_id );

		foreach ( $pages as &$page ) {
			$page['views'] = (int) get_post_meta( $page['id'], self::META_KEY, true );
		}
		unset( $page );

		usort(
			$pages,
			function ( $a, $b ) {
				return $b['views'] <=> $a['views'];
			}
		);

		$pages = array_filter(
			$pages,
			function ( $p ) {
				return $p['views'] > 0;
			}
		);

		return array_slice( array_values( $pages ), 0, $limit );
	}
}
