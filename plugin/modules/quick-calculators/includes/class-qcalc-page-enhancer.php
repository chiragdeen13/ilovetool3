<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Automatically enhances every tool page's content with:
 *  - a breadcrumb (Home > Category > Tool) + a "← Back" link
 *  - "Related Tools" (8, same category)
 *  - "Popular Tools" (8, most-visited site-wide, least-used ones drop
 *    off automatically as view counts change)
 *
 * Runs on the_content so it applies everywhere without editing each page.
 * Skips the homepage itself and non-tool pages (About, Contact, etc).
 */
class QCalc_PageEnhancer {

	public function __construct() {
		add_filter( 'the_content', array( $this, 'enhance' ) );
	}

	public function enhance( $content ) {
		if ( ! is_page() || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		// Skip the homepage itself (it has its own full layout).
		if ( has_shortcode( $content, 'tools_homepage' ) ) {
			return $content;
		}

		$page_id = get_the_ID();
		$title   = get_the_title( $page_id );
		$slug    = get_post_field( 'post_name', $page_id );

		if ( in_array( $slug, QCalc_Catalog::SKIP_SLUGS, true ) ) {
			// Not a "tool" page (About/Contact/Privacy/etc) — no
			// breadcrumb or related/popular tools here, but it should
			// still carry the site's standard 5 ad slots: left/right
			// sidebar, below-content, and two in-content.
			if ( ! function_exists( 'ilt3_ad_layout_start' ) ) {
				return $content;
			}
			$ad_top    = ilt3_ad_slot( 4 );
			$ad_bottom = ilt3_ad_slot( 5 );
			$ad_below  = ilt3_ad_slot( 3 );
			return ilt3_ad_layout_start() . $ad_top . $content . $ad_below . $ad_bottom . ilt3_ad_layout_end();
		}

		$category      = QCalc_Catalog::categorize( $title );
		$category_slug = QCalc_Catalog::slugify_category( $category );

		$breadcrumb = $this->render_breadcrumb( $title, $category, $category_slug );
		$related    = $this->render_related( $page_id, $category );
		$popular    = $this->render_popular( $page_id );

		if ( ! function_exists( 'ilt3_ad_layout_start' ) ) {
			return $breadcrumb . $content . $related . $popular;
		}

		$ad_top    = ilt3_ad_slot( 4 ); // in-content, near the top
		$ad_below  = ilt3_ad_slot( 3 ); // directly below the tool/content
		$ad_bottom = ilt3_ad_slot( 5 ); // in-content, between related & popular

		return ilt3_ad_layout_start()
			. $breadcrumb . $ad_top . $content . $ad_below
			. $related . $ad_bottom . $popular
			. ilt3_ad_layout_end();
	}

	private function render_breadcrumb( $title, $category, $category_slug ) {
		ob_start();
		?>
		<?php
		/* Accessibility/SEO fixes here, no visual change:
		   - the breadcrumb is now a real <nav> with an accessible name, so
		     it is announced as a breadcrumb rather than as loose links
		   - the separators are aria-hidden (they were being read aloud)
		   - "← Back" was an <a href="#"> with an inline onclick. That is a
		     link that goes nowhere, which crawlers follow and screen readers
		     announce as a link to the current page. It is an action, so it
		     is now a <button>; the history.back() behaviour is unchanged and
		     has moved to homepage.js with the rest of the site's JS. */
		?>
		<nav class="qh-breadcrumb" aria-label="Breadcrumb">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><span aria-hidden="true">🏠</span> Home</a>
			<span aria-hidden="true">›</span>
			<a href="<?php echo esc_url( home_url( '/#' . $category_slug ) ); ?>"><?php echo esc_html( $category ); ?></a>
			<span aria-hidden="true">›</span>
			<span class="qh-breadcrumb-current" aria-current="page"><?php echo esc_html( $title ); ?></span>
			<button type="button" class="qh-back-btn" data-qh-back>← Back</button>
		</nav>
		<?php
		return ob_get_clean();
	}

	private function render_related( $current_id, $category ) {
		$pages   = QCalc_Catalog::get_categorized_pages( $current_id );
		$related = array_values(
			array_filter(
				$pages,
				function ( $p ) use ( $category ) {
					return $p['category'] === $category;
				}
			)
		);

		if ( empty( $related ) ) {
			return '';
		}

		$related = array_slice( $related, 0, 8 );

		return $this->render_tool_grid( '🔗 Related Tools', $related, 'qh-related' );
	}

	private function render_popular( $current_id ) {
		if ( ! class_exists( 'QCalc_ViewCounter' ) ) {
			return '';
		}

		$popular = QCalc_ViewCounter::get_popular_pages( 8, $current_id );

		if ( empty( $popular ) ) {
			return '';
		}

		return $this->render_tool_grid( '🔥 Popular Tools', $popular, 'qh-popular' );
	}

	private function render_tool_grid( $heading, $tools, $extra_class ) {
		ob_start();
		?>
		<div class="qh-related <?php echo esc_attr( $extra_class ); ?>">
			<h3 class="qh-related-title"><?php echo esc_html( $heading ); ?></h3>
			<div class="qh-grid">
				<?php foreach ( $tools as $tool ) : ?>
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
