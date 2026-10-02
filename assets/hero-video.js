/**
 * Hero background video: pause / play button, pausing while the hero is off screen,
 * and no autoplay for visitors who prefer reduced motion.
 */
( function () {
	function init( hero ) {
		var video = hero.querySelector( '.sa-hero__bg-video' );
		var toggle = hero.querySelector( '.sa-hero__video-toggle' );

		if ( ! video ) {
			return;
		}

		var reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
		var userPaused = false;
		var visible = true;

		function setToggle( paused ) {
			if ( ! toggle ) {
				return;
			}

			toggle.setAttribute( 'aria-pressed', paused ? 'true' : 'false' );
			toggle.setAttribute(
				'aria-label',
				( paused ? toggle.getAttribute( 'data-label-play' ) : toggle.getAttribute( 'data-label-pause' ) ) || ''
			);
		}

		function play() {
			var attempt = video.play();

			// Autoplay can be blocked (e.g. low power mode): show the poster and let the visitor press play.
			if ( attempt && typeof attempt.catch === 'function' ) {
				attempt.catch( function () {
					userPaused = true;
					setToggle( true );
				} );
			}
		}

		function sync() {
			if ( userPaused || ! visible || reduceMotion ) {
				video.pause();
			} else {
				play();
			}
		}

		if ( reduceMotion ) {
			userPaused = true;
			setToggle( true );
			video.removeAttribute( 'autoplay' );
			video.pause();
			return;
		}

		if ( toggle ) {
			toggle.addEventListener( 'click', function () {
				userPaused = ! userPaused;
				setToggle( userPaused );
				sync();
			} );
		}

		if ( 'IntersectionObserver' in window ) {
			new IntersectionObserver(
				function ( entries ) {
					visible = entries[ entries.length - 1 ].isIntersecting;
					sync();
				},
				{ threshold: 0.05 }
			).observe( hero );
		}
	}

	function ready() {
		document.querySelectorAll( '.sa-hero--video-bg' ).forEach( init );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', ready );
	} else {
		ready();
	}
} )();
