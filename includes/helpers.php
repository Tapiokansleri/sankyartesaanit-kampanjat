<?php
/**
 * Shared render helpers for Sänkyartesaanit blocks.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Escape and output plain text.
 *
 * @param string $text Text.
 */
function kansleri_sa_esc_text( $text ) {
	echo esc_html( $text );
}

/**
 * Output sanitized rich text (HTML from editor or plain text).
 *
 * @param string $content Content.
 */
function kansleri_sa_render_rich_text( $content ) {
	$content = trim( (string) $content );

	if ( ! $content ) {
		return;
	}

	if ( $content === wp_strip_all_tags( $content ) ) {
		echo wp_kses_post( wpautop( esc_html( $content ) ) );
		return;
	}

	echo wp_kses_post( $content );
}

/**
 * Render a button link.
 *
 * @param string $url    URL.
 * @param string $label  Label.
 * @param string $class  CSS class.
 */
function kansleri_sa_render_button( $url, $label, $class = 'sa-btn sa-btn--primary' ) {
	if ( ! $label ) {
		return;
	}
	printf(
		'<a class="%1$s" href="%2$s">%3$s</a>',
		esc_attr( $class ),
		esc_url( $url ?: '#' ),
		esc_html( $label )
	);
}

/**
 * Render image from block attributes.
 *
 * @param array  $attrs Attribute array with imageId, imageUrl, imageAlt.
 * @param string $class CSS class for img.
 */
function kansleri_sa_render_image( $attrs, $class = 'sa-image' ) {
	$url = $attrs['imageUrl'] ?? '';
	$alt = $attrs['imageAlt'] ?? '';

	if ( ! empty( $attrs['imageId'] ) ) {
		$html = wp_get_attachment_image(
			(int) $attrs['imageId'],
			'large',
			false,
			array(
				'class'   => $class,
				'loading' => 'lazy',
				'alt'     => $alt,
			)
		);
		if ( $html ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}
	}

	if ( $url ) {
		printf(
			'<img class="%1$s" src="%2$s" alt="%3$s" loading="lazy" />',
			esc_attr( $class ),
			esc_url( $url ),
			esc_attr( $alt )
		);
	}
}

/**
 * Merge block wrapper attributes.
 *
 * @param array         $attributes Block attributes.
 * @param string        $class      Base CSS class.
 * @param WP_Block|null $block      Block instance.
 * @return string
 */
function kansleri_sa_render_fa_icon( $icon_name, $icon_style = 'solid', $class = 'sa-icon' ) {
	if ( ! $icon_name || ! preg_match( '/^[a-z0-9-]+$/', $icon_name ) ) {
		return;
	}

	$style_map = array(
		'solid'   => 'fa-solid',
		'regular' => 'fa-regular',
		'brands'  => 'fa-brands',
	);

	$prefix = $style_map[ $icon_style ] ?? 'fa-solid';

	printf(
		'<span class="%1$s" aria-hidden="true"><i class="%2$s fa-%3$s"></i></span>',
		esc_attr( $class ),
		esc_attr( $prefix ),
		esc_attr( $icon_name )
	);
}

function kansleri_sa_get_hero_trust_items( $attributes ) {
	$items = $attributes['trustItems'] ?? array();

	if ( is_array( $items ) && ! empty( $items ) ) {
		return array_values(
			array_filter(
				array_map(
					static function ( $item ) {
						return trim( (string) $item );
					},
					$items
				)
			)
		);
	}

	$line = trim( (string) ( $attributes['trustLine'] ?? '' ) );
	if ( ! $line ) {
		return array();
	}

	$parts = preg_split( '/\s*✓\s*/u', $line );

	return array_values(
		array_filter(
			array_map(
				static function ( $part ) {
					return trim( $part );
				},
				$parts ? $parts : array()
			)
		)
	);
}

function kansleri_sa_wrapper_attrs( $attributes, $class, $block = null ) {
	$extra = $attributes['className'] ?? '';
	$full  = trim( $class . ( $extra ? ' ' . $extra : '' ) );

	if ( empty( $attributes['align'] ) ) {
		$full .= ' alignfull';
	}

	return get_block_wrapper_attributes(
		array(
			'class' => $full,
		),
		$block
	);
}
