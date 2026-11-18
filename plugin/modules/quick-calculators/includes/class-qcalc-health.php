<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QCalc_Health {

	public function __construct() {
		add_shortcode( 'bmr_calculator', array( $this, 'render_bmr' ) );
		add_shortcode( 'ideal_weight_calculator', array( $this, 'render_ideal_weight' ) );
		add_shortcode( 'pregnancy_due_date_calculator', array( $this, 'render_pregnancy' ) );
	}

	private function enqueue() {
		wp_enqueue_style( 'qcalc-style' );
		wp_enqueue_script( 'qcalc-script' );
	}

	public function render_bmr() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="bmr">
			<h2 class="qcalc-title">BMR Calculator</h2>
			<p class="qcalc-subtitle">Estimate your Basal Metabolic Rate — calories your body burns at rest.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Gender
					<select class="qcalc-bmr-gender">
						<option value="male">Male</option>
						<option value="female">Female</option>
					</select>
				</label>
				<label>Age (years)
					<input type="number" class="qcalc-bmr-age" min="1" step="1" required />
				</label>
				<label>Weight (kg)
					<input type="number" class="qcalc-bmr-weight" min="1" step="0.1" required />
				</label>
				<label>Height (cm)
					<input type="number" class="qcalc-bmr-height" min="1" step="0.1" required />
				</label>
				<label>Activity Level
					<select class="qcalc-bmr-activity">
						<option value="1.2">Sedentary (little/no exercise)</option>
						<option value="1.375">Light (1-3 days/week)</option>
						<option value="1.55" selected>Moderate (3-5 days/week)</option>
						<option value="1.725">Active (6-7 days/week)</option>
						<option value="1.9">Very Active (athlete)</option>
					</select>
				</label>
				<button type="button" class="qcalc-btn qcalc-bmr-btn">Calculate</button>
			</form>
			<div class="qcalc-result qcalc-bmr-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_ideal_weight() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="ideal-weight">
			<h2 class="qcalc-title">Ideal Weight Calculator</h2>
			<p class="qcalc-subtitle">Get an estimated healthy weight range based on your height.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Gender
					<select class="qcalc-iw-gender">
						<option value="male">Male</option>
						<option value="female">Female</option>
					</select>
				</label>
				<label>Height (cm)
					<input type="number" class="qcalc-iw-height" min="1" step="0.1" required />
				</label>
				<button type="button" class="qcalc-btn qcalc-iw-btn">Calculate</button>
			</form>
			<div class="qcalc-result qcalc-iw-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_pregnancy() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="pregnancy">
			<h2 class="qcalc-title">Pregnancy Due Date Calculator</h2>
			<p class="qcalc-subtitle">Estimate your due date based on your last menstrual period (LMP).</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>First Day of Last Menstrual Period
					<input type="date" class="qcalc-preg-lmp" required />
				</label>
				<button type="button" class="qcalc-btn qcalc-preg-btn">Calculate Due Date</button>
			</form>
			<div class="qcalc-result qcalc-preg-result" style="display:none;"></div>
			<p style="font-size:12px;color:#8a8a9a;margin-top:10px;">This is a general estimate (Naegele's rule), not medical advice. Please consult your doctor.</p>
		</div>
		<?php
		return ob_get_clean();
	}
}
