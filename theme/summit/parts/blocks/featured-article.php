<?php
/**
 * Part: Featured Article Panel — 3-up editorial strip
 * Location: parts/blocks/featured-article.php
 *
 * Shows 3 articles in a horizontal grid on the homepage.
 * Design reference: panel7 — 1920x1226.
 *
 * Curated first: uses home_article_items relationship field (max 3).
 * Auto fallback: queries 3 latest articles.
 * Guard: section is not rendered when no articles exist.
 *
 * ACF fields (from front page):
 *   home_article_headline text         optional
 *   home_article_items    relationship  optional — article, max 3
 *   home_article_item     post_object   optional — legacy single article
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

// Curated selection — relationship field returns array of WP_Post objects.
$articles = get_field( 'home_article_items' );

// Legacy fallback — single post_object field.
if ( empty( $articles ) ) {
    $single = get_field( 'home_article_item' );
    if ( $single ) {
        $articles = [ $single ];
    }
}

// Auto fallback — 3 most recent articles.
if ( empty( $articles ) ) {
    $article_q = new WP_Query( [
        'post_type'      => 'article',
        'posts_per_page' => 6,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ] );
    $articles = $article_q->posts;
    wp_reset_postdata();
}

// Guard — do not render without articles.
if ( empty( $articles ) ) {
    return;
}

$section_headline = get_field( 'home_article_headline' ) ?: 'The Future of Luxury';
?>

<section class="home-article section--padded" aria-labelledby="home-article-heading">
    <div class="container container--wide">

        <header class="home-article__header">
            <div>
                <h2 class="home-article__section-headline" id="home-article-heading" data-reveal>
                    <?php echo esc_html( $section_headline ); ?>
                </h2>
                <p class="home-article__sub" data-reveal data-reveal-delay="1">
                    News and media published by Summit Communication Group exclusively here, first.
                </p>
            </div>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'article' ) ); ?>"
               class="btn btn--primary" data-reveal data-reveal-delay="1">
                All Articles
            </a>
        </header>

        <div class="home-article__grid">
            <?php
            $card_delays = [ 1, 2, 3 ];
            foreach ( $articles as $ai => $art_post ) :
                $art_id    = $art_post->ID;
                $delay     = $card_delays[ $ai ] ?? 3;
                $cat_terms  = get_the_terms( $art_id, 'article_category' );
                $cat_label  = ( ! is_wp_error( $cat_terms ) && ! empty( $cat_terms ) )
                              ? $cat_terms[0]->name : '';
                $read_time  = get_field( 'art_read_time', $art_id );
                $standfirst = get_field( 'art_standfirst', $art_id );
                if ( ! $read_time ) {
                    $read_time = summit_estimated_read_time( $art_id );
                }
            ?>
            <a href="<?php echo esc_url( get_permalink( $art_id ) ); ?>"
               class="home-article__card"
               data-reveal data-reveal-delay="<?php echo $delay; ?>">

                <?php if ( has_post_thumbnail( $art_id ) ) : ?>
                <figure class="home-article__card-media" aria-hidden="true">
                    <?php echo get_the_post_thumbnail( $art_id, 'large' ); ?>
                </figure>
                <?php endif; ?>

                <?php if ( $cat_label ) : ?>
                <span class="home-article__card-cat"><?php echo esc_html( $cat_label ); ?></span>
                <?php endif; ?>

                <h3 class="home-article__card-title">
                    <?php echo esc_html( get_the_title( $art_id ) ); ?>
                </h3>

                <?php if ( $standfirst ) : ?>
                <p class="home-article__card-standfirst">
                    <?php echo esc_html( wp_trim_words( $standfirst, 20, "\u{2026}" ) ); ?>
                </p>
                <?php endif; ?>

                <div class="home-article__card-meta">
                    <time datetime="<?php echo esc_attr( get_the_date( 'c', $art_id ) ); ?>">
                        <?php echo esc_html( get_the_date( 'j F Y', $art_id ) ); ?>
                    </time>
                    <?php if ( $read_time ) : ?>
                    <span><?php echo absint( $read_time ); ?> min read</span>
                    <?php endif; ?>
                </div>

            </a>
            <?php endforeach; ?>
        </div>

        <div class="home-article__nav" aria-label="Article slider navigation">
            <button class="home-article__arrow home-article__arrow--prev" aria-label="Previous articles" type="button">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button class="home-article__arrow home-article__arrow--next" aria-label="Next articles" type="button">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>

        <script>
        (function(){
            const grid = document.querySelector('.home-article__grid');
            if (!grid) return;
            const prev = document.querySelector('.home-article__arrow--prev');
            const next = document.querySelector('.home-article__arrow--next');
            const scrollAmount = () => {
                const card = grid.querySelector('.home-article__card');
                if (!card) return grid.clientWidth;
                return card.offsetWidth + 24; // card width + gap
            };
            if (prev) prev.addEventListener('click', () => {
                grid.scrollBy({ left: -scrollAmount(), behavior: 'smooth' });
            });
            if (next) next.addEventListener('click', () => {
                grid.scrollBy({ left: scrollAmount(), behavior: 'smooth' });
            });
        })();
        </script>

        <?php /* CTA is in the header row per approved comp */ ?>

    </div>
</section>
