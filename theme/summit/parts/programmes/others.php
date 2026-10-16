<?php
/**
 * Part: Programmes — the other programmes
 * Location: parts/programmes/others.php
 * Used by: page-programmes.php on programme pages
 *
 * The other programmes as a quiet numbered register, keeping each
 * programme's number, and the way back to the Overview. From the
 * navigation config.
 *
 * $args:
 *   section   the Programmes section from summit_nav( 'primary' )
 *   path      the current page path
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$section    = $args['section'] ?? [];
$path       = $args['path'] ?? '';
$programmes = array_values( $section['children'] ?? [] );

if ( count( $programmes ) < 2 ) {
    return;
}
?>
<nav class="prg-others" aria-label="<?php esc_attr_e( 'Other programmes', 'summit' ); ?>">
    <div class="container container--medium">
        <p class="prg-others__heading" data-reveal="fade">
            <a href="<?php echo esc_url( summit_nav_url( $section ) ); ?>"><?php echo esc_html( $section['label'] ); ?></a>
            <span aria-hidden="true">/</span>
            <span><?php esc_html_e( 'Other programmes', 'summit' ); ?></span>
        </p>
        <ol class="prg-others__list" role="list" data-reveal="fade">
            <?php foreach ( $programmes as $i => $item ) : if ( $item['path'] === $path ) { continue; } ?>
            <li>
                <a class="prg-others__link" href="<?php echo esc_url( summit_nav_url( $item ) ); ?>">
                    <span class="prg-others__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
                    <span class="prg-others__label"><?php echo esc_html( $item['label'] ); ?></span>
                    <span class="prg-others__arrow" aria-hidden="true">&rarr;</span>
                </a>
            </li>
            <?php endforeach; ?>
        </ol>
    </div>
</nav>
