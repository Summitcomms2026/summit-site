<?php
/**
 * Template Name: About
 * Location: page-about.php
 *
 * The About section: the Overview (/about), Origin Story, Manifesto, Why It
 * Matters, Partners & People and How We Work. The institutional argument
 * behind the rest of the site, and its most human section: chapters open
 * on a marker in the margin, statements speak in the serif, and paper and
 * black fields alternate with the argument.
 *
 *   hero      the shell hero — eyebrow · H1 · standfirst · CTA · link
 *   body      the page's own content: chapters authored as core blocks,
 *             styled by assets/css/about.css through their class names.
 *             [summit_about_sequence] places the section's editorial index;
 *             [summit_about_portrait] a portrait, or a typographic monogram
 *             where no portrait file exists (parts/about/portrait.php).
 *   next      on an About page, the way on through the section
 *   footer    the global CTA footer, with the section's own invitation
 *
 * Canonical source for the content lives in site-content/about/ (see its
 * manifest.json). Portraits live in assets/img/about/.
 *
 * Page fields (post meta):
 *   summit_eyebrow           eyebrow, parts separated by " / "
 *   summit_headline          optional display H1 (defaults to the page title)
 *   summit_tone              paper (default) | tinted | dark
 *   summit_standfirst        standfirst beneath the title
 *   summit_cta_label/_url    hero CTA (defaults to the global Let's Talk)
 *   summit_secondary_label/_url  a quiet contextual link beside the CTA
 *   summit_utilities         comma list of utility actions, e.g. "share"
 *   summit_summary           one line, read by the section's index
 *
 * Block class names the stylesheet understands:
 *   ab-chapter (+ --dark | --tinted | --close | --principle | --principle-line | --preface)
 *   ab-marker            the chapter's marker, set in the margin
 *   ab-statement         a heading at statement scale
 *   ab-lead / ab-note    a lead paragraph / a quiet note
 *   ab-forms / ab-form   the five forms of the work (groups: h3 · p · link)
 *   ab-stages / ab-stage the evolution register (label · h3 · text)
 *   ab-questions / ab-decisions  ruled lists
 *   ab-principle__num / __word / __claim / __note   a manifesto proposition
 *   ab-principle-line    the principle, at display scale
 *   ab-pressures / ab-pressure   paired terms and readings
 *   ab-profile           a person: portrait shortcode + text
 *   ab-works / ab-work   working principles (number · h3 · p)
 *   ab-pair / ab-pair__item      two forms side by side
 *   ab-link / ab-cta     a text link with an arrow / a primary CTA
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
$section   = summit_nav( 'primary' )['about'] ?? [];
$is_index  = ( $section['path'] ?? '' ) === $path;
$slug      = $is_index ? 'overview' : sanitize_html_class( basename( $path ) );

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

add_action( 'wp_enqueue_scripts', static function () {
    $v = wp_get_theme()->get( 'Version' );
    wp_enqueue_style( 'summit-page-shell', get_template_directory_uri() . '/assets/css/page-shell.css', [ 'summit-navigation' ], $v );
    wp_enqueue_style( 'summit-about', get_template_directory_uri() . '/assets/css/about.css', [ 'summit-page-shell' ], $v );
} );

add_filter( 'body_class', static function ( array $classes ) use ( $tone, $slug ): array {
    $classes[] = 'summit-shell';
    $classes[] = 'summit-about';
    $classes[] = 'summit-about--' . $slug;
    if ( 'dark' === $tone ) {
        $classes[] = 'summit-shell--dark';
    }
    return $classes;
} );

// The section's editorial index, placed by the editor.
add_shortcode( 'summit_about_sequence', static function () use ( $section, $path ): string {
    ob_start();
    get_template_part( 'parts/about/sequence', null, [ 'section' => $section, 'path' => $path ] );
    return (string) ob_get_clean();
} );

// A portrait when its file exists; a typographic monogram otherwise.
// A publication object from the Research family (Origin Story shows the
// things Summit made): the same part, so an approved file replaces the
// stand-in everywhere at once.
if ( ! shortcode_exists( 'summit_research_artwork' ) ) {
    add_shortcode( 'summit_research_artwork', static function ( $atts ): string {
        ob_start();
        get_template_part( 'parts/research/artwork', null, shortcode_atts( [
            'name' => '', 'kind' => 'cover', 'alt' => '', 'credit' => '',
            'title' => '', 'subtitle' => '', 'byline' => '', 'line' => '',
        ], $atts ) );
        return (string) ob_get_clean();
    } );
}

add_shortcode( 'summit_about_portrait', static function ( $atts ): string {
    ob_start();
    get_template_part( 'parts/about/portrait', null, shortcode_atts( [ 'name' => '', 'initials' => '', 'alt' => '' ], $atts ) );
    return (string) ob_get_clean();
} );

/*
 * Reveal: the site's existing reveal grammar (assets/js/scroll-animate.js)
 * applied to the authored content.
 */
$summit_ab_reveal = static function ( string $html ): string {
    if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
        return $html;
    }
    $count = 0;
    $p     = new WP_HTML_Tag_Processor( $html );
    while ( $p->next_tag() ) {
        if ( null !== $p->get_attribute( 'data-reveal' ) ) {
            continue;
        }
        if ( $p->has_class( 'ab-marker' ) || $p->has_class( 'ab-principle__num' ) || $p->has_class( 'ab-questions' )
            || $p->has_class( 'ab-decisions' ) || $p->has_class( 'ab-portrait' ) ) {
            $p->set_attribute( 'data-reveal', 'fade' );
        } elseif ( $p->has_class( 'ab-form' ) || $p->has_class( 'ab-stage' ) || $p->has_class( 'ab-pressure' )
            || $p->has_class( 'ab-work' ) || $p->has_class( 'ab-pair__item' ) ) {
            $p->set_attribute( 'data-reveal', '' );
            $p->set_attribute( 'data-reveal-delay', (string) min( 4, ++$count ) );
        } elseif ( $p->has_class( 'ab-forms' ) || $p->has_class( 'ab-stages' ) || $p->has_class( 'ab-pressures' )
            || $p->has_class( 'ab-works' ) || $p->has_class( 'ab-pair' ) ) {
            $count = 0;
        } elseif ( $p->has_class( 'ab-statement' ) || $p->has_class( 'ab-lead' ) || $p->has_class( 'ab-principle__word' )
            || $p->has_class( 'ab-principle__claim' ) || $p->has_class( 'ab-principle__note' ) || $p->has_class( 'ab-principle-line' )
            || $p->has_class( 'ab-cta' ) || $p->has_class( 'ab-profile__text' ) ) {
            $p->set_attribute( 'data-reveal', '' );
        }
    }
    return $p->get_updated_html();
};

get_header();
?>

<main class="site-main site-main--shell">
    <?php while ( have_posts() ) : the_post(); ?>

    <article <?php post_class( 'shell ab' ); ?>>

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
            echo '<div class="ab-body">' . $summit_ab_reveal( $content ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- the_content output
        }
        ?>

    </article>

    <?php endwhile; ?>

    <?php
    if ( ! $is_index ) {
        get_template_part( 'parts/about/next', null, [ 'section' => $section, 'path' => $path ] );
    }

    get_template_part( 'parts/global/cta-footer', null, [
        'headline' => __( 'Begin with the question.', 'summit' ),
        'body'     => __( 'If it concerns the position, authority, experience or stewardship of a luxury house, talk to Summit.', 'summit' ),
    ] );
    ?>
</main>

<?php
get_footer();
