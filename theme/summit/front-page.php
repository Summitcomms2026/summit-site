<?php
/**
 * Template: Homepage
 * Location: front-page.php
 *
 * WordPress template hierarchy: front-page.php takes precedence when a
 * static front page is set in Settings > Reading.
 *
 * The eight panels of the Editorial Master (Handover v1, S01-P001–P008),
 * set in the homepage's existing compositions:
 *
 *   1.  Build What Deserves to Last → parts/blocks/hero.php (ACF home_hero_*)
 *   2.  From Brand to Maison        → inline, the value-proposition field
 *   3.  What's the Brief?           → parts/blocks/capability-triptych.php
 *   4.  What Valuation Misses       → parts/blocks/observatory-feature.php
 *   5.  Ways to Begin               → parts/blocks/home-programmes.php
 *   6.  Research to Challenge       → parts/blocks/selected-work.php
 *   7.  Beyond the Headlines        → parts/blocks/featured-article.php
 *                                     (the live essays)
 *   8.  What's at Stake for You?    → parts/global/cta-footer.php
 *
 * Copy: the front page's summit_home field (summit_home_panel(), built from
 * the handover into site-content/home/manifest.json). The earlier client
 * cards, download, subscribe and sectors panels are not part of the Master
 * and are no longer shown (their parts remain in the theme).
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

    <!-- ── 2. From Brand to Maison (S01-P002) ─────────────────────────────── -->
    <?php $vp = summit_home_panel( 'S01-P002' ); ?>
    <section class="vp-block" aria-labelledby="home-vp-heading">
        <div class="container container--wide">
            <?php if ( ! empty( $vp['eyebrow'] ) ) : ?>
            <p class="home-eyebrow vp-block__eyebrow" data-reveal="fade"><?php echo esc_html( $vp['eyebrow'] ); ?></p>
            <?php endif; ?>
            <h2 class="vp-block__heading" id="home-vp-heading" data-reveal><?php echo esc_html( $vp['headline'] ?? '' ); ?></h2>
            <?php foreach ( $vp['paras'] ?? [] as $para ) : ?>
            <p class="vp-block__statement" data-reveal><?php echo esc_html( $para ); ?></p>
            <?php endforeach; ?>
            <?php foreach ( $vp['ctas'] ?? [] as $cta ) : ?>
            <p class="vp-block__link" data-reveal="fade"><a href="<?php echo esc_url( summit_meta_url( $cta['url'] ) ); ?>"><?php echo esc_html( $cta['label'] ); ?> <span aria-hidden="true">&rarr;</span></a></p>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- ── 3. What's the Brief? (S01-P003) ──────────────────────────────── -->
    <?php get_template_part( 'parts/blocks/capability-triptych' ); ?>

    <!-- ── 4. What Valuation Misses (S01-P004) ──────────────────────────── -->
    <?php get_template_part( 'parts/blocks/observatory-feature' ); ?>

    <!-- ── 5. Ways to Begin (S01-P005) ──────────────────────────────────── -->
    <?php get_template_part( 'parts/blocks/home-programmes' ); ?>

    <!-- ── 6. Research to Challenge (S01-P006) ──────────────────────────── -->
    <?php get_template_part( 'parts/blocks/selected-work' ); ?>

    <!-- ── 7. Beyond the Headlines (S01-P007) ───────────────────────────── -->
    <?php get_template_part( 'parts/blocks/featured-article' ); ?>

    <!-- ── 8. What's at Stake for You? (S01-P008) ───────────────────────── -->
    <?php
    $close = summit_home_panel( 'S01-P008' );
    $ctas  = $close['ctas'] ?? [];
    get_template_part( 'parts/global/cta-footer', null, array_filter( [
        'eyebrow'   => $close['eyebrow'] ?? '',
        'headline'  => $close['headline'] ?? 'What’s at Stake for You?',
        'body'      => $close['paras'][0] ?? '',
        'label'     => $ctas[0]['label'] ?? "Let\u{2019}s Talk",
        'url'       => isset( $ctas[0] ) ? summit_meta_url( $ctas[0]['url'] ) : null,
        'secondary' => isset( $ctas[1] ) ? [ 'label' => $ctas[1]['label'], 'url' => summit_meta_url( $ctas[1]['url'] ) ] : null,
        'image'     => get_template_directory_uri() . '/assets/img/cta-mainimage.jpg',
    ] ) );
    ?>

</main>

<?php get_footer(); ?>
