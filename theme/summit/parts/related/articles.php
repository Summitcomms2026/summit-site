<?php
/**
 * Part: Related Articles
 * Location: parts/related/articles.php
 *
 * Renders a related article grid from a supplied array of WP_Post objects.
 * Guard: suppresses output if no posts provided.
 *
 * Usage:
 *   get_template_part( 'parts/related/articles', null, [
 *       'posts'    => $related_articles,
 *       'headline' => 'Related Reading',
 *   ] );
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$posts    = $args['posts']    ?? [];
$headline = $args['headline'] ?? 'Related Reading';

// Normalise: ACF post_object fields may return a single post, not an array.
if ( $posts instanceof WP_Post ) {
    $posts = [ $posts ];
}

if ( empty( $posts ) ) {
    return;
}
?>

<section class="related-section section--padded" aria-labelledby="related-articles-heading">
    <div class="container container--wide">

        <h2 class="related-section__headline" id="related-articles-heading">
            <?php echo esc_html( $headline ); ?>
        </h2>

        <ul class="related-grid" role="list">
            <?php foreach ( $posts as $article ) :
                $art_id     = $article->ID;
                $standfirst = get_field( 'art_standfirst', $art_id );
                $read_time  = get_field( 'art_read_time', $art_id );
                $cat_terms  = get_the_terms( $art_id, 'article_category' );
                $cat_label  = ( ! is_wp_error( $cat_terms ) && ! empty( $cat_terms ) )
                              ? $cat_terms[0]->name : '';
            ?>
            <li class="article-card">
                <a href="<?php echo esc_url( get_permalink( $art_id ) ); ?>"
                   class="article-card__link"
                   aria-label="<?php echo esc_attr( 'Read: ' . get_the_title( $art_id ) ); ?>">

                    <?php if ( summit_has_article_hero( $art_id ) ) : ?>
                    <figure class="article-card__media" aria-hidden="true">
                        <?php echo summit_article_hero( $art_id, 'medium_large' ); ?>
                    </figure>
                    <?php endif; ?>

                    <div class="article-card__body">
                        <?php if ( $cat_label ) : ?>
                        <span class="article-card__eyebrow"><?php echo esc_html( $cat_label ); ?></span>
                        <?php endif; ?>

                        <h3 class="article-card__title"><?php echo esc_html( get_the_title( $art_id ) ); ?></h3>

                        <?php if ( $standfirst ) : ?>
                        <p class="article-card__standfirst"><?php echo esc_html( $standfirst ); ?></p>
                        <?php endif; ?>

                        <div class="article-card__meta">
                            <time datetime="<?php echo esc_attr( get_the_date( 'c', $art_id ) ); ?>">
                                <?php echo esc_html( get_the_date( 'j F Y', $art_id ) ); ?>
                            </time>
                            <?php if ( $read_time ) : ?>
                            <span><?php echo esc_html( $read_time ); ?> min read</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>

    </div>
</section>
