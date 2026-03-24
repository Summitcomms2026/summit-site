<?php
/**
 * Template: Article Single
 * Location: single-article.php
 * URL: /the-future-of-luxury/[slug]
 *
 * Renders a single editorial article with prose content,
 * metadata, and related content.
 *
 * ACF fields: art_* prefix (see acf-fields.php)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();
the_post();

$art_id      = get_the_ID();
$standfirst  = get_field( 'art_standfirst' );
$read_time   = get_field( 'art_read_time' );
$source_note = get_field( 'art_source_note' );

// Taxonomy.
$cat_terms = get_the_terms( $art_id, 'article_category' );
$cat_label = ( ! is_wp_error( $cat_terms ) && ! empty( $cat_terms ) )
             ? $cat_terms[0]->name : '';

// Related content.
$related_articles = get_field( 'art_related_articles' );
$related_case     = get_field( 'art_related_case' );
$related_episode  = get_field( 'art_related_episode' );
$related_download = get_field( 'art_related_download' );
?>

<main id="main" class="site-main" role="main">

    <!-- ── Hero ─────────────────────────────────────────────────────── -->
    <section class="section--padded" aria-labelledby="single-hero-title">
        <div class="container container--medium">
            <div class="single-hero">

                <?php if ( $cat_label ) : ?>
                <p class="eyebrow"><?php echo esc_html( $cat_label ); ?></p>
                <?php endif; ?>

                <h1 class="single-hero__title" id="single-hero-title">
                    <?php the_title(); ?>
                </h1>

                <?php if ( $standfirst ) : ?>
                <p class="single-hero__standfirst"><?php echo esc_html( $standfirst ); ?></p>
                <?php endif; ?>

                <div class="meta-bar">
                    <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                        <?php echo esc_html( get_the_date( 'j F Y' ) ); ?>
                    </time>
                    <?php if ( $read_time ) : ?>
                    <span class="meta-bar__separator"></span>
                    <span><?php echo esc_html( $read_time ); ?> min read</span>
                    <?php endif; ?>
                </div>

            </div>

            <?php if ( has_post_thumbnail() ) : ?>
            <figure class="single-hero__media">
                <?php the_post_thumbnail( 'full' ); ?>
            </figure>
            <?php endif; ?>
        </div>
    </section>

    <!-- ── Article Content ──────────────────────────────────────────── -->
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

    <!-- ── Related Content ──────────────────────────────────────────── -->
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

    <!-- ── CTA Footer ───────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php get_footer(); ?>
