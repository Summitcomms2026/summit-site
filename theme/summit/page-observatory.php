<?php
/**
 * Template Name: Observatory Overview
 * Location: page-observatory.php
 *
 * The Observatory's landing page: a long-form chaptered page built on the
 * internal page shell's grammar.
 *
 *   hero     the shell hero (dark) — eyebrow · H1 · standfirst · CTA · share
 *   body     the page's own content: chapters authored as core blocks in
 *            the editor (groups, headings, paragraphs, lists), styled by
 *            assets/css/observatory.css through their block class names
 *   footer   the global CTA footer (Let's Talk)
 *
 * The narrative is editorial content, not template prose: every word on
 * the page beneath the hero is edited in the block editor. This template
 * adds only the hero, the reveal attributes and the CTA footer.
 *
 * Page fields (post meta, as page-shell.php):
 *   summit_eyebrow      eyebrow, parts separated by " / "
 *   summit_standfirst   standfirst beneath the title
 *   summit_utilities    comma list of utility actions, e.g. "share"
 *
 * Block class names the stylesheet understands:
 *   obs-chapter (+ --tinted | --dark | --plain)   a chapter: <section> group
 *   obs-marker      chapter number and name, e.g. "01 — What it is"
 *   obs-lead        a lead paragraph
 *   obs-pull        a pull statement
 *   obs-register    the field's quantities (list; <strong> holds the figure)
 *   obs-note        a quiet note under a register
 *   obs-finding     a highlighted finding (group)
 *   obs-pillars / obs-pillar / obs-derived        the method (groups)
 *   obs-principles  governance principles (list)
 *   obs-audiences / obs-audience                  the two pathways (groups)
 *   obs-sectors     the sector register (ordered list)
 *   obs-steps / obs-step                          how it works (groups)
 *   obs-outcome     the classification a reading arrives at (group)
 *   obs-link        a text link with an arrow (paragraph)
 *   obs-cta         a primary call to action (paragraph holding a link)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$page_id    = get_queried_object_id();
$path       = '/' . trim( (string) get_page_uri( $page_id ), '/' );
$standfirst = get_post_meta( $page_id, 'summit_standfirst', true );
$utilities  = array_filter( array_map( 'trim', explode( ',', (string) get_post_meta( $page_id, 'summit_utilities', true ) ) ) );
$eyebrow    = array_filter( array_map( 'trim', explode( '/', (string) get_post_meta( $page_id, 'summit_eyebrow', true ) ) ) );

// Hero actions: the section's contextual CTA (from the navigation config),
// then utilities — exactly as the page shell assembles them.
$actions = [];
foreach ( summit_nav( 'primary' ) ?: [] as $section ) {
    if ( $section['path'] !== $path ) {
        continue;
    }
    foreach ( $section['children'] ?? [] as $child ) {
        if ( ! empty( $child['action'] ) ) {
            $actions[] = [ 'type' => 'cta', 'label' => $child['label'], 'url' => summit_nav_url( $child ) ];
        }
    }
}
foreach ( $utilities as $mode ) {
    $actions[] = [ 'type' => 'utility', 'mode' => sanitize_key( $mode ) ];
}

add_action( 'wp_enqueue_scripts', static function () {
    $v = wp_get_theme()->get( 'Version' );
    wp_enqueue_style( 'summit-page-shell', get_template_directory_uri() . '/assets/css/page-shell.css', [ 'summit-navigation' ], $v );
    wp_enqueue_style( 'summit-observatory', get_template_directory_uri() . '/assets/css/observatory.css', [ 'summit-page-shell' ], $v );
} );

add_filter( 'body_class', static function ( array $classes ): array {
    return array_merge( $classes, [ 'summit-shell', 'summit-shell--dark', 'summit-observatory' ] );
} );

/*
 * Reveal: give the authored content the site's existing reveal grammar
 * (assets/js/scroll-animate.js) without asking editors to manage it.
 * Markers fade; headings and paragraphs lift; repeated items stagger.
 */
$summit_obs_reveal = static function ( string $html ): string {
    if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
        return $html;
    }
    $staggered = [ 'obs-pillar', 'obs-step', 'obs-audience' ];
    $count     = [];
    $p         = new WP_HTML_Tag_Processor( $html );
    while ( $p->next_tag() ) {
        $tag = $p->get_tag();
        if ( $p->has_class( 'obs-marker' ) ) {
            $p->set_attribute( 'data-reveal', 'fade' );
            continue;
        }
        foreach ( $staggered as $class ) {
            if ( $p->has_class( $class ) ) {
                $count[ $class ] = ( $count[ $class ] ?? 0 ) + 1;
                $p->set_attribute( 'data-reveal', '' );
                $p->set_attribute( 'data-reveal-delay', (string) min( 6, $count[ $class ] ) );
                continue 2;
            }
        }
        if ( $p->has_class( 'obs-register' ) || $p->has_class( 'obs-sectors' ) || $p->has_class( 'obs-principles' )
            || $p->has_class( 'obs-finding' ) || $p->has_class( 'obs-derived' ) || $p->has_class( 'obs-outcome' ) ) {
            $p->set_attribute( 'data-reveal', 'fade' );
            continue;
        }
        if ( 'H2' === $tag || $p->has_class( 'obs-lead' ) || $p->has_class( 'obs-pull' ) || $p->has_class( 'obs-cta' ) ) {
            $p->set_attribute( 'data-reveal', '' );
        }
    }
    return $p->get_updated_html();
};

get_header();
?>

<main class="site-main site-main--shell">
    <?php while ( have_posts() ) : the_post(); ?>

    <article <?php post_class( 'shell obs' ); ?>>

        <?php get_template_part( 'parts/global/page-hero', null, [
            'eyebrow'    => $eyebrow,
            'title'      => get_the_title(),
            'standfirst' => $standfirst,
            'tone'       => 'dark',
            'actions'    => $actions,
        ] ); ?>

        <?php
        $content = apply_filters( 'the_content', get_the_content() );
        if ( trim( $content ) ) {
            echo '<div class="obs-body">' . $summit_obs_reveal( $content ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- the_content output
        }
        ?>

    </article>

    <?php endwhile; ?>

    <?php get_template_part( 'parts/global/cta-footer' ); ?>
</main>

<?php
get_footer();
