/**
 * Summit Navigation — Mega Menu Interaction
 * Location: assets/js/nav.js
 *
 * Handles:
 *   - Menu open / close toggle (the header's menu button is also the
 *     close control while the overlay is open)
 *   - aria-expanded / aria-hidden state management
 *   - Escape: first clears an open section, then closes the menu
 *   - Backdrop click to close
 *   - Body scroll lock when menu is open
 *   - Focus containment: the header bar + overlay; the rest of the page
 *     is made inert while the menu is open
 *   - Restores focus to the trigger button on close
 *   - Contextual section states (Solutions, The Observatory):
 *       desktop — hover (with a short intent delay) or keyboard focus on a
 *                 parent label shows its panel in the right-hand region;
 *                 any other primary label returns the editorial rail.
 *                 Clicking a parent label always navigates to it.
 *       small screens / touch — the quiet +/− control beside the parent
 *                 expands its pages in place; the label stays a link.
 *
 * No dependencies. Vanilla JS only.
 * Loaded in the footer — DOM is available immediately.
 *
 * Motion: CSS owns every transition (and prefers-reduced-motion). JS only
 * manages state classes and ARIA attributes.
 */

( function () {
    'use strict';

    // ── Element references ───────────────────────────────────────────────
    var menuBtn    = document.getElementById( 'menu-btn' );
    var megaMenu   = document.getElementById( 'mega-menu' );
    var backdrop   = document.getElementById( 'mega-menu-backdrop' );
    var header     = document.getElementById( 'site-header' );
    var siteNav    = document.getElementById( 'site-nav' );

    if ( ! menuBtn || ! megaMenu ) {
        // Elements not found — exit silently (e.g. admin bar iframe context)
        return;
    }

    // ── State ────────────────────────────────────────────────────────────
    var isOpen        = false;
    var lastFocused   = null;
    var scrollY       = 0;
    var activeSection = '';
    var hoverTimer    = null;
    var settleTimer   = null;
    var suppressFocus = false;
    var inerted       = [];

    var HOVER_INTENT = 90;    // ms — forgives a pointer crossing other labels
    var SETTLE_AFTER = 1100;  // ms — entrance stagger has finished by now

    // Desktop composition (panels in the right-hand region)
    var desktop = window.matchMedia( '(min-width: 960px)' );

    // ── Focusable elements query ─────────────────────────────────────────
    var FOCUSABLE = [
        'a[href]',
        'button:not([disabled])',
        'input:not([disabled])',
        'select:not([disabled])',
        'textarea:not([disabled])',
        '[tabindex]:not([tabindex="-1"])',
    ].join( ', ' );

    function toArray( list ) {
        return Array.prototype.slice.call( list );
    }

    // ── Sections and their panels ────────────────────────────────────────
    var items  = toArray( megaMenu.querySelectorAll( '.mega-menu__primary-item' ) );
    var panels = {};

    items.forEach( function ( item ) {
        var panel = item.querySelector( '.mega-menu__panel' );
        if ( ! panel ) { return; }

        panels[ item.getAttribute( 'data-section' ) ] = {
            item:   item,
            panel:  panel,
            link:   item.querySelector( '.mega-menu__primary-link' ),
            toggle: item.querySelector( '.mega-menu__primary-toggle' ),
        };
    } );

    // On desktop the parent link itself reports the panel's state;
    // below 960px the +/− control does, and the link is a plain link.
    function syncLinkState( entry, expanded ) {
        if ( desktop.matches ) {
            entry.link.setAttribute( 'aria-expanded', expanded ? 'true' : 'false' );
        } else {
            entry.link.removeAttribute( 'aria-expanded' );
        }
    }

    /**
     * Set the contextual section. A key with no panel (or '') shows the
     * editorial rail; data-section still records which label is active.
     */
    function setSection( key ) {
        var panelKey = panels[ key ] ? key : '';

        megaMenu.setAttribute( 'data-section', key || '' );
        megaMenu.classList.add( 'is-settled' );

        if ( panelKey === activeSection ) { return; }
        activeSection = panelKey;

        megaMenu.setAttribute( 'data-panel', panelKey );
        megaMenu.classList.toggle( 'has-panel', !! panelKey );

        Object.keys( panels ).forEach( function ( k ) {
            var entry = panels[ k ];
            var on    = k === panelKey;

            entry.item.classList.toggle( 'is-active', on );
            entry.panel.classList.toggle( 'is-active', on );

            // Hidden panels are removed from focus order and the a11y tree
            if ( on ) {
                entry.panel.removeAttribute( 'inert' );
            } else {
                entry.panel.setAttribute( 'inert', '' );
            }

            if ( entry.toggle ) {
                entry.toggle.setAttribute( 'aria-expanded', on ? 'true' : 'false' );
            }

            syncLinkState( entry, on );
        } );
    }

    function resetSection() {
        clearTimeout( hoverTimer );
        activeSection = null;  // force a full re-sync
        setSection( '' );
        megaMenu.removeAttribute( 'data-section' );
        megaMenu.classList.remove( 'is-settled' );
    }

    // ── Page inertness while open ────────────────────────────────────────
    function setPageInert( on ) {
        if ( on ) {
            inerted = toArray( document.body.children ).filter( function ( el ) {
                return el !== header && ! el.hasAttribute( 'inert' ) && el.tagName !== 'SCRIPT';
            } );
            inerted.forEach( function ( el ) { el.setAttribute( 'inert', '' ); } );
        } else {
            inerted.forEach( function ( el ) { el.removeAttribute( 'inert' ); } );
            inerted = [];
        }
    }

    // ── Open ─────────────────────────────────────────────────────────────
    function openMenu() {
        if ( isOpen ) { return; }
        isOpen      = true;
        lastFocused = document.activeElement;

        resetSection();

        menuBtn.setAttribute( 'aria-expanded', 'true' );
        menuBtn.setAttribute( 'aria-label',    'Close navigation menu' );
        megaMenu.removeAttribute( 'inert' );
        megaMenu.setAttribute( 'aria-hidden',  'false' );

        if ( header ) { header.classList.add( 'menu-is-open' ); }
        document.body.classList.add( 'menu-is-open' );

        // Scroll lock: record position, fix body
        scrollY = window.pageYOffset;
        document.body.style.top      = '-' + scrollY + 'px';
        document.body.style.position = 'fixed';
        document.body.style.width    = '100%';

        setPageInert( true );

        clearTimeout( settleTimer );
        settleTimer = setTimeout( function () {
            megaMenu.classList.add( 'is-settled' );
        }, SETTLE_AFTER );

        // Focus stays on the menu button, which is now the close control;
        // the next Tab enters the menu in reading order.
        menuBtn.focus();

        document.addEventListener( 'keydown', handleKeydown );
    }

    // ── Close ────────────────────────────────────────────────────────────
    function closeMenu() {
        if ( ! isOpen ) { return; }
        isOpen = false;

        clearTimeout( hoverTimer );
        clearTimeout( settleTimer );
        megaMenu.classList.remove( 'is-settled' );

        menuBtn.setAttribute( 'aria-expanded', 'false' );
        menuBtn.setAttribute( 'aria-label',    'Open navigation menu' );
        megaMenu.setAttribute( 'aria-hidden',  'true' );
        megaMenu.setAttribute( 'inert', '' );

        if ( header ) { header.classList.remove( 'menu-is-open' ); }
        document.body.classList.remove( 'menu-is-open' );

        setPageInert( false );

        // Restore scroll position
        document.body.style.position = '';
        document.body.style.top      = '';
        document.body.style.width    = '';
        window.scrollTo( 0, scrollY );

        // Restore focus to the trigger button
        ( lastFocused && document.contains( lastFocused ) ? lastFocused : menuBtn ).focus();

        document.removeEventListener( 'keydown', handleKeydown );
    }

    // ── Toggle ───────────────────────────────────────────────────────────
    function toggleMenu() {
        isOpen ? closeMenu() : openMenu();
    }

    // ── Focus containment helpers ────────────────────────────────────────
    function isReachable( el ) {
        if ( el.closest( '[inert]' ) ) { return false; }
        if ( ! el.getClientRects().length ) { return false; }
        return window.getComputedStyle( el ).visibility !== 'hidden';
    }

    function focusableInMenu() {
        var scope = toArray( ( siteNav || menuBtn.parentNode ).querySelectorAll( FOCUSABLE ) )
            .concat( toArray( megaMenu.querySelectorAll( FOCUSABLE ) ) );
        return scope.filter( isReachable );
    }

    // Focus the parent of a section without re-triggering its panel
    function focusQuietly( el ) {
        suppressFocus = true;
        el.focus();
        suppressFocus = false;
    }

    // ── Keyboard handling ────────────────────────────────────────────────
    function handleKeydown( e ) {
        // Escape: first clears an open section, then closes the menu
        if ( e.key === 'Escape' || e.key === 'Esc' ) {
            if ( activeSection ) {
                var entry  = panels[ activeSection ];
                var within = entry.panel.contains( document.activeElement );

                setSection( '' );

                if ( within ) {
                    focusQuietly( desktop.matches || ! entry.toggle ? entry.link : entry.toggle );
                }
                return;
            }
            closeMenu();
            return;
        }

        // Tab: contain focus within the header bar + overlay
        if ( e.key === 'Tab' ) {
            var focusable = focusableInMenu();

            if ( ! focusable.length ) { return; }

            var first   = focusable[0];
            var last    = focusable[ focusable.length - 1 ];
            var current = document.activeElement;

            if ( focusable.indexOf( current ) === -1 ) {
                e.preventDefault();
                ( e.shiftKey ? last : first ).focus();
                return;
            }

            if ( e.shiftKey ) {
                // Shift+Tab: wrap from first to last
                if ( current === first ) {
                    e.preventDefault();
                    last.focus();
                }
            } else {
                // Tab: wrap from last to first
                if ( current === last ) {
                    e.preventDefault();
                    first.focus();
                }
            }
        }
    }

    // ── Event listeners ──────────────────────────────────────────────────
    menuBtn.addEventListener( 'click', toggleMenu );

    if ( backdrop ) {
        backdrop.addEventListener( 'click', closeMenu );
    }

    // Close menu when a link is activated
    // (handles same-page links such as the archive anchors)
    megaMenu.addEventListener( 'click', function ( e ) {
        var link = e.target.closest( 'a[href]' );
        if ( link && link.closest( '.mega-menu__body' ) ) {
            closeMenu();
        }
    } );

    // Primary labels: hover intent + keyboard focus drive the right-hand
    // region on desktop. Every label participates, so moving to a section
    // without a panel brings the editorial rail back.
    items.forEach( function ( item ) {
        var key  = item.getAttribute( 'data-section' );
        var link = item.querySelector( '.mega-menu__primary-link' );
        if ( ! link ) { return; }

        link.addEventListener( 'mouseenter', function () {
            if ( ! desktop.matches ) { return; }
            clearTimeout( hoverTimer );
            hoverTimer = setTimeout( function () { setSection( key ); }, HOVER_INTENT );
        } );

        link.addEventListener( 'mouseleave', function () {
            clearTimeout( hoverTimer );
        } );

        link.addEventListener( 'focus', function () {
            if ( suppressFocus || ! desktop.matches ) { return; }
            clearTimeout( hoverTimer );
            setSection( key );
        } );
    } );

    // +/− controls: explicit disclosure (small screens and touch)
    Object.keys( panels ).forEach( function ( key ) {
        var entry = panels[ key ];
        if ( ! entry.toggle ) { return; }

        entry.toggle.addEventListener( 'click', function () {
            setSection( activeSection === key ? '' : key );
        } );
    } );

    // Keep the parent links' ARIA honest when the composition changes
    function onBreakpointChange() {
        Object.keys( panels ).forEach( function ( key ) {
            syncLinkState( panels[ key ], key === activeSection );
        } );
    }

    if ( desktop.addEventListener ) {
        desktop.addEventListener( 'change', onBreakpointChange );
    } else if ( desktop.addListener ) {
        desktop.addListener( onBreakpointChange );
    }

    // Closed overlay is removed from focus order and the a11y tree
    megaMenu.setAttribute( 'inert', '' );
    resetSection();

    // The sticky nav stays visible at all times. Visual variants
    // (transparent over the dark hero vs frosted-glass with dark text
    // when scrolled) are handled by .is-scrolled below.

    // ── Header scroll-state (.is-scrolled) ────────────────────────────
    // Adds a frosted-glass appearance to the header once the user has
    // scrolled past the hero. Separate from the hide/show logic above.
    var IS_SCROLLED_THRESHOLD = 80; // px

    function updateScrolledState() {
        if ( ! header ) { return; }
        if ( window.pageYOffset > IS_SCROLLED_THRESHOLD ) {
            header.classList.add( 'is-scrolled' );
        } else {
            header.classList.remove( 'is-scrolled' );
        }
    }

    window.addEventListener( 'scroll', updateScrolledState, { passive: true } );
    updateScrolledState(); // run immediately — handles mid-scroll page load

}() );
