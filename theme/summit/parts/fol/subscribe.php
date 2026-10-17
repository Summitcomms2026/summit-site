<?php
/**
 * Part: The Future of Luxury — subscribe
 * Location: parts/fol/subscribe.php
 * Used by: archive-article.php, single-article.php, taxonomy-article_category.php
 *
 * When a mailing-list service registers [summit_subscribe_form] (the
 * theme's existing hook, also read by parts/global/subscribe.php), it
 * renders here. Until then the form uses the site's one subscription
 * mechanism (inc/subscribe.php): Summit Core's enquiry handler, in place
 * through assets/js/subscribe.js or by redirect without JavaScript. The
 * confirmation appears only on the handler's success response.
 *
 * $args:
 *   source   the page path, recorded with the subscription
 *   id       the section's id attribute (default 'subscribe')
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$source  = $args['source'] ?? '';
$sid     = $args['id'] ?? 'subscribe';
$privacy = summit_nav( 'legal' )[0] ?? null;
[ 'state' => $state, 'message' => $returned ] = summit_subscribe_returned();
$field    = wp_unique_id( 'fol-subscribe-email-' );
?>
<section class="fol-panel fol-subscribe" id="<?php echo esc_attr( $sid ); ?>" aria-labelledby="<?php echo esc_attr( $sid ); ?>-heading">
    <div class="fol-wrap fol-subscribe__grid">
        <div class="fol-subscribe__intro" data-reveal>
            <?php // Copy: S09-P023 (the digest choice waits until a digest exists) ?>
            <p class="fol-panel__label"><?php esc_html_e( 'Subscribe', 'summit' ); ?></p>
            <h2 class="fol-subscribe__title" id="<?php echo esc_attr( $sid ); ?>-heading"><?php esc_html_e( 'For the Next Question', 'summit' ); ?></h2>
            <p class="fol-subscribe__sub"><?php esc_html_e( 'The best luxury stories seldom end where the headlines do. Receive new essays from The Future of Luxury when there is an argument worth making.', 'summit' ); ?></p>
        </div>

        <div class="fol-subscribe__action" data-reveal data-reveal-delay="1">
            <?php if ( shortcode_exists( 'summit_subscribe_form' ) ) :
                echo do_shortcode( '[summit_subscribe_form]' );
            elseif ( 'sent' === $state ) : ?>
            <p class="fol-subscribe__status is-sent" id="enquiry-status" tabindex="-1" role="status"><?php echo esc_html( $returned ); ?></p>
            <?php else : ?>
            <form class="fol-subscribe__form" method="post" action="<?php echo esc_url( summit_subscribe_action() ); ?>" data-summit-subscribe novalidate>
                <?php summit_subscribe_fields( $source ); ?>
                <label class="fol-subscribe__label" for="<?php echo esc_attr( $field ); ?>"><?php esc_html_e( 'Email address', 'summit' ); ?></label>
                <div class="fol-subscribe__row">
                    <input class="fol-subscribe__input" type="email" id="<?php echo esc_attr( $field ); ?>" name="enq_email" required autocomplete="email" spellcheck="false">
                    <button type="submit" class="btn btn--primary btn--compact fol-subscribe__submit"><?php esc_html_e( 'Subscribe', 'summit' ); ?></button>
                </div>
                <p class="fol-subscribe__status<?php echo $returned ? ' is-error' : ''; ?>"<?php echo $returned ? ' id="enquiry-status" tabindex="-1"' : ''; ?> role="status" aria-live="polite" data-subscribe-status><?php echo esc_html( $returned ); ?></p>
                <p class="fol-subscribe__privacy">
                    <?php esc_html_e( 'Your address is used only for The Future of Luxury.', 'summit' ); ?>
                    <?php if ( $privacy ) : ?>
                    <a href="<?php echo esc_url( summit_nav_url( $privacy ) ); ?>"><?php esc_html_e( 'Privacy', 'summit' ); ?></a>
                    <?php endif; ?>
                </p>
            </form>
            <?php endif; ?>
        </div>
    </div>
</section>
