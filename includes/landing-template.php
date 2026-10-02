<?php
/**
 * Default landing page for Kampanjat.
 *
 * The page lives in templates/landing-default.html as ordinary block markup. Media is stored as
 * tokens so the same file works on every site (Local, staging, production):
 *
 *   {{uploads}}/2026/09/file.webp   uploads URL of the current site
 *   {{id:2026/09/file.webp}}        attachment ID of that file in the current media library
 *   {{home}}                        home URL of the current site
 *
 * Files that are missing from the current media library are hot-linked from the live site
 * instead, so a new page never shows broken images. Update the template with:
 * wp kansleri-sa export-template <post_id>
 */

defined( 'ABSPATH' ) || exit;

class Kansleri_SA_Landing_Template {

	/**
	 * Template markup, relative to the plugin directory.
	 */
	const TEMPLATE_FILE = 'templates/landing-default.html';

	/**
	 * Uploads URL media is hot-linked from when the current media library does not have the file.
	 */
	const MEDIA_FALLBACK_BASE = 'https://sankyartesaanit.fi/wp-content/uploads';

	/**
	 * Characters allowed in an uploads-relative path inside a token.
	 */
	const REL_CHARS = '[A-Za-z0-9_\-.\/]+';

	/**
	 * @var string|null
	 */
	private static $content = null;

	/**
	 * Block markup inserted into new Kampanja posts, with media resolved for this site.
	 */
	public static function get_content() {
		if ( null === self::$content ) {
			$markup        = self::read_template_file();
			self::$content = '' !== $markup ? self::resolve( $markup ) : self::get_factory_sale_content();
		}

		return self::$content;
	}

	/**
	 * Replace the media tokens of a template and drop sections whose blocks are not installed.
	 *
	 * @param string $markup Template markup.
	 */
	public static function resolve( $markup ) {
		$rels = array();

		if ( preg_match_all( '~\{\{(?:id:|uploads\}\}/)(' . self::REL_CHARS . ')~', $markup, $found ) ) {
			foreach ( $found[1] as $rel ) {
				$rels[ self::base_path( $rel ) ] = true;
			}
		}

		$ids      = self::find_attachment_ids( array_keys( $rels ) );
		$uploads  = untrailingslashit( wp_get_upload_dir()['baseurl'] );
		$home     = untrailingslashit( home_url() );
		$fallback = untrailingslashit( apply_filters( 'kansleri_sa_template_media_fallback_base', self::MEDIA_FALLBACK_BASE ) );

		$markup = preg_replace_callback(
			'~\{\{id:(' . self::REL_CHARS . ')\}\}~',
			function ( $m ) use ( $ids ) {
				return (string) ( $ids[ self::base_path( $m[1] ) ] ?? 0 );
			},
			$markup
		);

		$markup = preg_replace_callback(
			'~\{\{uploads\}\}/(' . self::REL_CHARS . ')~',
			function ( $m ) use ( $ids, $uploads, $fallback ) {
				$base = isset( $ids[ self::base_path( $m[1] ) ] ) ? $uploads : $fallback;
				return $base . '/' . $m[1];
			},
			$markup
		);

		$markup = str_replace( '{{home}}', $home, $markup );

		return self::drop_unavailable_blocks( $markup );
	}

	/**
	 * Replace this site's media URLs, attachment IDs and home URL with tokens.
	 *
	 * @param string $content Block markup of an existing page.
	 * @return array{markup: string, media: string[], warnings: string[]}
	 */
	public static function tokenize( $content ) {
		$media    = array();
		$warnings = array();
		$bases    = array_unique(
			array(
				untrailingslashit( wp_get_upload_dir()['baseurl'] ),
				untrailingslashit( self::MEDIA_FALLBACK_BASE ),
			)
		);

		foreach ( $bases as $base ) {
			$pattern = '~https?:' . preg_quote( preg_replace( '~^https?:~', '', $base ), '~' ) . '/(' . self::REL_CHARS . ')~';
			$content = preg_replace_callback(
				$pattern,
				function ( $m ) use ( &$media ) {
					$media[ $m[1] ] = true;
					return '{{uploads}}/' . $m[1];
				},
				$content
			);
		}

		$bases_rel = array();
		foreach ( array_keys( $media ) as $rel ) {
			$bases_rel[ self::base_path( $rel ) ] = true;
		}

		$ids   = self::find_attachment_ids( array_keys( $bases_rel ) );
		$by_id = array_flip( $ids );

		foreach ( $ids as $rel => $id ) {
			$token   = '{{id:' . $rel . '}}';
			$content = preg_replace( '~("(?:imageId|videoBgId|videoId|id)"\s*:\s*)' . $id . '(?![0-9])~', '${1}' . $token, $content );
			$content = preg_replace( '~(wp-image-)' . $id . '(?![0-9])~', '${1}' . $token, $content );
		}

		$content = preg_replace_callback(
			'~("mediaIDs"\s*:\s*\[)([0-9,\s]*)(\])~',
			function ( $m ) use ( $by_id ) {
				$list = array_map(
					function ( $id ) use ( $by_id ) {
						$id = (int) trim( $id );
						return isset( $by_id[ $id ] ) ? '{{id:' . $by_id[ $id ] . '}}' : (string) $id;
					},
					explode( ',', $m[2] )
				);
				return $m[1] . implode( ',', $list ) . $m[3];
			},
			$content
		);

		foreach ( array( 'http', 'https' ) as $scheme ) {
			$content = str_replace( untrailingslashit( set_url_scheme( home_url(), $scheme ) ), '{{home}}', $content );
		}

		foreach ( $ids as $rel => $id ) {
			if ( preg_match( '~(?<![0-9])' . $id . '(?![0-9])~', $content ) ) {
				$warnings[] = sprintf( 'ID %d (%s) still appears outside the known attributes.', $id, $rel );
			}
		}

		foreach ( array_keys( $bases_rel ) as $rel ) {
			if ( ! isset( $ids[ $rel ] ) ) {
				$warnings[] = sprintf( '%s is not in this site\'s media library; it is referenced by URL only.', $rel );
			}
		}

		if ( preg_match( '~/wp-content/uploads/~', $content ) ) {
			$warnings[] = 'Some upload URLs point to another host and were left as they are.';
		}

		return array(
			'markup'   => $content,
			'media'    => array_keys( $media ),
			'warnings' => $warnings,
		);
	}

	/**
	 * Attachment IDs of uploads-relative paths in the current media library.
	 *
	 * @param string[] $rels Paths like 2026/09/file.webp.
	 * @return array<string, int>
	 */
	public static function find_attachment_ids( $rels ) {
		if ( empty( $rels ) ) {
			return array();
		}

		$attachments = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'any',
				'posts_per_page' => count( $rels ) * 2,
				'orderby'        => 'ID',
				'order'          => 'ASC',
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					array(
						'key'     => '_wp_attached_file',
						'value'   => array_values( $rels ),
						'compare' => 'IN',
					),
				),
			)
		);

		$ids = array();
		foreach ( $attachments as $attachment_id ) {
			$file = (string) get_post_meta( $attachment_id, '_wp_attached_file', true );
			if ( '' !== $file && ! isset( $ids[ $file ] ) ) {
				$ids[ $file ] = (int) $attachment_id;
			}
		}

		return $ids;
	}

	/**
	 * Path of the original upload for a resized variant (name-150x100.avif -> name.avif).
	 *
	 * @param string $rel Uploads-relative path.
	 */
	private static function base_path( $rel ) {
		return preg_replace( '~-\d+x\d+(?=\.[A-Za-z0-9]+$)~', '', $rel );
	}

	/**
	 * Remove top-level sections that need a plugin which is not active (e.g. the Spectra logo
	 * strip), so the editor shows no "block not supported" warnings. Blocks of unknown
	 * plugins are kept: static blocks live in JavaScript only, so PHP cannot tell if they exist.
	 *
	 * @param string $markup Block markup.
	 */
	private static function drop_unavailable_blocks( $markup ) {
		$blocks = parse_blocks( $markup );
		$kept   = array_values(
			array_filter(
				$blocks,
				function ( $block ) {
					return ! self::needs_missing_plugin( $block );
				}
			)
		);

		if ( count( $kept ) === count( $blocks ) ) {
			return $markup;
		}

		return trim( serialize_blocks( $kept ) );
	}

	/**
	 * @param array<string, mixed> $block Parsed block.
	 */
	private static function needs_missing_plugin( $block ) {
		/**
		 * Block name prefixes of third-party blocks used in the template, and whether their plugin is active.
		 *
		 * @param array<string, bool> $requirements Prefix => plugin is active.
		 */
		$requirements = apply_filters(
			'kansleri_sa_template_block_requirements',
			array(
				'uagb/' => defined( 'UAGB_VER' ), // Spectra (Ultimate Addons for Gutenberg).
			)
		);

		foreach ( $requirements as $prefix => $active ) {
			if ( ! $active && ! empty( $block['blockName'] ) && 0 === strpos( $block['blockName'], $prefix ) ) {
				return true;
			}
		}

		foreach ( $block['innerBlocks'] as $inner ) {
			if ( self::needs_missing_plugin( $inner ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return string Template markup or an empty string when the file is missing.
	 */
	private static function read_template_file() {
		$file = KANSLERI_SA_PLUGIN_DIR . self::TEMPLATE_FILE;

		if ( ! is_readable( $file ) ) {
			return '';
		}

		$markup = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		return is_string( $markup ) ? trim( str_replace( "\r\n", "\n", $markup ) ) : '';
	}

	/**
	 * Merge align full into block attributes.
	 *
	 * @param array<string, mixed> $attrs Block attributes.
	 * @return array<string, mixed>
	 */
	private static function with_align_full( $attrs = array() ) {
		if ( ! isset( $attrs['align'] ) ) {
			$attrs['align'] = 'full';
		}

		return $attrs;
	}

	/**
	 * Build a block comment attribute JSON string.
	 *
	 * @param array<string, mixed> $attrs Block attributes.
	 */
	private static function attrs_json( $attrs = array() ) {
		return wp_json_encode( self::with_align_full( $attrs ) );
	}

	/**
	 * The original Factory Sale layout: every block with its own default content.
	 */
	public static function get_factory_sale_content() {
		return '<!-- wp:sankyartesaanit/promo-banner ' . self::attrs_json() . ' /-->' . "\n\n"
			. '<!-- wp:sankyartesaanit/hero ' . self::attrs_json() . ' /-->' . "\n\n"
			. '<!-- wp:sankyartesaanit/feature-columns ' . self::attrs_json() . ' /-->' . "\n\n"
			. '<!-- wp:sankyartesaanit/split-content ' . self::attrs_json( array(
				'eyebrow'    => 'TUNNISTATKO OIREET?',
				'title'      => 'Heräätkö aamuisin niska jumissa tai selkä kipeänä?',
				'paragraphs' => array(
					'Katkonaiset yöt ja jäykkä olo aamuisin johtuvat usein yhdestä asiasta: väärästä tai kuluneesta sängystä.',
					'Kun sänky tukee kehoa oikein, uni syvenee ja palautuminen paranee yö toisensa jälkeen.',
				),
				'listItems'  => array(
					'Uni katkeaa öisin etkä nuku sikeästi',
					'Niska tai selkä on aamuisin kipeä ja jäykkä',
					'Sänky on painunut kuopalle keskeltä',
					'Heräät väsyneenä, vaikka nukut tarpeeksi tunteja',
				),
			) ) . ' /-->' . "\n\n"
			. '<!-- wp:sankyartesaanit/section-header ' . self::attrs_json( array(
				'eyebrow'     => 'TEHTAAN RAJOITETTU ERÄ',
				'title'       => 'Valitse oma sänkysi erikoishintaan',
				'description' => 'Molemmissa sama lupaus: käsityönä valmistettu premium-laatu, ilmainen toimitus ja asennus sekä 180 vuorokauden tyytyväisyystakuu.',
				'alignText'   => 'center',
				'theme'       => 'sand',
			) ) . ' /-->' . "\n\n"
			. '<!-- wp:sankyartesaanit/product-cards ' . self::attrs_json() . ' /-->' . "\n\n"
			. '<!-- wp:sankyartesaanit/numbered-features ' . self::attrs_json() . ' /-->' . "\n\n"
			. '<!-- wp:sankyartesaanit/section-header ' . self::attrs_json( array(
				'eyebrow'   => 'OSTAMINEN ILMAN RISKIÄ',
				'title'     => 'Kokeile rauhassa, maksa vasta kun olet tyytyväinen',
				'alignText' => 'center',
				'theme'     => 'warm',
			) ) . ' /-->' . "\n\n"
			. '<!-- wp:sankyartesaanit/stat-cards ' . self::attrs_json() . ' /-->' . "\n\n"
			. '<!-- wp:sankyartesaanit/section-header ' . self::attrs_json( array(
				'eyebrow' => 'ASIAKKAIDEN KOKEMUKSIA',
				'title'   => 'Nukkujat, jotka vaihtoivat parempaan',
				'theme'   => 'light',
			) ) . ' /-->' . "\n\n"
			. '<!-- wp:sankyartesaanit/testimonials ' . self::attrs_json() . ' /-->' . "\n\n"
			. '<!-- wp:sankyartesaanit/location-section ' . self::attrs_json() . ' /-->' . "\n\n"
			. '<!-- wp:sankyartesaanit/faq ' . self::attrs_json() . ' /-->' . "\n\n"
			. '<!-- wp:sankyartesaanit/cta-section ' . self::attrs_json() . ' /-->';
	}
}
