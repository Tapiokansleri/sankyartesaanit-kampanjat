<?php
defined( 'ABSPATH' ) || exit;
?>
<section <?php kansleri_sa_print_wrapper_attrs( $attributes, 'sa-cta-section', $block_instance ); ?>>
	<div class="sa-container sa-cta-section__inner">
		<?php if ( ! empty( $attributes['eyebrow'] ) ) : ?>
			<p class="sa-eyebrow sa-eyebrow--gold-pale sa-cta-section__eyebrow"><?php kansleri_sa_esc_text( $attributes['eyebrow'] ); ?></p>
		<?php endif; ?>
		<?php if ( ! empty( $attributes['title'] ) ) : ?>
			<h2 class="sa-cta-section__title"><?php kansleri_sa_esc_text( $attributes['title'] ); ?></h2>
		<?php endif; ?>
		<?php if ( ! empty( $attributes['description'] ) ) : ?>
			<p class="sa-cta-section__description"><?php kansleri_sa_esc_text( $attributes['description'] ); ?></p>
		<?php endif; ?>
		<div class="sa-cta-section__buttons">
			<?php
			kansleri_sa_render_button(
				$attributes['primaryButtonUrl'] ?? '#',
				$attributes['primaryButtonText'] ?? '',
				'sa-btn sa-btn--primary'
			);
			kansleri_sa_render_button(
				$attributes['secondaryButtonUrl'] ?? '#',
				$attributes['secondaryButtonText'] ?? '',
				'sa-btn sa-btn--ghost'
			);
			?>
		</div>
	</div>
</section>
