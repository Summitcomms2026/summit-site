/**
 * Summit — Solutions: three ways in
 * assets/js/solutions.js
 *
 * Progressive enhancement of the Solutions overview's lens index:
 *
 *   - The three lenses (By Industry / By Function / By Need) become a
 *     switcher: one lens's panel shows at a time, in place. ARIA tabs
 *     semantics; ← → Home End move between lenses. The URL hash follows
 *     the selected lens, and a hash on arrival selects it.
 *   - By Industry: the visual field follows the sector in hover or focus.
 *   - By Need: the held statement follows the need in hover or focus.
 *
 * Without this script every panel shows in sequence and the lenses are
 * plain in-page links. Motion is CSS-only and respects reduced motion.
 * No dependencies.
 */

( function () {
	'use strict';

	var root = document.querySelector( '[data-sx-ways]' );
	if ( ! root ) { return; }

	var list   = root.querySelector( '[data-sx-lenses]' );
	var tabs   = Array.prototype.slice.call( root.querySelectorAll( '[data-sx-lens]' ) );
	var panels = tabs.map( function ( tab ) {
		return document.getElementById( tab.getAttribute( 'href' ).slice( 1 ) );
	} );

	if ( ! list || ! tabs.length || panels.indexOf( null ) !== -1 ) { return; }

	// ── Lenses → tabs ────────────────────────────────────────────────────
	list.setAttribute( 'role', 'tablist' );

	tabs.forEach( function ( tab, i ) {
		var panel = panels[ i ];
		tab.setAttribute( 'role', 'tab' );
		tab.setAttribute( 'aria-controls', panel.id );
		tab.id = tab.id || panel.id + '-tab';
		tab.parentNode.setAttribute( 'role', 'presentation' );
		panel.setAttribute( 'role', 'tabpanel' );
		panel.setAttribute( 'aria-labelledby', tab.id );
	} );

	function select( index, focus, fromUser ) {
		tabs.forEach( function ( tab, i ) {
			var on = i === index;
			tab.setAttribute( 'aria-selected', on ? 'true' : 'false' );
			tab.setAttribute( 'tabindex', on ? '0' : '-1' );
			tab.classList.toggle( 'is-active', on );
			panels[ i ].classList.toggle( 'is-active', on );
			if ( on ) {
				panels[ i ].removeAttribute( 'inert' );
			} else {
				panels[ i ].setAttribute( 'inert', '' );
			}
		} );
		if ( focus ) { tabs[ index ].focus(); }
		if ( fromUser && window.history && history.replaceState ) {
			history.replaceState( null, '', '#' + panels[ index ].id );
		}
	}

	tabs.forEach( function ( tab, i ) {
		tab.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			select( i, false, true );
		} );
		tab.addEventListener( 'keydown', function ( e ) {
			var next = null;
			if ( 'ArrowRight' === e.key || 'ArrowDown' === e.key ) { next = ( i + 1 ) % tabs.length; }
			if ( 'ArrowLeft' === e.key || 'ArrowUp' === e.key ) { next = ( i - 1 + tabs.length ) % tabs.length; }
			if ( 'Home' === e.key ) { next = 0; }
			if ( 'End' === e.key ) { next = tabs.length - 1; }
			if ( null !== next ) {
				e.preventDefault();
				select( next, true, true );
			}
		} );
	} );

	// Initial lens: the hash, else the declared default, else the first.
	var start = 0;
	var wanted = window.location.hash.slice( 1 );
	panels.forEach( function ( panel, i ) {
		if ( panel.id === wanted ) { start = i; }
		else if ( ! wanted && panel.hasAttribute( 'data-sx-default' ) ) { start = i; }
	} );

	root.classList.add( 'is-enhanced' );
	select( start, false, false );

	// ── Follow-the-pointer: one active item drives a held field ──────────
	function follow( scope, itemSelector, targetSelector ) {
		var items   = Array.prototype.slice.call( scope.querySelectorAll( itemSelector ) );
		var targets = Array.prototype.slice.call( scope.querySelectorAll( targetSelector ) );
		if ( ! items.length || items.length !== targets.length ) { return; }

		function activate( index ) {
			items.forEach( function ( item, i ) { item.classList.toggle( 'is-active', i === index ); } );
			targets.forEach( function ( target, i ) { target.classList.toggle( 'is-active', i === index ); } );
		}

		items.forEach( function ( item, i ) {
			item.addEventListener( 'mouseenter', function () { activate( i ); } );
			item.addEventListener( 'focus', function () { activate( i ); } );
		} );
		activate( 0 );
	}

	panels.forEach( function ( panel ) {
		follow( panel, '[data-sx-sector]', '[data-sx-frame]' );
		follow( panel, '[data-sx-need]', '[data-sx-statement]' );
	} );
}() );
