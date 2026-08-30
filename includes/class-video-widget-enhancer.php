<?php
/**
 * Video widget enhancer.
 *
 * Injects Auto Detect into Elementor's existing Video widget without replacing it.
 *
 * @package ExtendVideoAddons
 */

namespace ExtendVideoAddons;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Video_Widget_Enhancer
 */
class Video_Widget_Enhancer {

	/**
	 * Singleton instance.
	 *
	 * @var Video_Widget_Enhancer|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Video_Widget_Enhancer
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'elementor/element/video/section_video/before_section_end', array( $this, 'register_controls' ) );
		add_action( 'elementor/widget/before_render_content', array( $this, 'before_render_content' ) );
	}

	/**
	 * Add Auto Detect option and URL field to the Video widget.
	 *
	 * @param Controls_Stack $element Video widget instance.
	 */
	public function register_controls( $element ) {
		$control = $element->get_controls( 'video_type' );

		if ( is_array( $control ) && ! empty( $control['options'] ) ) {
			$options = $control['options'];
			unset( $options['auto'] );

			$element->update_control(
				'video_type',
				array(
					'options' => array( 'auto' => esc_html__( 'Auto Detect', 'extend-video-addons' ) ) + $options,
					'default' => get_default_video_source(),
				)
			);
		}

		if ( null !== $element->get_controls( 'auto_url' ) ) {
			return;
		}

		$element->start_injection(
			array(
				'of' => 'video_type',
				'at' => 'after',
			)
		);

		$element->add_control(
			'auto_url',
			array(
				'label'       => esc_html__( 'Link', 'extend-video-addons' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active'     => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'placeholder' => esc_html__( 'Enter your URL (YouTube, Vimeo, Dailymotion, etc.)', 'extend-video-addons' ),
				'label_block' => true,
				'condition'   => array(
					'video_type' => 'auto',
				),
			)
		);

		$element->end_injection();
	}

	/**
	 * Resolve Auto Detect before Elementor renders the Video widget.
	 *
	 * Settings are updated before get_settings_for_display() is first called,
	 * so Elementor's parsed-settings cache is never stale.
	 *
	 * @param Widget_Base $widget Widget instance.
	 */
	public function before_render_content( $widget ) {
		if ( 'video' !== $widget->get_name() ) {
			return;
		}

		$raw      = $widget->get_settings();
		$settings = $widget->parse_dynamic_settings( $raw );
		$type     = isset( $settings['video_type'] ) ? $settings['video_type'] : '';

		if ( '' !== $type && 'auto' !== $type ) {
			return;
		}

		$video_data = $this->get_video_url_for_detection( $settings );

		if ( empty( $video_data['url'] ) ) {
			$this->force_empty_render( $widget );
			echo wp_kses_post(
				$this->get_notice_html(
					__( 'Video URL required', 'extend-video-addons' ),
					__( 'Please provide a video URL in the widget settings.', 'extend-video-addons' ),
					'warning'
				)
			);
			return;
		}

		$detected_type = Video_Source_Detector::detect_video_source( $video_data['url'] );

		if ( ! $detected_type ) {
			$this->force_empty_render( $widget );
			echo wp_kses_post(
				$this->get_notice_html(
					__( 'Unable to detect video source', 'extend-video-addons' ),
					__( 'Please select the video source manually from the widget settings.', 'extend-video-addons' ),
					'danger'
				)
			);
			return;
		}

		$this->apply_detected_source( $widget, $detected_type, $video_data['url'] );
	}

	/**
	 * Prevent Elementor from rendering a fallback when Auto Detect fails.
	 *
	 * @param Widget_Base $widget Widget instance.
	 */
	private function force_empty_render( $widget ) {
		$widget->set_settings( 'video_type', 'youtube' );
		$widget->set_settings( 'youtube_url', '' );
	}

	/**
	 * Copy the detected URL into the fields Elementor expects.
	 *
	 * @param Widget_Base $widget        Widget instance.
	 * @param string      $detected_type Detected source key.
	 * @param string      $url           Video URL.
	 */
	private function apply_detected_source( $widget, $detected_type, $url ) {
		$widget->set_settings( 'video_type', $detected_type );

		if ( 'hosted' === $detected_type ) {
			$widget->set_settings( 'insert_url', 'yes' );
			$widget->set_settings(
				'external_url',
				array(
					'url'               => $url,
					'is_external'       => '',
					'nofollow'          => '',
					'custom_attributes' => '',
				)
			);
			return;
		}

		if ( 'videopress' === $detected_type ) {
			$widget->set_settings( 'insert_url', 'yes' );
			$widget->set_settings( 'videopress_url', $url );
			return;
		}

		$widget->set_settings( $detected_type . '_url', $url );
	}

	/**
	 * Find a URL to detect from widget settings.
	 *
	 * @param array $settings Parsed widget settings.
	 * @return array{url: string, field: string}
	 */
	private function get_video_url_for_detection( $settings ) {
		if ( ! empty( $settings['auto_url'] ) && is_string( $settings['auto_url'] ) ) {
			return array(
				'url'   => $settings['auto_url'],
				'field' => 'auto_url',
			);
		}

		$url_fields = array(
			'youtube_url',
			'vimeo_url',
			'dailymotion_url',
			'videopress_url',
			'hosted_url',
			'external_url',
		);

		foreach ( $url_fields as $field ) {
			if ( empty( $settings[ $field ] ) ) {
				continue;
			}

			$url = $settings[ $field ];

			if ( in_array( $field, array( 'external_url', 'hosted_url' ), true ) && is_array( $url ) && isset( $url['url'] ) ) {
				$url = $url['url'];
			}

			if ( ! empty( $url ) && is_string( $url ) ) {
				return array(
					'url'   => $url,
					'field' => $field,
				);
			}
		}

		return array(
			'url'   => '',
			'field' => '',
		);
	}

	/**
	 * Build an Elementor-styled notice.
	 *
	 * @param string $title       Notice title.
	 * @param string $description Notice description.
	 * @param string $type        Alert type (danger|warning).
	 * @return string
	 */
	private function get_notice_html( $title, $description, $type ) {
		$type = in_array( $type, array( 'danger', 'warning' ), true ) ? $type : 'danger';

		return sprintf(
			'<div class="elementor-alert elementor-alert-%1$s" role="alert"><span class="elementor-alert-title">%2$s</span><span class="elementor-alert-description">%3$s</span></div>',
			esc_attr( $type ),
			esc_html( $title ),
			esc_html( $description )
		);
	}
}
