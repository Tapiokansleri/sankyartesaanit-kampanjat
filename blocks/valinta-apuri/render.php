<?php
defined( 'ABSPATH' ) || exit;

$products = array_values( array_filter( (array) ( $attributes['products'] ?? array() ), 'is_array' ) );

if ( empty( $products ) ) {
	return;
}

$theme  = sanitize_html_class( $attributes['theme'] ?? 'warm' );
$anchor = sanitize_title( $attributes['anchorId'] ?? '' );
$uid    = wp_unique_id( 'sa-finder-' );

$type_labels = array(
	'jenkki'   => __( 'Jenkkisänky', 'kansleri-sankyartesaanit' ),
	'moottori' => __( 'Moottorisänky', 'kansleri-sankyartesaanit' ),
	'patja'    => __( 'Sijauspatja', 'kansleri-sankyartesaanit' ),
);

$questions = array(
	'type'     => array(
		'legend'  => __( 'Millaisen sängyn haluat?', 'kansleri-sankyartesaanit' ),
		'options' => array(
			'any'      => __( 'En tiedä vielä', 'kansleri-sankyartesaanit' ),
			'jenkki'   => __( 'Jenkkisänky', 'kansleri-sankyartesaanit' ),
			'moottori' => __( 'Säädettävä moottorisänky', 'kansleri-sankyartesaanit' ),
			'best'     => __( 'Etsin parasta vaihtoehtoa (Ultrat ja Bohot)', 'kansleri-sankyartesaanit' ),
		),
	),
	'sleep'    => array(
		'legend'  => __( 'Haluatko Sensowell®-unenseurannan sänkyysi?', 'kansleri-sankyartesaanit' ),
		'options' => array(
			'any' => __( 'En tiedä vielä', 'kansleri-sankyartesaanit' ),
			'yes' => __( 'Kyllä kiitos!', 'kansleri-sankyartesaanit' ),
			'no'  => __( 'Ei tarvita', 'kansleri-sankyartesaanit' ),
		),
	),
	'feel'     => array(
		'legend'  => __( 'Millainen tuntuma?', 'kansleri-sankyartesaanit' ),
		'options' => array(
			'any'   => __( 'En tiedä vielä', 'kansleri-sankyartesaanit' ),
			'soft'  => __( 'Pehmeä', 'kansleri-sankyartesaanit' ),
			'fx'    => __( 'Pintapehmeä', 'kansleri-sankyartesaanit' ),
			'firm'  => __( 'Jämäkkä', 'kansleri-sankyartesaanit' ),
			'xhard' => __( 'Kova', 'kansleri-sankyartesaanit' ),
		),
		'hint'    => __( 'Pintapehmeä on FX Ergonomic -tuntuma: pinta tuntuu pehmeältä, mutta alta sänky tukee. Parisängyssä tuntuman voi valita kummallekin puolelle erikseen.', 'kansleri-sankyartesaanit' ),
	),
	'material' => array(
		'legend'  => __( 'Minkälaiset materiaalit haluat sänkyysi?', 'kansleri-sankyartesaanit' ),
		'options' => array(
			'any'     => __( 'Ei väliä', 'kansleri-sankyartesaanit' ),
			'natural' => __( 'Haluan hengittävät ja allergiavapaat luonnonmateriaalit', 'kansleri-sankyartesaanit' ),
		),
	),
);
?>
<section <?php kansleri_sa_print_wrapper_attrs( $attributes, 'sa-finder sa-finder--' . $theme, $block_instance, $anchor ? array( 'id' => $anchor ) : array() ); ?> data-sa-finder>
	<div class="sa-container">
		<header class="sa-finder__header">
			<?php if ( ! empty( $attributes['eyebrow'] ) ) : ?>
				<p class="sa-eyebrow"><?php kansleri_sa_esc_text( $attributes['eyebrow'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['title'] ) ) : ?>
				<h2 class="sa-finder__title"><?php kansleri_sa_esc_text( $attributes['title'] ); ?></h2>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['description'] ) ) : ?>
				<p class="sa-finder__description"><?php kansleri_sa_esc_text( $attributes['description'] ); ?></p>
			<?php endif; ?>
		</header>

		<div class="sa-finder__layout">
			<form class="sa-finder__form" action="#" onsubmit="return false;">
				<?php $n = 0; ?>
				<?php foreach ( $questions as $key => $question ) : ?>
					<?php $n++; ?>
					<fieldset class="sa-finder__question">
						<legend><span class="sa-finder__step"><?php echo (int) $n; ?></span><?php kansleri_sa_esc_text( $question['legend'] ); ?></legend>
						<div class="sa-finder__options">
							<?php foreach ( $question['options'] as $value => $label ) : ?>
								<label class="sa-finder__chip">
									<input type="radio" name="<?php echo esc_attr( $uid . '-' . $key ); ?>" data-q="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( 'any', $value ); ?> />
									<span><?php kansleri_sa_esc_text( $label ); ?></span>
								</label>
							<?php endforeach; ?>
						</div>
						<?php if ( ! empty( $question['hint'] ) ) : ?>
							<p class="sa-finder__hint"><?php kansleri_sa_esc_text( $question['hint'] ); ?></p>
						<?php endif; ?>
					</fieldset>
				<?php endforeach; ?>

				<button type="reset" class="sa-finder__reset"><?php esc_html_e( 'Tyhjennä valinnat', 'kansleri-sankyartesaanit' ); ?></button>
			</form>

			<div class="sa-finder__results" aria-live="polite">
				<p class="sa-finder__count"><span class="sa-finder__count-num"><?php echo count( $products ); ?></span> <span class="sa-finder__count-text"><?php esc_html_e( 'mallia sopii valintoihisi', 'kansleri-sankyartesaanit' ); ?></span></p>
				<ol class="sa-finder__list">
					<?php foreach ( $products as $product ) : ?>
						<?php
						$type    = $product['type'] ?? '';
						$sw      = $product['sensowell'] ?? 'no';
						$natural = $product['natural'] ?? 'no';
						$ultra   = ! empty( $product['ultra'] ) ? '1' : '0';
						$feels   = array_map( 'sanitize_key', (array) ( $product['feels'] ?? array() ) );
						$slug    = sanitize_title( $product['slug'] ?? '' );
						$badges  = array();
						if ( 'moottori' === $type ) {
							$badges[] = __( 'Säädettävä', 'kansleri-sankyartesaanit' );
						}
						if ( 'yes' === $sw ) {
							$badges[] = 'Sensowell';
						} elseif ( 'option' === $sw ) {
							$badges[] = __( 'Sensowell lisävaruste', 'kansleri-sankyartesaanit' );
						}
						if ( in_array( 'fx', $feels, true ) ) {
							$badges[] = 'FX Ergonomic';
						}
						if ( 'yes' === $natural ) {
							$badges[] = __( 'Luonnonmateriaalit', 'kansleri-sankyartesaanit' );
						}
						$meta = trim( ( $product['series'] ?? ( $type_labels[ $type ] ?? '' ) ) . ( ! empty( $product['springs'] ) ? ' · ' . $product['springs'] : '' ) );
						?>
						<li class="sa-finder__item" data-slug="<?php echo esc_attr( $slug ); ?>" data-type="<?php echo esc_attr( $type ); ?>" data-ultra="<?php echo esc_attr( $ultra ); ?>" data-sw="<?php echo esc_attr( $sw ); ?>" data-natural="<?php echo esc_attr( $natural ); ?>" data-feels="<?php echo esc_attr( implode( ' ', $feels ) ); ?>">
							<a class="sa-finder__link" href="<?php echo esc_url( $product['url'] ?? ( '/tuote/' . $slug . '/' ) ); ?>">
								<span class="sa-finder__thumb">
									<?php
									if ( ! empty( $product['imageId'] ) && wp_attachment_is_image( (int) $product['imageId'] ) ) {
										echo wp_get_attachment_image( (int) $product['imageId'], 'thumbnail', false, array( 'loading' => 'eager', 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									} elseif ( ! empty( $product['imageUrl'] ) ) {
										printf( '<img src="%s" alt="" />', esc_url( $product['imageUrl'] ) );
									}
									?>
								</span>
								<span class="sa-finder__info">
									<span class="sa-finder__top">
										<span class="sa-finder__name"><?php kansleri_sa_esc_text( $product['name'] ?? '' ); ?></span>
									</span>
									<span class="sa-finder__meta"><?php kansleri_sa_esc_text( $meta ); ?></span>
									<?php if ( $badges ) : ?>
										<span class="sa-finder__badges">
											<?php foreach ( $badges as $badge ) : ?>
												<span class="sa-finder__badge"><?php kansleri_sa_esc_text( $badge ); ?></span>
											<?php endforeach; ?>
										</span>
									<?php endif; ?>
								</span>
							</a>
						</li>
					<?php endforeach; ?>
				</ol>
				<button type="button" class="sa-finder__more" hidden></button>
				<p class="sa-finder__empty" hidden><?php esc_html_e( 'Täsmälleen sopivaa mallia ei löytynyt. Kokeile vähemmän rajauksia tai varaa aika, niin etsitään sopiva sänky yhdessä.', 'kansleri-sankyartesaanit' ); ?></p>
				<p class="sa-finder__note"><?php esc_html_e( 'Hinnat ja koot löydät tuotesivulta.', 'kansleri-sankyartesaanit' ); ?></p>
				<div class="sa-finder__actions">
					<?php kansleri_sa_render_button( $attributes['resultsCtaUrl'] ?? '#varaa-aika', $attributes['resultsCtaText'] ?? '', 'sa-btn sa-btn--primary' ); ?>
					<a class="sa-finder__jump" href="#" hidden><?php esc_html_e( 'Näytä valitut mallit vertailussa', 'kansleri-sankyartesaanit' ); ?></a>
				</div>
			</div>
		</div>
	</div>
</section>
