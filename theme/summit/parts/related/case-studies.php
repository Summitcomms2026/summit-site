<?php
/**
 * Part: Related Case Studies
 * Location: parts/related/case-studies.php
 *
 * Renders up to 3 related case study cards.
 * Reads ACF relationship field cs_related_cases from the current post.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$related = get_field( 'cs_related_cases' );

if ( empty( $related ) || ! is_array( $related ) ) {
    return;
}
?>

<section class="related-work" aria-labelledby="related-work-heading">
    <div class="container container--wide">

        <header class="related-work__header">
            <h2 class="related-work__heading" id="related-work-heading">Related Work</h2>
        </header>

        <ul class="case-study-grid case-study-grid--related" role="list">
            <?php foreach ( $related as $related_post ) :
                if ( ! $related_post instanceof WP_Post ) {
                    continue;
                }
                $summary    = get_field( 'cs_summary', $related_post->ID );
                $thumb      = get_the_post_thumbnail( $related_post->ID, 'large' );
                $svc_terms  = get_the_terms( $related_post->ID, 'service_type' );
                $svc_label  = ( ! is_wp_error( $svc_terms ) && ! empty( $svc_terms ) )
                              ? $svc_terms[0]->name : '';
            ?>
            <li class="case-study-card">
                <a href="<?php echo esc_url( get_permalink( $related_post->ID ) ); ?>"
                   class="case-study-card__link"
                   aria-label="<?php echo esc_attr( 'View case study: ' . get_the_title( $related_post->ID ) ); ?>">

                    <?php if ( $thumb ) : ?>
                    <figure class="case-study-card__media" aria-hidden="true">
                        <?php echo $thumb; ?>
                    </figure>
                    <?php endif; ?>

                    <div class="case-study-card__body">
                        <?php if ( $svc_label ) : ?>
                        <p class="case-study-card__service"><?php echo esc_html( $svc_label ); ?></p>
                        <?php endif; ?>
                        <h3 class="case-study-card__title">
                            <?php echo esc_html( get_the_title( $related_post->ID ) ); ?>
                        </h3>
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
