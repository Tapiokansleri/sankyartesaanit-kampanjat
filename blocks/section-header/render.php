<?php
defined( 'ABSPATH' ) || exit;

$eyebrow     = $attributes['eyebrow'] ?? '';
$title       = $attributes['title'] ?? '';
$description = $attributes['description'] ?? '';
$align       = $attributes['alignText'] ?? 'left';
$theme       = $attributes['theme'] ?? 'light';

if ( ! $eyebrow && ! $title && ! $description ) {
	return;
}

$class = sprintf(
	'sa-section-header alignfull sa-section-header--%s sa-section-header--align-%s',
	sanitize_html_class( $theme ),
	sanitize_html_class( $align )
);
?>
<section <?php echo kansleri_sa_wrapper_attrs( $attributes, $class , $block_instance ); ?>>
	<div class="sa-container sa-section-header__inner">
		<?php if ( $eyebrow ) : ?>
			<p class="sa-eyebrow"><?php kansleri_sa_esc_text( $eyebrow ); ?></p>
		<?php endif; ?>
		<?php if ( $title ) : ?>
			<h2 class="sa-section-header__title"><?php kansleri_sa_esc_text( $title ); ?></h2>
		<?php endif; ?>
		<?php if ( $description ) : ?>
			<p class="sa-section-header__description"><?php kansleri_sa_esc_text( $description ); ?></p>
		<?php endif; ?>
	</div>
</section>
