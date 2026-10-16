/**
 * Summit Navigation — Mega Menu Interaction
 * Location: assets/js/nav.js
 *
 * Handles:
 *   - Menu open / close toggle (the header's menu button is also the
 *     close control while the overlay is open)
 *   - aria-expanded / aria-hidden state management
 *   - Escape: closes the deepest open level of the tree, then the menu
 *   - Backdrop click to close
 *   - Body scroll lock when menu is open
 *   - Focus containment: the header bar + overlay; the rest of the page
 *     is made inert while the menu is open
 *   - Restores focus to the trigger button on close
 *   - The tree (approved composition study, DLG click model): nothing
 *     opens on hover or focus — those are visual cues only. Clicking (or
 *     Enter / Space on) a section row opens its branch; within Solutions,
 *     clicking a taxonomy opens its destinations. One branch and one
 *     taxonomy at a time; activating an open row closes it.
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
    var isOpen         = false;
    var lastFocused    = null;
    var scrollY        = 0;
    var activeSection  = '';
    var settleTimer    = null;
    var inerted        = [];

    var SETTLE_AFTER   = 1100;  // ms — entrance stagger has finished by now

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

    // ── The tree, read from the rendered (config-driven) markup ─────────
    var items    = toArray( megaMenu.querySelectorAll( '.mega-menu__primary-item' ) );
    var sections = {};

    items.forEach( function ( item ) {
        var panel = item.querySelector( '.mega-menu__panel' );
        if ( ! panel ) { return; }

        var key    = item.getAttribute( 'data-section' );
        var groups = {};
        var order  = [];

        toArray( panel.querySelectorAll( '.mega-menu__branch-item[data-group]' ) ).forEach( function ( gItem ) {
            var gKey = gItem.getAttribute( 'data-group' );
            order.push( gKey );
            groups[ gKey ] = {
                item:   gItem,
                toggle: gItem.querySelector( '.mega-menu__branch-toggle' ),
                leaves: gItem.querySelector( '.mega-menu__leaves' ),
            };
        } );

        sections[ key ] = {
            item:     item,
            panel:    panel,
            toggle:   item.querySelector( '.mega-menu__primary-toggle' ),
            taxonomy: panel.querySelector( '.mega-menu__branch--taxonomy' ),
            groups:   groups,
            order:    order,
            group:    '',
        };
    } );

    function setInert( el, on ) {
        if ( ! el ) { return; }
        if ( on ) {
            el.setAttribute( 'inert', '' );
        } else {
            el.removeAttribute( 'inert' );
        }
    }

    /**
     * Show one taxonomy's destinations within a section ('' for none).
     */
    function setGroup( sectionKey, groupKey ) {
        var entry = sections[ sectionKey ];
        if ( ! entry || ! entry.order.length ) { return; }

        groupKey    = entry.groups[ groupKey ] ? groupKey : '';
        entry.group = groupKey;

        entry.order.forEach( function ( gKey ) {
            var g  = entry.groups[ gKey ];
            var on = gKey === groupKey;

            g.item.classList.toggle( 'is-active', on );
            g.toggle.setAttribute( 'aria-expanded', on ? 'true' : 'false' );
            setInert( g.leaves, ! on );
        } );

        if ( entry.taxonomy ) {
            entry.taxonomy.classList.toggle( 'has-active', !! groupKey );
        }

        syncRules( entry );
    }

    // Desktop: the branch rule spans at least the primary list; if a
    // destination list runs longer, both rules extend together so the
    // two levels keep one baseline.
    function syncRules( entry ) {
        entry.panel.style.minHeight = '';
        var open = entry.group ? entry.groups[ entry.group ].leaves : null;
        if ( ! open || window.getComputedStyle( open ).position !== 'absolute' ) { return; }
        if ( open.offsetHeight > entry.panel.offsetHeight ) {
            entry.panel.style.minHeight = open.offsetHeight + 'px';
        }
    }

    /**
     * Open one section's branch ('' for none — the resting state).
     * A branch always opens at its first level: for Solutions, the
     * taxonomies with no destinations showing.
     */
    function setSection( key ) {
        key = sections[ key ] ? key : '';

        megaMenu.classList.add( 'is-settled' );

        if ( key === activeSection ) { return; }

        var previous = activeSection;
        activeSection = key;

        megaMenu.setAttribute( 'data-section', key );
        megaMenu.classList.toggle( 'has-section', !! key );

        Object.keys( sections ).forEach( function ( k ) {
            var entry = sections[ k ];
            var on    = k === key;

            entry.item.classList.toggle( 'is-active', on );
            entry.panel.classList.toggle( 'is-active', on );

            // Hidden branches are removed from focus order and the a11y tree
            setInert( entry.panel, ! on );

            if ( entry.toggle ) {
                entry.toggle.setAttribute( 'aria-expanded', on ? 'true' : 'false' );
            }
        } );

        if ( previous && sections[ previous ] ) {
            setGroup( previous, '' );
        }
    }

    function resetTree() {
        activeSection = null;  // force a full re-sync
        setSection( '' );
        Object.keys( sections ).forEach( function ( k ) { setGroup( k, '' ); } );
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

        resetTree();

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

    // ── Keyboard handling ────────────────────────────────────────────────
    function handleKeydown( e ) {
        // Escape closes the deepest open level first: destinations, then
        // the branch, then the menu. Focus returns to the row that opened
        // the level being closed, if it was inside that level.
        if ( e.key === 'Escape' || e.key === 'Esc' ) {
            var current = document.activeElement;
            var entry   = activeSection ? sections[ activeSection ] : null;

            if ( entry && entry.group ) {
                var group  = entry.groups[ entry.group ];
                var inLeaf = group.leaves.contains( current );

                setGroup( activeSection, '' );
                if ( inLeaf ) { group.toggle.focus(); }
                return;
            }

            if ( entry ) {
                var inBranch = entry.panel.contains( current );

                setSection( '' );
                if ( inBranch ) { entry.toggle.focus(); }
                return;
            }

            closeMenu();
            return;
        }

        // Tab: contain focus within the header bar + overlay
        if ( e.key === 'Tab' ) {
            var focusable = focusableInMenu();

            if ( ! focusable.length ) { return; }

            var first  = focusable[0];
            var last   = focusable[ focusable.length - 1 ];
            var active = document.activeElement;

            if ( focusable.indexOf( active ) === -1 ) {
                e.preventDefault();
                ( e.shiftKey ? last : first ).focus();
                return;
            }

            if ( e.shiftKey ) {
                // Shift+Tab: wrap from first to last
                if ( active === first ) {
                    e.preventDefault();
                    last.focus();
                }
            } else {
                // Tab: wrap from last to first
                if ( active === last ) {
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

    // The tree changes only on deliberate activation (click / tap /
    // Enter / Space — all native to buttons). Hover and focus are visual
    // cues only, handled in CSS.
    Object.keys( sections ).forEach( function ( key ) {
        var entry = sections[ key ];

        // Section row: open / close its branch
        entry.toggle.addEventListener( 'click', function () {
            setSection( activeSection === key ? '' : key );
        } );

        // Taxonomy row: open / close its destinations (one at a time)
        entry.order.forEach( function ( gKey ) {
            entry.groups[ gKey ].toggle.addEventListener( 'click', function () {
                setGroup( key, entry.group === gKey ? '' : gKey );
            } );
        } );
    } );

    // Keep the rules in step if the viewport changes while open
    window.addEventListener( 'resize', function () {
        if ( activeSection ) { syncRules( sections[ activeSection ] ); }
    }, { passive: true } );

    // Closed overlay is removed from focus order and the a11y tree
    megaMenu.setAttribute( 'inert', '' );
    resetTree();

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
