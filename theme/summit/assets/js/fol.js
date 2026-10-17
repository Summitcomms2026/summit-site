/**
 * The Future of Luxury — small enhancements
 * assets/js/fol.js (enqueued by inc/future-of-luxury.php on the section's views)
 *
 * Essay images: each inline image in the reading column is shown once it
 * has loaded; if it cannot load, its figure is withdrawn, so a dead remote
 * image never leaves an empty frame. (Subscribe forms are handled by the
 * site-wide assets/js/subscribe.js.)
 *
 * @package SummitTheme
 */
( function () {
	'use strict';

	var images  = document.querySelectorAll( '[data-fol-prose] img' );
	var pending = images.length;

	// Where an essay has lost more than one of its photographs, say so once,
	// where the first one stood, rather than leaving silent gaps.
	var acknowledge = function () {
		var gone = document.querySelectorAll( '[data-fol-withdrawn]' );
		if ( gone.length < 2 ) {
			return;
		}
		var note = document.createElement( 'p' );
		note.className = 'fol-prose__absence';
		note.textContent = 'The photographs first published with this essay are not shown here.';
		gone[ 0 ].parentNode.insertBefore( note, gone[ 0 ] );
	};

	images.forEach( function ( img ) {
		var done = false;
		var settle = function () {
			if ( done ) {
				return;
			}
			done = true;
			if ( img.naturalWidth > 0 ) {
				img.classList.add( 'is-loaded' );
			} else {
				var box = img.closest( 'figure' ) || img;
				box.hidden = true;
				box.setAttribute( 'data-fol-withdrawn', '' );
			}
			if ( --pending === 0 ) {
				acknowledge();
			}
		};
		img.loading = 'lazy';
		if ( img.complete ) {
			settle();
		} else {
			img.addEventListener( 'load', settle, { once: true } );
			img.addEventListener( 'error', settle, { once: true } );
		}
	} );
} )();
