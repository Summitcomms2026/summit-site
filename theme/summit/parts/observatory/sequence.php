<?php
/**
 * Part: Observatory sequence
 * Location: parts/observatory/sequence.php
 * Used by: page-observatory.php (chapter pages, not the Overview)
 *
 * The section as one quiet directory: its name, then its pages in reading
 * order with the current one marked (Design Pass 09: no competing previous
 * and next titles above it). The order and labels are the navigation
 * config's (inc/navigation-config.php).
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

?>
<nav class="obs-sequence" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: section name */ __( '%s: reading order', 'summit' ), $section['label'] ) ); ?>">
    <div class="container container--medium">

        <p class="obs-sequence__heading" data-reveal="fade"><?php echo esc_html( $section['label'] ); ?></p>

        <ol class="obs-sequence__list" role="list">
            <?php foreach ( $chapters as $i => $chapter ) : ?>
            <li>
                <?php if ( $i === $index ) : ?>
                <a class="obs-sequence__item is-current" href="<?php echo esc_url( summit_nav_url( $chapter ) ); ?>" aria-current="page">
                <?php else : ?>
                <a class="obs-sequence__item" href="<?php echo esc_url( summit_nav_url( $chapter ) ); ?>">
                <?php endif; ?>
                    <span><?php echo esc_html( $chapter['label'] ); ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ol>

    </div>
</nav>
