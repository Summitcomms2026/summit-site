<?php
/**
 * Template: Single Download
 * Location: single-download.php
 *
 * WordPress template hierarchy: single-download.php → single.php → index.php
 *
 * Component sequence:
 *   1. Download hero — cover image, title, description, file metadata
 *   2. Download CTA  — direct download or gated form placeholder
 *   3. Related articles  → parts/related/articles.php
 *   4. Related episode   → parts/related/episodes.php
 *   5. CTA footer        → parts/global/cta-footer.php
 *
 * SEO & Schema — not injected here.
 * Output handled in inc/seo.php:
 *   - summit_seo_meta()         hooked on wp_head
 *   - summit_schema_download()  hooked on wp_head (schema.org/DigitalDocument)
 * ACF fields consumed: dl_seo_title, dl_meta_desc, dl_og_image
 * Schema fields: get_the_title(), dl_file (url), dl_file_type, dl_og_image
 *
 * Gate logic:
 * When dl_gated is true, replace the direct download link with the
 * ActiveCampaign form integration. In v1.0 the gate is not yet wired;
 * a form placeholder renders. Wire up in v1.1 via inc/ac-forms.php.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();

    // ── Field retrieval ───────────────────────────────────────────────────
    $description      = get_field( 'dl_description' );
    $file             = get_field( 'dl_file' );           // ACF file array
    $file_type        = get_field( 'dl_file_type' );
    $file_size        = get_field( 'dl_file_size' );
    $cover_image      = get_field( 'dl_cover_image' );    // ACF image array
    $cta_label        = get_field( 'dl_cta_label' ) ?: 'Download';
    $is_gated         = get_field( 'dl_gated' );
    $related_articles = get_field( 'dl_related_article' );  // relationship → array
    $related_episode  = get_field( 'dl_related_episode' );  // post_object → WP_Post|null

    // Resolve file URL and filename
    $file_url  = is_array( $file ) ? ( $file['url']      ?? '' ) : '';
    $file_name = is_array( $file ) ? ( $file['filename']  ?? 'download' ) : 'download';

    // Cover image: prefer dl_cover_image, fall back to featured image
    $cover_src = '';
    $cover_alt = '';
    if ( is_array( $cover_image ) && ! empty( $cover_image['url'] ) ) {
        $cover_src = $cover_image['url'];
        $cover_alt = $cover_image['alt'] ?: get_the_title();
        $cover_w   = absint( $cover_image['width']  ?? 800 );
        $cover_h   = absint( $cover_image['height'] ?? 1000 );
    } elseif ( has_post_thumbnail() ) {
        $cover_src = get_the_post_thumbnail_url( get_the_ID(), 'large' );
        $cover_alt = get_the_title();
        $cover_w   = 800;
        $cover_h   = 1000;
    }
?>

<main id="main" class="site-main site-main--download" role="main">

    <!-- ── 1. Download hero ─────────────────────────────────────────────── -->
    <section class="download-hero" aria-labelledby="download-hero-title">
        <div class="container container--medium">

            <nav class="download-hero__breadcrumb" aria-label="Downloads navigation">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Summit</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">Downloads</span>
            </nav>

            <div class="download-hero__layout">

                <!-- Cover image -->
                <?php if ( $cover_src ) : ?>
                <figure class="download-hero__cover" aria-hidden="true">
                    <img src="<?php echo esc_url( $cover_src ); ?>"
                         alt="<?php echo esc_attr( $cover_alt ); ?>"
                         width="<?php echo $cover_w; ?>"
                         height="<?php echo $cover_h; ?>"
                         loading="eager">
                </figure>
                <?php endif; ?>

                <!-- Title, description, CTA -->
                <div class="download-hero__body">

                    <div class="download-hero__meta">
                        <?php if ( $file_type ) : ?>
                        <span class="download-hero__file-type"><?php echo esc_html( $file_type ); ?></span>
                        <?php endif; ?>
                        <?php if ( $file_size ) : ?>
                        <span class="download-hero__file-size"><?php echo esc_html( $file_size ); ?></span>
                        <?php endif; ?>
                    </div>

                    <h1 class="download-hero__title" id="download-hero-title">
                        <?php the_title(); ?>
                    </h1>

                    <?php if ( $description ) : ?>
                    <p class="download-hero__description"><?php echo esc_html( $description ); ?></p>
                    <?php endif; ?>

                    <!-- ── 2. Download CTA ─────────────────────────────── -->
                    <?php if ( $file_url && ! $is_gated ) : ?>

                    <div class="download-cta">
                        <a href="<?php echo esc_url( $file_url ); ?>"
                           class="btn btn--primary download-cta__btn"
                           download="<?php echo esc_attr( $file_name ); ?>"
                           rel="noopener">
                            <?php echo esc_html( $cta_label ); ?>
                        </a>
                        <?php if ( $file_size ) : ?>
                        <span class="download-cta__size"><?php echo esc_html( $file_size ); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php elseif ( $is_gated ) : ?>

                    <div class="download-cta download-cta--gated">
                        <?php
                        /*
                         * Gated download: ActiveCampaign form integration.
                         * Wire this up in v1.1 via inc/ac-forms.php and replace
                         * the placeholder form below with:
                         *   echo do_shortcode( '[summit_gate_form id="' . get_the_ID() . '"]' );
                         */
                        if ( shortcode_exists( 'summit_gate_form' ) ) :
                            echo do_shortcode( '[summit_gate_form id="' . get_the_ID() . '"]' );
                        else : ?>
                        <p class="download-cta__gate-note">
                            Enter your email address to access this resource.
                        </p>
                        <form class="download-cta__gate-form"
                              action="#"
                              method="post"
                              aria-label="Access this download">
                            <label for="gate-email-<?php the_ID(); ?>" class="screen-reader-text">
                                Email address
                            </label>
                            <input type="email"
                                   id="gate-email-<?php the_ID(); ?>"
                                   name="email"
                                   placeholder="Your email address"
                                   required
                                   autocomplete="email"
                                   class="download-cta__input">
                            <button type="submit" class="btn btn--primary">
                                <?php echo esc_html( $cta_label ); ?>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>

                    <?php endif; ?>

                </div>

            </div>
        </div>
    </section>

    <!-- ── 3. Related articles ──────────────────────────────────────────── -->
    <?php if ( ! empty( $related_articles ) ) :
        set_query_var( 'summit_related_articles', $related_articles );
        set_query_var( 'summit_related_heading',  'Related Reading' );
        get_template_part( 'parts/related/articles' );
    endif; ?>

    <!-- ── 4. Related episode ───────────────────────────────────────────── -->
    <?php if ( $related_episode instanceof WP_Post ) :
        set_query_var( 'summit_related_episodes', $related_episode );
        set_query_var( 'summit_related_heading',  'Related Episode' );
        get_template_part( 'parts/related/episodes' );
    endif; ?>

    <!-- ── 5. CTA footer ────────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php
endwhile;
get_footer();
