<?php
/**
 * Part: Homepage Hero Block
 * Location: parts/blocks/hero.php
 *
 * Renders the homepage hero section with eyebrow, H1 headline,
 * body copy and up to two CTAs. All fields are optional with
 * launch-quality hardcoded defaults.
 *
 * ACF fields (from front page):
 *   home_hero_eyebrow    text     optional
 *   home_hero_headline   text     optional — default provided
 *   home_hero_body       textarea optional — default provided
 *   home_hero_cta_label  text     optional — default: Design Tomorrow
 *   home_hero_cta_url    url      optional — default: /design-tomorrow
 *   home_hero_cta2_label text     optional — default: Explore Our Work
 *   home_hero_cta2_url   url      optional — default: /work-showcase
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$eyebrow   = get_field( 'home_hero_eyebrow' );
$headline  = get_field( 'home_hero_headline' )   ?: 'Brand Strategy, Experience Design and Digital Transformation for Luxury Brands';
$body      = get_field( 'home_hero_body' )        ?: 'We partner with luxury and premium brands to sharpen positioning, elevate creative expression and build digital experiences of lasting value.';
$cta_label = get_field( 'home_hero_cta_label' )   ?: 'Design Tomorrow';
$cta_url   = get_field( 'home_hero_cta_url' )     ?: home_url( '/design-tomorrow' );
$cta2_label = get_field( 'home_hero_cta2_label' ) ?: 'Explore Our Work';
$cta2_url   = get_field( 'home_hero_cta2_url' )   ?: get_post_type_archive_link( 'case_study' );
?>

<section class="home-hero section--padded-xl" aria-labelledby="home-hero-title">
    <div class="container container--medium">
        <header class="home-hero__header">

            <?php if ( $eyebrow ) : ?>
            <p class="home-hero__eyebrow" data-reveal="fade"><?php echo esc_html( $eyebrow ); ?></p>
            <?php endif; ?>

            <h1 class="home-hero__title" id="home-hero-title" data-reveal>
                <?php echo esc_html( $headline ); ?>
            </h1>

            <div class="home-hero__body" data-reveal data-reveal-delay="1">
                <?php echo wp_kses_post( wpautop( $body ) ); ?>
            </div>

            <div class="home-hero__actions" data-reveal data-reveal-delay="2">
                <a href="<?php echo esc_url( $cta_url ); ?>" class="btn btn--primary">
                    <?php echo esc_html( $cta_label ); ?>
                </a>
                <a href="<?php echo esc_url( $cta2_url ); ?>" class="btn btn--secondary">
                    <?php echo esc_html( $cta2_label ); ?>
                </a>
            </div>

        </header>
    </div>
</section>
