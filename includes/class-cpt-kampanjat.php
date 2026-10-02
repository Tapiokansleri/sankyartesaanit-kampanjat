<?php
/**
 * Kampanjat custom post type.
 */

defined( 'ABSPATH' ) || exit;

class Kansleri_SA_CPT_Kampanjat {

	const POST_TYPE = 'kampanja';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_filter( 'block_editor_settings_all', array( __CLASS__, 'set_block_template' ), 10, 2 );
		add_filter( 'default_content', array( __CLASS__, 'default_content' ), 10, 2 );
		add_action( 'wp_insert_post', array( __CLASS__, 'seed_new_post_content' ), 10, 3 );
		add_filter( 'template_include', array( __CLASS__, 'load_single_template' ) );
		add_filter( 'body_class', array( __CLASS__, 'body_class' ) );
		add_filter( 'astra_the_title_enabled', array( __CLASS__, 'hide_astra_title' ) );
		add_filter( 'astra_page_layout', array( __CLASS__, 'astra_full_width_layout' ) );
		add_filter( 'astra_get_content_layout', array( __CLASS__, 'astra_content_layout' ) );
		add_filter( 'astra_apply_container_structure', array( __CLASS__, 'astra_remove_container' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_frontend_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'enqueue_editor_assets' ) );
		add_filter( 'post_row_actions', array( __CLASS__, 'row_actions' ), 10, 2 );
		add_action( 'admin_post_kansleri_sa_reset_kampanja', array( __CLASS__, 'handle_reset_template' ) );
	}

	public static function activate() {
		self::register_post_type();
		flush_rewrite_rules();
	}

	public static function register_post_type() {
		$labels = array(
			'name'                  => __( 'Kampanjat', 'kansleri-sankyartesaanit' ),
			'singular_name'         => __( 'Kampanja', 'kansleri-sankyartesaanit' ),
			'menu_name'             => __( 'Kampanjat', 'kansleri-sankyartesaanit' ),
			'name_admin_bar'        => __( 'Kampanja', 'kansleri-sankyartesaanit' ),
			'add_new'               => __( 'Lisää uusi', 'kansleri-sankyartesaanit' ),
			'add_new_item'          => __( 'Lisää uusi kampanja', 'kansleri-sankyartesaanit' ),
			'new_item'              => __( 'Uusi kampanja', 'kansleri-sankyartesaanit' ),
			'edit_item'             => __( 'Muokkaa kampanjaa', 'kansleri-sankyartesaanit' ),
			'view_item'             => __( 'Näytä kampanja', 'kansleri-sankyartesaanit' ),
			'all_items'             => __( 'Kaikki kampanjat', 'kansleri-sankyartesaanit' ),
			'search_items'          => __( 'Etsi kampanjoita', 'kansleri-sankyartesaanit' ),
			'not_found'             => __( 'Kampanjoita ei löytynyt.', 'kansleri-sankyartesaanit' ),
			'not_found_in_trash'    => __( 'Roskakorissa ei kampanjoita.', 'kansleri-sankyartesaanit' ),
			'item_published'        => __( 'Kampanja julkaistu.', 'kansleri-sankyartesaanit' ),
			'item_updated'          => __( 'Kampanja päivitetty.', 'kansleri-sankyartesaanit' ),
		);

		register_post_type(
			self::POST_TYPE,
			array(
				'labels'              => $labels,
				'public'              => true,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => true,
				'menu_position'       => 25,
				'menu_icon'           => 'dashicons-megaphone',
				'capability_type'     => 'page',
				'map_meta_cap'        => true,
				'hierarchical'        => false,
				'rewrite'             => array(
					'slug'       => 'kampanja',
					'with_front' => false,
				),
				'has_archive'         => false,
				'query_var'           => true,
				'supports'            => array( 'title', 'editor', 'revisions', 'thumbnail', 'custom-fields' ),
			)
		);
	}

	/**
	 * Editor settings for Kampanja posts. New posts are filled by default_content() and seed_new_post_content().
	 *
	 * @param array<string, mixed> $settings Editor settings.
	 * @param WP_Post              $context  Post context.
	 */
	public static function set_block_template( $settings, $context ) {
		if ( empty( $context->post ) || self::POST_TYPE !== $context->post->post_type ) {
			return $settings;
		}

		$settings['alignWide']      = true;
		$settings['supportsLayout'] = true;

		return $settings;
	}

	/**
	 * Default content for new Kampanja posts in the editor.
	 *
	 * @param string  $content Default content.
	 * @param WP_Post $post    Post object.
	 */
	public static function default_content( $content, $post ) {
		if ( self::POST_TYPE === $post->post_type && self::is_empty_content( $content ) ) {
			return Kansleri_SA_Landing_Template::get_content();
		}

		return $content;
	}

	/**
	 * Persist landing blocks when a new Kampanja is first saved.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @param bool    $update  Whether this is an update.
	 */
	public static function seed_new_post_content( $post_id, $post, $update ) {
		if ( $update || self::POST_TYPE !== $post->post_type ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		if ( ! self::is_empty_content( $post->post_content ) ) {
			return;
		}

		remove_action( 'wp_insert_post', array( __CLASS__, 'seed_new_post_content' ), 10 );

		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => wp_slash( Kansleri_SA_Landing_Template::get_content() ),
			)
		);

		add_action( 'wp_insert_post', array( __CLASS__, 'seed_new_post_content' ), 10, 3 );
	}

	public static function load_single_template( $template ) {
		if ( ! is_singular( self::POST_TYPE ) ) {
			return $template;
		}

		$plugin_template = KANSLERI_SA_PLUGIN_DIR . 'templates/single-kampanja.php';

		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}

		return $template;
	}

	/**
	 * @param string[] $classes Body classes.
	 * @return string[]
	 */
	public static function body_class( $classes ) {
		if ( is_singular( self::POST_TYPE ) ) {
			$classes[] = 'kampanja-landing';
			$classes[] = 'kansleri-sa-landing';
		}

		return $classes;
	}

	/**
	 * Hide Astra entry title on Kampanja landing pages.
	 *
	 * @param bool $enabled Whether title is shown.
	 */
	public static function hide_astra_title( $enabled ) {
		if ( is_singular( self::POST_TYPE ) ) {
			return false;
		}

		return $enabled;
	}

	/**
	 * Use full-width layout in Astra for Kampanja pages.
	 *
	 * @param string $layout Astra layout slug.
	 */
	public static function astra_full_width_layout( $layout ) {
		if ( is_singular( self::POST_TYPE ) ) {
			return 'no-sidebar-page';
		}

		return $layout;
	}

	/**
	 * Astra page-builder layout — no content padding/max-width.
	 *
	 * @param string $layout Content layout slug.
	 */
	public static function astra_content_layout( $layout ) {
		if ( is_singular( self::POST_TYPE ) ) {
			return 'page-builder';
		}

		return $layout;
	}

	/**
	 * Remove Astra inner container on Kampanja pages.
	 *
	 * @param bool $apply Whether to apply container markup.
	 */
	public static function astra_remove_container( $apply ) {
		if ( is_singular( self::POST_TYPE ) ) {
			return false;
		}

		return $apply;
	}

	public static function enqueue_frontend_assets() {
		if ( ! is_singular( self::POST_TYPE ) ) {
			return;
		}

		wp_enqueue_style( 'kansleri-sa-frontend' );
	}

	public static function enqueue_editor_assets() {
		global $post;

		if ( ! $post || self::POST_TYPE !== $post->post_type ) {
			return;
		}

		wp_enqueue_style(
			'kansleri-sa-kampanja-editor',
			KANSLERI_SA_PLUGIN_URL . 'assets/kampanja-editor.css',
			array( 'kansleri-sa-editor-ui' ),
			KANSLERI_SA_VERSION
		);
	}

	/**
	 * @param string[] $actions Row actions.
	 * @param WP_Post  $post    Post object.
	 * @return string[]
	 */
	public static function row_actions( $actions, $post ) {
		if ( self::POST_TYPE !== $post->post_type || ! current_user_can( 'edit_post', $post->ID ) ) {
			return $actions;
		}

		$url = wp_nonce_url(
			admin_url( 'admin-post.php?action=kansleri_sa_reset_kampanja&post_id=' . $post->ID ),
			'kansleri_sa_reset_' . $post->ID
		);

		$actions['kansleri_sa_reset'] = sprintf(
			'<a href="%s" onclick="return confirm(\'%s\');">%s</a>',
			esc_url( $url ),
			esc_js( __( 'This replaces all blocks with the default landing page template. Continue?', 'kansleri-sankyartesaanit' ) ),
			esc_html__( 'Reset blocks', 'kansleri-sankyartesaanit' )
		);

		return $actions;
	}

	public static function handle_reset_template() {
		$post_id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0;

		if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( esc_html__( 'You cannot edit this campaign.', 'kansleri-sankyartesaanit' ) );
		}

		check_admin_referer( 'kansleri_sa_reset_' . $post_id );

		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => wp_slash( Kansleri_SA_Landing_Template::get_content() ),
			)
		);

		wp_safe_redirect(
			add_query_arg(
				array(
					'post'   => $post_id,
					'action' => 'edit',
					'kansleri_sa_reset' => '1',
				),
				admin_url( 'post.php' )
			)
		);
		exit;
	}

	/**
	 * @param string $content Post content.
	 */
	private static function is_empty_content( $content ) {
		return '' === trim( (string) $content );
	}
}
