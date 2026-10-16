/**
 * Subscribe — in-place submission for every subscribe form
 * assets/js/subscribe.js (enqueued by summit_subscribe_fields(), inc/subscribe.php)
 *
 * Forms opt in with data-summit-subscribe. The script submits through
 * admin-ajax and reports the handler's real response in the form's
 * [data-subscribe-status] element (a polite live region, inside the form
 * or beside it). Without it,
 * the form posts normally and the page shows the returned state.
 *
 * @package SummitTheme
 */
( function () {
	'use strict';

	var ajaxUrl = window.summitSubscribe && window.summitSubscribe.ajaxUrl;
	if ( ! ajaxUrl || ! window.fetch || ! window.FormData ) {
		return;
	}

	document.querySelectorAll( 'form[data-summit-subscribe]' ).forEach( function ( form ) {
		// The status line sits inside the form, or directly after it.
		var status = form.querySelector( '[data-subscribe-status]' ) || ( form.parentElement && form.parentElement.querySelector( '[data-subscribe-status]' ) );
		var button = form.querySelector( '[type="submit"]' );
		var label  = button ? button.textContent : '';

		var say = function ( text, state ) {
			if ( ! status ) {
				return;
			}
			status.textContent = text;
			status.classList.toggle( 'is-error', 'error' === state );
			status.classList.toggle( 'is-sent', 'sent' === state );
		};

		form.setAttribute( 'novalidate', '' );

		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();

			var invalid = form.querySelector( ':invalid' );
			form.querySelectorAll( '[aria-invalid]' ).forEach( function ( el ) { el.removeAttribute( 'aria-invalid' ); } );
			if ( invalid ) {
				invalid.setAttribute( 'aria-invalid', 'true' );
				say( 'email' === invalid.type ? 'Please enter a valid email address.' : 'Please complete the required fields.', 'error' );
				invalid.focus();
				return;
			}

			if ( button ) {
				button.disabled = true;
				button.textContent = 'Sending…';
			}
			say( '', '' );

			fetch( ajaxUrl, { method: 'POST', credentials: 'same-origin', body: new FormData( form ) } )
				.then( function ( r ) { return r.json(); } )
				.then( function ( result ) {
					var message = result && result.data && result.data.message;
					if ( result && result.success ) {
						say( message || 'You’re on the list.', 'sent' );
						form.classList.add( 'is-sent' );
						form.reset();
					} else {
						say( message || 'Something went wrong. Please try again.', 'error' );
					}
				} )
				.catch( function () {
					say( 'A network error occurred. Please try again.', 'error' );
				} )
				.finally( function () {
					if ( button ) {
						button.disabled = false;
						button.textContent = label;
					}
				} );
		} );
	} );
} )();
