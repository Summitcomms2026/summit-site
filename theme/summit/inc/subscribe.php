<?php
/**
 * Subscribe — the one submission mechanism
 * Location: inc/subscribe.php
 *
 * Every subscribe form on the site (the homepage, The Future of Luxury, the
 * global subscribe part) submits the same way: to Summit Core's enquiry
 * handler as type "fol_subscription", in place through
 * assets/js/subscribe.js, or without JavaScript by redirect back with
 * ?enquiry=sent|invalid|failed. Each form keeps its own design; it opts in
 * with data-summit-subscribe, these hidden fields, and a status element
 * marked data-subscribe-status.
 *
 * No mailing-list provider is connected. Addresses are kept as private
 * Enquiry records and the site's admin is notified; the confirmation never
 * claims that an email has been sent.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * The form's action URL.
 */
function summit_subscribe_action(): string {
    return admin_url( 'admin-post.php' );
}

/**
 * Print the hidden fields a subscribe form needs, and enqueue the script.
 *
 * @param string $source The page path, recorded with the subscription.
 */
function summit_subscribe_fields( string $source = '' ): void {
    if ( '' === $source ) {
        $source = '/' . trim( (string) wp_parse_url( home_url( add_query_arg( [] ) ), PHP_URL_PATH ), '/' );
    }
    wp_nonce_field( 'summit_enquiry_submit', 'summit_enquiry_nonce', false );
    echo '<input type="hidden" name="action" value="summit_enquiry">';
    echo '<input type="hidden" name="enq_type" value="fol_subscription">';
    echo '<input type="hidden" name="enq_form_source" value="' . esc_attr( $source ) . '">';

    $v = wp_get_theme()->get( 'Version' );
    wp_enqueue_script( 'summit-subscribe', get_template_directory_uri() . '/assets/js/subscribe.js', [], $v, true );
    wp_localize_script( 'summit-subscribe', 'summitSubscribe', [ 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ] );
}

/**
 * The state returned by the no-JavaScript path, if any.
 *
 * @return array{state:string,message:string}
 */
function summit_subscribe_returned(): array {
    $state    = isset( $_GET['enquiry'] ) ? sanitize_key( wp_unslash( $_GET['enquiry'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification -- display state only
    $messages = [
        'sent'    => __( 'You’re on the list.', 'summit' ),
        'invalid' => __( 'Please enter a valid email address.', 'summit' ),
        'failed'  => __( 'Something went wrong. Please try again.', 'summit' ),
    ];
    return [ 'state' => isset( $messages[ $state ] ) ? $state : '', 'message' => $messages[ $state ] ?? '' ];
}
