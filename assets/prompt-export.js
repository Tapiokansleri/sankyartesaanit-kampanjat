( function ( plugins, editPost, element, components, data, blocks, i18n ) {
	const { registerPlugin } = plugins;
	const { PluginSidebar } = editPost;
	const { createElement: el, useState } = element;
	const { Button, PanelBody, Notice } = components;
	const { useSelect } = data;
	const { __ } = i18n;

	function flattenBlocks( blockList, result ) {
		( blockList || [] ).forEach( function ( block ) {
			result.push( block );
			if ( block.innerBlocks && block.innerBlocks.length ) {
				flattenBlocks( block.innerBlocks, result );
			}
		} );
		return result;
	}

	function isSaBlock( name ) {
		return name && name.indexOf( 'sankyartesaanit/' ) === 0;
	}

	function formatFields( fields ) {
		if ( ! fields ) {
			return '';
		}

		return Object.keys( fields )
			.map( function ( key ) {
				return '  - ' + key + ': ' + fields[ key ];
			} )
			.join( '\n' );
	}

	function buildBlockReference( blockNames, schema ) {
		const docs = schema.blocks || {};
		const aliases = schema.aliases || {};
		const unique = blockNames.filter( function ( name, index, arr ) {
			return isSaBlock( name ) && arr.indexOf( name ) === index;
		} );

		return unique
			.map( function ( name ) {
				const doc = docs[ name ] || {};
				let section = '### ' + ( doc.title || name ) + '\n';
				section += 'Lohko: `' + name + '`\n';
				if ( aliases[ name.replace( 'sankyartesaanit/', '' ) ] ) {
					section += 'Alias-kentät: ' + aliases[ name.replace( 'sankyartesaanit/', '' ) ] + '\n';
				}
				section += formatFields( doc.fields );
				section += '\n\nEsimerkkimuoto:\n```\n' + buildExampleComment( name ) + '\n```';
				return section;
			} )
			.join( '\n\n' );
	}

	function buildExampleComment( blockName ) {
		const example = blocks.createBlock( blockName, { align: 'full' } );
		if ( ! example ) {
			return '<!-- wp:' + blockName.replace( '/', ' ' ) + ' {"align":"full"} /-->';
		}
		return blocks.serialize( [ example ] ).trim();
	}

	function buildCurrentBlocksSection( editorBlocks ) {
		const flat = flattenBlocks( editorBlocks, [] );

		if ( ! flat.length ) {
			return '(Ei lohkoja sivulla.)';
		}

		return flat
			.map( function ( block, index ) {
				const attrs = Object.assign( {}, block.attributes || {} );
				const label = isSaBlock( block.name )
					? block.name
					: block.name + ' (muu lohko — älä muokkaa rakennetta)';

				return (
					( index + 1 ) +
					'. **' +
					label +
					'**\n```json\n' +
					JSON.stringify( attrs, null, 2 ) +
					'\n```'
				);
			} )
			.join( '\n\n' );
	}

	function buildPrompt( editorBlocks, schema ) {
		const flat = flattenBlocks( editorBlocks, [] );
		const saNames = flat.map( function ( b ) {
			return b.name;
		} );

		const parts = [
			'# Sänkyartesaanit kampanjasivu — sisällön tuottamisen prompt',
			'',
			'## Ohjeet AI:lle',
			schema.instructions || '',
			'',
			'## Nykyinen sivun rakenne ja sisältö',
			'Alla jokainen lohko järjestyksessä nykyisillä attribuuteilla. Päivitä arvot kampanjaan sopiviksi.',
			'',
			buildCurrentBlocksSection( editorBlocks ),
			'',
			'## Lohkojen kentät (Sänkyartesaanit-lohkot)',
			buildBlockReference( saNames, schema ),
			'',
			'## Toivottu vastaus',
			'Palauta koko sivun lohkokommentit peräkkäin samassa järjestyksessä. Älä jätä lohkoja pois.',
			'Muut lohkot (esim. uagb/*) kopioi sellaisenaan nykyisestä sisällöstä.',
		];

		return parts.join( '\n' );
	}

	function PromptExportSidebar() {
		const editorBlocks = useSelect( function ( select ) {
			return select( 'core/block-editor' ).getBlocks();
		}, [] );

		const [ notice, setNotice ] = useState( null );

		function copyPrompt() {
			const schema = window.kansleriSAPromptSchema || {};
			const text = buildPrompt( editorBlocks, schema );

			function onSuccess() {
				setNotice( 'success' );
				window.setTimeout( function () {
					setNotice( null );
				}, 3000 );
			}

			if ( navigator.clipboard && navigator.clipboard.writeText ) {
				navigator.clipboard.writeText( text ).then( onSuccess ).catch( function () {
					fallbackCopy( text, onSuccess );
				} );
				return;
			}

			fallbackCopy( text, onSuccess );
		}

		function fallbackCopy( text, onSuccess ) {
			const textarea = document.createElement( 'textarea' );
			textarea.value = text;
			textarea.style.position = 'fixed';
			textarea.style.opacity = '0';
			document.body.appendChild( textarea );
			textarea.select();
			try {
				document.execCommand( 'copy' );
				onSuccess();
			} catch ( e ) {
				setNotice( 'error' );
			}
			document.body.removeChild( textarea );
		}

		const saCount = flattenBlocks( editorBlocks, [] ).filter( function ( b ) {
			return isSaBlock( b.name );
		} ).length;

		return el(
			PluginSidebar,
			{
				name: 'kansleri-sa-prompt-export',
				title: __( 'AI-prompt', 'kansleri-sankyartesaanit' ),
				icon: 'clipboard',
			},
			el(
				PanelBody,
				{
					title: __( 'Kopioi kampanja-AI-prompt', 'kansleri-sankyartesaanit' ),
					initialOpen: true,
				},
				el(
					'p',
					{ className: 'sa-prompt-export-help' },
					__(
						'Luo valmiin promptin, joka sisältää kaikki sivun lohkot nykyisine kenttineen sekä ohjeet tekoälylle (ChatGPT, Claude jne.). Liitä promptiin kampanjan tiedot ja pyydä AI:ta palauttamaan päivitetty Gutenberg-lohkokoodi.',
						'kansleri-sankyartesaanit'
					)
				),
				el(
					'p',
					{ className: 'sa-prompt-export-meta' },
					__(
						'Sänkyartesaanit-lohkoja sivulla:',
						'kansleri-sankyartesaanit'
					) +
						' ' +
						String( saCount )
				),
				el(
					Button,
					{
						variant: 'primary',
						onClick: copyPrompt,
						style: { width: '100%', justifyContent: 'center' },
					},
					__( 'Kopioi prompt leikepöydälle', 'kansleri-sankyartesaanit' )
				),
				notice === 'success'
					? el(
							Notice,
							{ status: 'success', isDismissible: false },
							__( 'Prompt kopioitu leikepöydälle!', 'kansleri-sankyartesaanit' )
					  )
					: null,
				notice === 'error'
					? el(
							Notice,
							{ status: 'error', isDismissible: false },
							__( 'Kopiointi epäonnistui. Kokeile uudelleen.', 'kansleri-sankyartesaanit' )
					  )
					: null
			)
		);
	}

	registerPlugin( 'kansleri-sa-prompt-export', {
		render: PromptExportSidebar,
		icon: 'clipboard',
	} );
} )(
	window.wp.plugins,
	window.wp.editPost,
	window.wp.element,
	window.wp.components,
	window.wp.data,
	window.wp.blocks,
	window.wp.i18n
);
