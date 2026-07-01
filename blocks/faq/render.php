<?php
defined( 'ABSPATH' ) || exit;

$items = $attributes['items'] ?? array();
?>
<section <?php echo kansleri_sa_wrapper_attrs( $attributes, 'sa-faq alignfull', $block_instance ); ?>>
	<div class="sa-container sa-faq__inner">
		<?php if ( ! empty( $attributes['eyebrow'] ) ) : ?>
			<p class="sa-eyebrow sa-faq__eyebrow"><?php kansleri_sa_esc_text( $attributes['eyebrow'] ); ?></p>
		<?php endif; ?>
		<?php if ( ! empty( $attributes['title'] ) ) : ?>
			<h2 class="sa-faq__title"><?php kansleri_sa_esc_text( $attributes['title'] ); ?></h2>
		<?php endif; ?>
		<?php if ( ! empty( $items ) ) : ?>
			<div class="sa-faq__list">
				<?php foreach ( $items as $index => $item ) : ?>
					<details class="sa-faq__item" <?php echo 0 === (int) $index ? 'open' : ''; ?>>
						<?php if ( ! empty( $item['question'] ) ) : ?>
							<summary class="sa-faq__question">
								<span class="sa-faq__question-text"><?php kansleri_sa_esc_text( $item['question'] ); ?></span>
								<span class="sa-faq__chevron" aria-hidden="true"><span class="sa-faq__chevron-icon"></span></span>
							</summary>
						<?php endif; ?>
						<?php if ( ! empty( $item['answer'] ) ) : ?>
							<div class="sa-faq__answer">
								<div class="sa-faq__answer-inner">
									<?php kansleri_sa_render_rich_text( $item['answer'] ); ?>
								</div>
							</div>
						<?php endif; ?>
					</details>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
