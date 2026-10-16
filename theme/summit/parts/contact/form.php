<?php
/**
 * Part: Contact — the question form
 * Location: parts/contact/form.php
 * Used by: page-contact.php via [summit_contact_form]
 *
 * Name, email, organisation (optional) and the question — nothing else.
 * Submits through Summit Core's enquiry handler as type "general" (Let's
 * Talk) or "media_speaking" (Media Enquiries): stored as a private Enquiry
 * with the source route, and the site's admin address notified.
 *
 * The same progressive pattern as the Observatory's private briefing: with
 * JavaScript, assets/js/enquiry-form.js submits in place and reports in the
 * live region; without it, the form posts to admin-post.php and the handler
 * redirects back with ?enquiry=sent|invalid|failed, shown here.
 *
 * $args:
 *   type     'general' | 'media'
 *   source   the page path, recorded with the enquiry
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$is_media = 'media' === ( $args['type'] ?? '' );
$source   = $args['source'] ?? '';
$privacy  = summit_nav( 'legal' )[0] ?? null;
$state    = isset( $_GET['enquiry'] ) ? sanitize_key( wp_unslash( $_GET['enquiry'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification -- display state only

$copy = $is_media ? [
    'type'     => 'media_speaking',
    'heading'  => __( 'Write to Summit.', 'summit' ),
    'question' => __( 'Your enquiry', 'summit' ),
    'hint'     => __( 'The publication or programme, the subject and any deadline.', 'summit' ),
    'submit'   => __( 'Send enquiry', 'summit' ),
] : [
    'type'     => 'general',
    'heading'  => __( 'Write to Summit.', 'summit' ),
    'question' => __( 'What would you like to discuss?', 'summit' ),
    'hint'     => __( 'A few sentences is enough. Please do not include confidential figures.', 'summit' ),
    'submit'   => __( 'Send', 'summit' ),
];
$messages = [
    'sent'    => __( 'Thank you. Your note has been received, and Summit will reply.', 'summit' ),
    'invalid' => __( 'Please give your name, a valid email address and your question.', 'summit' ),
    'failed'  => __( 'Something went wrong. Please try again.', 'summit' ),
];
$returned = $messages[ $state ] ?? '';
?>
<div class="ct-form-wrap">
    <h2 class="ct-form__heading" data-reveal><?php echo esc_html( $copy['heading'] ); ?></h2>

    <?php if ( 'sent' === $state ) : ?>
    <div class="ct-form__feedback ct-form__feedback--returned" id="enquiry-status" tabindex="-1" role="status">
        <p class="dt-form__success"><?php echo esc_html( $returned ); ?></p>
    </div>
    <?php else : ?>
    <form class="ct-form" id="summit-enquiry-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-reveal="fade">
        <?php wp_nonce_field( 'summit_enquiry_submit', 'summit_enquiry_nonce' ); ?>
        <input type="hidden" name="action" value="summit_enquiry">
        <input type="hidden" name="enq_type" value="<?php echo esc_attr( $copy['type'] ); ?>">
        <input type="hidden" name="enq_form_source" value="<?php echo esc_attr( $source ); ?>">

        <?php if ( $returned ) : ?>
        <div class="ct-form__feedback ct-form__feedback--returned" id="enquiry-status" tabindex="-1" role="alert">
            <p class="dt-form__error"><?php echo esc_html( $returned ); ?></p>
        </div>
        <?php endif; ?>

        <div class="ct-form__row">
            <div class="ct-form__field">
                <label class="ct-form__label" for="ct-name"><?php esc_html_e( 'Name', 'summit' ); ?></label>
                <input class="ct-form__input" type="text" id="ct-name" name="enq_name" required autocomplete="name">
            </div>
            <div class="ct-form__field">
                <label class="ct-form__label" for="ct-email"><?php esc_html_e( 'Email', 'summit' ); ?></label>
                <input class="ct-form__input" type="email" id="ct-email" name="enq_email" required autocomplete="email" spellcheck="false">
            </div>
        </div>
        <div class="ct-form__field">
            <label class="ct-form__label" for="ct-organisation">
                <?php echo esc_html( $is_media ? __( 'Publication or organisation', 'summit' ) : __( 'Organisation', 'summit' ) ); ?>
                <span class="ct-form__optional"><?php esc_html_e( '(optional)', 'summit' ); ?></span>
            </label>
            <input class="ct-form__input" type="text" id="ct-organisation" name="enq_company" autocomplete="organization">
        </div>
        <div class="ct-form__field">
            <label class="ct-form__label" for="ct-question"><?php echo esc_html( $copy['question'] ); ?></label>
            <textarea class="ct-form__input ct-form__textarea" id="ct-question" name="enq_message" rows="6" required aria-describedby="ct-question-hint"></textarea>
            <p class="ct-form__hint" id="ct-question-hint"><?php echo esc_html( $copy['hint'] ); ?></p>
        </div>
        <div class="ct-form__actions">
            <button type="submit" class="btn btn--primary btn--compact dt-form__submit"><?php echo esc_html( $copy['submit'] ); ?></button>
            <p class="ct-form__privacy">
                <?php esc_html_e( 'Your details are used only to reply.', 'summit' ); ?>
                <?php if ( $privacy ) : ?>
                <a href="<?php echo esc_url( summit_nav_url( $privacy ) ); ?>"><?php esc_html_e( 'Privacy', 'summit' ); ?></a>
                <?php endif; ?>
            </p>
        </div>
        <div class="ct-form__feedback" aria-live="polite">
            <p class="dt-form__success" hidden><?php echo esc_html( $messages['sent'] ); ?></p>
            <p class="dt-form__error" hidden><?php echo esc_html( $messages['failed'] ); ?></p>
        </div>
    </form>
    <?php endif; ?>
</div>
