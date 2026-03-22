<?php
/**
 * Part: Showreel Panel
 * Location: parts/archive/showreel-panel.php
 *
 * Responsive video/showreel embed block for archive landing pages.
 * Renders an oembed in a 16:9 responsive wrapper with optional caption.
 *
 * Usage:
 *   set_query_var( 'summit_showreel_embed', get_field('work_showreel_embed') );
 *   set_query_var( 'summit_showreel_caption', 'Summit Showreel 2025' ); // optional
 *   get_template_part( 'parts/archive/showreel-panel' );
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$embed   = get_query_var( 'summit_showreel_embed', '' );
$caption = get_query_var( 'summit_showreel_caption', '' );

if ( empty( $embed ) ) {
	return;
}
?>

<section class="showreel-panel" aria-label="Showreel">
    <div class="container container--wide">
        <div class="showreel-panel__embed-wrap">
            <?php echo $embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- oembed output from ACF ?>
        </div>
        <?php if ( $caption ) : ?>
        <p class="showreel-panel__caption"><?php echo esc_html( $caption ); ?></p>
        <?php endif; ?>
    </div>
</section>
