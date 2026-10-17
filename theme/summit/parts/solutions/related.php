<?php
/**
 * Part: Solutions — the rest of this lens
 * Location: parts/solutions/related.php
 * Used by: page-solutions.php on Industry, Function and Need pages
 *
 * The other entries of the page's lens (e.g. "Also by need"), and the way
 * back to all three ways in. Drawn from the navigation config.
 *
 * $args:
 *   section   the Solutions section from summit_nav( 'primary' )
 *   lens      the page's group (label + children)
 *   path      the current page path
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$section = $args['section'] ?? [];
$lens    = $args['lens'] ?? [];
$path    = $args['path'] ?? '';
$items   = array_values( array_filter( $lens['children'] ?? [], static fn( $c ) => $c['path'] !== $path ) );

if ( ! $items ) {
    return;
}

/* translators: %s: lens name, e.g. "by need" */
$heading = sprintf( __( 'Also %s', 'summit' ), lcfirst( $lens['label'] ) );
?>
<nav class="sol-related" aria-label="<?php echo esc_attr( $heading ); ?>">
    <div class="container container--medium">
        <p class="sol-related__heading" data-reveal="fade">
            <a href="<?php echo esc_url( summit_nav_url( $section ) ); ?>"><?php echo esc_html( $section['label'] ); ?></a>
            <span aria-hidden="true">/</span>
            <span><?php echo esc_html( $heading ); ?></span>
        </p>
        <ul class="sol-related__list" role="list" data-reveal="fade">
            <?php foreach ( $items as $item ) : ?>
            <li>
                <a class="sol-related__link" href="<?php echo esc_url( summit_nav_url( $item ) ); ?>">
                    <span><?php echo esc_html( $item['label'] ); ?></span>
                    <span class="sol-related__arrow" aria-hidden="true">&rarr;</span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <p class="sol-related__all" data-reveal="fade">
            <a href="<?php echo esc_url( summit_nav_url( $section ) . '#sx-ways-heading' ); ?>"><?php esc_html_e( 'All three ways in', 'summit' ); ?> <span aria-hidden="true">&rarr;</span></a>
        </p>
    </div>
</nav>
