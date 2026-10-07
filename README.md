# Videtect – Auto Video Source for Elementor

Enhances the Elementor video widget with automatic video source detection. Paste any supported video URL and the plugin automatically detects the platform—YouTube, Vimeo, Dailymotion, VideoPress, or self-hosted.

This plugin is **not affiliated with Elementor**. "Elementor" is a trademark of its respective owner.

![WordPress](https://img.shields.io/badge/WordPress-5.0+-blue.svg)
![Elementor](https://img.shields.io/badge/Elementor-3.0+-green.svg)
![PHP](https://img.shields.io/badge/PHP-7.4+-purple.svg)
![License](https://img.shields.io/badge/License-GPL%20v2-orange.svg)

## Features

- **Auto Source Detection** – Automatically detects video platform from URL patterns
- **Multiple Platform Support** – YouTube, Vimeo, Dailymotion, VideoPress, and self-hosted videos
- **Dynamic Content Support** – Works with Elementor dynamic content (ACF fields, post meta)
- **Non-invasive** – Extends Elementor's existing Video widget; does not replace it
- **Backward Compatible** – Existing Video widgets and original Elementor features continue to work

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

1. Upload the `videtect` folder to `/wp-content/plugins/`
2. Activate the plugin through **Plugins** → **Add New** → **Activate**
3. Ensure Elementor is installed and activated
4. Configure options in **Settings** → **Videtect** (optional)

## Usage

1. Add a **Video** widget to your Elementor page
2. In the widget settings, select **Auto Detect** from the **Source** dropdown
3. Paste your video URL in the **Link** field
4. The plugin detects the platform automatically and displays the video

### Manual Override

You can always manually select YouTube, Vimeo, Dailymotion, VideoPress, or Self Hosted if auto-detection fails or you prefer manual control.

## Settings

Access **Settings** → **Videtect** to:

- View plugin information and compatibility status
- Configure default behavior (optional)
- Access the About the Author section

## About the Author

**H M Shahadul Islam**

- GitHub: [@shahadul878](https://github.com/shahadul878)
- Plugin URL: [GitHub Repository](https://github.com/shahadul878/extend-video-addons)

## Technical Details

- **Namespace:** `Videtect`
- **Text Domain / Slug:** `videtect`
- **Widget:** Extends Elementor `video` widget via hooks (does not unregister it)
- **Hooks:** `elementor/element/video/section_video/before_section_end`, `elementor/widget/before_render_content`

## Releasing

Releases are built and published automatically via [GitHub Actions](.github/workflows/release.yml).

1. Update version in `videtect.php` and `readme.txt` (Stable tag).
2. Update [CHANGELOG.md](CHANGELOG.md) for the new version.
3. Commit, then create and push an annotated tag:
   ```bash
   git tag -a v1.0.5 -m "Release 1.0.5"
   git push origin v1.0.5
   ```
4. The workflow builds `videtect-{version}.zip` and creates a [GitHub Release](https://github.com/shahadul878/extend-video-addons/releases).

## Changelog

### 1.0.5
- Renamed to Videtect – Auto Video Source for Elementor
- Text domain / slug: `videtect`

### 1.0.4
- Renamed for WordPress.org trademark guidelines

### 1.0.3
- Text domain matches plugin slug

### 1.0.1
- WordPress.org compatibility (text domain, readme, Tested up to 7.1)
- Auto Detect injected into the core Elementor Video widget instead of replacing it
- Default source setting applied to new widgets
- Localized editor detection messages

### 1.0.0
- Initial release
- Auto source detection
- Support for all major video platforms
- Dynamic content support
- Settings page with About section

## License

GPL v2 or later. See [license file](https://www.gnu.org/licenses/gpl-2.0.html).
