<?php
defined( 'ABSPATH' ) || exit;

$tool = is_array( $attributes['tool'] ?? null ) ? $attributes['tool'] : array();
$type = $tool['type'] ?? '';

if ( ! in_array( $type, array( 'checklist', 'range', 'choice' ), true ) ) {
	return;
}

$uid = wp_unique_id( 'sa-tool-' );

// Only what the script needs to compute the result travels as JSON; questions and items are real HTML.
$data = array( 'type' => $type );
foreach ( array( 'results', 'outcomes', 'unit' ) as $key ) {
	if ( isset( $tool[ $key ] ) ) {
		$data[ $key ] = $tool[ $key ];
	}
}

$wrapper = get_block_wrapper_attributes(
	array(
		'class'        => 'sa-tool sa-tool--' . $type,
		'data-sa-tool' => '',
	)
);
?>
<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( ! empty( $tool['eyebrow'] ) ) : ?>
		<p class="sa-tool__eyebrow"><?php kansleri_sa_esc_text( $tool['eyebrow'] ); ?></p>
	<?php endif; ?>
	<?php if ( ! empty( $tool['title'] ) ) : ?>
		<h3 class="sa-tool__title"><?php kansleri_sa_esc_text( $tool['title'] ); ?></h3>
	<?php endif; ?>
	<?php if ( ! empty( $tool['intro'] ) ) : ?>
		<p class="sa-tool__intro"><?php kansleri_sa_esc_text( $tool['intro'] ); ?></p>
	<?php endif; ?>

	<form class="sa-tool__form" action="#" onsubmit="return false;">
		<?php if ( 'checklist' === $type ) : ?>
			<ul class="sa-tool__checks">
				<?php foreach ( (array) ( $tool['items'] ?? array() ) as $item ) : ?>
					<li>
						<label class="sa-tool__check">
							<input type="checkbox" />
							<span><?php kansleri_sa_esc_text( $item ); ?></span>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php elseif ( 'range' === $type ) : ?>
			<?php
			$min   = (float) ( $tool['min'] ?? 0 );
			$max   = (float) ( $tool['max'] ?? 10 );
			$step  = (float) ( $tool['step'] ?? 1 );
			$value = (float) ( $tool['value'] ?? $min );
			?>
			<?php if ( ! empty( $tool['label'] ) ) : ?>
				<label class="sa-tool__label" for="<?php echo esc_attr( $uid . '-range' ); ?>"><?php kansleri_sa_esc_text( $tool['label'] ); ?></label>
			<?php endif; ?>
			<div class="sa-tool__range-row">
				<input class="sa-tool__range" type="range" id="<?php echo esc_attr( $uid . '-range' ); ?>" min="<?php echo esc_attr( $min ); ?>" max="<?php echo esc_attr( $max ); ?>" step="<?php echo esc_attr( $step ); ?>" value="<?php echo esc_attr( $value ); ?>" />
				<output class="sa-tool__value" for="<?php echo esc_attr( $uid . '-range' ); ?>"><?php echo esc_html( $value . ( ! empty( $tool['unit'] ) ? ' ' . $tool['unit'] : '' ) ); ?></output>
			</div>
			<div class="sa-tool__scale" aria-hidden="true"><span><?php echo esc_html( $min ); ?></span><span><?php echo esc_html( $max ); ?></span></div>
		<?php else : ?>
			<?php foreach ( (array) ( $tool['questions'] ?? array() ) as $qi => $question ) : ?>
				<fieldset class="sa-tool__question">
					<legend><?php kansleri_sa_esc_text( $question['q'] ?? '' ); ?></legend>
					<div class="sa-tool__options">
						<?php foreach ( (array) ( $question['options'] ?? array() ) as $oi => $option ) : ?>
							<label class="sa-tool__chip">
								<input type="radio" name="<?php echo esc_attr( $uid . '-q' . $qi ); ?>" value="<?php echo (int) $oi; ?>" data-score="<?php echo esc_attr( wp_json_encode( (object) ( $option['score'] ?? array() ) ) ); ?>" />
								<span><?php kansleri_sa_esc_text( $option['label'] ?? '' ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				</fieldset>
			<?php endforeach; ?>
		<?php endif; ?>
	</form>

	<div class="sa-tool__result" aria-live="polite" hidden>
		<p class="sa-tool__result-title"></p>
		<p class="sa-tool__result-text"></p>
		<a class="sa-btn sa-btn--primary sa-tool__cta" href="#" hidden></a>
	</div>
	<?php if ( 'choice' === $type ) : ?>
		<p class="sa-tool__progress"></p>
	<?php endif; ?>

	<?php if ( ! empty( $tool['note'] ) ) : ?>
		<p class="sa-tool__note"><?php kansleri_sa_esc_text( $tool['note'] ); ?></p>
	<?php endif; ?>

	<script type="application/json" class="sa-tool__data"><?php echo wp_json_encode( $data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?></script>
</div>
