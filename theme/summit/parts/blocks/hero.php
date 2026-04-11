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
$headline  = get_field( 'home_hero_headline' )   ?: 'Designing the Future of Luxury';
$body      = get_field( 'home_hero_body' )        ?: 'Summit Communication Group is a design consultancy specialising in brand strategy, experience design and digital transformation for luxury brands.';
$cta_label = get_field( 'home_hero_cta_label' )   ?: 'What We Do';
$cta_url   = get_field( 'home_hero_cta_url' )     ?: home_url( '/what-we-do' );
$cta2_label = get_field( 'home_hero_cta2_label' ) ?: "Let\u{2019}s Talk";
$cta2_url   = get_field( 'home_hero_cta2_url' )   ?: home_url( '/design-tomorrow' );

$hero_image = get_field( 'home_hero_image' );
$hero_url   = $hero_image ? $hero_image['url'] : get_template_directory_uri() . '/assets/img/top-herophoto.jpg';
?>

<section class="home-hero" aria-labelledby="home-hero-title"
         style="--hero-bg: url('<?php echo esc_url( $hero_url ); ?>');">
    <div class="container container--wide">
        <div class="home-hero__layout">

            <div class="home-hero__left">
                <h1 class="home-hero__title" id="home-hero-title" data-reveal>
                    <?php
                    echo wp_kses(
                        str_replace( 'Luxury', '<em>Luxury</em>', esc_html( $headline ) ),
                        [ 'em' => [] ]
                    );
                    ?>
                </h1>
            </div>

            <div class="home-hero__right" data-reveal data-reveal-delay="1">
                <div class="home-hero__body">
                    <?php echo wp_kses_post( wpautop( $body ) ); ?>
                </div>
                <div class="home-hero__actions">
                    <a href="<?php echo esc_url( $cta_url ); ?>" class="btn btn--secondary">
                        <?php echo esc_html( $cta_label ); ?>
                    </a>
                    <a href="<?php echo esc_url( $cta2_url ); ?>" class="btn btn--primary">
                        <?php echo esc_html( $cta2_label ); ?>
                    </a>
                </div>
            </div>

        </div>
    </div>
    <button type="button" class="home-hero__scroll" data-reveal="fade" data-reveal-delay="3"
            onclick="this.closest('section').nextElementSibling.scrollIntoView({behavior:'smooth'})">
        <svg viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
            <path d="M5 1v7M1.5 5.5 5 9l3.5-3.5"/>
        </svg>
        Scroll
    </button>
</section>
