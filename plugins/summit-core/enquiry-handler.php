<?php
/**
 * Enquiry Form Handler
 *
 * Processes AJAX submissions from the Design Tomorrow contact form.
 * Creates an enquiry CPT post with ACF fields and sends an email notification.
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
 * Process the enquiry form submission.
 */
function summit_handle_enquiry(): void {

	// ── Nonce verification ──────────────────────────────────────────────
	if ( ! isset( $_POST['summit_enquiry_nonce'] )
	     || ! wp_verify_nonce( $_POST['summit_enquiry_nonce'], 'summit_enquiry_submit' ) ) {
		wp_send_json_error( [ 'message' => 'Security check failed. Please refresh and try again.' ], 403 );
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

	if ( empty( $name ) ) {
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
	], true ) ) {
		$errors[] = 'Please select an enquiry type.';
	}

	if ( empty( $message ) ) {
		$errors[] = 'A message is required.';
	}

	if ( ! empty( $errors ) ) {
		wp_send_json_error( [ 'message' => implode( ' ', $errors ) ], 422 );
	}

	// ── Create enquiry CPT post ─────────────────────────────────────────
	$post_title = sprintf( '%s — %s', $name, ucwords( str_replace( '_', ' ', $type ) ) );

	$post_id = wp_insert_post( [
		'post_type'   => 'enquiry',
		'post_title'  => $post_title,
		'post_status' => 'publish',
	], true );

	if ( is_wp_error( $post_id ) ) {
		wp_send_json_error( [ 'message' => 'Could not save enquiry. Please try again.' ], 500 );
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
	$type_label  = ucwords( str_replace( '_', ' ', $type ) );
	$subject     = sprintf( '[Summit] New enquiry from %s — %s', $name, $type_label );

	$body  = "New enquiry received via the Design Tomorrow form.\n\n";
	$body .= "Name: {$name}\n";
	$body .= "Email: {$email}\n";
	$body .= "Company: {$company}\n";
	$body .= "Type: {$type_label}\n";
	$body .= "Source: {$form_source}\n\n";
	$body .= "Message:\n{$message}\n\n";
	$body .= "View in admin: " . admin_url( "post.php?post={$post_id}&action=edit" ) . "\n";

	$mail_sent = wp_mail( $admin_email, $subject, $body, [
		'Content-Type: text/plain; charset=UTF-8',
		"Reply-To: {$name} <{$email}>",
	] );

	// ── Respond ─────────────────────────────────────────────────────────
	wp_send_json_success( [
		'message'    => 'Thank you. We have received your enquiry.',
		'post_id'    => $post_id,
		'mail_sent'  => $mail_sent,
	] );
}
