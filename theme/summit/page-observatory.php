<?php
/**
 * Template Name: Observatory
 * Location: page-observatory.php
 *
 * The Observatory section: the Overview and its chapters (For CMOs, For
 * Family Offices & Investors, The Field, How It Works, Request a Private
 * Briefing). Long-form chaptered pages built on the internal page shell's
 * grammar.
 *
 *   hero      the shell hero — eyebrow · H1 · standfirst · CTA · utilities
 *   body      the page's own content: chapters authored as core blocks in
 *             the editor (groups, headings, paragraphs, lists), styled by
 *             assets/css/observatory.css through their block class names
 *   form      the private briefing form, where the page asks for it
 *   sequence  on chapter pages, the section's reading order
 *   footer    the global CTA footer (Let's Talk)
 *
 * The narrative is editorial content, not template prose: every word in
 * the body is edited in the block editor. Canonical source for the content
 * lives in site-content/observatory/ (see its manifest.json).
 *
 * Page fields (post meta, as page-shell.php):
 *   summit_eyebrow      eyebrow, parts separated by " / "
 *   summit_headline     optional display H1 (defaults to the page title)
 *   summit_tone         dark | tinted | paper (default)
 *   summit_standfirst   standfirst beneath the title
 *   summit_utilities    comma list of utility actions, e.g. "share, print"
 *   summit_form         "briefing" renders the private briefing form
 *
 * Block class names the stylesheet understands:
 *   obs-chapter (+ --tinted | --dark | --briefing)  a chapter: <section> group
 *   obs-marker      chapter number and name, e.g. "01 — What it is"
 *   obs-statement   a heading at statement scale
 *   obs-lead        a lead paragraph
 *   obs-pull        a pull statement (serif)
 *   obs-register    quantities (list; <strong> holds the figure)
 *   obs-note        a quiet note
 *   obs-finding     a highlighted finding (group)
 *   obs-pillars / obs-pillar / obs-derived        the method (groups)
 *   obs-rows / obs-row                            numbered readings (groups)
 *   obs-readings / obs-reading                      an annotated reading sequence (groups)
 *   obs-principles  principles (list)
 *   obs-list        a ruled two-column list
 *   obs-audiences / obs-audience                  pathways (groups)
 *   obs-sectors     the sector register (ordered list)
 *   obs-steps / obs-step                          a sequence (groups)
 *   obs-outcome     paired readings, <strong> labels (group of paragraphs)
 *   obs-link        a text link with an arrow (paragraph)
 *   obs-cta         a primary call to action (paragraph holding a link)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$page_id    = get_queried_object_id();
$path       = '/' . trim( (string) get_page_uri( $page_id ), '/' );
$tone       = get_post_meta( $page_id, 'summit_tone', true ) ?: 'paper';
$headline   = get_post_meta( $page_id, 'summit_headline', true );
$standfirst = get_post_meta( $page_id, 'summit_standfirst', true );
$form       = get_post_meta( $page_id, 'summit_form', true );
$utilities  = array_filter( array_map( 'trim', explode( ',', (string) get_post_meta( $page_id, 'summit_utilities', true ) ) ) );
$eyebrow    = array_filter( array_map( 'trim', explode( '/', (string) get_post_meta( $page_id, 'summit_eyebrow', true ) ) ) );

// The section this page belongs to (the Overview, or one of its chapters).
$section = null;
foreach ( summit_nav( 'primary' ) ?: [] as $candidate ) {
    $paths = array_column( $candidate['children'] ?? [], 'path' );
    if ( $candidate['path'] === $path || in_array( $path, $paths, true ) ) {
        $section = $candidate;
        break;
    }
}
$is_overview = $section && $section['path'] === $path;

// Hero actions: the section's contextual CTA (not on its own page), then
// utilities — exactly as the page shell assembles them.
// The page's own CTAs (from the editorial handover) come first.
$actions = summit_meta_actions( $page_id );
if ( ! $actions ) {
    foreach ( $section['children'] ?? [] as $child ) {
        if ( ! empty( $child['action'] ) && $child['path'] !== $path ) {
            $actions[] = [ 'type' => 'cta', 'label' => $child['label'], 'url' => summit_nav_url( $child ) ];
        }
    }
}
foreach ( $utilities as $mode ) {
    $actions[] = [ 'type' => 'utility', 'mode' => sanitize_key( $mode ) ];
}

$slug = $is_overview ? 'overview' : sanitize_html_class( basename( $path ) );

// The briefing form, in place inside the page's own form panel
add_shortcode( 'summit_briefing_form', static function () use ( $path ): string {
    ob_start();
    get_template_part( 'parts/observatory/briefing-form', null, [ 'source' => $path, 'embedded' => true ] );
    return (string) ob_get_clean();
} );

add_action( 'wp_enqueue_scripts', static function () use ( $form ) {
    $v = wp_get_theme()->get( 'Version' );
    wp_enqueue_style( 'summit-page-shell', get_template_directory_uri() . '/assets/css/page-shell.css', [ 'summit-navigation' ], $v );
    wp_enqueue_style( 'summit-observatory', get_template_directory_uri() . '/assets/css/observatory.css', [ 'summit-page-shell' ], $v );

    // The briefing form submits through Summit's existing enquiry handler.
    if ( 'briefing' === $form ) {
        wp_enqueue_script( 'summit-enquiry-form', get_template_directory_uri() . '/assets/js/enquiry-form.js', [], $v, true );
        wp_localize_script( 'summit-enquiry-form', 'summitEnquiry', [ 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ] );
    }
} );

add_filter( 'body_class', static function ( array $classes ) use ( $tone, $slug ): array {
    $classes[] = 'summit-shell';
    $classes[] = 'summit-observatory';
    $classes[] = 'summit-observatory--' . $slug;
    if ( 'dark' === $tone ) {
        $classes[] = 'summit-shell--dark';  // header sits transparent over the dark hero
    }
    return $classes;
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
    $staggered = [ 'obs-pillar', 'obs-row', 'obs-reading', 'obs-step', 'obs-audience' ];
    $faded     = [ 'obs-register', 'obs-sectors', 'obs-principles', 'obs-list', 'obs-finding', 'obs-derived', 'obs-outcome' ];
    $count     = [];
    $p         = new WP_HTML_Tag_Processor( $html );
    while ( $p->next_tag() ) {
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
        foreach ( $faded as $class ) {
            if ( $p->has_class( $class ) ) {
                $p->set_attribute( 'data-reveal', 'fade' );
                continue 2;
            }
        }
        if ( 'H2' === $p->get_tag() || $p->has_class( 'obs-lead' ) || $p->has_class( 'obs-pull' ) || $p->has_class( 'obs-cta' ) ) {
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
            'title'      => $headline ?: get_the_title(),
            'standfirst' => $standfirst,
            'tone'       => $tone,
            'actions'    => $actions,
        ] ); ?>

        <?php
        $content = apply_filters( 'the_content', get_the_content() );
        if ( trim( $content ) ) {
            echo '<div class="obs-body">' . $summit_obs_reveal( $content ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- the_content output
        }

        if ( 'briefing' === $form && ! has_shortcode( get_the_content(), 'summit_briefing_form' ) ) {
            get_template_part( 'parts/observatory/briefing-form', null, [ 'source' => $path ] );
        }
        ?>

    </article>

    <?php endwhile; ?>

    <?php
    if ( $section && ! $is_overview ) {
        get_template_part( 'parts/observatory/sequence', null, [ 'section' => $section, 'path' => $path ] );
    }

    get_template_part( 'parts/global/cta-footer' );
    ?>
</main>

<?php
get_footer();
