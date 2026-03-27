<?php
/**
 * Batch 17: Retag published case studies with correct service_type terms.
 *
 * Maps each published case study to one of the three core service types
 * (brand-strategy, experience-design, digital-transformation) so that
 * service pages show intentional matches instead of falling back to
 * the "3 most recent" query.
 *
 * Run:  wp eval-file plugins/summit-core/retag-case-studies.php --path=app/public
 *
 * Idempotent — safe to re-run.
 *
 * @package SummitCore
 */

defined( 'ABSPATH' ) || exit;

echo "\n== Batch 17: Retag case studies with core service_type terms ==\n\n";

// ── Mapping: slug → target service_type slug ─────────────────────────
$mapping = [
	'kaleida-studios'                => 'experience-design',
	'tastemakers-podcast'            => 'brand-strategy',
	'leading-plastic-surgeons-films' => 'experience-design',
];

foreach ( $mapping as $slug => $target_service ) {

	$post = get_page_by_path( $slug, OBJECT, 'case_study' );

	if ( ! $post ) {
		echo "[skip] {$slug} — not found in database\n";
		continue;
	}

	// Report current terms before change.
	$current_terms = wp_get_object_terms( $post->ID, 'service_type', [ 'fields' => 'slugs' ] );
	$before        = is_wp_error( $current_terms ) ? '(error)' : ( empty( $current_terms ) ? '(none)' : implode( ', ', $current_terms ) );

	// Assign the target term (replaces any existing service_type terms).
	$result = wp_set_object_terms( $post->ID, $target_service, 'service_type' );

	if ( is_wp_error( $result ) ) {
		echo "[error] {$slug} — {$result->get_error_message()}\n";
		continue;
	}

	echo "[tagged] {$slug}: {$before} → {$target_service}\n";
}

echo "\nDone.\n";
