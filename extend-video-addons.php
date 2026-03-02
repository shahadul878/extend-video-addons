<?php
/**
 * Plugin Name: Extend Video Addons
 * Plugin URI: https://github.com/shahadul878
 * Description: Enhances Elementor video widget with automatic video source detection from URL patterns. Works with dynamic content and supports YouTube, Vimeo, Dailymotion, VideoPress, and self-hosted videos.
 * Version: 1.0.0
 * Author: H M Shahadul Islam
 * Author URI: https://github.com/shahadul878
 * Text Domain: extend-video-addons
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace ExtendVideoAddons;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define plugin constants
define( 'EVA_VERSION', '1.0.0' );
define( 'EVA_PLUGIN_FILE', __FILE__ );
define( 'EVA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EVA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'EVA_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Check if Elementor is installed and activated
 *
 * @return bool
 */
function is_elementor_active() {
	return did_action( 'elementor/loaded' );
}

/**
 * Display admin notice if Elementor is not active
 */
function admin_notice_missing_elementor() {
	if ( is_elementor_active() ) {
		return;
	}

	$message = sprintf(
		/* translators: 1: Plugin name 2: Elementor */
		esc_html__( '%1$s requires %2$s to be installed and activated.', 'extend-video-addons' ),
		'<strong>' . esc_html__( 'Extend Video Addons', 'extend-video-addons' ) . '</strong>',
		'<strong>' . esc_html__( 'Elementor', 'extend-video-addons' ) . '</strong>'
	);

	printf( '<div class="notice notice-error"><p>%s</p></div>', wp_kses_post( $message ) );
}

/**
 * Load plugin files
 */
function load_plugin() {
	// Check if Elementor is active
	if ( ! is_elementor_active() ) {
		add_action( 'admin_notices', __NAMESPACE__ . '\\admin_notice_missing_elementor' );
		return;
	}

	// Include Video_Source_Detector (no Elementor dependency)
	require_once EVA_PLUGIN_DIR . 'includes/class-video-source-detector.php';

	// Register widget - Extended_Video_Widget is loaded inside the callback so that
	// Elementor\Widget_Video exists (Elementor loads its widgets in init_widgets before this hook fires)
	add_action( 'elementor/widgets/register', __NAMESPACE__ . '\\register_widget', 99 );

	// Enqueue admin scripts
	add_action( 'elementor/editor/before_enqueue_scripts', __NAMESPACE__ . '\\enqueue_editor_scripts' );
}

/**
 * Register extended video widget
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 */
function register_widget( $widgets_manager ) {
	// Load extended widget class here - Elementor\Widget_Video is guaranteed to exist
	// at this point since we run on elementor/widgets/register (after Elementor loads its widgets)
	require_once EVA_PLUGIN_DIR . 'includes/class-extended-video-widget.php';

	// Unregister original video widget
	$widgets_manager->unregister( 'video' );

	// Register extended video widget
	$widgets_manager->register( new Extended_Video_Widget() );
}

/**
 * Enqueue editor scripts
 */
function enqueue_editor_scripts() {
	wp_enqueue_script(
		'eva-admin',
		EVA_PLUGIN_URL . 'assets/js/admin.js',
		[ 'jquery', 'elementor-editor' ],
		EVA_VERSION,
		true
	);

	wp_localize_script(
		'eva-admin',
		'evaAdmin',
		[
			'nonce' => wp_create_nonce( 'eva-admin' ),
		]
	);
}

/**
 * Load admin settings page.
 */
function load_settings_page() {
	if ( is_admin() ) {
		require_once EVA_PLUGIN_DIR . 'includes/class-settings-page.php';
		new Settings_Page();
	}
}

// Initialize plugin
add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_settings_page' );
