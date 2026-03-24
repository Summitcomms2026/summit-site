<?php
/**
 * Template: Case Study Single
 * Location: single-case_study.php
 * URL: /work-showcase/[slug]
 *
 * Renders a single case study with narrative sections,
 * gallery, video, and related content.
 *
 * ACF fields: cs_* prefix (see acf-fields.php)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();
the_post();

$cs_id     = get_the_ID();
$summary   = get_field( 'cs_summary' );
$client    = get_field( 'cs_client' );
$year      = get_field( 'cs_year' );
$geographic = get_field( 'cs_geographic' );
$intro     = get_field( 'cs_intro' );
$challenge = get_field( 'cs_challenge' );
$idea      = get_field( 'cs_strategic_idea' );
$built     = get_field( 'cs_what_we_built' );
$outcome   = get_field( 'cs_outcome' );
$signal    = get_field( 'cs_outcome_signal' );
$gallery   = get_field( 'cs_gallery' );
$video     = get_field( 'cs_video_embed' );
$cta_label = get_field( 'cs_cta_label' );
$cta_url   = get_field( 'cs_cta_url' );

// Taxonomies.
$svc_terms    = get_the_terms( $cs_id, 'service_type' );
$sector_terms = get_the_terms( $cs_id, 'sector' );

// Related content.
$related_cases   = get_field( 'cs_related_cases' );
$related_article = get_field( 'cs_related_article' );
$related_episode = get_field( 'cs_related_episode' );
?>

<main id="main" class="site-main" role="main">

    <!-- ── Hero ─────────────────────────────────────────────────────── -->
    <section class="section--padded" aria-labelledby="single-hero-title">
        <div class="container container--medium">
            <div class="single-hero">

                <div class="single-case-study__meta">
                    <?php if ( ! is_wp_error( $svc_terms ) && ! empty( $svc_terms ) ) : ?>
                    <span class="single-case-study__meta-item"><?php echo esc_html( $svc_terms[0]->name ); ?></span>
                    <?php endif; ?>
                    <?php if ( ! is_wp_error( $sector_terms ) && ! empty( $sector_terms ) ) : ?>
                    <span class="single-case-study__meta-item"><?php echo esc_html( $sector_terms[0]->name ); ?></span>
                    <?php endif; ?>
                    <?php if ( $year ) : ?>
                    <span class="single-case-study__meta-item"><?php echo absint( $year ); ?></span>
                    <?php endif; ?>
                </div>

                <h1 class="single-hero__title" id="single-hero-title">
                    <?php the_title(); ?>
                </h1>

                <?php if ( $summary ) : ?>
                <p class="single-hero__standfirst"><?php echo esc_html( $summary ); ?></p>
                <?php endif; ?>

                <?php if ( $client ) : ?>
                <p class="meta-bar">
                    <span>Client: <?php echo esc_html( $client ); ?></span>
                    <?php if ( $geographic ) : ?>
                    <span class="meta-bar__separator"></span>
                    <span><?php echo esc_html( $geographic ); ?></span>
                    <?php endif; ?>
                </p>
                <?php endif; ?>

            </div>

            <?php if ( has_post_thumbnail() ) : ?>
            <figure class="single-hero__media">
                <?php the_post_thumbnail( 'full' ); ?>
            </figure>
            <?php endif; ?>
        </div>
    </section>

    <!-- ── Introduction ─────────────────────────────────────────────── -->
    <?php if ( $intro ) : ?>
    <section class="section--padded">
        <div class="container container--narrow">
            <div class="single-case-study__intro">
                <?php echo wp_kses_post( wpautop( $intro ) ); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── The Challenge ────────────────────────────────────────────── -->
    <?php if ( $challenge ) : ?>
    <section class="section--tinted section--padded">
        <div class="container container--narrow">
            <h2 class="single-case-study__section-title">The Challenge</h2>
            <div class="single-case-study__section-body">
                <?php echo wp_kses_post( $challenge ); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── The Strategic Idea ───────────────────────────────────────── -->
    <?php if ( $idea ) : ?>
    <section class="section--padded">
        <div class="container container--narrow">
            <h2 class="single-case-study__section-title">The Strategic Idea</h2>
            <div class="single-case-study__section-body">
                <?php echo wp_kses_post( $idea ); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── What We Built ────────────────────────────────────────────── -->
    <?php if ( $built ) : ?>
    <section class="section--tinted section--padded">
        <div class="container container--narrow">
            <h2 class="single-case-study__section-title">What We Built</h2>
            <div class="single-case-study__section-body">
                <?php echo wp_kses_post( $built ); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Gallery ──────────────────────────────────────────────────── -->
    <?php if ( ! empty( $gallery ) ) : ?>
    <section class="section--dark section--padded">
        <div class="container container--wide">
            <div class="single-case-study__gallery">
                <?php foreach ( $gallery as $image ) : ?>
                <figure class="single-case-study__gallery-item">
                    <img src="<?php echo esc_url( $image['url'] ); ?>"
                         alt="<?php echo esc_attr( $image['alt'] ?? '' ); ?>"
                         width="<?php echo esc_attr( $image['width'] ?? '' ); ?>"
                         height="<?php echo esc_attr( $image['height'] ?? '' ); ?>"
                         loading="lazy">
                </figure>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Video ────────────────────────────────────────────────────── -->
    <?php if ( $video ) : ?>
    <section class="section--dark section--padded">
        <div class="container container--wide">
            <div class="embed-wrap">
                <?php echo $video; // phpcs:ignore -- oEmbed output. ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── The Outcome ──────────────────────────────────────────────── -->
    <?php if ( $outcome ) : ?>
    <section class="section--padded">
        <div class="container container--narrow">
            <h2 class="single-case-study__section-title">The Outcome</h2>
            <div class="single-case-study__section-body">
                <?php echo wp_kses_post( $outcome ); ?>
            </div>
            <?php if ( $signal ) : ?>
            <p class="single-case-study__outcome-signal">
                <?php echo esc_html( $signal ); ?>
            </p>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Related Content ──────────────────────────────────────────── -->
    <?php
    get_template_part( 'parts/related/case-studies', null, [
        'posts'    => $related_cases ?: [],
        'headline' => 'Related Work',
    ] );

    get_template_part( 'parts/related/articles', null, [
        'posts'    => $related_article ?: [],
        'headline' => 'Related Reading',
    ] );

    get_template_part( 'parts/related/episodes', null, [
        'posts'    => $related_episode ?: [],
        'headline' => 'Related Episode',
    ] );
    ?>

    <!-- ── CTA Footer ───────────────────────────────────────────────── -->
    <?php
    get_template_part( 'parts/global/cta-footer', null, [
        'label' => $cta_label ?: null,
        'url'   => $cta_url ?: null,
    ] );
    ?>

</main>

<?php get_footer(); ?>
