<?php
/**
 * Part: Contact — the enquiry form
 * Location: parts/contact/form.php
 * Used by: page-contact.php via [summit_contact_form heading="…"]
 *
 * Let's Talk (S11-P002) and Media Enquiries (S11-P017), in the handover's
 * own words. Three things are required — name, email and a few sentences —
 * and everything else is optional.
 *
 *   Let's Talk    What brings you here? (General enquiry · Private briefing ·
 *                 Media enquiry · Career enquiry) — routes the record as
 *                 general | observatory_briefing | media_speaking | career;
 *                 topic (prefilled from the page the visitor came from,
 *                 editable); organisation; phone; and a separate, unticked
 *                 opt-in to The Future of Luxury
 *   Media         publication, type of request, deadline and time zone
 *
 * Context arrives in the address: ?topic= (a page), ?programme=, ?intent=
 * (general | briefing | media | career) and ?context= (a choice made on the
 * page). The page's own guides may also leave a summary in the browser
 * (sessionStorage), which assets/js/editorial.js places in the message for
 * the visitor to edit or delete. Nothing is sent until the visitor sends it.
 *
 * Submits through Summit Core's enquiry handler: stored as a private
 * Enquiry with the source route and topic, and the site's admin address
 * notified. With JavaScript (assets/js/enquiry-form.js) the reply appears in
 * place, and only after the server has acknowledged it; without it, the
 * form posts to admin-post.php and the handler redirects back with
 * ?enquiry=sent|invalid|failed, shown here.
 *
 * $args:
 *   type      'general' | 'media'
 *   source    the page path, recorded with the enquiry
 *   heading   the panel's headline (optional)
 *   topics    topic slug → page name, from the Let's Talk page (optional)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$is_media = 'media' === ( $args['type'] ?? '' );
$source   = $args['source'] ?? '';
$topics   = (array) ( $args['topics'] ?? [] );
$privacy  = summit_nav( 'legal' )[0] ?? null;
// phpcs:disable WordPress.Security.NonceVerification -- display state and prefill only
$state    = isset( $_GET['enquiry'] ) ? sanitize_key( wp_unslash( $_GET['enquiry'] ) ) : '';
$q_topic  = isset( $_GET['topic'] ) ? sanitize_key( wp_unslash( $_GET['topic'] ) ) : '';
$q_prog   = isset( $_GET['programme'] ) ? sanitize_key( wp_unslash( $_GET['programme'] ) ) : '';
$q_intent = isset( $_GET['intent'] ) ? sanitize_key( wp_unslash( $_GET['intent'] ) ) : '';
$q_ctx    = isset( $_GET['context'] ) ? sanitize_text_field( wp_unslash( $_GET['context'] ) ) : '';
// phpcs:enable

// The topic a visitor arrived with, in words
$topic = '';
if ( $q_prog && isset( $topics[ 'programme:' . $q_prog ] ) ) {
    $topic = $topics[ 'programme:' . $q_prog ];
} elseif ( 'undecided' === $q_prog ) {
    $topic = __( 'Programmes', 'summit' );
} elseif ( $q_topic && isset( $topics[ $q_topic ] ) ) {
    $topic = $topics[ $q_topic ];
}
if ( $q_ctx ) {
    $topic = $topic ? $topic . ' — ' . mb_substr( $q_ctx, 0, 120 ) : mb_substr( $q_ctx, 0, 120 );
}
$origin = $source . ( $q_prog ? '?programme=' . $q_prog : ( $q_topic ? '?topic=' . $q_topic : '' ) );

$reasons = [
    'general'  => [ 'general', __( 'General enquiry', 'summit' ) ],
    'briefing' => [ 'observatory_briefing', __( 'Private briefing', 'summit' ) ],
    'media'    => [ 'media_speaking', __( 'Media enquiry', 'summit' ) ],
    'career'   => [ 'career', __( 'Career enquiry', 'summit' ) ],
];
$chosen = isset( $reasons[ $q_intent ] ) ? $q_intent : 'general';

$requests = [ __( 'Interview', 'summit' ), __( 'Comment', 'summit' ), __( 'Research', 'summit' ), __( 'Speaking', 'summit' ), __( 'Permissions', 'summit' ), __( 'Other', 'summit' ) ];

$heading  = $args['heading'] ?? '';
$messages = [
    'sent'    => __( 'Thank you. Your note has been received, and Summit will reply.', 'summit' ),
    'invalid' => __( 'Please give your name, a valid email address and a few sentences about the subject.', 'summit' ),
    'failed'  => __( 'Something went wrong. Please try again.', 'summit' ),
];
$returned = $messages[ $state ] ?? '';
$req      = '<span class="ct-form__req" aria-hidden="true">*</span>';
?>
<div class="ct-form-wrap">
    <?php if ( $heading ) : ?>
    <h2 class="ct-form__heading" data-reveal><?php echo esc_html( $heading ); ?></h2>
    <?php endif; ?>

    <?php if ( 'sent' === $state ) : ?>
    <div class="ct-form__feedback ct-form__feedback--returned" id="enquiry-status" tabindex="-1" role="status">
        <p class="dt-form__success"><?php echo esc_html( $returned ); ?></p>
    </div>
    <?php else : ?>
    <form class="ct-form" id="summit-enquiry-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-reveal="fade" data-summit-enquiry>
        <?php wp_nonce_field( 'summit_enquiry_submit', 'summit_enquiry_nonce' ); ?>
        <input type="hidden" name="action" value="summit_enquiry">
        <input type="hidden" name="enq_form_source" value="<?php echo esc_attr( $origin ); ?>">
        <?php if ( $is_media ) : ?>
        <input type="hidden" name="enq_type" value="media_speaking">
        <?php endif; ?>

        <?php if ( $returned ) : ?>
        <div class="ct-form__feedback ct-form__feedback--returned" id="enquiry-status" tabindex="-1" role="alert">
            <p class="dt-form__error"><?php echo esc_html( $returned ); ?></p>
        </div>
        <?php endif; ?>

        <?php if ( ! $is_media ) : ?>
        <fieldset class="ct-form__field ct-form__choices">
            <legend class="ct-form__label"><?php esc_html_e( 'What brings you here?', 'summit' ); ?></legend>
            <div class="ct-form__options">
                <?php foreach ( $reasons as $key => [ $value, $label ] ) : ?>
                <label class="ct-form__option">
                    <input type="radio" name="enq_type" value="<?php echo esc_attr( $value ); ?>"<?php checked( $chosen, $key ); ?>>
                    <span><?php echo esc_html( $label ); ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </fieldset>
        <?php endif; ?>

        <div class="ct-form__row">
            <div class="ct-form__field">
                <label class="ct-form__label" for="ct-name"><?php esc_html_e( 'Your name', 'summit' ); ?> <?php echo $req; // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?></label>
                <input class="ct-form__input" type="text" id="ct-name" name="enq_name" required autocomplete="name">
            </div>
            <div class="ct-form__field">
                <label class="ct-form__label" for="ct-email"><?php echo esc_html( $is_media ? __( 'Work email', 'summit' ) : __( 'Email address', 'summit' ) ); ?> <?php echo $req; // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?></label>
                <input class="ct-form__input" type="email" id="ct-email" name="enq_email" required autocomplete="email" spellcheck="false">
            </div>
        </div>
        <div class="ct-form__row">
            <div class="ct-form__field">
                <label class="ct-form__label" for="ct-organisation">
                    <?php echo esc_html( $is_media ? __( 'Publication or organisation', 'summit' ) : __( 'Organisation', 'summit' ) ); ?>
                    <span class="ct-form__optional"><?php esc_html_e( '(optional)', 'summit' ); ?></span>
                </label>
                <input class="ct-form__input" type="text" id="ct-organisation" name="enq_company" autocomplete="organization">
            </div>
            <?php if ( $is_media ) : ?>
            <div class="ct-form__field">
                <label class="ct-form__label" for="ct-request"><?php esc_html_e( 'Type of request', 'summit' ); ?></label>
                <select class="ct-form__input ct-form__select" id="ct-request" name="enq_request">
                    <?php foreach ( $requests as $r ) : ?>
                    <option value="<?php echo esc_attr( $r ); ?>"><?php echo esc_html( $r ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php else : ?>
            <div class="ct-form__field">
                <label class="ct-form__label" for="ct-phone">
                    <?php esc_html_e( 'Phone', 'summit' ); ?>
                    <span class="ct-form__optional"><?php esc_html_e( '(optional)', 'summit' ); ?></span>
                </label>
                <input class="ct-form__input" type="tel" id="ct-phone" name="enq_telephone" autocomplete="tel">
            </div>
            <?php endif; ?>
        </div>

        <?php if ( ! $is_media ) : ?>
        <div class="ct-form__field">
            <label class="ct-form__label" for="ct-topic">
                <?php esc_html_e( 'Topic', 'summit' ); ?>
                <span class="ct-form__optional"><?php esc_html_e( '(optional)', 'summit' ); ?></span>
            </label>
            <input class="ct-form__input" type="text" id="ct-topic" name="enq_regarding" value="<?php echo esc_attr( $topic ); ?>">
        </div>
        <?php endif; ?>

        <div class="ct-form__field">
            <label class="ct-form__label" for="ct-question"><?php echo esc_html( $is_media ? __( 'Topic or question', 'summit' ) : __( 'What would you like to talk about?', 'summit' ) ); ?> <?php echo $req; // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?></label>
            <textarea class="ct-form__input ct-form__textarea" id="ct-question" name="enq_message" rows="6" required<?php echo $is_media ? '' : ' aria-describedby="ct-question-hint"'; ?> data-summit-summary-target></textarea>
            <?php if ( ! $is_media ) : ?>
            <p class="ct-form__hint" id="ct-question-hint"><?php esc_html_e( 'A few sentences are enough. Please don’t include passwords, detailed financial documents or other sensitive material in this form.', 'summit' ); ?></p>
            <?php endif; ?>
        </div>

        <?php if ( $is_media ) : ?>
        <div class="ct-form__field">
            <label class="ct-form__label" for="ct-deadline">
                <?php esc_html_e( 'Deadline and time zone', 'summit' ); ?>
                <span class="ct-form__optional"><?php esc_html_e( '(if relevant)', 'summit' ); ?></span>
            </label>
            <input class="ct-form__input" type="text" id="ct-deadline" name="enq_deadline">
        </div>
        <?php else : ?>
        <div class="ct-form__field ct-form__consent">
            <label class="ct-form__option">
                <input type="checkbox" name="enq_newsletter" value="1">
                <span><?php esc_html_e( 'I’d also like to receive The Future of Luxury', 'summit' ); ?></span>
            </label>
        </div>
        <?php endif; ?>

        <div class="ct-form__actions">
            <button type="submit" class="btn btn--primary btn--compact dt-form__submit"><?php echo esc_html( $is_media ? __( 'Send Media Enquiry', 'summit' ) : __( 'Send Enquiry', 'summit' ) ); ?></button>
            <p class="ct-form__privacy">
                <?php esc_html_e( 'We’ll use your details to respond.', 'summit' ); ?>
                <?php if ( $privacy ) : ?>
                <?php
                /* translators: %s: link to the Privacy page */
                printf( esc_html__( 'Read our %s.', 'summit' ), '<a href="' . esc_url( summit_nav_url( $privacy ) ) . '">' . esc_html__( 'Privacy Policy', 'summit' ) . '</a>' );
                ?>
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
