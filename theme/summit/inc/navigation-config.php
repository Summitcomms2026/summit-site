<?php
/**
 * Navigation Configuration — Single Source of Truth
 * Location: inc/navigation-config.php
 * Required from: functions.php
 *
 * Declarative description of Summit's RESOLVED site architecture:
 * primary mega-menu sections, their grouped children, the primary CTA,
 * and the utility / legal / social / subscribe destinations.
 *
 * This file is DATA + a thin accessor only. It does NOT render markup and
 * it does NOT own content architecture (no CPTs, taxonomies, ACF, WP Menus
 * or database state — see THEME_ARCHITECTURE.md). Rendering components
 * (header.php, footer.php, the mega menu) will consume summit_nav() so that
 * labels and canonical paths live in exactly one place.
 *
 * Scope notes (per Brief 01 / Stage 0 authorisation):
 *   - Paths are the NEW canonical architecture. They are intentionally
 *     declared even though the matching WordPress Pages do not exist yet.
 *     No redirects, rewrite rules or page-guards are introduced here;
 *     route resolution / migration is decided when scaffolding is approved.
 *   - Legacy pages (Who We Are, What We Do, Selected Portfolio, Tastemakers)
 *     are deliberately NOT surfaced here. They may remain live and untouched;
 *     their disposition is decided separately.
 *   - Home is reached via the Summit mark, so it is not a primary list item.
 *     The principal mega-menu list is the six destination families below;
 *     Let's Talk functions as the primary CTA, not a list item.
 *   - Each primary section is a REAL destination page as well as a menu
 *     family: it carries its own 'path' plus its grouped children. The
 *     exact desktop/mobile interaction (how children are revealed, how the
 *     parent is reached) is resolved during the menu implementation, not here.
 *   - The dynamic editorial rail / article feed is kept entirely separate
 *     (see inc/nav-functions.php :: summit_get_menu_articles()). The
 *     'editorial_rail' flag below is a hint for which section may host that
 *     live feed — it is not static content and holds no articles.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;


/**
 * Return the full navigation configuration (memoised for the request).
 *
 * @return array
 */
function summit_nav_config(): array {
    static $config = null;

    if ( null !== $config ) {
        return $config;
    }

    $config = [

        /* ── Primary mega-menu sections ─────────────────────────────────
         * Six destination families. Keyed by a stable slug used internally
         * to identify a section's sub-state; keys are not user-facing.
         * ---------------------------------------------------------------- */
        'primary' => [

            'solutions' => [
                'label'  => 'Solutions',
                'path'   => '/solutions',
                'groups' => [
                    'by-industry' => [
                        'label'    => 'By Industry',
                        'children' => [
                            [ 'label' => 'Watches & Jewellery',             'path' => '/solutions/watches-jewellery' ],
                            [ 'label' => 'Fashion & Accessories',           'path' => '/solutions/fashion-accessories' ],
                            [ 'label' => 'Beauty & Fragrance',              'path' => '/solutions/beauty-fragrance' ],
                            [ 'label' => 'Wine & Spirits',                  'path' => '/solutions/wine-spirits' ],
                            [ 'label' => 'Automotive',                      'path' => '/solutions/automotive' ],
                            [ 'label' => 'Real Estate & Hospitality',       'path' => '/solutions/real-estate-hospitality' ],
                            [ 'label' => 'Art & Collectibles',              'path' => '/solutions/art-collectibles' ],
                            [ 'label' => 'Private Banking & Wealth Management', 'path' => '/solutions/private-banking-wealth-management' ],
                        ],
                    ],
                    'by-function' => [
                        'label'    => 'By Function',
                        'children' => [
                            [ 'label' => 'Strategy & Planning',     'path' => '/solutions/strategy-planning' ],
                            [ 'label' => 'Brand & Marketing',       'path' => '/solutions/brand-marketing' ],
                            [ 'label' => 'Editorial & Influence',   'path' => '/solutions/editorial-influence' ],
                            [ 'label' => 'Experience Design',       'path' => '/solutions/experience-design' ],
                            [ 'label' => 'Digital Commerce',        'path' => '/solutions/digital-commerce' ],
                            [ 'label' => 'Product, IP & Patrimony', 'path' => '/solutions/product-ip-patrimony' ],
                        ],
                    ],
                    'by-need' => [
                        'label'    => 'By Need',
                        'children' => [
                            [ 'label' => 'Build a Maison',                  'path' => '/solutions/build-a-maison' ],
                            [ 'label' => 'Reposition a Maison',             'path' => '/solutions/reposition-a-maison' ],
                            [ 'label' => 'Enhance Desire Without Dilution', 'path' => '/solutions/enhance-desire-without-dilution' ],
                            [ 'label' => 'Strengthen an Icon',              'path' => '/solutions/strengthen-an-icon' ],
                            [ 'label' => 'Activate Patrimony & IP',         'path' => '/solutions/activate-patrimony-ip' ],
                            [ 'label' => 'Transform the Client Experience', 'path' => '/solutions/transform-the-client-experience' ],
                            [ 'label' => 'Develop Thought Leadership',      'path' => '/solutions/develop-thought-leadership' ],
                            [ 'label' => 'Applied AI',                      'path' => '/solutions/applied-ai' ],
                            [ 'label' => 'Investment & Acquisitions',       'path' => '/solutions/investment-acquisitions' ],
                            [ 'label' => 'Maison Due Diligence',            'path' => '/solutions/maison-due-diligence' ],
                        ],
                    ],
                ],
            ],

            'observatory' => [
                'label'    => 'The Observatory',
                'path'     => '/the-observatory',
                'children' => [
                    [ 'label' => 'Overview',                       'path' => '/the-observatory' ],  // the parent landing page, not a separate /overview page
                    [ 'label' => 'For CMOs',                       'path' => '/the-observatory/for-cmos' ],
                    [ 'label' => 'For Family Offices & Investors', 'path' => '/the-observatory/for-family-offices-investors' ],
                    [ 'label' => 'The Field',                      'path' => '/the-observatory/field' ],
                    [ 'label' => 'How It Works',                   'path' => '/the-observatory/how-it-works' ],
                    [ 'label' => 'Request a Private Briefing',     'path' => '/the-observatory/request-a-private-briefing', 'action' => true ],  // a request, not a content page
                ],
            ],

            'programmes' => [
                'label'    => 'Programmes',
                'path'     => '/programmes',
                'children' => [
                    [ 'label' => 'Maison Formation',      'path' => '/programmes/maison-formation' ],
                    [ 'label' => 'Editorial & Influence', 'path' => '/programmes/editorial-influence' ],
                    [ 'label' => 'Digital Growth',        'path' => '/programmes/digital-growth' ],
                    [ 'label' => 'Client Experience',     'path' => '/programmes/client-experience' ],
                    [ 'label' => 'Agentic AI',            'path' => '/programmes/agentic-ai' ],
                    [ 'label' => 'Brand & IP Stewardship','path' => '/programmes/brand-ip-stewardship' ],
                ],
            ],

            'research' => [
                'label'    => 'Research',
                'path'     => '/research',
                'children' => [
                    [ 'label' => 'Touch Me Not',       'path' => '/research/touch-me-not' ],
                    [ 'label' => 'The Luxury 100',     'path' => '/research/luxury-100' ],
                    [ 'label' => 'White Papers',       'path' => '/research/white-papers' ],
                    [ 'label' => 'Research Programme', 'path' => '/research/programme' ],
                ],
            ],

            'future-of-luxury' => [
                'label'          => 'The Future of Luxury',
                'path'           => '/the-future-of-luxury',
                'editorial_rail' => true,  // may host the live article feed (feed stays separate)
                // /the-future-of-luxury/{slug} is the article URL namespace, so children
                // resolve to the archive itself or to anchors on it — never sub-pages.
                // The anchor IDs are added when the archive template is implemented.
                'children'       => [
                    [ 'label' => 'Latest',            'path' => '/the-future-of-luxury' ],
                    [ 'label' => 'Essays & Analysis', 'path' => '/the-future-of-luxury#essays-analysis' ],
                    [ 'label' => 'Subscribe',         'path' => '/the-future-of-luxury#subscribe' ],
                ],
            ],

            'about' => [
                'label'    => 'About',
                'path'     => '/about',
                'children' => [
                    [ 'label' => 'Origin Story',       'path' => '/about/origin-story' ],
                    [ 'label' => 'Manifesto',          'path' => '/about/manifesto' ],
                    [ 'label' => 'Why It Matters',     'path' => '/about/why-it-matters' ],
                    [ 'label' => 'Partners & People',  'path' => '/about/partners-people' ],
                    [ 'label' => 'How We Work',        'path' => '/about/how-we-work' ],
                ],
            ],

        ],

        /* ── Primary call-to-action ─────────────────────────────────────
         * Canonical destination in the new architecture. Any temporary
         * legacy route (e.g. /design-tomorrow) is preserved independently
         * and must not redefine this canonical path.
         * ---------------------------------------------------------------- */
        'cta' => [
            'label' => 'Let’s Talk',
            'path'  => '/lets-talk',
        ],

        /* ── Utility / footer destinations ──────────────────────────────
         * Navigation destinations only. NOT the contextual action language
         * (Share / LinkedIn / Email / Print / Download, etc.) — that
         * reusable action system is defined later (Stage E), not here.
         * ---------------------------------------------------------------- */
        'utility' => [
            [ 'label' => 'Client Access',   'path' => '/client-access' ],
            [ 'label' => 'Careers',         'path' => '/careers' ],
            [ 'label' => 'Media Enquiries', 'path' => '/media-enquiries' ],
        ],

        'legal' => [
            [ 'label' => 'Privacy', 'path' => '/privacy' ],
            [ 'label' => 'Terms',   'path' => '/terms' ],
        ],

        // No public social link until Summit's company LinkedIn URL is
        // confirmed: the handover's channel configuration marks it "pending
        // exact approval", and its sources disagree (/company/summitcommunication/
        // in the Pass 11 footer notes; /company/summitcomms/ in the earlier
        // site). Add the confirmed entry here — label, url, 'external' => true —
        // and the footer shows it. See editorial-handover/v1/channels.json.
        'social' => [],

        'subscribe' => [
            'label' => 'Subscribe',
            'path'  => '/the-future-of-luxury#subscribe',
        ],

    ];

    return $config;
}


/**
 * Thin accessor for navigation configuration.
 *
 * summit_nav()            → entire config array
 * summit_nav( 'primary' ) → the primary sections array
 * summit_nav( 'cta' )     → the CTA array, etc.
 *
 * @param  string $key  Optional top-level key.
 * @return mixed         Full config, the requested branch, or null if absent.
 */
function summit_nav( string $key = '' ) {
    $config = summit_nav_config();

    if ( '' === $key ) {
        return $config;
    }

    return $config[ $key ] ?? null;
}


/**
 * Resolve a config item to an absolute URL.
 *
 * External items carry a full 'url'; internal items carry a site-relative
 * 'path' (which may include a #fragment) and are passed through home_url().
 *
 * @param  array $item  A config item with 'url' or 'path'.
 * @return string
 */
function summit_nav_url( array $item ): string {
    if ( ! empty( $item['url'] ) ) {
        return $item['url'];
    }

    return home_url( $item['path'] ?? '/' );
}


/**
 * Whether the current request sits at or beneath a configured path.
 *
 * Fragments are ignored, so '/the-future-of-luxury#subscribe' matches the
 * archive. With $exact, only the path itself matches (no descendants).
 *
 * @param  string $path   Site-relative path from the config.
 * @param  bool   $exact  Match the path only, not its descendants.
 * @return bool
 */
function summit_nav_is_current( string $path, bool $exact = false ): bool {
    global $wp;

    $request = '/' . trim( (string) ( $wp->request ?? '' ), '/' );
    $target  = '/' . trim( strtok( $path, '#' ), '/' );

    if ( $request === $target ) {
        return true;
    }

    return ! $exact && '/' !== $target && 0 === strpos( $request . '/', $target . '/' );
}
