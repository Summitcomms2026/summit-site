<?php
/**
 * Part: The Future of Luxury — further reading
 * Location: parts/fol/related.php
 * Used by: single-article.php
 *
 * Two or three published essays (summit_fol_related(): the editors'
 * art_related_articles first, then the same subject, then the newest).
 *
 * $args:
 *   post_id   the essay being read
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$related = summit_fol_related( (int) ( $args['post_id'] ?? 0 ), 3 );
if ( count( $related ) < 2 ) {
    return;
}
?>
<section class="fol-panel fol-related" aria-labelledby="fol-related-heading">
    <div class="fol-wrap">
        <header class="fol-panel__head" data-reveal="fade">
            <h2 class="fol-panel__title" id="fol-related-heading"><?php esc_html_e( 'Further reading', 'summit' ); ?></h2>
        </header>
        <div class="fol-related__grid">
            <?php foreach ( $related as $i => $item ) : ?>
            <div data-reveal data-reveal-delay="<?php echo esc_attr( (string) $i ); ?>">
                <?php get_template_part( 'parts/fol/story', null, [ 'post' => $item, 'variant' => 'feature' ] ); ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
