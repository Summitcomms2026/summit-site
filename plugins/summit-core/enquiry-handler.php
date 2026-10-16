<?php
/**
 * Enquiry Form Handler
 *
 * Processes submissions from Summit's enquiry forms (Design Tomorrow, The
 * Observatory's private briefing request, and The Future of Luxury's
 * subscribe form). Creates an enquiry CPT post with ACF fields and sends an
 * email notification.
 *
 * A subscription asks only for an email address: it is recorded as an
 * enquiry of type "fol_subscription" until a mailing-list service is
 * connected (the theme's [summit_subscribe_form] hook takes over then).
 *
 * Forms submit by AJAX (assets/js/enquiry-form.js) and receive JSON. Without
 * JavaScript, a form posting to admin-post.php is answered with a redirect
 * back to its page carrying ?enquiry=sent|invalid|failed.
 *
 * @package SummitCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register AJAX handlers for both logged-in and guest users.
 */
add_action( 'wp_ajax_summit_enquiry',        'summit_handle_enquiry' );
add_action( 'wp_ajax_nopriv_summit_enquiry', 'summit_handle_enquiry' );

/**
 * No-JavaScript fallback: the same handler, answered with a redirect.
 */
add_action( 'admin_post_summit_enquiry',        'summit_handle_enquiry' );
add_action( 'admin_post_nopriv_summit_enquiry', 'summit_handle_enquiry' );

/**
 * Answer a submission: JSON for AJAX, otherwise a redirect back to the
 * submitting page with a state it can display. Exits.
 */
function summit_enquiry_respond( bool $ok, string $message, int $status = 200, array $data = [] ): void {
	if ( wp_doing_ajax() ) {
		if ( $ok ) {
			wp_send_json_success( array_merge( [ 'message' => $message ], $data ) );
		}
		wp_send_json_error( [ 'message' => $message ], $status );
	}

	$state = $ok ? 'sent' : ( 422 === $status ? 'invalid' : 'failed' );
	$back  = remove_query_arg( 'enquiry', wp_get_referer() ?: home_url( '/' ) );
	wp_safe_redirect( add_query_arg( 'enquiry', $state, $back ) . '#enquiry-status', 303 );
	exit;
}

/**
 * Process the enquiry form submission.
 */
function summit_handle_enquiry(): void {

	// ── Nonce verification ──────────────────────────────────────────────
	if ( ! isset( $_POST['summit_enquiry_nonce'] )
	     || ! wp_verify_nonce( $_POST['summit_enquiry_nonce'], 'summit_enquiry_submit' ) ) {
		summit_enquiry_respond( false, 'Security check failed. Please refresh and try again.', 403 );
	}

	// ── Sanitise inputs ─────────────────────────────────────────────────
	$name        = sanitize_text_field( wp_unslash( $_POST['enq_name']    ?? '' ) );
	$email       = sanitize_email( wp_unslash( $_POST['enq_email']        ?? '' ) );
	$company     = sanitize_text_field( wp_unslash( $_POST['enq_company'] ?? '' ) );
	$type        = sanitize_text_field( wp_unslash( $_POST['enq_type']    ?? '' ) );
	$message     = sanitize_textarea_field( wp_unslash( $_POST['enq_message'] ?? '' ) );
	$form_source = sanitize_text_field( wp_unslash( $_POST['enq_form_source'] ?? '' ) );

	// ── Validate required fields ────────────────────────────────────────
	$errors = [];

	$is_subscription = 'fol_subscription' === $type;

	if ( empty( $name ) && ! $is_subscription ) {
		$errors[] = 'Name is required.';
	}

	if ( empty( $email ) || ! is_email( $email ) ) {
		$errors[] = 'A valid email address is required.';
	}

	if ( empty( $type ) || ! in_array( $type, [
		'brand_strategy',
		'experience_design',
		'digital_transformation',
		'general',
		'observatory_briefing',
		'fol_subscription',
	], true ) ) {
		$errors[] = 'Please select an enquiry type.';
	}

	if ( empty( $message ) && ! $is_subscription ) {
		$errors[] = 'A message is required.';
	}

	if ( ! empty( $errors ) ) {
		summit_enquiry_respond( false, implode( ' ', $errors ), 422 );
	}

	// ── Create enquiry CPT post ─────────────────────────────────────────
	$type_labels = [
		'observatory_briefing' => 'Private Briefing (The Observatory)',
		'fol_subscription'     => 'Subscription (The Future of Luxury)',
	];
	$type_label = $type_labels[ $type ] ?? ucwords( str_replace( '_', ' ', $type ) );
	$post_title = sprintf( '%s — %s', $name ?: $email, $type_label );

	$post_id = wp_insert_post( [
		'post_type'   => 'enquiry',
		'post_title'  => $post_title,
		'post_status' => 'publish',
	], true );

	if ( is_wp_error( $post_id ) ) {
		summit_enquiry_respond( false, 'Could not save enquiry. Please try again.', 500 );
	}

	// ── Populate ACF fields ─────────────────────────────────────────────
	if ( function_exists( 'update_field' ) ) {
		update_field( 'enq_name',        $name,        $post_id );
		update_field( 'enq_email',       $email,       $post_id );
		update_field( 'enq_company',     $company,     $post_id );
		update_field( 'enq_type',        $type,        $post_id );
		update_field( 'enq_message',     $message,     $post_id );
		update_field( 'enq_form_source', $form_source, $post_id );
		update_field( 'enq_status',      'new',        $post_id );
		update_field( 'enq_submitted',   current_time( 'Y-m-d H:i:s' ), $post_id );
	}

	// ── Send email notification ─────────────────────────────────────────
	$admin_email = get_option( 'admin_email' );

	if ( $is_subscription ) {
		$subject = 'New subscription — The Future of Luxury';

		$body  = "A subscription to The Future of Luxury was received.\n\n";
		$body .= "Email: {$email}\n";
		$body .= 'Source: ' . ( 0 === strpos( $form_source, '/' ) ? home_url( $form_source ) : $form_source ) . "\n\n";
		$body .= "View in admin: " . admin_url( "post.php?post={$post_id}&action=edit" ) . "\n";
	} elseif ( 'observatory_briefing' === $type ) {
		$subject = 'Private briefing request — The Observatory';

		$body  = "A private briefing request was received through The Observatory.\n\n";
		$body .= "Name: {$name}\n";
		$body .= "Email: {$email}\n";
		if ( '' !== $company ) {
			$body .= "Organisation: {$company}\n";
		}
		$body .= "Enquiry type: {$type_label}\n";
		$body .= 'Source: ' . ( 0 === strpos( $form_source, '/' ) ? home_url( $form_source ) : $form_source ) . "\n\n";
		$body .= "Question:\n{$message}\n\n";
		$body .= "View in admin: " . admin_url( "post.php?post={$post_id}&action=edit" ) . "\n";
	} else {
		$subject = sprintf( '[Summit] New enquiry from %s — %s', $name, $type_label );

		$body  = "New enquiry received via the Design Tomorrow form.\n\n";
		$body .= "Name: {$name}\n";
		$body .= "Email: {$email}\n";
		$body .= "Company: {$company}\n";
		$body .= "Type: {$type_label}\n";
		$body .= "Source: {$form_source}\n\n";
		$body .= "Message:\n{$message}\n\n";
		$body .= "View in admin: " . admin_url( "post.php?post={$post_id}&action=edit" ) . "\n";
	}

	$mail_sent = wp_mail( $admin_email, $subject, $body, [
		'Content-Type: text/plain; charset=UTF-8',
		$name ? "Reply-To: {$name} <{$email}>" : "Reply-To: {$email}",
	] );

	// ── Respond ─────────────────────────────────────────────────────────
	summit_enquiry_respond( true, $is_subscription ? 'Thank you. Your address has been received.' : 'Thank you. We have received your enquiry.', 200, [
		'post_id'    => $post_id,
		'mail_sent'  => $mail_sent,
	] );
}
