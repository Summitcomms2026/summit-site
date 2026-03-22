<?php
/**
 * Part: Showreel Block
 * Location: parts/blocks/showreel.php
 *
 * Renders a cinematic video embed section. Uses ACF oEmbed field for
 * provider-agnostic video embedding (YouTube, Vimeo, etc.).
 *
 * Guard: section is not rendered when no embed URL is provided.
 *
 * ACF fields (from front page):
 *   home_showreel_headline text   optional
 *   home_showreel_body     textarea optional
 *   home_showreel_embed    oembed optional — guard field
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$embed = get_field( 'home_showreel_embed' );

// Guard — do not render when no video is available.
if ( empty( $embed ) ) {
    return;
}

$headline = get_field( 'home_showreel_headline' ) ?: 'See the Work';
$body     = get_field( 'home_showreel_body' );
?>

<section class="showreel-block section--dark section--padded" aria-labelledby="showreel-heading">
    <div class="container container--wide">

        <header class="showreel-block__header">
            <h2 class="showreel-block__headline" id="showreel-heading">
                <?php echo esc_html( $headline ); ?>
            </h2>
            <?php if ( $body ) : ?>
            <div class="showreel-block__body">
                <?php echo wp_kses_post( wpautop( $body ) ); ?>
            </div>
            <?php endif; ?>
        </header>

        <div class="showreel-block__embed">
            <?php echo $embed; // ACF oEmbed returns the full iframe markup ?>
        </div>

    </div>
</section>
