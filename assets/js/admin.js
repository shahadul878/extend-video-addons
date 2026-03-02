/**
 * Extend Video Addons - Admin JavaScript
 *
 * Provides auto-detection functionality in Elementor editor.
 */

(function ($) {
	'use strict';

	/**
	 * Video Source Detector (Client-side)
	 */
	const VideoDetector = {
		/**
		 * Detect video source from URL
		 *
		 * @param {string} url Video URL
		 * @returns {string|false} Video type or false
		 */
		detect: function (url) {
			if (!url || typeof url !== 'string') {
				return false;
			}

			url = url.toLowerCase().trim();

			// Check for dynamic content tags
			if (url.indexOf('[elementor-tag') !== -1 || url.indexOf('__dynamic__') !== -1) {
				return false;
			}

			// YouTube
			if (url.indexOf('youtube.com') !== -1 || url.indexOf('youtu.be') !== -1 || url.indexOf('youtube-nocookie.com') !== -1) {
				return 'youtube';
			}

			// Vimeo
			if (url.indexOf('vimeo.com') !== -1) {
				return 'vimeo';
			}

			// Dailymotion
			if (url.indexOf('dailymotion.com') !== -1 || url.indexOf('dai.ly') !== -1) {
				return 'dailymotion';
			}

			// VideoPress
			if (url.indexOf('videopress.com') !== -1) {
				return 'videopress';
			}

			// Self-hosted (check file extension)
			const videoExtensions = ['.mp4', '.webm', '.ogg', '.ogv', '.mov', '.m4v', '.avi', '.wmv', '.flv', '.mkv'];
			const urlPath = url.split('?')[0]; // Remove query string
			for (let i = 0; i < videoExtensions.length; i++) {
				if (urlPath.endsWith(videoExtensions[i])) {
					return 'hosted';
				}
			}

			return false;
		}
	};

	/**
	 * Initialize auto-detection
	 */
	function initAutoDetection() {
		// Wait for Elementor to be ready
		if (typeof elementor === 'undefined') {
			setTimeout(initAutoDetection, 100);
			return;
		}

		// Listen for widget settings changes
		elementor.hooks.addAction('panel/open_editor/widget/video', function (panel, model, view) {
			setupAutoDetection(panel, model, view);
		});
	}

	/**
	 * Setup auto-detection for video widget
	 *
	 * @param {object} panel Elementor panel
	 * @param {object} model Widget model
	 * @param {object} view Widget view
	 */
	function setupAutoDetection(panel, model, view) {
		// Get URL input fields
		const urlFields = [
			'youtube_url',
			'vimeo_url',
			'dailymotion_url',
			'videopress_url',
			'external_url',
			'hosted_url'
		];

		urlFields.forEach(function (fieldName) {
			const controlSelector = `[data-setting="${fieldName}"]`;
			const $control = panel.$el.find(controlSelector);

			if ($control.length) {
				// Handle different control types
				const $input = $control.find('input[type="text"], input[type="url"]');

				if ($input.length) {
					// Listen for input changes
					$input.on('input change paste', function () {
						const url = $(this).val();
						if (url && url.trim()) {
							detectAndUpdateVideoType(url, panel, model);
						}
					});
				}
			}
		});

		// Also listen for video_type changes to reset if user manually selects
		const $videoTypeControl = panel.$el.find('[data-setting="video_type"]');
		if ($videoTypeControl.length) {
			$videoTypeControl.on('change', function () {
				const selectedType = $(this).val();
				// If user manually selects a type (not 'auto'), don't auto-detect
				if (selectedType && selectedType !== 'auto') {
					// Remove any error messages
					panel.$el.find('.eva-detection-error').remove();
				}
			});
		}
	}

	/**
	 * Detect video source and update video_type dropdown
	 *
	 * @param {string} url Video URL
	 * @param {object} panel Elementor panel
	 * @param {object} model Widget model
	 */
	function detectAndUpdateVideoType(url, panel, model) {
		// Check current video_type
		const currentType = model.getSetting('video_type');
		
		// Only auto-detect if video_type is empty or 'auto'
		if (currentType && currentType !== 'auto') {
			return;
		}

		const detectedType = VideoDetector.detect(url);

		if (detectedType) {
			// Update video_type control
			const $videoTypeControl = panel.$el.find('[data-setting="video_type"]');
			if ($videoTypeControl.length) {
				$videoTypeControl.val(detectedType).trigger('change');
				
				// Update model
				model.setSetting('video_type', detectedType);
				
				// Show success message briefly
				showDetectionMessage(panel, 'success', 'Video source detected: ' + detectedType);
			}
		} else if (url && url.trim() && !VideoDetector.isDynamicContent(url)) {
			// Show error message if detection fails (but not for dynamic content)
			showDetectionMessage(panel, 'error', 'Unable to detect video source. Please select manually.');
		}
	}

	/**
	 * Check if URL contains dynamic content
	 *
	 * @param {string} url URL to check
	 * @returns {boolean}
	 */
	VideoDetector.isDynamicContent = function (url) {
		return url.indexOf('[elementor-tag') !== -1 || url.indexOf('__dynamic__') !== -1;
	};

	/**
	 * Show detection message
	 *
	 * @param {object} panel Elementor panel
	 * @param {string} type Message type (success/error)
	 * @param {string} message Message text
	 */
	function showDetectionMessage(panel, type, message) {
		// Remove existing messages
		panel.$el.find('.eva-detection-message').remove();

		const $message = $('<div>', {
			class: 'eva-detection-message eva-detection-' + type,
			text: message,
			css: {
				padding: '8px 12px',
				margin: '10px 0',
				borderRadius: '3px',
				fontSize: '12px',
				backgroundColor: type === 'success' ? '#d4edda' : '#f8d7da',
				color: type === 'success' ? '#155724' : '#721c24',
				border: '1px solid ' + (type === 'success' ? '#c3e6cb' : '#f5c6cb')
			}
		});

		// Insert after video_type control
		const $videoTypeControl = panel.$el.find('[data-setting="video_type"]').closest('.elementor-control');
		if ($videoTypeControl.length) {
			$videoTypeControl.after($message);
			
			// Auto-remove after 3 seconds
			setTimeout(function () {
				$message.fadeOut(300, function () {
					$(this).remove();
				});
			}, 3000);
		}
	}

	// Initialize when DOM is ready
	$(document).ready(function () {
		initAutoDetection();
	});

	// Also initialize when Elementor editor is loaded
	if (typeof elementor !== 'undefined') {
		elementor.on('preview:loaded', function () {
			initAutoDetection();
		});
	}

})(jQuery);
