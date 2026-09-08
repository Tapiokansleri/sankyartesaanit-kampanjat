# Changelog

All notable changes to this project are documented here.

## [1.0.5] - 2026-09-08

### Added

- Hero block **YouTube video** media type with horizontal / vertical orientation (Shorts auto-detected)

### Fixed

- **UKK (FAQ)** block: answer RichText fields now editable in the sidebar — debounced local state and preview prevent focus loss on each keystroke

[1.0.5]: https://github.com/Tapiokansleri/sankyartesaanit-kampanjat/releases/tag/v1.0.5

## [1.0.4] - 2026-07-02

### Added

- Hero **image vertical alignment** option (top / center)

### Fixed

- Split-content video no longer cropped; image and video no longer overlap — only the selected media type is shown
- Hero image appears **before text on mobile**
- Attribute normalizer no longer forces video mode when `videoId` is `0`
- Plugin Check: GPL license header, `readme.txt`, self-hosted Font Awesome, escaped block wrapper attributes

[1.0.4]: https://github.com/Tapiokansleri/sankyartesaanit-kampanjat/releases/tag/v1.0.4

## [1.0.3] - 2026-07-01

### Added

- **Jaettu sisältö** block supports video (mp4) alongside images — media library upload, direct URL, optional poster, autoplay/loop/muted
- Attribute normalizer accepts `video`, `videoSrc`, `poster`, and `posterUrl` aliases in pasted markup

[1.0.3]: https://github.com/Tapiokansleri/sankyartesaanit-kampanjat/releases/tag/v1.0.3

## [1.0.2] - 2026-07-01

### Added

- Editor sidebar **AI-prompt** panel with **Kopioi prompt leikepöydälle** — exports all blocks + field instructions for AI tools

[1.0.2]: https://github.com/Tapiokansleri/sankyartesaanit-kampanjat/releases/tag/v1.0.2

## [1.0.1] - 2026-07-01

### Fixed

- Accept alternate block attribute names in pasted markup (`lead`, `priceNow`, `features`, `cards`, `ctaPrimaryLabel`, etc.) without falling back to defaults
- Hero supports `bonusTitle`, `bonusItems`, `scarcityBadge`, and Font Awesome icon strings in feature columns

[1.0.1]: https://github.com/Tapiokansleri/sankyartesaanit-kampanjat/releases/tag/v1.0.1

## [1.0.0] - 2026-07-01

### Added

- Kampanjat custom post type with auto-seeded landing page template
- 12 Sänkyartesaanit Gutenberg blocks (dynamic, sidebar editing)
- Font Awesome icons on feature columns and stat cards (visual picker)
- Hero trust badges, improved price layout
- FAQ accordion with rich-text answers, chevrons, and animation
- Unified mobile padding across all blocks
- GitHub release auto-updates via Plugin Update Checker

[1.0.0]: https://github.com/Tapiokansleri/sankyartesaanit-kampanjat/releases/tag/v1.0.0
