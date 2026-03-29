<?php
/**
 * Part: Download Feature Panel
 * Location: parts/blocks/download-feature.php
 *
 * Features a single download asset on the homepage. Tinted panel.
 * Split layout: cover image alongside title, description and CTA.
 *
 * Curated first: uses home_download_item post_object field.
 * Auto fallback: queries latest download post.
 * Guard: section is not rendered when no downloads exist.
 *
 * ACF fields (from front page):
 *   home_download_headline text        optional
 *   home_download_body     textarea    optional
 *   home_download_item     post_object optional — download
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

// Curated selection.
$download = get_field( 'home_download_item' );

// Auto fallback — latest download.
if ( empty( $download ) ) {
    $dl_query = new WP_Query( [
        'post_type'      => 'download',
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ] );
    if ( $dl_query->have_posts() ) {
        $download = $dl_query->posts[0];
    }
    wp_reset_postdata();
}

// Guard — do not render without a download.
if ( empty( $download ) ) {
    return;
}

$dl_id           = $download->ID;
$section_headline = get_field( 'home_download_headline' ) ?: 'The Characteristics of Luxury';
$section_body     = get_field( 'home_download_body' );
$dl_title         = get_the_title( $dl_id );
$dl_excerpt       = has_excerpt( $dl_id ) ? get_the_excerpt( $dl_id ) : '';
?>

<section class="download-feature section--tinted section--padded" aria-labelledby="download-heading">
    <div class="container container--medium">

        <div class="download-feature__layout">

            <?php if ( has_post_thumbnail( $dl_id ) ) : ?>
            <figure class="download-feature__cover" aria-hidden="true" data-reveal="fade">
                <?php echo get_the_post_thumbnail( $dl_id, 'medium_large' ); ?>
            </figure>
            <?php endif; ?>

            <div class="download-feature__content">

                <h2 class="download-feature__headline" id="download-heading" data-reveal>
                    <?php echo esc_html( $section_headline ); ?>
                </h2>

                <?php if ( $section_body ) : ?>
                <div class="download-feature__body" data-reveal data-reveal-delay="1">
                    <?php echo wp_kses_post( wpautop( $section_body ) ); ?>
                </div>
                <?php elseif ( $dl_excerpt ) : ?>
                <p class="download-feature__body" data-reveal data-reveal-delay="1">
                    <?php echo esc_html( $dl_excerpt ); ?>
                </p>
                <?php endif; ?>

                <a href="<?php echo esc_url( get_permalink( $dl_id ) ); ?>"
                   class="btn btn--primary"
                   data-reveal data-reveal-delay="2">
                    View Download
                </a>

            </div>

        </div>

    </div>
</section>
