/**
 * Summit — Actions
 * Location: assets/js/actions.js
 *
 * Progressive enhancement for the utility-action language rendered by
 * parts/global/action.php and action-group.php:
 *
 *   share  — Web Share API where available. Elsewhere it becomes a quiet
 *            disclosure for the fallback set (Copy Link / Email /
 *            LinkedIn) rendered beside it.
 *   copy   — copies the page URL; the label briefly reads "Copied" and a
 *            polite live region announces it. No modal.
 *   print  — window.print().
 *
 * Buttons that need JavaScript are rendered with [hidden] and revealed
 * here, so nothing inert is ever shown. No dependencies; no analytics.
 *
 * Loaded in the footer (enqueued only when such an action is rendered).
 */

( function () {
    'use strict';

    var COPIED_FOR = 2200; // ms

    function toArray( list ) {
        return Array.prototype.slice.call( list );
    }

    // ── Copy link ────────────────────────────────────────────────────────
    function copyText( text ) {
        if ( navigator.clipboard && window.isSecureContext ) {
            return navigator.clipboard.writeText( text );
        }
        // Fallback for non-secure contexts (e.g. local development over http)
        return new Promise( function ( resolve, reject ) {
            var field = document.createElement( 'textarea' );
            field.value = text;
            field.setAttribute( 'readonly', '' );
            field.style.position = 'fixed';
            field.style.opacity  = '0';
            document.body.appendChild( field );
            field.select();
            try {
                document.execCommand( 'copy' ) ? resolve() : reject();
            } catch ( err ) {
                reject( err );
            }
            document.body.removeChild( field );
        } );
    }

    toArray( document.querySelectorAll( '[data-action="copy"]' ) ).forEach( function ( button ) {
        var label  = button.querySelector( '.action__label' );
        var status = button.querySelector( '.action__status' );
        var resting = label ? label.textContent : '';
        var timer   = null;

        button.hidden = false;

        button.addEventListener( 'click', function () {
            copyText( button.getAttribute( 'data-url' ) || window.location.href ).then( function () {
                if ( label )  { label.textContent = 'Copied'; }
                if ( status ) { status.textContent = 'Link copied to clipboard'; }
                button.classList.add( 'is-done' );
                clearTimeout( timer );
                timer = setTimeout( function () {
                    if ( label )  { label.textContent = resting; }
                    if ( status ) { status.textContent = ''; }
                    button.classList.remove( 'is-done' );
                }, COPIED_FOR );
            } ).catch( function () {
                if ( status ) { status.textContent = 'Copy failed — please copy the address from your browser'; }
            } );
        } );
    } );

    // ── Print ────────────────────────────────────────────────────────────
    toArray( document.querySelectorAll( '[data-action="print"]' ) ).forEach( function ( button ) {
        button.hidden = false;
        button.addEventListener( 'click', function () { window.print(); } );
    } );

    // ── Share ────────────────────────────────────────────────────────────
    var canShare = typeof navigator.share === 'function';

    toArray( document.querySelectorAll( '[data-action-share]' ) ).forEach( function ( wrap ) {
        var button   = wrap.querySelector( '[data-action="share"]' );
        var fallback = wrap.querySelector( '.action-share__fallback' );
        if ( ! button ) { return; }

        button.hidden = false;

        if ( canShare ) {
            // Native sheet; the fallback set is not needed.
            if ( fallback ) { fallback.hidden = true; }

            button.addEventListener( 'click', function () {
                navigator.share( {
                    title: button.getAttribute( 'data-title' ) || document.title,
                    url:   button.getAttribute( 'data-url' ) || window.location.href,
                } ).catch( function () { /* dismissed — nothing to do */ } );
            } );
            return;
        }

        // No Web Share: "Share" discloses the fallback set beside it.
        if ( ! fallback ) { return; }

        fallback.hidden = true;
        button.setAttribute( 'aria-expanded', 'false' );
        button.setAttribute( 'aria-controls', fallback.id );

        button.addEventListener( 'click', function () {
            var open = button.getAttribute( 'aria-expanded' ) === 'true';
            button.setAttribute( 'aria-expanded', open ? 'false' : 'true' );
            fallback.hidden = open;
            wrap.classList.toggle( 'is-open', ! open );
            if ( ! open ) {
                var first = fallback.querySelector( 'a, button:not([hidden])' );
                if ( first ) { first.focus(); }
            }
        } );

        wrap.addEventListener( 'keydown', function ( e ) {
            if ( ( e.key === 'Escape' || e.key === 'Esc' ) && ! fallback.hidden ) {
                button.setAttribute( 'aria-expanded', 'false' );
                fallback.hidden = true;
                wrap.classList.remove( 'is-open' );
                button.focus();
            }
        } );
    } );

}() );
