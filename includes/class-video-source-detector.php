<?php
/**
 * Video Source Detector
 *
 * Detects video platform from URL patterns.
 *
 * @package ExtendVideoAddons
 */

namespace ExtendVideoAddons;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Video_Source_Detector
 */
class Video_Source_Detector {

	/**
	 * Detect video source from URL
	 *
	 * @param string $url Video URL.
	 * @return string|false Video type (youtube, vimeo, dailymotion, videopress, hosted) or false if undetectable.
	 */
	public static function detect_video_source( $url ) {
		if ( empty( $url ) || ! is_string( $url ) ) {
			return false;
		}

		// Clean URL
		$url = trim( $url );
		$url = strtolower( $url );

		// Check for dynamic content tags (skip detection in editor)
		if ( self::is_dynamic_content( $url ) ) {
			return false;
		}

		// YouTube detection
		if ( self::is_youtube( $url ) ) {
			return 'youtube';
		}

		// Vimeo detection
		if ( self::is_vimeo( $url ) ) {
			return 'vimeo';
		}

		// Dailymotion detection
		if ( self::is_dailymotion( $url ) ) {
			return 'dailymotion';
		}

		// VideoPress detection
		if ( self::is_videopress( $url ) ) {
			return 'videopress';
		}

		// Self-hosted video detection (by file extension)
		if ( self::is_hosted( $url ) ) {
			return 'hosted';
		}

		return false;
	}

	/**
	 * Check if URL contains dynamic content tag structure
	 *
	 * @param string $url URL to check.
	 * @return bool True if dynamic content detected.
	 */
	public static function is_dynamic_content( $url ) {
		return false !== strpos( $url, '[elementor-tag' ) || false !== strpos( $url, '__dynamic__' );
	}

	/**
	 * Check if URL is YouTube
	 *
	 * @param string $url URL to check.
	 * @return bool True if YouTube URL.
	 */
	private static function is_youtube( $url ) {
		$patterns = [
			'youtube.com',
			'youtu.be',
			'youtube-nocookie.com',
		];

		foreach ( $patterns as $pattern ) {
			if ( false !== strpos( $url, $pattern ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if URL is Vimeo
	 *
	 * @param string $url URL to check.
	 * @return bool True if Vimeo URL.
	 */
	private static function is_vimeo( $url ) {
		return false !== strpos( $url, 'vimeo.com' );
	}

	/**
	 * Check if URL is Dailymotion
	 *
	 * @param string $url URL to check.
	 * @return bool True if Dailymotion URL.
	 */
	private static function is_dailymotion( $url ) {
		$patterns = [
			'dailymotion.com',
			'dai.ly',
		];

		foreach ( $patterns as $pattern ) {
			if ( false !== strpos( $url, $pattern ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if URL is VideoPress
	 *
	 * @param string $url URL to check.
	 * @return bool True if VideoPress URL.
	 */
	private static function is_videopress( $url ) {
		return false !== strpos( $url, 'videopress.com' );
	}

	/**
	 * Check if URL is self-hosted video (by file extension)
	 *
	 * @param string $url URL to check.
	 * @return bool True if self-hosted video URL.
	 */
	private static function is_hosted( $url ) {
		$video_extensions = [
			'.mp4',
			'.webm',
			'.ogg',
			'.ogv',
			'.mov',
			'.m4v',
			'.avi',
			'.wmv',
			'.flv',
			'.mkv',
		];

		$url_lower = strtolower( $url );
		$url_path = wp_parse_url( $url_lower, PHP_URL_PATH );

		if ( empty( $url_path ) ) {
			return false;
		}

		foreach ( $video_extensions as $ext ) {
			if ( substr( $url_path, -strlen( $ext ) ) === $ext ) {
				return true;
			}
		}

		return false;
	}
}
