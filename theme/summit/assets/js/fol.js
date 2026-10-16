/**
 * The Future of Luxury — small enhancements
 * assets/js/fol.js (enqueued by inc/future-of-luxury.php on the section's views)
 *
 * 1. Essay images: each inline image in the reading column is shown once
 *    it has loaded; if it cannot load, its figure is withdrawn, so a dead
 *    remote image never leaves an empty frame.
 * 2. Subscribe: submits in place through admin-ajax and reports the
 *    handler's real response. Without this script the form posts normally.
 *
 * @package SummitTheme
 */
( function () {
	'use strict';

	// ── 1. Essay images ────────────────────────────────────────────────
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

	// ── 2. Subscribe ───────────────────────────────────────────────────
	var ajaxUrl = window.summitFol && window.summitFol.ajaxUrl;
	if ( ! ajaxUrl || ! window.fetch ) {
		return;
	}
	document.querySelectorAll( 'form[data-fol-subscribe]' ).forEach( function ( form ) {
		var input  = form.querySelector( 'input[type="email"]' );
		var button = form.querySelector( 'button[type="submit"]' );
		var status = form.querySelector( '.fol-subscribe__status' );
		var label  = button ? button.textContent : '';

		var say = function ( text, state ) {
			status.textContent = text;
			status.classList.toggle( 'is-error', 'error' === state );
			status.classList.toggle( 'is-sent', 'sent' === state );
		};

		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			if ( ! input.value || ! input.checkValidity() ) {
				say( 'Please enter a valid email address.', 'error' );
				input.setAttribute( 'aria-invalid', 'true' );
				input.focus();
				return;
			}
			input.removeAttribute( 'aria-invalid' );
			button.disabled = true;
			button.textContent = 'Sending…';
			say( '', '' );

			fetch( ajaxUrl, { method: 'POST', credentials: 'same-origin', body: new FormData( form ) } )
				.then( function ( r ) { return r.json(); } )
				.then( function ( result ) {
					if ( result && result.success ) {
						say( result.data && result.data.message ? result.data.message : 'Thank you.', 'sent' );
						form.classList.add( 'is-sent' );
						input.value = '';
					} else {
						say( result && result.data && result.data.message ? result.data.message : 'Something went wrong. Please try again.', 'error' );
					}
				} )
				.catch( function () {
					say( 'A network error occurred. Please try again.', 'error' );
				} )
				.finally( function () {
					button.disabled = false;
					button.textContent = label;
				} );
		} );
	} );
} )();
