<?php
/**
 * Part: Selected Work Panel
 * Location: parts/blocks/selected-work.php
 *
 * Displays curated or auto-queried case studies on the homepage.
 * Reuses the case-study-card markup from page-work.php.
 *
 * Curated first: uses home_work_cases relationship field.
 * Auto fallback: queries 3 most recent case_study posts.
 * Guard: section is not rendered when no case studies exist.
 *
 * ACF fields (from front page):
 *   home_work_headline text         optional
 *   home_work_cases    relationship  optional — case_study, max 4
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$headline = get_field( 'home_work_headline' ) ?: 'Selected Work';

// Curated selection — ACF relationship field returns array of WP_Post objects.
$cases = get_field( 'home_work_cases' );

// Auto fallback — 3 most recent case studies.
if ( empty( $cases ) ) {
    $fallback_query = new WP_Query( [
        'post_type'      => 'case_study',
        'posts_per_page' => 3,
        'orderby'        => [ 'menu_order' => 'ASC', 'date' => 'DESC' ],
        'no_found_rows'  => true,
    ] );
    $cases = $fallback_query->posts;
    wp_reset_postdata();
}

// Guard — do not render when no case studies exist.
if ( empty( $cases ) ) {
    return;
}
?>

<section class="selected-work section--padded" aria-labelledby="selected-work-heading">
    <div class="container container--wide">

        <header class="selected-work__header">
            <h2 class="selected-work__headline" id="selected-work-heading">
                <?php echo esc_html( $headline ); ?>
            </h2>
        </header>

        <ul class="case-study-grid" role="list">
            <?php foreach ( $cases as $case ) :
                $cs_id     = $case->ID;
                $summary   = get_field( 'cs_summary', $cs_id );
                $client    = get_field( 'cs_client', $cs_id );
                $year      = get_field( 'cs_year', $cs_id );
                $svc_terms = get_the_terms( $cs_id, 'service_type' );
                $svc_label = ( ! is_wp_error( $svc_terms ) && ! empty( $svc_terms ) )
                             ? $svc_terms[0]->name : '';
            ?>
            <li class="case-study-card">
                <a href="<?php echo esc_url( get_permalink( $cs_id ) ); ?>"
                   class="case-study-card__link"
                   aria-label="<?php echo esc_attr( 'View case study: ' . get_the_title( $cs_id ) ); ?>">

                    <?php if ( has_post_thumbnail( $cs_id ) ) : ?>
                    <figure class="case-study-card__media" aria-hidden="true">
                        <?php echo get_the_post_thumbnail( $cs_id, 'large' ); ?>
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

                        <h3 class="case-study-card__title"><?php echo esc_html( get_the_title( $cs_id ) ); ?></h3>

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
            <?php endforeach; ?>
        </ul>

        <footer class="selected-work__footer">
            <a href="<?php echo esc_url( get_post_type_archive_link( 'case_study' ) ); ?>" class="btn btn--secondary">
                Explore Our Work
            </a>
        </footer>

    </div>
</section>
