/**
 * Summit — Value Proposition scroll-direction colour shift
 * assets/js/vp-scroll-direction.js
 *
 * Toggles .vp--scroll-up on the VP panel based on scroll direction.
 * Dark when scrolling down (default), light when scrolling up.
 * Only active while the panel is in the viewport.
 *
 * No dependencies. Loaded on the front page only.
 */

( function () {
    'use strict';

    var vp = document.querySelector( '.vp-block' );
    if ( ! vp ) { return; }

    if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
        return;
    }

    var lastY = window.scrollY;

    window.addEventListener( 'scroll', function () {
        var y    = window.scrollY;
        var rect = vp.getBoundingClientRect();
        var inView = rect.top < window.innerHeight && rect.bottom > 0;

        if ( inView ) {
            if ( y < lastY ) {
                vp.classList.add( 'vp--scroll-up' );
            } else if ( y > lastY ) {
                vp.classList.remove( 'vp--scroll-up' );
            }
        }

        lastY = y;
    }, { passive: true } );
}() );
