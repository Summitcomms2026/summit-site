<?php
/**
 * Part: CTA Footer
 * Location: parts/global/cta-footer.php
 *
 * Site-wide call-to-action section used at the bottom of single
 * templates and the homepage. Dark section.
 *
 * Accepts optional $args overrides via get_template_part() third parameter:
 *   'headline' string  CTA headline
 *   'body'     string  CTA body copy
 *   'label'    string  Button label
 *   'url'      string  Button URL
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

// Defaults: Summit's global proposition and the canonical CTA (from the
// navigation config). Templates may still pass their own overrides.
$nav_cta  = function_exists( 'summit_nav' ) ? summit_nav( 'cta' ) : null;
$headline = $args['headline'] ?? 'Quality of judgement makes luxury endure.';
$body     = $args['body']     ?? 'For questions of strategy, desire, stewardship and long-term value, talk to Summit.';
$label    = $args['label']    ?? ( $nav_cta['label'] ?? "Let\u{2019}s Talk" );
$url      = $args['url']      ?? ( $nav_cta ? summit_nav_url( $nav_cta ) : home_url( '/lets-talk/' ) );
$image    = $args['image']    ?? '';

$image_class = $image ? ' cta-footer--image' : '';
$image_style = $image ? " style=\"--cta-bg: url('" . esc_url( $image ) . "');\"" : '';
?>

<section class="cta-footer section--dark section--padded<?php echo $image_class; ?>"
         aria-labelledby="cta-footer-heading"<?php echo $image_style; ?>>
    <div class="container container--medium">
        <div class="cta-footer__inner text-center">

            <h2 class="cta-footer__headline" id="cta-footer-heading" data-reveal="fade">
                <?php echo esc_html( $headline ); ?>
            </h2>

            <?php if ( $body ) : ?>
            <p class="cta-footer__body" data-reveal="fade" data-reveal-delay="1">
                <?php echo esc_html( $body ); ?>
            </p>
            <?php endif; ?>

            <a href="<?php echo esc_url( $url ); ?>" class="btn cta-footer__btn" data-reveal="fade" data-reveal-delay="2">
                <?php echo esc_html( $label ); ?>
            </a>

        </div>
    </div>
</section>
