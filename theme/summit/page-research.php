<?php
/**
 * Template Name: Research
 * Location: page-research.php
 *
 * The Research section: the Overview (/research), Touch Me Not, The Luxury
 * 100, White Papers and the Research Programme. Built on the internal page
 * shell's grammar and set like a book: a running head with a folio number
 * opens each chapter, text sits in a centred reading column, and
 * publications (covers, metadata, excerpts) break out to the full measure.
 *
 *   hero      the shell hero — eyebrow · H1 · standfirst · CTA · links
 *   body      the page's own content: chapters authored as core blocks in
 *             the editor, styled by assets/css/research.css through their
 *             block class names. [summit_research_chair] places The Luxury
 *             100's hundred places with its empty chair; [summit_research_artwork]
 *             shows a work of art when its file is present (see
 *             parts/research/artwork.php) and a designed stand-in otherwise.
 *   also      on a Research page, the rest of the collection
 *   footer    the global CTA footer (Let's Talk)
 *
 * Canonical source for the content lives in site-content/research/ (see
 * its manifest.json). Approved images live in assets/img/research/;
 * development artwork in assets/img/research/provisional/ (git-ignored).
 *
 * Page fields (post meta):
 *   summit_eyebrow           eyebrow, parts separated by " / "
 *   summit_headline          optional display H1 (defaults to the page title)
 *   summit_tone              paper (default) | tinted | dark
 *   summit_standfirst        standfirst beneath the title
 *   summit_cta_label/_url    hero CTA (defaults to the global Let's Talk)
 *   summit_secondary_label/_url  a quiet contextual link beside the CTA
 *   summit_utilities         comma list of utility actions, e.g. "share"
 *   summit_summary           one line, read by the collection register
 *
 * Block class names the stylesheet understands:
 *   res-chapter (+ --tinted | --dark | --close)  a chapter: <section> group
 *   res-head          running head: the section's name; the folio number is
 *                     drawn by the stylesheet, decoratively
 *   res-statement     a statement in the serif voice
 *   res-lead          a lead paragraph
 *   res-note          a quiet note
 *   res-publication / res-publication__text      a publication: cover + text
 *   res-cover         a cover or frontispiece (image)
 *   res-meta          publication metadata (list; <strong> holds the term)
 *   res-excerpt       a short excerpt (quote)
 *   res-figures       a reading's figures (list; <strong> holds the figure)
 *   res-terms         defined terms (list; <strong> holds the term)
 *   res-contents      a contents list (group of parts: h3 + list)
 *   res-findings / res-finding   short findings (groups: h3 + paragraph)
 *   res-objects       a register of objects (list)
 *   res-entry         a catalogue entry (group)
 *   res-typecover     a typographic cover where no cleared image exists
 *   res-wide          take the full measure
 *   res-link          a text link with an arrow (paragraph)
 *   res-cta           a primary call to action (paragraph holding a link)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$page_id   = get_queried_object_id();
$path      = '/' . trim( (string) get_page_uri( $page_id ), '/' );
$meta      = static fn( string $key ): string => (string) get_post_meta( $page_id, $key, true );
$tone      = $meta( 'summit_tone' ) ?: 'paper';
$utilities = array_filter( array_map( 'trim', explode( ',', $meta( 'summit_utilities' ) ) ) );
$eyebrow   = array_filter( array_map( 'trim', explode( '/', $meta( 'summit_eyebrow' ) ) ) );
$section   = summit_nav( 'primary' )['research'] ?? [];
$is_index  = ( $section['path'] ?? '' ) === $path;
$is_child  = in_array( $path, array_column( $section['children'] ?? [], 'path' ), true );

$nav_cta = summit_nav( 'cta' );
$actions = [ [
    'type'  => 'cta',
    'label' => $meta( 'summit_cta_label' ) ?: ( $nav_cta['label'] ?? '' ),
    'url'   => $meta( 'summit_cta_url' ) ? home_url( $meta( 'summit_cta_url' ) ) : summit_nav_url( $nav_cta ),
] ];
if ( $meta( 'summit_secondary_label' ) && $meta( 'summit_secondary_url' ) ) {
    $actions[] = [
        'type'  => 'utility',
        'mode'  => 'link',
        'icon'  => false,
        'label' => $meta( 'summit_secondary_label' ),
        'url'   => home_url( $meta( 'summit_secondary_url' ) ),
        'class' => 'action--contextual',
    ];
}
foreach ( $utilities as $mode ) {
    $actions[] = [ 'type' => 'utility', 'mode' => sanitize_key( $mode ) ];
}

$slug = $is_index ? 'overview' : sanitize_html_class( basename( $path ) );

add_action( 'wp_enqueue_scripts', static function () {
    $v = wp_get_theme()->get( 'Version' );
    wp_enqueue_style( 'summit-page-shell', get_template_directory_uri() . '/assets/css/page-shell.css', [ 'summit-navigation' ], $v );
    wp_enqueue_style( 'summit-research', get_template_directory_uri() . '/assets/css/research.css', [ 'summit-page-shell' ], $v );
} );

add_filter( 'body_class', static function ( array $classes ) use ( $tone, $slug ): array {
    $classes[] = 'summit-shell';
    $classes[] = 'summit-research';
    $classes[] = 'summit-research--' . $slug;
    if ( 'dark' === $tone ) {
        $classes[] = 'summit-shell--dark';
    }
    return $classes;
} );

// The Luxury 100's hundred places, with the empty chair, placed by the editor.
add_shortcode( 'summit_research_chair', static function ( $atts ): string {
    $atts = shortcode_atts( [ 'empty' => '83', 'places' => '100' ], $atts );
    ob_start();
    get_template_part( 'parts/research/chair', null, [ 'empty' => (int) $atts['empty'], 'places' => (int) $atts['places'] ] );
    return (string) ob_get_clean();
} );

// Artwork shown only when its file is present; a designed stand-in otherwise.
add_shortcode( 'summit_research_artwork', static function ( $atts ): string {
    ob_start();
    get_template_part( 'parts/research/artwork', null, shortcode_atts( [
        'name' => '', 'kind' => 'cover', 'alt' => '', 'credit' => '',
        'title' => '', 'subtitle' => '', 'byline' => '', 'line' => '',
    ], $atts ) );
    return (string) ob_get_clean();
} );

/*
 * Reveal: the site's existing reveal grammar (assets/js/scroll-animate.js)
 * applied to the authored content.
 */
$summit_res_reveal = static function ( string $html ): string {
    if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
        return $html;
    }
    $count = 0;
    $p     = new WP_HTML_Tag_Processor( $html );
    while ( $p->next_tag() ) {
        if ( null !== $p->get_attribute( 'data-reveal' ) ) {
            continue;
        }
        if ( $p->has_class( 'res-head' ) || $p->has_class( 'res-cover' ) || $p->has_class( 'res-art' ) || $p->has_class( 'res-meta' ) || $p->has_class( 'res-figures' )
            || $p->has_class( 'res-terms' ) || $p->has_class( 'res-objects' ) || $p->has_class( 'res-contents' ) || $p->has_class( 'res-typecover' ) ) {
            $p->set_attribute( 'data-reveal', 'fade' );
        } elseif ( $p->has_class( 'res-finding' ) || $p->has_class( 'res-entry' ) ) {
            $p->set_attribute( 'data-reveal', '' );
            $p->set_attribute( 'data-reveal-delay', (string) min( 6, ++$count ) );
        } elseif ( 'H2' === $p->get_tag() || $p->has_class( 'res-statement' ) || $p->has_class( 'res-lead' ) || $p->has_class( 'res-excerpt' ) || $p->has_class( 'res-cta' ) ) {
            $p->set_attribute( 'data-reveal', '' );
        }
    }
    return $p->get_updated_html();
};

get_header();
?>

<main class="site-main site-main--shell">
    <?php while ( have_posts() ) : the_post(); ?>

    <article <?php post_class( 'shell res' ); ?>>

        <?php get_template_part( 'parts/global/page-hero', null, [
            'eyebrow'    => $eyebrow,
            'title'      => $meta( 'summit_headline' ) ?: get_the_title(),
            'standfirst' => $meta( 'summit_standfirst' ),
            'tone'       => $tone,
            'actions'    => $actions,
        ] ); ?>

        <?php
        $content = apply_filters( 'the_content', get_the_content() );
        if ( trim( $content ) ) {
            echo '<div class="res-body">' . $summit_res_reveal( $content ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- the_content output
        }
        ?>

    </article>

    <?php endwhile; ?>

    <?php
    if ( $is_child ) {
        get_template_part( 'parts/research/collection', null, [ 'section' => $section, 'path' => $path ] );
    }

    get_template_part( 'parts/global/cta-footer' );
    ?>
</main>

<?php
get_footer();
