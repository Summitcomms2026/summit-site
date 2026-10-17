<?php
/**
 * Template Name: Programmes
 * Location: page-programmes.php
 *
 * The Programmes section: the Overview (/programmes) and the programme pages
 * (Maison Formation, Editorial & Influence, Digital Growth, Client
 * Experience, Agentic AI, Brand & IP Stewardship). Built on the internal
 * page shell's grammar; slower and more structured than Solutions.
 *
 *   hero      the shell hero — eyebrow · H1 · standfirst · CTA · utilities
 *   body      the page's own content: chapters authored as core blocks in
 *             the editor, styled by assets/css/programmes.css through their
 *             block class names. [summit_programmes_register] places the
 *             numbered programme register, drawn from the navigation config.
 *   others    on a programme page, the other programmes
 *   footer    the global CTA footer (Let's Talk)
 *
 * Canonical source for the content lives in site-content/programmes/ (see
 * its manifest.json).
 *
 * Page fields (post meta):
 *   summit_eyebrow           eyebrow, parts separated by " / "
 *   summit_headline          optional display H1 (defaults to the page title)
 *   summit_tone              paper (default) | tinted | dark
 *   summit_standfirst        standfirst beneath the title
 *   summit_cta_label/_url    hero CTA (defaults to the global Let's Talk)
 *   summit_utilities         comma list of utility actions, e.g. "share"
 * Programme pages also feed the register:
 *   summit_summary           the programme's one-sentence proposition
 *   summit_moment            who it is for, or the moment it answers
 *   summit_related           comma list of Solutions paths it draws on
 *
 * Block class names the stylesheet understands:
 *   prg-chapter (+ --tinted | --dark | --close)  a chapter: <section> group
 *   prg-mark         the chapter's numeral and name, e.g. "II · The questions"
 *   prg-statement    a statement in the serif voice
 *   prg-lead         a lead paragraph
 *   prg-note         a quiet note
 *   prg-contrast / prg-contrast__col     two columns set against each other
 *   prg-questions / prg-question         territories: label + question (groups)
 *   prg-list         a ruled list
 *   prg-inline       a short list set inline
 *   prg-feature      a featured programme (group)
 *   prg-link         a text link with an arrow (paragraph)
 *   prg-cta          a primary call to action (paragraph holding a link)
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
$section   = summit_nav( 'primary' )['programmes'] ?? [];
$is_index  = ( $section['path'] ?? '' ) === $path;
$is_child  = in_array( $path, array_column( $section['children'] ?? [], 'path' ), true );

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
    wp_enqueue_style( 'summit-programmes', get_template_directory_uri() . '/assets/css/programmes.css', [ 'summit-page-shell' ], $v );
    if ( $is_index ) {
        wp_enqueue_script( 'summit-programmes', get_template_directory_uri() . '/assets/js/programmes.js', [], $v, true );
    }
} );

add_filter( 'body_class', static function ( array $classes ) use ( $tone, $slug ): array {
    $classes[] = 'summit-shell';
    $classes[] = 'summit-programmes';
    $classes[] = 'summit-programmes--' . $slug;
    if ( 'dark' === $tone ) {
        $classes[] = 'summit-shell--dark';
    }
    return $classes;
} );

// The numbered register, placed by the editor.
add_shortcode( 'summit_programmes_register', static function () use ( $section ): string {
    ob_start();
    get_template_part( 'parts/programmes/register', null, [ 'section' => $section ] );
    return (string) ob_get_clean();
} );

/*
 * Reveal: the site's existing reveal grammar (assets/js/scroll-animate.js)
 * applied to the authored content.
 */
$summit_prg_reveal = static function ( string $html ): string {
    if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
        return $html;
    }
    $count = 0;
    $p     = new WP_HTML_Tag_Processor( $html );
    while ( $p->next_tag() ) {
        if ( null !== $p->get_attribute( 'data-reveal' ) ) {
            continue;
        }
        if ( $p->has_class( 'prg-mark' ) || $p->has_class( 'prg-list' ) || $p->has_class( 'prg-contrast' ) || $p->has_class( 'prg-inline' ) ) {
            $p->set_attribute( 'data-reveal', 'fade' );
        } elseif ( $p->has_class( 'prg-question' ) ) {
            $p->set_attribute( 'data-reveal', '' );
            $p->set_attribute( 'data-reveal-delay', (string) ( 1 + ( $count++ % 2 ) ) );
        } elseif ( 'H2' === $p->get_tag() || $p->has_class( 'prg-statement' ) || $p->has_class( 'prg-lead' ) || $p->has_class( 'prg-cta' ) ) {
            $p->set_attribute( 'data-reveal', '' );
        }
    }
    return $p->get_updated_html();
};

get_header();
?>

<main class="site-main site-main--shell">
    <?php while ( have_posts() ) : the_post(); ?>

    <article <?php post_class( 'shell prg' ); ?>>

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
            echo '<div class="prg-body">' . $summit_prg_reveal( $content ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- the_content output
        }
        ?>

    </article>

    <?php endwhile; ?>

    <?php
    if ( $is_child ) {
        get_template_part( 'parts/programmes/others', null, [ 'section' => $section, 'path' => $path ] );
    }

    get_template_part( 'parts/global/cta-footer' );
    ?>
</main>

<?php
get_footer();
