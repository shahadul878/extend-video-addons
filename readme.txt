=== Extend Video Addons ===

Contributors: shahadul878
Tags: elementor, video, youtube, vimeo, dailymotion, videopress, auto-detect, self-hosted
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Enhances Elementor video widget with automatic video source detection from URL patterns. Supports YouTube, Vimeo, Dailymotion, VideoPress, and self-hosted videos.

== Description ==

Extend Video Addons adds an **Auto Detect** option to the Elementor video widget, so you can paste any supported video URL and the plugin will automatically detect the source. No need to manually select YouTube, Vimeo, or Self Hosted—just paste and go.

= Features =

* **Auto Source Detection** - Automatically detects video platform from URL patterns
* **Multiple Platform Support** - YouTube, Vimeo, Dailymotion, VideoPress, and self-hosted videos (.mp4, .webm, .ogg, .mov, etc.)
* **Dynamic Content Support** - Works with Elementor dynamic content (ACF fields, post meta)
* **Seamless Integration** - Replaces the original Elementor video widget with enhanced version
* **Backward Compatible** - All original Elementor video widget features preserved

= Supported Video Platforms =

* **YouTube** - youtube.com, youtu.be, youtube-nocookie.com
* **Vimeo** - vimeo.com
* **Dailymotion** - dailymotion.com, dai.ly
* **VideoPress** - videopress.com
* **Self-Hosted** - .mp4, .webm, .ogg, .ogv, .mov, .m4v, .avi, .wmv, .flv, .mkv

= Requirements =

* WordPress 5.0 or higher
* Elementor 3.0 or higher
* PHP 7.4 or higher

== Installation ==

1. Upload the `extend-video-addons` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure Elementor is installed and activated
4. Add a Video widget in Elementor and select "Auto Detect" from Source

== Frequently Asked Questions ==

= What happens if the plugin can't detect the video source? =

An error message will appear asking you to select the video source manually from the dropdown. You can always override auto-detection by choosing a specific source (YouTube, Vimeo, etc.).

= Does this work with dynamic content? =

Yes. The plugin fully supports Elementor dynamic content. URLs from ACF fields, post meta, and other dynamic sources are detected after the dynamic tags are resolved.

= Will this affect my existing video widgets? =

The plugin seamlessly replaces the Elementor video widget. Existing widgets continue to work. When editing, you'll see the new "Auto Detect" option in the Source dropdown.

== Screenshots ==

1. Video widget with Auto Detect source selected
2. Paste URL and video is automatically detected

== Changelog ==

= 1.0.0 =
* Initial release
* Auto source detection for Elementor video widget
* Support for YouTube, Vimeo, Dailymotion, VideoPress, self-hosted
* Dynamic content support
* Direct render for auto-detected self-hosted videos

== Upgrade Notice ==

= 1.0.0 =
Initial release of Extend Video Addons.
