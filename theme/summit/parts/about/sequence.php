<?php
/**
 * Part: About — the section's editorial index
 * Location: parts/about/sequence.php
 * Used by: page-about.php via [summit_about_sequence]
 *
 * The About pages in reading order, as a numbered sequence: numeral,
 * title and each page's one line (summit_summary). From the navigation
 * config, so the sequence follows the IA.
 *
 * $args:
 *   section   the About section from summit_nav( 'primary' )
 *   path      the current page path
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$pages = array_values( array_filter(
    $args['section']['children'] ?? [],
    static fn( $c ) => ( $c['path'] ?? '' ) !== ( $args['path'] ?? '' )
) );
if ( ! $pages ) {
    return;
}
?>
<ol class="ab-sequence" role="list">
    <?php foreach ( $pages as $i => $item ) :
        $page    = get_page_by_path( trim( $item['path'], '/' ), OBJECT, 'page' );
        $summary = $page ? (string) get_post_meta( $page->ID, 'summit_summary', true ) : ''; ?>
    <li class="ab-sequence__item" data-reveal data-reveal-delay="<?php echo esc_attr( (string) min( 4, $i ) ); ?>">
        <a class="ab-sequence__link" href="<?php echo esc_url( summit_nav_url( $item ) ); ?>">
            <span class="ab-sequence__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
            <span class="ab-sequence__title"><?php echo esc_html( $item['label'] ); ?></span>
            <?php if ( $summary ) : ?>
            <span class="ab-sequence__summary"><?php echo esc_html( $summary ); ?></span>
            <?php endif; ?>
            <span class="ab-sequence__arrow" aria-hidden="true">&rarr;</span>
        </a>
    </li>
    <?php endforeach; ?>
</ol>
