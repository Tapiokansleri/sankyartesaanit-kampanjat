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
 * Resolve video URL from block attributes.
 *
 * @param array $attrs Attributes.
 */
function kansleri_sa_get_video_url( $attrs ) {
	$url = $attrs['videoUrl'] ?? '';

	if ( ! $url && ! empty( $attrs['videoId'] ) ) {
		$url = wp_get_attachment_url( (int) $attrs['videoId'] ) ?: '';
	}

	return $url;
}

/**
 * Resolve poster image URL for video blocks.
 *
 * @param array $attrs Attributes.
 */
function kansleri_sa_get_media_poster_url( $attrs ) {
	$poster = $attrs['videoPosterUrl'] ?? '';

	if ( ! $poster && ! empty( $attrs['imageUrl'] ) ) {
		$poster = $attrs['imageUrl'];
	}

	if ( ! $poster && ! empty( $attrs['imageId'] ) ) {
		$poster = wp_get_attachment_image_url( (int) $attrs['imageId'], 'large' ) ?: '';
	}

	return $poster;
}

/**
 * Render video from block attributes.
 *
 * @param array  $attrs Attribute array with videoId, videoUrl, optional poster from imageUrl/imageId.
 * @param string $class CSS class for video element.
 */
function kansleri_sa_render_video( $attrs, $class = 'sa-video' ) {
	$url = kansleri_sa_get_video_url( $attrs );

	if ( ! $url ) {
		return;
	}

	$poster = kansleri_sa_get_media_poster_url( $attrs );

	$mime = 'video/mp4';
	if ( ! empty( $attrs['videoId'] ) ) {
		$attached_mime = get_post_mime_type( (int) $attrs['videoId'] );
		if ( $attached_mime ) {
			$mime = $attached_mime;
		}
	}

	$width  = 0;
	$height = 0;

	if ( ! empty( $attrs['videoId'] ) ) {
		$meta = wp_get_attachment_metadata( (int) $attrs['videoId'] );
		if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
			$width  = (int) $meta['width'];
			$height = (int) $meta['height'];
		}
	}

	if ( ( ! $width || ! $height ) && ! empty( $attrs['imageId'] ) ) {
		$image_meta = wp_get_attachment_metadata( (int) $attrs['imageId'] );
		if ( ! empty( $image_meta['width'] ) && ! empty( $image_meta['height'] ) ) {
			$width  = (int) $image_meta['width'];
			$height = (int) $image_meta['height'];
		}
	}

	$video_attrs = array(
		'class'       => $class,
		'controls'    => true,
		'playsinline' => true,
		'preload'     => 'auto',
	);

	if ( $width && $height ) {
		$video_attrs['width']  = $width;
		$video_attrs['height'] = $height;
	}

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

	echo '<div class="sa-media-frame">';

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

	echo '</video></div>';
}

/**
 * Render split-content media (image or video).
 *
 * @param array $attrs Block attributes.
 */
function kansleri_sa_render_split_media( $attrs ) {
	$media_type = ( $attrs['mediaType'] ?? 'image' ) === 'video' ? 'video' : 'image';

	if ( 'video' === $media_type && kansleri_sa_get_video_url( $attrs ) ) {
		kansleri_sa_render_video( $attrs, 'sa-split-content__video' );
		return;
	}

	kansleri_sa_render_image( $attrs, 'sa-split-content__image' );
}

/**
 * Extract a YouTube video ID from a URL or bare ID.
 *
 * @param string $url YouTube URL or 11-character video ID.
 */
function kansleri_sa_get_youtube_id( $url ) {
	$url = trim( (string) $url );

	if ( ! $url ) {
		return '';
	}

	if ( preg_match( '/^[a-zA-Z0-9_-]{11}$/', $url ) ) {
		return $url;
	}

	$patterns = array(
		'#(?:youtube\.com/watch\?(?:[^&]+&)*v=|youtube\.com/embed/|youtube\.com/shorts/|youtu\.be/)([a-zA-Z0-9_-]{11})#',
	);

	foreach ( $patterns as $pattern ) {
		if ( preg_match( $pattern, $url, $matches ) ) {
			return $matches[1];
		}
	}

	return '';
}

/**
 * Resolve YouTube embed orientation.
 *
 * @param array $attrs Block attributes.
 */
function kansleri_sa_get_youtube_orientation( $attrs ) {
	$orientation = $attrs['youtubeOrientation'] ?? 'horizontal';

	return 'vertical' === $orientation ? 'vertical' : 'horizontal';
}

/**
 * Render a responsive YouTube embed.
 *
 * @param string $url         YouTube URL or video ID.
 * @param string $class       CSS class for the iframe.
 * @param string $title       Accessible iframe title.
 * @param string $orientation "horizontal" or "vertical".
 */
function kansleri_sa_render_youtube_embed( $url, $class = 'sa-youtube-embed', $title = '', $orientation = 'horizontal' ) {
	$video_id = kansleri_sa_get_youtube_id( $url );

	if ( ! $video_id ) {
		return;
	}

	$orientation  = 'vertical' === $orientation ? 'vertical' : 'horizontal';
	$iframe_title = $title ? $title : __( 'YouTube video', 'kansleri-sankyartesaanit' );
	$src          = sprintf(
		'https://www.youtube-nocookie.com/embed/%1$s?rel=0&modestbranding=1',
		rawurlencode( $video_id )
	);

	printf(
		'<div class="sa-youtube-embed-wrap sa-youtube-embed-wrap--%1$s">',
		esc_attr( $orientation )
	);
	printf(
		'<iframe class="%1$s" src="%2$s" title="%3$s" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
		esc_attr( $class ),
		esc_url( $src ),
		esc_attr( $iframe_title )
	);
	echo '</div>';
}

/**
 * Render a silent, looping video file used as a decorative hero background.
 *
 * The video is purely decorative (hidden from assistive technology, not focusable).
 * Playback is controlled by the pause button in the hero and by hero-video.js.
 *
 * @param string $url    Video file URL (mp4 / webm).
 * @param string $poster Optional poster image URL shown before the video plays.
 */
function kansleri_sa_render_video_background( $url, $poster = '' ) {
	$url = esc_url( $url );

	if ( ! $url ) {
		return;
	}

	$filetype = wp_check_filetype( (string) wp_parse_url( $url, PHP_URL_PATH ) );
	$mime     = ! empty( $filetype['type'] ) && 0 === strpos( $filetype['type'], 'video/' ) ? $filetype['type'] : 'video/mp4';

	printf(
		'<video class="sa-hero__bg-video" autoplay muted loop playsinline preload="auto" aria-hidden="true" tabindex="-1" disablepictureinpicture disableremoteplayback%1$s><source src="%2$s" type="%3$s"></video>',
		$poster ? ' poster="' . esc_url( $poster ) . '"' : '',
		esc_url( $url ),
		esc_attr( $mime )
	);
}

/**
 * Render hero media (image or YouTube video).
 *
 * @param array $attrs Block attributes.
 */
function kansleri_sa_render_hero_media( $attrs ) {
	$media_type = ( $attrs['mediaType'] ?? 'image' ) === 'youtube' ? 'youtube' : 'image';

	if ( 'youtube' === $media_type && kansleri_sa_get_youtube_id( $attrs['youtubeUrl'] ?? '' ) ) {
		kansleri_sa_render_youtube_embed(
			$attrs['youtubeUrl'],
			'sa-hero__youtube',
			$attrs['imageAlt'] ?? ( $attrs['title'] ?? '' ),
			kansleri_sa_get_youtube_orientation( $attrs )
		);
		return;
	}

	kansleri_sa_render_image( $attrs, 'sa-hero__image' );
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

/**
 * Output block wrapper attributes (escaped by WordPress core).
 *
 * @param array         $attributes Block attributes.
 * @param string        $class      Base CSS class.
 * @param WP_Block|null $block      Block instance.
 */
function kansleri_sa_print_wrapper_attrs( $attributes, $class, $block = null ) {
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in get_block_wrapper_attributes().
	echo kansleri_sa_wrapper_attrs( $attributes, $class, $block );
}
