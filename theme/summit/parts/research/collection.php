<?php
/**
 * Part: Research — the rest of the collection
 * Location: parts/research/collection.php
 * Used by: page-research.php on Research pages
 *
 * The other works in the collection, as a catalogue register: shelf
 * number, title and each page's one-line summary (summit_summary), then
 * the way back to Research. From the navigation config.
 *
 * $args:
 *   section   the Research section from summit_nav( 'primary' )
 *   path      the current page path
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$section = $args['section'] ?? [];
$path    = $args['path'] ?? '';
$works   = array_values( $section['children'] ?? [] );

if ( count( $works ) < 2 ) {
    return;
}
?>
<nav class="res-collection" aria-label="<?php esc_attr_e( 'The rest of the collection', 'summit' ); ?>">
    <div class="container container--medium">
        <p class="res-collection__heading" data-reveal="fade">
            <a href="<?php echo esc_url( summit_nav_url( $section ) ); ?>"><?php echo esc_html( $section['label'] ); ?></a>
            <span aria-hidden="true">/</span>
            <span><?php esc_html_e( 'Also in the collection', 'summit' ); ?></span>
        </p>
        <ol class="res-collection__list" role="list" data-reveal="fade">
            <?php foreach ( $works as $i => $item ) :
                if ( $item['path'] === $path ) {
                    continue;
                }
                $page    = get_page_by_path( trim( $item['path'], '/' ), OBJECT, 'page' );
                $summary = $page ? (string) get_post_meta( $page->ID, 'summit_summary', true ) : ''; ?>
            <li>
                <a class="res-collection__link" href="<?php echo esc_url( summit_nav_url( $item ) ); ?>">
                    <span class="res-collection__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
                    <span class="res-collection__title"><?php echo esc_html( $item['label'] ); ?></span>
                    <?php if ( $summary ) : ?>
                    <span class="res-collection__summary"><?php echo esc_html( $summary ); ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ol>
    </div>
</nav>
