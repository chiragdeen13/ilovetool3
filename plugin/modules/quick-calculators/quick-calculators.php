<?php
/**
 * Plugin Name: Quick Calculators
 * Description: A pack of fast, no-API-needed calculators (Age, BMI, Percentage, GPA) via shortcodes. Pure client-side JS — no external services, no ongoing cost.
 * Version: 1.6.0
 * Author: Your Site
 * Text Domain: quick-calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'QCALC_PATH', plugin_dir_path( __FILE__ ) );
define( 'QCALC_URL', plugin_dir_url( __FILE__ ) );
/* Bumped 1.6.0 -> 1.6.1: homepage.css and homepage.js both changed in the
   SEO pass. This constant is what wp_register_style/script use as the ?ver=
   query string, so without the bump every returning visitor (and every CDN)
   would keep serving the old cached CSS/JS and the new markup would look
   broken to them. */
define( 'QCALC_VERSION', '1.6.2' );

require_once QCALC_PATH . 'includes/class-qcalc-catalog.php';
require_once QCALC_PATH . 'includes/class-qcalc-view-counter.php';
require_once QCALC_PATH . 'includes/class-qcalc-shortcodes.php';
require_once QCALC_PATH . 'includes/class-qcalc-admin.php';
require_once QCALC_PATH . 'includes/class-qcalc-finance.php';
require_once QCALC_PATH . 'includes/class-qcalc-converters.php';
require_once QCALC_PATH . 'includes/class-qcalc-text.php';
require_once QCALC_PATH . 'includes/class-qcalc-health.php';
require_once QCALC_PATH . 'includes/class-qcalc-math.php';
require_once QCALC_PATH . 'includes/class-qcalc-datetime.php';
require_once QCALC_PATH . 'includes/class-qcalc-homepage.php';
require_once QCALC_PATH . 'includes/class-qcalc-related-tools.php';
require_once QCALC_PATH . 'includes/class-qcalc-duplicates-admin.php';
require_once QCALC_PATH . 'includes/class-qcalc-site-chrome.php';
require_once QCALC_PATH . 'includes/class-qcalc-page-enhancer.php';

function qcalc_init() {
	new QCalc_ViewCounter();
	new QCalc_Shortcodes();
	new QCalc_Admin();
	new QCalc_Finance();
	new QCalc_Converters();
	new QCalc_Text();
	new QCalc_Health();
	new QCalc_Math();
	new QCalc_DateTime();
	new QCalc_Homepage();
	new QCalc_RelatedTools();
	new QCalc_DuplicatesAdmin();
	new QCalc_SiteChrome();
	new QCalc_PageEnhancer();
}
add_action( 'plugins_loaded', 'qcalc_init' );

function qcalc_enqueue_assets() {
	wp_register_style( 'qcalc-style', QCALC_URL . 'assets/css/quick-calculators.css', array(), QCALC_VERSION );
	wp_register_script( 'qcalc-script', QCALC_URL . 'assets/js/quick-calculators.js', array(), QCALC_VERSION, true );

	wp_register_style( 'qcalc-homepage-style', QCALC_URL . 'assets/css/homepage.css', array(), QCALC_VERSION );
	wp_register_script( 'qcalc-homepage-script', QCALC_URL . 'assets/js/homepage.js', array(), QCALC_VERSION, true );
	wp_enqueue_style( 'qcalc-homepage-style' );
	wp_enqueue_script( 'qcalc-homepage-script' );
}
add_action( 'wp_enqueue_scripts', 'qcalc_enqueue_assets' );
