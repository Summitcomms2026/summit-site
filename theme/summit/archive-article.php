<?php
/**
 * Template: Article Archive
 * Location: archive-article.php
 * URL: /the-future-of-luxury
 *
 * Displays The Future of Luxury editorial archive.
 * Features a featured article slot on page 1, followed by an article grid.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Featured article — only on page 1.
$featured_article = null;
$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

if ( $paged === 1 ) {
    $featured_q = new WP_Query( [
        'post_type'      => 'article',
        'posts_per_page' => 1,
        'meta_key'       => 'art_featured',
        'meta_value'     => '1',
        'no_found_rows'  => true,
    ] );
    if ( $featured_q->have_posts() ) {
        $featured_article = $featured_q->posts[0];
    }
    wp_reset_postdata();

    // Fallback: use the most recent article if none flagged featured.
    if ( ! $featured_article ) {
        $latest_q = new WP_Query( [
            'post_type'      => 'article',
            'posts_per_page' => 1,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        ] );
        if ( $latest_q->have_posts() ) {
            $featured_article = $latest_q->posts[0];
        }
        wp_reset_postdata();
    }
}
?>

<main id="main" class="site-main" role="main">

    <!-- ── Archive Hero ─────────────────────────────────────────────── -->
    <section class="section--padded" aria-labelledby="archive-hero-heading">
        <div class="container container--medium">
            <div class="archive-hero">
                <p class="archive-hero__eyebrow">The Future of Luxury</p>
                <h1 class="archive-hero__headline" id="archive-hero-heading">
                    Thought Leadership for the Luxury Sector
                </h1>
                <div class="archive-hero__body">
                    <p>Perspectives on brand strategy, experience design, culture and technology shaping the future of luxury.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Featured Article (page 1 only) ───────────────────────────── -->
    <?php if ( $featured_article ) :
        $fa_id       = $featured_article->ID;
        $fa_stand    = get_field( 'art_standfirst', $fa_id );
        $fa_time     = get_field( 'art_read_time', $fa_id );
        $fa_cats     = get_the_terms( $fa_id, 'article_category' );
        $fa_cat      = ( ! is_wp_error( $fa_cats ) && ! empty( $fa_cats ) )
                       ? $fa_cats[0]->name : '';
    ?>
    <section class="section--padded">
        <div class="container container--wide">
            <div class="featured-article">
                <a href="<?php echo esc_url( get_permalink( $fa_id ) ); ?>"
                   class="featured-article__link"
                   aria-label="<?php echo esc_attr( 'Read article: ' . get_the_title( $fa_id ) ); ?>">

                    <?php if ( summit_has_article_hero( $fa_id ) ) : ?>
                    <figure class="featured-article__media" aria-hidden="true">
                        <?php echo summit_article_hero( $fa_id, 'large' ); ?>
                    </figure>
                    <?php endif; ?>

                    <div class="featured-article__body">
                        <?php if ( $fa_cat ) : ?>
                        <span class="featured-article__eyebrow"><?php echo esc_html( $fa_cat ); ?></span>
                        <?php endif; ?>

                        <h2 class="featured-article__title">
                            <?php echo esc_html( get_the_title( $fa_id ) ); ?>
                        </h2>

                        <?php if ( $fa_stand ) : ?>
                        <p class="featured-article__standfirst">
                            <?php echo esc_html( $fa_stand ); ?>
                        </p>
                        <?php endif; ?>

                        <div class="featured-article__meta">
                            <time datetime="<?php echo esc_attr( get_the_date( 'c', $fa_id ) ); ?>">
                                <?php echo esc_html( get_the_date( 'j F Y', $fa_id ) ); ?>
                            </time>
                            <?php if ( $fa_time ) : ?>
                            <span><?php echo esc_html( $fa_time ); ?> min read</span>
                            <?php endif; ?>
                        </div>

                        <span class="featured-article__cta" aria-hidden="true">Read Article</span>
                    </div>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Article Grid ─────────────────────────────────────────────── -->
    <section class="section--padded">
        <div class="container container--wide">

            <?php if ( have_posts() ) : ?>

            <ul class="article-grid" role="list">
                <?php while ( have_posts() ) : the_post();
                    // Skip the featured article to avoid duplicate on page 1.
                    if ( $featured_article && get_the_ID() === $featured_article->ID ) {
                        continue;
                    }

                    $art_id     = get_the_ID();
                    $standfirst = get_field( 'art_standfirst' );
                    $read_time  = get_field( 'art_read_time' );
                    $cat_terms  = get_the_terms( $art_id, 'article_category' );
                    $cat_label  = ( ! is_wp_error( $cat_terms ) && ! empty( $cat_terms ) )
                                  ? $cat_terms[0]->name : '';
                ?>
                <li class="article-card">
                    <a href="<?php the_permalink(); ?>"
                       class="article-card__link"
                       aria-label="<?php echo esc_attr( 'Read: ' . get_the_title() ); ?>">

                        <?php if ( summit_has_article_hero() ) : ?>
                        <figure class="article-card__media" aria-hidden="true">
                            <?php echo summit_article_hero( null, 'medium_large' ); ?>
                        </figure>
                        <?php endif; ?>

                        <div class="article-card__body">
                            <?php if ( $cat_label ) : ?>
                            <span class="article-card__eyebrow"><?php echo esc_html( $cat_label ); ?></span>
                            <?php endif; ?>

                            <h2 class="article-card__title"><?php the_title(); ?></h2>

                            <?php if ( $standfirst ) : ?>
                            <p class="article-card__standfirst"><?php echo esc_html( $standfirst ); ?></p>
                            <?php endif; ?>

                            <div class="article-card__meta">
                                <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                                    <?php echo esc_html( get_the_date( 'j F Y' ) ); ?>
                                </time>
                                <?php if ( $read_time ) : ?>
                                <span><?php echo esc_html( $read_time ); ?> min read</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </li>
                <?php endwhile; ?>
            </ul>

            <div class="pagination">
                <?php the_posts_pagination( [
                    'mid_size'  => 2,
                    'prev_text' => '&larr;',
                    'next_text' => '&rarr;',
                ] ); ?>
            </div>

            <?php else : ?>

            <div class="empty-state">
                <p class="empty-state__message">Articles will appear here as they are published.</p>
            </div>

            <?php endif; ?>

        </div>
    </section>

    <!-- ── CTA Footer ───────────────────────────────────────────────── -->
    <?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php get_footer(); ?>
