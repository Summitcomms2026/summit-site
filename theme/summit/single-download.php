<?php
/**
 * Template: Download Single
 * Location: single-download.php
 * URL: /downloads/[slug]
 *
 * Renders a single download asset with cover image,
 * description, download CTA, and related content.
 *
 * ACF fields: dl_* prefix (see acf-fields.php)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();
the_post();

$dl_id      = get_the_ID();
$desc       = get_field( 'dl_description' );
$file       = get_field( 'dl_file' );
$file_type  = get_field( 'dl_file_type' );
$file_size  = get_field( 'dl_file_size' );
$cover      = get_field( 'dl_cover_image' );
$cta_label  = get_field( 'dl_cta_label' ) ?: 'Download';

// Related content.
$related_case    = get_field( 'dl_related_case' );
$related_article = get_field( 'dl_related_article' );
$related_episode = get_field( 'dl_related_episode' );
?>

<main id="main" class="site-main" role="main">

    <!-- ── Hero ─────────────────────────────────────────────────────── -->
    <section class="section--padded" aria-labelledby="single-hero-title">
        <div class="container container--medium">
            <div class="single-download__layout">

                <?php if ( $cover ) : ?>
                <figure class="single-download__cover">
                    <img src="<?php echo esc_url( $cover['url'] ); ?>"
                         alt="<?php echo esc_attr( $cover['alt'] ?? '' ); ?>"
                         width="<?php echo esc_attr( $cover['width'] ?? '' ); ?>"
                         height="<?php echo esc_attr( $cover['height'] ?? '' ); ?>"
                         loading="lazy">
                </figure>
                <?php elseif ( has_post_thumbnail() ) : ?>
                <figure class="single-download__cover">
                    <?php the_post_thumbnail( 'medium_large' ); ?>
                </figure>
                <?php endif; ?>

                <div>
                    <?php if ( $file_type ) : ?>
                    <p class="eyebrow"><?php echo esc_html( $file_type ); ?></p>
                    <?php endif; ?>

                    <h1 class="single-hero__title" id="single-hero-title">
                        <?php the_title(); ?>
                    </h1>

                    <?php if ( $desc ) : ?>
                    <div class="single-download__description">
                        <?php echo wp_kses_post( wpautop( $desc ) ); ?>
                    </div>
                    <?php endif; ?>

                    <?php if ( $file_size ) : ?>
                    <div class="single-download__file-meta">
                        <?php if ( $file_type ) : ?>
                        <span><?php echo esc_html( $file_type ); ?></span>
                        <?php endif; ?>
                        <span><?php echo esc_html( $file_size ); ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if ( $file ) : ?>
                    <a href="<?php echo esc_url( $file['url'] ); ?>"
                       class="btn btn--primary"
                       download>
                        <?php echo esc_html( $cta_label ); ?>
                    </a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>

    <!-- ── Related Content ──────────────────────────────────────────── -->
    <?php
    get_template_part( 'parts/related/case-studies', null, [
        'posts'    => $related_case ?: [],
        'headline' => 'Related Work',
    ] );

    get_template_part( 'parts/related/articles', null, [
        'posts'    => $related_article ?: [],
        'headline' => 'Related Reading',
    ] );

    get_template_part( 'parts/related/episodes', null, [
        'posts'    => $related_episode ?: [],
        'headline' => 'Related Episode',
    ] );
    ?>

    <!-- ── CTA Footer ───────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php get_footer(); ?>
