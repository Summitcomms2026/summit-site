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
            <h2 class="fol-panel__title" id="fol-related-heading"><?php esc_html_e( 'Related Reading', 'summit' ); ?></h2>
        </header>
        <div class="fol-related__grid">
            <?php foreach ( $related as $i => $item ) : ?>
            <div data-reveal data-reveal-delay="<?php echo esc_attr( (string) $i ); ?>">
                <?php get_template_part( 'parts/fol/story', null, [ 'post' => $item, 'variant' => 'feature' ] ); ?>
            </div>
            <?php endforeach; ?>
        </div>

        <?php // Related Work (S09-P019): the conversation the essay may prompt ?>
        <div class="fol-related__work" data-reveal="fade">
            <p class="fol-panel__label"><?php esc_html_e( 'Related Work', 'summit' ); ?></p>
            <p class="fol-related__line"><?php esc_html_e( 'If the essay raises a question your organisation is facing, a related Summit capability may be useful. Explore the work or speak to us.', 'summit' ); ?></p>
            <p class="fol-related__links">
                <a href="<?php echo esc_url( home_url( '/solutions' ) ); ?>"><?php esc_html_e( 'Explore Solutions', 'summit' ); ?> <span aria-hidden="true">&rarr;</span></a>
                <a href="<?php echo esc_url( add_query_arg( [ 'topic' => 'the-future-of-luxury', 'context' => rawurlencode( wp_strip_all_tags( html_entity_decode( get_the_title( (int) ( $args['post_id'] ?? 0 ) ), ENT_QUOTES, 'UTF-8' ) ) ) ], home_url( '/lets-talk' ) ) ); ?>"><?php esc_html_e( 'Discuss the Question', 'summit' ); ?> <span aria-hidden="true">&rarr;</span></a>
            </p>
        </div>
    </div>
</section>
