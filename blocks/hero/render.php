<?php
defined( 'ABSPATH' ) || exit;

$position       = ( $attributes['imagePosition'] ?? 'right' ) === 'left' ? 'left' : 'right';
$vertical_align = ( $attributes['imageVerticalAlign'] ?? 'center' ) === 'top' ? 'top' : 'center';
$media_type     = ( $attributes['mediaType'] ?? 'image' ) === 'youtube' ? 'youtube' : 'image';
$youtube_class  = '';

if ( 'youtube' === $media_type ) {
	$youtube_class = ' sa-hero--youtube-' . kansleri_sa_get_youtube_orientation( $attributes );
}

$class = sprintf(
	'sa-hero alignfull sa-hero--image-%s sa-hero--valign-%s sa-hero--media-%s%s',
	$position,
	$vertical_align,
	$media_type,
	$youtube_class
);
$bonus_items = $attributes['bonusItems'] ?? array();
?>
<section <?php kansleri_sa_print_wrapper_attrs( $attributes, $class, $block_instance ); ?>>
	<div class="sa-container sa-hero__grid">
		<div class="sa-hero__content">
			<?php if ( ! empty( $attributes['eyebrow'] ) ) : ?>
				<p class="sa-eyebrow"><?php kansleri_sa_esc_text( $attributes['eyebrow'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['title'] ) ) : ?>
				<h1 class="sa-hero__title"><?php kansleri_sa_esc_text( $attributes['title'] ); ?></h1>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['description'] ) ) : ?>
				<p class="sa-hero__description"><?php kansleri_sa_esc_text( $attributes['description'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['price'] ) ) : ?>
				<div class="sa-hero__price">
					<p class="sa-hero__price-current">
						<strong><?php kansleri_sa_esc_text( $attributes['price'] ); ?></strong>
					</p>
					<?php if ( ! empty( $attributes['comparePrice'] ) || ! empty( $attributes['savingsText'] ) ) : ?>
						<p class="sa-hero__price-meta">
							<?php if ( ! empty( $attributes['comparePrice'] ) ) : ?>
								<s><?php kansleri_sa_esc_text( $attributes['comparePrice'] ); ?></s>
							<?php endif; ?>
							<?php if ( ! empty( $attributes['savingsText'] ) ) : ?>
								<strong class="sa-hero__savings"><?php kansleri_sa_esc_text( $attributes['savingsText'] ); ?></strong>
							<?php endif; ?>
						</p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['sizes'] ) ) : ?>
				<p class="sa-hero__sizes"><?php kansleri_sa_esc_text( $attributes['sizes'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['bonusTitle'] ) || ! empty( $bonus_items ) ) : ?>
				<div class="sa-hero__bonus">
					<?php if ( ! empty( $attributes['bonusTitle'] ) ) : ?>
						<p class="sa-hero__bonus-title"><strong><?php kansleri_sa_esc_text( $attributes['bonusTitle'] ); ?></strong></p>
					<?php endif; ?>
					<?php if ( ! empty( $bonus_items ) ) : ?>
						<ul class="sa-hero__bonus-list">
							<?php foreach ( $bonus_items as $bonus_item ) : ?>
								<?php if ( $bonus_item ) : ?>
									<li><?php kansleri_sa_esc_text( $bonus_item ); ?></li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="sa-hero__buttons">
				<?php
				kansleri_sa_render_button(
					$attributes['primaryButtonUrl'] ?? '#',
					$attributes['primaryButtonText'] ?? '',
					'sa-btn sa-btn--primary'
				);
				kansleri_sa_render_button(
					$attributes['secondaryButtonUrl'] ?? '#',
					$attributes['secondaryButtonText'] ?? '',
					'sa-btn sa-btn--outline'
				);
				?>
			</div>
			<?php
			$trust_items = kansleri_sa_get_hero_trust_items( $attributes );
			if ( ! empty( $trust_items ) ) :
				?>
				<ul class="sa-hero__trust-list">
					<?php foreach ( $trust_items as $trust_item ) : ?>
						<li class="sa-hero__trust-item">
							<span class="sa-hero__trust-icon" aria-hidden="true"><i class="fa-solid fa-circle-check"></i></span>
							<span class="sa-hero__trust-text"><?php kansleri_sa_esc_text( $trust_item ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<div class="sa-hero__media">
			<?php if ( ! empty( $attributes['scarcityBadge'] ) ) : ?>
				<span class="sa-hero__badge"><?php kansleri_sa_esc_text( $attributes['scarcityBadge'] ); ?></span>
			<?php endif; ?>
			<?php kansleri_sa_render_hero_media( $attributes ); ?>
		</div>
	</div>
</section>
