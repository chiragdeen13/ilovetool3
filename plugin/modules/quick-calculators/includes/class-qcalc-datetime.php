<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QCalc_DateTime {

	public function __construct() {
		add_shortcode( 'date_difference_calculator', array( $this, 'render_date_diff' ) );
	}

	private function enqueue() {
		wp_enqueue_style( 'qcalc-style' );
		wp_enqueue_script( 'qcalc-script' );
	}

	public function render_date_diff() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="date-diff">
			<h2 class="qcalc-title">Date Difference Calculator</h2>
			<p class="qcalc-subtitle">Find the number of days, weeks, and months between two dates.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Start Date
					<input type="date" class="qcalc-dd-start" required />
				</label>
				<label>End Date
					<input type="date" class="qcalc-dd-end qcalc-autotoday" required />
				</label>
				<button type="button" class="qcalc-btn qcalc-dd-btn">Calculate</button>
			</form>
			<div class="qcalc-result qcalc-dd-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}
}
