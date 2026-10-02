<?php
/**
 * Block registration and assets.
 */

defined( 'ABSPATH' ) || exit;

class Kansleri_Sankyartesaanit_Blocks {

	const BLOCKS = array(
		'promo-banner',
		'section-header',
		'hero',
		'feature-columns',
		'split-content',
		'product-cards',
		'numbered-features',
		'stat-cards',
		'testimonials',
		'location-section',
		'faq',
		'cta-section',
		'booking-embed',
	);

	/**
	 * Section blocks that can carry an optional CTA row (primary + secondary button).
	 */
	const CTA_BLOCKS = array(
		'section-header',
		'feature-columns',
		'split-content',
		'product-cards',
		'numbered-features',
		'stat-cards',
		'testimonials',
		'location-section',
		'faq',
	);

	/**
	 * CTA blocks that sit on a dark background (secondary button gets the ghost style).
	 */
	const DARK_BLOCKS = array(
		'numbered-features',
	);

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_assets' ) );
		add_action( 'init', array( __CLASS__, 'register_blocks' ) );
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'enqueue_editor_assets' ) );
		add_filter( 'block_categories_all', array( __CLASS__, 'register_category' ), 10, 2 );
		add_filter( 'register_block_type_args', array( __CLASS__, 'add_cta_attributes' ), 10, 2 );
	}

	/**
	 * Give every section block the optional CTA row attributes.
	 *
	 * @param array  $args       Block type arguments.
	 * @param string $block_type Block type name.
	 * @return array
	 */
	public static function add_cta_attributes( $args, $block_type ) {
		if ( 0 !== strpos( (string) $block_type, 'sankyartesaanit/' ) ) {
			return $args;
		}

		if ( ! in_array( substr( $block_type, strlen( 'sankyartesaanit/' ) ), self::CTA_BLOCKS, true ) ) {
			return $args;
		}

		$args['attributes'] = array_merge(
			isset( $args['attributes'] ) && is_array( $args['attributes'] ) ? $args['attributes'] : array(),
			array(
				'ctaText'          => array(
					'type'    => 'string',
					'default' => '',
				),
				'ctaUrl'           => array(
					'type'    => 'string',
					'default' => '#varaa-aika',
				),
				'ctaSecondaryText' => array(
					'type'    => 'string',
					'default' => '',
				),
				'ctaSecondaryUrl'  => array(
					'type'    => 'string',
					'default' => 'tel:025464065',
				),
			)
		);

		return $args;
	}

	/**
	 * Add the CTA row to a rendered section block (inside the section, so it shares its background).
	 *
	 * @param string $block      Block slug.
	 * @param array  $attributes Block attributes.
	 * @param string $html       Rendered block HTML.
	 * @return string
	 */
	public static function maybe_append_cta( $block, $attributes, $html ) {
		if ( ! in_array( $block, self::CTA_BLOCKS, true ) || empty( $attributes['ctaText'] ) ) {
			return $html;
		}

		// Split content: the CTA row belongs at the end of the text column, not under the image.
		if ( 'split-content' === $block ) {
			$row   = kansleri_sa_get_cta_row_html( $attributes, 'inline' );
			$media = strpos( $html, '<div class="sa-split-content__media">' );

			if ( false !== $media ) {
				$end = strrpos( substr( $html, 0, $media ), '</div>' );

				if ( false !== $end ) {
					return substr( $html, 0, $end ) . $row . substr( $html, $end );
				}
			}
		}

		$row = kansleri_sa_get_cta_row_html( $attributes, 'section', in_array( $block, self::DARK_BLOCKS, true ) );
		$end = strrpos( $html, '</section>' );

		if ( false === $end ) {
			return $html . $row;
		}

		return substr( $html, 0, $end ) . $row . substr( $html, $end );
	}

	public static function register_assets() {
		wp_register_style(
			'kansleri-sa-frontend',
			KANSLERI_SA_PLUGIN_URL . 'assets/frontend.css',
			array(),
			KANSLERI_SA_VERSION
		);

		wp_register_style(
			'kansleri-sa-fontawesome',
			KANSLERI_SA_PLUGIN_URL . 'assets/fontawesome/css/all.min.css',
			array(),
			'6.5.1'
		);

		wp_register_script(
			'kansleri-sa-faq',
			KANSLERI_SA_PLUGIN_URL . 'assets/faq.js',
			array(),
			KANSLERI_SA_VERSION,
			true
		);

		wp_register_script(
			'kansleri-sa-hero-video',
			KANSLERI_SA_PLUGIN_URL . 'assets/hero-video.js',
			array(),
			KANSLERI_SA_VERSION,
			true
		);
	}

	public static function register_category( $categories ) {
		array_unshift(
			$categories,
			array(
				'slug'  => 'sankyartesaanit',
				'title' => __( 'Sänkyartesaanit', 'kansleri-sankyartesaanit' ),
				'icon'  => 'bed',
			)
		);
		return $categories;
	}

	public static function register_blocks() {
		foreach ( self::BLOCKS as $block ) {
			$dir = KANSLERI_SA_PLUGIN_DIR . 'blocks/' . $block;
			if ( ! file_exists( $dir . '/block.json' ) ) {
				continue;
			}

			register_block_type(
				$dir,
				array(
					'render_callback' => function ( $attributes, $content, $block_instance ) use ( $block ) {
						return self::render_block( $block, $attributes, $content, $block_instance );
					},
				)
			);
		}
	}

	public static function render_block( $block, $attributes, $content, $block_instance ) {
		$file = KANSLERI_SA_PLUGIN_DIR . 'blocks/' . $block . '/render.php';
		if ( ! file_exists( $file ) ) {
			return '';
		}

		$attributes = Kansleri_SA_Attribute_Normalizer::normalize( $block, $attributes );

		if ( in_array( $block, array( 'feature-columns', 'stat-cards', 'hero' ), true ) ) {
			wp_enqueue_style( 'kansleri-sa-fontawesome' );
		}

		if ( 'faq' === $block && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			wp_enqueue_script( 'kansleri-sa-faq' );
		}

		if ( 'hero' === $block && ! empty( $attributes['videoBackground'] ) && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			wp_enqueue_script( 'kansleri-sa-hero-video' );
		}

		ob_start();
		include $file;
		$html = ob_get_clean();

		return self::maybe_append_cta( $block, $attributes, $html );
	}

	public static function get_fa_icons() {
		$file = KANSLERI_SA_PLUGIN_DIR . 'assets/fa-icons.json';
		if ( ! file_exists( $file ) ) {
			return array();
		}

		$icons = json_decode( file_get_contents( $file ), true );

		return is_array( $icons ) ? $icons : array();
	}

	public static function enqueue_editor_assets() {
		wp_enqueue_style( 'kansleri-sa-fontawesome' );

		wp_enqueue_style(
			'kansleri-sa-fa-picker',
			KANSLERI_SA_PLUGIN_URL . 'assets/fa-icon-picker.css',
			array(),
			KANSLERI_SA_VERSION
		);

		wp_enqueue_script(
			'kansleri-sa-fa-picker',
			KANSLERI_SA_PLUGIN_URL . 'assets/fa-icon-picker.js',
			array(
				'wp-element',
				'wp-components',
				'wp-i18n',
			),
			KANSLERI_SA_VERSION,
			true
		);

		wp_localize_script(
			'kansleri-sa-fa-picker',
			'kansleriSAFaIcons',
			self::get_fa_icons()
		);

		wp_enqueue_style(
			'kansleri-sa-editor',
			KANSLERI_SA_PLUGIN_URL . 'assets/frontend.css',
			array(),
			KANSLERI_SA_VERSION
		);

		wp_enqueue_style(
			'kansleri-sa-editor-ui',
			KANSLERI_SA_PLUGIN_URL . 'assets/editor.css',
			array( 'kansleri-sa-editor' ),
			KANSLERI_SA_VERSION
		);

		wp_enqueue_script(
			'kansleri-sa-editor',
			KANSLERI_SA_PLUGIN_URL . 'assets/editor.js',
			array(
				'wp-blocks',
				'wp-element',
				'wp-components',
				'wp-block-editor',
				'wp-i18n',
				'wp-hooks',
				'wp-compose',
				'wp-server-side-render',
				'kansleri-sa-fa-picker',
			),
			KANSLERI_SA_VERSION,
			true
		);

		self::enqueue_prompt_export();
	}

	/**
	 * Sidebar tool: copy AI prompt for kampanja content.
	 */
	public static function enqueue_prompt_export() {
		global $post;

		if ( ! $post || Kansleri_SA_CPT_Kampanjat::POST_TYPE !== $post->post_type ) {
			return;
		}

		wp_enqueue_script(
			'kansleri-sa-prompt-export',
			KANSLERI_SA_PLUGIN_URL . 'assets/prompt-export.js',
			array(
				'wp-plugins',
				'wp-edit-post',
				'wp-element',
				'wp-components',
				'wp-data',
				'wp-blocks',
				'wp-i18n',
			),
			KANSLERI_SA_VERSION,
			true
		);

		wp_localize_script(
			'kansleri-sa-prompt-export',
			'kansleriSAPromptSchema',
			Kansleri_SA_Prompt_Schema::get()
		);
	}
}
