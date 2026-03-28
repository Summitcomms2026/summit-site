/**
 * Summit Navigation
 *
 * Keyboard handling for the mobile navigation menu.
 * Closes the menu on Escape and returns focus to the toggle button.
 */
( function () {
    'use strict';

    var nav    = document.getElementById( 'site-nav' );
    var toggle = document.querySelector( '.site-header__toggle' );

    if ( ! nav || ! toggle ) {
        return;
    }

    document.addEventListener( 'keydown', function ( e ) {
        if ( e.key !== 'Escape' ) {
            return;
        }

        if ( toggle.getAttribute( 'aria-expanded' ) !== 'true' ) {
            return;
        }

        toggle.setAttribute( 'aria-expanded', 'false' );
        nav.setAttribute( 'data-open', 'false' );
        toggle.focus();
    } );
} )();
