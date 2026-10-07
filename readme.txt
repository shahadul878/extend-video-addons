=== Videtect – Auto Video Source for Elementor ===

Contributors: shahadul878
Tags: elementor, video, youtube, vimeo, autodetection
Requires at least: 5.0
Tested up to: 7.1
Stable tag: 1.0.5
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Auto-detects YouTube, Vimeo, Dailymotion, VideoPress, and self-hosted URLs in the Elementor Video widget.

== Description ==

**Videtect – Auto Video Source for Elementor** adds an **Auto Detect** option to Elementor's Video widget. Paste any supported video URL and the plugin identifies the source. You do not need to manually choose YouTube, Vimeo, or Self Hosted.

This plugin is not affiliated with Elementor. "Elementor" is a trademark of its respective owner.

= Features =

* **Auto Source Detection** - Automatically detects the video platform from URL patterns
* **Multiple Platform Support** - YouTube, Vimeo, Dailymotion, VideoPress, and self-hosted videos (.mp4, .webm, .ogg, .mov, etc.)
* **Dynamic Content Support** - Works with Elementor dynamic content (ACF fields, post meta)
* **Non-invasive** - Extends the existing Elementor Video widget; it does not replace it
* **Backward Compatible** - Existing Video widgets and all original Elementor video features continue to work

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

1. Upload the `videtect` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure Elementor is installed and activated
4. Add a Video widget in Elementor and select "Auto Detect" from Source

== Frequently Asked Questions ==

= What happens if the plugin can't detect the video source? =

An error message will appear asking you to select the video source manually from the dropdown. You can always override auto-detection by choosing a specific source (YouTube, Vimeo, etc.).

= Does this work with dynamic content? =

Yes. The plugin fully supports Elementor dynamic content. URLs from ACF fields, post meta, and other dynamic sources are detected after the dynamic tags are resolved.

= Will this affect my existing video widgets? =

No. The plugin adds an Auto Detect option to Elementor's Video widget. Existing widgets keep working. When editing, you will see Auto Detect in the Source dropdown.

= Is this an official Elementor plugin? =

No. This is an independent plugin by H M Shahadul Islam. It is not affiliated with, endorsed by, or sponsored by Elementor.

== Screenshots ==

1. Video widget with Auto Detect source selected
2. Paste URL and video is automatically detected

== Changelog ==

= 1.0.5 =
* Rename to Videtect – Auto Video Source for Elementor
* Text domain / slug: `videtect`

= 1.0.4 =
* Rename for WordPress.org trademark and distinctiveness guidelines

= 1.0.3 =
* Fix text domain to match the plugin slug

= 1.0.1 =
* WordPress.org compatibility updates
* Extend Elementor's Video widget with hooks instead of replacing it
* Update Tested up to WordPress 7.1
* Localize editor detection messages
* Apply the default source setting to new Video widgets

= 1.0.0 =
* Initial release
* Auto source detection for Elementor video widget
* Support for YouTube, Vimeo, Dailymotion, VideoPress, self-hosted
* Dynamic content support

== Upgrade Notice ==

= 1.0.5 =
Renamed to Videtect – Auto Video Source for Elementor. Request slug: videtect.
