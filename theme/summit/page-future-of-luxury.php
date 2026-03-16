<?php
/**
 * Template: The Future of Luxury
 * Location: page-future-of-luxury.php
 *
 * WordPress template hierarchy: page-{slug}.php → page.php → index.php
 * Matches the WordPress page with slug: future-of-luxury  (URL: /future-of-luxury)
 *
 * The article CPT has has_archive => false. This page template owns
 * the primary editorial archive display via a custom WP_Query.
 * Taxonomy archives (article_category) are handled by taxonomy-article_category.php.
 *
 * Component sequence:
 *   1. Archive hero (page title + intro)
 *   2. Featured article (flagged art_featured = 1)
 *   3. Category navigation → links to taxonomy-article_category.php archives
 *   4. Article grid (standard WP_Query)
 *   5. Pagination
 *   6. Subscribe module → parts/global/subscribe.php
 *
 * SEO & Schema — not injected here.
 * Output handled in inc/seo.php via standard page SEO logic.
 * Schema type: Blog + CollectionPage
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

// ── Page context ──────────────────────────────────────────────────────────
the_post(); // page loop — consumes the wp_page post

// ── Featured article query ────────────────────────────────────────────────
$featured_article = null;
$featured_query   = new WP_Query( [
    'post_type'      => 'article',
    'posts_per_page' => 1,
    'meta_key'       => 'art_featured',
    'meta_value'     => '1',
    'meta_compare'   => '=',
    'no_found_rows'  => true,
] );
if ( $featured_query->have_posts() ) {
    $featured_query->the_post();
    $featured_article = get_post();
    wp_reset_postdata();
}

// ── Pagination for main article grid ─────────────────────────────────────
$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;

// ── Article grid query ────────────────────────────────────────────────────
// Exclude the featured article from the main grid to prevent duplication.
$exclude_ids = $featured_article ? [ $featured_article->ID ] : [];

$article_query = new WP_Query( [
    'post_type'      => 'article',
    'posts_per_page' => 9,
    'paged'          => $paged,
    'post__not_in'   => $exclude_ids,
    'orderby'        => 'date',
    'order'          => 'DESC',
] );

// ── Article categories for the filter nav ────────────────────────────────
$article_cats = get_terms( [ 'taxonomy' => 'article_category', 'hide_empty' => true ] );
?>

<main id="main" class="site-main site-main--future-of-luxury" role="main">

    <!-- ── 1. Archive hero ──────────────────────────────────────────────── -->
    <section class="archive-hero archive-hero--editorial" aria-labelledby="fol-hero-title">
        <div class="container container--medium">
            <header class="archive-hero__header">
                <p class="archive-hero__eyebrow">Editorial</p>
                <h1 class="archive-hero__title" id="fol-hero-title">
                    <?php echo esc_html( get_the_title() ); ?>
                </h1>
                <?php
                $page_intro = get_the_content();
                if ( $page_intro ) :
                    echo apply_filters( 'the_content', $page_intro ); // phpcs:ignore
                else : ?>
                <p class="archive-hero__sub">
                    Strategic thinking on luxury, culture, technology and the forces
                    shaping how sophisticated audiences assign value.
                </p>
                <?php endif; ?>
            </header>
        </div>
    </section>

    <!-- ── 2. Featured article ──────────────────────────────────────────── -->
    <?php if ( $featured_article ) :
        $feat_standfirst = get_field( 'art_standfirst', $featured_article->ID );
        $feat_read_time  = get_field( 'art_read_time',  $featured_article->ID );
        $feat_cats       = get_the_terms( $featured_article->ID, 'article_category' );
        $feat_cat_name   = ( ! is_wp_error( $feat_cats ) && ! empty( $feat_cats ) )
                           ? $feat_cats[0]->name : '';
        $feat_thumb      = get_the_post_thumbnail( $featured_article->ID, 'full' );
    ?>
    <section class="featured-article" aria-labelledby="featured-article-title">
        <div class="container container--wide">
            <a href="<?php echo esc_url( get_permalink( $featured_article->ID ) ); ?>"
               class="featured-article__link">

                <?php if ( $feat_thumb ) : ?>
                <figure class="featured-article__media" aria-hidden="true">
                    <?php echo $feat_thumb; ?>
                </figure>
                <?php endif; ?>

                <div class="featured-article__body">
                    <?php if ( $feat_cat_name ) : ?>
                    <p class="featured-article__eyebrow"><?php echo esc_html( $feat_cat_name ); ?></p>
                    <?php endif; ?>
                    <h2 class="featured-article__title" id="featured-article-title">
                        <?php echo esc_html( get_the_title( $featured_article->ID ) ); ?>
                    </h2>
                    <?php if ( $feat_standfirst ) : ?>
                    <p class="featured-article__standfirst">
                        <?php echo esc_html( $feat_standfirst ); ?>
                    </p>
                    <?php endif; ?>
                    <div class="featured-article__meta">
                        <time datetime="<?php echo esc_attr( get_the_date( 'c', $featured_article->ID ) ); ?>">
                            <?php echo esc_html( get_the_date( 'j F Y', $featured_article->ID ) ); ?>
                        </time>
                        <?php if ( $feat_read_time ) : ?>
                        <span><?php echo absint( $feat_read_time ); ?> min read</span>
                        <?php endif; ?>
                    </div>
                    <span class="featured-article__cta" aria-hidden="true">Read Article</span>
                </div>

            </a>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── 3. Category navigation ───────────────────────────────────────── -->
    <?php if ( ! is_wp_error( $article_cats ) && ! empty( $article_cats ) ) : ?>
    <nav class="archive-filters archive-filters--categories" aria-label="Browse by category">
        <div class="container container--wide">
            <ul class="archive-filters__list" role="list">

                <li class="archive-filters__item">
                    <a href="<?php echo esc_url( home_url( '/future-of-luxury' ) ); ?>"
                       class="archive-filters__btn is-active"
                       aria-current="true">
                        All
                    </a>
                </li>

                <?php foreach ( $article_cats as $cat ) : ?>
                <li class="archive-filters__item">
                    <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"
                       class="archive-filters__btn">
                        <?php echo esc_html( $cat->name ); ?>
                    </a>
                </li>
                <?php endforeach; ?>

            </ul>
        </div>
    </nav>
    <?php endif; ?>

    <!-- ── 4. Article grid ──────────────────────────────────────────────── -->
    <section class="archive-grid archive-grid--articles" aria-label="Article index">
        <div class="container container--wide">

            <?php if ( $article_query->have_posts() ) : ?>

            <ul class="article-grid" role="list">
                <?php while ( $article_query->have_posts() ) : $article_query->the_post();
                    $standfirst = get_field( 'art_standfirst' );
                    $read_time  = get_field( 'art_read_time' );
                    $cats       = get_the_terms( get_the_ID(), 'article_category' );
                    $cat_name   = ( ! is_wp_error( $cats ) && ! empty( $cats ) )
                                  ? $cats[0]->name : '';
                ?>
                <li class="article-card" data-post-id="<?php the_ID(); ?>">
                    <a href="<?php the_permalink(); ?>"
                       class="article-card__link"
                       aria-label="<?php echo esc_attr( 'Read: ' . get_the_title() ); ?>">

                        <?php if ( has_post_thumbnail() ) : ?>
                        <figure class="article-card__media" aria-hidden="true">
                            <?php the_post_thumbnail( 'medium_large' ); ?>
                        </figure>
                        <?php endif; ?>

                        <div class="article-card__body">
                            <div class="article-card__meta">
                                <?php if ( $cat_name ) : ?>
                                <span class="article-card__category"><?php echo esc_html( $cat_name ); ?></span>
                                <?php endif; ?>
                                <time class="article-card__date"
                                      datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                                    <?php echo esc_html( get_the_date( 'j F Y' ) ); ?>
                                </time>
                                <?php if ( $read_time ) : ?>
                                <span class="article-card__read-time">
                                    <?php echo absint( $read_time ); ?> min read
                                </span>
                                <?php endif; ?>
                            </div>
                            <h2 class="article-card__title"><?php the_title(); ?></h2>
                            <?php if ( $standfirst ) : ?>
                            <p class="article-card__standfirst"><?php echo esc_html( $standfirst ); ?></p>
                            <?php endif; ?>
                            <span class="article-card__cta" aria-hidden="true">Read Article</span>
                        </div>

                    </a>
                </li>
                <?php endwhile; ?>
            </ul>

            <!-- ── 5. Pagination ─────────────────────────────────────── -->
            <?php
            $big = 999999;
            echo paginate_links( [
                'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format'    => '?paged=%#%',
                'current'   => max( 1, $paged ),
                'total'     => $article_query->max_num_pages,
                'prev_text' => '<span aria-hidden="true">&larr;</span><span class="screen-reader-text"> Previous</span>',
                'next_text' => '<span class="screen-reader-text">Next </span><span aria-hidden="true">&rarr;</span>',
                'type'      => 'list',
            ] );
            wp_reset_postdata();
            ?>

            <?php else : ?>
            <div class="archive-empty">
                <p class="archive-empty__message">No articles published yet.</p>
            </div>
            <?php wp_reset_postdata(); ?>
            <?php endif; ?>

        </div>
    </section>

    <!-- ── 6. Subscribe ─────────────────────────────────────────────────── -->
    <?php
    set_query_var( 'summit_subscribe_heading', 'Subscribe to The Future of Luxury' );
    set_query_var( 'summit_subscribe_sub',     'Thinking on luxury, culture and the forces shaping how sophisticated audiences assign value — delivered directly.' );
    get_template_part( 'parts/global/subscribe' );
    ?>

</main>

<?php get_footer(); ?>
