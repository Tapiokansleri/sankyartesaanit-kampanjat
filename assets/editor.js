( function ( blocks, element, components, blockEditor, serverSideRender, i18n ) {
	const { registerBlockType } = blocks;
	const { createElement: el, Fragment, useState, useEffect, useRef } = element;
	const {
		PanelBody,
		TextControl,
		TextareaControl,
		SelectControl,
		Button,
		ToggleControl,
		RangeControl,
	} = components;
	const { InspectorControls, MediaUpload, MediaUploadCheck, useBlockProps, RichText } = blockEditor;
	const ServerSideRender = serverSideRender;
	const { __ } = i18n;

	function BlockPreview( props ) {
		const blockProps = useBlockProps( {
			className: 'sa-editor-block',
		} );

		return el(
			'div',
			blockProps,
			el(
				'div',
				{ className: 'sa-editor-block__preview kansleri-sa-editor-preview' },
				el( ServerSideRender, {
					block: props.blockName,
					attributes: props.attributes,
				} )
			)
		);
	}

	function preview( blockName, attributes ) {
		return el( BlockPreview, {
			blockName: blockName,
			attributes: attributes,
		} );
	}

	function textField( label, attribute, attributes, setAttributes, help ) {
		return el( TextControl, {
			label: label,
			value: attributes[ attribute ] || '',
			help: help || '',
			onChange: function ( value ) {
				const update = {};
				update[ attribute ] = value;
				setAttributes( update );
			},
		} );
	}

	function textareaField( label, attribute, attributes, setAttributes, rows ) {
		return el( TextareaControl, {
			label: label,
			value: attributes[ attribute ] || '',
			rows: rows || 3,
			onChange: function ( value ) {
				const update = {};
				update[ attribute ] = value;
				setAttributes( update );
			},
		} );
	}

	function selectField( label, attribute, attributes, setAttributes, options ) {
		return el( SelectControl, {
			label: label,
			value: attributes[ attribute ] || options[ 0 ].value,
			options: options,
			onChange: function ( value ) {
				const update = {};
				update[ attribute ] = value;
				setAttributes( update );
			},
		} );
	}

	function imageField( label, attributes, setAttributes ) {
		const imageUrl = attributes.imageUrl || '';
		const imageId = attributes.imageId || 0;

		return el(
			'div',
			{ className: 'sa-editor-image-field' },
			el( 'p', { className: 'components-base-control__label' }, label ),
			imageUrl
				? el( 'img', {
						src: imageUrl,
						alt: attributes.imageAlt || '',
						style: { maxWidth: '100%', marginBottom: '8px' },
				  } )
				: null,
			el(
				MediaUploadCheck,
				null,
				el( MediaUpload, {
					onSelect: function ( media ) {
						setAttributes( {
							imageId: media.id,
							imageUrl: media.url,
							imageAlt: media.alt || attributes.imageAlt || '',
						} );
					},
					allowedTypes: [ 'image' ],
					value: imageId,
					render: function ( obj ) {
						return el(
							Button,
							{ variant: 'secondary', onClick: obj.open },
							imageUrl ? __( 'Vaihda kuva', 'kansleri-sankyartesaanit' ) : __( 'Valitse kuva', 'kansleri-sankyartesaanit' )
						);
					},
				} )
			),
			imageUrl
				? el(
						Button,
						{
							isDestructive: true,
							variant: 'link',
							onClick: function () {
								setAttributes( { imageId: 0, imageUrl: '', imageAlt: '' } );
							},
						},
						__( 'Poista kuva', 'kansleri-sankyartesaanit' )
				  )
				: null,
			textField( __( 'Kuvan alt-teksti', 'kansleri-sankyartesaanit' ), 'imageAlt', attributes, setAttributes )
		);
	}

	function videoField( label, attributes, setAttributes ) {
		const videoUrl = attributes.videoUrl || '';
		const videoId = attributes.videoId || 0;

		return el(
			'div',
			{ className: 'sa-editor-video-field' },
			el( 'p', { className: 'components-base-control__label' }, label ),
			videoUrl
				? el( 'video', {
						src: videoUrl,
						controls: true,
						style: { maxWidth: '100%', marginBottom: '8px' },
				  } )
				: null,
			el(
				MediaUploadCheck,
				null,
				el( MediaUpload, {
					onSelect: function ( media ) {
						setAttributes( {
							videoId: media.id,
							videoUrl: media.url,
						} );
					},
					allowedTypes: [ 'video' ],
					value: videoId,
					render: function ( obj ) {
						return el(
							Button,
							{ variant: 'secondary', onClick: obj.open },
							videoUrl ? __( 'Vaihda video', 'kansleri-sankyartesaanit' ) : __( 'Valitse video', 'kansleri-sankyartesaanit' )
						);
					},
				} )
			),
			textField(
				__( 'Videon URL (mp4)', 'kansleri-sankyartesaanit' ),
				'videoUrl',
				attributes,
				setAttributes,
				__( 'Voit liittää suoran mp4-osoitteen tai valita median kirjastosta.', 'kansleri-sankyartesaanit' )
			),
			videoUrl
				? el(
						Button,
						{
							isDestructive: true,
							variant: 'link',
							onClick: function () {
								setAttributes( { videoId: 0, videoUrl: '' } );
							},
						},
						__( 'Poista video', 'kansleri-sankyartesaanit' )
				  )
				: null,
			el( ToggleControl, {
				label: __( 'Toista automaattisesti', 'kansleri-sankyartesaanit' ),
				checked: !! attributes.videoAutoplay,
				help: __( 'Autoplay vaatii yleensä mykistetyn videon.', 'kansleri-sankyartesaanit' ),
				onChange: function ( value ) {
					const update = { videoAutoplay: value };
					if ( value ) {
						update.videoMuted = true;
					}
					setAttributes( update );
				},
			} ),
			el( ToggleControl, {
				label: __( 'Toista uudelleen', 'kansleri-sankyartesaanit' ),
				checked: !! attributes.videoLoop,
				onChange: function ( value ) {
					setAttributes( { videoLoop: value } );
				},
			} ),
			el( ToggleControl, {
				label: __( 'Mykistetty', 'kansleri-sankyartesaanit' ),
				checked: !! attributes.videoMuted,
				onChange: function ( value ) {
					setAttributes( { videoMuted: value } );
				},
			} )
		);
	}

	function backgroundVideoField( attributes, setAttributes ) {
		const videoUrl = attributes.videoBgUrl || '';
		const videoId = attributes.videoBgId || 0;

		return el(
			'div',
			{ className: 'sa-editor-video-field' },
			el( 'p', { className: 'components-base-control__label' }, __( 'Taustavideo (mp4 tai webm)', 'kansleri-sankyartesaanit' ) ),
			videoUrl
				? el( 'video', {
						src: videoUrl,
						controls: true,
						muted: true,
						style: { maxWidth: '100%', marginBottom: '8px' },
				  } )
				: null,
			el(
				MediaUploadCheck,
				null,
				el( MediaUpload, {
					onSelect: function ( media ) {
						setAttributes( {
							videoBgId: media.id,
							videoBgUrl: media.url,
						} );
					},
					allowedTypes: [ 'video' ],
					value: videoId,
					render: function ( obj ) {
						return el(
							Button,
							{ variant: 'secondary', onClick: obj.open },
							videoUrl ? __( 'Vaihda video', 'kansleri-sankyartesaanit' ) : __( 'Valitse video', 'kansleri-sankyartesaanit' )
						);
					},
				} )
			),
			textField(
				__( 'Videon URL', 'kansleri-sankyartesaanit' ),
				'videoBgUrl',
				attributes,
				setAttributes,
				__(
					'Valitse video mediakirjastosta tai liitä suora mp4-osoite. Vinkki: lyhyt, mykistetty 1080p-silmukka alle 8 Mt:n tiedostona toimii parhaiten.',
					'kansleri-sankyartesaanit'
				)
			),
			videoUrl
				? el(
						Button,
						{
							isDestructive: true,
							variant: 'link',
							onClick: function () {
								setAttributes( { videoBgId: 0, videoBgUrl: '' } );
							},
						},
						__( 'Poista video', 'kansleri-sankyartesaanit' )
				  )
				: null
		);
	}

	function getYoutubeId( url ) {
		const value = ( url || '' ).trim();

		if ( ! value ) {
			return '';
		}

		if ( /^[a-zA-Z0-9_-]{11}$/.test( value ) ) {
			return value;
		}

		const match = value.match(
			/(?:youtube\.com\/watch\?(?:[^&]+&)*v=|youtube\.com\/embed\/|youtube\.com\/shorts\/|youtu\.be\/)([a-zA-Z0-9_-]{11})/
		);

		return match ? match[1] : '';
	}

	function youtubeField( attributes, setAttributes ) {
		const youtubeUrl = attributes.youtubeUrl || '';
		const youtubeId = getYoutubeId( youtubeUrl );
		const orientation = ( attributes.youtubeOrientation || 'horizontal' ) === 'vertical' ? 'vertical' : 'horizontal';
		const aspectRatio = orientation === 'vertical' ? '9 / 16' : '16 / 9';

		return el(
			'div',
			{ className: 'sa-editor-youtube-field' },
			textField(
				__( 'YouTube-linkki', 'kansleri-sankyartesaanit' ),
				'youtubeUrl',
				attributes,
				setAttributes,
				__( 'Liitä YouTube-videon osoite (youtube.com tai youtu.be). Lataa video ensin YouTubeen.', 'kansleri-sankyartesaanit' )
			),
			selectField(
				__( 'Videon suunta', 'kansleri-sankyartesaanit' ),
				'youtubeOrientation',
				attributes,
				setAttributes,
				[
					{ label: 'Vaakasuunta (16:9)', value: 'horizontal' },
					{ label: 'Pystysuunta (9:16)', value: 'vertical' },
				]
			),
			youtubeId
				? el( 'iframe', {
						title: __( 'YouTube-esikatselu', 'kansleri-sankyartesaanit' ),
						src: 'https://www.youtube-nocookie.com/embed/' + youtubeId + '?rel=0&modestbranding=1',
						style: {
							width: '100%',
							maxWidth: orientation === 'vertical' ? '220px' : '100%',
							aspectRatio: aspectRatio,
							border: 0,
							marginBottom: '8px',
							display: 'block',
							marginInline: orientation === 'vertical' ? 'auto' : undefined,
						},
						loading: 'lazy',
						allow: 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share',
						allowFullScreen: true,
				  } )
				: null,
			textField(
				__( 'Videon kuvaus (saavutettavuus)', 'kansleri-sankyartesaanit' ),
				'imageAlt',
				attributes,
				setAttributes,
				__( 'Kuvaa lyhyesti videon sisältö ruudunlukijoille.', 'kansleri-sankyartesaanit' )
			)
		);
	}

	function nestedImageField( item, index, arrayKey, attributes, setAttributes, label ) {
		const items = ( attributes[ arrayKey ] || [] ).slice();
		const current = items[ index ] || {};

		return el(
			'div',
			{ className: 'sa-editor-image-field' },
			el( 'p', { className: 'components-base-control__label' }, label ),
			current.imageUrl
				? el( 'img', {
						src: current.imageUrl,
						alt: current.imageAlt || '',
						style: { maxWidth: '100%', marginBottom: '8px' },
				  } )
				: null,
			el(
				MediaUploadCheck,
				null,
				el( MediaUpload, {
					onSelect: function ( media ) {
						items[ index ] = Object.assign( {}, current, {
							imageId: media.id,
							imageUrl: media.url,
							imageAlt: media.alt || current.imageAlt || '',
						} );
						const update = {};
						update[ arrayKey ] = items;
						setAttributes( update );
					},
					allowedTypes: [ 'image' ],
					value: current.imageId || 0,
					render: function ( obj ) {
						return el(
							Button,
							{ variant: 'secondary', onClick: obj.open },
							current.imageUrl ? __( 'Vaihda kuva', 'kansleri-sankyartesaanit' ) : __( 'Valitse kuva', 'kansleri-sankyartesaanit' )
						);
					},
				} )
			)
		);
	}

	function DebouncedPreview( props ) {
		const debouncedAttributes = useDebouncedAttributes( props.attributes, 500 );

		return preview( props.blockName, debouncedAttributes );
	}

	function useDebouncedAttributes( attributes, delay ) {
		const [ debounced, setDebounced ] = useState( attributes );
		const timerRef = useRef( null );

		useEffect(
			function () {
				clearTimeout( timerRef.current );
				timerRef.current = setTimeout( function () {
					setDebounced( attributes );
				}, delay );

				return function () {
					clearTimeout( timerRef.current );
				};
			},
			[ attributes, delay ]
		);

		return debounced;
	}

	function cloneItems( items ) {
		return ( items || [] ).map( function ( item ) {
			return Object.assign( {}, item );
		} );
	}

	function FaqBlockEdit( props ) {
		const attributes = props.attributes;
		const setAttributes = props.setAttributes;
		const [ items, setItems ] = useState( function () {
			return cloneItems( attributes.items );
		} );
		const saveTimer = useRef( null );
		const lastSaved = useRef( JSON.stringify( attributes.items || [] ) );

		useEffect(
			function () {
				const incoming = JSON.stringify( attributes.items || [] );

				if ( incoming === lastSaved.current ) {
					return;
				}

				lastSaved.current = incoming;
				setItems( cloneItems( attributes.items ) );
			},
			[ attributes.items ]
		);

		function persistItems( nextItems ) {
			setItems( nextItems );
			clearTimeout( saveTimer.current );
			saveTimer.current = setTimeout( function () {
				lastSaved.current = JSON.stringify( nextItems );
				setAttributes( { items: nextItems } );
			}, 400 );
		}

		function updateItem( index, patch ) {
			const next = items.slice();
			next[ index ] = Object.assign( {}, next[ index ], patch );
			persistItems( next );
		}

		return el(
			Fragment,
			null,
			el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Otsikko', 'kansleri-sankyartesaanit' ), initialOpen: true },
					textField( __( 'Yläotsikko', 'kansleri-sankyartesaanit' ), 'eyebrow', attributes, setAttributes ),
					textField( __( 'Otsikko', 'kansleri-sankyartesaanit' ), 'title', attributes, setAttributes )
				),
				el(
					PanelBody,
					{ title: __( 'Kysymykset', 'kansleri-sankyartesaanit' ), initialOpen: true },
					el(
						'p',
						{ className: 'sa-editor-note' },
						__( 'Muokkaa kysymyksiä ja vastauksia alla. Vastauksissa voi käyttää lihavointia ja linkkejä.', 'kansleri-sankyartesaanit' )
					),
					items.map( function ( item, index ) {
						return el(
							'div',
							{ key: 'faq-item-' + index, className: 'sa-editor-repeater-item' },
							el(
								'p',
								{ className: 'sa-editor-repeater-item__title' },
								__( 'Kysymys', 'kansleri-sankyartesaanit' ) + ' ' + ( index + 1 )
							),
							el( TextControl, {
								label: __( 'Kysymys', 'kansleri-sankyartesaanit' ),
								value: item.question || '',
								onChange: function ( value ) {
									updateItem( index, { question: value } );
								},
							} ),
							el(
								'div',
								{ className: 'sa-editor-rich-text-field' },
								el( 'p', { className: 'components-base-control__label' }, __( 'Vastaus', 'kansleri-sankyartesaanit' ) ),
								el( RichText, {
									identifier: 'faq-answer-' + index,
									tagName: 'div',
									className: 'sa-editor-rich-text',
									value: item.answer || '',
									onChange: function ( value ) {
										updateItem( index, { answer: value } );
									},
									allowedFormats: [
										'core/bold',
										'core/italic',
										'core/link',
										'core/strikethrough',
										'core/list',
									],
									multiline: 'p',
									placeholder: __( 'Kirjoita vastaus…', 'kansleri-sankyartesaanit' ),
								} )
							),
							el(
								Button,
								{
									isDestructive: true,
									variant: 'secondary',
									onClick: function () {
										const next = items.slice();
										next.splice( index, 1 );
										persistItems( next );
									},
								},
								__( 'Poista', 'kansleri-sankyartesaanit' )
							)
						);
					} ),
					el(
						Button,
						{
							variant: 'primary',
							onClick: function () {
								persistItems(
									items.concat( [
										{
											question: '',
											answer: '',
										},
									] )
								);
							},
						},
						__( 'Lisää kysymys', 'kansleri-sankyartesaanit' )
					)
				)
			),
			el( DebouncedPreview, {
				blockName: 'sankyartesaanit/faq',
				attributes: attributes,
			} )
		);
	}

	function repeaterControls( config ) {
		const items = config.attributes[ config.arrayKey ] || [];

		return el(
			Fragment,
			null,
			el(
				'p',
				{ className: 'sa-editor-note' },
				config.note || __( 'Muokkaa kohteita sivupaneelista. Sisältöä ei voi muokata suoraan editorissa.', 'kansleri-sankyartesaanit' )
			),
			items.map( function ( item, index ) {
				return el(
					'div',
					{ key: index, className: 'sa-editor-repeater-item' },
					el( 'p', { className: 'sa-editor-repeater-item__title' }, ( config.itemLabel || 'Kohde' ) + ' ' + ( index + 1 ) ),
					( config.fields || [] ).map( function ( field ) {
						if ( field.type === 'image' ) {
							return nestedImageField( item, index, config.arrayKey, config.attributes, config.setAttributes, field.label );
						}

						const value = item[ field.key ] || '';
						const onChange = function ( newValue ) {
							const next = ( config.attributes[ config.arrayKey ] || [] ).slice();
							next[ index ] = Object.assign( {}, next[ index ], ( function () {
								const patch = {};
								patch[ field.key ] = newValue;
								return patch;
							} )() );
							const update = {};
							update[ config.arrayKey ] = next;
							config.setAttributes( update );
						};

						if ( field.type === 'textarea' ) {
							return el( TextareaControl, {
								key: field.key,
								label: field.label,
								value: value,
								rows: field.rows || 3,
								onChange: onChange,
							} );
						}

						if ( field.type === 'lines' ) {
							return el( TextareaControl, {
								key: field.key,
								label: field.label,
								help: __( 'Yksi rivi per kohta', 'kansleri-sankyartesaanit' ),
								value: Array.isArray( value ) ? value.join( '\n' ) : value,
								rows: field.rows || 4,
								onChange: function ( text ) {
									onChange(
										text
											.split( '\n' )
											.map( function ( line ) {
												return line.trim();
											} )
											.filter( Boolean )
									);
								},
							} );
						}

						if ( field.type === 'fa-icon' ) {
							const FaIconField = window.KansleriSA && window.KansleriSA.FaIconField;
							if ( ! FaIconField ) {
								return null;
							}

							return el( FaIconField, {
								key: field.key,
								label: field.label,
								iconName: item.icon || '',
								iconStyle: item.iconStyle || 'solid',
								onChange: function ( name, style ) {
									const next = ( config.attributes[ config.arrayKey ] || [] ).slice();
									next[ index ] = Object.assign( {}, next[ index ], {
										icon: name,
										iconStyle: style || 'solid',
									} );
									const update = {};
									update[ config.arrayKey ] = next;
									config.setAttributes( update );
								},
							} );
						}

						if ( field.type === 'rich-text' ) {
							return el(
								'div',
								{ key: field.key + '-' + index, className: 'sa-editor-rich-text-field' },
								el( 'p', { className: 'components-base-control__label' }, field.label ),
								el( RichText, {
									identifier: config.arrayKey + '-' + field.key + '-' + index,
									tagName: 'div',
									className: 'sa-editor-rich-text',
									value: value,
									onChange: onChange,
									allowedFormats: [
										'core/bold',
										'core/italic',
										'core/link',
										'core/strikethrough',
										'core/list',
									],
									multiline: 'p',
									placeholder: field.placeholder || '',
								} )
							);
						}

						return el( TextControl, {
							key: field.key,
							label: field.label,
							value: value,
							onChange: onChange,
						} );
					} ),
					config.canRemove !== false
						? el(
								Button,
								{
									isDestructive: true,
									variant: 'secondary',
									onClick: function () {
										const next = ( config.attributes[ config.arrayKey ] || [] ).slice();
										next.splice( index, 1 );
										const update = {};
										update[ config.arrayKey ] = next;
										config.setAttributes( update );
									},
								},
								__( 'Poista', 'kansleri-sankyartesaanit' )
						  )
						: null
				);
			} ),
			config.canAdd !== false
				? el(
						Button,
						{
							variant: 'primary',
							onClick: function () {
								const next = ( config.attributes[ config.arrayKey ] || [] ).slice();
								next.push( Object.assign( {}, config.defaultItem || {} ) );
								const update = {};
								update[ config.arrayKey ] = next;
								config.setAttributes( update );
							},
						},
						config.addLabel || __( 'Lisää kohde', 'kansleri-sankyartesaanit' )
				  )
				: null
		);
	}

	registerBlockType( 'sankyartesaanit/promo-banner', {
		edit: function ( props ) {
			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Ilmoitusteksti', 'kansleri-sankyartesaanit' ), initialOpen: true },
						textareaField( __( 'Teksti', 'kansleri-sankyartesaanit' ), 'text', props.attributes, props.setAttributes, 3 )
					)
				),
				preview( 'sankyartesaanit/promo-banner', props.attributes )
			);
		},
		save: function () {
			return null;
		},
	} );

	registerBlockType( 'sankyartesaanit/section-header', {
		edit: function ( props ) {
			const attrs = props.attributes;
			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Otsikon sisältö', 'kansleri-sankyartesaanit' ), initialOpen: true },
						textField( __( 'Yläotsikko', 'kansleri-sankyartesaanit' ), 'eyebrow', attrs, props.setAttributes ),
						textField( __( 'Otsikko', 'kansleri-sankyartesaanit' ), 'title', attrs, props.setAttributes ),
						textareaField( __( 'Kuvaus', 'kansleri-sankyartesaanit' ), 'description', attrs, props.setAttributes, 4 ),
						selectField(
							__( 'Tasaus', 'kansleri-sankyartesaanit' ),
							'alignText',
							attrs,
							props.setAttributes,
							[
								{ label: 'Vasen', value: 'left' },
								{ label: 'Keskitetty', value: 'center' },
							]
						),
						selectField(
							__( 'Teema', 'kansleri-sankyartesaanit' ),
							'theme',
							attrs,
							props.setAttributes,
							[
								{ label: 'Vaalea', value: 'light' },
								{ label: 'Lämmin', value: 'warm' },
								{ label: 'Kerma', value: 'cream' },
								{ label: 'Hiekka', value: 'sand' },
								{ label: 'Tumma', value: 'dark' },
							]
						)
					)
				),
				preview( 'sankyartesaanit/section-header', attrs )
			);
		},
		save: function () {
			return null;
		},
	} );

	registerBlockType( 'sankyartesaanit/hero', {
		edit: function ( props ) {
			const attrs = props.attributes;
			const isYoutube = ( attrs.mediaType || 'image' ) === 'youtube';
			const videoBackground = !! attrs.videoBackground;
			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Hero-sisältö', 'kansleri-sankyartesaanit' ), initialOpen: true },
						textField( __( 'Yläotsikko', 'kansleri-sankyartesaanit' ), 'eyebrow', attrs, props.setAttributes ),
						textField( __( 'Otsikko', 'kansleri-sankyartesaanit' ), 'title', attrs, props.setAttributes ),
						textareaField( __( 'Kuvaus', 'kansleri-sankyartesaanit' ), 'description', attrs, props.setAttributes, 3 ),
						textField( __( 'Hinta', 'kansleri-sankyartesaanit' ), 'price', attrs, props.setAttributes ),
						textField( __( 'Vertailuhinta', 'kansleri-sankyartesaanit' ), 'comparePrice', attrs, props.setAttributes ),
						textField( __( 'Säästöteksti', 'kansleri-sankyartesaanit' ), 'savingsText', attrs, props.setAttributes ),
						textField( __( 'Koot', 'kansleri-sankyartesaanit' ), 'sizes', attrs, props.setAttributes ),
						el( TextareaControl, {
							label: __( 'Luottamusmerkit', 'kansleri-sankyartesaanit' ),
							help: __( 'Yksi rivi per kohta', 'kansleri-sankyartesaanit' ),
							value: ( function () {
								if ( attrs.trustItems && attrs.trustItems.length ) {
									return attrs.trustItems.join( '\n' );
								}
								if ( ! attrs.trustLine ) {
									return '';
								}
								return attrs.trustLine
									.split( '✓' )
									.map( function ( part ) {
										return part.trim();
									} )
									.filter( Boolean )
									.join( '\n' );
							} )(),
							rows: 4,
							onChange: function ( text ) {
								props.setAttributes( {
									trustItems: text
										.split( '\n' )
										.map( function ( line ) {
											return line.trim();
										} )
										.filter( Boolean ),
								} );
							},
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Painikkeet', 'kansleri-sankyartesaanit' ) },
						textField( __( 'Ensisijainen painike', 'kansleri-sankyartesaanit' ), 'primaryButtonText', attrs, props.setAttributes ),
						textField( __( 'Ensisijainen linkki', 'kansleri-sankyartesaanit' ), 'primaryButtonUrl', attrs, props.setAttributes ),
						textField( __( 'Toissijainen painike', 'kansleri-sankyartesaanit' ), 'secondaryButtonText', attrs, props.setAttributes ),
						textField( __( 'Toissijainen linkki', 'kansleri-sankyartesaanit' ), 'secondaryButtonUrl', attrs, props.setAttributes )
					),
					el(
						PanelBody,
						{ title: __( 'Media', 'kansleri-sankyartesaanit' ) },
						el( ToggleControl, {
							label: __( 'Videotausta', 'kansleri-sankyartesaanit' ),
							checked: videoBackground,
							help: __(
								'Video toistuu hiljaa ja silmukkana hero-osion taustalla ja teksti näkyy sen päällä.',
								'kansleri-sankyartesaanit'
							),
							onChange: function ( value ) {
								props.setAttributes( { videoBackground: value } );
							},
						} ),
						videoBackground ? backgroundVideoField( attrs, props.setAttributes ) : null,
						videoBackground
							? el( RangeControl, {
									label: __( 'Taustan tummennus (%)', 'kansleri-sankyartesaanit' ),
									help: __( 'Tummentaa videota, jotta teksti pysyy luettavana.', 'kansleri-sankyartesaanit' ),
									value: typeof attrs.videoOverlay === 'number' ? attrs.videoOverlay : 60,
									min: 0,
									max: 90,
									step: 5,
									onChange: function ( value ) {
										props.setAttributes( { videoOverlay: typeof value === 'number' ? value : 60 } );
									},
							  } )
							: null,
						videoBackground
							? imageField(
									__( 'Varakuva (näkyy ennen videota ja jos video ei toistu)', 'kansleri-sankyartesaanit' ),
									attrs,
									props.setAttributes
							  )
							: null,
						videoBackground
							? null
							: selectField(
									__( 'Median tyyppi', 'kansleri-sankyartesaanit' ),
									'mediaType',
									attrs,
									props.setAttributes,
									[
										{ label: 'Kuva', value: 'image' },
										{ label: 'YouTube-video', value: 'youtube' },
									]
							  ),
						videoBackground
							? null
							: isYoutube
							? youtubeField( attrs, props.setAttributes )
							: imageField( __( 'Hero-kuva', 'kansleri-sankyartesaanit' ), attrs, props.setAttributes ),
						videoBackground
							? null
							: selectField(
									__( 'Median sijainti', 'kansleri-sankyartesaanit' ),
									'imagePosition',
									attrs,
									props.setAttributes,
									[
										{ label: 'Oikealla', value: 'right' },
										{ label: 'Vasemmalla', value: 'left' },
									]
							  ),
						videoBackground
							? null
							: selectField(
									__( 'Median pystysuunta', 'kansleri-sankyartesaanit' ),
									'imageVerticalAlign',
									attrs,
									props.setAttributes,
									[
										{ label: 'Keskellä', value: 'center' },
										{ label: 'Ylhäällä', value: 'top' },
									]
							  )

					)
				),
				preview( 'sankyartesaanit/hero', attrs )
			);
		},
		save: function () {
			return null;
		},
	} );

	registerBlockType( 'sankyartesaanit/feature-columns', {
		edit: function ( props ) {
			const attrs = props.attributes;
			const showIcons = attrs.showIcons !== false;
			const columnFields = [
				{ key: 'title', label: __( 'Otsikko', 'kansleri-sankyartesaanit' ) },
				{ key: 'description', label: __( 'Kuvaus', 'kansleri-sankyartesaanit' ), type: 'textarea' },
			];

			if ( showIcons ) {
				columnFields.unshift( {
					key: 'icon',
					label: __( 'Font Awesome -ikoni', 'kansleri-sankyartesaanit' ),
					type: 'fa-icon',
				} );
			}

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Sarakkeet', 'kansleri-sankyartesaanit' ), initialOpen: true },
						el( ToggleControl, {
							label: __( 'Näytä Font Awesome -ikonit', 'kansleri-sankyartesaanit' ),
							checked: showIcons,
							onChange: function ( value ) {
								props.setAttributes( { showIcons: value } );
							},
						} ),
						repeaterControls( {
							attributes: attrs,
							setAttributes: props.setAttributes,
							arrayKey: 'columns',
							itemLabel: __( 'Sarake', 'kansleri-sankyartesaanit' ),
							defaultItem: { title: '', description: '', icon: '', iconStyle: 'solid' },
							fields: columnFields,
						} ),
						selectField(
							__( 'Teema', 'kansleri-sankyartesaanit' ),
							'theme',
							attrs,
							props.setAttributes,
							[
								{ label: 'Kerma', value: 'cream' },
								{ label: 'Lämmin', value: 'warm' },
							]
						)
					)
				),
				preview( 'sankyartesaanit/feature-columns', attrs )
			);
		},
		save: function () {
			return null;
		},
	} );

	registerBlockType( 'sankyartesaanit/split-content', {
		edit: function ( props ) {
			const attrs = props.attributes;
			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Sisältö', 'kansleri-sankyartesaanit' ), initialOpen: true },
						textField( __( 'Yläotsikko', 'kansleri-sankyartesaanit' ), 'eyebrow', attrs, props.setAttributes ),
						textField( __( 'Otsikko', 'kansleri-sankyartesaanit' ), 'title', attrs, props.setAttributes ),
						el( TextareaControl, {
							label: __( 'Kappaleet', 'kansleri-sankyartesaanit' ),
							help: __( 'Yksi kappale per rivi', 'kansleri-sankyartesaanit' ),
							value: ( attrs.paragraphs || [] ).join( '\n' ),
							rows: 4,
							onChange: function ( text ) {
								props.setAttributes( {
									paragraphs: text
										.split( '\n' )
										.map( function ( line ) {
											return line.trim();
										} )
										.filter( Boolean ),
								} );
							},
						} ),
						el( TextareaControl, {
							label: __( 'Lista', 'kansleri-sankyartesaanit' ),
							help: __( 'Yksi kohta per rivi', 'kansleri-sankyartesaanit' ),
							value: ( attrs.listItems || [] ).join( '\n' ),
							rows: 5,
							onChange: function ( text ) {
								props.setAttributes( {
									listItems: text
										.split( '\n' )
										.map( function ( line ) {
											return line.trim();
										} )
										.filter( Boolean ),
								} );
							},
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Media', 'kansleri-sankyartesaanit' ) },
						selectField(
							__( 'Median tyyppi', 'kansleri-sankyartesaanit' ),
							'mediaType',
							attrs,
							props.setAttributes,
							[
								{ label: 'Kuva', value: 'image' },
								{ label: 'Video (mp4)', value: 'video' },
							]
						),
						( attrs.mediaType || 'image' ) === 'video'
							? el(
									Fragment,
									null,
									videoField( __( 'Video', 'kansleri-sankyartesaanit' ), attrs, props.setAttributes ),
									imageField(
										__( 'Esikatselukuva (poster)', 'kansleri-sankyartesaanit' ),
										attrs,
										props.setAttributes
									)
							  )
							: imageField( __( 'Kuva', 'kansleri-sankyartesaanit' ), attrs, props.setAttributes ),
						selectField(
							__( 'Median sijainti', 'kansleri-sankyartesaanit' ),
							'imagePosition',
							attrs,
							props.setAttributes,
							[
								{ label: 'Oikealla', value: 'right' },
								{ label: 'Vasemmalla', value: 'left' },
							]
						)
					)
				),
				preview( 'sankyartesaanit/split-content', attrs )
			);
		},
		save: function () {
			return null;
		},
	} );

	registerBlockType( 'sankyartesaanit/product-cards', {
		edit: function ( props ) {
			const attrs = props.attributes;
			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Tuotekortit', 'kansleri-sankyartesaanit' ), initialOpen: true },
						repeaterControls( {
							attributes: attrs,
							setAttributes: props.setAttributes,
							arrayKey: 'products',
							itemLabel: __( 'Tuote', 'kansleri-sankyartesaanit' ),
							addLabel: __( 'Lisää tuote', 'kansleri-sankyartesaanit' ),
							defaultItem: {
								imageId: 0,
								imageUrl: '',
								imageAlt: '',
								eyebrow: '',
								title: '',
								price: '',
								comparePrice: '',
								priceNote: '',
								features: [],
								buttonText: '',
								buttonUrl: '/ajanvaraus/',
							},
							fields: [
								{ key: 'imageUrl', label: __( 'Kuva', 'kansleri-sankyartesaanit' ), type: 'image' },
								{ key: 'eyebrow', label: __( 'Yläotsikko', 'kansleri-sankyartesaanit' ) },
								{ key: 'title', label: __( 'Otsikko', 'kansleri-sankyartesaanit' ) },
								{ key: 'price', label: __( 'Hinta', 'kansleri-sankyartesaanit' ) },
								{ key: 'comparePrice', label: __( 'Vertailuhinta', 'kansleri-sankyartesaanit' ) },
								{ key: 'priceNote', label: __( 'Hintahuomio', 'kansleri-sankyartesaanit' ) },
								{ key: 'features', label: __( 'Ominaisuudet', 'kansleri-sankyartesaanit' ), type: 'lines' },
								{ key: 'buttonText', label: __( 'Painikkeen teksti', 'kansleri-sankyartesaanit' ) },
								{ key: 'buttonUrl', label: __( 'Painikkeen linkki', 'kansleri-sankyartesaanit' ) },
							],
						} )
					)
				),
				preview( 'sankyartesaanit/product-cards', attrs )
			);
		},
		save: function () {
			return null;
		},
	} );

	registerBlockType( 'sankyartesaanit/numbered-features', {
		edit: function ( props ) {
			const attrs = props.attributes;
			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Otsikko', 'kansleri-sankyartesaanit' ), initialOpen: true },
						textField( __( 'Yläotsikko', 'kansleri-sankyartesaanit' ), 'eyebrow', attrs, props.setAttributes ),
						textField( __( 'Otsikko', 'kansleri-sankyartesaanit' ), 'title', attrs, props.setAttributes ),
						textareaField( __( 'Kuvaus', 'kansleri-sankyartesaanit' ), 'description', attrs, props.setAttributes, 3 )
					),
					el(
						PanelBody,
						{ title: __( 'Ominaisuudet', 'kansleri-sankyartesaanit' ) },
						repeaterControls( {
							attributes: attrs,
							setAttributes: props.setAttributes,
							arrayKey: 'items',
							itemLabel: __( 'Ominaisuus', 'kansleri-sankyartesaanit' ),
							defaultItem: { number: '', title: '', description: '' },
							fields: [
								{ key: 'number', label: __( 'Numero', 'kansleri-sankyartesaanit' ) },
								{ key: 'title', label: __( 'Otsikko', 'kansleri-sankyartesaanit' ) },
								{ key: 'description', label: __( 'Kuvaus', 'kansleri-sankyartesaanit' ), type: 'textarea' },
							],
						} )
					)
				),
				preview( 'sankyartesaanit/numbered-features', attrs )
			);
		},
		save: function () {
			return null;
		},
	} );

	registerBlockType( 'sankyartesaanit/stat-cards', {
		edit: function ( props ) {
			const attrs = props.attributes;
			const showIcons = attrs.showIcons !== false;
			const statFields = [
				{ key: 'value', label: __( 'Arvo', 'kansleri-sankyartesaanit' ) },
				{ key: 'label', label: __( 'Selite', 'kansleri-sankyartesaanit' ) },
			];

			if ( showIcons ) {
				statFields.unshift( {
					key: 'icon',
					label: __( 'Font Awesome -ikoni', 'kansleri-sankyartesaanit' ),
					type: 'fa-icon',
				} );
			}

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Tilastokortit', 'kansleri-sankyartesaanit' ), initialOpen: true },
						el( ToggleControl, {
							label: __( 'Näytä Font Awesome -ikonit', 'kansleri-sankyartesaanit' ),
							checked: showIcons,
							onChange: function ( value ) {
								props.setAttributes( { showIcons: value } );
							},
						} ),
						repeaterControls( {
							attributes: attrs,
							setAttributes: props.setAttributes,
							arrayKey: 'stats',
							itemLabel: __( 'Kortti', 'kansleri-sankyartesaanit' ),
							defaultItem: { value: '', label: '', icon: '', iconStyle: 'solid' },
							fields: statFields,
						} )
					)
				),
				preview( 'sankyartesaanit/stat-cards', attrs )
			);
		},
		save: function () {
			return null;
		},
	} );

	registerBlockType( 'sankyartesaanit/testimonials', {
		edit: function ( props ) {
			const attrs = props.attributes;
			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Asiakaskokemukset', 'kansleri-sankyartesaanit' ), initialOpen: true },
						repeaterControls( {
							attributes: attrs,
							setAttributes: props.setAttributes,
							arrayKey: 'items',
							itemLabel: __( 'Kokemus', 'kansleri-sankyartesaanit' ),
							defaultItem: {
								quote: '',
								productName: '',
								authorLabel: '',
								linkUrl: '',
								linkText: '',
							},
							fields: [
								{ key: 'quote', label: __( 'Lainaus', 'kansleri-sankyartesaanit' ), type: 'textarea' },
								{ key: 'productName', label: __( 'Tuotenimi', 'kansleri-sankyartesaanit' ) },
								{ key: 'authorLabel', label: __( 'Asiakas', 'kansleri-sankyartesaanit' ) },
								{ key: 'linkUrl', label: __( 'Linkki', 'kansleri-sankyartesaanit' ) },
								{ key: 'linkText', label: __( 'Linkin teksti', 'kansleri-sankyartesaanit' ) },
							],
						} )
					)
				),
				preview( 'sankyartesaanit/testimonials', attrs )
			);
		},
		save: function () {
			return null;
		},
	} );

	registerBlockType( 'sankyartesaanit/location-section', {
		edit: function ( props ) {
			const attrs = props.attributes;
			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Otsikko', 'kansleri-sankyartesaanit' ), initialOpen: true },
						textField( __( 'Yläotsikko', 'kansleri-sankyartesaanit' ), 'eyebrow', attrs, props.setAttributes ),
						textField( __( 'Otsikko', 'kansleri-sankyartesaanit' ), 'title', attrs, props.setAttributes ),
						textareaField( __( 'Kuvaus', 'kansleri-sankyartesaanit' ), 'description', attrs, props.setAttributes, 3 ),
						textField( __( 'Lahjateksti', 'kansleri-sankyartesaanit' ), 'giftText', attrs, props.setAttributes )
					),
					el(
						PanelBody,
						{ title: __( 'Myymälät', 'kansleri-sankyartesaanit' ) },
						repeaterControls( {
							attributes: attrs,
							setAttributes: props.setAttributes,
							arrayKey: 'locations',
							itemLabel: __( 'Myymälä', 'kansleri-sankyartesaanit' ),
							defaultItem: {
								eyebrow: '',
								title: '',
								address: '',
								buttonText: '',
								buttonUrl: '/ajanvaraus/',
							},
							fields: [
								{ key: 'eyebrow', label: __( 'Kaupunki', 'kansleri-sankyartesaanit' ) },
								{ key: 'title', label: __( 'Nimi', 'kansleri-sankyartesaanit' ) },
								{ key: 'address', label: __( 'Osoite', 'kansleri-sankyartesaanit' ) },
								{ key: 'buttonText', label: __( 'Painikkeen teksti', 'kansleri-sankyartesaanit' ) },
								{ key: 'buttonUrl', label: __( 'Painikkeen linkki', 'kansleri-sankyartesaanit' ) },
							],
						} )
					)
				),
				preview( 'sankyartesaanit/location-section', attrs )
			);
		},
		save: function () {
			return null;
		},
	} );

	registerBlockType( 'sankyartesaanit/faq', {
		edit: FaqBlockEdit,
		save: function () {
			return null;
		},
	} );

	registerBlockType( 'sankyartesaanit/cta-section', {
		edit: function ( props ) {
			const attrs = props.attributes;
			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Toimintakehotus', 'kansleri-sankyartesaanit' ), initialOpen: true },
						textField( __( 'Yläotsikko', 'kansleri-sankyartesaanit' ), 'eyebrow', attrs, props.setAttributes ),
						textField( __( 'Otsikko', 'kansleri-sankyartesaanit' ), 'title', attrs, props.setAttributes ),
						textareaField( __( 'Kuvaus', 'kansleri-sankyartesaanit' ), 'description', attrs, props.setAttributes, 3 ),
						textField( __( 'Ensisijainen painike', 'kansleri-sankyartesaanit' ), 'primaryButtonText', attrs, props.setAttributes ),
						textField( __( 'Ensisijainen linkki', 'kansleri-sankyartesaanit' ), 'primaryButtonUrl', attrs, props.setAttributes ),
						textField( __( 'Toissijainen painike', 'kansleri-sankyartesaanit' ), 'secondaryButtonText', attrs, props.setAttributes ),
						textField( __( 'Toissijainen linkki', 'kansleri-sankyartesaanit' ), 'secondaryButtonUrl', attrs, props.setAttributes )
					)
				),
				preview( 'sankyartesaanit/cta-section', attrs )
			);
		},
		save: function () {
			return null;
		},
	} );
	registerBlockType( 'sankyartesaanit/booking-embed', {
		edit: function ( props ) {
			const attrs = props.attributes;
			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Ajanvarauskalenteri', 'kansleri-sankyartesaanit' ), initialOpen: true },
						textField( __( 'Yläotsikko', 'kansleri-sankyartesaanit' ), 'eyebrow', attrs, props.setAttributes ),
						textField( __( 'Otsikko', 'kansleri-sankyartesaanit' ), 'title', attrs, props.setAttributes ),
						textareaField( __( 'Kuvaus', 'kansleri-sankyartesaanit' ), 'description', attrs, props.setAttributes, 3 ),
						textField(
							__( 'Kalenterin osoite (https)', 'kansleri-sankyartesaanit' ),
							'embedUrl',
							attrs,
							props.setAttributes,
							__( 'Easybook-kalenterin upotusosoite. Vain https-osoitteet toimivat.', 'kansleri-sankyartesaanit' )
						),
						el( RangeControl, {
							label: __( 'Kalenterin korkeus (px)', 'kansleri-sankyartesaanit' ),
							value: typeof attrs.embedHeight === 'number' ? attrs.embedHeight : 640,
							min: 360,
							max: 1000,
							step: 20,
							onChange: function ( value ) {
								props.setAttributes( { embedHeight: typeof value === 'number' ? value : 640 } );
							},
						} ),
						textField(
							__( 'Ankkuri', 'kansleri-sankyartesaanit' ),
							'anchorId',
							attrs,
							props.setAttributes,
							__( 'Sivun CTA-painikkeet ohjaavat tähän, esim. #varaa-aika. Älä muuta ilman, että päivität painikkeiden linkit.', 'kansleri-sankyartesaanit' )
						),
						textareaField( __( 'Soittoteksti', 'kansleri-sankyartesaanit' ), 'callLead', attrs, props.setAttributes, 2 ),
						textField( __( 'Soittopainike', 'kansleri-sankyartesaanit' ), 'callText', attrs, props.setAttributes ),
						textField( __( 'Soittolinkki', 'kansleri-sankyartesaanit' ), 'callUrl', attrs, props.setAttributes ),
						selectField(
							__( 'Teema', 'kansleri-sankyartesaanit' ),
							'theme',
							attrs,
							props.setAttributes,
							[
								{ label: 'Hiekka', value: 'sand' },
								{ label: 'Lämmin', value: 'warm' },
								{ label: 'Vaalea', value: 'light' },
								{ label: 'Tumma', value: 'dark' },
							]
						)
					)
				),
				preview( 'sankyartesaanit/booking-embed', attrs )
			);
		},
		save: function () {
			return null;
		},
	} );

	// Optional CTA row (primary + secondary button) for every section block.
	const CTA_BLOCK_SLUGS = [
		'section-header',
		'feature-columns',
		'split-content',
		'product-cards',
		'numbered-features',
		'stat-cards',
		'testimonials',
		'location-section',
		'faq',
	];

	window.wp.hooks.addFilter(
		'editor.BlockEdit',
		'kansleri-sa/cta-controls',
		window.wp.compose.createHigherOrderComponent( function ( BlockEdit ) {
			return function ( props ) {
				const name = props.name || '';

				if ( name.indexOf( 'sankyartesaanit/' ) !== 0 || CTA_BLOCK_SLUGS.indexOf( name.replace( 'sankyartesaanit/', '' ) ) === -1 ) {
					return el( BlockEdit, props );
				}

				const attrs = props.attributes;

				return el(
					Fragment,
					null,
					el( BlockEdit, props ),
					el(
						InspectorControls,
						null,
						el(
							PanelBody,
							{ title: __( 'Toimintakutsu (CTA-rivi)', 'kansleri-sankyartesaanit' ), initialOpen: !! attrs.ctaText },
							textField(
								__( 'Ensisijainen painike', 'kansleri-sankyartesaanit' ),
								'ctaText',
								attrs,
								props.setAttributes,
								__( 'Esim. Varaa henkilökohtainen palveluaika. Tyhjä teksti = ei CTA-riviä.', 'kansleri-sankyartesaanit' )
							),
							textField( __( 'Ensisijainen linkki', 'kansleri-sankyartesaanit' ), 'ctaUrl', attrs, props.setAttributes ),
							textField( __( 'Toissijainen painike', 'kansleri-sankyartesaanit' ), 'ctaSecondaryText', attrs, props.setAttributes ),
							textField( __( 'Toissijainen linkki', 'kansleri-sankyartesaanit' ), 'ctaSecondaryUrl', attrs, props.setAttributes )
						)
					)
				);
			};
		}, 'withKansleriCtaControls' )
	);
} )(
	window.wp.blocks,
	window.wp.element,
	window.wp.components,
	window.wp.blockEditor,
	window.wp.serverSideRender,
	window.wp.i18n
);
