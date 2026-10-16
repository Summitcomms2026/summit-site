/**
 * Summit Navigation — Mega Menu Interaction
 * Location: assets/js/nav.js
 *
 * Handles:
 *   - Menu open / close toggle (the header's menu button is also the
 *     close control while the overlay is open)
 *   - aria-expanded / aria-hidden state management
 *   - Escape: steps back one level of the tree, then closes the menu
 *   - Backdrop click to close
 *   - Body scroll lock when menu is open
 *   - Focus containment: the header bar + overlay; the rest of the page
 *     is made inert while the menu is open
 *   - Restores focus to the trigger button on close
 *   - The progressive tree:
 *       desktop — hover (with a short intent delay) or keyboard focus on a
 *                 primary label opens its branch beside it; within
 *                 Solutions, hover or focus on a taxonomy control shows its
 *                 destinations in the next column (By Industry by default,
 *                 or whichever taxonomy was chosen earlier in this menu
 *                 session). Clicking a primary label always navigates.
 *       small screens / touch — quiet +/− controls expand each level in
 *                 place; primary labels stay links.
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
    var chosenGroup    = {};    // section → taxonomy chosen this menu session
    var hoverTimer     = null;
    var groupTimer     = null;
    var settleTimer    = null;
    var suppressFocus  = false;
    var inerted        = [];

    var INTENT_FIRST   = 60;    // ms — opening the first branch
    var INTENT_SWITCH  = 120;   // ms — moving between branches: forgives a
                                //      pointer crossing other labels
    var INTENT_GROUP   = 80;    // ms — moving between taxonomies
    var SETTLE_AFTER   = 1100;  // ms — entrance stagger has finished by now

    // Desktop composition (branches in columns beside the primary list)
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
            link:     item.querySelector( '.mega-menu__primary-link' ),
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

    // On desktop the primary link itself reports its branch's state;
    // below 960px the +/− control does, and the link is a plain link.
    function syncLinkState( entry, expanded ) {
        if ( desktop.matches ) {
            entry.link.setAttribute( 'aria-expanded', expanded ? 'true' : 'false' );
        } else {
            entry.link.removeAttribute( 'aria-expanded' );
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
    }

    // A user's own choice of taxonomy is kept for the rest of the session
    function chooseGroup( sectionKey, groupKey ) {
        chosenGroup[ sectionKey ] = groupKey;
        setGroup( sectionKey, groupKey );
    }

    /**
     * Open one section's branch ('' for none — the resting state).
     */
    function setSection( key ) {
        key = sections[ key ] ? key : '';

        megaMenu.classList.add( 'is-settled' );

        if ( key === activeSection ) { return; }
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

            syncLinkState( entry, on );
        } );

        // Solutions: the taxonomy chosen earlier this session, otherwise
        // By Industry on desktop; on small screens nothing until asked.
        var entry = sections[ key ];
        if ( entry && entry.order.length ) {
            setGroup( key, chosenGroup[ key ] || ( desktop.matches ? entry.order[0] : '' ) );
        }
    }

    function resetTree() {
        clearTimeout( hoverTimer );
        clearTimeout( groupTimer );
        chosenGroup   = {};
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

        clearTimeout( hoverTimer );
        clearTimeout( groupTimer );
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

    // Move focus without the focus itself re-opening a branch
    function focusQuietly( el ) {
        suppressFocus = true;
        el.focus();
        suppressFocus = false;
    }

    // ── Keyboard handling ────────────────────────────────────────────────
    function handleKeydown( e ) {
        // Escape steps back one level: destinations → their taxonomy,
        // an open branch → its primary label, and finally closes the menu.
        if ( e.key === 'Escape' || e.key === 'Esc' ) {
            var current = document.activeElement;
            var leaves  = current && current.closest ? current.closest( '.mega-menu__leaves' ) : null;

            if ( leaves ) {
                var owner = leaves.parentNode.querySelector( '.mega-menu__branch-toggle' );
                if ( owner ) {
                    focusQuietly( owner );
                    return;
                }
            }

            if ( activeSection ) {
                var entry  = sections[ activeSection ];
                var within = entry.panel.contains( current );

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

    // Primary labels: hover intent + keyboard focus open the branch on
    // desktop. A label is never a disclosure-only control — clicking it
    // navigates to the section itself.
    items.forEach( function ( item ) {
        var key  = item.getAttribute( 'data-section' );
        var link = item.querySelector( '.mega-menu__primary-link' );
        if ( ! link ) { return; }

        link.addEventListener( 'mouseenter', function () {
            if ( ! desktop.matches ) { return; }
            clearTimeout( hoverTimer );
            hoverTimer = setTimeout( function () {
                setSection( key );
            }, activeSection ? INTENT_SWITCH : INTENT_FIRST );
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

    Object.keys( sections ).forEach( function ( key ) {
        var entry = sections[ key ];

        // Primary +/−: explicit disclosure (small screens and touch)
        if ( entry.toggle ) {
            entry.toggle.addEventListener( 'click', function () {
                setSection( activeSection === key ? '' : key );
            } );
        }

        // Taxonomy controls: hover intent / focus select on desktop;
        // a click selects on desktop and toggles below 960px.
        entry.order.forEach( function ( gKey ) {
            var toggle = entry.groups[ gKey ].toggle;

            toggle.addEventListener( 'mouseenter', function () {
                if ( ! desktop.matches ) { return; }
                clearTimeout( groupTimer );
                groupTimer = setTimeout( function () {
                    chooseGroup( key, gKey );
                }, INTENT_GROUP );
            } );

            toggle.addEventListener( 'mouseleave', function () {
                clearTimeout( groupTimer );
            } );

            toggle.addEventListener( 'focus', function () {
                if ( suppressFocus || ! desktop.matches ) { return; }
                clearTimeout( groupTimer );
                chooseGroup( key, gKey );
            } );

            toggle.addEventListener( 'click', function () {
                clearTimeout( groupTimer );
                if ( desktop.matches ) {
                    chooseGroup( key, gKey );
                } else {
                    chooseGroup( key, entry.group === gKey ? '' : gKey );
                }
            } );
        } );
    } );

    // Keep the primary links' ARIA honest when the composition changes
    function onBreakpointChange() {
        Object.keys( sections ).forEach( function ( key ) {
            syncLinkState( sections[ key ], key === activeSection );
        } );
    }

    if ( desktop.addEventListener ) {
        desktop.addEventListener( 'change', onBreakpointChange );
    } else if ( desktop.addListener ) {
        desktop.addListener( onBreakpointChange );
    }

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
