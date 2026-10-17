<?php
/**
 * The Future of Luxury — editorial art
 * Location: inc/editorial-art.php
 *
 * An essay without a photograph is still published with an image: an
 * authored typographic plate built from the essay's own metadata. One
 * composition per essay, adapting by container width from the mega
 * menu's 120 px thumbnail to the essay's full-width opener — HTML, CSS and
 * a little inline SVG, rendered on the server, with no remote service.
 *
 * Five composition families, chosen from the essay's category (the real
 * article_category taxonomy — no new field):
 *
 *   house    the maison as wordmark — Brand & Positioning, Luxury Strategy,
 *            Fashion & Leather Goods
 *   ledger   transaction and governance — Wealth, Value & Collecting
 *   object   the object, named inside a dial — Watches & Jewellery;
 *            Sport, Sponsorship & Status
 *   culture  the editorial word, cropped — Culture & Media, Hospitality &
 *            Travel, The New Luxury Customer
 *   diagram  research-adjacent notation — Luxury and Technology
 *
 * One subject per plate (The Quiet System): the word, its construction and
 * a small folio number — no repeated masthead, no category inside the
 * artwork (it sits beside every card).
 *
 * Colour (Pass 05): each family is a recurring editorial colour family —
 * house: deep violet or ink · object: rose and umber · ledger: paper, charcoal
 * and metal · culture: smoked lilac or violet ink on paper · diagram:
 * lilac notation on paper. A few essays carry a trace of their subject's
 * own colour (a third value in the art-direction keys) — a signal, never
 * a field, and never the house's branding. At card scale one colour
 * idea; at the opener, tonal depth; in the menu, stripped back.
 *
 * The plate's word ("key") is art direction, kept here per essay; an
 * essay without an entry falls back to the words before its title's colon,
 * or its first proper names. The plate is decorative (aria-hidden): the
 * headline always exists as text beside it.
 *
 * Real photographs win where their file exists (summit_fol_visual()).
 * Styles: assets/css/editorial-art.css (site-wide — the mega menu carries
 * plates on every page).
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Art direction per essay: slug => [ key, secondary, trace? ].
 * The trace is a subject colour shown as a signal (folio and a short
 * rule): Argyle's pink, the Vanquish's racing green, desert light.
 */
function summit_editorial_art_keys(): array {
    return [
        'the-creative-director-delusion'                       => [ 'Creative Director', 'Musical chairs' ],
        'prada-make-versace-dangerous-again'                    => [ 'Versace', 'Prada' ],
        'the-house-that-stumbled-inside-guccis-crisis'          => [ 'Gucci', 'The house that stumbled' ],
        'ralph-lauren-the-art-of-selling-through-chaos'         => [ 'Ralph Lauren', 'Selling through chaos' ],
        'aston-martins-new-vanquish-something-to-do-with-desire' => [ 'Vanquish', 'Aston Martin', '#4E6B58' ],
        'the-future-of-luxury-a-masterclass-in-reinvention'     => [ 'Dietrich', 'A masterclass in reinvention' ],
        'brabus-how-black-bold-and-bespoke-turned-excess-into-haute-couture' => [ 'BRABUS', 'Black, bold, bespoke' ],
        'can-sothebys-bet-big-on-debt-and-survive'               => [ 'Sotheby’s', 'Debt' ],
        'the-longevity-turn-when-time-becomes-exclusive-luxury'  => [ 'Time', 'The longevity turn' ],
        'from-hill-of-grace-to-argyle-pink-diamonds-why-australias-luxury-houses-need-a-harder-shell' => [ 'Argyle', 'Hill of Grace', '#D49AA5' ],
        'watches-and-wonders-time-desire-and-horology'          => [ 'Horology', 'Watches and Wonders' ],
        'the-characteristics-of-luxury-a-visual-conversation'    => [ 'Characteristics', 'A visual conversation' ],
        'the-luxurification-of-sport'                            => [ 'Sport', 'Luxurification' ],
        'dslcollection-chinese-contemporary-art-enters-digital-era' => [ 'DSL', 'Collection' ],
        'the-art-experience-how-museums-can-embrace-screen-culture' => [ 'Museum', 'Screen culture' ],
        'brave-new-world-decoupling-art-sales'                   => [ 'Decoupling', 'Art and experience' ],
        'saudi-arabias-tourism-ecosystem-next-frontier'          => [ 'Saudi', 'The next frontier', '#C9A574' ],
        'hospitality-is-eating-luxurys-lunch'                    => [ 'Lunch', 'Hospitality' ],
        'from-limited-edition-to-one-only-personalisation-in-luxury' => [ 'One & Only', 'From limited edition' ],
        'gen-z-wont-buy-your-myth'                               => [ 'Myth', 'Gen Z' ],
        'what-aupen-tells-us-about-the-next-wave-of-asian-luxury' => [ 'Aupen', 'The next wave' ],
        'apples-vision-pro-launch-metaverse-potential-for-brands' => [ 'Vision Pro', 'Apple' ],
        'luxurys-new-advantage-is-invisible'                     => [ 'Invisible', 'The new advantage' ],
        'the-future-of-culture-artificial-intelligence-arms-race' => [ 'AI', 'The arms race' ],
    ];
}

/**
 * The composition family for an essay, from its category.
 */
function summit_editorial_art_family( int $post_id ): string {
    $map = [
        'brand-positioning'       => 'house',
        'luxury-strategy'         => 'house',
        'fashion-leather-goods'   => 'house',
        'wealth-value-collecting' => 'ledger',
        'watches-jewellery'       => 'object',
        'sport-sponsorship-status' => 'object',
        'culture-media'           => 'culture',
        'hospitality-travel'      => 'culture',
        'the-new-luxury-customer' => 'culture',
        'luxury-and-technology'   => 'diagram',
    ];
    $term = function_exists( 'summit_fol_category' ) ? summit_fol_category( $post_id ) : null;
    return $term && isset( $map[ $term->slug ] ) ? $map[ $term->slug ] : 'culture';
}

/**
 * The essay's number in the publication, by date of publication.
 */
function summit_editorial_art_number( int $post_id ): int {
    static $order = null;
    if ( null === $order ) {
        $ids   = get_posts( [
            'post_type'      => 'article',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'orderby'        => [ 'date' => 'ASC', 'ID' => 'ASC' ],
            'no_found_rows'  => true,
        ] );
        $order = array_flip( array_map( 'intval', $ids ) );
    }
    return isset( $order[ $post_id ] ) ? $order[ $post_id ] + 1 : 0;
}

/**
 * The plate's word: art-directed, else derived from the title.
 *
 * @return array{0:string,1:string,2?:string}
 */
function summit_editorial_art_key( int $post_id ): array {
    $slug = (string) get_post_field( 'post_name', $post_id );
    $keys = summit_editorial_art_keys();
    if ( isset( $keys[ $slug ] ) ) {
        return $keys[ $slug ];
    }
    $title = html_entity_decode( wp_strip_all_tags( get_the_title( $post_id ) ), ENT_QUOTES, 'UTF-8' );
    if ( false !== strpos( $title, ':' ) ) {
        [ $head, $tail ] = array_map( 'trim', explode( ':', $title, 2 ) );
        if ( str_word_count( $head ) <= 3 ) {
            return [ $head, rtrim( $tail, '.' ) ];
        }
    }
    preg_match_all( '/\b[A-Z][\p{L}’\'&]+/u', $title, $m );
    $names = array_slice( array_values( array_diff( $m[0], [ 'The', 'A', 'An', 'How', 'Why', 'What', 'When', 'From', 'Is', 'Can' ] ) ), 0, 2 );
    return [ $names ? implode( ' ', $names ) : wp_trim_words( $title, 2, '' ), '' ];
}

/**
 * The plate itself.
 *
 * @param int    $post_id The essay.
 * @param string $context 'card' (default) | 'hero' — a hint for the plate's
 *                        own proportions where its frame does not set them.
 * @return string Markup (decorative; aria-hidden).
 */
function summit_editorial_art( int $post_id, string $context = 'card' ): string {
    $family = summit_editorial_art_family( $post_id );
    $art    = summit_editorial_art_key( $post_id );
    $key    = $art[0];
    $second = $art[1] ?? '';
    $trace  = isset( $art[2] ) ? sanitize_hex_color( $art[2] ) : '';
    $number = summit_editorial_art_number( $post_id );
    $year   = get_the_date( 'Y', $post_id );
    $tone   = in_array( $family, [ 'ledger', 'diagram' ], true ) ? 'paper' : ( $post_id % 3 === 1 ? 'paper' : 'dark' );
    if ( 'house' === $family ) {
        $tone = $post_id % 2 ? 'dark' : 'ink';   // deep violet, or ink
    }

    $geometry = [
        'house'   => '<rect x="4" y="4" width="92" height="92"/><rect x="6.5" y="6.5" width="87" height="87"/><path d="M4 70h92M4 73h92"/>',
        'ledger'  => '<path d="M0 30h100M0 42h100M0 54h100M0 66h100M0 78h100M70 22v70"/>',
        'object'  => '<circle cx="50" cy="50" r="40"/><circle cx="50" cy="50" r="31"/><path d="M50 4v8M50 88v8M4 50h8M88 50h8M17.5 17.5l5.7 5.7M76.8 76.8l5.7 5.7M82.5 17.5l-5.7 5.7M23.2 76.8l-5.7 5.7"/>',
        'culture' => '<path d="M72 0v100M76 0v100"/>',
        'diagram' => '<path d="M50 8v84M8 50h84M20.3 20.3l59.4 59.4M79.7 20.3 20.3 79.7"/><circle cx="50" cy="50" r="12"/><circle cx="50" cy="50" r="30"/>',
    ][ $family ];

    $long    = mb_strlen( $key ) > 12 ? ' ed-art--long' : '';
    $classes = sprintf( 'ed-art ed-art--%s ed-art--%s ed-art--ctx-%s%s%s', $family, $tone, sanitize_html_class( $context ), $trace ? ' has-trace' : '', $long );

    ob_start();
    ?>
<span class="<?php echo esc_attr( $classes ); ?>"<?php echo $trace ? ' style="--ed-trace: ' . esc_attr( $trace ) . ';"' : ''; ?> aria-hidden="true">
    <svg class="ed-art__geo" viewBox="0 0 100 100" preserveAspectRatio="<?php echo in_array( $family, [ 'object', 'diagram' ], true ) ? 'xMidYMid meet' : 'none'; ?>" focusable="false"><?php echo $geometry; // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?></svg>
    <?php if ( $number ) : ?><span class="ed-art__folio"><?php echo esc_html( sprintf( 'No. %02d', $number ) ); ?></span><?php endif; ?>
    <span class="ed-art__key"><?php echo esc_html( $key ); ?></span>
    <?php if ( $second ) : ?><span class="ed-art__second"><?php echo esc_html( $second ); ?></span><?php endif; ?>
    <?php if ( 'ledger' === $family ) : ?>
    <span class="ed-art__ledger"><span><?php echo esc_html( $year ); ?></span></span>
    <?php endif; ?>
</span>
    <?php
    return trim( (string) ob_get_clean() );
}

/**
 * An essay's visual: its photograph when the file exists, else its plate.
 */
function summit_fol_visual( int $post_id, string $size = 'large', array $attr = [], string $context = 'card' ): string {
    $image = function_exists( 'summit_fol_image' ) ? summit_fol_image( $post_id, $size, $attr ) : '';
    return $image ?: summit_editorial_art( $post_id, $context );
}

add_action( 'wp_enqueue_scripts', static function (): void {
    wp_enqueue_style( 'summit-editorial-art', get_template_directory_uri() . '/assets/css/editorial-art.css', [ 'summit-components' ], wp_get_theme()->get( 'Version' ) );
} );
