<?php
defined( 'ABSPATH' ) || exit;

$locations = $attributes['locations'] ?? array();
$theme     = sanitize_html_class( $attributes['theme'] ?? 'sand' );
$class     = 'sa-location-section alignfull sa-location-section--' . $theme;
?>
<section <?php kansleri_sa_print_wrapper_attrs( $attributes, $class, $block_instance ); ?>>
	<div class="sa-container sa-location-section__intro">
		<?php if ( ! empty( $attributes['eyebrow'] ) ) : ?>
			<p class="sa-eyebrow"><?php kansleri_sa_esc_text( $attributes['eyebrow'] ); ?></p>
		<?php endif; ?>
		<?php if ( ! empty( $attributes['title'] ) ) : ?>
			<h2 class="sa-location-section__title"><?php kansleri_sa_esc_text( $attributes['title'] ); ?></h2>
		<?php endif; ?>
		<?php if ( ! empty( $attributes['description'] ) ) : ?>
			<p class="sa-location-section__description"><?php kansleri_sa_esc_text( $attributes['description'] ); ?></p>
		<?php endif; ?>
		<?php if ( ! empty( $attributes['giftText'] ) ) : ?>
			<p class="sa-location-section__gift"><?php kansleri_sa_esc_text( $attributes['giftText'] ); ?></p>
		<?php endif; ?>
	</div>
	<?php if ( ! empty( $locations ) ) : ?>
		<div class="sa-container sa-location-section__grid">
			<?php foreach ( $locations as $location ) : ?>
				<div class="sa-location-card">
					<?php if ( ! empty( $location['eyebrow'] ) ) : ?>
						<p class="sa-eyebrow"><?php kansleri_sa_esc_text( $location['eyebrow'] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $location['title'] ) ) : ?>
						<h3 class="sa-location-card__title"><?php kansleri_sa_esc_text( $location['title'] ); ?></h3>
					<?php endif; ?>
					<?php if ( ! empty( $location['address'] ) ) : ?>
						<p class="sa-location-card__address"><?php kansleri_sa_esc_text( $location['address'] ); ?></p>
					<?php endif; ?>
					<?php
					kansleri_sa_render_button(
						$location['buttonUrl'] ?? '#',
						$location['buttonText'] ?? '',
						'sa-btn sa-btn--primary'
					);
					?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
