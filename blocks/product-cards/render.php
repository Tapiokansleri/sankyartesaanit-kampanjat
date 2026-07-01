<?php
defined( 'ABSPATH' ) || exit;

$products = $attributes['products'] ?? array();
$theme    = sanitize_html_class( $attributes['theme'] ?? 'sand' );

if ( empty( $products ) ) {
	return;
}

$class = 'sa-product-cards alignfull sa-product-cards--' . $theme;
?>
<section <?php echo kansleri_sa_wrapper_attrs( $attributes, $class , $block_instance ); ?>>
	<div class="sa-container sa-product-cards__grid">
		<?php foreach ( $products as $product ) : ?>
			<article class="sa-product-card">
				<div class="sa-product-card__media">
					<?php kansleri_sa_render_image( $product, 'sa-product-card__image' ); ?>
				</div>
				<div class="sa-product-card__body">
					<?php if ( ! empty( $product['eyebrow'] ) ) : ?>
						<p class="sa-eyebrow sa-product-card__eyebrow"><?php kansleri_sa_esc_text( $product['eyebrow'] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $product['title'] ) ) : ?>
						<h3 class="sa-product-card__title"><?php kansleri_sa_esc_text( $product['title'] ); ?></h3>
					<?php endif; ?>
					<?php if ( ! empty( $product['price'] ) ) : ?>
						<p class="sa-product-card__price">
							<strong><?php kansleri_sa_esc_text( $product['price'] ); ?></strong>
							<?php if ( ! empty( $product['comparePrice'] ) ) : ?>
								<s><?php kansleri_sa_esc_text( $product['comparePrice'] ); ?></s>
							<?php endif; ?>
							<?php if ( ! empty( $product['priceNote'] ) ) : ?>
								<span class="sa-product-card__price-note"><?php kansleri_sa_esc_text( $product['priceNote'] ); ?></span>
							<?php endif; ?>
						</p>
					<?php endif; ?>
					<?php if ( ! empty( $product['features'] ) ) : ?>
						<ul class="sa-product-card__features">
							<?php foreach ( $product['features'] as $feature ) : ?>
								<?php if ( $feature ) : ?>
									<li><?php kansleri_sa_esc_text( $feature ); ?></li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<?php
					kansleri_sa_render_button(
						$product['buttonUrl'] ?? '#',
						$product['buttonText'] ?? '',
						'sa-btn sa-btn--primary sa-product-card__btn'
					);
					?>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
</section>
