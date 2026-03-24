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
?>

<section class="cta-footer section--dark section--padded" aria-labelledby="cta-footer-heading">
    <div class="container container--medium">
        <div class="cta-footer__inner text-center">

            <h2 class="cta-footer__headline" id="cta-footer-heading">
                <?php echo esc_html( $headline ); ?>
            </h2>

            <?php if ( $body ) : ?>
            <p class="cta-footer__body">
                <?php echo esc_html( $body ); ?>
            </p>
            <?php endif; ?>

            <a href="<?php echo esc_url( $url ); ?>" class="btn btn--primary">
                <?php echo esc_html( $label ); ?>
            </a>

        </div>
    </div>
</section>
