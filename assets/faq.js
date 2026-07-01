( function () {
	var DURATION = 320;

	function setAnswerHeight( answer, height ) {
		answer.style.height = height;
	}

	function clearAnswerHeight( answer ) {
		answer.style.height = '';
	}

	function animateFaqItem( item, shouldOpen ) {
		var answer = item.querySelector( '.sa-faq__answer' );
		var inner  = item.querySelector( '.sa-faq__answer-inner' );

		if ( ! answer || ! inner ) {
			item.open = shouldOpen;
			return;
		}

		if ( item.classList.contains( 'is-animating' ) ) {
			return;
		}

		item.classList.add( 'is-animating' );

		if ( shouldOpen ) {
			item.open = true;
			setAnswerHeight( answer, '0px' );
			answer.offsetHeight; // Force reflow.
			setAnswerHeight( answer, inner.offsetHeight + 'px' );

			window.setTimeout( function () {
				if ( item.open ) {
					clearAnswerHeight( answer );
				}
				item.classList.remove( 'is-animating' );
			}, DURATION );

			return;
		}

		setAnswerHeight( answer, inner.offsetHeight + 'px' );
		answer.offsetHeight; // Force reflow.
		setAnswerHeight( answer, '0px' );

		window.setTimeout( function () {
			item.open = false;
			clearAnswerHeight( answer );
			item.classList.remove( 'is-animating' );
		}, DURATION );
	}

	function bindFaq( root ) {
		root.querySelectorAll( '.sa-faq__item' ).forEach( function ( item ) {
			var summary = item.querySelector( '.sa-faq__question' );

			if ( ! summary || summary.dataset.saFaqBound ) {
				return;
			}

			summary.dataset.saFaqBound = '1';

			summary.addEventListener( 'click', function ( event ) {
				event.preventDefault();
				animateFaqItem( item, ! item.open );
			} );
		} );
	}

	function init() {
		document.querySelectorAll( '.sa-faq' ).forEach( bindFaq );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
