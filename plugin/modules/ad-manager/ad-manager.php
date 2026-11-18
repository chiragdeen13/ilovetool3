<?php
/*
Module: Ad Manager
Description: Outputs the Google AdSense review/verification script in
every page's <head>, plus 5 ad slots laid out as: a LEFT sidebar ad, a
RIGHT sidebar ad (both alongside the main content on desktop), ONE ad
directly below the tool/content, and TWO ads within the content area
(top and bottom). Works on every page — homepage, tool pages, and
About/Contact/blog pages alike.

Nothing is hardcoded: paste your real Adsterra ad codes into
Settings → Ad Manager. An empty slot renders nothing to real visitors;
logged-in admins see a small dashed placeholder box instead, so you can
check positions before you have real codes.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ILT3_AdManager {

	const OPTION_NAME = 'ilt3_ad_settings';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// AdSense verification script — must be in <head> on every page.
		add_action( 'wp_head', array( $this, 'render_adsense_script' ), 5 );

		// Blog posts aren't touched by QCalc_PageEnhancer (that only
		// covers Pages), so this wraps them in the same sidebar layout
		// + in-content slots, so every page type gets all 5 ad slots.
		add_filter( 'the_content', array( $this, 'inject_into_posts' ) );
	}

	public function inject_into_posts( $content ) {
		if ( ! is_single() || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		$top    = ilt3_ad_slot( 4 );
		$bottom = ilt3_ad_slot( 5 );
		$below  = ilt3_ad_slot( 3 );

		return ilt3_ad_layout_start() . $top . $content . $below . $bottom . ilt3_ad_layout_end();
	}

	private function get_options() {
		return wp_parse_args(
			get_option( self::OPTION_NAME, array() ),
			array(
				'adsense_client' => 'ca-pub-2145538352518444',
				'slot_1'         => '',
				'slot_2'         => '',
				'slot_3'         => '',
				'slot_4'         => '',
				'slot_5'         => '',
			)
		);
	}

	public function render_adsense_script() {
		$opts = $this->get_options();
		if ( empty( $opts['adsense_client'] ) ) {
			return;
		}
		printf(
			'<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=%s" crossorigin="anonymous"></script>' . "\n",
			esc_attr( $opts['adsense_client'] )
		);
	}

	public function add_settings_page() {
		// FIX (consolidation): was under Settings -> Ad Manager (separate from
		// every other module's admin page); now a submenu of the shared
		// "iLoveTool3 Suite" parent menu registered in imei-check-services.php.
		add_submenu_page(
			'imei-master',
			'Ad Manager',
			'Ad Manager',
			'manage_options',
			'ilt3-ad-manager',
			array( $this, 'render_settings_page' )
		);
	}

	public function register_settings() {
		register_setting( 'ilt3_ad_settings_group', self::OPTION_NAME );
	}

	public function render_settings_page() {
		$opts = $this->get_options();
		?>
		<div class="wrap">
			<h1>Ad Manager</h1>
			<p>The AdSense script below is added to every page's <code>&lt;head&gt;</code> automatically. Paste your Adsterra (or any other network's) ad codes into the 5 slots below — every page (homepage, tool pages, About/Contact, blog posts) gets the same layout: a left sidebar ad, a right sidebar ad, one ad below the tool/content, and two ads within the content. Leave a slot blank to skip it — nothing broken shows to visitors, only a small "not configured" box that only you (as admin) can see.</p>

			<form method="post" action="options.php">
				<?php settings_fields( 'ilt3_ad_settings_group' ); ?>

				<table class="form-table">
					<tr>
						<th><label>AdSense Publisher/Client ID</label></th>
						<td>
							<input type="text" style="width:400px" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[adsense_client]" value="<?php echo esc_attr( $opts['adsense_client'] ); ?>" placeholder="ca-pub-xxxxxxxxxxxxxxxx" />
							<p class="description">This generates the required <code>&lt;script&gt;</code> tag in every page's <code>&lt;head&gt;</code>.</p>
						</td>
					</tr>
					<tr><th colspan="2"><h2>Ad Slots</h2></th></tr>
					<tr>
						<th><label>Slot 1 — Left sidebar (every page)</label></th>
						<td><textarea name="<?php echo esc_attr( self::OPTION_NAME ); ?>[slot_1]" rows="4" style="width:100%;font-family:monospace;"><?php echo esc_textarea( $opts['slot_1'] ); ?></textarea>
						<p class="description">Best size: a narrow vertical banner (e.g. 160×600 or 120×600). Stacks above the content on mobile/tablet where there's no room for a sidebar.</p></td>
					</tr>
					<tr>
						<th><label>Slot 2 — Right sidebar (every page)</label></th>
						<td><textarea name="<?php echo esc_attr( self::OPTION_NAME ); ?>[slot_2]" rows="4" style="width:100%;font-family:monospace;"><?php echo esc_textarea( $opts['slot_2'] ); ?></textarea>
						<p class="description">Same sizing as the left sidebar. Stacks below the content on mobile/tablet.</p></td>
					</tr>
					<tr>
						<th><label>Slot 3 — Directly below the tool/content</label></th>
						<td><textarea name="<?php echo esc_attr( self::OPTION_NAME ); ?>[slot_3]" rows="4" style="width:100%;font-family:monospace;"><?php echo esc_textarea( $opts['slot_3'] ); ?></textarea></td>
					</tr>
					<tr>
						<th><label>Slot 4 — In-content, near the top</label></th>
						<td><textarea name="<?php echo esc_attr( self::OPTION_NAME ); ?>[slot_4]" rows="4" style="width:100%;font-family:monospace;"><?php echo esc_textarea( $opts['slot_4'] ); ?></textarea></td>
					</tr>
					<tr>
						<th><label>Slot 5 — In-content, further down (between Related &amp; Popular Tools)</label></th>
						<td><textarea name="<?php echo esc_attr( self::OPTION_NAME ); ?>[slot_5]" rows="4" style="width:100%;font-family:monospace;"><?php echo esc_textarea( $opts['slot_5'] ); ?></textarea></td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>

			<p style="color:#8a5c00;background:#fff3cd;border:1px solid #ffe58f;padding:10px 14px;border-radius:8px;">⚠️ Reminder: only use banner/native Adsterra ad formats here. Popunder/interstitial formats violate Google's Better Ads Standards and can get your AdSense application rejected or a later account disabled if run alongside AdSense.</p>
		</div>
		<?php
	}
}

new ILT3_AdManager();

/**
 * Outputs the raw ad code for a given slot (1-5), or — for logged-in
 * admins only, when the slot is empty — a small placeholder box so you
 * can see where each ad will sit before you have real codes. Regular
 * visitors see nothing at all for an empty slot.
 */
function ilt3_ad_slot( $n ) {
	$opts = get_option( ILT3_AdManager::OPTION_NAME, array() );
	$code = isset( $opts[ 'slot_' . $n ] ) ? trim( $opts[ 'slot_' . $n ] ) : '';

	if ( $code !== '' ) {
		return '<div class="ilt3-ad-slot ilt3-ad-slot-' . intval( $n ) . '">' . $code . '</div>';
	}

	if ( current_user_can( 'manage_options' ) ) {
		return '<div class="ilt3-ad-slot ilt3-ad-placeholder">Ad Slot ' . intval( $n ) . '<br>(not configured — only visible to admins)</div>';
	}

	return '';
}

/**
 * Opens the 3-column layout: left sidebar ad | main content column.
 * Call ilt3_ad_layout_end() to close it (which renders the right
 * sidebar ad and closes the wrapping tags). On narrow screens the CSS
 * stacks these full-width above/below the main column instead of
 * side-by-side, so the ads keep showing rather than disappearing.
 */
function ilt3_ad_layout_start() {
	return '<div class="ilt3-layout">'
		. '<aside class="ilt3-sidebar ilt3-sidebar-left">' . ilt3_ad_slot( 1 ) . '</aside>'
		. '<div class="ilt3-main">';
}

function ilt3_ad_layout_end() {
	return '</div>'
		. '<aside class="ilt3-sidebar ilt3-sidebar-right">' . ilt3_ad_slot( 2 ) . '</aside>'
		. '</div>';
}
