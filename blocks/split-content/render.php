<?php
defined( 'ABSPATH' ) || exit;

$position   = ( $attributes['imagePosition'] ?? 'right' ) === 'left' ? 'left' : 'right';
$theme      = sanitize_html_class( $attributes['theme'] ?? 'default' );
$paragraphs = $attributes['paragraphs'] ?? array();
$list_items = $attributes['listItems'] ?? array();
$media_type = ( $attributes['mediaType'] ?? 'image' ) === 'video' ? 'video' : 'image';

$class = sprintf(
	'sa-split-content alignfull sa-split-content--media-%s sa-split-content--image-%s sa-split-content--%s',
	$media_type,
	$position,
	$theme
);
?>
<section <?php kansleri_sa_print_wrapper_attrs( $attributes, $class, $block_instance ); ?>>
	<div class="sa-container sa-split-content__grid">
		<div class="sa-split-content__text">
			<?php if ( ! empty( $attributes['eyebrow'] ) ) : ?>
				<p class="sa-eyebrow"><?php kansleri_sa_esc_text( $attributes['eyebrow'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['title'] ) ) : ?>
				<h2 class="sa-split-content__title"><?php kansleri_sa_esc_text( $attributes['title'] ); ?></h2>
			<?php endif; ?>
			<?php foreach ( $paragraphs as $paragraph ) : ?>
				<?php if ( $paragraph ) : ?>
					<p><?php kansleri_sa_esc_text( $paragraph ); ?></p>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php if ( ! empty( $list_items ) ) : ?>
				<ul class="sa-split-content__list">
					<?php foreach ( $list_items as $item ) : ?>
						<?php if ( $item ) : ?>
							<li><?php kansleri_sa_esc_text( $item ); ?></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<div class="sa-split-content__media">
			<?php kansleri_sa_render_split_media( $attributes ); ?>
		</div>
	</div>
</section>
