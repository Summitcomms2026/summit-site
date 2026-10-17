/**
 * Summit — editorial components
 * assets/js/editorial.js (enqueued on pages by inc/editorial.php)
 *
 * Every component here is complete without JavaScript; this only makes the
 * conversation guides interactive. Nothing is scored, nothing is sent and
 * nothing is stored beyond this browser tab: a guide's summary waits in
 * sessionStorage until the visitor opens Let's Talk, where it appears in
 * the message for them to edit or delete.
 *
 *   FAQ          native <details>; on small screens one answer open at a time
 *   Guides       .summit-guide--choice / --toggle: choose one option to see
 *                its questions; the page's Let's Talk links carry the choice
 *   Checklist    .summit-guide--checklist: Reviewed / Partly Reviewed / Not
 *                Yet Tested / Unsure per area; returns the questions for the
 *                areas not yet reviewed — never a grade
 *   Check        [data-summit-dqc]: the Demand Quality Check
 *   Let's Talk   [data-summit-summary-target]: places a waiting summary
 */
( function () {
	'use strict';

	var KEY = 'summit-enquiry-summary';
	var FRESH = 30 * 60 * 1000;   // a summary waits half an hour, then lapses
	var store = {
		set: function ( value ) {
			try {
				window.sessionStorage.setItem( KEY, JSON.stringify( { t: Date.now(), text: value } ) );
			} catch ( e ) { /* private mode: the link still works */ }
		},
		take: function () {
			try {
				var v = JSON.parse( window.sessionStorage.getItem( KEY ) || 'null' );
				window.sessionStorage.removeItem( KEY );
				return v && Date.now() - v.t < FRESH ? v.text : null;
			} catch ( e ) {
				return null;
			}
		}
	};

	function text( el ) {
		return el ? el.textContent.replace( /\s+/g, ' ' ).trim() : '';
	}

	function sectionTitle( el ) {
		var section = el.closest( 'section' );
		var h2 = section && section.querySelector( 'h2' );
		return text( h2 );
	}

	// The page's Let's Talk links carry the visitor's choice as context
	function setContext( value ) {
		document.querySelectorAll( 'main a[href*="/lets-talk"]' ).forEach( function ( a ) {
			try {
				var url = new URL( a.href, window.location.href );
				if ( value ) {
					url.searchParams.set( 'context', value );
				} else {
					url.searchParams.delete( 'context' );
				}
				a.href = url.toString();
			} catch ( e ) { /* leave the link as it is */ }
		} );
	}

	function copy( value, status ) {
		var done = function () { if ( status ) { status.textContent = 'Copied.'; } };
		if ( navigator.clipboard && navigator.clipboard.writeText ) {
			navigator.clipboard.writeText( value ).then( done, function () { fallbackCopy( value ); done(); } );
		} else {
			fallbackCopy( value );
			done();
		}
	}

	function fallbackCopy( value ) {
		var t = document.createElement( 'textarea' );
		t.value = value;
		t.setAttribute( 'readonly', '' );
		t.style.position = 'absolute';
		t.style.left = '-9999px';
		document.body.appendChild( t );
		t.select();
		try { document.execCommand( 'copy' ); } catch ( e ) { /* nothing more to try */ }
		document.body.removeChild( t );
	}

	/* ── FAQ ───────────────────────────────────────────────────────────── */
	var small = window.matchMedia( '(max-width: 767px)' );
	document.querySelectorAll( '.summit-faq' ).forEach( function ( faq ) {
		faq.addEventListener( 'toggle', function ( e ) {
			var item = e.target;
			if ( ! item.open || ! small.matches ) {
				return;
			}
			faq.querySelectorAll( 'details[open]' ).forEach( function ( other ) {
				if ( other !== item ) {
					other.open = false;
				}
			} );
		}, true );
	} );

	/* ── Choice guides and the two-state explainer ─────────────────────── */
	document.querySelectorAll( '.summit-guide--choice, .summit-guide--toggle' ).forEach( function ( guide, g ) {
		var options = Array.prototype.slice.call( guide.querySelectorAll( '.summit-guide__option' ) );
		if ( ! options.length ) {
			return;
		}
		var title = sectionTitle( guide );
		var list = document.createElement( 'ul' );
		list.className = 'summit-guide__choices';
		list.setAttribute( 'role', 'list' );
		var buttons = [];

		options.forEach( function ( option, i ) {
			var heading = option.querySelector( 'h3' );
			var id = 'summit-guide-' + g + '-' + i;
			option.id = id;
			option.hidden = true;
			option.setAttribute( 'tabindex', '-1' );
			var li = document.createElement( 'li' );
			var b = document.createElement( 'button' );
			b.type = 'button';
			b.className = 'summit-guide__choice';
			b.textContent = text( heading );
			b.setAttribute( 'aria-pressed', 'false' );
			b.setAttribute( 'aria-controls', id );
			b.addEventListener( 'click', function () { choose( i ); } );
			li.appendChild( b );
			list.appendChild( li );
			buttons.push( b );
		} );

		var clear = document.createElement( 'button' );
		clear.type = 'button';
		clear.className = 'summit-guide__clear';
		clear.textContent = 'Clear selection';
		clear.hidden = true;
		clear.addEventListener( 'click', function () { choose( -1 ); buttons[ 0 ].focus(); } );

		var wrap = guide.querySelector( '.summit-guide__options' );
		wrap.parentNode.insertBefore( list, wrap );
		wrap.parentNode.insertBefore( clear, wrap.nextSibling );
		guide.classList.add( 'is-enhanced' );

		function choose( n ) {
			options.forEach( function ( option, i ) {
				option.hidden = i !== n;
				buttons[ i ].setAttribute( 'aria-pressed', i === n ? 'true' : 'false' );
			} );
			clear.hidden = n < 0;
			if ( n < 0 ) {
				setContext( '' );
				return;
			}
			var chosen = options[ n ];
			var label = text( chosen.querySelector( 'h3' ) );
			var lines = Array.prototype.map.call( chosen.querySelectorAll( 'p' ), text ).join( '\n' );
			setContext( label );
			store.set( title + ' — ' + label + '\n' + lines );
		}
	} );

	/* ── Checklist ─────────────────────────────────────────────────────── */
	var STATES = [ 'Reviewed', 'Partly Reviewed', 'Not Yet Tested', 'Unsure' ];
	document.querySelectorAll( '.summit-guide--checklist' ).forEach( function ( guide, g ) {
		var options = Array.prototype.slice.call( guide.querySelectorAll( '.summit-guide__option' ) );
		var action = guide.querySelector( '.summit-guide__action' );
		if ( ! options.length || ! action ) {
			return;
		}
		var title = sectionTitle( guide );
		options.forEach( function ( option, i ) {
			var area = text( option.querySelector( 'h3' ) );
			var set = document.createElement( 'fieldset' );
			set.className = 'summit-guide__states';
			var legend = document.createElement( 'legend' );
			legend.textContent = area;
			set.appendChild( legend );
			STATES.forEach( function ( state ) {
				var label = document.createElement( 'label' );
				var input = document.createElement( 'input' );
				input.type = 'radio';
				input.name = 'summit-check-' + g + '-' + i;
				input.value = state;
				label.appendChild( input );
				label.appendChild( document.createTextNode( ' ' + state ) );
				set.appendChild( label );
			} );
			option.appendChild( set );
		} );

		var button = document.createElement( 'button' );
		button.type = 'button';
		button.className = 'btn btn--primary btn--compact summit-guide__submit';
		button.textContent = text( action );
		action.replaceWith( button );

		var result = document.createElement( 'div' );
		result.className = 'summit-guide__result';
		result.setAttribute( 'tabindex', '-1' );
		result.hidden = true;
		button.insertAdjacentElement( 'afterend', result );
		guide.classList.add( 'is-enhanced' );

		button.addEventListener( 'click', function () {
			result.innerHTML = '';
			var summary = [ title ];
			options.forEach( function ( option, i ) {
				var picked = option.querySelector( 'input[name="summit-check-' + g + '-' + i + '"]:checked' );
				var state = picked ? picked.value : 'Unsure';
				var area = text( option.querySelector( 'h3' ) );
				summary.push( area + ': ' + state );
				if ( 'Reviewed' === state ) {
					return;
				}
				var h = document.createElement( 'h4' );
				h.textContent = area + ' · ' + state;
				result.appendChild( h );
				Array.prototype.forEach.call( option.querySelectorAll( 'p' ), function ( p ) {
					var q = document.createElement( 'p' );
					q.textContent = text( p );
					result.appendChild( q );
					summary.push( '  ' + text( p ) );
				} );
			} );
			if ( ! result.childNodes.length ) {
				result.hidden = true;
				return;
			}
			result.hidden = false;
			result.focus();
			store.set( summary.join( '\n' ) );
		} );
	} );

	/* ── The Demand Quality Check ──────────────────────────────────────── */
	document.querySelectorAll( '[data-summit-dqc]' ).forEach( function ( tool ) {
		var specEl = tool.querySelector( '[data-dqc-spec]' );
		var spec;
		try { spec = JSON.parse( specEl.textContent ); } catch ( e ) { return; }
		var form = tool.querySelector( 'form' );
		var progress = tool.querySelector( '[data-dqc-progress]' );
		var result = tool.querySelector( '[data-dqc-result]' );
		var fallback = tool.parentNode.querySelector( '[data-dqc-fallback]' );
		var status = tool.querySelector( '[data-dqc-status]' );
		var summaryText = '';
		tool.hidden = false;
		if ( fallback ) {
			fallback.hidden = true;
		}

		function answers() {
			var a = {};
			spec.questions.forEach( function ( q ) {
				var c = form.querySelector( 'input[name="dqc-' + q.key + '"]:checked' );
				a[ q.key ] = c ? c.value : '';
			} );
			return a;
		}

		form.addEventListener( 'change', function () {
			var a = answers();
			var n = Object.keys( a ).filter( function ( k ) { return a[ k ]; } ).length;
			progress.textContent = n + ' of ' + spec.questions.length + ' answered';
		} );

		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			var a = answers();
			if ( ! a.exposed ) {
				var first = form.querySelector( 'input[name="dqc-exposed"]' );
				progress.textContent = 'Choose what feels most exposed to see a reading.';
				if ( first ) { first.focus(); }
				return;
			}
			var c = spec.considerations[ a.exposed ];
			var before = spec.before[ a.decision || 'None yet' ];
			tool.querySelector( '[data-dqc-lead]' ).textContent = c[ 0 ];
			tool.querySelector( '[data-dqc-consideration]' ).textContent = c[ 1 ];
			tool.querySelector( '[data-dqc-before]' ).textContent = before;
			var lines = [ 'Demand Quality Check' ];
			spec.questions.forEach( function ( q ) {
				lines.push( q.text + ' ' + ( a[ q.key ] || '—' ) );
			} );
			lines.push( '', 'Your main consideration: ' + c[ 0 ], c[ 1 ], '', 'Before you decide… ' + before );
			summaryText = lines.join( '\n' );
			store.set( summaryText );    // any Let's Talk link on the page carries it
			result.hidden = false;
			status.textContent = '';
			result.focus();
		} );

		tool.querySelector( '[data-dqc-discuss]' ).addEventListener( 'click', function () {
			if ( summaryText ) {
				store.set( summaryText );
			}
		} );
		tool.querySelector( '[data-dqc-copy]' ).addEventListener( 'click', function () {
			if ( summaryText ) {
				copy( summaryText, status );
			}
		} );
	} );

	/* ── Let's Talk: a waiting summary, for the visitor to edit ────────── */
	var target = document.querySelector( '[data-summit-summary-target]' );
	if ( target && ! target.value ) {
		var waiting = store.take();
		if ( waiting ) {
			target.value = waiting + '\n\n';
		}
	}
}() );
