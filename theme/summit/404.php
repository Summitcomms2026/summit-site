<?php
/**
 * 404 Template
 *
 * The internal page shell's hero on a dark field, then the way back into
 * the site: the primary sections and Let's Talk, from the navigation
 * config, so the recovery paths always match the architecture.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', static function () {
    wp_enqueue_style( 'summit-page-shell', get_template_directory_uri() . '/assets/css/page-shell.css', [ 'summit-navigation' ], wp_get_theme()->get( 'Version' ) );
} );

add_filter( 'body_class', static function ( array $classes ): array {
    $classes[] = 'summit-shell';
    $classes[] = 'summit-shell--dark';
    return $classes;
} );

$nav_cta  = summit_nav( 'cta' );
$sections = summit_nav( 'primary' ) ?: [];

get_header();
?>

<main id="main" class="site-main site-main--shell" role="main">

    <?php get_template_part( 'parts/global/page-hero', null, [
        'eyebrow'    => [ __( 'Not found', 'summit' ) ],
        'title'      => __( 'This page is not here.', 'summit' ),
        'standfirst' => __( 'It may have moved as the site has grown. The way back in is below.', 'summit' ),
        'tone'       => 'dark',
        'actions'    => [
            [ 'type' => 'cta', 'label' => __( 'Return home', 'summit' ), 'url' => home_url( '/' ) ],
            [ 'type' => 'utility', 'mode' => 'link', 'icon' => false, 'label' => $nav_cta['label'] ?? '', 'url' => summit_nav_url( $nav_cta ), 'class' => 'action--contextual' ],
        ],
    ] ); ?>

    <nav class="shell-index" aria-label="<?php esc_attr_e( 'The site', 'summit' ); ?>">
        <div class="container container--medium">
            <p class="shell-index__heading"><span><?php esc_html_e( 'The site', 'summit' ); ?></span></p>
            <div class="shell-index__groups">
                <div class="shell-index__group">
                    <ul class="shell-index__list" role="list">
                        <?php foreach ( $sections as $section ) : ?>
                        <li>
                            <a class="shell-index__link" href="<?php echo esc_url( summit_nav_url( $section ) ); ?>">
                                <span class="shell-index__label"><?php echo esc_html( $section['label'] ); ?></span>
                                <span class="shell-index__arrow" aria-hidden="true">&rarr;</span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php
get_footer();
