<?php
/**
 * Block patterns for landing pages.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		register_block_pattern_category(
			'sankyartesaanit',
			array(
				'label' => __( 'Sänkyartesaanit', 'kansleri-sankyartesaanit' ),
			)
		);
	}
);

/**
 * Register the landing page patterns.
 *
 * The default page looks its media up in the media library, so the patterns are only built where
 * they can be used: the post editor and the REST requests it makes.
 */
function kansleri_sa_register_patterns() {
	static $registered = false;

	if ( $registered ) {
		return;
	}

	if ( is_admin() && ! in_array( $GLOBALS['pagenow'] ?? '', array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$registered = true;

	register_block_pattern(
		'sankyartesaanit/landing-page',
		array(
			'title'       => __( 'Sänkyartesaanit - Laskeutumissivu (Hypnosé Ultra)', 'kansleri-sankyartesaanit' ),
			'description' => __( 'Oletuspohja uusille kampanjoille: hero-taustavideo, osiot toimintakutsuineen ja ajanvarauskalenteri.', 'kansleri-sankyartesaanit' ),
			'categories'  => array( 'sankyartesaanit' ),
			'content'     => Kansleri_SA_Landing_Template::get_content(),
		)
	);

	register_block_pattern(
		'sankyartesaanit/landing-page-factory-sale',
		array(
			'title'       => __( 'Sänkyartesaanit - Tarjouslaskeutumissivu (Factory Sale)', 'kansleri-sankyartesaanit' ),
			'description' => __( 'Aiempi oletuspohja: koko tarjouslaskeutumissivun rakenne valmiilla lohkoilla.', 'kansleri-sankyartesaanit' ),
			'categories'  => array( 'sankyartesaanit' ),
			'content'     => Kansleri_SA_Landing_Template::get_factory_sale_content(),
		)
	);
}

add_action( 'admin_init', 'kansleri_sa_register_patterns' );
add_action( 'rest_api_init', 'kansleri_sa_register_patterns' );
