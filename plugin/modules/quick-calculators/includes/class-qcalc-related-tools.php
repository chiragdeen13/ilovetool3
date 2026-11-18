<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * [related_tools] — add this to the bottom of any tool page. It detects
 * the current page's category automatically (same rules as the homepage)
 * and shows a few other tools from that category.
 */
class QCalc_RelatedTools {

	public function __construct() {
		add_shortcode( 'related_tools', array( $this, 'render' ) );
	}

	public function render() {
		if ( ! is_page() ) {
			return '';
		}

		wp_enqueue_style( 'qcalc-homepage-style' );

		$current_id       = get_queried_object_id();
		$current_title    = get_the_title( $current_id );
		$current_category = QCalc_Catalog::categorize( $current_title );

		$all_pages = QCalc_Catalog::get_categorized_pages( $current_id );
		$related   = array_values(
			array_filter(
				$all_pages,
				function ( $p ) use ( $current_category ) {
					return $p['category'] === $current_category;
				}
			)
		);

		if ( empty( $related ) ) {
			return '';
		}

		$related = array_slice( $related, 0, 4 );

		ob_start();
		?>
		<div class="qh-related">
			<h3 class="qh-related-title">Related Tools</h3>
			<div class="qh-grid">
				<?php foreach ( $related as $tool ) : ?>
					<div class="qh-card" data-name="<?php echo esc_attr( strtolower( $tool['name'] ) ); ?>">
						<div class="qh-card-body">
							<div class="qh-card-name"><?php echo esc_html( $tool['name'] ); ?></div>
						</div>
						<a class="qh-card-btn" href="<?php echo esc_url( home_url( '/' . $tool['slug'] . '/' ) ); ?>">Open</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
