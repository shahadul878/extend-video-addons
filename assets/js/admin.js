/**
 * Extend Video Add-ons for Elementor - Editor JavaScript
 *
 * Shows Auto Detect feedback in the Elementor editor without changing the
 * selected source while Auto Detect is active.
 */
(function ($) {
	'use strict';

	const i18n = (window.extendVideoAddons && window.extendVideoAddons.i18n) ? window.extendVideoAddons.i18n : {
		detected: 'Video source detected: %s',
		undetectable: 'Unable to detect video source. Please select manually.'
	};

	const VideoDetector = {
		detect: function (url) {
			if (!url || typeof url !== 'string') {
				return false;
			}

			url = url.toLowerCase().trim();

			if (url.indexOf('[elementor-tag') !== -1 || url.indexOf('__dynamic__') !== -1) {
				return false;
			}

			if (url.indexOf('youtube.com') !== -1 || url.indexOf('youtu.be') !== -1 || url.indexOf('youtube-nocookie.com') !== -1) {
				return 'youtube';
			}

			if (url.indexOf('vimeo.com') !== -1) {
				return 'vimeo';
			}

			if (url.indexOf('dailymotion.com') !== -1 || url.indexOf('dai.ly') !== -1) {
				return 'dailymotion';
			}

			if (url.indexOf('videopress.com') !== -1) {
				return 'videopress';
			}

			const videoExtensions = ['.mp4', '.webm', '.ogg', '.ogv', '.mov', '.m4v', '.avi', '.wmv', '.flv', '.mkv'];
			const urlPath = url.split('?')[0];
			for (let i = 0; i < videoExtensions.length; i++) {
				if (urlPath.endsWith(videoExtensions[i])) {
					return 'hosted';
				}
			}

			return false;
		},

		isDynamicContent: function (url) {
			return url.indexOf('[elementor-tag') !== -1 || url.indexOf('__dynamic__') !== -1;
		}
	};

	function initAutoDetection() {
		if (typeof elementor === 'undefined') {
			setTimeout(initAutoDetection, 100);
			return;
		}

		elementor.hooks.addAction('panel/open_editor/widget/video', function (panel, model) {
			setupAutoDetection(panel, model);
		});
	}

	function setupAutoDetection(panel, model) {
		const $autoInput = panel.$el.find('[data-setting="auto_url"]').find('input[type="text"], input[type="url"]');

		if ($autoInput.length) {
			$autoInput.on('input change paste', function () {
				const url = $(this).val();
				if (url && url.trim()) {
					showDetectionResult(url, panel, model);
				}
			});
		}

		const $videoTypeControl = panel.$el.find('[data-setting="video_type"]');
		if ($videoTypeControl.length) {
			$videoTypeControl.on('change', function () {
				const selectedType = $(this).val();
				if (selectedType && selectedType !== 'auto') {
					panel.$el.find('.extend-video-addons-detection-message').remove();
				}
			});
		}
	}

	function showDetectionResult(url, panel, model) {
		const currentType = model.getSetting('video_type');

		if (currentType && currentType !== 'auto') {
			return;
		}

		const detectedType = VideoDetector.detect(url);

		if (detectedType) {
			const label = i18n.detected.replace('%s', detectedType);
			showDetectionMessage(panel, 'success', label);
			return;
		}

		if (url && url.trim() && !VideoDetector.isDynamicContent(url)) {
			showDetectionMessage(panel, 'error', i18n.undetectable);
		}
	}

	function showDetectionMessage(panel, type, message) {
		panel.$el.find('.extend-video-addons-detection-message').remove();

		const $message = $('<div>', {
			class: 'extend-video-addons-detection-message extend-video-addons-detection-' + type,
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

		const $videoTypeControl = panel.$el.find('[data-setting="video_type"]').closest('.elementor-control');
		if ($videoTypeControl.length) {
			$videoTypeControl.after($message);

			setTimeout(function () {
				$message.fadeOut(300, function () {
					$(this).remove();
				});
			}, 3000);
		}
	}

	$(function () {
		initAutoDetection();
	});

	if (typeof elementor !== 'undefined') {
		elementor.on('preview:loaded', function () {
			initAutoDetection();
		});
	}

})(jQuery);
