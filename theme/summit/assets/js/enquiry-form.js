/**
 * Summit Enquiry Form — AJAX submission handler
 *
 * Submits the Design Tomorrow contact form via WP AJAX,
 * shows success/error feedback without page reload.
 *
 * @package SummitTheme
 */

( function () {
	'use strict';

	var form = document.getElementById( 'summit-enquiry-form' );
	if ( ! form ) return;

	var submitBtn   = form.querySelector( '.dt-form__submit' );
	var successEl   = form.querySelector( '.dt-form__success' );
	var errorEl     = form.querySelector( '.dt-form__error' );
	var originalTxt = submitBtn ? submitBtn.textContent : '';

	form.addEventListener( 'submit', function ( e ) {
		e.preventDefault();

		// Reset feedback state
		successEl.hidden = true;
		errorEl.hidden   = true;

		// Client-side validation
		if ( ! form.checkValidity() ) {
			form.reportValidity();
			return;
		}

		// Disable button during submission
		submitBtn.disabled    = true;
		submitBtn.textContent = 'Sending\u2026';

		var data = new FormData( form );

		fetch( summitEnquiry.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: data,
		} )
			.then( function ( response ) {
				return response.json();
			} )
			.then( function ( result ) {
				if ( result.success ) {
					successEl.hidden = false;
					form.reset();
				} else {
					errorEl.textContent = ( result.data && result.data.message )
						? result.data.message
						: 'Something went wrong. Please try again.';
					errorEl.hidden = false;
				}
			} )
			.catch( function () {
				errorEl.textContent = 'A network error occurred. Please check your connection and try again.';
				errorEl.hidden = false;
			} )
			.finally( function () {
				submitBtn.disabled    = false;
				submitBtn.textContent = originalTxt;
			} );
	} );
} )();
