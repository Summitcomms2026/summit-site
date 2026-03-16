<?php
/**
 * Template: Article Category Taxonomy Archive
 * Location: taxonomy-article_category.php
 *
 * WordPress template hierarchy:
 *   taxonomy-article_category-{term-slug}.php
 *   → taxonomy-article_category.php     ← this file
 *   → taxonomy.php
 *   → archive.php
 *   → index.php
 *
 * The taxonomy is registered with the key 'article_category'.
 * Its rewrite slug is 'topic' (registered in summit-core/inc/taxonomies.php).
 * Public URL: /topic/{term-slug}
 *
 * Example: the "Luxury Strategy" term is accessible at /topic/luxury-strategy
 *
 * This template is the category-filtered view of The Future of Luxury.
 * It mirrors the layout of page-future-of-luxury.php with the current
 * category highlighted in the filter nav and the hero adapted to the
 * term name and description. The featured article slot belongs to the
 * main /future-of-luxury page; category archives begin with the filter
 * nav and article grid directly.
 *
 * WordPress supplies the article query via the global loop — have_posts()
 * and the_post() iterate over articles already filtered by this term.
 * No custom WP_Query is needed here.
 *
 * Component sequence:
 *   1. Category hero — breadcrumb, term name, term description
 *   2. Category filter nav — all terms, current highlighted with aria-current
 *   3. Article grid (global WP loop — already filtered by term)
 *   4. Pagination
 *   5. Subscribe module → parts/global/subscribe.php
 *
 * SEO & Schema — not injected here.
 * Output handled in inc/seo.php. WordPress sets the canonical URL for
 * taxonomy archives automatically; inc/seo.php adds OG and description.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

$current_term = get_queried_object(); // WP_Term for this article_category

// All article categories for the filter nav
$all_cats = get_terms( [ 'taxonomy' => 'article_category', 'hide_empty' => true ] );
?>

<main id="main" class="site-main site-main--category-archive" role="main">

    <!-- ── 1. Category hero ─────────────────────────────────────────────── -->
    <section class="archive-hero archive-hero--category"
             aria-labelledby="cat-hero-title">
        <div class="container container--medium">
            <header class="archive-hero__header">

                <nav class="archive-hero__breadcrumb" aria-label="Editorial navigation">
                    <a href="<?php echo esc_url( home_url( '/future-of-luxury' ) ); ?>">
                        The Future of Luxury
                    </a>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page">
                        <?php echo esc_html( $current_term->name ); ?>
                    </span>
                </nav>

                <p class="archive-hero__eyebrow">Editorial Category</p>

                <h1 class="archive-hero__title" id="cat-hero-title">
                    <?php echo esc_html( $current_term->name ); ?>
                </h1>

                <?php if ( $current_term->description ) : ?>
                <p class="archive-hero__sub">
                    <?php echo esc_html( $current_term->description ); ?>
                </p>
                <?php else : ?>
                <p class="archive-hero__sub">
                    Articles on
                    <?php echo esc_html( strtolower( $current_term->name ) ); ?>
                    from The Future of Luxury.
                </p>
                <?php endif; ?>

            </header>
        </div>
    </section>

    <!-- ── 2. Category filter nav ───────────────────────────────────────── -->
    <?php if ( ! is_wp_error( $all_cats ) && ! empty( $all_cats ) ) : ?>
    <nav class="archive-filters archive-filters--categories"
         aria-label="Browse by category">
        <div class="container container--wide">
            <ul class="archive-filters__list" role="list">

                <li class="archive-filters__item">
                    <a href="<?php echo esc_url( home_url( '/future-of-luxury' ) ); ?>"
                       class="archive-filters__btn">
                        All
                    </a>
                </li>

                <?php foreach ( $all_cats as $cat ) :
                    $is_active = (int) $cat->term_id === (int) $current_term->term_id;
                ?>
                <li class="archive-filters__item">
                    <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"
                       class="archive-filters__btn <?php echo $is_active ? 'is-active' : ''; ?>"
                       aria-current="<?php echo $is_active ? 'page' : 'false'; ?>">
                        <?php echo esc_html( $cat->name ); ?>
                    </a>
                </li>
                <?php endforeach; ?>

            </ul>
        </div>
    </nav>
    <?php endif; ?>

    <!-- ── 3. Article grid ──────────────────────────────────────────────── -->
    <section class="archive-grid archive-grid--articles"
             aria-label="Articles in this category">
        <div class="container container--wide">

            <?php if ( have_posts() ) : ?>

            <ul class="article-grid" role="list">
                <?php while ( have_posts() ) : the_post();
                    $standfirst = get_field( 'art_standfirst' );
                    $read_time  = get_field( 'art_read_time' );
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
                                <span class="article-card__category">
                                    <?php echo esc_html( $current_term->name ); ?>
                                </span>
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
                            <p class="article-card__standfirst">
                                <?php echo esc_html( $standfirst ); ?>
                            </p>
                            <?php endif; ?>
                            <span class="article-card__cta" aria-hidden="true">
                                Read Article
                            </span>
                        </div>

                    </a>
                </li>
                <?php endwhile; ?>
            </ul>

            <!-- ── 4. Pagination ─────────────────────────────────────── -->
            <?php
            the_posts_pagination( [
                'mid_size'           => 2,
                'prev_text'          => '<span aria-hidden="true">&larr;</span>'
                                      . '<span class="screen-reader-text"> Previous</span>',
                'next_text'          => '<span class="screen-reader-text">Next </span>'
                                      . '<span aria-hidden="true">&rarr;</span>',
                'screen_reader_text' => 'Articles navigation',
                'aria_label'         => 'Articles in ' . esc_html( $current_term->name ),
                'class'              => 'archive-pagination',
            ] );
            ?>

            <?php else : ?>
            <div class="archive-empty">
                <p class="archive-empty__message">
                    No articles published in this category yet.
                </p>
                <a href="<?php echo esc_url( home_url( '/future-of-luxury' ) ); ?>"
                   class="btn btn--secondary">
                    View All Articles
                </a>
            </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- ── 5. Subscribe ─────────────────────────────────────────────────── -->
    <?php
    set_query_var( 'summit_subscribe_heading', 'Subscribe to The Future of Luxury' );
    set_query_var( 'summit_subscribe_id', 'subscribe-cat-' . absint( $current_term->term_id ) );
    get_template_part( 'parts/global/subscribe' );
    ?>

</main>

<?php get_footer(); ?>
