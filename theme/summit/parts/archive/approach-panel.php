<?php
/**
 * Part: Approach Panel
 * Location: parts/archive/approach-panel.php
 *
 * Strategic framing text block for archive landing pages.
 * Renders a heading and rich text body in a narrow container.
 *
 * Usage:
 *   set_query_var( 'summit_approach_heading', 'How We Approach Work' );
 *   set_query_var( 'summit_approach_body', get_field('work_approach_body') );
 *   get_template_part( 'parts/archive/approach-panel' );
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$heading = get_query_var( 'summit_approach_heading', '' );
$body    = get_query_var( 'summit_approach_body', '' );

if ( empty( $heading ) && empty( $body ) ) {
	return;
}
?>

<section class="approach-panel" aria-labelledby="approach-panel-heading">
    <div class="container container--narrow">

        <?php if ( $heading ) : ?>
        <h2 class="approach-panel__heading" id="approach-panel-heading">
            <?php echo esc_html( $heading ); ?>
        </h2>
        <?php endif; ?>

        <?php if ( $body ) : ?>
        <div class="approach-panel__body rich-text">
            <?php echo wp_kses_post( $body ); ?>
        </div>
        <?php endif; ?>

    </div>
</section>
