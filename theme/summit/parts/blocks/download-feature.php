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

$dl_id = $download->ID;
$body  = 'Designed for senior managers, this visual conversation demonstrates the principles that define enduring luxury brands and how they can be applied as a business strategy.';

// ACF override — strip tags to prevent raw HTML output.
$acf_body = get_field( 'home_download_body' );
if ( $acf_body ) {
    $body = wp_strip_all_tags( $acf_body );
}
?>

<section class="download-feature section--tinted" aria-labelledby="download-heading">
    <div class="container container--wide">

        <div class="download-feature__layout">

            <div class="download-feature__cover" data-reveal>
                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/characteristics-mainimage.jpg' ); ?>"
                     alt="The Characteristics of Luxury" width="916" height="498" loading="lazy">
            </div>

            <div class="download-feature__content" data-reveal data-reveal-delay="1">

                <h2 class="download-feature__headline" id="download-heading">
                    The Characteristics of Luxury
                </h2>

                <p class="download-feature__body">
                    <?php echo esc_html( $body ); ?>
                </p>

                <a href="<?php echo esc_url( get_permalink( $dl_id ) ); ?>"
                   class="download-feature__cta">
                    Download Your Copy
                </a>

            </div>

        </div>

    </div>
</section>
