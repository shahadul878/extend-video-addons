<?php
/**
 * Settings Page
 *
 * Admin settings page for Extend Video Addons.
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
	const OPTION_GROUP = 'eva_settings';

	/**
	 * Option name.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'eva_options';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_menu_page' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
	}

	/**
	 * Add settings menu page.
	 */
	public function add_menu_page() {
		add_options_page(
			__( 'Extend Video Addons', 'extend-video-addons' ),
			__( 'Extend Video Addons', 'extend-video-addons' ),
			'manage_options',
			'extend-video-addons',
			[ $this, 'render_page' ]
		);
	}

	/**
	 * Register settings.
	 */
	public function register_settings() {
		register_setting(
			self::OPTION_GROUP,
			self::OPTION_NAME,
			[
				'type'              => 'array',
				'sanitize_callback' => [ $this, 'sanitize_options' ],
			]
		);

		add_settings_section(
			'eva_main_section',
			__( 'General Settings', 'extend-video-addons' ),
			[ $this, 'render_main_section' ],
			'extend-video-addons'
		);

		add_settings_field(
			'eva_default_source',
			__( 'Default Video Source', 'extend-video-addons' ),
			[ $this, 'render_default_source_field' ],
			'extend-video-addons',
			'eva_main_section',
			[
				'label_for' => 'eva_default_source',
			]
		);
	}

	/**
	 * Sanitize options.
	 *
	 * @param array $input Raw input.
	 * @return array Sanitized options.
	 */
	public function sanitize_options( $input ) {
		$sanitized = [];

		if ( isset( $input['default_source'] ) && in_array( $input['default_source'], [ 'auto', 'youtube', 'vimeo', 'dailymotion', 'videopress', 'hosted' ], true ) ) {
			$sanitized['default_source'] = $input['default_source'];
		}

		return $sanitized;
	}

	/**
	 * Render main section description.
	 */
	public function render_main_section() {
		echo '<p>' . esc_html__( 'Configure default behavior for the extended video widget.', 'extend-video-addons' ) . '</p>';
	}

	/**
	 * Render default source field.
	 */
	public function render_default_source_field() {
		$options = get_option( self::OPTION_NAME, [] );
		$value   = isset( $options['default_source'] ) ? $options['default_source'] : 'auto';
		?>
		<select id="eva_default_source" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[default_source]">
			<option value="auto" <?php selected( $value, 'auto' ); ?>><?php esc_html_e( 'Auto Detect (Recommended)', 'extend-video-addons' ); ?></option>
			<option value="youtube" <?php selected( $value, 'youtube' ); ?>><?php esc_html_e( 'YouTube', 'extend-video-addons' ); ?></option>
			<option value="vimeo" <?php selected( $value, 'vimeo' ); ?>><?php esc_html_e( 'Vimeo', 'extend-video-addons' ); ?></option>
			<option value="dailymotion" <?php selected( $value, 'dailymotion' ); ?>><?php esc_html_e( 'Dailymotion', 'extend-video-addons' ); ?></option>
			<option value="videopress" <?php selected( $value, 'videopress' ); ?>><?php esc_html_e( 'VideoPress', 'extend-video-addons' ); ?></option>
			<option value="hosted" <?php selected( $value, 'hosted' ); ?>><?php esc_html_e( 'Self Hosted', 'extend-video-addons' ); ?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Default source when adding a new video widget. Auto Detect automatically identifies the platform from the URL.', 'extend-video-addons' ); ?></p>
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

		wp_add_inline_style( 'wp-admin', '
			.eva-about-card { background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; padding: 20px; margin-top: 20px; box-shadow: 0 1px 1px rgba(0,0,0,.04); }
			.eva-about-card h2 { margin-top: 0; }
			.eva-about-card .eva-author-links { margin-top: 15px; }
			.eva-about-card .eva-author-links a { margin-right: 15px; }
			.eva-status-list { list-style: none; padding: 0; margin: 15px 0 0 0; }
			.eva-status-list li { padding: 8px 0; border-bottom: 1px solid #f0f0f1; }
			.eva-status-list li:last-child { border-bottom: none; }
			.eva-status-ok { color: #00a32a; }
			.eva-status-missing { color: #d63638; }
		' );
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
				submit_button( __( 'Save Settings', 'extend-video-addons' ) );
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
		<div class="eva-about-card">
			<h2><?php esc_html_e( 'Compatibility Status', 'extend-video-addons' ); ?></h2>
			<ul class="eva-status-list">
				<li>
					<?php if ( $elementor_active ) : ?>
						<span class="eva-status-ok">✓</span> <?php esc_html_e( 'Elementor: Active', 'extend-video-addons' ); ?>
					<?php else : ?>
						<span class="eva-status-missing">✗</span> <?php esc_html_e( 'Elementor: Not active (required)', 'extend-video-addons' ); ?>
					<?php endif; ?>
				</li>
				<li><?php printf( esc_html__( 'WordPress: %s', 'extend-video-addons' ), esc_html( $wp_version ) ); ?></li>
				<li><?php printf( esc_html__( 'PHP: %s', 'extend-video-addons' ), esc_html( $php_version ) ); ?></li>
				<li><?php printf( esc_html__( 'Plugin Version: %s', 'extend-video-addons' ), esc_html( EVA_VERSION ) ); ?></li>
			</ul>
		</div>
		<?php
	}

	/**
	 * Render About the Author section.
	 */
	private function render_about_section() {
		?>
		<div class="eva-about-card">
			<h2><?php esc_html_e( 'About the Author', 'extend-video-addons' ); ?></h2>
			<p><strong>H M Shahadul Islam</strong></p>
			<p><?php esc_html_e( 'WordPress and Elementor plugin developer. This plugin enhances the Elementor video widget with automatic source detection for a smoother workflow.', 'extend-video-addons' ); ?></p>
			<div class="eva-author-links">
				<a href="https://github.com/shahadul878" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'GitHub', 'extend-video-addons' ); ?></a>
				<a href="https://github.com/shahadul878/extend-video-addons" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Plugin Repository', 'extend-video-addons' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Get default video source from options.
	 *
	 * @return string
	 */
	public static function get_default_source() {
		$options = get_option( self::OPTION_NAME, [] );
		return isset( $options['default_source'] ) ? $options['default_source'] : 'auto';
	}
}
