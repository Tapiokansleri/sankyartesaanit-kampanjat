# Kansleri & Sänkyartesaanit

WordPress plugin for **Sänkyartesaanit** campaign landing pages. Create a **Kampanja** post and the full landing page block structure is seeded automatically.

Repository: [github.com/Tapiokansleri/sankyartesaanit-kampanjat](https://github.com/Tapiokansleri/sankyartesaanit-kampanjat)

## Features

- **Kampanjat** custom post type with auto-seeded landing template
- **12 Gutenberg blocks** (sidebar editing + live server-side preview):
  - Promo banner, Hero, Feature columns, Split content, Section header
  - Product cards, Numbered features, Stat cards, Testimonials
  - Location section, FAQ, CTA section
- Font Awesome icon picker on feature columns and stat cards
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
2. Go to **Kampanjat → Lisää uusi**.
3. Edit blocks from the sidebar — content is not edited inline in the canvas.
4. Publish the kampanja page.

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
| `templates/` | Single kampanja template |

## License

GPL v2 or later. See [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html).
