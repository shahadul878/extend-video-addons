<?php
/**
 * Plugin Name: Extend Video Add-ons for Elementor
 * Plugin URI: https://github.com/shahadul878/extend-video-addons
 * Description: Adds Auto Detect to the Elementor Video widget so YouTube, Vimeo, Dailymotion, VideoPress, and self-hosted URLs are identified automatically.
 * Version: 1.0.2
 * Author: H M Shahadul Islam
 * Author URI: https://github.com/shahadul878
 * Text Domain: extend-video-add-ons-for-elementor
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Requires Plugins: elementor
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace ExtendVideoAddons;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'EXTEND_VIDEO_ADDONS_VERSION', '1.0.2' );
define( 'EXTEND_VIDEO_ADDONS_FILE', __FILE__ );
define( 'EXTEND_VIDEO_ADDONS_DIR', plugin_dir_path( __FILE__ ) );
define( 'EXTEND_VIDEO_ADDONS_URL', plugin_dir_url( __FILE__ ) );
define( 'EXTEND_VIDEO_ADDONS_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Option name for plugin settings.
 *
 * @return string
 */
function get_option_name() {
	return 'eva_options';
}

/**
 * Allowed video source values.
 *
 * @return string[]
 */
function get_allowed_sources() {
	return array( 'auto', 'youtube', 'vimeo', 'dailymotion', 'videopress', 'hosted' );
}

/**
 * Default video source from settings.
 *
 * @return string
 */
function get_default_video_source() {
	$options = get_option( get_option_name(), array() );
	$source  = isset( $options['default_source'] ) ? $options['default_source'] : 'auto';

	return in_array( $source, get_allowed_sources(), true ) ? $source : 'auto';
}

/**
 * Check if Elementor is installed and activated.
 *
 * @return bool
 */
function is_elementor_active() {
	return did_action( 'elementor/loaded' );
}

/**
 * Display admin notice if Elementor is not active.
 */
function admin_notice_missing_elementor() {
	if ( is_elementor_active() ) {
		return;
	}

	$message = sprintf(
		/* translators: 1: Plugin name, 2: Elementor */
		esc_html__( '%1$s requires %2$s to be installed and activated.', 'extend-video-add-ons-for-elementor' ),
		'<strong>' . esc_html__( 'Extend Video Add-ons for Elementor', 'extend-video-add-ons-for-elementor' ) . '</strong>',
		'<strong>' . esc_html__( 'Elementor', 'extend-video-add-ons-for-elementor' ) . '</strong>'
	);

	printf( '<div class="notice notice-error"><p>%s</p></div>', wp_kses_post( $message ) );
}

/**
 * Load plugin files.
 */
function load_plugin() {
	if ( ! is_elementor_active() ) {
		add_action( 'admin_notices', __NAMESPACE__ . '\\admin_notice_missing_elementor' );
		return;
	}

	require_once EXTEND_VIDEO_ADDONS_DIR . 'includes/class-video-source-detector.php';
	require_once EXTEND_VIDEO_ADDONS_DIR . 'includes/class-video-widget-enhancer.php';

	Video_Widget_Enhancer::instance();

	add_action( 'elementor/editor/before_enqueue_scripts', __NAMESPACE__ . '\\enqueue_editor_scripts' );
}

/**
 * Enqueue editor scripts.
 */
function enqueue_editor_scripts() {
	wp_enqueue_script(
		'extend-video-addons-admin',
		EXTEND_VIDEO_ADDONS_URL . 'assets/js/admin.js',
		array( 'jquery', 'elementor-editor' ),
		EXTEND_VIDEO_ADDONS_VERSION,
		true
	);

	wp_localize_script(
		'extend-video-addons-admin',
		'extendVideoAddons',
		array(
			'i18n' => array(
				/* translators: %s: detected video platform name (youtube, vimeo, etc.) */
				'detected'     => __( 'Video source detected: %s', 'extend-video-add-ons-for-elementor' ),
				'undetectable' => __( 'Unable to detect video source. Please select manually.', 'extend-video-add-ons-for-elementor' ),
			),
		)
	);
}

/**
 * Load admin settings page.
 */
function load_settings_page() {
	if ( ! is_admin() ) {
		return;
	}

	require_once EXTEND_VIDEO_ADDONS_DIR . 'includes/class-settings-page.php';
	new Settings_Page();
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_settings_page' );
