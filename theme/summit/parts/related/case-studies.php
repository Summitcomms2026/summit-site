<?php
/**
 * Part: Related Case Studies
 * Location: parts/related/case-studies.php
 *
 * Renders a related case study grid from a supplied array of WP_Post objects.
 * Guard: suppresses output if no posts provided.
 *
 * Usage:
 *   get_template_part( 'parts/related/case-studies', null, [
 *       'posts'    => $related_cases,
 *       'headline' => 'Related Work',
 *   ] );
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$posts    = $args['posts']    ?? [];
$headline = $args['headline'] ?? 'Related Work';

if ( empty( $posts ) ) {
    return;
}
?>

<section class="related-section section--tinted section--padded" aria-labelledby="related-cases-heading">
    <div class="container container--wide">

        <h2 class="related-section__headline" id="related-cases-heading">
            <?php echo esc_html( $headline ); ?>
        </h2>

        <ul class="related-grid" role="list">
            <?php foreach ( $posts as $case ) :
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

    </div>
</section>
