<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QCalc_Finance {

	public function __construct() {
		add_shortcode( 'simple_interest_calculator', array( $this, 'render_simple_interest' ) );
		add_shortcode( 'compound_interest_calculator', array( $this, 'render_compound_interest' ) );
		add_shortcode( 'loan_calculator', array( $this, 'render_loan' ) );
		add_shortcode( 'discount_calculator', array( $this, 'render_discount' ) );
		add_shortcode( 'tip_calculator', array( $this, 'render_tip' ) );
	}

	private function enqueue() {
		wp_enqueue_style( 'qcalc-style' );
		wp_enqueue_script( 'qcalc-script' );
	}

	public function render_simple_interest() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="simple-interest">
			<h2 class="qcalc-title">Simple Interest Calculator</h2>
			<p class="qcalc-subtitle">Calculate interest earned or owed using the simple interest formula.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Principal Amount
					<input type="number" class="qcalc-si-principal" min="0" step="any" required />
				</label>
				<label>Annual Interest Rate (%)
					<input type="number" class="qcalc-si-rate" min="0" step="any" required />
				</label>
				<label>Time (years)
					<input type="number" class="qcalc-si-time" min="0" step="any" required />
				</label>
				<button type="button" class="qcalc-btn qcalc-si-btn">Calculate</button>
			</form>
			<div class="qcalc-result qcalc-si-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_compound_interest() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="compound-interest">
			<h2 class="qcalc-title">Compound Interest Calculator</h2>
			<p class="qcalc-subtitle">See how your investment grows with compounding.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Principal Amount
					<input type="number" class="qcalc-ci-principal" min="0" step="any" required />
				</label>
				<label>Annual Interest Rate (%)
					<input type="number" class="qcalc-ci-rate" min="0" step="any" required />
				</label>
				<label>Time (years)
					<input type="number" class="qcalc-ci-time" min="0" step="any" required />
				</label>
				<label>Compounds per year
					<select class="qcalc-ci-freq">
						<option value="1">Annually</option>
						<option value="2">Semi-Annually</option>
						<option value="4">Quarterly</option>
						<option value="12" selected>Monthly</option>
						<option value="365">Daily</option>
					</select>
				</label>
				<button type="button" class="qcalc-btn qcalc-ci-btn">Calculate</button>
			</form>
			<div class="qcalc-result qcalc-ci-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_loan() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="loan">
			<h2 class="qcalc-title">Loan / EMI Calculator</h2>
			<p class="qcalc-subtitle">Find your monthly installment (EMI) for any loan.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Loan Amount
					<input type="number" class="qcalc-loan-amount" min="0" step="any" required />
				</label>
				<label>Annual Interest Rate (%)
					<input type="number" class="qcalc-loan-rate" min="0" step="any" required />
				</label>
				<label>Loan Term (months)
					<input type="number" class="qcalc-loan-months" min="1" step="1" required />
				</label>
				<button type="button" class="qcalc-btn qcalc-loan-btn">Calculate EMI</button>
			</form>
			<div class="qcalc-result qcalc-loan-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_discount() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="discount">
			<h2 class="qcalc-title">Discount Calculator</h2>
			<p class="qcalc-subtitle">Find the final price after a percentage discount.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Original Price
					<input type="number" class="qcalc-disc-price" min="0" step="any" required />
				</label>
				<label>Discount (%)
					<input type="number" class="qcalc-disc-percent" min="0" max="100" step="any" required />
				</label>
				<button type="button" class="qcalc-btn qcalc-disc-btn">Calculate</button>
			</form>
			<div class="qcalc-result qcalc-disc-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_tip() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="tip">
			<h2 class="qcalc-title">Tip Calculator</h2>
			<p class="qcalc-subtitle">Split the bill and calculate the tip amount.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Bill Amount
					<input type="number" class="qcalc-tip-bill" min="0" step="any" required />
				</label>
				<label>Tip (%)
					<input type="number" class="qcalc-tip-percent" min="0" step="any" value="10" required />
				</label>
				<label>Number of People
					<input type="number" class="qcalc-tip-people" min="1" step="1" value="1" required />
				</label>
				<button type="button" class="qcalc-btn qcalc-tip-btn">Calculate</button>
			</form>
			<div class="qcalc-result qcalc-tip-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}
}
