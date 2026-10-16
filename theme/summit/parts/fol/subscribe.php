<?php
/**
 * Part: The Future of Luxury — subscribe
 * Location: parts/fol/subscribe.php
 * Used by: archive-article.php, single-article.php, taxonomy-article_category.php
 *
 * When a mailing-list service registers [summit_subscribe_form] (the
 * theme's existing hook, also read by parts/global/subscribe.php), it
 * renders here. Until then the form submits through Summit Core's enquiry
 * handler as type "fol_subscription": the address is stored as a private
 * Enquiry and the site's admin address is notified. Nothing is claimed
 * that has not happened — the confirmation appears only on the handler's
 * success response.
 *
 * With JavaScript, assets/js/fol.js submits in place (admin-ajax). Without
 * it, the form posts to admin-post.php and the handler redirects back with
 * ?enquiry=sent|invalid|failed, shown here.
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
$state   = isset( $_GET['enquiry'] ) ? sanitize_key( wp_unslash( $_GET['enquiry'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification -- display state only
$messages = [
    'sent'    => __( 'Thank you. Your address has been received.', 'summit' ),
    'invalid' => __( 'Please enter a valid email address.', 'summit' ),
    'failed'  => __( 'Something went wrong. Please try again.', 'summit' ),
];
$returned = $messages[ $state ] ?? '';
$field    = wp_unique_id( 'fol-subscribe-email-' );
?>
<section class="fol-panel fol-subscribe" id="<?php echo esc_attr( $sid ); ?>" aria-labelledby="<?php echo esc_attr( $sid ); ?>-heading">
    <div class="fol-wrap fol-subscribe__grid">
        <div class="fol-subscribe__intro" data-reveal>
            <h2 class="fol-subscribe__title" id="<?php echo esc_attr( $sid ); ?>-heading"><?php esc_html_e( 'Subscribe', 'summit' ); ?></h2>
            <p class="fol-subscribe__sub"><?php esc_html_e( 'New essays and analysis from The Future of Luxury, by email.', 'summit' ); ?></p>
        </div>

        <div class="fol-subscribe__action" data-reveal data-reveal-delay="1">
            <?php if ( shortcode_exists( 'summit_subscribe_form' ) ) :
                echo do_shortcode( '[summit_subscribe_form]' );
            elseif ( 'sent' === $state ) : ?>
            <p class="fol-subscribe__status is-sent" id="enquiry-status" tabindex="-1" role="status"><?php echo esc_html( $returned ); ?></p>
            <?php else : ?>
            <form class="fol-subscribe__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-fol-subscribe novalidate>
                <?php wp_nonce_field( 'summit_enquiry_submit', 'summit_enquiry_nonce', false ); ?>
                <input type="hidden" name="action" value="summit_enquiry">
                <input type="hidden" name="enq_type" value="fol_subscription">
                <input type="hidden" name="enq_form_source" value="<?php echo esc_attr( $source ); ?>">
                <label class="fol-subscribe__label" for="<?php echo esc_attr( $field ); ?>"><?php esc_html_e( 'Email address', 'summit' ); ?></label>
                <div class="fol-subscribe__row">
                    <input class="fol-subscribe__input" type="email" id="<?php echo esc_attr( $field ); ?>" name="enq_email" required autocomplete="email" spellcheck="false">
                    <button type="submit" class="btn btn--primary btn--compact fol-subscribe__submit"><?php esc_html_e( 'Subscribe', 'summit' ); ?></button>
                </div>
                <p class="fol-subscribe__status<?php echo $returned ? ' is-error' : ''; ?>"<?php echo $returned ? ' id="enquiry-status" tabindex="-1"' : ''; ?> role="status" aria-live="polite"><?php echo esc_html( $returned ); ?></p>
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
