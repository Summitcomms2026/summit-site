<?php
/**
 * Part: Private briefing request
 * Location: parts/observatory/briefing-form.php
 * Used by: page-observatory.php — in place, through [summit_briefing_form]
 *          inside the page's own form panel (S06-P037), or after the
 *          content when the page sets summit_form = briefing and has no
 *          such panel
 *
 * The handover's request (S06-P037): what brings you here (an optional
 * audience that helps route the reply and never blocks it), what you would
 * like to understand, and how we can reach you — name, work email,
 * organisation (optional). Nothing else.
 *
 * ?audience=cmo | investor (from For CMOs, For Family Offices & Investors)
 * preselects the audience; the visitor can change it.
 *
 * Submits through Summit's existing enquiry handler (Summit Core,
 * enquiry-handler.php) as enquiry type "observatory_briefing": the request is
 * stored as a private Enquiry and the site's admin address is notified.
 * With JavaScript, assets/js/enquiry-form.js submits in place (admin-ajax)
 * and reports success only after the server acknowledges it. Without it,
 * the form posts to admin-post.php and the handler redirects back here with
 * ?enquiry=sent|invalid|failed, which this part displays.
 *
 * $args:
 *   source     the page path, recorded with the enquiry
 *   embedded   true inside the page's own panel (no section or heading)
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$source   = $args['source'] ?? '';
$embedded = ! empty( $args['embedded'] );
$privacy  = summit_nav( 'legal' )[0] ?? null;
// phpcs:disable WordPress.Security.NonceVerification -- display state and prefill only
$state    = isset( $_GET['enquiry'] ) ? sanitize_key( wp_unslash( $_GET['enquiry'] ) ) : '';
$audience = isset( $_GET['audience'] ) ? sanitize_key( wp_unslash( $_GET['audience'] ) ) : '';
// phpcs:enable

$audiences = [
    'cmo'      => __( 'Maison leadership or marketing', 'summit' ),
    'investor' => __( 'Family office or investment', 'summit' ),
    'founder'  => __( 'Founder or owner', 'summit' ),
    'other'    => __( 'Research or another question', 'summit' ),
];

$messages = [
    'sent'    => __( 'Thank you. Your request has been received, and we will reply personally.', 'summit' ),
    'invalid' => __( 'Please give your name, a valid email address and the question you would like to consider.', 'summit' ),
    'failed'  => __( 'Something went wrong. Please try again.', 'summit' ),
];
$returned = $messages[ $state ] ?? '';

if ( ! $embedded ) : ?>
<section class="obs-chapter obs-chapter--form" aria-labelledby="obs-briefing-heading">
    <h2 id="obs-briefing-heading" data-reveal><?php esc_html_e( 'Tell Us What Is on Your Mind', 'summit' ); ?></h2>
<?php endif; ?>

    <?php if ( 'sent' === $state ) : ?>

    <div class="obs-form__feedback obs-form__feedback--returned" id="enquiry-status" tabindex="-1" role="status">
        <p class="dt-form__success"><?php echo esc_html( $returned ); ?></p>
    </div>

    <?php else : ?>

    <form class="obs-form" id="summit-enquiry-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-reveal="fade" data-summit-enquiry>

        <?php wp_nonce_field( 'summit_enquiry_submit', 'summit_enquiry_nonce' ); ?>
        <input type="hidden" name="action" value="summit_enquiry">
        <input type="hidden" name="enq_type" value="observatory_briefing">
        <input type="hidden" name="enq_form_source" value="<?php echo esc_attr( $source . ( isset( $audiences[ $audience ] ) ? '?audience=' . $audience : '' ) ); ?>">

        <?php if ( $returned ) : ?>
        <div class="obs-form__feedback obs-form__feedback--returned" id="enquiry-status" tabindex="-1" role="alert">
            <p class="dt-form__error"><?php echo esc_html( $returned ); ?></p>
        </div>
        <?php endif; ?>

        <fieldset class="obs-form__field obs-form__choices">
            <legend class="obs-form__label"><?php esc_html_e( 'What brings you here?', 'summit' ); ?></legend>
            <div class="obs-form__options">
                <?php foreach ( $audiences as $key => $label ) : ?>
                <label class="obs-form__option">
                    <input type="radio" name="enq_audience" value="<?php echo esc_attr( $label ); ?>"<?php checked( $audience, $key ); ?>>
                    <span><?php echo esc_html( $label ); ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </fieldset>

        <div class="obs-form__field">
            <label class="obs-form__label" for="obs-question"><?php esc_html_e( 'What would you like to understand?', 'summit' ); ?></label>
            <textarea class="obs-form__input obs-form__textarea" id="obs-question" name="enq_message" rows="5" required aria-describedby="obs-question-hint" data-summit-summary-target></textarea>
            <p class="obs-form__hint" id="obs-question-hint"><?php esc_html_e( 'A brief description in your own words. No polished brief required.', 'summit' ); ?></p>
        </div>

        <fieldset class="obs-form__field obs-form__group">
            <legend class="obs-form__label"><?php esc_html_e( 'How can we reach you?', 'summit' ); ?></legend>
            <div class="obs-form__row">
                <div class="obs-form__field">
                    <label class="obs-form__label obs-form__label--small" for="obs-name"><?php esc_html_e( 'Name', 'summit' ); ?></label>
                    <input class="obs-form__input" type="text" id="obs-name" name="enq_name" required autocomplete="name">
                </div>
                <div class="obs-form__field">
                    <label class="obs-form__label obs-form__label--small" for="obs-email"><?php esc_html_e( 'Work email', 'summit' ); ?></label>
                    <input class="obs-form__input" type="email" id="obs-email" name="enq_email" required autocomplete="email">
                </div>
            </div>
            <div class="obs-form__field">
                <label class="obs-form__label obs-form__label--small" for="obs-organisation">
                    <?php esc_html_e( 'Organisation', 'summit' ); ?>
                    <span class="obs-form__optional"><?php esc_html_e( '(optional)', 'summit' ); ?></span>
                </label>
                <input class="obs-form__input" type="text" id="obs-organisation" name="enq_company" autocomplete="organization">
            </div>
        </fieldset>

        <p class="obs-form__hint obs-form__caution"><?php esc_html_e( 'Please do not send confidential documents or sensitive deal information through this initial form.', 'summit' ); ?></p>

        <div class="obs-form__actions">
            <button type="submit" class="btn btn--primary btn--compact dt-form__submit"><?php esc_html_e( 'Request a Private Briefing', 'summit' ); ?></button>
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
<?php if ( ! $embedded ) : ?>
</section>
<?php endif; ?>
