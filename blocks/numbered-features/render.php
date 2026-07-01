<?php
defined( 'ABSPATH' ) || exit;

$items = $attributes['items'] ?? array();
?>
<section <?php echo kansleri_sa_wrapper_attrs( $attributes, 'sa-numbered-features', $block_instance ); ?>>
	<div class="sa-container sa-numbered-features__inner">
		<div class="sa-numbered-features__header">
			<?php if ( ! empty( $attributes['eyebrow'] ) ) : ?>
				<p class="sa-eyebrow sa-eyebrow--gold-pale"><?php kansleri_sa_esc_text( $attributes['eyebrow'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['title'] ) ) : ?>
				<h2 class="sa-numbered-features__title"><?php kansleri_sa_esc_text( $attributes['title'] ); ?></h2>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['description'] ) ) : ?>
				<p class="sa-numbered-features__description"><?php kansleri_sa_esc_text( $attributes['description'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php if ( ! empty( $items ) ) : ?>
			<div class="sa-numbered-features__grid">
				<?php foreach ( $items as $item ) : ?>
					<div class="sa-numbered-features__item">
						<?php if ( ! empty( $item['number'] ) ) : ?>
							<p class="sa-numbered-features__number"><?php kansleri_sa_esc_text( $item['number'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $item['title'] ) ) : ?>
							<h3 class="sa-numbered-features__item-title"><?php kansleri_sa_esc_text( $item['title'] ); ?></h3>
						<?php endif; ?>
						<?php if ( ! empty( $item['description'] ) ) : ?>
							<p class="sa-numbered-features__item-desc"><?php kansleri_sa_esc_text( $item['description'] ); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
