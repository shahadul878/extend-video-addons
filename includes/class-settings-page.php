<?php
/**
 * Settings Page
 *
 * Admin settings page for Videtect – Auto Video Source for Elementor.
 *
 * @package Videtect
 */

namespace Videtect;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Settings_Page
 */
class Settings_Page {

	/**
	 * Option group name.
	 *
	 * @var string
	 */
	const OPTION_GROUP = 'videtect_settings';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Add settings menu page.
	 */
	public function add_menu_page() {
		add_options_page(
			__( 'Videtect – Auto Video Source for Elementor', 'videtect' ),
			__( 'Videtect', 'videtect' ),
			'manage_options',
			'videtect',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Register settings.
	 */
	public function register_settings() {
		register_setting(
			self::OPTION_GROUP,
			get_option_name(),
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_options' ),
			)
		);

		add_settings_section(
			'videtect_main_section',
			__( 'General Settings', 'videtect' ),
			array( $this, 'render_main_section' ),
			'videtect'
		);

		add_settings_field(
			'videtect_default_source',
			__( 'Default Video Source', 'videtect' ),
			array( $this, 'render_default_source_field' ),
			'videtect',
			'videtect_main_section',
			array(
				'label_for' => 'videtect_default_source',
			)
		);
	}

	/**
	 * Sanitize options.
	 *
	 * @param array $input Raw input.
	 * @return array Sanitized options.
	 */
	public function sanitize_options( $input ) {
		$sanitized = array();

		if ( ! is_array( $input ) ) {
			return $sanitized;
		}

		if ( isset( $input['default_source'] ) && in_array( $input['default_source'], get_allowed_sources(), true ) ) {
			$sanitized['default_source'] = $input['default_source'];
		}

		return $sanitized;
	}

	/**
	 * Render main section description.
	 */
	public function render_main_section() {
		echo '<p>' . esc_html__( 'Configure default behavior for the Elementor Video widget Auto Detect option.', 'videtect' ) . '</p>';
	}

	/**
	 * Render default source field.
	 */
	public function render_default_source_field() {
		$value = get_default_video_source();
		$name  = get_option_name() . '[default_source]';
		?>
		<select id="videtect_default_source" name="<?php echo esc_attr( $name ); ?>">
			<option value="auto" <?php selected( $value, 'auto' ); ?>><?php esc_html_e( 'Auto Detect (Recommended)', 'videtect' ); ?></option>
			<option value="youtube" <?php selected( $value, 'youtube' ); ?>><?php esc_html_e( 'YouTube', 'videtect' ); ?></option>
			<option value="vimeo" <?php selected( $value, 'vimeo' ); ?>><?php esc_html_e( 'Vimeo', 'videtect' ); ?></option>
			<option value="dailymotion" <?php selected( $value, 'dailymotion' ); ?>><?php esc_html_e( 'Dailymotion', 'videtect' ); ?></option>
			<option value="videopress" <?php selected( $value, 'videopress' ); ?>><?php esc_html_e( 'VideoPress', 'videtect' ); ?></option>
			<option value="hosted" <?php selected( $value, 'hosted' ); ?>><?php esc_html_e( 'Self Hosted', 'videtect' ); ?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Default source when adding a new Video widget. Auto Detect identifies the platform from the URL.', 'videtect' ); ?></p>
		<?php
	}

	/**
	 * Enqueue admin styles.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_styles( $hook ) {
		if ( 'settings_page_videtect' !== $hook ) {
			return;
		}

		wp_add_inline_style(
			'wp-admin',
			'
			.videtect-card { background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; padding: 20px; margin-top: 20px; box-shadow: 0 1px 1px rgba(0,0,0,.04); }
			.videtect-card h2 { margin-top: 0; }
			.videtect-card .videtect-author-links { margin-top: 15px; }
			.videtect-card .videtect-author-links a { margin-right: 15px; }
			.videtect-status-list { list-style: none; padding: 0; margin: 15px 0 0 0; }
			.videtect-status-list li { padding: 8px 0; border-bottom: 1px solid #f0f0f1; }
			.videtect-status-list li:last-child { border-bottom: none; }
			.videtect-status-ok { color: #00a32a; }
			.videtect-status-missing { color: #d63638; }
			'
		);
	}

	/**
	 * Render settings page.
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<form action="options.php" method="post">
				<?php
				settings_fields( self::OPTION_GROUP );
				do_settings_sections( 'videtect' );
				submit_button( __( 'Save Settings', 'videtect' ) );
				?>
			</form>

			<?php $this->render_status_section(); ?>
			<?php $this->render_about_section(); ?>
		</div>
		<?php
	}

	/**
	 * Render compatibility status section.
	 */
	private function render_status_section() {
		$elementor_active = did_action( 'elementor/loaded' );
		$wp_version       = get_bloginfo( 'version' );
		$php_version      = PHP_VERSION;
		?>
		<div class="videtect-card">
			<h2><?php esc_html_e( 'Compatibility Status', 'videtect' ); ?></h2>
			<ul class="videtect-status-list">
				<li>
					<?php if ( $elementor_active ) : ?>
						<span class="videtect-status-ok">&#10003;</span> <?php esc_html_e( 'Elementor: Active', 'videtect' ); ?>
					<?php else : ?>
						<span class="videtect-status-missing">&#10007;</span> <?php esc_html_e( 'Elementor: Not active (required)', 'videtect' ); ?>
					<?php endif; ?>
				</li>
				<li>
					<?php
					printf(
						/* translators: %s: WordPress version number */
						esc_html__( 'WordPress: %s', 'videtect' ),
						esc_html( $wp_version )
					);
					?>
				</li>
				<li>
					<?php
					printf(
						/* translators: %s: PHP version number */
						esc_html__( 'PHP: %s', 'videtect' ),
						esc_html( $php_version )
					);
					?>
				</li>
				<li>
					<?php
					printf(
						/* translators: %s: plugin version number */
						esc_html__( 'Plugin Version: %s', 'videtect' ),
						esc_html( VIDETECT_VERSION )
					);
					?>
				</li>
			</ul>
		</div>
		<?php
	}

	/**
	 * Render About the Author section.
	 */
	private function render_about_section() {
		?>
		<div class="videtect-card">
			<h2><?php esc_html_e( 'About the Author', 'videtect' ); ?></h2>
			<p><strong><?php echo esc_html( 'H M Shahadul Islam' ); ?></strong></p>
			<p><?php esc_html_e( 'WordPress and Elementor plugin developer. This plugin enhances the Elementor Video widget with automatic source detection.', 'videtect' ); ?></p>
			<div class="videtect-author-links">
				<a href="https://github.com/shahadul878" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'GitHub', 'videtect' ); ?></a>
				<a href="https://github.com/shahadul878/extend-video-addons" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Plugin Repository', 'videtect' ); ?></a>
			</div>
		</div>
		<?php
	}
}
