<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the homepage CONTENT via [tools_homepage] — hero, search, IMEI
 * card, popular tools, and the full categorized tool grid. The site-wide
 * header/footer/dark-mode-toggle come from QCalc_SiteChrome instead (so
 * they appear on every page, not just here).
 *
 * Fully dynamic: every published page is auto-sorted into one of the 15
 * categories (see QCalc_Catalog) by keyword-matching its title.
 */
class QCalc_Homepage {

	public function __construct() {
		add_shortcode( 'tools_homepage', array( $this, 'render' ) );
	}

	public function render() {
		wp_enqueue_style( 'qcalc-style' );
		wp_enqueue_script( 'qcalc-script' );

		$current_id     = is_page() ? get_queried_object_id() : 0;
		$pages          = QCalc_Catalog::get_categorized_pages( $current_id );
		$total_tools    = count( $pages );

		$ordered_names = QCalc_Catalog::get_category_icons();
		$grouped       = array_fill_keys( $ordered_names, array() );
		foreach ( $pages as $page ) {
			$grouped[ $page['category'] ][] = $page;
		}

		$popular = class_exists( 'QCalc_ViewCounter' ) ? QCalc_ViewCounter::get_popular_pages( 8, $current_id ) : array();

		$duplicate_groups = current_user_can( 'manage_options' ) ? QCalc_Catalog::find_duplicate_pages() : array();

		ob_start();
		$use_ad_layout = function_exists( 'ilt3_ad_layout_start' );
		?>
		<?php if ( $use_ad_layout ) : ?><?php echo ilt3_ad_layout_start(); ?><?php endif; ?>
		<div class="qh-wrap" id="qh-all-tools">

			<?php if ( ! empty( $duplicate_groups ) ) : ?>
				<div class="qh-admin-notice">
					⚠️ <?php echo count( $duplicate_groups ); ?> possible duplicate page group(s) found (only you, as admin, see this).
					<a href="<?php echo esc_url( admin_url( 'options-general.php?page=quick-calculators-duplicates' ) ); ?>" class="qh-admin-notice-btn">Review Duplicates</a>
				</div>
			<?php endif; ?>

			<div class="qh-hero">
				<?php
				/* SEO: this is the page's single primary H1. It used to be
				   just the site name, which told a search engine nothing
				   about what the page offers. Now it describes the page
				   while still leading with the brand. The bolt is marked
				   aria-hidden because it is decoration, not part of the
				   heading a screen reader should announce. */
				?>
				<h1 class="qh-hero-title">
					<span aria-hidden="true">⚡</span>
					<?php echo esc_html( get_bloginfo( 'name' ) ); ?> — Free Online Tools &amp; Calculators
				</h1>
				<p class="qh-hero-sub"><?php echo intval( $total_tools ); ?>+ Free Online Tools — Fast, Free &amp; No Registration!</p>
				<div class="qh-badges">
					<span class="qh-badge">✅ 100% Free</span>
					<span class="qh-badge">🔒 Privacy Friendly</span>
					<span class="qh-badge">⚡ Instant Results</span>
					<span class="qh-badge">📱 Works on Mobile</span>
				</div>
			</div>

			<?php /* Accessibility: the search box had no label of any kind. */ ?>
			<label for="qh-tool-search" class="qh-visually-hidden">Search all tools</label>
			<input type="search" id="qh-tool-search" class="qh-search" autocomplete="off"
				aria-label="Search all tools"
				placeholder="Type to search tools... (e.g. IMEI, calculator, password)" />

			<?php echo $use_ad_layout ? ilt3_ad_slot( 4 ) : ''; ?>
			<?php echo $use_ad_layout ? ilt3_ad_slot( 3 ) : ''; ?>

			<?php if ( ! empty( $popular ) ) : ?>
				<div class="qh-category">
					<h2 class="qh-category-title">🔥 Popular Tools <span class="qh-count"><?php echo count( $popular ); ?></span></h2>
					<div class="qh-grid">
						<?php foreach ( $popular as $tool ) : ?>
							<div class="qh-card" data-name="<?php echo esc_attr( strtolower( $tool['name'] ) ); ?>">
								<div class="qh-card-icon">🔥</div>
								<div class="qh-card-body">
									<div class="qh-card-name"><?php echo esc_html( $tool['name'] ); ?></div>
								</div>
								<a class="qh-card-btn" href="<?php echo esc_url( get_permalink( $tool['id'] ) ); ?>">Open</a>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php echo $use_ad_layout ? ilt3_ad_slot( 5 ) : ''; ?>

			<?php /* SEO/outline: the category headings below are H3s so they sit
			         UNDER this H2 instead of beside it — that gives the page a
			         real H1 > H2 > H3 outline. Same class, so styling is unchanged. */ ?>
			<h2 class="qh-section-title">🛠️ Browse All <?php echo intval( $total_tools ); ?>+ Tools</h2>

			<?php foreach ( $grouped as $cat_name => $tools ) : ?>
				<?php if ( empty( $tools ) ) : continue; endif; ?>
				<?php
				preg_match( '/^(\S+)\s*(.*)$/u', $cat_name, $cat_parts );
				$cat_icon = isset( $cat_parts[1] ) ? $cat_parts[1] : '🔧';
				$cat_id   = QCalc_Catalog::slugify_category( $cat_name );
				?>
				<div class="qh-category" id="<?php echo esc_attr( $cat_id ); ?>">
					<h3 class="qh-category-title"><?php echo esc_html( $cat_name ); ?> <span class="qh-count"><?php echo count( $tools ); ?></span></h3>
					<div class="qh-grid">
						<?php foreach ( $tools as $tool ) : ?>
							<div class="qh-card" data-name="<?php echo esc_attr( strtolower( $tool['name'] ) ); ?>">
								<div class="qh-card-icon"><?php echo esc_html( $cat_icon ); ?></div>
								<div class="qh-card-body">
									<div class="qh-card-name"><?php echo esc_html( $tool['name'] ); ?></div>
									<div class="qh-card-slug">/<?php echo esc_html( $tool['slug'] ); ?></div>
								</div>
								<a class="qh-card-btn" href="<?php echo esc_url( get_permalink( $tool['id'] ) ); ?>">Open</a>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>

			<?php
			/* ------------------------------------------------------------
			   Homepage explanatory content.

			   The homepage was almost entirely link cards — a crawler could
			   see hundreds of tool names but nothing describing what this
			   site is or who it is for. These three short sections fix that
			   with information a first-time visitor actually wants: what
			   the site does, why the tools are built the way they are, and
			   how to find the one you need.

			   Deliberately short and specific. No repeated tool names, no
			   padded paragraphs, no invented statistics or claims.
			   ------------------------------------------------------------ */
			$site_name = get_bloginfo( 'name' );
			?>
			<section class="qh-about" id="about-this-site">
				<h2>What is <?php echo esc_html( $site_name ); ?>?</h2>
				<p>
					<?php echo esc_html( $site_name ); ?> is a collection of <?php echo intval( $total_tools ); ?>
					small, single-purpose web tools — calculators, converters, checkers and generators — that each do
					one job and do it immediately. There is no account to create, nothing to install, and no free
					trial that expires. Open a tool, enter your numbers or text, and you have your answer.
				</p>
				<p>
					The tools cover everyday problems from quite different areas: checking whether a phone's IMEI is
					valid before you buy it, working out a loan repayment or a Zakat obligation, converting a PDF into
					another format, counting the words in an essay, or formatting a block of JSON while debugging.
					They are grouped into the categories above so related tools sit together.
				</p>

				<h2>Why use these tools</h2>
				<p>
					Almost everything here runs in your own browser rather than on a server. Your numbers, your text
					and your files stay on your device, which means results appear instantly and there is nothing
					sensitive sitting in someone else's logs afterwards. The few tools that genuinely need a server —
					certain file conversions and live exchange rates — say so on the page itself.
				</p>
				<p>
					Each tool page also explains the method behind the result: which formula a calculator uses, what a
					checksum actually proves, where a standard comes from. That matters when you are relying on the
					number for something real, like a medical measurement or a loan decision, and want to sanity-check
					it against another source.
				</p>

				<h2>Finding the right tool</h2>
				<p>
					There are three ways in. Use the search box near the top of this page to filter every tool by name
					as you type. Browse the category sections above if you know roughly what kind of tool you need but
					not its exact name. Or open the <strong>All Tools</strong> menu in the site header to jump straight
					to a category.
				</p>
				<p>
					Every tool page also lists related tools from the same category at the bottom, which is usually the
					fastest route when one calculation leads into another.
				</p>

				<h2>Practical ways to use the tools</h2>
				<h3>Before buying a used phone</h3>
				<p>
					Run the IMEI through the validator first. A number that fails the check-digit test is a warning sign
					of a mistyped or made-up IMEI. Then dial <strong>*#06#</strong> on the phone itself and confirm that the
					number it shows matches the one on the box and in the settings menu.
				</p>
				<h3>Working out money</h3>
				<p>
					For a loan, enter the amount, the rate and the term, then compare the total repaid across several terms
					rather than looking only at the monthly instalment. A longer term lowers the payment but usually raises
					the total interest you pay.
				</p>
				<h3>Cleaning up text and files</h3>
				<p>
					Word and character counters help you stay inside the limits for an essay, an application or a post.
					Formatters and converters save time when you are debugging JSON, encoding a value, or turning a
					document into another format.
				</p>

				<h2>Frequently asked questions</h2>
				<h3>Are these tools really free?</h3>
				<p>
					Yes. You do not need an account or a subscription to use any of them, and the site is supported by
					advertising rather than by charging for results.
				</p>
				<h3>Can I trust the results?</h3>
				<p>
					The calculators use standard, documented formulas, and each tool page explains the method so you can
					check the working yourself. For medical, legal, tax or financial decisions, treat the result as a
					guide and confirm it with a qualified professional or the official source.
				</p>
				<h3>Does the IMEI validator show whether a phone is stolen or blocked?</h3>
				<p>
					No. It checks that the number is 15 digits long and passes the standard check-digit (Luhn) test, which
					confirms the number is well-formed. It cannot show a phone's ownership history or whether a carrier has
					blocked it. For that, ask the seller for proof of purchase and check with your mobile carrier.
				</p>
				<h3>Do the tools work on a phone or tablet?</h3>
				<p>
					Yes. The layout adapts to phone, tablet and desktop screens, and there is a dark-mode switch in the
					header if you prefer a darker page.
				</p>
				<h3>How many tools are there, and are new ones added?</h3>
				<p>
					There are currently <?php echo intval( $total_tools ); ?> tools, and the list grows over time. A new tool
					appears in its category automatically once it is published.
				</p>
				<h3>How do I suggest a tool or report a problem?</h3>
				<p>
					<?php
					$contact_page = get_page_by_path( 'contact-us' );
					if ( ! $contact_page ) {
						$contact_page = get_page_by_path( 'contact' );
					}
					if ( $contact_page && 'publish' === $contact_page->post_status ) :
						?>
						Send a message through the <a href="<?php echo esc_url( get_permalink( $contact_page ) ); ?>">Contact page</a>.
					<?php else : ?>
						Get in touch through the contact details listed on this site.
					<?php endif; ?>
					Name the tool and describe what went wrong, or what you would like it to do, and include the numbers
					you entered if a result looked incorrect.
				</p>
			</section>

		</div>
		<?php if ( $use_ad_layout ) : ?><?php echo ilt3_ad_layout_end(); ?><?php endif; ?>
		<?php
		return ob_get_clean();
	}
}
