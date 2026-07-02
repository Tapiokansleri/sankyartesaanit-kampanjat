<?php
defined( 'ABSPATH' ) || exit;

$stats      = $attributes['stats'] ?? array();
$theme      = sanitize_html_class( $attributes['theme'] ?? 'warm' );
$show_icons = ! empty( $attributes['showIcons'] );

if ( empty( $stats ) ) {
	return;
}

$class = 'sa-stat-cards alignfull sa-stat-cards--' . $theme;
if ( $show_icons ) {
	$class .= ' sa-stat-cards--has-icons';
}
?>
<section <?php kansleri_sa_print_wrapper_attrs( $attributes, $class, $block_instance ); ?>>
	<div class="sa-container sa-stat-cards__grid">
		<?php foreach ( $stats as $stat ) : ?>
			<?php
			$value      = $stat['value'] ?? '';
			$label      = $stat['label'] ?? '';
			$icon       = $stat['icon'] ?? '';
			$icon_style = $stat['iconStyle'] ?? 'solid';

			if ( ! $value && ! $label && ! $icon ) {
				continue;
			}
			?>
			<div class="sa-stat-card">
				<?php if ( $show_icons && $icon ) : ?>
					<?php kansleri_sa_render_fa_icon( $icon, $icon_style, 'sa-stat-card__icon' ); ?>
				<?php endif; ?>
				<?php if ( $value ) : ?>
					<h3 class="sa-stat-card__value"><?php kansleri_sa_esc_text( $value ); ?></h3>
				<?php endif; ?>
				<?php if ( $label ) : ?>
					<p class="sa-stat-card__label"><?php kansleri_sa_esc_text( $label ); ?></p>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
