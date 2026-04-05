<?php
/**
 * Part: Selected Work Panel
 * Location: parts/blocks/selected-work.php
 *
 * Displays curated or auto-queried case studies on the homepage.
 * Dark section with project cards showing service type, title,
 * and summary text.
 *
 * Curated first: uses home_work_cases relationship field.
 * Auto fallback: queries 4 most recent case_study posts.
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

// Auto fallback — 4 most recent case studies.
if ( empty( $cases ) ) {
    $fallback_query = new WP_Query( [
        'post_type'      => 'case_study',
        'posts_per_page' => 4,
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

<section class="selected-work section--dark" aria-labelledby="selected-work-heading">
    <div class="container container--wide">

        <header class="selected-work__header">
            <h2 class="selected-work__headline" id="selected-work-heading" data-reveal>
                <?php echo esc_html( $headline ); ?>
            </h2>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'case_study' ) ); ?>"
               class="btn btn--primary" data-reveal data-reveal-delay="1">
                View Projects
            </a>
        </header>

        <ul class="case-study-grid" role="list">
            <?php
            $card_delays = [ 1, 2, 3, 4 ];
            foreach ( $cases as $card_i => $case ) :
                $cs_id      = $case->ID;
                $card_delay = $card_delays[ $card_i ] ?? 4;
                $summary    = get_field( 'cs_summary', $cs_id );
                $svc_terms  = get_the_terms( $cs_id, 'service_type' );
                $svc_label  = ( ! is_wp_error( $svc_terms ) && ! empty( $svc_terms ) )
                              ? $svc_terms[0]->name : '';
            ?>
            <li class="case-study-card" data-reveal data-reveal-delay="<?php echo $card_delay; ?>">
                <a href="<?php echo esc_url( get_permalink( $cs_id ) ); ?>"
                   class="case-study-card__link"
                   aria-label="<?php echo esc_attr( 'View case study: ' . get_the_title( $cs_id ) ); ?>">

                    <?php if ( has_post_thumbnail( $cs_id ) ) : ?>
                    <figure class="case-study-card__media" aria-hidden="true">
                        <?php echo get_the_post_thumbnail( $cs_id, 'large' ); ?>
                    </figure>
                    <?php endif; ?>

                    <div class="case-study-card__body">

                        <?php if ( $svc_label ) : ?>
                        <span class="case-study-card__service"><?php echo esc_html( $svc_label ); ?></span>
                        <?php endif; ?>

                        <h3 class="case-study-card__title"><?php echo esc_html( get_the_title( $cs_id ) ); ?></h3>

                        <?php if ( $summary ) : ?>
                        <p class="case-study-card__summary"><?php echo esc_html( $summary ); ?></p>
                        <?php endif; ?>

                    </div>

                </a>
            </li>
            <?php endforeach; ?>
        </ul>

    </div>
</section>
