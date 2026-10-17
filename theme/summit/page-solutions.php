<?php
/**
 * Template Name: Solutions
 * Location: page-solutions.php
 *
 * The Solutions section: the Overview (/solutions) and its Industry,
 * Function and Need pages. Built on the internal page shell's grammar.
 *
 *   hero      the shell hero — eyebrow · H1 · standfirst · CTA · links
 *   body      the page's own content: chapters authored as core blocks in
 *             the editor, styled by assets/css/solutions.css through their
 *             block class names. Two shortcodes place config-driven panels:
 *             [summit_solutions_ways]        the three ways in (lens switcher)
 *             [summit_solutions_programmes]  the programme rows
 *   related   on Solutions pages, the other entries of the same lens
 *   footer    the global CTA footer (Let's Talk)
 *
 * Canonical source for the content lives in site-content/solutions/
 * (see its manifest.json).
 *
 * Page fields (post meta):
 *   summit_eyebrow           eyebrow, parts separated by " / "
 *   summit_headline          optional display H1 (defaults to the page title)
 *   summit_tone              paper (default) | tinted | dark
 *   summit_standfirst        standfirst beneath the title
 *   summit_cta_label/_url    hero CTA (defaults to the global Let's Talk)
 *   summit_secondary_label/_url  a quiet contextual link beside the CTA
 *   summit_utilities         comma list of utility actions, e.g. "share"
 *
 * Entry pages feed the overview: "summit_summary" (one line, shown in the
 * index) and "summit_visual" (attachment ID or URL, a sector's image). An
 * entry page without content of its own shows its visual as a band.
 *
 * Overview panel classes (blocks): sx-field (a full-width panel), sx-reset,
 *   sx-feature / sx-feature__text / sx-ledger / sx-ledger__col(--read),
 *   sx-programmes, sx-intel, sx-head, sx-strips / sx-strip, sx-label,
 *   sx-more.
 *
 * Block class names the stylesheet understands:
 *   sol-chapter (+ --tinted | --dark | --close)  a chapter: <section> group
 *   sol-kicker      the chapter's short label
 *   sol-statement   a heading at statement scale (full measure)
 *   sol-lead        a lead paragraph
 *   sol-note        a quiet note
 *   sol-list        a ruled list
 *   sol-rows / sol-row        term and reading (groups: h3 + paragraph)
 *   sol-pair        paired readings, <strong> labels (group of paragraphs)
 *   sol-link        a text link with an arrow (paragraph)
 *   sol-cta         a primary call to action (paragraph holding a link)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$page_id    = get_queried_object_id();
$path       = '/' . trim( (string) get_page_uri( $page_id ), '/' );
$meta       = static fn( string $key ): string => (string) get_post_meta( $page_id, $key, true );
$tone       = $meta( 'summit_tone' ) ?: 'paper';
$utilities  = array_filter( array_map( 'trim', explode( ',', $meta( 'summit_utilities' ) ) ) );
$eyebrow    = array_filter( array_map( 'trim', explode( '/', $meta( 'summit_eyebrow' ) ) ) );
$section    = summit_nav( 'primary' )['solutions'] ?? [];
$is_index   = ( $section['path'] ?? '' ) === $path;

// Where this page sits: its lens (group) and the other entries of that lens.
$lens = null;
foreach ( $section['groups'] ?? [] as $group ) {
    if ( in_array( $path, array_column( $group['children'], 'path' ), true ) ) {
        $lens = $group;
        break;
    }
}

// Hero actions: the page's CTA (default: the global Let's Talk), a quiet
// contextual link, then utilities.
$nav_cta = summit_nav( 'cta' );
$actions = [ [
    'type'  => 'cta',
    'label' => $meta( 'summit_cta_label' ) ?: ( $nav_cta['label'] ?? '' ),
    'url'   => $meta( 'summit_cta_url' ) ? summit_meta_url( $meta( 'summit_cta_url' ) ) : summit_nav_url( $nav_cta ),
] ];
if ( $meta( 'summit_secondary_label' ) && $meta( 'summit_secondary_url' ) ) {
    $actions[] = [
        'type'  => 'utility',
        'mode'  => 'link',
        'icon'  => false,
        'label' => $meta( 'summit_secondary_label' ),
        'url'   => summit_meta_url( $meta( 'summit_secondary_url' ) ),
        'class' => 'action--contextual',
    ];
}
foreach ( $utilities as $mode ) {
    $actions[] = [ 'type' => 'utility', 'mode' => sanitize_key( $mode ) ];
}

$slug = $is_index ? 'overview' : sanitize_html_class( basename( $path ) );

add_action( 'wp_enqueue_scripts', static function () use ( $is_index ) {
    $v = wp_get_theme()->get( 'Version' );
    wp_enqueue_style( 'summit-page-shell', get_template_directory_uri() . '/assets/css/page-shell.css', [ 'summit-navigation' ], $v );
    wp_enqueue_style( 'summit-solutions', get_template_directory_uri() . '/assets/css/solutions.css', [ 'summit-page-shell' ], $v );
    if ( $is_index ) {
        wp_enqueue_script( 'summit-solutions', get_template_directory_uri() . '/assets/js/solutions.js', [], $v, true );
    }
} );

add_filter( 'body_class', static function ( array $classes ) use ( $tone, $slug ): array {
    $classes[] = 'summit-shell';
    $classes[] = 'summit-solutions';
    $classes[] = 'summit-solutions--' . $slug;
    if ( 'dark' === $tone ) {
        $classes[] = 'summit-shell--dark';
    }
    return $classes;
} );

// Config-driven panels, placed by the editor.
add_shortcode( 'summit_solutions_ways', static function () use ( $section ): string {
    ob_start();
    get_template_part( 'parts/solutions/ways-in', null, [ 'section' => $section ] );
    return (string) ob_get_clean();
} );
add_shortcode( 'summit_solutions_programmes', static function (): string {
    ob_start();
    get_template_part( 'parts/solutions/programmes' );
    return (string) ob_get_clean();
} );

/*
 * Reveal: the site's existing reveal grammar (assets/js/scroll-animate.js)
 * applied to the authored content. Kickers fade; headings and leads lift;
 * repeated rows stagger.
 */
$summit_sol_reveal = static function ( string $html ): string {
    if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
        return $html;
    }
    $count = 0;
    $p     = new WP_HTML_Tag_Processor( $html );
    while ( $p->next_tag() ) {
        if ( $p->has_class( 'sx-ways' ) ) {
            // The lens switcher manages its own states; only its index fades in.
            $p->set_attribute( 'data-reveal', 'fade' );
            while ( $p->next_tag() && ! $p->has_class( 'sx-reset' ) && ! $p->has_class( 'sx-field' ) ) {
                // skip the switcher's contents
            }
        }
        if ( $p->get_attribute( 'data-reveal' ) !== null ) {
            continue;
        }
        if ( $p->has_class( 'sol-kicker' ) || $p->has_class( 'sol-list' ) || $p->has_class( 'sol-pair' )
            || $p->has_class( 'sx-label' ) || $p->has_class( 'sx-ledger' ) || $p->has_class( 'sx-rows' ) ) {
            $p->set_attribute( 'data-reveal', 'fade' );
        } elseif ( $p->has_class( 'sol-row' ) || $p->has_class( 'sx-strip' ) ) {
            $p->set_attribute( 'data-reveal', '' );
            $p->set_attribute( 'data-reveal-delay', (string) min( 6, ++$count ) );
        } elseif ( $p->has_class( 'sol-rows' ) || $p->has_class( 'sx-strips' ) ) {
            $count = 0;
        } elseif ( 'H2' === $p->get_tag() || $p->has_class( 'sol-lead' ) || $p->has_class( 'sol-cta' )
            || $p->has_class( 'sx-reset__line' ) || $p->has_class( 'sx-reset__support' ) || $p->has_class( 'sx-head__intro' ) || $p->has_class( 'sx-feature__intro' ) ) {
            $p->set_attribute( 'data-reveal', '' );
        }
    }
    return $p->get_updated_html();
};

get_header();
?>

<main class="site-main site-main--shell">
    <?php while ( have_posts() ) : the_post(); ?>

    <article <?php post_class( 'shell sol' ); ?>>

        <?php get_template_part( 'parts/global/page-hero', null, [
            'eyebrow'    => $eyebrow,
            'title'      => $meta( 'summit_headline' ) ?: get_the_title(),
            'standfirst' => $meta( 'summit_standfirst' ),
            'tone'       => $tone,
            'actions'    => $actions,
        ] ); ?>

        <?php
        $content = apply_filters( 'the_content', get_the_content() );

        // An entry with a visual (summit_visual — the same image the
        // overview's industry field uses) opens on it as a band, before its
        // chapters: the image keeps its place now the page has its copy.
        $visual = $meta( 'summit_visual' );
        if ( $visual && ! $is_index ) :
            // A theme image may have a wide companion (<name>-wide.jpg) for the band.
            $wide = is_numeric( $visual ) ? '' : preg_replace( '/\.jpg$/', '-wide.jpg', $visual );
            if ( $wide && ! is_readable( ABSPATH . ltrim( (string) wp_parse_url( $wide, PHP_URL_PATH ), '/' ) ) ) {
                $wide = '';
            }
            $visual_html = is_numeric( $visual )
                ? wp_get_attachment_image( (int) $visual, 'large', false, [ 'alt' => '', 'loading' => 'eager' ] )
                : '<img src="' . esc_url( $wide ?: $visual ) . '" alt="" width="' . ( $wide ? 1600 : 800 ) . '" height="' . ( $wide ? 686 : 1000 ) . '" decoding="async">';
        ?>
        <figure class="sol-entry-visual" data-reveal="fade">
            <?php echo $visual_html; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above ?>
        </figure>
        <?php endif;

        if ( trim( $content ) ) {
            echo '<div class="sol-body">' . $summit_sol_reveal( $content ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- the_content output
        }
        ?>

    </article>

    <?php endwhile; ?>

    <?php
    if ( $lens ) {
        get_template_part( 'parts/solutions/related', null, [ 'section' => $section, 'lens' => $lens, 'path' => $path ] );
    }

    get_template_part( 'parts/global/cta-footer' );
    ?>
</main>

<?php
get_footer();
