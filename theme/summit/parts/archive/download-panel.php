<?php
/**
 * Part: Download Panel
 * Location: parts/archive/download-panel.php
 *
 * Reusable download promo block for archive landing pages.
 * Renders a featured download with cover image, title, description,
 * file type badge, and CTA linking to the download single page.
 *
 * Usage:
 *   set_query_var( 'summit_download_post', $download_post );
 *   set_query_var( 'summit_download_heading', 'Download' ); // optional
 *   get_template_part( 'parts/archive/download-panel' );
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$download = get_query_var( 'summit_download_post', null );
$heading  = get_query_var( 'summit_download_heading', 'Download' );

if ( ! $download instanceof WP_Post ) {
	return;
}

$dl_description = get_field( 'dl_description', $download->ID );
$dl_file_type   = get_field( 'dl_file_type',   $download->ID );
$dl_file_size   = get_field( 'dl_file_size',   $download->ID );
$dl_cover       = get_field( 'dl_cover_image', $download->ID );
$dl_cta_label   = get_field( 'dl_cta_label',   $download->ID );
$dl_permalink   = get_permalink( $download->ID );
$dl_title       = get_the_title( $download->ID );

$cta_text = $dl_cta_label ? $dl_cta_label : 'Download';
?>

<section class="download-panel" aria-labelledby="download-panel-heading">
    <div class="container container--wide">
        <div class="download-panel__inner">

            <?php if ( $dl_cover && ! empty( $dl_cover['url'] ) ) : ?>
            <figure class="download-panel__media" aria-hidden="true">
                <img src="<?php echo esc_url( $dl_cover['sizes']['medium_large'] ?? $dl_cover['url'] ); ?>"
                     alt="<?php echo esc_attr( $dl_cover['alt'] ?: $dl_title ); ?>"
                     width="<?php echo esc_attr( $dl_cover['sizes']['medium_large-width'] ?? $dl_cover['width'] ); ?>"
                     height="<?php echo esc_attr( $dl_cover['sizes']['medium_large-height'] ?? $dl_cover['height'] ); ?>"
                     loading="lazy">
            </figure>
            <?php endif; ?>

            <div class="download-panel__body">
                <h2 class="download-panel__heading" id="download-panel-heading">
                    <?php echo esc_html( $heading ); ?>
                </h2>
                <h3 class="download-panel__title">
                    <?php echo esc_html( $dl_title ); ?>
                </h3>

                <?php if ( $dl_description ) : ?>
                <p class="download-panel__description">
                    <?php echo esc_html( $dl_description ); ?>
                </p>
                <?php endif; ?>

                <div class="download-panel__meta">
                    <?php if ( $dl_file_type ) : ?>
                    <span class="download-panel__badge"><?php echo esc_html( $dl_file_type ); ?></span>
                    <?php endif; ?>
                    <?php if ( $dl_file_size ) : ?>
                    <span class="download-panel__size"><?php echo esc_html( $dl_file_size ); ?></span>
                    <?php endif; ?>
                </div>

                <a href="<?php echo esc_url( $dl_permalink ); ?>"
                   class="btn btn--primary download-panel__cta"
                   aria-label="<?php echo esc_attr( $cta_text . ': ' . $dl_title ); ?>">
                    <?php echo esc_html( $cta_text ); ?>
                </a>
            </div>

        </div>
    </div>
</section>
