/**
 * Interactive article tools: checklist (count of ticked items), range (value bands)
 * and choice (highest total score). The result updates as soon as the reader answers.
 */
( function () {
	'use strict';

	function show( root, result ) {
		var box = root.querySelector( '.sa-tool__result' );
		if ( ! result ) {
			box.hidden = true;
			return;
		}
		box.querySelector( '.sa-tool__result-title' ).textContent = result.title || '';
		box.querySelector( '.sa-tool__result-text' ).textContent = result.text || '';
		var cta = box.querySelector( '.sa-tool__cta' );
		if ( result.ctaText && result.ctaUrl ) {
			cta.textContent = result.ctaText;
			cta.setAttribute( 'href', result.ctaUrl );
			cta.hidden = false;
		} else {
			cta.hidden = true;
		}
		box.hidden = false;
	}

	function init( root ) {
		var dataEl = root.querySelector( '.sa-tool__data' );
		if ( ! dataEl ) {
			return;
		}
		var data;
		try {
			data = JSON.parse( dataEl.textContent );
		} catch ( e ) {
			return;
		}
		var form = root.querySelector( '.sa-tool__form' );

		if ( data.type === 'checklist' ) {
			var touched = false;
			var results = ( data.results || [] ).slice().sort( function ( a, b ) {
				return ( a.min || 0 ) - ( b.min || 0 );
			} );
			form.addEventListener( 'change', function () {
				touched = true;
				var count = form.querySelectorAll( 'input[type="checkbox"]:checked' ).length;
				var pick = null;
				results.forEach( function ( r ) {
					if ( count >= ( r.min || 0 ) ) {
						pick = r;
					}
				} );
				show( root, touched ? pick : null );
			} );
		}

		if ( data.type === 'range' ) {
			var range = root.querySelector( '.sa-tool__range' );
			var out = root.querySelector( '.sa-tool__value' );
			var unit = data.unit ? ' ' + data.unit : '';
			var update = function () {
				var value = Number( range.value );
				out.textContent = value.toLocaleString( 'fi-FI' ) + unit;
				var pick = null;
				( data.results || [] ).some( function ( r ) {
					if ( value <= Number( r.max ) ) {
						pick = r;
						return true;
					}
					return false;
				} );
				show( root, pick || ( data.results || [] )[ ( data.results || [] ).length - 1 ] );
			};
			range.addEventListener( 'input', update );
			update();
		}

		if ( data.type === 'choice' ) {
			var questions = root.querySelectorAll( '.sa-tool__question' );
			var progress = root.querySelector( '.sa-tool__progress' );
			var keys = Object.keys( data.outcomes || {} );
			var updateChoice = function () {
				var answered = form.querySelectorAll( 'input[type="radio"]:checked' );
				var left = questions.length - answered.length;
				if ( left > 0 ) {
					progress.textContent = left === 1 ? 'Vastaa vielä yhteen kysymykseen.' : 'Vastaa vielä ' + left + ' kysymykseen.';
					progress.hidden = false;
					show( root, null );
					return;
				}
				progress.hidden = true;
				var totals = {};
				keys.forEach( function ( k ) {
					totals[ k ] = 0;
				} );
				Array.prototype.forEach.call( answered, function ( input ) {
					var score = {};
					try {
						score = JSON.parse( input.getAttribute( 'data-score' ) || '{}' );
					} catch ( e ) {}
					Object.keys( score ).forEach( function ( k ) {
						totals[ k ] = ( totals[ k ] || 0 ) + Number( score[ k ] );
					} );
				} );
				var best = keys[ 0 ];
				keys.forEach( function ( k ) {
					if ( totals[ k ] > totals[ best ] ) {
						best = k;
					}
				} );
				show( root, data.outcomes[ best ] );
			};
			form.addEventListener( 'change', updateChoice );
			updateChoice();
		}
	}

	function boot() {
		Array.prototype.forEach.call( document.querySelectorAll( '[data-sa-tool]' ), init );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
