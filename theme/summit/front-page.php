<?php
/**
 * Template: Homepage
 * Location: front-page.php
 *
 * WordPress template hierarchy: front-page.php takes precedence when a
 * static front page is set in Settings > Reading.
 *
 * Component sequence (matches approved design comp):
 *   1.  Hero              → parts/blocks/hero.php (full-bleed photo)
 *   2.  Value Proposition → inline (centered statement)
 *   3.  Capability Triptych → parts/blocks/capability-triptych.php
 *   4.  Selected Work     → parts/blocks/selected-work.php
 *   5.  Tastemakers       → parts/blocks/tastemakers-feature.php
 *   6.  Download          → parts/blocks/download-feature.php
 *   7.  Featured Article  → parts/blocks/featured-article.php (3-up strip)
 *   8.  Subscribe         → parts/global/subscribe.php
 *   9.  Sectors Grid      → inline (World of Luxury)
 *  10.  CTA Footer        → parts/global/cta-footer.php (with bg image)
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

    <!-- ── 2. Value Proposition ───────────────────────────────────────────── -->
    <?php
    $vp_headline = get_field( 'home_vp_headline' ) ?: 'Strategy, Creativity and Technology for the Luxury Sector';
    $vp_body     = get_field( 'home_vp_body' )
        ?: '<p>Summit Communication Group operates at the intersection of luxury, design and technology. We help ambitious brands sharpen their positioning, elevate their creative expression and build digital experiences that reflect who they truly are.</p>';
    ?>
    <section class="vp-block" aria-labelledby="vp-heading">
        <div class="container container--narrow">
            <h2 class="vp-block__headline" id="vp-heading" data-reveal>
                <?php echo esc_html( $vp_headline ); ?>
            </h2>
            <div class="vp-block__body" data-reveal data-reveal-delay="1">
                <?php echo wp_kses_post( $vp_body ); ?>
            </div>
        </div>
    </section>

    <!-- ── 3. Capability Triptych ─────────────────────────────────────────── -->
    <?php get_template_part( 'parts/blocks/capability-triptych' ); ?>

    <!-- ── 4. Selected Work ───────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/blocks/selected-work' ); ?>

    <!-- ── 5. Tastemakers ─────────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/blocks/tastemakers-feature' ); ?>

    <!-- ── 6. Download ────────────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/blocks/download-feature' ); ?>

    <!-- ── 7. Featured Article (3-up strip) ───────────────────────────────── -->
    <?php get_template_part( 'parts/blocks/featured-article' ); ?>

    <!-- ── 8. Subscribe ───────────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/global/subscribe' ); ?>

    <!-- ── 9. World of Luxury / Sectors Grid ──────────────────────────────── -->
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
    <?php
    get_template_part( 'parts/global/cta-footer', null, [
        'image' => get_template_directory_uri() . '/assets/img/cta-mainimage.jpg',
    ] );
    ?>

</main>

<?php get_footer(); ?>
