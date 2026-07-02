<?php
/**
 * Block field documentation for AI prompt export.
 */

defined( 'ABSPATH' ) || exit;

class Kansleri_SA_Prompt_Schema {

	/**
	 * @return array<string, mixed>
	 */
	public static function get() {
		return array(
			'instructions' => self::get_instructions(),
			'blocks'       => self::get_block_docs(),
			'aliases'      => self::get_alias_notes(),
		);
	}

	/**
	 * @return string
	 */
	private static function get_instructions() {
		return implode(
			"\n",
			array(
				'Tehtäväsi on tuottaa uusi sisältö tälle Sänkyartesaanit-kampanjasivulle.',
				'Palauta VAIN WordPress Gutenberg -lohkokommentit (<!-- wp:... -->), ei selityksiä ennen tai jälkeen.',
				'Jokaisessa lohkossa käytä "align":"full".',
				'Säilytä sivun lohkojen järjestys ja määrä — päivitä vain tekstit, hinnat, kuvat ja linkit.',
				'Kirjoita suomeksi. Säilytä kampanjan sävy: luotettava, premium, selkeä.',
				'Kuvat: käytä imageUrl (täysi URL) ja imageAlt. imageId voi olla 0 jos URL on annettu.',
				'Font Awesome -ikonit: muodossa "fa-solid fa-nimi" TAI erikseen icon + iconStyle (esim. gem, solid).',
				'FAQ-vastauksissa voi käyttää HTML:ää (<p>, <strong>, <ul><li>).',
				'Puhelinlinkit: tel:025464065. Sisäiset linkit: /ajanvaraus/',
			)
		);
	}

	/**
	 * @return array<string, string>
	 */
	private static function get_alias_notes() {
		return array(
			'hero'              => 'Voit käyttää joko canonical- tai alias-kenttiä: lead=description, priceNow=price, priceOld=comparePrice, priceBadge=savingsText, priceNote=sizes, ctaPrimaryLabel/Link, ctaSecondaryLabel/Link, microtrust=trustItems.',
			'numbered-features' => 'features (text) = items (description).',
			'stat-cards'        => 'cards (stat) = stats (value).',
			'cta-section'       => 'ctaPrimaryLabel/Link = primaryButtonText/Url, ctaSecondaryLabel/Link = secondaryButtonText/Url.',
		);
	}

	/**
	 * @return array<string, array<string, mixed>>
	 */
	private static function get_block_docs() {
		return array(
			'sankyartesaanit/promo-banner'      => array(
				'title'  => 'Promo Banner — yläpalkki',
				'fields' => array(
					'text' => 'Ilmoitusteksti (string)',
				),
			),
			'sankyartesaanit/hero'              => array(
				'title'  => 'Hero — pääosio kuvalla',
				'fields' => array(
					'eyebrow'             => 'Yläotsikko (string)',
					'title'               => 'Pääotsikko h1 (string)',
					'description / lead'  => 'Ingressi (string)',
					'price / priceNow'    => 'Nykyhinta (string, esim. "5 950 €")',
					'comparePrice / priceOld' => 'Vertailuhinta yliviivattuna (string)',
					'savingsText / priceBadge' => 'Säästöteksti (string, esim. "Säästä 9 000 €")',
					'sizes / priceNote'   => 'Koot / lisätiedot hinnasta (string)',
					'bonusTitle'          => 'Bonuksen otsikko (string, valinnainen)',
					'bonusItems'          => 'Bonukset listana (string[])',
					'primaryButtonText / ctaPrimaryLabel' => 'Ensisijainen painike (string)',
					'primaryButtonUrl / ctaPrimaryLink'   => 'Ensisijainen linkki (string)',
					'secondaryButtonText / ctaSecondaryLabel' => 'Toissijainen painike (string)',
					'secondaryButtonUrl / ctaSecondaryLink'   => 'Toissijainen linkki (string)',
					'trustItems / microtrust' => 'Luottamusmerkit (string[], 4 kpl)',
					'scarcityBadge'       => 'Kiireellisyysmerkintä kuvalla (string, valinnainen)',
					'imageUrl, imageAlt, imageId' => 'Hero-kuva',
					'imagePosition'       => '"right" | "left"',
				),
			),
			'sankyartesaanit/feature-columns'   => array(
				'title'  => 'Ominaisuussarakkeet — 3 saraketta',
				'fields' => array(
					'showIcons' => 'true | false',
					'theme'     => '"cream" | "warm"',
					'columns[]' => 'Sarakkeet: title, description, icon (esim. fa-solid fa-heart tai gem), iconStyle (solid)',
				),
			),
			'sankyartesaanit/split-content'     => array(
				'title'  => 'Jaettu sisältö — teksti + kuva',
				'fields' => array(
					'eyebrow'       => 'Yläotsikko (string)',
					'title'         => 'Otsikko (string)',
					'paragraphs[]'  => 'Kappaleet (string[])',
					'listItems[]'   => 'Luettelokohdat (string[])',
					'imageUrl, imageAlt, imageId' => 'Kuva',
					'imagePosition' => '"right" | "left"',
				),
			),
			'sankyartesaanit/section-header'    => array(
				'title'  => 'Osio-otsikko',
				'fields' => array(
					'eyebrow'     => 'Yläotsikko (string)',
					'title'       => 'Otsikko (string)',
					'description' => 'Kuvaus (string, valinnainen)',
					'alignText'   => '"left" | "center"',
					'theme'       => '"light" | "warm" | "cream" | "sand" | "dark"',
				),
			),
			'sankyartesaanit/product-cards'     => array(
				'title'  => 'Tuotekortit',
				'fields' => array(
					'theme'       => '"sand"',
					'products[]'  => 'Tuotteet: imageUrl, imageAlt, eyebrow, title, price, comparePrice, priceNote, features[] (string[]), buttonText, buttonUrl',
				),
			),
			'sankyartesaanit/numbered-features' => array(
				'title'  => 'Numeroidut ominaisuudet — tumma osio',
				'fields' => array(
					'eyebrow'      => 'Yläotsikko (string)',
					'title'        => 'Otsikko (string)',
					'description'  => 'Ingressi (string)',
					'items[] / features[]' => 'Kohdat: number (esim. "01"), title, description / text',
				),
			),
			'sankyartesaanit/stat-cards'        => array(
				'title'  => 'Tilastokortit',
				'fields' => array(
					'showIcons' => 'true | false',
					'theme'     => '"warm"',
					'stats[] / cards[]' => 'Kortit: value / stat, label, icon (valinnainen), iconStyle',
				),
			),
			'sankyartesaanit/testimonials'      => array(
				'title'  => 'Asiakaskokemukset',
				'fields' => array(
					'items[]' => 'Kohdat: quote, productName, authorLabel, linkUrl, linkText',
				),
			),
			'sankyartesaanit/location-section'  => array(
				'title'  => 'Myymäläosio',
				'fields' => array(
					'eyebrow'      => 'Yläotsikko (string)',
					'title'        => 'Otsikko (string)',
					'description'  => 'Kuvaus (string)',
					'giftText'     => 'Lahjateksti (string)',
					'locations[]'  => 'Myymälät: eyebrow, title, address, buttonText, buttonUrl',
					'theme'        => '"sand"',
				),
			),
			'sankyartesaanit/faq'               => array(
				'title'  => 'UKK — usein kysytyt',
				'fields' => array(
					'eyebrow'  => 'Yläotsikko (string)',
					'title'    => 'Otsikko (string)',
					'items[]'  => 'Kysymykset: question (string), answer (HTML string, rich text)',
				),
			),
			'sankyartesaanit/cta-section'       => array(
				'title'  => 'Toimintakehotus — lopun CTA',
				'fields' => array(
					'eyebrow'     => 'Yläotsikko (string)',
					'title'       => 'Otsikko (string)',
					'description' => 'Kuvaus (string)',
					'primaryButtonText / ctaPrimaryLabel' => 'Ensisijainen painike',
					'primaryButtonUrl / ctaPrimaryLink'   => 'Ensisijainen linkki',
					'secondaryButtonText / ctaSecondaryLabel' => 'Toissijainen painike',
					'secondaryButtonUrl / ctaSecondaryLink'   => 'Toissijainen linkki',
				),
			),
		);
	}
}
