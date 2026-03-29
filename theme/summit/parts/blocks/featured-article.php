<?php
/**
 * Part: Featured Article Panel
 * Location: parts/blocks/featured-article.php
 *
 * Features a single article on the homepage. Reuses the existing
 * .featured-article component markup and CSS from cards.css.
 *
 * Curated first: uses home_article_item post_object field.
 * Auto fallback: queries latest article with art_featured flag,
 *   then latest article by date.
 * Guard: section is not rendered when no articles exist.
 *
 * ACF fields (from front page):
 *   home_article_headline text        optional
 *   home_article_item     post_object optional — article
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

// Curated selection.
$article = get_field( 'home_article_item' );

// Auto fallback — try featured article first.
if ( empty( $article ) ) {
    $featured_q = new WP_Query( [
        'post_type'      => 'article',
        'posts_per_page' => 1,
        'meta_key'       => 'art_featured',
        'meta_value'     => '1',
        'no_found_rows'  => true,
    ] );
    if ( $featured_q->have_posts() ) {
        $article = $featured_q->posts[0];
    }
    wp_reset_postdata();
}

// Fallback to latest article.
if ( empty( $article ) ) {
    $latest_q = new WP_Query( [
        'post_type'      => 'article',
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ] );
    if ( $latest_q->have_posts() ) {
        $article = $latest_q->posts[0];
    }
    wp_reset_postdata();
}

// Guard — do not render without an article.
if ( empty( $article ) ) {
    return;
}

$section_headline = get_field( 'home_article_headline' ) ?: 'The Future of Luxury';
$art_id      = $article->ID;
$standfirst  = get_field( 'art_standfirst', $art_id );
$read_time   = get_field( 'art_read_time', $art_id );
$cat_terms   = get_the_terms( $art_id, 'article_category' );
$cat_label   = ( ! is_wp_error( $cat_terms ) && ! empty( $cat_terms ) )
               ? $cat_terms[0]->name : '';
?>

<section class="home-article section--padded" aria-labelledby="home-article-heading">
    <div class="container container--medium">

        <header class="home-article__header">
            <h2 class="home-article__section-headline" id="home-article-heading" data-reveal>
                <?php echo esc_html( $section_headline ); ?>
            </h2>
        </header>

        <div class="featured-article" data-reveal data-reveal-delay="1">
            <a href="<?php echo esc_url( get_permalink( $art_id ) ); ?>"
               class="featured-article__link"
               aria-label="<?php echo esc_attr( 'Read article: ' . get_the_title( $art_id ) ); ?>">

                <?php if ( has_post_thumbnail( $art_id ) ) : ?>
                <figure class="featured-article__media" aria-hidden="true">
                    <?php echo get_the_post_thumbnail( $art_id, 'large' ); ?>
                </figure>
                <?php endif; ?>

                <div class="featured-article__body">

                    <?php if ( $cat_label ) : ?>
                    <span class="featured-article__eyebrow"><?php echo esc_html( $cat_label ); ?></span>
                    <?php endif; ?>

                    <h3 class="featured-article__title">
                        <?php echo esc_html( get_the_title( $art_id ) ); ?>
                    </h3>

                    <?php if ( $standfirst ) : ?>
                    <p class="featured-article__standfirst">
                        <?php echo esc_html( $standfirst ); ?>
                    </p>
                    <?php endif; ?>

                    <div class="featured-article__meta">
                        <time datetime="<?php echo esc_attr( get_the_date( 'c', $art_id ) ); ?>">
                            <?php echo esc_html( get_the_date( 'j F Y', $art_id ) ); ?>
                        </time>
                        <?php if ( $read_time ) : ?>
                        <span><?php echo esc_html( $read_time ); ?> min read</span>
                        <?php endif; ?>
                    </div>

                    <span class="featured-article__cta" aria-hidden="true">Read Article</span>

                </div>

            </a>
        </div>

        <footer class="home-article__footer">
            <a href="<?php echo esc_url( get_post_type_archive_link( 'article' ) ); ?>" class="btn btn--secondary">
                View All Articles
            </a>
        </footer>

    </div>
</section>
