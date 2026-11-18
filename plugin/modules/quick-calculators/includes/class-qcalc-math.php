<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QCalc_Math {

	public function __construct() {
		add_shortcode( 'average_calculator', array( $this, 'render_average' ) );
		add_shortcode( 'percentage_change_calculator', array( $this, 'render_percentage_change' ) );
		add_shortcode( 'marks_percentage_calculator', array( $this, 'render_marks_percentage' ) );
		add_shortcode( 'speed_distance_time_calculator', array( $this, 'render_speed_distance_time' ) );
		add_shortcode( 'ratio_calculator', array( $this, 'render_ratio' ) );
	}

	private function enqueue() {
		wp_enqueue_style( 'qcalc-style' );
		wp_enqueue_script( 'qcalc-script' );
	}

	public function render_average() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="average">
			<h2 class="qcalc-title">Average Calculator</h2>
			<p class="qcalc-subtitle">Find the average (mean) of a list of numbers.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Enter numbers separated by commas
					<input type="text" class="qcalc-avg-input" placeholder="e.g. 10, 20, 30, 40" required />
				</label>
				<button type="button" class="qcalc-btn qcalc-avg-btn">Calculate</button>
			</form>
			<div class="qcalc-result qcalc-avg-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_percentage_change() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="percentage-change">
			<h2 class="qcalc-title">Percentage Change Calculator</h2>
			<p class="qcalc-subtitle">Find the percentage increase or decrease between two values.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Initial Value
					<input type="number" class="qcalc-pc-initial" step="any" required />
				</label>
				<label>New Value
					<input type="number" class="qcalc-pc-new" step="any" required />
				</label>
				<button type="button" class="qcalc-btn qcalc-pc-btn">Calculate</button>
			</form>
			<div class="qcalc-result qcalc-pc-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_marks_percentage() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="marks-percentage">
			<h2 class="qcalc-title">Marks Percentage Calculator</h2>
			<p class="qcalc-subtitle">Calculate your percentage from obtained and total marks.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Marks Obtained
					<input type="number" class="qcalc-mp-obtained" min="0" step="any" required />
				</label>
				<label>Total Marks
					<input type="number" class="qcalc-mp-total" min="1" step="any" required />
				</label>
				<button type="button" class="qcalc-btn qcalc-mp-btn">Calculate</button>
			</form>
			<div class="qcalc-result qcalc-mp-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_speed_distance_time() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="sdt">
			<h2 class="qcalc-title">Speed, Distance &amp; Time Calculator</h2>
			<p class="qcalc-subtitle">Enter any two values to calculate the third.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Distance (km) — leave blank to calculate
					<input type="number" class="qcalc-sdt-distance" step="any" />
				</label>
				<label>Speed (km/h) — leave blank to calculate
					<input type="number" class="qcalc-sdt-speed" step="any" />
				</label>
				<label>Time (hours) — leave blank to calculate
					<input type="number" class="qcalc-sdt-time" step="any" />
				</label>
				<button type="button" class="qcalc-btn qcalc-sdt-btn">Calculate</button>
			</form>
			<div class="qcalc-result qcalc-sdt-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_ratio() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="ratio">
			<h2 class="qcalc-title">Ratio Calculator</h2>
			<p class="qcalc-subtitle">Simplify a ratio or solve for a missing value in A:B = C:D.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Simplify Ratio — A
					<input type="number" class="qcalc-ratio-a" step="any" />
				</label>
				<label>Simplify Ratio — B
					<input type="number" class="qcalc-ratio-b" step="any" />
				</label>
				<button type="button" class="qcalc-btn qcalc-ratio-btn">Simplify</button>
			</form>
			<div class="qcalc-result qcalc-ratio-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}
}
