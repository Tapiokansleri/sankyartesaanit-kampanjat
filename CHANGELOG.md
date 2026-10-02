# Changelog

All notable changes to this project are documented here.

## [1.2.0] - 2026-10-02

### Added

- **New default landing page**: a new Kampanja now starts from the Hypnosé Ultra page (video hero, sections with "Varaa henkilökohtainen palveluaika" + "Soita" buttons, certification logos and the booking calendar) instead of the old Factory Sale layout. The page is stored in `templates/landing-default.html`
- Media in the template follows the site it is used on: images, the hero video and logos are looked up by file name in the media library and use that site's attachment IDs and uploads URL, so nothing breaks when the page moves between Local, staging and production. Files that are missing from a media library are hot-linked from sankyartesaanit.fi (filter `kansleri_sa_template_media_fallback_base`) instead of showing broken images
- The Spectra logo strip is left out of the template automatically when Spectra is not active
- WP-CLI: `wp kansleri-sa export-template <post_id>` saves any page as the new default template (media is tokenised for you)
- The old Factory Sale layout is still available as the block pattern "Sänkyartesaanit - Tarjouslaskeutumissivu (Factory Sale)"

### Changed

- The block pattern "Sänkyartesaanit - Laskeutumissivu" now inserts the new default page; patterns are only built in the editor and its REST requests, not on every front-end request
- The editor no longer applies a separate block template to Kampanjat; new posts are filled from the default landing page only (default content and seeding)

### Fixed

- "Reset blocks" and the automatic seeding of empty new campaigns stripped every backslash from the template markup (`wp_update_post()` expects slashed data), which corrupts block attributes that contain JSON escapes
- Hero with a video background: the text was dark navy on the darkened picture in the block editor preview; it is now white, like on the live page

## [1.1.0] - 2026-10-02

### Added

- Hero **video background** option (Hero → Media → "Videotausta"): a video file (mp4 / webm) from the media library plays silently and loops behind the hero text. Includes an adjustable darkening overlay, a poster image fallback, a pause / play button, pausing while the hero is off screen, and a still image for visitors who prefer reduced motion
- Hero **details row**: the bonus list and trust points now sit in a calm row under the headline and media instead of squeezing the text column
- A single product card is now centered instead of sitting in the left half
- **Booking calendar block** ("Sänkyartesaanit - Ajanvarauskalenteri"): embeds the Easybook calendar (https iframe, adjustable height) with a heading, a call button and the anchor `#varaa-aika`; in the editor preview it shows a placeholder instead of loading the calendar
- **CTA row for every section block** (Toimintakutsu in the sidebar): a primary button (e.g. "Varaa henkilökohtainen palveluaika" linking to `#varaa-aika`) and a secondary one (e.g. "Soita"), shown at the end of the section; works on section header, feature columns, split content, product cards, numbered features, stat cards, testimonials, location section and FAQ

### Changed

- Visual refinement of all blocks: consistent section spacing, balanced headings, calmer hero layout, readable type sizes on mobile, brand-gold list bullets, soft card shadows. Fonts, colours and button styles are unchanged
- Split-content eyebrow no longer inherits paragraph spacing

[1.2.0]: https://github.com/Tapiokansleri/sankyartesaanit-kampanjat/releases/tag/v1.2.0
[1.1.0]: https://github.com/Tapiokansleri/sankyartesaanit-kampanjat/releases/tag/v1.1.0

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
