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
	);

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_assets' ) );
		add_action( 'init', array( __CLASS__, 'register_blocks' ) );
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'enqueue_editor_assets' ) );
		add_filter( 'block_categories_all', array( __CLASS__, 'register_category' ), 10, 2 );
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
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
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

		if ( in_array( $block, array( 'feature-columns', 'stat-cards', 'hero' ), true ) ) {
			wp_enqueue_style( 'kansleri-sa-fontawesome' );
		}

		if ( 'faq' === $block ) {
			wp_enqueue_script( 'kansleri-sa-faq' );
		}

		ob_start();
		include $file;
		return ob_get_clean();
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
				'wp-server-side-render',
				'kansleri-sa-fa-picker',
			),
			KANSLERI_SA_VERSION,
			true
		);
	}
}
