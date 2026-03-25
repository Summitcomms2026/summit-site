<?php
/**
 * Template: Single Article
 * Location: single-article.php
 * URL: /the-future-of-luxury/[slug]
 *
 * Component sequence:
 *   1. Article hero — breadcrumb, headline, subtitle, standfirst, featured image
 *   2. Metadata bar — author, date, read time, cornerstone badge
 *   3. Article body (the_content)
 *   4. Source note
 *   5. Author module
 *   6. Related articles   → parts/related/articles.php
 *   7. Related case study → parts/related/case-studies.php
 *   8. Related episode    → parts/related/episodes.php
 *   9. Related download   → parts/related/downloads.php
 *  10. Subscribe module   → parts/global/subscribe.php
 *  11. CTA footer         → parts/global/cta-footer.php
 *
 * SEO & Schema — not injected here.
 * Output handled in inc/seo.php.
 *
 * ACF fields: art_* prefix (see acf-fields.php)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();

    // ── Field retrieval ───────────────────────────────────────────────────
    $art_id           = get_the_ID();
    $standfirst       = get_field( 'art_standfirst' );
    $read_time        = get_field( 'art_read_time' );
    $cornerstone      = get_field( 'art_cornerstone' );
    $source_note      = get_field( 'art_source_note' );
    $subscribe_on     = get_field( 'art_subscribe_prompt' );
    $related_articles = get_field( 'art_related_articles' ); // relationship → array
    $related_case     = get_field( 'art_related_case' );     // post_object → WP_Post|null
    $related_episode  = get_field( 'art_related_episode' );  // post_object → WP_Post|null
    $related_download = get_field( 'art_related_download' ); // post_object → WP_Post|null

    // Author — defensive display name resolution.
    $author_id    = (int) get_the_author_meta( 'ID' );
    $author_login = get_the_author_meta( 'user_login' );
    $raw_display  = get_the_author_meta( 'display_name' );
    $first_name   = get_the_author_meta( 'first_name' );
    $last_name    = get_the_author_meta( 'last_name' );
    $author_bio   = get_the_author_meta( 'description' );

    // Fallback chain: human display_name → first+last → humanised login.
    if ( $raw_display && $raw_display !== $author_login ) {
        $author_name = $raw_display;
    } elseif ( $first_name && $last_name ) {
        $author_name = trim( $first_name . ' ' . $last_name );
    } else {
        $author_name = ucwords( str_replace( [ '.', '_', '-' ], ' ', $author_login ) );
    }

    // Avatar — suppress if no meaningful avatar found.
    $avatar_data    = get_avatar_data( $author_id, [ 'size' => 120 ] );
    $has_avatar     = ! empty( $avatar_data['found_avatar'] );

    // Category.
    $cat_terms = get_the_terms( $art_id, 'article_category' );
    $cat_label = ( ! is_wp_error( $cat_terms ) && ! empty( $cat_terms ) )
                 ? $cat_terms[0]->name : '';
    $cat_url   = ( ! is_wp_error( $cat_terms ) && ! empty( $cat_terms ) )
                 ? get_term_link( $cat_terms[0] ) : '';

    $pub_date = get_the_date( 'j F Y' );
    $pub_iso  = get_the_date( 'c' );
?>

<main id="main" class="site-main" role="main">

    <!-- ── 1 & 2. Hero + metadata ───────────────────────────────────────── -->
    <section class="section--padded" aria-labelledby="single-hero-title">
        <div class="container container--medium">

            <nav class="article-hero__breadcrumb" aria-label="Article navigation">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'article' ) ); ?>">
                    The Future of Luxury
                </a>
                <?php if ( $cat_label && ! is_wp_error( $cat_url ) ) : ?>
                <span aria-hidden="true">/</span>
                <a href="<?php echo esc_url( $cat_url ); ?>"><?php echo esc_html( $cat_label ); ?></a>
                <?php endif; ?>
            </nav>

            <div class="single-hero">

                <?php if ( $cat_label ) : ?>
                <p class="eyebrow"><?php echo esc_html( $cat_label ); ?></p>
                <?php endif; ?>

                <h1 class="single-hero__title" id="single-hero-title">
                    <?php the_title(); ?>
                </h1>

                <?php
                $subtitle = get_field( 'art_subtitle' );
                if ( $subtitle ) : ?>
                <p class="single-hero__subtitle"><?php echo esc_html( $subtitle ); ?></p>
                <?php endif; ?>

                <?php if ( $standfirst ) : ?>
                <p class="single-hero__standfirst"><?php echo esc_html( $standfirst ); ?></p>
                <?php endif; ?>

                <div class="meta-bar" role="contentinfo" aria-label="Article information">
                    <?php if ( $author_name ) : ?>
                    <span class="meta-bar__author"><?php echo esc_html( $author_name ); ?></span>
                    <?php endif; ?>
                    <time datetime="<?php echo esc_attr( $pub_iso ); ?>">
                        <?php echo esc_html( $pub_date ); ?>
                    </time>
                    <?php if ( $read_time ) : ?>
                    <span class="meta-bar__separator"></span>
                    <span><?php echo absint( $read_time ); ?> min read</span>
                    <?php endif; ?>
                    <?php if ( $cornerstone ) : ?>
                    <span class="meta-bar__cornerstone">Essential Reading</span>
                    <?php endif; ?>
                </div>

            </div>

            <?php if ( summit_has_article_hero() ) : ?>
            <figure class="single-hero__media">
                <?php echo summit_article_hero( null, 'full' ); ?>
            </figure>
            <?php endif; ?>

        </div>
    </section>

    <!-- ── 3 & 4. Article body + source note ────────────────────────────── -->
    <section class="section--padded">
        <div class="container container--narrow">
            <div class="prose">
                <?php the_content(); ?>
            </div>

            <?php if ( $source_note ) : ?>
            <p class="single-article__source-note">
                <?php echo esc_html( $source_note ); ?>
            </p>
            <?php endif; ?>
        </div>
    </section>

    <!-- ── 5. Author module ──────────────────────────────────────────────── -->
    <?php if ( $author_name ) : ?>
    <section class="section--padded author-module" aria-labelledby="author-module-heading">
        <div class="container container--narrow">
            <div class="author-module__inner">

                <?php if ( $has_avatar ) :
                    $avatar = get_avatar( $author_id, 120, '', $author_name, [ 'class' => 'author-module__avatar' ] );
                ?>
                <figure class="author-module__portrait" aria-hidden="true">
                    <?php echo $avatar; ?>
                </figure>
                <?php endif; ?>

                <div class="author-module__body">
                    <h2 class="screen-reader-text" id="author-module-heading">About the author</h2>
                    <p class="author-module__display-name"><?php echo esc_html( $author_name ); ?></p>
                    <?php if ( $author_bio ) : ?>
                    <p class="author-module__bio"><?php echo esc_html( $author_bio ); ?></p>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── 6–9. Related content ──────────────────────────────────────────── -->
    <?php
    get_template_part( 'parts/related/articles', null, [
        'posts'    => $related_articles ?: [],
        'headline' => 'Related Reading',
    ] );

    get_template_part( 'parts/related/case-studies', null, [
        'posts'    => $related_case ?: [],
        'headline' => 'Related Work',
    ] );

    get_template_part( 'parts/related/episodes', null, [
        'posts'    => $related_episode ?: [],
        'headline' => 'Related Episode',
    ] );

    get_template_part( 'parts/related/downloads', null, [
        'posts'    => $related_download ?: [],
        'headline' => 'Related Download',
    ] );
    ?>

    <!-- ── 10. Subscribe ─────────────────────────────────────────────────── -->
    <?php if ( $subscribe_on !== false ) :
        get_template_part( 'parts/global/subscribe' );
    endif; ?>

    <!-- ── 11. CTA Footer ───────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php
endwhile;
get_footer();
