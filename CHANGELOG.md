# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.1] - 2026-08-30

### Changed

- WordPress.org compatibility: text domain matches plugin slug `extend-video-addons`
- Auto Detect is injected into Elementor's Video widget instead of unregistering and replacing it
- `Tested up to` updated to WordPress 7.1
- Default video source setting is applied to new Video widgets
- Editor detection messages are localized

### Fixed

- Auto Detect no longer depends on private Elementor properties (fatal/dynamic-property bug)
- Editor JS no longer switches Source away from Auto Detect (which hid the URL field)

## [1.0.0] - 2025-03-02

### Added

- Auto source detection for Elementor video widget
- "Auto Detect" option in Video widget Source dropdown
- Support for YouTube, Vimeo, Dailymotion, VideoPress, and self-hosted videos
- Dynamic content support (ACF, post meta, dynamic tags)
- Direct render for auto-detected self-hosted videos
- Settings page (Settings → Extend Video Add-ons)
- Default video source setting
- Compatibility status and About the Author on settings page
- WordPress.org–style readme.txt and documentation

[1.0.1]: https://github.com/shahadul878/extend-video-addons/releases/tag/v1.0.1
[1.0.0]: https://github.com/shahadul878/extend-video-addons/releases/tag/v1.0.0
