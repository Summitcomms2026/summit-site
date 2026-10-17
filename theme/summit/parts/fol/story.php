<?php
/**
 * Part: The Future of Luxury — a story
 * Location: parts/fol/story.php
 * Used by: archive-article.php, taxonomy-article_category.php, parts/fol/related.php
 *
 * One article as the archive presents it. The variant sets the hierarchy:
 *
 *   lead     the issue's lead: display headline, standfirst, byline; the
 *            image beside it when one exists, otherwise a purely
 *            typographic lead across the full measure
 *   feature  a large teaser: image (if any), category, headline, standfirst
 *   item     a compact teaser on a hairline: category, headline, meta
 *
 * Lead and feature stories carry the essay's visual: its photograph when
 * the file exists, otherwise its editorial plate (summit_fol_visual()).
 * Items stay typographic.
 *
 * $args:
 *   post      WP_Post
 *   variant   'lead' | 'feature' | 'item' (default 'item')
 *   level     heading level, 2–4 (default 3)
 *   category  show the category (default true)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$story = $args['post'] ?? null;
if ( ! $story instanceof WP_Post ) {
    return;
}

$variant  = in_array( $args['variant'] ?? '', [ 'lead', 'feature', 'item' ], true ) ? $args['variant'] : 'item';
$level    = min( 4, max( 2, (int) ( $args['level'] ?? 3 ) ) );
$id       = $story->ID;
$url      = get_permalink( $id );
$title    = get_the_title( $id );
$category = ( $args['category'] ?? true ) ? summit_fol_category( $id ) : null;
$author   = summit_fol_author( $id );
$minutes  = summit_fol_read_time( $id );
$stand    = 'item' === $variant ? '' : summit_fol_standfirst( $id );
$image    = 'item' === $variant ? '' : summit_fol_visual( $id, 'lead' === $variant ? 'large' : 'medium_large', [ 'sizes' => 'lead' === $variant ? '(min-width: 1024px) 55vw, 100vw' : '(min-width: 1024px) 40vw, 100vw', 'loading' => 'lead' === $variant ? 'eager' : 'lazy' ] );
$heading  = 'h' . $level;

$classes = 'fol-story fol-story--' . $variant . ( $image ? ' has-image' : ' is-set' );
?>
<article class="<?php echo esc_attr( $classes ); ?>">
    <?php if ( $image ) : ?>
    <a class="fol-story__media" href="<?php echo esc_url( $url ); ?>" tabindex="-1" aria-hidden="true">
        <?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput -- wp_get_attachment_image ?>
    </a>
    <?php endif; ?>

    <div class="fol-story__text">
        <?php if ( $category ) : ?>
        <p class="fol-story__kicker"><?php echo esc_html( $category->name ); ?></p>
        <?php endif; ?>

        <<?php echo $heading; // phpcs:ignore WordPress.Security.EscapeOutput -- fixed tag ?> class="fol-story__title">
            <a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a>
        </<?php echo $heading; // phpcs:ignore WordPress.Security.EscapeOutput -- fixed tag ?>>

        <?php if ( $stand ) : ?>
        <p class="fol-story__standfirst"><?php echo esc_html( $stand ); ?></p>
        <?php endif; ?>

        <p class="fol-story__meta">
            <?php if ( $author && 'item' !== $variant ) : ?>
            <span><?php echo esc_html( $author ); ?></span>
            <?php endif; ?>
            <time datetime="<?php echo esc_attr( get_the_date( 'c', $id ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y', $id ) ); ?></time>
            <?php if ( $minutes ) : ?>
            <span><?php
                /* translators: %d: minutes */
                echo esc_html( sprintf( __( '%d min read', 'summit' ), $minutes ) );
            ?></span>
            <?php endif; ?>
        </p>
    </div>
</article>
