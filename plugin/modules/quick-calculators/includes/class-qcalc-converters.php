<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QCalc_Converters {

	public function __construct() {
		add_shortcode( 'length_converter', array( $this, 'render_length' ) );
		add_shortcode( 'weight_converter', array( $this, 'render_weight' ) );
		add_shortcode( 'temperature_converter', array( $this, 'render_temperature' ) );
		add_shortcode( 'area_calculator', array( $this, 'render_area' ) );
		add_shortcode( 'number_base_converter', array( $this, 'render_number_base' ) );
		add_shortcode( 'roman_numeral_converter', array( $this, 'render_roman' ) );
		add_shortcode( 'number_to_words_converter', array( $this, 'render_number_words' ) );
	}

	private function enqueue() {
		wp_enqueue_style( 'qcalc-style' );
		wp_enqueue_script( 'qcalc-script' );
	}

	public function render_length() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="length">
			<h2 class="qcalc-title">Length Converter</h2>
			<p class="qcalc-subtitle">Convert between meters, feet, miles, and more.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Value
					<input type="number" class="qcalc-len-value" step="any" required />
				</label>
				<label>From
					<select class="qcalc-len-from">
						<option value="mm">Millimeters</option>
						<option value="cm">Centimeters</option>
						<option value="m" selected>Meters</option>
						<option value="km">Kilometers</option>
						<option value="in">Inches</option>
						<option value="ft">Feet</option>
						<option value="yd">Yards</option>
						<option value="mi">Miles</option>
					</select>
				</label>
				<label>To
					<select class="qcalc-len-to">
						<option value="mm">Millimeters</option>
						<option value="cm">Centimeters</option>
						<option value="m">Meters</option>
						<option value="km">Kilometers</option>
						<option value="in">Inches</option>
						<option value="ft" selected>Feet</option>
						<option value="yd">Yards</option>
						<option value="mi">Miles</option>
					</select>
				</label>
				<button type="button" class="qcalc-btn qcalc-len-btn">Convert</button>
			</form>
			<div class="qcalc-result qcalc-len-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_weight() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="weight">
			<h2 class="qcalc-title">Weight Converter</h2>
			<p class="qcalc-subtitle">Convert between kilograms, pounds, ounces, and more.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Value
					<input type="number" class="qcalc-wt-value" step="any" required />
				</label>
				<label>From
					<select class="qcalc-wt-from">
						<option value="mg">Milligrams</option>
						<option value="g">Grams</option>
						<option value="kg" selected>Kilograms</option>
						<option value="oz">Ounces</option>
						<option value="lb">Pounds</option>
						<option value="ton">Metric Tons</option>
					</select>
				</label>
				<label>To
					<select class="qcalc-wt-to">
						<option value="mg">Milligrams</option>
						<option value="g">Grams</option>
						<option value="kg">Kilograms</option>
						<option value="oz">Ounces</option>
						<option value="lb" selected>Pounds</option>
						<option value="ton">Metric Tons</option>
					</select>
				</label>
				<button type="button" class="qcalc-btn qcalc-wt-btn">Convert</button>
			</form>
			<div class="qcalc-result qcalc-wt-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_temperature() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="temperature">
			<h2 class="qcalc-title">Temperature Converter</h2>
			<p class="qcalc-subtitle">Convert between Celsius, Fahrenheit, and Kelvin.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Value
					<input type="number" class="qcalc-temp-value" step="any" required />
				</label>
				<label>From
					<select class="qcalc-temp-from">
						<option value="c" selected>Celsius</option>
						<option value="f">Fahrenheit</option>
						<option value="k">Kelvin</option>
					</select>
				</label>
				<label>To
					<select class="qcalc-temp-to">
						<option value="c">Celsius</option>
						<option value="f" selected>Fahrenheit</option>
						<option value="k">Kelvin</option>
					</select>
				</label>
				<button type="button" class="qcalc-btn qcalc-temp-btn">Convert</button>
			</form>
			<div class="qcalc-result qcalc-temp-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_area() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="area">
			<h2 class="qcalc-title">Area Calculator</h2>
			<p class="qcalc-subtitle">Calculate the area of common shapes.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Shape
					<select class="qcalc-area-shape">
						<option value="rectangle">Rectangle</option>
						<option value="square">Square</option>
						<option value="circle">Circle</option>
						<option value="triangle">Triangle</option>
					</select>
				</label>
				<div class="qcalc-area-fields" data-shape="rectangle">
					<label>Length <input type="number" class="qcalc-area-a" step="any" /></label>
					<label>Width <input type="number" class="qcalc-area-b" step="any" /></label>
				</div>
				<button type="button" class="qcalc-btn qcalc-area-btn">Calculate</button>
			</form>
			<div class="qcalc-result qcalc-area-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_number_base() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="number-base">
			<h2 class="qcalc-title">Binary / Decimal / Hex Converter</h2>
			<p class="qcalc-subtitle">Convert numbers between binary, decimal, octal, and hexadecimal.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Value
					<input type="text" class="qcalc-base-value" required />
				</label>
				<label>From Base
					<select class="qcalc-base-from">
						<option value="2">Binary</option>
						<option value="8">Octal</option>
						<option value="10" selected>Decimal</option>
						<option value="16">Hexadecimal</option>
					</select>
				</label>
				<button type="button" class="qcalc-btn qcalc-base-btn">Convert to All</button>
			</form>
			<div class="qcalc-result qcalc-base-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_roman() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="roman">
			<h2 class="qcalc-title">Roman Numeral Converter</h2>
			<p class="qcalc-subtitle">Convert between numbers and Roman numerals (1–3999).</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Number (1-3999)
					<input type="number" class="qcalc-roman-num" min="1" max="3999" step="1" />
				</label>
				<label>Or enter Roman Numeral
					<input type="text" class="qcalc-roman-text" placeholder="e.g. MCMXCIV" />
				</label>
				<button type="button" class="qcalc-btn qcalc-roman-btn">Convert</button>
			</form>
			<div class="qcalc-result qcalc-roman-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_number_words() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="number-words">
			<h2 class="qcalc-title">Number to Words Converter</h2>
			<p class="qcalc-subtitle">Convert any number into written English words.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Number
					<input type="number" class="qcalc-nw-value" step="1" required />
				</label>
				<button type="button" class="qcalc-btn qcalc-nw-btn">Convert</button>
			</form>
			<div class="qcalc-result qcalc-nw-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}
}
