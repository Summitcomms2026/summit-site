<?php
/**
 * Template: Homepage
 * Location: front-page.php
 *
 * WordPress template hierarchy: front-page.php takes precedence when a
 * static front page is set in Settings > Reading.
 *
 * Component sequence:
 *   1.  Hero              → parts/blocks/hero.php
 *   2.  Showreel          → parts/blocks/showreel.php
 *   3.  Value Proposition → inline
 *   4.  Capability Triptych → parts/blocks/capability-triptych.php
 *   5.  Selected Work     → parts/blocks/selected-work.php
 *   6.  Tastemakers       → parts/blocks/tastemakers-feature.php
 *   7.  Download          → parts/blocks/download-feature.php
 *   8.  Featured Article  → parts/blocks/featured-article.php
 *   9.  Sectors Grid      → inline
 *  10.  CTA Footer        → parts/global/cta-footer.php (existing)
 *
 * Dark/light rhythm: L → D → L → T → L → D → T → L → D → D
 *
 * ACF field group: group_summit_homepage (home_* prefix)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

the_post(); // consume the front-page post for ACF context
?>

<main id="main" class="site-main site-main--home" role="main">

    <!-- ── 1. Hero ────────────────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/blocks/hero' ); ?>

    <!-- ── 2. Showreel ────────────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/blocks/showreel' ); ?>

    <!-- ── 3. Value Proposition ───────────────────────────────────────────── -->
    <?php
    $vp_headline = get_field( 'home_vp_headline' ) ?: 'Strategy, Creativity and Technology for the Luxury Sector';
    $vp_body     = get_field( 'home_vp_body' )
        ?: '<p>Summit Communication Group operates at the intersection of luxury, design and technology. We help ambitious brands sharpen their positioning, elevate their creative expression and build digital experiences that reflect who they truly are.</p>';
    ?>
    <section class="vp-block section--padded" aria-labelledby="vp-heading">
        <div class="container container--narrow">
            <h2 class="vp-block__headline" id="vp-heading" data-reveal>
                <?php echo esc_html( $vp_headline ); ?>
            </h2>
            <div class="vp-block__body" data-reveal data-reveal-delay="1">
                <?php echo wp_kses_post( $vp_body ); ?>
            </div>
        </div>
    </section>

    <!-- ── 4. Capability Triptych ─────────────────────────────────────────── -->
    <?php get_template_part( 'parts/blocks/capability-triptych' ); ?>

    <!-- ── 5. Selected Work ───────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/blocks/selected-work' ); ?>

    <!-- ── 6. Tastemakers ─────────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/blocks/tastemakers-feature' ); ?>

    <!-- ── 7. Download ────────────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/blocks/download-feature' ); ?>

    <!-- ── 8. Featured Article ────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/blocks/featured-article' ); ?>

    <!-- ── 9. Sectors Grid ────────────────────────────────────────────────── -->
    <?php
    $sectors_headline = get_field( 'home_sectors_headline' ) ?: 'The Luxury Economy';
    $sectors_body     = get_field( 'home_sectors_body' );

    $sectors = [
        'Fashion & Leather Goods',
        'Watches & Jewellery',
        'Hospitality & Travel',
        'Beauty & Fragrance',
        'Automotive & Aviation',
        'Art & Culture',
        'Wine & Spirits',
        'Property & Interiors',
        'Sport & Lifestyle',
        'Wealth & Finance',
        'Technology & Innovation',
        'Food & Gastronomy',
    ];
    ?>
    <section class="sectors-grid-section section--dark section--padded" aria-labelledby="sectors-heading">
        <div class="container container--wide">

            <header class="sectors-grid-section__header">
                <h2 class="sectors-grid-section__headline" id="sectors-heading" data-reveal="fade">
                    <?php echo esc_html( $sectors_headline ); ?>
                </h2>
                <?php if ( $sectors_body ) : ?>
                <div class="sectors-grid-section__body" data-reveal="fade" data-reveal-delay="1">
                    <?php echo wp_kses_post( $sectors_body ); ?>
                </div>
                <?php endif; ?>
            </header>

            <ul class="sectors-grid" role="list">
                <?php
                $sector_delays = [ 1, 2, 3, 4, 5, 6, 1, 2, 3, 4, 5, 6 ];
                foreach ( $sectors as $si => $sector ) :
                    $sd = $sector_delays[ $si ] ?? 1;
                ?>
                <li class="sectors-grid__item" data-reveal="fade" data-reveal-delay="<?php echo $sd; ?>">
                    <?php echo esc_html( $sector ); ?>
                </li>
                <?php endforeach; ?>
            </ul>

        </div>
    </section>

    <!-- ── 10. CTA Footer ─────────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php get_footer(); ?>
