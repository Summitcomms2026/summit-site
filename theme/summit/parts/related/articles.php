<?php
/**
 * Part: Related Articles
 * Location: parts/related/articles.php
 *
 * Renders related article cards. Accepts a WP_Post array via set_query_var.
 *
 * Usage:
 *   set_query_var( 'summit_related_articles', get_field('cs_related_article') );
 *   set_query_var( 'summit_related_heading', 'Related Thinking' ); // optional
 *   get_template_part( 'parts/related/articles' );
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$articles = get_query_var( 'summit_related_articles', [] );
$heading  = get_query_var( 'summit_related_heading',  'Related Reading' );

if ( empty( $articles ) || ! is_array( $articles ) ) {
    return;
}

// Normalise: single WP_Post (post_object field) → array
if ( $articles instanceof WP_Post ) {
    $articles = [ $articles ];
}
$articles = array_filter( $articles, fn( $p ) => $p instanceof WP_Post );

if ( empty( $articles ) ) {
    return;
}
?>

<section class="related-reading" aria-labelledby="related-reading-heading">
    <div class="container container--wide">

        <header class="related-reading__header">
            <h2 class="related-reading__heading" id="related-reading-heading">
                <?php echo esc_html( $heading ); ?>
            </h2>
        </header>

        <ul class="article-grid article-grid--related" role="list">
            <?php foreach ( $articles as $art ) :
                $standfirst = get_field( 'art_standfirst', $art->ID );
                $read_time  = get_field( 'art_read_time',  $art->ID );
                $cats       = get_the_terms( $art->ID, 'article_category' );
                $cat_name   = ( ! is_wp_error( $cats ) && ! empty( $cats ) ) ? $cats[0]->name : '';
                $thumb      = get_the_post_thumbnail( $art->ID, 'medium_large' );
                $date_disp  = get_the_date( 'j F Y', $art->ID );
                $date_iso   = get_the_date( 'c',     $art->ID );
            ?>
            <li class="article-card">
                <a href="<?php echo esc_url( get_permalink( $art->ID ) ); ?>"
                   class="article-card__link"
                   aria-label="<?php echo esc_attr( 'Read: ' . get_the_title( $art->ID ) ); ?>">

                    <?php if ( $thumb ) : ?>
                    <figure class="article-card__media" aria-hidden="true">
                        <?php echo $thumb; ?>
                    </figure>
                    <?php endif; ?>

                    <div class="article-card__body">
                        <div class="article-card__meta">
                            <?php if ( $cat_name ) : ?>
                            <span class="article-card__category"><?php echo esc_html( $cat_name ); ?></span>
                            <?php endif; ?>
                            <time class="article-card__date"
                                  datetime="<?php echo esc_attr( $date_iso ); ?>">
                                <?php echo esc_html( $date_disp ); ?>
                            </time>
                            <?php if ( $read_time ) : ?>
                            <span class="article-card__read-time">
                                <?php echo absint( $read_time ); ?> min read
                            </span>
                            <?php endif; ?>
                        </div>

                        <h3 class="article-card__title">
                            <?php echo esc_html( get_the_title( $art->ID ) ); ?>
                        </h3>

                        <?php if ( $standfirst ) : ?>
                        <p class="article-card__standfirst">
                            <?php echo esc_html( $standfirst ); ?>
                        </p>
                        <?php endif; ?>

                        <span class="article-card__cta" aria-hidden="true">Read Article</span>
                    </div>

                </a>
            </li>
            <?php endforeach; ?>
        </ul>

    </div>
</section>
