<?php
defined( 'ABSPATH' ) || exit;

$columns   = $attributes['columns'] ?? array();
$theme     = $attributes['theme'] ?? 'cream';
$show_icons = ! empty( $attributes['showIcons'] );

if ( empty( $columns ) ) {
	return;
}

$class = 'sa-feature-columns alignfull sa-feature-columns--' . sanitize_html_class( $theme );
if ( $show_icons ) {
	$class .= ' sa-feature-columns--has-icons';
}
?>
<section <?php kansleri_sa_print_wrapper_attrs( $attributes, $class, $block_instance ); ?>>
	<div class="sa-container sa-feature-columns__grid">
		<?php foreach ( $columns as $column ) : ?>
			<?php
			$col_title = $column['title'] ?? '';
			$col_desc  = $column['description'] ?? '';
			$icon      = $column['icon'] ?? '';
			$icon_style = $column['iconStyle'] ?? 'solid';

			if ( ! $col_title && ! $col_desc && ! $icon ) {
				continue;
			}
			?>
			<div class="sa-feature-columns__item">
				<?php if ( $show_icons && $icon ) : ?>
					<?php kansleri_sa_render_fa_icon( $icon, $icon_style, 'sa-feature-columns__icon' ); ?>
				<?php endif; ?>
				<?php if ( $col_title ) : ?>
					<h3 class="sa-feature-columns__title"><?php kansleri_sa_esc_text( $col_title ); ?></h3>
				<?php endif; ?>
				<?php if ( $col_desc ) : ?>
					<p class="sa-feature-columns__description"><?php kansleri_sa_esc_text( $col_desc ); ?></p>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
