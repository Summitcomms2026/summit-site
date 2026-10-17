<?php
/**
 * Template Name: Contact
 * Location: page-contact.php
 *
 * Let's Talk (/lets-talk) and Media Enquiries (/media-enquiries): speaking
 * to Summit rather than submitting a lead form. The shell hero, then the
 * page's own content — a single question form beside quieter routes and
 * direct contact — then the global footer.
 *
 *   hero      the shell hero — eyebrow · H1 · standfirst (no CTA: this is
 *             where the CTA leads)
 *   body      the page's content as core blocks, styled by
 *             assets/css/contact.css. Shortcodes:
 *             [summit_contact_form]    the form (parts/contact/form.php)
 *             [summit_contact_direct]  established direct contact
 *
 * The form submits through Summit Core's enquiry handler, with JavaScript
 * in place (assets/js/enquiry-form.js) and without it by redirect.
 *
 * Page fields (post meta):
 *   summit_eyebrow, summit_headline, summit_tone, summit_standfirst
 *   summit_form         general (default) | media — the enquiry type
 *
 * Canonical source: site-content/contact/ (see its manifest.json).
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$page_id = get_queried_object_id();
$path    = '/' . trim( (string) get_page_uri( $page_id ), '/' );
$meta    = static fn( string $key ): string => (string) get_post_meta( $page_id, $key, true );
$tone    = $meta( 'summit_tone' ) ?: 'paper';
$eyebrow = array_filter( array_map( 'trim', explode( '/', $meta( 'summit_eyebrow' ) ) ) );
$form    = 'media' === $meta( 'summit_form' ) ? 'media' : 'general';

add_action( 'wp_enqueue_scripts', static function () {
    $v = wp_get_theme()->get( 'Version' );
    wp_enqueue_style( 'summit-page-shell', get_template_directory_uri() . '/assets/css/page-shell.css', [ 'summit-navigation' ], $v );
    wp_enqueue_style( 'summit-contact', get_template_directory_uri() . '/assets/css/contact.css', [ 'summit-page-shell' ], $v );
    wp_enqueue_script( 'summit-enquiry-form', get_template_directory_uri() . '/assets/js/enquiry-form.js', [], $v, true );
    wp_localize_script( 'summit-enquiry-form', 'summitEnquiry', [ 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ] );
} );

add_filter( 'body_class', static function ( array $classes ) use ( $tone, $form ): array {
    $classes[] = 'summit-shell';
    $classes[] = 'summit-contact';
    $classes[] = 'summit-contact--' . $form;
    if ( 'dark' === $tone ) {
        $classes[] = 'summit-shell--dark';
    }
    return $classes;
} );

// Topic slug → page name, so an enquiry can say where it came from
$lets_talk = get_page_by_path( 'lets-talk', OBJECT, 'page' );
$topics    = $lets_talk ? (array) json_decode( (string) get_post_meta( $lets_talk->ID, 'summit_topics', true ), true ) : [];

add_shortcode( 'summit_contact_form', static function ( $atts ) use ( $form, $path, $topics ): string {
    $atts = shortcode_atts( [ 'heading' => '' ], $atts );
    ob_start();
    get_template_part( 'parts/contact/form', null, [ 'type' => $form, 'source' => $path, 'heading' => $atts['heading'], 'topics' => $topics ] );
    return (string) ob_get_clean();
} );

add_shortcode( 'summit_contact_direct', static function (): string {
    ob_start();
    get_template_part( 'parts/contact/direct' );
    return (string) ob_get_clean();
} );

get_header();
?>

<main class="site-main site-main--shell">
    <?php while ( have_posts() ) : the_post(); ?>

    <article <?php post_class( 'shell ct' ); ?>>

        <?php get_template_part( 'parts/global/page-hero', null, [
            'eyebrow'    => $eyebrow,
            'title'      => $meta( 'summit_headline' ) ?: get_the_title(),
            'standfirst' => $meta( 'summit_standfirst' ),
            'tone'       => $tone,
            'actions'    => summit_meta_actions( $page_id ),
        ] ); ?>

        <div class="ct-body">
            <?php the_content(); ?>
        </div>

    </article>

    <?php endwhile; ?>
</main>

<?php
get_footer();
