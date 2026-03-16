<?php
/**
 * Part: CTA Footer Block
 * Location: parts/global/cta-footer.php
 *
 * Reusable closing CTA panel rendered at the foot of single and page
 * templates. Caller passes context via set_query_var() before calling
 * get_template_part().
 *
 * Variables (all optional — defaults shown):
 *   summit_cta_headline  string  'Start a Conversation'
 *   summit_cta_sub       string  Supporting sentence
 *   summit_cta_label     string  'Design Tomorrow'
 *   summit_cta_url       string  home_url('/design-tomorrow')
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$headline = get_query_var( 'summit_cta_headline', 'Start a Conversation' );
$sub      = get_query_var( 'summit_cta_sub',      'We work with luxury brands and ambitious organisations. If you have a project in mind, we would like to hear about it.' );
$label    = get_query_var( 'summit_cta_label',    'Design Tomorrow' );
$url      = get_query_var( 'summit_cta_url',      home_url( '/design-tomorrow' ) );
?>

<section class="cta-footer-block" aria-labelledby="cta-footer-heading">
    <div class="container container--medium">
        <div class="cta-footer-block__inner">

            <h2 class="cta-footer-block__headline" id="cta-footer-heading">
                <?php echo esc_html( $headline ); ?>
            </h2>

            <?php if ( $sub ) : ?>
            <p class="cta-footer-block__sub">
                <?php echo esc_html( $sub ); ?>
            </p>
            <?php endif; ?>

            <a href="<?php echo esc_url( $url ); ?>" class="btn btn--primary">
                <?php echo esc_html( $label ); ?>
            </a>

        </div>
    </div>
</section>
