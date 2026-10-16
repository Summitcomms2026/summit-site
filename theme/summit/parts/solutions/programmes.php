<?php
/**
 * Part: Solutions — the programme rows
 * Location: parts/solutions/programmes.php
 * Used by: page-solutions.php via [summit_solutions_programmes]
 *
 * The Programmes as numbered rows, drawn from the navigation config. A
 * programme page's "summit_summary" field, where set, adds its line.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$programmes = summit_nav( 'primary' )['programmes']['children'] ?? [];

if ( ! $programmes ) {
    return;
}
?>
<ul class="sx-rows" role="list">
    <?php foreach ( array_values( $programmes ) as $i => $item ) :
        $page    = get_page_by_path( trim( $item['path'], '/' ), OBJECT, 'page' );
        $summary = $page ? (string) get_post_meta( $page->ID, 'summit_summary', true ) : ''; ?>
    <li>
        <a class="sx-row" href="<?php echo esc_url( summit_nav_url( $item ) ); ?>">
            <span class="sx-num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
            <span class="sx-row__name"><?php echo esc_html( $item['label'] ); ?></span>
            <span class="sx-arrow" aria-hidden="true">&rarr;</span>
            <?php if ( $summary ) : ?>
            <span class="sx-row__line"><?php echo esc_html( $summary ); ?></span>
            <?php endif; ?>
        </a>
    </li>
    <?php endforeach; ?>
</ul>
