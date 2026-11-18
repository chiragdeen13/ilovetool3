<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin screen for the handful of SEO values that are genuinely editorial
 * decisions rather than code: the homepage title/description, the social
 * sharing image, and the on/off switches for the output-level fixes.
 *
 * Sensible defaults are baked in, so this page is optional — the site is
 * fully optimised without anyone ever opening it.
 */
class ILT3_SEO_Settings {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_page' ), 11 );
		add_action( 'admin_init', array( $this, 'register' ) );
	}

	public function add_page() {
		add_submenu_page(
			'imei-master',
			'SEO Settings',
			'SEO Settings',
			'manage_options',
			'ilt3-seo',
			array( $this, 'render' )
		);
	}

	public function register() {
		register_setting(
			'ilt3_seo_group',
			ILT3_SEO::OPTION_NAME,
			array( 'sanitize_callback' => array( $this, 'sanitize' ) )
		);
	}

	public function sanitize( $input ) {
		$input = is_array( $input ) ? $input : array();

		$ga4 = strtoupper( trim( sanitize_text_field( $input['ga4_id'] ?? '' ) ) );
		if ( '' !== $ga4 && ! ILT3_SEO_Analytics::valid_id( $ga4 ) ) {
			add_settings_error(
				ILT3_SEO::OPTION_NAME,
				'ilt3_bad_ga4',
				'That does not look like a GA4 Measurement ID (it starts with G- followed by letters and digits, e.g. G-ABC123DEF4). It was not saved.'
			);
			$ga4 = '';
		}

		$out = array(
			'home_title'       => sanitize_text_field( $input['home_title'] ?? '' ),
			'home_description' => sanitize_text_field( $input['home_description'] ?? '' ),
			'og_image'         => esc_url_raw( trim( $input['og_image'] ?? '' ) ),
			'twitter_site'     => sanitize_text_field( $input['twitter_site'] ?? '' ),
			'fix_duplicate_h1' => empty( $input['fix_duplicate_h1'] ) ? 0 : 1,
			'fix_missing_alt'  => empty( $input['fix_missing_alt'] ) ? 0 : 1,
			'clean_head'       => empty( $input['clean_head'] ) ? 0 : 1,
			'disable_emojis'   => empty( $input['disable_emojis'] ) ? 0 : 1,
			'llms_txt'         => empty( $input['llms_txt'] ) ? 0 : 1,
			'ga4_id'           => $ga4,
		);

		// Social profile URLs — http(s) only, blank means "not used".
		foreach ( array_keys( ILT3_SEO_Social::NETWORKS ) as $key ) {
			$out[ $key ] = esc_url_raw( trim( $input[ $key ] ?? '' ), array( 'http', 'https' ) );
		}

		return $out;
	}

	public function render() {
		$opts = ILT3_SEO::options();
		$name = ILT3_SEO::OPTION_NAME;
		?>
		<div class="wrap">
			<h1>SEO Settings</h1>
			<?php settings_errors( ILT3_SEO::OPTION_NAME ); ?>

			<?php if ( ILT3_SEO::seo_plugin_active() ) : ?>
				<div class="notice notice-warning">
					<p><strong>A dedicated SEO plugin is active.</strong> This module has stepped aside and is not
					outputting titles, meta descriptions, canonicals or structured data, so the site does not end up
					with two competing sets of tags. Only the robots.txt, sitemap and performance fixes below are
					still applied.</p>
				</div>
			<?php endif; ?>

			<p>Everything on this page is optional — sensible defaults are already applied. Fill a field in only when
			you want to override the default.</p>

			<form method="post" action="options.php">
				<?php settings_fields( 'ilt3_seo_group' ); ?>

				<h2>Homepage</h2>
				<table class="form-table">
					<tr>
						<th><label for="ilt3-home-title">Homepage SEO title</label></th>
						<td>
							<input type="text" id="ilt3-home-title" class="regular-text" style="width:520px"
								name="<?php echo esc_attr( $name ); ?>[home_title]"
								value="<?php echo esc_attr( $opts['home_title'] ); ?>" />
							<p class="description">
								Leave blank to use: <code><?php echo esc_html( ILT3_SEO::default_home_title() ); ?></code><br />
								Aim for roughly 50–60 characters so Google doesn't truncate it.
							</p>
						</td>
					</tr>
					<tr>
						<th><label for="ilt3-home-desc">Homepage meta description</label></th>
						<td>
							<textarea id="ilt3-home-desc" rows="3" style="width:520px"
								name="<?php echo esc_attr( $name ); ?>[home_description]"><?php echo esc_textarea( $opts['home_description'] ); ?></textarea>
							<p class="description">
								Leave blank to use: <code><?php echo esc_html( ILT3_SEO::default_home_description() ); ?></code><br />
								Aim for roughly 150–160 characters.
							</p>
						</td>
					</tr>
				</table>

				<h2>Social sharing</h2>
				<table class="form-table">
					<tr>
						<th><label for="ilt3-og-image">Sharing image URL (og:image)</label></th>
						<td>
							<input type="url" id="ilt3-og-image" class="regular-text" style="width:520px"
								name="<?php echo esc_attr( $name ); ?>[og_image]"
								value="<?php echo esc_attr( $opts['og_image'] ); ?>"
								placeholder="https://example.com/wp-content/uploads/social-card.png" />
							<p class="description">
								Upload an image to the Media Library (1200×630 PNG or JPG works best) and paste its URL here.
								<?php if ( has_site_icon() ) : ?>
									Currently falling back to your Site Icon:
									<code><?php echo esc_html( get_site_icon_url( 512 ) ); ?></code>
								<?php else : ?>
									<strong>No image is set and no Site Icon exists</strong>, so <code>og:image</code> and
									<code>twitter:image</code> are being omitted on purpose — a broken image URL is worse
									than none. Set either one and they start appearing automatically.
								<?php endif; ?>
							</p>
						</td>
					</tr>
					<tr>
						<th><label for="ilt3-twitter">X / Twitter handle</label></th>
						<td>
							<input type="text" id="ilt3-twitter" class="regular-text"
								name="<?php echo esc_attr( $name ); ?>[twitter_site]"
								value="<?php echo esc_attr( $opts['twitter_site'] ); ?>" placeholder="@yourhandle" />
							<p class="description">Optional. Used for <code>twitter:site</code>. Leave blank if you have no account.</p>
						</td>
					</tr>
				</table>

				<h2>Social profiles</h2>
				<p>Paste the full address of each official profile you actually have. Filled-in profiles are linked in the
				site footer and added to the site's structured data. Leave a field blank to skip that network — nothing
				is shown for empty ones.</p>
				<table class="form-table">
					<?php
					$social_placeholders = array(
						'social_facebook'  => 'https://www.facebook.com/yourpage',
						'social_x'         => 'https://x.com/yourhandle',
						'social_instagram' => 'https://www.instagram.com/yourhandle',
						'social_linkedin'  => 'https://www.linkedin.com/company/yourcompany',
						'social_youtube'   => 'https://www.youtube.com/@yourchannel',
					);
					foreach ( ILT3_SEO_Social::NETWORKS as $key => $label ) :
						?>
						<tr>
							<th><label for="ilt3-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
							<td>
								<input type="url" id="ilt3-<?php echo esc_attr( $key ); ?>" class="regular-text" style="width:520px"
									name="<?php echo esc_attr( $name ); ?>[<?php echo esc_attr( $key ); ?>]"
									value="<?php echo esc_attr( $opts[ $key ] ); ?>"
									placeholder="<?php echo esc_attr( $social_placeholders[ $key ] ); ?>" />
							</td>
						</tr>
					<?php endforeach; ?>
				</table>

				<h2>Analytics</h2>
				<table class="form-table">
					<tr>
						<th><label for="ilt3-ga4">Google Analytics 4 ID</label></th>
						<td>
							<input type="text" id="ilt3-ga4" class="regular-text"
								name="<?php echo esc_attr( $name ); ?>[ga4_id]"
								value="<?php echo esc_attr( $opts['ga4_id'] ); ?>" placeholder="G-XXXXXXXXXX" />
							<p class="description">
								Optional. Adds the standard Google tag to every public page (logged-in administrators are not tracked).
								<strong>Leave blank if analytics is already added by another plugin</strong> (Site Kit, MonsterInsights,
								a header snippet…) — otherwise every visit would be counted twice.
							</p>
						</td>
					</tr>
				</table>

				<h2>Technical fixes</h2>
				<table class="form-table">
					<tr>
						<th>Heading structure</th>
						<td>
							<label>
								<input type="checkbox" value="1" name="<?php echo esc_attr( $name ); ?>[fix_duplicate_h1]"
									<?php checked( $opts['fix_duplicate_h1'], 1 ); ?> />
								Keep exactly one <code>&lt;h1&gt;</code> per page (extras above it become plain text blocks, extras below it become <code>&lt;h2&gt;</code>)
							</label>
							<p class="description">Needed because the theme prints its own H1 page title in addition to the one in the page content.</p>
						</td>
					</tr>
					<tr>
						<th>Image alt text</th>
						<td>
							<label>
								<input type="checkbox" value="1" name="<?php echo esc_attr( $name ); ?>[fix_missing_alt]"
									<?php checked( $opts['fix_missing_alt'], 1 ); ?> />
								Add alt text to any image that has none (theme logo, ad images, etc.)
							</label>
							<p class="description">Images that already have an alt attribute, including a deliberately empty one, are never changed.</p>
						</td>
					</tr>
					<tr>
						<th>Emoji script</th>
						<td>
							<label>
								<input type="checkbox" value="1" name="<?php echo esc_attr( $name ); ?>[disable_emojis]"
									<?php checked( $opts['disable_emojis'], 1 ); ?> />
								Disable WordPress's emoji replacement script
							</label>
							<p class="description">
								This is what was turning the emoji in your headings and tool cards into ~100 external
								images loaded from <code>s.w.org</code> on every page. Emoji still display normally —
								browsers render them natively.
							</p>
						</td>
					</tr>
					<tr>
						<th>Head cleanup</th>
						<td>
							<label>
								<input type="checkbox" value="1" name="<?php echo esc_attr( $name ); ?>[clean_head]"
									<?php checked( $opts['clean_head'], 1 ); ?> />
								Remove the WordPress generator, RSD and WLW manifest tags
							</label>
						</td>
					</tr>
					<tr>
						<th>llms.txt</th>
						<td>
							<label>
								<input type="checkbox" value="1" name="<?php echo esc_attr( $name ); ?>[llms_txt]"
									<?php checked( $opts['llms_txt'], 1 ); ?> />
								Publish an <code>/llms.txt</code> guide for AI assistants
							</label>
							<p class="description">
								Built automatically from your tool list and refreshed when pages change.
								<a href="<?php echo esc_url( home_url( '/llms.txt' ) ); ?>" target="_blank" rel="noopener">View llms.txt</a>
							</p>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>

			<h2>Where to check your work</h2>
			<ul style="list-style:disc;padding-left:22px">
				<li><a href="<?php echo esc_url( home_url( '/robots.txt' ) ); ?>" target="_blank" rel="noopener">View robots.txt</a></li>
				<li><a href="<?php echo esc_url( home_url( '/wp-sitemap.xml' ) ); ?>" target="_blank" rel="noopener">View XML sitemap</a></li>
				<li><a href="https://search.google.com/test/rich-results" target="_blank" rel="noopener">Google Rich Results Test</a> — paste a tool page URL to validate the structured data</li>
			</ul>
		</div>
		<?php
	}
}
