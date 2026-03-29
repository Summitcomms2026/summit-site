/**
 * Summit — Scroll Reveal
 * assets/js/scroll-animate.js
 *
 * Watches [data-reveal] elements and adds .is-visible
 * when they enter the viewport. Respects prefers-reduced-motion.
 *
 * Attributes:
 *   data-reveal             fade + lift (default)
 *   data-reveal="fade"      fade only — no vertical movement
 *   data-reveal-delay="N"   N × 100ms stagger (1–6)
 *
 * No dependencies. Vanilla JS only.
 * Loaded in the footer — DOM is available immediately.
 */

( function () {
    'use strict';

    var elements = document.querySelectorAll( '[data-reveal]' );
    if ( ! elements.length ) { return; }

    // Reduced motion: reveal everything immediately, skip all animation
    if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
        elements.forEach( function ( el ) {
            el.classList.add( 'is-visible' );
        } );
        return;
    }

    var observer = new IntersectionObserver(
        function ( entries ) {
            entries.forEach( function ( entry ) {
                if ( entry.isIntersecting ) {
                    entry.target.classList.add( 'is-visible' );
                    observer.unobserve( entry.target ); // fire once only
                }
            } );
        },
        {
            threshold:  0.12,
            rootMargin: '0px 0px -48px 0px', // trigger slightly before fully in view
        }
    );

    elements.forEach( function ( el ) {
        observer.observe( el );
    } );

}() );
