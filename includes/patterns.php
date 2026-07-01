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

		register_block_pattern(
			'sankyartesaanit/landing-page',
			array(
				'title'       => __( 'Sänkyartesaanit - Tarjouslaskeutumissivu', 'kansleri-sankyartesaanit' ),
				'description' => __( 'Koko tarjouslaskeutumissivun rakenne valmiilla lohkoilla.', 'kansleri-sankyartesaanit' ),
				'categories'  => array( 'sankyartesaanit' ),
				'content'     => Kansleri_SA_Landing_Template::get_content(),
			)
		);
	}
);
