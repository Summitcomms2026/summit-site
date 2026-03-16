<?php
/**
 * Template: Single Article
 * Location: single-article.php
 *
 * WordPress template hierarchy: single-article.php → single.php → index.php
 *
 * Component sequence (summit_page_specs.md §10):
 *   1. Article hero — headline, standfirst, featured image
 *   2. Metadata bar — category, author, date, read time
 *   3. Article body (the_content)
 *   4. Author module
 *   5. Related articles   → parts/related/articles.php
 *   6. Related episode    → parts/related/episodes.php
 *   7. Subscribe module   → parts/global/subscribe.php
 *
 * SEO & Schema — not injected here.
 * Output handled in inc/seo.php:
 *   - summit_seo_meta()      hooked on wp_head (title, description, OG, canonical, noindex)
 *   - summit_schema_article() hooked on wp_head (schema.org/Article + schema.org/Person)
 * ACF fields consumed: art_seo_title, art_meta_desc, art_og_image, art_canonical, art_noindex
 * Schema fields: get_the_title(), get_the_date('c'), get_the_modified_date('c'),
 *                get_the_author_meta('display_name'), art_og_image, get_permalink()
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();

    // ── Field retrieval ───────────────────────────────────────────────────
    $standfirst       = get_field( 'art_standfirst' );
    $read_time        = get_field( 'art_read_time' );
    $cornerstone      = get_field( 'art_cornerstone' );
    $subscribe_on     = get_field( 'art_subscribe_prompt' );
    $related_articles = get_field( 'art_related_articles' ); // relationship → array
    $related_episode  = get_field( 'art_related_episode' );  // post_object → WP_Post|null

    // Author — extended user profile fields managed in ACF user group
    $author_id   = (int) get_the_author_meta( 'ID' );
    $author_name = get_the_author_meta( 'display_name' );
    $author_bio  = get_the_author_meta( 'description' );

    // Category
    $categories = get_the_terms( get_the_ID(), 'article_category' );
    $cat_name   = ( ! is_wp_error( $categories ) && ! empty( $categories ) )
                  ? $categories[0]->name : '';
    $cat_url    = ( ! is_wp_error( $categories ) && ! empty( $categories ) )
                  ? get_term_link( $categories[0] ) : '';

    $pub_date = get_the_date( 'j F Y' );
    $pub_iso  = get_the_date( 'c' );
?>

<main id="main" class="site-main site-main--article" role="main">

    <!-- ── 1 & 2. Article hero + metadata bar ───────────────────────────── -->
    <section class="article-hero" aria-labelledby="article-hero-title">
        <div class="container container--narrow">

            <nav class="article-hero__breadcrumb" aria-label="Article navigation">
                <a href="<?php echo esc_url( home_url( '/future-of-luxury' ) ); ?>">
                    The Future of Luxury
                </a>
                <?php if ( $cat_name && ! is_wp_error( $cat_url ) ) : ?>
                <span aria-hidden="true">/</span>
                <a href="<?php echo esc_url( $cat_url ); ?>"><?php echo esc_html( $cat_name ); ?></a>
                <?php endif; ?>
            </nav>

            <header class="article-hero__header">
                <h1 class="article-hero__title" id="article-hero-title">
                    <?php the_title(); ?>
                </h1>
                <?php if ( $standfirst ) : ?>
                <p class="article-hero__standfirst"><?php echo esc_html( $standfirst ); ?></p>
                <?php endif; ?>
            </header>

            <div class="article-meta" role="contentinfo" aria-label="Article information">
                <?php if ( $author_name ) : ?>
                <span class="article-meta__author"><?php echo esc_html( $author_name ); ?></span>
                <?php endif; ?>
                <time class="article-meta__date" datetime="<?php echo esc_attr( $pub_iso ); ?>">
                    <?php echo esc_html( $pub_date ); ?>
                </time>
                <?php if ( $read_time ) : ?>
                <span class="article-meta__read-time">
                    <?php echo absint( $read_time ); ?> min read
                </span>
                <?php endif; ?>
                <?php if ( $cornerstone ) : ?>
                <span class="article-meta__cornerstone">Essential Reading</span>
                <?php endif; ?>
            </div>

        </div>

        <?php if ( has_post_thumbnail() ) : ?>
        <figure class="article-hero__media">
            <div class="container container--wide">
                <?php the_post_thumbnail( 'full', [
                    'loading' => 'eager',
                    'alt'     => esc_attr( get_the_title() ),
                ] ); ?>
            </div>
        </figure>
        <?php endif; ?>

    </section>

    <!-- ── 3. Article body ──────────────────────────────────────────────── -->
    <article class="article-body" aria-labelledby="article-hero-title">
        <div class="container container--narrow">
            <div class="wysiwyg wysiwyg--editorial">
                <?php
                /*
                 * the_content() is the correct output function for post body content.
                 * WordPress runs it through wp_kses_post internally via the content
                 * filter pipeline. Do not add a second wp_kses_post wrapper here.
                 */
                the_content();
                ?>
            </div>
        </div>
    </article>

    <!-- ── 4. Author module ─────────────────────────────────────────────── -->
    <?php if ( $author_name ) : ?>
    <section class="author-module" aria-labelledby="author-module-heading">
        <div class="container container--narrow">
            <div class="author-module__inner">

                <?php $avatar = get_avatar( $author_id, 120, '', $author_name, [ 'class' => 'author-module__avatar' ] );
                if ( $avatar ) : ?>
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

    <!-- ── 5. Related articles ──────────────────────────────────────────── -->
    <?php if ( ! empty( $related_articles ) ) :
        set_query_var( 'summit_related_articles', $related_articles );
        set_query_var( 'summit_related_heading',  'Related Reading' );
        get_template_part( 'parts/related/articles' );
    endif; ?>

    <!-- ── 6. Related episode ───────────────────────────────────────────── -->
    <?php if ( $related_episode instanceof WP_Post ) :
        set_query_var( 'summit_related_episodes', $related_episode );
        set_query_var( 'summit_related_heading',  'Listen: Related Episode' );
        get_template_part( 'parts/related/episodes' );
    endif; ?>

    <!-- ── 7. Subscribe ─────────────────────────────────────────────────── -->
    <?php if ( $subscribe_on !== false ) :
        get_template_part( 'parts/global/subscribe' );
    endif; ?>

</main>

<?php
endwhile;
get_footer();
