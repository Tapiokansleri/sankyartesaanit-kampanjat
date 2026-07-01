<?php
defined( 'ABSPATH' ) || exit;

$text = $attributes['text'] ?? '';
if ( ! $text ) {
	return;
}
?>
<section <?php echo kansleri_sa_wrapper_attrs( $attributes, 'sa-promo-banner alignfull' , $block_instance ); ?>>
	<div class="sa-container sa-promo-banner__inner">
		<p class="sa-promo-banner__text"><?php kansleri_sa_esc_text( $text ); ?></p>
	</div>
</section>
