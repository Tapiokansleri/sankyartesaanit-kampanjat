<?php
/**
 * Map alternate block attribute names to the canonical schema.
 */

defined( 'ABSPATH' ) || exit;

class Kansleri_SA_Attribute_Normalizer {

	/**
	 * @param string               $block      Block slug.
	 * @param array<string, mixed> $attributes Block attributes.
	 * @return array<string, mixed>
	 */
	public static function normalize( $block, $attributes ) {
		if ( ! is_array( $attributes ) ) {
			return array();
		}

		switch ( $block ) {
			case 'hero':
				return self::normalize_hero( $attributes );
			case 'cta-section':
				return self::normalize_cta( $attributes );
			case 'numbered-features':
				return self::normalize_numbered_features( $attributes );
			case 'stat-cards':
				return self::normalize_stat_cards( $attributes );
			case 'feature-columns':
				return self::normalize_feature_columns( $attributes );
			case 'split-content':
				return self::normalize_split_content( $attributes );
			default:
				return $attributes;
		}
	}

	/**
	 * @param array<string, mixed> $attributes Attributes.
	 * @param array<string,string> $map        Alias => canonical.
	 * @return array<string, mixed>
	 */
	private static function map_aliases( $attributes, $map ) {
		foreach ( $map as $alias => $canonical ) {
			if ( self::has_value( $attributes[ $alias ] ?? null ) ) {
				$attributes[ $canonical ] = $attributes[ $alias ];
			}
		}

		return $attributes;
	}

	/**
	 * @param mixed $value Value.
	 */
	private static function has_value( $value ) {
		if ( is_array( $value ) ) {
			return ! empty( $value );
		}

		return null !== $value && '' !== $value;
	}

	/**
	 * @param array<string, mixed> $attributes Attributes.
	 * @return array<string, mixed>
	 */
	private static function normalize_hero( $attributes ) {
		$attributes = self::map_aliases(
			$attributes,
			array(
				'lead'               => 'description',
				'priceNow'           => 'price',
				'priceOld'           => 'comparePrice',
				'priceBadge'         => 'savingsText',
				'priceNote'          => 'sizes',
				'ctaPrimaryLabel'    => 'primaryButtonText',
				'ctaPrimaryLink'     => 'primaryButtonUrl',
				'ctaSecondaryLabel'  => 'secondaryButtonText',
				'ctaSecondaryLink'   => 'secondaryButtonUrl',
			)
		);

		if ( self::has_value( $attributes['microtrust'] ?? null ) && ! self::has_value( $attributes['trustItems'] ?? null ) ) {
			$attributes['trustItems'] = $attributes['microtrust'];
		}

		return $attributes;
	}

	/**
	 * @param array<string, mixed> $attributes Attributes.
	 * @return array<string, mixed>
	 */
	private static function normalize_cta( $attributes ) {
		return self::map_aliases(
			$attributes,
			array(
				'ctaPrimaryLabel'   => 'primaryButtonText',
				'ctaPrimaryLink'    => 'primaryButtonUrl',
				'ctaSecondaryLabel' => 'secondaryButtonText',
				'ctaSecondaryLink'  => 'secondaryButtonUrl',
			)
		);
	}

	/**
	 * @param array<string, mixed> $attributes Attributes.
	 * @return array<string, mixed>
	 */
	private static function normalize_numbered_features( $attributes ) {
		if ( self::has_value( $attributes['features'] ?? null ) ) {
			$attributes['items'] = array_map(
				static function ( $item ) {
					if ( ! is_array( $item ) ) {
						return $item;
					}

					if ( self::has_value( $item['text'] ?? null ) ) {
						$item['description'] = $item['text'];
					}

					return $item;
				},
				$attributes['features']
			);
		}

		return $attributes;
	}

	/**
	 * @param array<string, mixed> $attributes Attributes.
	 * @return array<string, mixed>
	 */
	private static function normalize_stat_cards( $attributes ) {
		if ( self::has_value( $attributes['cards'] ?? null ) ) {
			$attributes['stats'] = array_map(
				static function ( $card ) {
					if ( ! is_array( $card ) ) {
						return $card;
					}

					if ( self::has_value( $card['stat'] ?? null ) ) {
						$card['value'] = $card['stat'];
					}

					return self::normalize_column_icon( $card );
				},
				$attributes['cards']
			);
		} elseif ( self::has_value( $attributes['stats'] ?? null ) ) {
			$attributes['stats'] = array_map(
				static function ( $card ) {
					return is_array( $card ) ? self::normalize_column_icon( $card ) : $card;
				},
				$attributes['stats']
			);
		}

		return $attributes;
	}

	/**
	 * @param array<string, mixed> $attributes Attributes.
	 * @return array<string, mixed>
	 */
	private static function normalize_feature_columns( $attributes ) {
		if ( ! self::has_value( $attributes['columns'] ?? null ) ) {
			return $attributes;
		}

		$attributes['columns'] = array_map(
			static function ( $column ) {
				return self::normalize_column_icon( $column );
			},
			$attributes['columns']
		);

		if ( ! isset( $attributes['showIcons'] ) ) {
			$attributes['showIcons'] = true;
		}

		return $attributes;
	}

	/**
	 * @param array<string, mixed> $column Column data.
	 * @return array<string, mixed>
	 */
	private static function normalize_column_icon( $column ) {
		if ( ! is_array( $column ) || empty( $column['icon'] ) ) {
			return $column;
		}

		$icon = trim( (string) $column['icon'] );

		if ( preg_match( '/^(fa-solid|fa-regular|fa-brands)\s+fa-([a-z0-9-]+)$/i', $icon, $matches ) ) {
			$column['icon']      = $matches[2];
			$column['iconStyle'] = str_replace( 'fa-', '', strtolower( $matches[1] ) );
		} elseif ( preg_match( '/^fa-([a-z0-9-]+)$/i', $icon, $matches ) ) {
			$column['icon']      = $matches[1];
			$column['iconStyle'] = $column['iconStyle'] ?? 'solid';
		}

		return $column;
	}

	/**
	 * @param array<string, mixed> $attributes Attributes.
	 * @return array<string, mixed>
	 */
	private static function normalize_split_content( $attributes ) {
		$attributes = self::map_aliases(
			$attributes,
			array(
				'video'     => 'videoUrl',
				'videoSrc'  => 'videoUrl',
				'posterUrl' => 'videoPosterUrl',
				'poster'    => 'videoPosterUrl',
			)
		);

		if ( self::has_value( $attributes['videoUrl'] ?? null ) || self::has_value( $attributes['videoId'] ?? null ) ) {
			$attributes['mediaType'] = 'video';
		}

		return $attributes;
	}
}
