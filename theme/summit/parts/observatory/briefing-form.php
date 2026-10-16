<?php
/**
 * Part: Private briefing request
 * Location: parts/observatory/briefing-form.php
 * Used by: page-observatory.php when the page sets summit_form = briefing
 *
 * Submits through Summit's existing enquiry handler (Summit Core,
 * enquiry-handler.php) as enquiry type "observatory_briefing": the request is
 * stored as a private Enquiry and the site's admin address is notified.
 *
 * Asks only for what a reply needs: name, email, organisation (optional)
 * and the question.
 *
 * With JavaScript, assets/js/enquiry-form.js submits in place (admin-ajax)
 * and reports success or failure in the live region. Without it, the form
 * posts to admin-post.php and the handler redirects back here with
 * ?enquiry=sent|invalid|failed, which this part displays.
 *
 * $args:
 *   source   the page path, recorded with the enquiry
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$source  = $args['source'] ?? '';
$privacy = summit_nav( 'legal' )[0] ?? null;
$state   = isset( $_GET['enquiry'] ) ? sanitize_key( wp_unslash( $_GET['enquiry'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification -- display state only

$messages = [
    'sent'    => __( 'Thank you. Your request has been received, and we will reply personally.', 'summit' ),
    'invalid' => __( 'Please give your name, a valid email address and the question you would like to consider.', 'summit' ),
    'failed'  => __( 'Something went wrong. Please try again.', 'summit' ),
];
$returned = $messages[ $state ] ?? '';
?>
<section class="obs-chapter obs-chapter--form" aria-labelledby="obs-briefing-heading">
    <p class="obs-marker" data-reveal="fade"><?php esc_html_e( '03 — Request', 'summit' ); ?></p>

    <h2 id="obs-briefing-heading" data-reveal><?php esc_html_e( 'Begin with a short note.', 'summit' ); ?></h2>

    <?php if ( 'sent' === $state ) : ?>

    <div class="obs-form__feedback obs-form__feedback--returned" id="enquiry-status" tabindex="-1" role="status">
        <p class="dt-form__success"><?php echo esc_html( $returned ); ?></p>
    </div>

    <?php else : ?>

    <p class="obs-lead" data-reveal><?php esc_html_e( 'Tell us who you are and the question you would like the Observatory to consider. Every request is read and answered personally.', 'summit' ); ?></p>

    <form class="obs-form" id="summit-enquiry-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-reveal="fade">

        <?php wp_nonce_field( 'summit_enquiry_submit', 'summit_enquiry_nonce' ); ?>
        <input type="hidden" name="action" value="summit_enquiry">
        <input type="hidden" name="enq_type" value="observatory_briefing">
        <input type="hidden" name="enq_form_source" value="<?php echo esc_attr( $source ); ?>">

        <?php if ( $returned ) : ?>
        <div class="obs-form__feedback obs-form__feedback--returned" id="enquiry-status" tabindex="-1" role="alert">
            <p class="dt-form__error"><?php echo esc_html( $returned ); ?></p>
        </div>
        <?php endif; ?>

        <div class="obs-form__row">
            <div class="obs-form__field">
                <label class="obs-form__label" for="obs-name"><?php esc_html_e( 'Name', 'summit' ); ?></label>
                <input class="obs-form__input" type="text" id="obs-name" name="enq_name" required autocomplete="name">
            </div>
            <div class="obs-form__field">
                <label class="obs-form__label" for="obs-email"><?php esc_html_e( 'Email', 'summit' ); ?></label>
                <input class="obs-form__input" type="email" id="obs-email" name="enq_email" required autocomplete="email">
            </div>
        </div>

        <div class="obs-form__field">
            <label class="obs-form__label" for="obs-organisation">
                <?php esc_html_e( 'Organisation', 'summit' ); ?>
                <span class="obs-form__optional"><?php esc_html_e( '(optional)', 'summit' ); ?></span>
            </label>
            <input class="obs-form__input" type="text" id="obs-organisation" name="enq_company" autocomplete="organization">
        </div>

        <div class="obs-form__field">
            <label class="obs-form__label" for="obs-question"><?php esc_html_e( 'The question', 'summit' ); ?></label>
            <textarea class="obs-form__input obs-form__textarea" id="obs-question" name="enq_message" rows="5" required aria-describedby="obs-question-hint"></textarea>
            <p class="obs-form__hint" id="obs-question-hint"><?php esc_html_e( 'What you would like the Observatory to consider. A few sentences is enough; please do not include confidential figures.', 'summit' ); ?></p>
        </div>

        <div class="obs-form__actions">
            <button type="submit" class="btn btn--primary btn--compact dt-form__submit"><?php esc_html_e( 'Send request', 'summit' ); ?></button>
            <p class="obs-form__privacy">
                <?php esc_html_e( 'We use these details only to respond to your request.', 'summit' ); ?>
                <?php if ( $privacy ) : ?>
                <a href="<?php echo esc_url( summit_nav_url( $privacy ) ); ?>"><?php esc_html_e( 'Privacy', 'summit' ); ?></a>
                <?php endif; ?>
            </p>
        </div>

        <div class="obs-form__feedback" aria-live="polite">
            <p class="dt-form__success" hidden><?php echo esc_html( $messages['sent'] ); ?></p>
            <p class="dt-form__error" hidden><?php echo esc_html( $messages['failed'] ); ?></p>
        </div>

    </form>

    <?php endif; ?>
</section>
