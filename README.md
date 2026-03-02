# Extend Video Addons

Enhances the Elementor video widget with automatic video source detection. Paste any supported video URL and the plugin automatically detects the platform—YouTube, Vimeo, Dailymotion, VideoPress, or self-hosted.

![WordPress](https://img.shields.io/badge/WordPress-5.0+-blue.svg)
![Elementor](https://img.shields.io/badge/Elementor-3.0+-green.svg)
![PHP](https://img.shields.io/badge/PHP-7.4+-purple.svg)
![License](https://img.shields.io/badge/License-GPL%20v2-orange.svg)

## Features

- **Auto Source Detection** – Automatically detects video platform from URL patterns
- **Multiple Platform Support** – YouTube, Vimeo, Dailymotion, VideoPress, and self-hosted videos
- **Dynamic Content Support** – Works with Elementor dynamic content (ACF fields, post meta)
- **Seamless Integration** – Replaces the original Elementor video widget with enhanced version
- **Backward Compatible** – All original Elementor video widget features preserved

## Supported Video Platforms

| Platform    | URL Patterns                                      |
| ----------- | ------------------------------------------------- |
| YouTube     | youtube.com, youtu.be, youtube-nocookie.com       |
| Vimeo       | vimeo.com                                         |
| Dailymotion | dailymotion.com, dai.ly                           |
| VideoPress  | videopress.com                                    |
| Self-Hosted | .mp4, .webm, .ogg, .ogv, .mov, .m4v, .avi, .wmv, .flv, .mkv |

## Requirements

- WordPress 5.0+
- Elementor 3.0+
- PHP 7.4+

## Installation

1. Upload the `extend-video-addons` folder to `/wp-content/plugins/`
2. Activate the plugin through **Plugins** → **Add New** → **Activate**
3. Ensure Elementor is installed and activated
4. Configure options in **Settings** → **Extend Video Addons** (optional)

## Usage

1. Add a **Video** widget to your Elementor page
2. In the widget settings, select **Auto Detect** from the **Source** dropdown
3. Paste your video URL in the **Link** field
4. The plugin detects the platform automatically and displays the video

### Manual Override

You can always manually select YouTube, Vimeo, Dailymotion, VideoPress, or Self Hosted if auto-detection fails or you prefer manual control.

## Settings

Access **Settings** → **Extend Video Addons** to:

- View plugin information and compatibility status
- Configure default behavior (optional)
- Access the About the Author section

## About the Author

**H M Shahadul Islam**

- GitHub: [@shahadul878](https://github.com/shahadul878)
- Plugin URL: [GitHub Repository](https://github.com/shahadul878/extend-video-addons)

## Technical Details

- **Namespace:** `ExtendVideoAddons`
- **Text Domain:** `extend-video-addons`
- **Widget:** Replaces Elementor `video` widget
- **Hooks:** `elementor/widgets/register` (priority 99)

## Changelog

### 1.0.0
- Initial release
- Auto source detection
- Support for all major video platforms
- Dynamic content support
- Direct render for auto-detected self-hosted videos
- Settings page with About section

## License

GPL v2 or later. See [license file](https://www.gnu.org/licenses/gpl-2.0.html).
