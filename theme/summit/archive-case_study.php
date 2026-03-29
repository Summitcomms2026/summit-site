<?php
/**
 * Template: Case Study Archive
 * Location: archive-case_study.php
 * URL: /work-showcase
 *
 * Displays the Work Showcase archive grid.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main" class="site-main" role="main">

    <!-- ── Archive Hero ─────────────────────────────────────────────── -->
    <section class="section--padded" aria-labelledby="archive-hero-heading">
        <div class="container container--medium">
            <div class="archive-hero">
                <p class="archive-hero__eyebrow" data-reveal="fade">Work Showcase</p>
                <h1 class="archive-hero__headline" id="archive-hero-heading" data-reveal>
                    Selected Work
                </h1>
                <div class="archive-hero__body">
                    <p data-reveal data-reveal-delay="1">A selection of projects across brand strategy, experience design and digital transformation for luxury and premium brands.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Strategic Framing ──────────────────────────────────────── -->
    <section class="section--padded archive-framing">
        <div class="container container--narrow">
            <div class="archive-framing__body">
                <p>What follows is a selective account of the work — chosen not to impress with volume, but to show how strategy, design and craft converge in practice.</p>
            </div>
        </div>
    </section>

    <!-- ── Showreel (conditional) ─────────────────────────────────── -->
    <?php
    $front_page_id  = (int) get_option( 'page_on_front' );
    $showreel_embed = $front_page_id ? get_field( 'home_showreel_embed', $front_page_id ) : '';

    if ( $showreel_embed ) : ?>
    <section class="showreel-block section--dark section--padded" aria-labelledby="archive-showreel-heading">
        <div class="container container--wide">
            <header class="showreel-block__header">
                <h2 class="showreel-block__headline" id="archive-showreel-heading">
                    See the Work
                </h2>
            </header>
            <div class="showreel-block__embed">
                <?php echo $showreel_embed; // ACF oEmbed markup. ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Case Study Grid ──────────────────────────────────────────── -->
    <section class="section--padded">
        <div class="container container--wide">

            <?php if ( have_posts() ) : ?>

            <ul class="case-study-grid" role="list">
                <?php while ( have_posts() ) : the_post();
                    $cs_id     = get_the_ID();
                    $summary   = get_field( 'cs_summary' );
                    $client    = get_field( 'cs_client' );
                    $year      = get_field( 'cs_year' );
                    $svc_terms = get_the_terms( $cs_id, 'service_type' );
                    $svc_label = ( ! is_wp_error( $svc_terms ) && ! empty( $svc_terms ) )
                                 ? $svc_terms[0]->name : '';
                ?>
                <li class="case-study-card">
                    <a href="<?php the_permalink(); ?>"
                       class="case-study-card__link"
                       aria-label="<?php echo esc_attr( 'View case study: ' . get_the_title() ); ?>">

                        <?php if ( has_post_thumbnail() ) : ?>
                        <figure class="case-study-card__media" aria-hidden="true">
                            <?php the_post_thumbnail( 'large' ); ?>
                        </figure>
                        <?php endif; ?>

                        <div class="case-study-card__body">
                            <div class="case-study-card__meta">
                                <?php if ( $svc_label ) : ?>
                                <span class="case-study-card__service"><?php echo esc_html( $svc_label ); ?></span>
                                <?php endif; ?>
                                <?php if ( $year ) : ?>
                                <span class="case-study-card__year"><?php echo absint( $year ); ?></span>
                                <?php endif; ?>
                            </div>

                            <h2 class="case-study-card__title"><?php the_title(); ?></h2>

                            <?php if ( $client ) : ?>
                            <p class="case-study-card__client"><?php echo esc_html( $client ); ?></p>
                            <?php endif; ?>

                            <?php if ( $summary ) : ?>
                            <p class="case-study-card__summary"><?php echo esc_html( $summary ); ?></p>
                            <?php endif; ?>

                            <span class="case-study-card__cta" aria-hidden="true">View Project</span>
                        </div>
                    </a>
                </li>
                <?php endwhile; ?>
            </ul>

            <div class="pagination">
                <?php the_posts_pagination( [
                    'mid_size'  => 2,
                    'prev_text' => '&larr;',
                    'next_text' => '&rarr;',
                ] ); ?>
            </div>

            <?php else : ?>

            <div class="empty-state">
                <p class="empty-state__message">Case studies will appear here as projects are published.</p>
            </div>

            <?php endif; ?>

        </div>
    </section>

    <!-- ── CTA Footer ───────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php get_footer(); ?>
