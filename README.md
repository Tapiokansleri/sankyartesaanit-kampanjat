# Kansleri & Sänkyartesaanit

WordPress plugin for **Sänkyartesaanit** campaign landing pages. Create a **Kampanja** post and the full landing page block structure is seeded automatically.

Repository: [github.com/Tapiokansleri/sankyartesaanit-kampanjat](https://github.com/Tapiokansleri/sankyartesaanit-kampanjat)

## Features

- **Kampanjat** custom post type; every new campaign starts from the default landing page (Hypnosé Ultra layout, see below)
- **13 Gutenberg blocks** (sidebar editing + live server-side preview):
  - Promo banner, Hero, Feature columns, Split content, Section header
  - Product cards, Numbered features, Stat cards, Testimonials
  - Location section, FAQ, CTA section
- Font Awesome icon picker on feature columns and stat cards
- **Booking calendar** block (embedded Easybook calendar) and an optional **CTA row** on every section block (e.g. "Varaa henkilökohtainen palveluaika" + "Soita")
- Hero with image, YouTube video or a **silent looping video background** (mp4 / webm, overlay, pause button, reduced-motion fallback)
- Full-width kampanja layout (Astra-compatible)
- Mobile-optimised spacing and typography

## Requirements

- WordPress 6.0+
- PHP 7.4+
- Block editor (Gutenberg)

## Installation

### Manual

1. Download `kansleri-sankyartesaanit.zip` from the [latest release](https://github.com/Tapiokansleri/sankyartesaanit-kampanjat/releases).
2. In WordPress admin go to **Plugins → Add New → Upload Plugin**.
3. Upload the zip and activate **Kansleri & Sänkyartesaanit**.

### From Git

```bash
git clone https://github.com/Tapiokansleri/sankyartesaanit-kampanjat.git kansleri-sankyartesaanit
```

Copy the folder into `wp-content/plugins/` and activate.

## Usage

1. Activate the plugin.
2. Go to **Kampanjat → Lisää uusi**. The page opens pre-filled with the default landing page: video hero, sections with booking / call buttons, certification logos and the booking calendar.
3. Edit blocks from the sidebar — content is not edited inline in the canvas.
4. Publish the kampanja page.

The previous Factory Sale layout is still available as the block pattern **Sänkyartesaanit - Tarjouslaskeutumissivu (Factory Sale)**.

### Default landing page template

The default page is ordinary block markup in `templates/landing-default.html`. Media is stored as tokens so the same file works on every site:

| Token | Becomes |
|-------|---------|
| `{{uploads}}/2026/09/file.webp` | the uploads URL of the current site |
| `{{id:2026/09/file.webp}}` | the attachment ID of that file in the current media library |
| `{{home}}` | the home URL of the current site |

Files that are not in the current media library are hot-linked from sankyartesaanit.fi, so a new page never shows broken images. Blocks of the Spectra plugin (the logo strip) are dropped from the template when Spectra is not active.

To make another page the default, build it on the site where its media lives and run:

```bash
wp kansleri-sa export-template <post_id>
```

This rewrites `templates/landing-default.html` in the active plugin folder (use `--output=<file>` to write straight into a repository clone, `--dry-run` to preview). Commit the file and release as usual.

## Updates

The plugin checks [GitHub Releases](https://github.com/Tapiokansleri/sankyartesaanit-kampanjat/releases) automatically. When a new version is published, WordPress shows an update notice under **Plugins** (same as plugins from WordPress.org).

### Releasing a new version (maintainers)

1. Bump the version in `kansleri-sankyartesaanit.php` (`Version:` header and `KANSLERI_SA_VERSION`).
2. Update `CHANGELOG.md`.
3. Commit and push to `main`.
4. Create and push a tag:

```bash
git tag v1.1.0
git push origin v1.1.0
```

GitHub Actions builds `kansleri-sankyartesaanit.zip` and attaches it to the release. Installed sites pick up the update within ~12 hours (WordPress update cache).

## Development

Plugin slug: `kansleri-sankyartesaanit`

Key paths:

| Path | Purpose |
|------|---------|
| `blocks/` | Block `block.json` + `render.php` |
| `assets/` | CSS, JS, icons |
| `includes/` | CPT, blocks registration, helpers |
| `templates/` | Single kampanja template and the default landing page (`landing-default.html`) |

## License

GPL v2 or later. See [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html).
