<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Injects a consistent header (logo, Home / All Tools / Blog / About nav,
 * dark-mode toggle) and footer on EVERY page site-wide — not just the
 * homepage — via wp_body_open / wp_footer. Also sets a favicon.
 *
 * Note: most themes (including Astra) render their own header too. If you
 * see two headers stacked, hide the theme's default header/nav in
 * Appearance > Customize (Astra: Header Builder) so only this one shows.
 */
class QCalc_SiteChrome {

	public function __construct() {
		add_action( 'wp_body_open', array( $this, 'render_header' ) );
		add_action( 'wp_footer', array( $this, 'render_footer' ) );
		add_action( 'wp_head', array( $this, 'render_favicon' ) );
	}

	public function render_favicon() {
		// Simple inline SVG favicon (a lightning bolt) — no image upload needed.
		// If the site already has a custom Site Icon set (Appearance > Customize),
		// that one wins; this is only a fallback.
		if ( has_site_icon() ) {
			return;
		}
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" rx="20" fill="%233b6fed"/><text x="50" y="68" font-size="60" text-anchor="middle" fill="white">⚡</text></svg>';
		echo '<link rel="icon" href="data:image/svg+xml,' . str_replace( '#', '%23', rawurlencode( $svg ) ) . '">' . "\n";
	}

	/* FIX: the real page slugs on this site ('about-us', 'privacy',
	   'terms-and-conditions'...) didn't match what this file was looking
	   for ('about', 'privacy-policy'...), so those pages silently
	   dropped out of the About dropdown and footer. This tries every
	   real slug the page might have. */
	private function find_legal_page( $slugs ) {
		foreach ( $slugs as $slug ) {
			$page = get_page_by_path( $slug );
			if ( $page ) {
				return $page;
			}
		}
		return null;
	}

	public function render_header() {
		wp_enqueue_style( 'qcalc-homepage-style' );
		wp_enqueue_script( 'qcalc-homepage-script' );

		$rules       = QCalc_Catalog::get_rules();
		$about_page  = $this->find_legal_page( array( 'about-us', 'about' ) );
		$privacy     = $this->find_legal_page( array( 'privacy', 'privacy-policy' ) );
		$contact     = $this->find_legal_page( array( 'contact-us', 'contact' ) );
		$terms       = $this->find_legal_page( array( 'terms-and-conditions', 'terms-of-service', 'terms' ) );
		/* FIX: this used to fall back to home_url('/blog/') unconditionally,
		   which produces a 404 link in the main navigation on any install
		   where no posts page and no /blog/ page exist. Now it only falls
		   back to a destination that actually resolves, and the Blog item is
		   skipped entirely if there is nothing to link to. */
		$blog_url = get_permalink( get_option( 'page_for_posts' ) );
		if ( ! $blog_url ) {
			$blog_page = get_page_by_path( 'blog' );
			if ( $blog_page ) {
				$blog_url = get_permalink( $blog_page );
			} elseif ( wp_count_posts( 'post' )->publish > 0 ) {
				// Posts exist but there's no dedicated page for them; the
				// site root lists them on a default WordPress setup.
				$blog_url = home_url( '/blog/' );
			} else {
				$blog_url = '';
			}
		}
		?>
		<?php /* Accessibility/SEO: was a bare <div>. Using real landmark
		         elements lets assistive tech (and crawlers parsing page
		         structure) identify the site header and its navigation. */ ?>
		<header class="qh-site-header">
			<div class="qh-topnav">
				<a class="qh-topnav-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">⚡ <?php echo esc_html( get_bloginfo( 'name' ) ); ?></a>
				<nav class="qh-topnav-links" aria-label="Primary">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>

					<div class="qh-dropdown">
						<button type="button" class="qh-dropdown-toggle" aria-expanded="false" aria-haspopup="true">All Tools <span aria-hidden="true">▾</span></button>
						<div class="qh-dropdown-menu">
							<?php foreach ( $rules as $rule ) : ?>
								<a href="<?php echo esc_url( home_url( '/#' . QCalc_Catalog::slugify_category( $rule['name'] ) ) ); ?>"><?php echo esc_html( $rule['name'] ); ?></a>
							<?php endforeach; ?>
						</div>
					</div>

					<?php if ( $blog_url ) : ?>
						<a href="<?php echo esc_url( $blog_url ); ?>">Blog</a>
					<?php endif; ?>

					<div class="qh-dropdown">
						<button type="button" class="qh-dropdown-toggle" aria-expanded="false" aria-haspopup="true">About <span aria-hidden="true">▾</span></button>
						<div class="qh-dropdown-menu">
							<?php if ( $about_page ) : ?>
								<a href="<?php echo esc_url( get_permalink( $about_page ) ); ?>">About Us</a>
							<?php endif; ?>
							<?php if ( $privacy ) : ?>
								<a href="<?php echo esc_url( get_permalink( $privacy ) ); ?>">Privacy Policy</a>
							<?php endif; ?>
							<?php if ( $contact ) : ?>
								<a href="<?php echo esc_url( get_permalink( $contact ) ); ?>">Contact Us</a>
							<?php endif; ?>
							<?php if ( $terms ) : ?>
								<a href="<?php echo esc_url( get_permalink( $terms ) ); ?>">Terms and Conditions</a>
							<?php endif; ?>
						</div>
					</div>
				</nav>
				<button type="button" class="qh-theme-toggle" aria-label="Switch to dark mode" aria-pressed="false"><span aria-hidden="true">🌙</span></button>
			</div>
		</header>
		<?php
	}

	public function render_footer() {
		?>
		<footer class="qh-site-footer">
			<div class="qh-footer">
				<p>Copyright © <?php echo esc_html( date( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
				<p class="qh-footer-links">
					<?php
					$privacy = $this->find_legal_page( array( 'privacy', 'privacy-policy' ) );
					$terms   = $this->find_legal_page( array( 'terms-and-conditions', 'terms-of-service', 'terms' ) );
					$contact = $this->find_legal_page( array( 'contact-us', 'contact' ) );
					$links   = array();
					if ( $privacy ) {
						$links[] = '<a href="' . esc_url( get_permalink( $privacy ) ) . '">Privacy Policy</a>';
					}
					if ( $terms ) {
						// FIX: this used to link to home_url('/terms') which isn't a real
						// page on this site (404) — now links to the actual Terms page.
						$links[] = '<a href="' . esc_url( get_permalink( $terms ) ) . '">Terms</a>';
					}
					if ( $contact ) {
						$links[] = '<a href="' . esc_url( get_permalink( $contact ) ) . '">Contact</a>';
					}
					echo wp_kses_post( implode( ' · ', $links ) );
					?>
				</p>
				<?php
				/* Social profile links — only rendered when at least one profile
				   URL has been entered in iLoveTool3 Suite → SEO Settings. */
				echo class_exists( 'ILT3_SEO_Social' ) ? wp_kses_post( ILT3_SEO_Social::footer_html() ) : '';
				?>
			</div>
		</footer>
		<?php
	}
}
