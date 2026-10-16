<?php
/**
 * Part: About — the way on through the section
 * Location: parts/about/next.php
 * Used by: page-about.php on About pages
 *
 * The next page in the section's reading order (wrapping to the first),
 * set large, with the way back to the section's overview beside it.
 *
 * $args:
 *   section   the About section from summit_nav( 'primary' )
 *   path      the current page path
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$section = $args['section'] ?? [];
$pages   = array_values( $section['children'] ?? [] );
$paths   = array_column( $pages, 'path' );
$here    = array_search( $args['path'] ?? '', $paths, true );
if ( false === $here || count( $pages ) < 2 ) {
    return;
}
$next    = $pages[ ( $here + 1 ) % count( $pages ) ];
$page    = get_page_by_path( trim( $next['path'], '/' ), OBJECT, 'page' );
$summary = $page ? (string) get_post_meta( $page->ID, 'summit_summary', true ) : '';
?>
<nav class="ab-next" aria-label="<?php esc_attr_e( 'Continue through About', 'summit' ); ?>">
    <div class="ab-next__inner">
        <p class="ab-next__label" data-reveal="fade">
            <a href="<?php echo esc_url( summit_nav_url( $section ) ); ?>"><?php echo esc_html( $section['label'] ?? '' ); ?></a>
            <span aria-hidden="true">/</span>
            <span><?php esc_html_e( 'Next', 'summit' ); ?></span>
        </p>
        <a class="ab-next__link" href="<?php echo esc_url( summit_nav_url( $next ) ); ?>" data-reveal>
            <span class="ab-next__title"><?php echo esc_html( $next['label'] ); ?></span>
            <?php if ( $summary ) : ?>
            <span class="ab-next__summary"><?php echo esc_html( $summary ); ?></span>
            <?php endif; ?>
            <span class="ab-next__arrow" aria-hidden="true">&rarr;</span>
        </a>
    </div>
</nav>
