<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QCalc_Text {

	public function __construct() {
		add_shortcode( 'word_counter', array( $this, 'render_word_counter' ) );
		add_shortcode( 'character_counter', array( $this, 'render_character_counter' ) );
		add_shortcode( 'text_case_converter', array( $this, 'render_case_converter' ) );
		add_shortcode( 'password_generator', array( $this, 'render_password_generator' ) );
	}

	private function enqueue() {
		wp_enqueue_style( 'qcalc-style' );
		wp_enqueue_script( 'qcalc-script' );
	}

	public function render_word_counter() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="word-counter">
			<h2 class="qcalc-title">Word Counter</h2>
			<p class="qcalc-subtitle">Count words, characters, sentences, and paragraphs live.</p>
			<textarea class="qcalc-wc-input" rows="8" style="width:100%;padding:10px;font-size:15px;border:2px solid #d8d8e0;border-radius:6px;" placeholder="Paste or type your text here..."></textarea>
			<div class="qcalc-result qcalc-wc-result" style="display:block;margin-top:14px;">Words: 0 | Characters: 0 | Sentences: 0 | Paragraphs: 0</div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_character_counter() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="char-counter">
			<h2 class="qcalc-title">Character Counter</h2>
			<p class="qcalc-subtitle">Count characters with and without spaces — useful for bios, tweets, meta descriptions.</p>
			<textarea class="qcalc-cc-input" rows="6" style="width:100%;padding:10px;font-size:15px;border:2px solid #d8d8e0;border-radius:6px;" placeholder="Type here..."></textarea>
			<div class="qcalc-result qcalc-cc-result" style="display:block;margin-top:14px;">With spaces: 0 | Without spaces: 0</div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_case_converter() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="case-converter">
			<h2 class="qcalc-title">Text Case Converter</h2>
			<p class="qcalc-subtitle">Convert text to UPPERCASE, lowercase, Title Case, or Sentence case.</p>
			<textarea class="qcalc-case-input" rows="5" style="width:100%;padding:10px;font-size:15px;border:2px solid #d8d8e0;border-radius:6px;margin-bottom:12px;" placeholder="Type here..."></textarea>
			<div style="display:flex;gap:8px;flex-wrap:wrap;">
				<button type="button" class="qcalc-btn qcalc-case-btn" data-case="upper">UPPERCASE</button>
				<button type="button" class="qcalc-btn qcalc-case-btn" data-case="lower">lowercase</button>
				<button type="button" class="qcalc-btn qcalc-case-btn" data-case="title">Title Case</button>
				<button type="button" class="qcalc-btn qcalc-case-btn" data-case="sentence">Sentence case</button>
			</div>
			<div class="qcalc-result qcalc-case-result" style="display:none;"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_password_generator() {
		$this->enqueue();
		ob_start();
		?>
		<div class="qcalc-wrap" data-qcalc="password">
			<h2 class="qcalc-title">Password Generator</h2>
			<p class="qcalc-subtitle">Generate a strong random password. Generated entirely in your browser — nothing is sent anywhere.</p>
			<form class="qcalc-form" onsubmit="return false;">
				<label>Length
					<input type="number" class="qcalc-pw-length" min="4" max="64" value="16" />
				</label>
				<label><input type="checkbox" class="qcalc-pw-upper" checked style="width:auto;display:inline-block;" /> Include Uppercase (A-Z)</label>
				<label><input type="checkbox" class="qcalc-pw-numbers" checked style="width:auto;display:inline-block;" /> Include Numbers (0-9)</label>
				<label><input type="checkbox" class="qcalc-pw-symbols" checked style="width:auto;display:inline-block;" /> Include Symbols (!@#$)</label>
				<button type="button" class="qcalc-btn qcalc-pw-btn">Generate Password</button>
			</form>
			<div class="qcalc-result qcalc-pw-result" style="display:none;font-family:monospace;font-size:18px;"></div>
		</div>
		<?php
		return ob_get_clean();
	}
}
