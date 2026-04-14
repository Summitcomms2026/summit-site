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

$headline = $args['headline'] ?? "Let\u{2019}s Design Tomorrow";
$body     = $args['body']     ?? 'We partner with ambitious brands to sharpen positioning, elevate creative expression and build digital experiences of lasting value.';
$label    = $args['label']    ?? 'Start a Conversation';
$url      = $args['url']      ?? home_url( '/design-tomorrow/' );
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
