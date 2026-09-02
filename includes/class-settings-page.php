<?php
/**
 * Settings Page
 *
 * Admin settings page for Extend Video Add-ons for Elementor.
 *
 * @package ExtendVideoAddons
 */

namespace ExtendVideoAddons;

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
	const OPTION_GROUP = 'extend_video_addons_settings';

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
			__( 'Extend Video Add-ons for Elementor', 'extend-video-add-ons-for-elementor' ),
			__( 'Extend Video Add-ons', 'extend-video-add-ons-for-elementor' ),
			'manage_options',
			'extend-video-addons',
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
			'extend_video_addons_main_section',
			__( 'General Settings', 'extend-video-add-ons-for-elementor' ),
			array( $this, 'render_main_section' ),
			'extend-video-addons'
		);

		add_settings_field(
			'extend_video_addons_default_source',
			__( 'Default Video Source', 'extend-video-add-ons-for-elementor' ),
			array( $this, 'render_default_source_field' ),
			'extend-video-addons',
			'extend_video_addons_main_section',
			array(
				'label_for' => 'extend_video_addons_default_source',
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
		echo '<p>' . esc_html__( 'Configure default behavior for the Elementor Video widget Auto Detect option.', 'extend-video-add-ons-for-elementor' ) . '</p>';
	}

	/**
	 * Render default source field.
	 */
	public function render_default_source_field() {
		$value = get_default_video_source();
		$name  = get_option_name() . '[default_source]';
		?>
		<select id="extend_video_addons_default_source" name="<?php echo esc_attr( $name ); ?>">
			<option value="auto" <?php selected( $value, 'auto' ); ?>><?php esc_html_e( 'Auto Detect (Recommended)', 'extend-video-add-ons-for-elementor' ); ?></option>
			<option value="youtube" <?php selected( $value, 'youtube' ); ?>><?php esc_html_e( 'YouTube', 'extend-video-add-ons-for-elementor' ); ?></option>
			<option value="vimeo" <?php selected( $value, 'vimeo' ); ?>><?php esc_html_e( 'Vimeo', 'extend-video-add-ons-for-elementor' ); ?></option>
			<option value="dailymotion" <?php selected( $value, 'dailymotion' ); ?>><?php esc_html_e( 'Dailymotion', 'extend-video-add-ons-for-elementor' ); ?></option>
			<option value="videopress" <?php selected( $value, 'videopress' ); ?>><?php esc_html_e( 'VideoPress', 'extend-video-add-ons-for-elementor' ); ?></option>
			<option value="hosted" <?php selected( $value, 'hosted' ); ?>><?php esc_html_e( 'Self Hosted', 'extend-video-add-ons-for-elementor' ); ?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Default source when adding a new Video widget. Auto Detect identifies the platform from the URL.', 'extend-video-add-ons-for-elementor' ); ?></p>
		<?php
	}

	/**
	 * Enqueue admin styles.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_styles( $hook ) {
		if ( 'settings_page_extend-video-addons' !== $hook ) {
			return;
		}

		wp_add_inline_style(
			'wp-admin',
			'
			.extend-video-addons-card { background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; padding: 20px; margin-top: 20px; box-shadow: 0 1px 1px rgba(0,0,0,.04); }
			.extend-video-addons-card h2 { margin-top: 0; }
			.extend-video-addons-card .extend-video-addons-author-links { margin-top: 15px; }
			.extend-video-addons-card .extend-video-addons-author-links a { margin-right: 15px; }
			.extend-video-addons-status-list { list-style: none; padding: 0; margin: 15px 0 0 0; }
			.extend-video-addons-status-list li { padding: 8px 0; border-bottom: 1px solid #f0f0f1; }
			.extend-video-addons-status-list li:last-child { border-bottom: none; }
			.extend-video-addons-status-ok { color: #00a32a; }
			.extend-video-addons-status-missing { color: #d63638; }
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
				do_settings_sections( 'extend-video-addons' );
				submit_button( __( 'Save Settings', 'extend-video-add-ons-for-elementor' ) );
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
		<div class="extend-video-addons-card">
			<h2><?php esc_html_e( 'Compatibility Status', 'extend-video-add-ons-for-elementor' ); ?></h2>
			<ul class="extend-video-addons-status-list">
				<li>
					<?php if ( $elementor_active ) : ?>
						<span class="extend-video-addons-status-ok">&#10003;</span> <?php esc_html_e( 'Elementor: Active', 'extend-video-add-ons-for-elementor' ); ?>
					<?php else : ?>
						<span class="extend-video-addons-status-missing">&#10007;</span> <?php esc_html_e( 'Elementor: Not active (required)', 'extend-video-add-ons-for-elementor' ); ?>
					<?php endif; ?>
				</li>
				<li>
					<?php
					printf(
						/* translators: %s: WordPress version number */
						esc_html__( 'WordPress: %s', 'extend-video-add-ons-for-elementor' ),
						esc_html( $wp_version )
					);
					?>
				</li>
				<li>
					<?php
					printf(
						/* translators: %s: PHP version number */
						esc_html__( 'PHP: %s', 'extend-video-add-ons-for-elementor' ),
						esc_html( $php_version )
					);
					?>
				</li>
				<li>
					<?php
					printf(
						/* translators: %s: plugin version number */
						esc_html__( 'Plugin Version: %s', 'extend-video-add-ons-for-elementor' ),
						esc_html( EXTEND_VIDEO_ADDONS_VERSION )
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
		<div class="extend-video-addons-card">
			<h2><?php esc_html_e( 'About the Author', 'extend-video-add-ons-for-elementor' ); ?></h2>
			<p><strong><?php echo esc_html( 'H M Shahadul Islam' ); ?></strong></p>
			<p><?php esc_html_e( 'WordPress and Elementor plugin developer. This plugin enhances the Elementor Video widget with automatic source detection.', 'extend-video-add-ons-for-elementor' ); ?></p>
			<div class="extend-video-addons-author-links">
				<a href="https://github.com/shahadul878" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'GitHub', 'extend-video-add-ons-for-elementor' ); ?></a>
				<a href="https://github.com/shahadul878/extend-video-addons" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Plugin Repository', 'extend-video-add-ons-for-elementor' ); ?></a>
			</div>
		</div>
		<?php
	}
}
