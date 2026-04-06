/**
 * Summit Navigation — Mega Menu Interaction
 * Location: assets/js/nav.js
 *
 * Handles:
 *   - Menu open / close toggle
 *   - aria-expanded / aria-hidden state management
 *   - Escape key to close
 *   - Backdrop click to close
 *   - Body scroll lock when menu is open
 *   - Focus trap inside the open overlay
 *   - Restores focus to the trigger button on close
 *
 * No dependencies. Vanilla JS only.
 * Loaded in the footer — DOM is available immediately.
 *
 * Motion: the CSS handles the transition via
 * opacity + visibility + transform. JS only manages state classes
 * and ARIA attributes. This keeps animation logic in CSS where it
 * belongs and respects prefers-reduced-motion at the CSS layer.
 */

( function () {
    'use strict';

    // ── Element references ───────────────────────────────────────────────
    var menuBtn    = document.getElementById( 'menu-btn' );
    var closeBtn   = document.getElementById( 'menu-close-btn' );
    var megaMenu   = document.getElementById( 'mega-menu' );
    var backdrop   = document.getElementById( 'mega-menu-backdrop' );
    var header     = document.getElementById( 'site-header' );

    if ( ! menuBtn || ! megaMenu ) {
        // Elements not found — exit silently (e.g. admin bar iframe context)
        return;
    }

    // ── State ────────────────────────────────────────────────────────────
    var isOpen        = false;
    var lastFocused   = null;
    var scrollY       = 0;

    // ── Focusable elements query ─────────────────────────────────────────
    var FOCUSABLE = [
        'a[href]',
        'button:not([disabled])',
        'input:not([disabled])',
        'select:not([disabled])',
        'textarea:not([disabled])',
        '[tabindex]:not([tabindex="-1"])',
    ].join( ', ' );

    // ── Open ─────────────────────────────────────────────────────────────
    function openMenu() {
        if ( isOpen ) { return; }
        isOpen      = true;
        lastFocused = document.activeElement;

        menuBtn.setAttribute( 'aria-expanded', 'true' );
        megaMenu.setAttribute( 'aria-hidden',  'false' );

        if ( header ) { header.classList.add( 'menu-is-open' ); }
        document.body.classList.add( 'menu-is-open' );

        // Scroll lock: record position, fix body
        scrollY = window.pageYOffset;
        document.body.style.top      = '-' + scrollY + 'px';
        document.body.style.position = 'fixed';
        document.body.style.width    = '100%';

        // Move focus to first focusable item in the overlay
        requestAnimationFrame( function () {
            var first = megaMenu.querySelectorAll( FOCUSABLE );
            if ( first.length ) { first[0].focus(); }
        } );

        document.addEventListener( 'keydown', handleKeydown );
    }

    // ── Close ────────────────────────────────────────────────────────────
    function closeMenu() {
        if ( ! isOpen ) { return; }
        isOpen = false;

        menuBtn.setAttribute( 'aria-expanded', 'false' );
        megaMenu.setAttribute( 'aria-hidden',  'true' );

        if ( header ) { header.classList.remove( 'menu-is-open' ); }
        document.body.classList.remove( 'menu-is-open' );

        // Restore scroll position
        document.body.style.position = '';
        document.body.style.top      = '';
        document.body.style.width    = '';
        window.scrollTo( 0, scrollY );

        // Restore focus to the trigger button
        if ( lastFocused ) { lastFocused.focus(); }

        document.removeEventListener( 'keydown', handleKeydown );
    }

    // ── Toggle ───────────────────────────────────────────────────────────
    function toggleMenu() {
        isOpen ? closeMenu() : openMenu();
    }

    // ── Keyboard handling ────────────────────────────────────────────────
    function handleKeydown( e ) {
        // Escape always closes
        if ( e.key === 'Escape' || e.key === 'Esc' ) {
            closeMenu();
            return;
        }

        // Tab: trap focus within the overlay
        if ( e.key === 'Tab' ) {
            var focusable = Array.prototype.slice.call(
                megaMenu.querySelectorAll( FOCUSABLE )
            );

            if ( ! focusable.length ) { return; }

            var first = focusable[0];
            var last  = focusable[ focusable.length - 1 ];

            if ( e.shiftKey ) {
                // Shift+Tab: wrap from first to last
                if ( document.activeElement === first ) {
                    e.preventDefault();
                    last.focus();
                }
            } else {
                // Tab: wrap from last to first
                if ( document.activeElement === last ) {
                    e.preventDefault();
                    first.focus();
                }
            }
        }
    }

    // ── Event listeners ──────────────────────────────────────────────────
    menuBtn.addEventListener( 'click', toggleMenu );

    if ( closeBtn ) {
        closeBtn.addEventListener( 'click', closeMenu );
    }

    if ( backdrop ) {
        backdrop.addEventListener( 'click', closeMenu );
    }

    // Close menu when a nav link is activated
    // (handles SPA-style navigation and same-page links)
    megaMenu.addEventListener( 'click', function ( e ) {
        if ( e.target.tagName === 'A' && e.target.closest( '.mega-menu__body' ) ) {
            closeMenu();
        }
    } );

    // ── Accessibility: make duplicate brand links in overlay non-interactive ──
    // The logo in the mega-menu nav bar is aria-hidden="true" / tabindex="-1"
    // in the HTML (set directly). This is just a safety net.
    var overlayBrand = megaMenu.querySelector( '.site-nav__brand[aria-hidden="true"]' );
    if ( overlayBrand ) {
        overlayBrand.setAttribute( 'tabindex', '-1' );
    }

    // ── Scroll-aware header ────────────────────────────────────────────
    // Hides top nav on scroll-down, shows on scroll-up.
    // Uses rAF throttling and an 8px threshold to avoid iOS bounce jitter.
    var siteNav = document.getElementById( 'site-nav' );

    if ( siteNav ) {
        var lastScrollY  = 0;
        var ticking      = false;
        var navHidden    = false;
        var SCROLL_THRESHOLD = 8;
        var navHeight    = 56;

        function updateScrollDirection() {
            var currentY = window.pageYOffset;

            // Always show when near top
            if ( currentY < navHeight ) {
                if ( navHidden ) {
                    siteNav.classList.remove( 'site-nav--hidden' );
                    navHidden = false;
                }
                lastScrollY = currentY;
                ticking = false;
                return;
            }

            // Never hide when menu is open
            if ( isOpen ) {
                lastScrollY = currentY;
                ticking = false;
                return;
            }

            var delta = currentY - lastScrollY;

            if ( delta > SCROLL_THRESHOLD && ! navHidden ) {
                // Scrolling down — hide
                siteNav.classList.add( 'site-nav--hidden' );
                navHidden = true;
            } else if ( delta < -SCROLL_THRESHOLD && navHidden ) {
                // Scrolling up — show
                siteNav.classList.remove( 'site-nav--hidden' );
                navHidden = false;
            }

            lastScrollY = currentY;
            ticking = false;
        }

        window.addEventListener( 'scroll', function () {
            if ( ! ticking ) {
                requestAnimationFrame( updateScrollDirection );
                ticking = true;
            }
        }, { passive: true } );
    }

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
