<?php
defined( 'ABSPATH' ) || exit;

$items = $attributes['items'] ?? array();

if ( empty( $items ) ) {
	return;
}
?>
<section <?php echo kansleri_sa_wrapper_attrs( $attributes, 'sa-testimonials alignfull' , $block_instance ); ?>>
	<div class="sa-container sa-testimonials__grid">
		<?php foreach ( $items as $item ) : ?>
			<blockquote class="sa-testimonial">
				<p class="sa-testimonial__stars" aria-hidden="true">★★★★★</p>
				<?php if ( ! empty( $item['quote'] ) ) : ?>
					<p class="sa-testimonial__quote">&ldquo;<?php kansleri_sa_esc_text( $item['quote'] ); ?>&rdquo;</p>
				<?php endif; ?>
				<footer class="sa-testimonial__footer">
					<?php if ( ! empty( $item['productName'] ) ) : ?>
						<strong><?php kansleri_sa_esc_text( $item['productName'] ); ?></strong>
					<?php endif; ?>
					<?php if ( ! empty( $item['authorLabel'] ) ) : ?>
						<span>, <?php kansleri_sa_esc_text( $item['authorLabel'] ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $item['linkUrl'] ) && ! empty( $item['linkText'] ) ) : ?>
						<a class="sa-testimonial__link" href="<?php echo esc_url( $item['linkUrl'] ); ?>">
							<?php kansleri_sa_esc_text( $item['linkText'] ); ?>
						</a>
					<?php endif; ?>
				</footer>
			</blockquote>
		<?php endforeach; ?>
	</div>
</section>
