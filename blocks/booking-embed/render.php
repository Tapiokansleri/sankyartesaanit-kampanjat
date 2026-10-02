<?php
defined( 'ABSPATH' ) || exit;

$anchor_id = sanitize_title( $attributes['anchorId'] ?? '' );
$anchor_id = '' !== $anchor_id ? $anchor_id : 'varaa-aika';
$theme     = sanitize_html_class( $attributes['theme'] ?? 'sand' );
$height    = max( 360, min( 1200, (int) ( $attributes['embedHeight'] ?? 640 ) ) );

// Only https embeds are allowed.
$embed_url = trim( (string) ( $attributes['embedUrl'] ?? '' ) );
$embed_url = 0 === strpos( $embed_url, 'https://' ) ? esc_url( $embed_url, array( 'https' ) ) : '';

$is_editor_preview = defined( 'REST_REQUEST' ) && REST_REQUEST;
$call_text         = trim( (string) ( $attributes['callText'] ?? '' ) );
$call_lead         = trim( (string) ( $attributes['callLead'] ?? '' ) );

$class = 'sa-booking alignfull sa-booking--' . $theme;
?>
<section <?php kansleri_sa_print_wrapper_attrs( $attributes, $class, $block_instance, array( 'id' => $anchor_id ) ); ?>>
	<div class="sa-container sa-booking__inner">
		<div class="sa-booking__header">
			<?php if ( ! empty( $attributes['eyebrow'] ) ) : ?>
				<p class="sa-eyebrow"><?php kansleri_sa_esc_text( $attributes['eyebrow'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['title'] ) ) : ?>
				<h2 class="sa-booking__title"><?php kansleri_sa_esc_text( $attributes['title'] ); ?></h2>
			<?php endif; ?>
			<?php if ( ! empty( $attributes['description'] ) ) : ?>
				<p class="sa-booking__description"><?php kansleri_sa_esc_text( $attributes['description'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php if ( $embed_url ) : ?>
			<div class="sa-booking__frame">
				<?php if ( $is_editor_preview ) : ?>
					<p class="sa-booking__placeholder"><?php echo esc_html__( 'Ajanvarauskalenteri näkyy tässä julkaistulla sivulla.', 'kansleri-sankyartesaanit' ); ?></p>
				<?php else : ?>
					<iframe
						class="sa-booking__iframe"
						src="<?php echo esc_url( $embed_url, array( 'https' ) ); ?>"
						title="<?php echo esc_attr__( 'Ajanvarauskalenteri', 'kansleri-sankyartesaanit' ); ?>"
						loading="lazy"
						referrerpolicy="strict-origin-when-cross-origin"
						style="<?php echo esc_attr( 'height:' . $height . 'px' ); ?>"
					></iframe>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if ( $call_text ) : ?>
			<div class="sa-booking__call">
				<?php if ( $call_lead ) : ?>
					<p class="sa-booking__call-lead"><?php kansleri_sa_esc_text( $call_lead ); ?></p>
				<?php endif; ?>
				<?php
				kansleri_sa_render_button(
					$attributes['callUrl'] ?? 'tel:025464065',
					$call_text,
					'dark' === $theme ? 'sa-btn sa-btn--ghost' : 'sa-btn sa-btn--outline'
				);
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
