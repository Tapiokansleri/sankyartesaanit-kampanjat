<?php
/**
 * Default landing page block structure for Kampanjat.
 */

defined( 'ABSPATH' ) || exit;

class Kansleri_SA_Landing_Template {

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
	 * Block markup inserted into new Kampanja posts.
	 */
	public static function get_content() {
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

	/**
	 * Editor template row: block name + attrs with align full.
	 *
	 * @param string               $name  Block name.
	 * @param array<string, mixed> $attrs Block attributes.
	 * @return array<int, mixed>
	 */
	private static function template_block( $name, $attrs = array() ) {
		return array( $name, self::with_align_full( $attrs ) );
	}

	/**
	 * Block editor template array for new Kampanja posts.
	 *
	 * @return array<int, array<int, mixed>>
	 */
	public static function get_editor_template() {
		return array(
			self::template_block( 'sankyartesaanit/promo-banner' ),
			self::template_block( 'sankyartesaanit/hero' ),
			self::template_block( 'sankyartesaanit/feature-columns' ),
			self::template_block(
				'sankyartesaanit/split-content',
				array(
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
				)
			),
			self::template_block(
				'sankyartesaanit/section-header',
				array(
					'eyebrow'     => 'TEHTAAN RAJOITETTU ERÄ',
					'title'       => 'Valitse oma sänkysi erikoishintaan',
					'description' => 'Molemmissa sama lupaus: käsityönä valmistettu premium-laatu, ilmainen toimitus ja asennus sekä 180 vuorokauden tyytyväisyystakuu.',
					'alignText'   => 'center',
					'theme'       => 'sand',
				)
			),
			self::template_block( 'sankyartesaanit/product-cards' ),
			self::template_block( 'sankyartesaanit/numbered-features' ),
			self::template_block(
				'sankyartesaanit/section-header',
				array(
					'eyebrow'   => 'OSTAMINEN ILMAN RISKIÄ',
					'title'     => 'Kokeile rauhassa, maksa vasta kun olet tyytyväinen',
					'alignText' => 'center',
					'theme'     => 'warm',
				)
			),
			self::template_block( 'sankyartesaanit/stat-cards' ),
			self::template_block(
				'sankyartesaanit/section-header',
				array(
					'eyebrow' => 'ASIAKKAIDEN KOKEMUKSIA',
					'title'   => 'Nukkujat, jotka vaihtoivat parempaan',
					'theme'   => 'light',
				)
			),
			self::template_block( 'sankyartesaanit/testimonials' ),
			self::template_block( 'sankyartesaanit/location-section' ),
			self::template_block( 'sankyartesaanit/faq' ),
			self::template_block( 'sankyartesaanit/cta-section' ),
		);
	}
}
