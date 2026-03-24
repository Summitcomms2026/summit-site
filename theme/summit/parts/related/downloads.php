<?php
/**
 * Part: Related Download
 * Location: parts/related/downloads.php
 *
 * Renders a related download card from a supplied WP_Post object or array.
 * Guard: suppresses output if no post provided.
 *
 * Usage:
 *   get_template_part( 'parts/related/downloads', null, [
 *       'posts'    => $related_download,
 *       'headline' => 'Related Download',
 *   ] );
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$posts    = $args['posts']    ?? [];
$headline = $args['headline'] ?? 'Related Download';

// Normalise: ACF post_object may return a single post.
if ( $posts instanceof WP_Post ) {
    $posts = [ $posts ];
}

if ( empty( $posts ) ) {
    return;
}

$download  = $posts[0];
$dl_id     = $download->ID;
$desc      = get_field( 'dl_description', $dl_id );
$file_type = get_field( 'dl_file_type', $dl_id );
$file_size = get_field( 'dl_file_size', $dl_id );
$cover     = get_field( 'dl_cover_image', $dl_id );
?>

<section class="related-section section--padded" aria-labelledby="related-download-heading">
    <div class="container container--medium">

        <h2 class="related-section__headline" id="related-download-heading">
            <?php echo esc_html( $headline ); ?>
        </h2>

        <div class="download-card">
            <a href="<?php echo esc_url( get_permalink( $dl_id ) ); ?>"
               class="download-card__link"
               aria-label="<?php echo esc_attr( 'Download: ' . get_the_title( $dl_id ) ); ?>">

                <?php if ( $cover ) : ?>
                <figure class="download-card__cover" aria-hidden="true">
                    <img src="<?php echo esc_url( $cover['url'] ); ?>"
                         alt="<?php echo esc_attr( $cover['alt'] ?? '' ); ?>"
                         loading="lazy">
                </figure>
                <?php elseif ( has_post_thumbnail( $dl_id ) ) : ?>
                <figure class="download-card__cover" aria-hidden="true">
                    <?php echo get_the_post_thumbnail( $dl_id, 'medium' ); ?>
                </figure>
                <?php endif; ?>

                <div>
                    <?php if ( $file_type ) : ?>
                    <span class="download-card__badge"><?php echo esc_html( $file_type ); ?></span>
                    <?php endif; ?>

                    <h3 class="download-card__title"><?php echo esc_html( get_the_title( $dl_id ) ); ?></h3>

                    <?php if ( $desc ) : ?>
                    <p class="download-card__description"><?php echo esc_html( $desc ); ?></p>
                    <?php endif; ?>

                    <?php if ( $file_size ) : ?>
                    <span class="download-card__meta"><?php echo esc_html( $file_size ); ?></span>
                    <?php endif; ?>
                </div>
            </a>
        </div>

    </div>
</section>
