<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QCalc_Shortcodes {

	public function __construct() {
		add_shortcode( 'age_calculator', array( $this, 'render_age' ) );
		add_shortcode( 'bmi_calculator', array( $this, 'render_bmi' ) );
		add_shortcode( 'percentage_calculator', array( $this, 'render_percentage' ) );
		add_shortcode( 'gpa_calculator', array( $this, 'render_gpa' ) );
	}

	private function enqueue() {
		wp_enqueue_style( 'qcalc-style' );
		wp_enqueue_script( 'qcalc-script' );
	}

	public function render_age() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="age">
			<h2 class="qcalc-title">Age Calculator</h2>
			<p class="qcalc-subtitle">Find your exact age in years, months, and days.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Date of Birth
					<input type="date" class="qcalc-dob" required />
				</label>
				<label>Calculate age as of
					<input type="date" class="qcalc-asof qcalc-autotoday" />
				</label>
				<button type="button" class="qcalc-btn qcalc-age-btn">Calculate Age</button>
			</form>
			<div class="qcalc-result qcalc-age-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_bmi() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="bmi">
			<h2 class="qcalc-title">BMI Calculator</h2>
			<p class="qcalc-subtitle">Check your Body Mass Index and weight category.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Gender
					<select class="qcalc-bmi-gender">
						<option value="male">Male</option>
						<option value="female">Female</option>
					</select>
				</label>
				<label>Units
					<select class="qcalc-bmi-units">
						<option value="metric">Metric (kg / cm)</option>
						<option value="imperial">Imperial (lb / ft-in)</option>
					</select>
				</label>
				<label>Weight
					<span class="qcalc-bmi-weight-unit-label">(kg)</span>
					<input type="number" class="qcalc-bmi-weight" min="1" step="0.1" required />
				</label>
				<div class="qcalc-bmi-height-metric">
					<label>Height (cm)
						<input type="number" class="qcalc-bmi-height-cm" min="1" step="0.1" />
					</label>
				</div>
				<div class="qcalc-bmi-height-imperial" style="display:none;">
					<label>Height (feet)
						<input type="number" class="qcalc-bmi-height-ft" min="0" step="1" />
					</label>
					<label>Height (inches)
						<input type="number" class="qcalc-bmi-height-in" min="0" max="11" step="0.1" />
					</label>
				</div>
				<button type="button" class="qcalc-btn qcalc-bmi-btn">Calculate BMI</button>
			</form>
			<div class="qcalc-result qcalc-bmi-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_percentage() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="percentage">
			<h2 class="qcalc-title">Percentage Calculator</h2>
			<p class="qcalc-subtitle">Find what X% of Y is, instantly.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>What is
					<input type="number" class="qcalc-pct-x" step="any" required />
					% of
					<input type="number" class="qcalc-pct-y" step="any" required />
					?
				</label>
				<button type="button" class="qcalc-btn qcalc-pct-btn">Calculate</button>
			</form>
			<div class="qcalc-result qcalc-pct-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_gpa() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="gpa">
			<h2 class="qcalc-title">GPA Calculator</h2>
			<p class="qcalc-subtitle">Add your courses and grades to calculate your GPA (4.0 scale).</p>
			<div class="qcalc-gpa-rows">
				<div class="qcalc-gpa-row">
					<input type="text" class="qcalc-gpa-course" placeholder="Course name" />
					<select class="qcalc-gpa-grade">
						<option value="4">A</option>
						<option value="3.7">A-</option>
						<option value="3.3">B+</option>
						<option value="3">B</option>
						<option value="2.7">B-</option>
						<option value="2.3">C+</option>
						<option value="2">C</option>
						<option value="1.7">C-</option>
						<option value="1">D</option>
						<option value="0">F</option>
					</select>
					<input type="number" class="qcalc-gpa-credits" placeholder="Credit hours" min="0" step="0.5" value="3" />
				</div>
			</div>
			<button type="button" class="qcalc-btn qcalc-gpa-add">+ Add Course</button>
			<button type="button" class="qcalc-btn qcalc-gpa-btn">Calculate GPA</button>
			<div class="qcalc-result qcalc-gpa-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}
}
