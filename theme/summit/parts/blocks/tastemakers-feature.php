<?php
/**
 * Part: Tastemakers Feature Panel
 * Location: parts/blocks/tastemakers-feature.php
 *
 * Features a single podcast season on the homepage. Dark panel.
 *
 * Curated first: uses home_tastemakers_season post_object field.
 * Auto fallback: queries latest podcast_season by ps_is_featured
 *   flag, then by ps_season_number DESC.
 * Guard: section is not rendered when no seasons exist.
 *
 * ACF fields (from front page):
 *   home_tastemakers_headline text        optional
 *   home_tastemakers_body     textarea    optional
 *   home_tastemakers_season   post_object optional — podcast_season
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

// Curated selection.
$season = get_field( 'home_tastemakers_season' );

// Auto fallback — find featured season, then latest.
if ( empty( $season ) ) {
    $featured_q = new WP_Query( [
        'post_type'      => 'podcast_season',
        'posts_per_page' => 1,
        'meta_key'       => 'ps_is_featured',
        'meta_value'     => '1',
        'no_found_rows'  => true,
    ] );
    if ( $featured_q->have_posts() ) {
        $season = $featured_q->posts[0];
    }
    wp_reset_postdata();
}

if ( empty( $season ) ) {
    $latest_q = new WP_Query( [
        'post_type'      => 'podcast_season',
        'posts_per_page' => 1,
        'meta_key'       => 'ps_season_number',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ] );
    if ( $latest_q->have_posts() ) {
        $season = $latest_q->posts[0];
    }
    wp_reset_postdata();
}

// Guard — do not render without a season.
if ( empty( $season ) ) {
    return;
}

$headline  = get_field( 'home_tastemakers_headline' ) ?: 'Tastemakers';
$body      = get_field( 'home_tastemakers_body' );
$season_id = $season->ID;
$subtitle  = get_field( 'ps_subtitle', $season_id );
$theme     = get_field( 'ps_theme', $season_id );
$artwork   = get_field( 'ps_artwork', $season_id );
?>

<section class="tastemakers-feature section--dark section--padded" aria-labelledby="tastemakers-heading">
    <div class="container container--wide">

        <div class="tastemakers-feature__layout">

            <?php if ( $artwork ) : ?>
            <figure class="tastemakers-feature__artwork" aria-hidden="true" data-reveal="fade">
                <img src="<?php echo esc_url( $artwork['url'] ); ?>"
                     alt="<?php echo esc_attr( $artwork['alt'] ?? '' ); ?>"
                     width="<?php echo esc_attr( $artwork['width'] ?? '' ); ?>"
                     height="<?php echo esc_attr( $artwork['height'] ?? '' ); ?>"
                     loading="lazy">
            </figure>
            <?php endif; ?>

            <div class="tastemakers-feature__content">

                <p class="tastemakers-feature__eyebrow" data-reveal="fade"><?php echo esc_html( $headline ); ?></p>

                <h2 class="tastemakers-feature__title" id="tastemakers-heading" data-reveal="fade">
                    <?php echo esc_html( get_the_title( $season_id ) ); ?>
                </h2>

                <?php if ( $subtitle ) : ?>
                <p class="tastemakers-feature__subtitle" data-reveal="fade" data-reveal-delay="1">
                    <?php echo esc_html( $subtitle ); ?>
                </p>
                <?php endif; ?>

                <?php if ( $theme ) : ?>
                <p class="tastemakers-feature__theme" data-reveal="fade" data-reveal-delay="1">
                    <?php echo esc_html( $theme ); ?>
                </p>
                <?php endif; ?>

                <?php if ( $body ) : ?>
                <div class="tastemakers-feature__body" data-reveal="fade" data-reveal-delay="2">
                    <?php echo wp_kses_post( wpautop( $body ) ); ?>
                </div>
                <?php endif; ?>

                <a href="<?php echo esc_url( get_permalink( $season_id ) ); ?>"
                   class="btn btn--primary"
                   data-reveal="fade" data-reveal-delay="3">
                    Listen to Tastemakers
                </a>

            </div>

        </div>

    </div>
</section>
