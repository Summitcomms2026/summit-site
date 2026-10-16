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

	document.querySelectorAll( '[data-fol-prose] img' ).forEach( function ( img ) {
		var settle = function () {
			if ( img.naturalWidth > 0 ) {
				img.classList.add( 'is-loaded' );
				return;
			}
			var box = img.closest( 'figure' ) || img;
			box.hidden = true;
			box.setAttribute( 'data-fol-withdrawn', '' );
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
