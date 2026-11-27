/**
 * Valinta-apuri: filters the model list in real time and, on the same page,
 * the comparison cards (.sa-model) and table rows (.sa-table) of the chosen models.
 */
( function () {
	'use strict';

	var VISIBLE = 6;

	function slugFromLink( el ) {
		var link = el && el.querySelector( 'a[href*="/tuote/"]' );
		if ( ! link ) {
			return '';
		}
		var match = link.getAttribute( 'href' ).match( /\/tuote\/([^/?#]+)/ );
		return match ? match[ 1 ] : '';
	}

	function init( root ) {
		var form = root.querySelector( '.sa-finder__form' );
		var items = Array.prototype.slice.call( root.querySelectorAll( '.sa-finder__item' ) );
		var countNum = root.querySelector( '.sa-finder__count-num' );
		var countText = root.querySelector( '.sa-finder__count-text' );
		var more = root.querySelector( '.sa-finder__more' );
		var empty = root.querySelector( '.sa-finder__empty' );
		var jump = root.querySelector( '.sa-finder__jump' );
		var expanded = false;

		var cards = Array.prototype.slice.call( document.querySelectorAll( '.sa-model' ) ).map( function ( el ) {
			return { el: el, slug: slugFromLink( el ) };
		} );
		var rows = Array.prototype.slice.call( document.querySelectorAll( '.sa-table tbody tr' ) ).map( function ( el ) {
			return { el: el, slug: slugFromLink( el ) };
		} );

		function state() {
			var s = { type: 'any', sleep: 'any', feel: 'any', material: 'any' };
			Array.prototype.forEach.call( form.querySelectorAll( 'input[type="radio"]:checked' ), function ( input ) {
				s[ input.getAttribute( 'data-q' ) ] = input.value;
			} );
			return s;
		}

		function matches( d, s ) {
			var feels = ( d.feels || '' ).split( ' ' );
			if ( s.type === 'best' ? d.ultra !== '1' : s.type !== 'any' && d.type !== s.type ) {
				return false;
			}
			if ( s.sleep === 'yes' && d.sw === 'no' ) {
				return false;
			}
			if ( s.sleep === 'no' && d.sw === 'yes' ) {
				return false;
			}
			if ( s.feel === 'soft' && feels.indexOf( 'soft' ) === -1 ) {
				return false;
			}
			if ( s.feel === 'fx' && feels.indexOf( 'fx' ) === -1 ) {
				return false;
			}
			if ( s.feel === 'firm' && feels.indexOf( 'medium' ) === -1 && feels.indexOf( 'hard' ) === -1 ) {
				return false;
			}
			if ( s.feel === 'xhard' && feels.indexOf( 'xhard' ) === -1 ) {
				return false;
			}
			if ( s.material === 'natural' && d.natural !== 'yes' ) {
				return false;
			}
			return true;
		}

		function update() {
			var s = state();
			var active = s.type !== 'any' || s.sleep !== 'any' || s.feel !== 'any' || s.material !== 'any';
			var ok = {};
			var shown = 0;
			var total = 0;

			items.forEach( function ( item ) {
				var hit = matches( item.dataset, s );
				if ( hit ) {
					total++;
					ok[ item.dataset.slug ] = true;
				}
				var visible = hit && ( expanded || shown < VISIBLE );
				if ( visible ) {
					shown++;
				}
				item.hidden = ! visible;
			} );

			countNum.textContent = total;
			countText.textContent = total === 1 ? 'malli sopii valintoihisi' : 'mallia sopii valintoihisi';
			empty.hidden = total !== 0;

			if ( total > VISIBLE ) {
				more.hidden = false;
				more.textContent = expanded ? 'Näytä vähemmän' : 'Näytä kaikki ' + total;
			} else {
				more.hidden = true;
			}

			// Same page: comparison cards and table rows follow the selection.
			cards.forEach( function ( card ) {
				if ( card.slug ) {
					card.el.classList.toggle( 'is-filtered-out', active && ! ok[ card.slug ] );
				}
			} );
			Array.prototype.forEach.call( document.querySelectorAll( '.sa-band' ), function ( band ) {
				var bandCards = band.querySelectorAll( '.sa-model' );
				if ( ! bandCards.length ) {
					return;
				}
				var visible = band.querySelectorAll( '.sa-model:not(.is-filtered-out)' ).length;
				band.classList.toggle( 'is-filtered-empty', visible === 0 );
			} );
			rows.forEach( function ( row ) {
				if ( row.slug ) {
					row.el.classList.toggle( 'is-filtered-out', active && ! ok[ row.slug ] );
				}
			} );

			if ( jump ) {
				jump.hidden = ! ( active && total > 0 && cards.length );
			}
		}

		form.addEventListener( 'change', update );
		form.addEventListener( 'reset', function () {
			expanded = false;
			window.setTimeout( update, 0 );
		} );
		more.addEventListener( 'click', function () {
			expanded = ! expanded;
			update();
		} );
		if ( jump ) {
			jump.addEventListener( 'click', function ( event ) {
				var first = document.querySelector( '.sa-model:not(.is-filtered-out)' );
				if ( first ) {
					event.preventDefault();
					var band = first.closest( '.sa-band' ) || first;
					band.scrollIntoView( { behavior: 'smooth', block: 'start' } );
				}
			} );
		}

		update();
	}

	function boot() {
		Array.prototype.forEach.call( document.querySelectorAll( '[data-sa-finder]' ), init );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
