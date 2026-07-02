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
 * Render video from block attributes.
 *
 * @param array  $attrs Attribute array with videoId, videoUrl, optional poster from imageUrl/imageId.
 * @param string $class CSS class for video element.
 */
function kansleri_sa_render_video( $attrs, $class = 'sa-video' ) {
	$url = $attrs['videoUrl'] ?? '';

	if ( ! $url && ! empty( $attrs['videoId'] ) ) {
		$url = wp_get_attachment_url( (int) $attrs['videoId'] ) ?: '';
	}

	if ( ! $url ) {
		return;
	}

	$poster = $attrs['videoPosterUrl'] ?? '';

	if ( ! $poster && ! empty( $attrs['imageUrl'] ) ) {
		$poster = $attrs['imageUrl'];
	}

	if ( ! $poster && ! empty( $attrs['imageId'] ) ) {
		$poster = wp_get_attachment_image_url( (int) $attrs['imageId'], 'large' ) ?: '';
	}

	$mime = 'video/mp4';
	if ( ! empty( $attrs['videoId'] ) ) {
		$attached_mime = get_post_mime_type( (int) $attrs['videoId'] );
		if ( $attached_mime ) {
			$mime = $attached_mime;
		}
	}

	$video_attrs = array(
		'class'       => $class,
		'controls'    => true,
		'playsinline' => true,
		'preload'     => 'metadata',
	);

	if ( ! empty( $attrs['videoAutoplay'] ) ) {
		$video_attrs['autoplay'] = true;
	}

	if ( ! empty( $attrs['videoLoop'] ) ) {
		$video_attrs['loop'] = true;
	}

	if ( ! empty( $attrs['videoMuted'] ) || ! empty( $attrs['videoAutoplay'] ) ) {
		$video_attrs['muted'] = true;
	}

	if ( $poster ) {
		$video_attrs['poster'] = $poster;
	}

	echo '<video';
	foreach ( $video_attrs as $attr_name => $attr_value ) {
		if ( true === $attr_value ) {
			echo ' ' . esc_attr( $attr_name );
			continue;
		}
		printf( ' %1$s="%2$s"', esc_attr( $attr_name ), esc_attr( (string) $attr_value ) );
	}
	echo '>';

	printf(
		'<source src="%1$s" type="%2$s" />',
		esc_url( $url ),
		esc_attr( $mime )
	);

	echo '</video>';
}

/**
 * Render split-content media (image or video).
 *
 * @param array $attrs Block attributes.
 */
function kansleri_sa_render_split_media( $attrs ) {
	$media_type = $attrs['mediaType'] ?? 'image';

	if ( 'video' === $media_type ) {
		kansleri_sa_render_video( $attrs, 'sa-split-content__video' );
		return;
	}

	kansleri_sa_render_image( $attrs, 'sa-split-content__image' );
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
	$items = $attributes['trustItems'] ?? $attributes['microtrust'] ?? array();

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
