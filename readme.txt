=== Kansleri & Sänkyartesaanit ===
Contributors: kansleri
Tags: gutenberg, blocks, landing-page, kampanja
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Kampanjat-laskeutumissivut Sänkyartesaanit-lohkoilla. Luo kampanja → kaikki lohkot latautuvat automaattisesti.

== Description ==

WordPress plugin for Sänkyartesaanit campaign landing pages. Create a Kampanja post and the full landing page block structure is seeded automatically.

* Kampanjat custom post type; new campaigns start from the Hypnosé Ultra landing page (media follows the site's media library)
* 13 Gutenberg blocks (sidebar editing + live server-side preview)
* Font Awesome icon picker on feature columns and stat cards
* Full-width kampanja layout (Astra-compatible)
* Mobile-optimised spacing and typography

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/` or install the zip from GitHub Releases.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **Kampanjat → Lisää uusi** to create a campaign page.

== Changelog ==

= 1.2.0 =
* New default landing page for new campaigns (Hypnosé Ultra layout with video hero, CTA rows, logos and booking calendar); the old Factory Sale layout stays available as a block pattern
* Template media is resolved per site (attachment IDs and uploads URL), with a hot-link fallback so images are never broken
* WP-CLI: wp kansleri-sa export-template <post_id> saves a page as the default template
* Fix: Reset blocks / auto-seeding stripped backslashes from block markup
* Fix: hero video-background text colour in the editor preview

= 1.1.0 =
* Hero video background option: silent looping mp4/webm with overlay, poster fallback, pause button and reduced-motion support
* Hero bonus list and trust points moved into a details row under headline and media
* Booking calendar block (embedded Easybook calendar, anchor #varaa-aika) and an optional CTA row (primary + secondary button) for every section block
* Visual refinement: section spacing, mobile type sizes, list bullets, card shadows (fonts, colours and buttons unchanged)
* A single product card is centered

= 1.0.5 =
* Hero block supports YouTube video (horizontal / vertical orientation)
* Fix FAQ block: answer fields editable in the editor sidebar (debounced preview)

= 1.0.4 =
* Fix split-content video cropping and image/video overlap on the frontend
* Hero image vertical alignment (top / center)
* Hero image shown before text on mobile
* Plugin Check: GPL license, readme.txt, self-hosted Font Awesome, wrapper attr escaping

= 1.0.3 =
* Split-content block supports video (mp4)
* Hero image vertical alignment option

= 1.0.2 =
* AI prompt copy sidebar for kampanja editor

= 1.0.1 =
* Attribute alias normalizer for pasted block markup

= 1.0.0 =
* Initial release
