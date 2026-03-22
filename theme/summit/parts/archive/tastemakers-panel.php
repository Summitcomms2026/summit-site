<?php
/**
 * Part: Tastemakers Panel
 * Location: parts/archive/tastemakers-panel.php
 *
 * Cross-promotion panel linking editorial archives to the Tastemakers
 * podcast property. Renders season artwork, label, title, thesis excerpt,
 * and CTAs to the season page and /tastemakers.
 *
 * Usage:
 *   set_query_var( 'summit_tm_season', $season_post );
 *   set_query_var( 'summit_tm_heading', 'From Tastemakers' ); // optional
 *   get_template_part( 'parts/archive/tastemakers-panel' );
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$season  = get_query_var( 'summit_tm_season', null );
$heading = get_query_var( 'summit_tm_heading', 'From Tastemakers' );

if ( ! $season instanceof WP_Post ) {
	return;
}

$season_number = get_field( 'ps_season_number', $season->ID );
$subtitle      = get_field( 'ps_subtitle',       $season->ID );
$thesis        = get_field( 'ps_thesis',          $season->ID );
$artwork       = get_field( 'ps_artwork',         $season->ID );
$season_link   = get_permalink( $season->ID );
$season_title  = get_the_title( $season->ID );

// Truncate thesis to ~200 characters at a word boundary.
$thesis_excerpt = '';
if ( $thesis ) {
	$thesis_plain = wp_strip_all_tags( $thesis );
	if ( mb_strlen( $thesis_plain ) > 200 ) {
		$thesis_excerpt = mb_substr( $thesis_plain, 0, 200 );
		$thesis_excerpt = preg_replace( '/\s+\S*$/', '', $thesis_excerpt ) . '…';
	} else {
		$thesis_excerpt = $thesis_plain;
	}
}

$season_label = $season_number ? 'Season ' . absint( $season_number ) : '';
?>

<section class="tastemakers-panel" aria-labelledby="tastemakers-panel-heading">
    <div class="container container--wide">
        <div class="tastemakers-panel__inner">

            <?php if ( $artwork && ! empty( $artwork['url'] ) ) : ?>
            <figure class="tastemakers-panel__media" aria-hidden="true">
                <img src="<?php echo esc_url( $artwork['sizes']['medium_large'] ?? $artwork['url'] ); ?>"
                     alt="<?php echo esc_attr( $artwork['alt'] ?: $season_title ); ?>"
                     width="<?php echo esc_attr( $artwork['sizes']['medium_large-width'] ?? $artwork['width'] ); ?>"
                     height="<?php echo esc_attr( $artwork['sizes']['medium_large-height'] ?? $artwork['height'] ); ?>"
                     loading="lazy">
            </figure>
            <?php endif; ?>

            <div class="tastemakers-panel__body">
                <p class="tastemakers-panel__eyebrow"><?php echo esc_html( $heading ); ?></p>

                <?php if ( $season_label ) : ?>
                <p class="tastemakers-panel__season-label"><?php echo esc_html( $season_label ); ?></p>
                <?php endif; ?>

                <h2 class="tastemakers-panel__heading" id="tastemakers-panel-heading">
                    <?php echo esc_html( $subtitle ?: $season_title ); ?>
                </h2>

                <?php if ( $thesis_excerpt ) : ?>
                <p class="tastemakers-panel__excerpt">
                    <?php echo esc_html( $thesis_excerpt ); ?>
                </p>
                <?php endif; ?>

                <div class="tastemakers-panel__actions">
                    <a href="<?php echo esc_url( $season_link ); ?>"
                       class="btn btn--primary">
                        Explore This Season
                    </a>
                    <a href="<?php echo esc_url( home_url( '/tastemakers' ) ); ?>"
                       class="btn btn--secondary">
                        All Episodes
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>
