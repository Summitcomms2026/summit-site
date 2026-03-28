<?php
/**
 * Part: Sector Signals
 * Location: parts/archive/sector-signals.php
 *
 * Logo/sector adjacency grid for the Work Showcase archive landing.
 * Renders a grid of client or sector logos from an ACF repeater field.
 *
 * Usage:
 *   set_query_var( 'summit_sector_logos', get_field('work_sector_logos') );
 *   get_template_part( 'parts/archive/sector-signals' );
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$logos = get_query_var( 'summit_sector_logos', [] );

if ( empty( $logos ) || ! is_array( $logos ) ) {
	return;
}
?>

<section class="sector-signals" aria-labelledby="sector-signals-heading">
    <div class="container container--wide">

        <h2 class="sector-signals__heading" id="sector-signals-heading">
            Sectors We Serve
        </h2>

        <ul class="sector-signals__grid" role="list">
            <?php foreach ( $logos as $row ) :
                $image = $row['logo_image'] ?? null;
                $label = $row['logo_label'] ?? '';
                if ( ! $image || empty( $image['url'] ) ) { continue; }
            ?>
            <li class="sector-signals__item">
                <img src="<?php echo esc_url( $image['sizes']['thumbnail'] ?? $image['url'] ); ?>"
                     alt="<?php echo esc_attr( $image['alt'] ?: $label ); ?>"
                     width="<?php echo esc_attr( $image['sizes']['thumbnail-width'] ?? $image['width'] ); ?>"
                     height="<?php echo esc_attr( $image['sizes']['thumbnail-height'] ?? $image['height'] ); ?>"
                     loading="lazy"
                     class="sector-signals__logo">
            </li>
            <?php endforeach; ?>
        </ul>

    </div>
</section>
