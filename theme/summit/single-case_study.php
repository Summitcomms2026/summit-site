<?php
/**
 * Template: Single Case Study
 * Location: single-case_study.php
 *
 * WordPress template hierarchy: single-case_study.php → single.php → index.php
 *
 * Component sequence (summit_page_specs.md §8):
 *   1.  Case study hero — title, intro, featured image
 *   2.  Project metadata strip — client, services, sector, year
 *   3.  Challenge
 *   4.  Strategic Idea
 *   5.  What We Built
 *   6.  Outcome (+ outcome signal)
 *   7.  Project gallery / video
 *   8.  Related case studies  → parts/related/case-studies.php
 *   9.  Related articles      → parts/related/articles.php
 *   10. CTA footer            → parts/global/cta-footer.php
 *
 * SEO & Schema — not injected here.
 * Output for this post type is handled in inc/seo.php:
 *   - summit_seo_meta()   hooked on wp_head (title, description, OG, canonical, noindex)
 *   - summit_schema_cs()  hooked on wp_head (schema.org/CreativeWork)
 * ACF fields consumed by those functions:
 *   cs_seo_title, cs_meta_desc, cs_og_image, cs_canonical, cs_noindex
 * Schema fields: get_the_title(), cs_client, cs_year, cs_og_image, get_permalink()
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();

    // ── Field retrieval ───────────────────────────────────────────────────
    $summary        = get_field( 'cs_summary' );
    $client         = get_field( 'cs_client' );
    $year           = get_field( 'cs_year' );
    $geographic     = get_field( 'cs_geographic' );
    $intro          = get_field( 'cs_intro' );
    $challenge      = get_field( 'cs_challenge' );
    $strategic_idea = get_field( 'cs_strategic_idea' );
    $what_we_built  = get_field( 'cs_what_we_built' );
    $outcome        = get_field( 'cs_outcome' );
    $outcome_signal = get_field( 'cs_outcome_signal' );
    $gallery        = get_field( 'cs_gallery' );
    $video_embed    = get_field( 'cs_video_embed' );
    $related_cases  = get_field( 'cs_related_cases' );   // relationship → array
    $related_art    = get_field( 'cs_related_article' ); // relationship → array
    $cta_label      = get_field( 'cs_cta_label' ) ?: 'Start a Conversation';
    $cta_url        = get_field( 'cs_cta_url' )   ?: home_url( '/design-tomorrow' );

    // Taxonomy terms for the metadata strip
    $sectors       = get_the_terms( get_the_ID(), 'sector' );
    $service_types = get_the_terms( get_the_ID(), 'service_type' );
?>

<main id="main" class="site-main site-main--case-study" role="main">

    <!-- ── 1. Hero ──────────────────────────────────────────────────────── -->
    <section class="cs-hero" aria-labelledby="cs-hero-title">
        <div class="container container--wide">

            <nav class="cs-hero__breadcrumb" aria-label="Case study navigation">
                <a href="<?php echo esc_url( home_url( '/work' ) ); ?>">Work</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page"><?php the_title(); ?></span>
            </nav>

            <header class="cs-hero__header">
                <h1 class="cs-hero__title" id="cs-hero-title">
                    <?php the_title(); ?>
                </h1>
                <?php if ( $intro ) : ?>
                <p class="cs-hero__intro"><?php echo esc_html( $intro ); ?></p>
                <?php endif; ?>
            </header>

            <?php if ( has_post_thumbnail() ) : ?>
            <figure class="cs-hero__media">
                <?php the_post_thumbnail( 'full', [
                    'loading' => 'eager',
                    'alt'     => esc_attr( get_the_title() ),
                ] ); ?>
            </figure>
            <?php endif; ?>

        </div>
    </section>

    <!-- ── 2. Metadata strip ────────────────────────────────────────────── -->
    <section class="cs-meta-strip" aria-label="Project details">
        <div class="container container--medium">

            <dl class="cs-meta-strip__list">

                <?php if ( $client ) : ?>
                <div class="cs-meta-strip__item">
                    <dt class="cs-meta-strip__label">Client</dt>
                    <dd class="cs-meta-strip__value"><?php echo esc_html( $client ); ?></dd>
                </div>
                <?php endif; ?>

                <?php if ( ! is_wp_error( $service_types ) && ! empty( $service_types ) ) : ?>
                <div class="cs-meta-strip__item">
                    <dt class="cs-meta-strip__label">Services</dt>
                    <dd class="cs-meta-strip__value">
                        <?php echo esc_html( implode( ', ', wp_list_pluck( $service_types, 'name' ) ) ); ?>
                    </dd>
                </div>
                <?php endif; ?>

                <?php if ( ! is_wp_error( $sectors ) && ! empty( $sectors ) ) : ?>
                <div class="cs-meta-strip__item">
                    <dt class="cs-meta-strip__label">Sector</dt>
                    <dd class="cs-meta-strip__value">
                        <?php echo esc_html( implode( ', ', wp_list_pluck( $sectors, 'name' ) ) ); ?>
                    </dd>
                </div>
                <?php endif; ?>

                <?php if ( $year ) : ?>
                <div class="cs-meta-strip__item">
                    <dt class="cs-meta-strip__label">Year</dt>
                    <dd class="cs-meta-strip__value"><?php echo absint( $year ); ?></dd>
                </div>
                <?php endif; ?>

                <?php if ( $geographic ) : ?>
                <div class="cs-meta-strip__item">
                    <dt class="cs-meta-strip__label">Location</dt>
                    <dd class="cs-meta-strip__value"><?php echo esc_html( $geographic ); ?></dd>
                </div>
                <?php endif; ?>

            </dl>

            <?php if ( $summary ) : ?>
            <p class="cs-meta-strip__summary"><?php echo esc_html( $summary ); ?></p>
            <?php endif; ?>

        </div>
    </section>

    <!-- ── 3–6. Narrative sections ──────────────────────────────────────── -->
    <div class="cs-narrative">

        <?php if ( $challenge ) : ?>
        <section class="cs-narrative__section cs-narrative__section--challenge"
                 aria-labelledby="cs-challenge-heading">
            <div class="container container--narrow">
                <h2 class="cs-narrative__label" id="cs-challenge-heading">Challenge</h2>
                <div class="cs-narrative__body wysiwyg">
                    <?php echo wp_kses_post( $challenge ); ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <?php if ( $strategic_idea ) : ?>
        <section class="cs-narrative__section cs-narrative__section--idea"
                 aria-labelledby="cs-idea-heading">
            <div class="container container--narrow">
                <h2 class="cs-narrative__label" id="cs-idea-heading">Strategic Idea</h2>
                <div class="cs-narrative__body wysiwyg">
                    <?php echo wp_kses_post( $strategic_idea ); ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <?php if ( $what_we_built ) : ?>
        <section class="cs-narrative__section cs-narrative__section--built"
                 aria-labelledby="cs-built-heading">
            <div class="container container--narrow">
                <h2 class="cs-narrative__label" id="cs-built-heading">What We Built</h2>
                <div class="cs-narrative__body wysiwyg">
                    <?php echo wp_kses_post( $what_we_built ); ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <?php if ( $outcome ) : ?>
        <section class="cs-narrative__section cs-narrative__section--outcome"
                 aria-labelledby="cs-outcome-heading">
            <div class="container container--narrow">
                <h2 class="cs-narrative__label" id="cs-outcome-heading">Outcome</h2>
                <div class="cs-narrative__body wysiwyg">
                    <?php echo wp_kses_post( $outcome ); ?>
                </div>
                <?php if ( $outcome_signal ) : ?>
                <blockquote class="cs-narrative__signal">
                    <p><?php echo esc_html( $outcome_signal ); ?></p>
                </blockquote>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>

    </div>

    <!-- ── 7. Video / Gallery ────────────────────────────────────────────── -->
    <?php if ( $video_embed ) : ?>
    <section class="cs-video" aria-label="Project film">
        <div class="container container--wide">
            <div class="cs-video__player">
                <?php
                if ( filter_var( $video_embed, FILTER_VALIDATE_URL ) ) {
                    printf(
                        '<div class="cs-video__embed-wrap"><iframe src="%s" allow="autoplay; fullscreen" allowfullscreen loading="lazy" title="%s — Project Film"></iframe></div>',
                        esc_url( $video_embed ),
                        esc_attr( get_the_title() )
                    );
                } else {
                    echo wp_kses_post( $video_embed );
                }
                ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ( ! empty( $gallery ) && is_array( $gallery ) ) : ?>
    <section class="cs-gallery" aria-label="Project gallery">
        <div class="container container--wide">
            <ul class="cs-gallery__grid" role="list">
                <?php foreach ( $gallery as $image ) :
                    if ( empty( $image['url'] ) ) { continue; }
                ?>
                <li class="cs-gallery__item">
                    <figure>
                        <img src="<?php echo esc_url( $image['url'] ); ?>"
                             alt="<?php echo esc_attr( $image['alt'] ?: get_the_title() . ' — project image' ); ?>"
                             width="<?php echo absint( $image['width']  ?? 1200 ); ?>"
                             height="<?php echo absint( $image['height'] ?? 800 ); ?>"
                             loading="lazy">
                        <?php if ( ! empty( $image['caption'] ) ) : ?>
                        <figcaption class="cs-gallery__caption">
                            <?php echo esc_html( $image['caption'] ); ?>
                        </figcaption>
                        <?php endif; ?>
                    </figure>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── 8. Related case studies ──────────────────────────────────────── -->
    <?php if ( ! empty( $related_cases ) ) :
        get_template_part( 'parts/related/case-studies' );
    endif; ?>

    <!-- ── 9. Related articles ──────────────────────────────────────────── -->
    <?php if ( ! empty( $related_art ) ) :
        set_query_var( 'summit_related_articles', $related_art );
        set_query_var( 'summit_related_heading',  'Related Thinking' );
        get_template_part( 'parts/related/articles' );
    endif; ?>

    <!-- ── 10. CTA footer ───────────────────────────────────────────────── -->
    <?php
    set_query_var( 'summit_cta_label', $cta_label );
    set_query_var( 'summit_cta_url',   $cta_url );
    get_template_part( 'parts/global/cta-footer' );
    ?>

</main>

<?php
endwhile;
get_footer();
