<?php
/**
 * Part: Observatory sequence
 * Location: parts/observatory/sequence.php
 * Used by: page-observatory.php (chapter pages, not the Overview)
 *
 * The section read as a publication: previous and next chapter, then the
 * whole reading order with the current chapter marked. The order and
 * labels are the navigation config's (inc/navigation-config.php).
 *
 * $args:
 *   section   the Observatory section from summit_nav( 'primary' )
 *   path      the current page path
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$section  = $args['section'] ?? [];
$path     = $args['path'] ?? '';
$chapters = array_values( $section['children'] ?? [] );
$index    = array_search( $path, array_column( $chapters, 'path' ), true );

if ( false === $index ) {
    return;
}

$prev = $chapters[ $index - 1 ] ?? null;
$next = $chapters[ $index + 1 ] ?? null;
?>
<nav class="obs-sequence" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: section name */ __( '%s: reading order', 'summit' ), $section['label'] ) ); ?>">
    <div class="container container--medium">

        <p class="obs-sequence__heading" data-reveal="fade"><?php echo esc_html( $section['label'] ); ?></p>

        <div class="obs-sequence__pager" data-reveal="fade">
            <?php if ( $prev ) : ?>
            <a class="obs-sequence__step obs-sequence__step--prev" href="<?php echo esc_url( summit_nav_url( $prev ) ); ?>" rel="prev">
                <span class="obs-sequence__dir"><span aria-hidden="true">&larr;</span> <?php esc_html_e( 'Previous', 'summit' ); ?></span>
                <span class="obs-sequence__label"><?php echo esc_html( $prev['label'] ); ?></span>
            </a>
            <?php endif; ?>
            <?php if ( $next ) : ?>
            <a class="obs-sequence__step obs-sequence__step--next" href="<?php echo esc_url( summit_nav_url( $next ) ); ?>" rel="next">
                <span class="obs-sequence__dir"><?php esc_html_e( 'Next', 'summit' ); ?> <span aria-hidden="true">&rarr;</span></span>
                <span class="obs-sequence__label"><?php echo esc_html( $next['label'] ); ?></span>
            </a>
            <?php endif; ?>
        </div>

        <ol class="obs-sequence__list" role="list">
            <?php foreach ( $chapters as $i => $chapter ) : ?>
            <li>
                <?php if ( $i === $index ) : ?>
                <a class="obs-sequence__item is-current" href="<?php echo esc_url( summit_nav_url( $chapter ) ); ?>" aria-current="page">
                <?php else : ?>
                <a class="obs-sequence__item" href="<?php echo esc_url( summit_nav_url( $chapter ) ); ?>">
                <?php endif; ?>
                    <span class="obs-sequence__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $i ) ); ?></span>
                    <span><?php echo esc_html( $chapter['label'] ); ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ol>

    </div>
</nav>
