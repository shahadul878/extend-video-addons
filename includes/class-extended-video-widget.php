<?php
/**
 * Extended Video Widget
 *
 * Extends Elementor video widget with auto source detection.
 *
 * @package ExtendVideoAddons
 */

namespace ExtendVideoAddons;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Widget_Video;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Extended_Video_Widget
 *
 * Extends Elementor Widget_Video to add auto-detection functionality.
 */
class Extended_Video_Widget extends Widget_Video {

	/**
	 * Register video widget controls.
	 *
	 * Adds "Auto Detect" option to video_type dropdown.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		// Call parent to get all original controls
		parent::register_controls();

		// Modify video_type control to add "Auto Detect" option
		$elementor = Plugin::instance();
		$control_data = $elementor->controls_manager->get_control_from_stack(
			$this->get_unique_name(),
			'video_type'
		);

		if ( ! is_wp_error( $control_data ) && isset( $control_data['options'] ) ) {
			// Add "Auto Detect" option at the beginning
			$control_data['options'] = array_merge(
				[ 'auto' => esc_html__( 'Auto Detect', 'extend-video-addons' ) ],
				$control_data['options']
			);

			// Update the control (first arg must be Controls_Stack instance, not string)
			$elementor->controls_manager->update_control_in_stack(
				$this,
				'video_type',
				$control_data
			);
		}

		// Add Video URL field for Auto Detect - appears when Source is "Auto Detect"
		$this->start_injection( [
			'of' => 'video_type',
			'at' => 'after',
		] );
		$this->add_control(
			'auto_url',
			[
				'label'   => esc_html__( 'Link', 'extend-video-addons' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'placeholder' => esc_html__( 'Enter your URL (YouTube, Vimeo, Dailymotion, etc.)', 'extend-video-addons' ),
				'label_block' => true,
				'condition' => [
					'video_type' => 'auto',
				],
			]
		);
		$this->end_injection();
	}

	/**
	 * Render video widget output on the frontend.
	 *
	 * Adds auto-detection logic before calling parent render.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		// Check if auto-detection is needed
		if ( empty( $settings['video_type'] ) || 'auto' === $settings['video_type'] ) {
			// Get the URL from the appropriate field based on current video_type
			// Since video_type might be 'auto', we need to check all URL fields
			$video_data = $this->get_video_url_for_detection( $settings );

			if ( ! empty( $video_data ) && isset( $video_data['url'] ) ) {
				// Detect video source
				$detected_type = Video_Source_Detector::detect_video_source( $video_data['url'] );

				if ( $detected_type ) {
					// For hosted from Auto Detect: render directly - parent's settings chain
					// doesn't pick up our runtime changes reliably.
					if ( 'hosted' === $detected_type && 'auto_url' === $video_data['field'] ) {
						$this->render_auto_detected_hosted_video( $video_data['url'], $settings );
						return;
					}

					// For other types, update settings and let parent render
					$this->ensure_url_in_correct_field( $detected_type, $video_data['url'], $video_data['field'] );
					$this->set_settings( 'video_type', $detected_type );
					$this->parsed_active_settings  = null;
					$this->parsed_dynamic_settings = null;
				} else {
					// Show error message if detection fails
					?>
					<div class="elementor-alert elementor-alert-danger" role="alert">
						<span class="elementor-alert-title">
							<?php echo esc_html__( 'Unable to detect video source', 'extend-video-addons' ); ?>
						</span>
						<span class="elementor-alert-description">
							<?php echo esc_html__( 'Please select the video source manually from the widget settings.', 'extend-video-addons' ); ?>
						</span>
					</div>
					<?php
					return;
				}
			} else {
				// No URL provided
				?>
				<div class="elementor-alert elementor-alert-warning" role="alert">
					<span class="elementor-alert-title">
						<?php echo esc_html__( 'Video URL required', 'extend-video-addons' ); ?>
					</span>
					<span class="elementor-alert-description">
						<?php echo esc_html__( 'Please provide a video URL in the widget settings.', 'extend-video-addons' ); ?>
					</span>
				</div>
				<?php
				return;
			}
		}

		// Call parent render to use all original functionality
		parent::render();
	}

	/**
	 * Render hosted video when auto-detected from URL.
	 *
	 * Bypasses parent's settings chain which doesn't pick up runtime changes.
	 *
	 * @param string $url      Video URL.
	 * @param array  $settings Widget settings from get_settings_for_display().
	 */
	private function render_auto_detected_hosted_video( $url, $settings ) {
		$video_url = $url;
		if ( ! empty( $settings['start'] ) || ! empty( $settings['end'] ) ) {
			$video_url .= '#t=';
			if ( ! empty( $settings['start'] ) ) {
				$video_url .= $settings['start'];
			}
			if ( ! empty( $settings['end'] ) ) {
				$video_url .= ',' . $settings['end'];
			}
		}

		$video_params = [];
		foreach ( [ 'autoplay', 'loop', 'controls' ] as $option_name ) {
			if ( ! empty( $settings[ $option_name ] ) ) {
				$video_params[ $option_name ] = '';
			}
		}
		if ( ! empty( $settings['preload'] ) ) {
			$video_params['preload'] = $settings['preload'];
		} else {
			$video_params['preload'] = 'metadata';
		}
		if ( ! empty( $settings['mute'] ) ) {
			$video_params['muted'] = 'muted';
		}
		if ( ! empty( $settings['play_on_mobile'] ) ) {
			$video_params['playsinline'] = '';
		}
		if ( empty( $settings['download_button'] ) ) {
			$video_params['controlsList'] = 'nodownload';
		}
		if ( ! empty( $settings['poster']['url'] ) ) {
			$video_params['poster'] = $settings['poster']['url'];
		}

		$this->add_render_attribute( 'video-wrapper', 'class', 'elementor-wrapper elementor-open-inline e-hosted-video' );
		?>
		<div <?php $this->print_render_attribute_string( 'video-wrapper' ); ?>>
			<video class="elementor-video" src="<?php echo esc_attr( $video_url ); ?>" <?php Utils::print_html_attributes( $video_params ); ?>></video>
		</div>
		<?php
	}

	/**
	 * Get video URL for detection
	 *
	 * Checks all possible URL fields to find the video URL.
	 * Also ensures the URL is in the correct field for the detected type.
	 *
	 * @param array $settings Widget settings.
	 * @return array Array with 'url' and 'field' keys, or empty array.
	 */
	private function get_video_url_for_detection( $settings ) {
		// When Auto Detect is selected, check auto_url first
		if ( ! empty( $settings['video_type'] ) && 'auto' === $settings['video_type'] ) {
			$url = isset( $settings['auto_url'] ) ? $settings['auto_url'] : '';
			if ( ! empty( $url ) && is_string( $url ) ) {
				return [ 'url' => $url, 'field' => 'auto_url' ];
			}
		}

		// Check all possible URL fields
		$url_fields = [
			'youtube_url',
			'vimeo_url',
			'dailymotion_url',
			'videopress_url',
			'hosted_url',
			'external_url',
		];

		foreach ( $url_fields as $field ) {
			if ( isset( $settings[ $field ] ) ) {
				$url = $settings[ $field ];

				// Handle external_url which is an array with 'url' key
				if ( 'external_url' === $field && is_array( $url ) && isset( $url['url'] ) ) {
					$url = $url['url'];
				}

				// Handle hosted_url which is an array with 'url' key
				if ( 'hosted_url' === $field && is_array( $url ) && isset( $url['url'] ) ) {
					$url = $url['url'];
				}

				if ( ! empty( $url ) && is_string( $url ) ) {
					return [
						'url'   => $url,
						'field' => $field,
					];
				}
			}
		}

		return [];
	}

	/**
	 * Ensure URL is in the correct field for the detected video type
	 *
	 * @param string $detected_type Detected video type.
	 * @param string $url Video URL.
	 * @param string $current_field Current field name where URL is stored.
	 */
	private function ensure_url_in_correct_field( $detected_type, $url, $current_field ) {
		$expected_field = $detected_type . '_url';

		// When coming from auto_url, always need to set the correct field
		if ( 'auto_url' === $current_field ) {
			if ( 'hosted' === $detected_type ) {
				$this->set_settings( 'insert_url', 'yes' );
				// Match Elementor URL control structure for get_hosted_video_url()
				$this->set_settings( 'external_url', [
					'url'               => $url,
					'is_external'       => '',
					'nofollow'          => '',
					'custom_attributes' => '',
				] );
			} elseif ( 'videopress' === $detected_type ) {
				$this->set_settings( 'insert_url', 'yes' );
				$this->set_settings( 'videopress_url', $url );
			} else {
				$this->set_settings( $expected_field, $url );
			}
			return;
		}

		// Special handling for hosted videos
		if ( 'hosted' === $detected_type ) {
			// Check if URL is external or hosted
			if ( 'external_url' === $current_field ) {
				// Keep it in external_url, parent will handle it
				return;
			} elseif ( 'hosted_url' !== $current_field ) {
				// Move to hosted_url field
				$this->set_settings( 'hosted_url', [ 'url' => $url ] );
				$this->set_settings( 'insert_url', '' );
			}
		} elseif ( 'videopress' === $detected_type ) {
			// VideoPress can be in videopress_url or hosted_url
			if ( 'hosted_url' === $current_field ) {
				// Keep it, parent will handle it
				return;
			} elseif ( 'videopress_url' !== $current_field ) {
				// Move to videopress_url
				$this->set_settings( 'videopress_url', $url );
				$this->set_settings( 'insert_url', 'yes' );
			}
		} elseif ( $expected_field !== $current_field ) {
			// Move URL to the correct field
			$this->set_settings( $expected_field, $url );
			// Clear the old field
			$this->set_settings( $current_field, '' );
		}
	}
}
