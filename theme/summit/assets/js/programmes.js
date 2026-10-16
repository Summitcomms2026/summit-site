/**
 * Summit — Programmes: the held field
 * assets/js/programmes.js
 *
 * On the Programmes overview, the register's held field shows the moment of
 * the programme in hover or focus (keyboard focus anywhere inside an entry
 * counts). Without this script the moment reads inline in each entry and
 * the held field stays hidden. Motion is CSS-only and respects reduced
 * motion. No dependencies.
 */

( function () {
	'use strict';

	var root = document.querySelector( '[data-prg-register]' );
	if ( ! root ) { return; }

	var entries = Array.prototype.slice.call( root.querySelectorAll( '[data-prg-entry]' ) );
	var held    = Array.prototype.slice.call( root.querySelectorAll( '[data-prg-held]' ) );
	if ( ! entries.length || entries.length !== held.length ) { return; }

	function activate( index ) {
		entries.forEach( function ( entry, i ) { entry.classList.toggle( 'is-active', i === index ); } );
		held.forEach( function ( item, i ) { item.classList.toggle( 'is-active', i === index ); } );
	}

	entries.forEach( function ( entry, i ) {
		entry.addEventListener( 'mouseenter', function () { activate( i ); } );
		entry.addEventListener( 'focusin', function () { activate( i ); } );
	} );

	root.classList.add( 'is-enhanced' );
	activate( 0 );
}() );
