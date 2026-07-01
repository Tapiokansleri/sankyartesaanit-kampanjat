( function ( element, components, i18n ) {
	const { createElement: el, useState, useMemo, Fragment } = element;
	const { Modal, Button, TextControl, SelectControl } = components;
	const { __ } = i18n;

	const STYLE_PREFIX = {
		solid: 'fa-solid',
		regular: 'fa-regular',
		brands: 'fa-brands',
	};

	function iconClass( icon ) {
		if ( ! icon || ! icon.name ) {
			return '';
		}
		const prefix = STYLE_PREFIX[ icon.style ] || STYLE_PREFIX.solid;
		return prefix + ' fa-' + icon.name;
	}

	function findIcon( icons, name, style ) {
		if ( ! name ) {
			return null;
		}
		return (
			icons.find( function ( item ) {
				return item.name === name && ( ! style || item.style === style );
			} ) ||
			icons.find( function ( item ) {
				return item.name === name;
			} ) ||
			null
		);
	}

	function FaIconPickerModal( props ) {
		const icons = window.kansleriSAFaIcons || [];
		const [ query, setQuery ] = useState( '' );

		const filtered = useMemo(
			function () {
				const q = query.trim().toLowerCase();
				if ( ! q ) {
					return icons;
				}
				return icons.filter( function ( icon ) {
					return (
						icon.name.indexOf( q ) !== -1 ||
						( icon.label && icon.label.toLowerCase().indexOf( q ) !== -1 )
					);
				} );
			},
			[ icons, query ]
		);

		if ( ! props.isOpen ) {
			return null;
		}

		return el(
			Modal,
			{
				title: __( 'Valitse Font Awesome -ikoni', 'kansleri-sankyartesaanit' ),
				onRequestClose: props.onClose,
				className: 'sa-fa-picker-modal',
				size: 'large',
			},
			el( TextControl, {
				label: __( 'Hae ikoneita', 'kansleri-sankyartesaanit' ),
				value: query,
				placeholder: __( 'Esim. star, truck, bed…', 'kansleri-sankyartesaanit' ),
				onChange: setQuery,
			} ),
			el(
				'p',
				{ className: 'sa-fa-picker-count' },
				filtered.length + ' ' + __( 'ikonia', 'kansleri-sankyartesaanit' )
			),
			el(
				'div',
				{ className: 'sa-fa-picker-grid', role: 'listbox' },
				filtered.map( function ( icon ) {
					const selected =
						props.currentName === icon.name && props.currentStyle === icon.style;
					return el(
						'button',
						{
							key: icon.style + '-' + icon.name,
							type: 'button',
							className:
								'sa-fa-picker-icon-btn' + ( selected ? ' is-selected' : '' ),
							title: icon.label || icon.name,
							onClick: function () {
								props.onSelect( icon.name, icon.style );
								props.onClose();
							},
						},
						el( 'i', { className: iconClass( icon ), 'aria-hidden': true } ),
						el( 'span', { className: 'sa-fa-picker-icon-label' }, icon.label || icon.name )
					);
				} )
			),
			el(
				'div',
				{ className: 'sa-fa-picker-actions' },
				el(
					Button,
					{
						variant: 'secondary',
						isDestructive: true,
						onClick: function () {
							props.onSelect( '', 'solid' );
							props.onClose();
						},
					},
					__( 'Poista ikoni', 'kansleri-sankyartesaanit' )
				),
				el(
					Button,
					{ variant: 'secondary', onClick: props.onClose },
					__( 'Peruuta', 'kansleri-sankyartesaanit' )
				)
			)
		);
	}

	function FaIconField( props ) {
		const icons = window.kansleriSAFaIcons || [];
		const [ isOpen, setIsOpen ] = useState( false );
		const iconName = props.iconName || '';
		const iconStyle = props.iconStyle || 'solid';
		const iconData = findIcon( icons, iconName, iconStyle );

		return el(
			Fragment,
			null,
			el( 'p', { className: 'components-base-control__label' }, props.label ),
			el(
				'div',
				{ className: 'sa-fa-field' },
				iconName
					? el(
							'span',
							{ className: 'sa-fa-field-preview' },
							el( 'i', {
								className: iconClass( iconData || { name: iconName, style: iconStyle } ),
								'aria-hidden': true,
							} ),
							el( 'code', null, iconName )
					  )
					: el(
							'span',
							{ className: 'sa-fa-field-empty' },
							__( 'Ei ikonia valittuna', 'kansleri-sankyartesaanit' )
					  ),
				el(
					Button,
					{ variant: 'secondary', onClick: function () { setIsOpen( true ); } },
					iconName
						? __( 'Vaihda ikoni', 'kansleri-sankyartesaanit' )
						: __( 'Valitse ikoni', 'kansleri-sankyartesaanit' )
				)
			),
			el( FaIconPickerModal, {
				isOpen: isOpen,
				onClose: function () { setIsOpen( false ); },
				currentName: iconName,
				currentStyle: iconStyle,
				onSelect: function ( name, style ) {
					props.onChange( name, style );
				},
			} )
		);
	}

	window.KansleriSA = window.KansleriSA || {};
	window.KansleriSA.FaIconField = FaIconField;
	window.KansleriSA.iconClass = iconClass;
} )( window.wp.element, window.wp.components, window.wp.i18n );
