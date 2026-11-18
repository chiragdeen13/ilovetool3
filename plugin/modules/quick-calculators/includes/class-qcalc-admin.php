<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QCalc_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
	}

	public function add_page() {
		// FIX (consolidation): was under Settings -> Quick Calculators; now a
		// submenu of the shared "iLoveTool3 Suite" parent menu.
		add_submenu_page(
			'imei-master',
			'Quick Calculators',
			'Quick Calculators',
			'manage_options',
			'quick-calculators',
			array( $this, 'render_page' )
		);
	}

	public function render_page() {
		?>
		<div class="wrap">
			<h1>Quick Calculators</h1>
			<p>No API keys or settings needed — everything runs in the browser. Add any shortcode below to a Page or Post.</p>
			<table class="widefat striped" style="max-width:700px">
				<thead>
					<tr><th>Calculator</th><th>Shortcode</th></tr>
				</thead>
				<tbody>
					<tr><td>Age Calculator</td><td><code>[age_calculator]</code></td></tr>
					<tr><td>BMI Calculator</td><td><code>[bmi_calculator]</code></td></tr>
					<tr><td>Percentage Calculator</td><td><code>[percentage_calculator]</code></td></tr>
					<tr><td>GPA Calculator</td><td><code>[gpa_calculator]</code></td></tr>

					<tr><td colspan="2"><strong>Finance</strong></td></tr>
					<tr><td>Simple Interest Calculator</td><td><code>[simple_interest_calculator]</code></td></tr>
					<tr><td>Compound Interest Calculator</td><td><code>[compound_interest_calculator]</code></td></tr>
					<tr><td>Loan / EMI Calculator</td><td><code>[loan_calculator]</code></td></tr>
					<tr><td>Discount Calculator</td><td><code>[discount_calculator]</code></td></tr>
					<tr><td>Tip Calculator</td><td><code>[tip_calculator]</code></td></tr>

					<tr><td colspan="2"><strong>Converters</strong></td></tr>
					<tr><td>Length Converter</td><td><code>[length_converter]</code></td></tr>
					<tr><td>Weight Converter</td><td><code>[weight_converter]</code></td></tr>
					<tr><td>Temperature Converter</td><td><code>[temperature_converter]</code></td></tr>
					<tr><td>Area Calculator</td><td><code>[area_calculator]</code></td></tr>
					<tr><td>Binary/Decimal/Hex Converter</td><td><code>[number_base_converter]</code></td></tr>
					<tr><td>Roman Numeral Converter</td><td><code>[roman_numeral_converter]</code></td></tr>
					<tr><td>Number to Words Converter</td><td><code>[number_to_words_converter]</code></td></tr>

					<tr><td colspan="2"><strong>Text Tools</strong></td></tr>
					<tr><td>Word Counter</td><td><code>[word_counter]</code></td></tr>
					<tr><td>Character Counter</td><td><code>[character_counter]</code></td></tr>
					<tr><td>Text Case Converter</td><td><code>[text_case_converter]</code></td></tr>
					<tr><td>Password Generator</td><td><code>[password_generator]</code></td></tr>

					<tr><td colspan="2"><strong>Health</strong></td></tr>
					<tr><td>BMR Calculator</td><td><code>[bmr_calculator]</code></td></tr>
					<tr><td>Ideal Weight Calculator</td><td><code>[ideal_weight_calculator]</code></td></tr>
					<tr><td>Pregnancy Due Date Calculator</td><td><code>[pregnancy_due_date_calculator]</code></td></tr>

					<tr><td colspan="2"><strong>Math</strong></td></tr>
					<tr><td>Average Calculator</td><td><code>[average_calculator]</code></td></tr>
					<tr><td>Percentage Change Calculator</td><td><code>[percentage_change_calculator]</code></td></tr>
					<tr><td>Marks Percentage Calculator</td><td><code>[marks_percentage_calculator]</code></td></tr>
					<tr><td>Speed/Distance/Time Calculator</td><td><code>[speed_distance_time_calculator]</code></td></tr>
					<tr><td>Ratio Calculator</td><td><code>[ratio_calculator]</code></td></tr>

					<tr><td colspan="2"><strong>Date/Time</strong></td></tr>
					<tr><td>Date Difference Calculator</td><td><code>[date_difference_calculator]</code></td></tr>

					<tr><td colspan="2"><strong>Homepage</strong></td></tr>
					<tr><td>Tools Directory Homepage</td><td><code>[tools_homepage]</code></td></tr>
					<tr><td>Related Tools (optional — now automatic on every tool page too)</td><td><code>[related_tools]</code></td></tr>
				</tbody>
			</table>
			<p style="margin-top:16px">The homepage now auto-categorizes <strong>every published page on your site</strong> by its title — no manual list to maintain. New pages you create will appear in the right category automatically. See <a href="<?php echo esc_url( admin_url( 'admin.php?page=quick-calculators-duplicates' ) ); ?>">Duplicate Pages</a> to check for accidental duplicate pages.</p>
			<p><strong>Also new:</strong> a header (Home / All Tools / Blog / About) and footer with dark-mode toggle now appear automatically on <em>every</em> page — no setup needed. Every tool page also automatically gets a breadcrumb + back button, 8 Related Tools, and 8 Popular Tools appended below its content.</p>
			<p style="color:#8a8a9a;">Note: most themes (including Astra) already render their own header/nav. If you see two headers stacked, hide the theme's default one in Appearance → Customize (Astra: Header Builder) so only this plugin's header shows.</p>
		</div>
		<?php
	}
}
