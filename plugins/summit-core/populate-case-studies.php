<?php
/**
 * Batch 15 — Populate and Publish Case Studies
 *
 * Reads batch-15-case-studies.json and populates narrative ACF fields
 * for the 3 selected case studies, sets related article links, and
 * publishes them. Holds the remaining 2 as drafts without touching
 * their placeholder content.
 *
 * Also updates the homepage Selected Work field to reference only
 * published case studies, and cleans up stale related-case cross-links
 * that point to draft posts.
 *
 * Run via WP-CLI:
 *   wp eval-file plugins/summit-core/populate-case-studies.php --path=app/public
 *
 * Revert published case studies to draft (does NOT restore homepage
 * or relationship fields to their prior state):
 *   wp eval-file plugins/summit-core/populate-case-studies.php --unpublish --path=app/public
 *
 * Dev-only. Local-only. Do not deploy to production.
 *
 * @package SummitCore
 */

defined( 'ABSPATH' ) || exit;

echo "\n══════════════════════════════════════════════════════════\n";
echo "Batch 15 — Populate and Publish Case Studies\n";
echo "══════════════════════════════════════════════════════════\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 1: Parse CLI flags
// ─────────────────────────────────────────────────────────────────────────────
echo "── Preflight checks ──────────────────────────────────────\n\n";

$mode = 'publish';
if ( isset( $args ) && is_array( $args ) ) {
	foreach ( $args as $arg ) {
		if ( $arg === '--unpublish' ) {
			$mode = 'unpublish';
		}
	}
}
echo "[OK] Mode: {$mode}\n";

if ( $mode === 'unpublish' ) {
	echo "  Note: --unpublish reverts the 3 published case studies to draft.\n";
	echo "  It does NOT restore homepage or relationship fields to prior state.\n";
}

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 2: Load batch-15-case-studies.json
// ─────────────────────────────────────────────────────────────────────────────
$data_path = dirname( __FILE__ ) . '/data/batch-15-case-studies.json';
if ( ! file_exists( $data_path ) || ! is_readable( $data_path ) ) {
	echo "[ABORT] Data file missing or unreadable: {$data_path}\n";
	return;
}

$entries = json_decode( file_get_contents( $data_path ), true );
if ( ! is_array( $entries ) || empty( $entries ) ) {
	echo "[ABORT] Data file is empty or invalid JSON.\n";
	return;
}

$publish_entries = [];
$hold_entries    = [];
foreach ( $entries as $entry ) {
	if ( ( $entry['action'] ?? '' ) === 'publish' ) {
		$publish_entries[] = $entry;
	} else {
		$hold_entries[] = $entry;
	}
}

echo "[OK] Data file loaded: " . count( $publish_entries ) . " to publish, " . count( $hold_entries ) . " to hold.\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 3: ACF availability
// ─────────────────────────────────────────────────────────────────────────────
if ( ! function_exists( 'update_field' ) || ! function_exists( 'get_field' ) ) {
	echo "[ABORT] ACF is not active.\n";
	return;
}
echo "[OK] ACF is active.\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 4: Resolve all case study posts
// ─────────────────────────────────────────────────────────────────────────────
$targets       = [];
$resolve_fails = [];

foreach ( $entries as $entry ) {
	$wp_slug = $entry['wp_slug'];

	$q = new WP_Query( [
		'post_type'      => 'case_study',
		'post_status'    => 'any',
		'post_name__in'  => [ $wp_slug ],
		'posts_per_page' => 1,
		'no_found_rows'  => true,
	] );

	if ( empty( $q->posts ) ) {
		$resolve_fails[] = "{$wp_slug}: post not found";
		continue;
	}

	$post    = $q->posts[0];
	$post_id = $post->ID;

	if ( get_post_type( $post_id ) !== 'case_study' ) {
		$resolve_fails[] = "{$wp_slug} (ID {$post_id}): not a case_study post type";
		continue;
	}

	$targets[ $wp_slug ] = [
		'post_id'        => $post_id,
		'current_status' => $post->post_status,
		'action'         => $entry['action'] ?? 'hold',
		'entry'          => $entry,
	];
}

if ( ! empty( $resolve_fails ) ) {
	echo "[ABORT] Could not resolve all target posts:\n";
	foreach ( $resolve_fails as $fail ) {
		echo "  - {$fail}\n";
	}
	return;
}

echo "[OK] All " . count( $targets ) . " case study posts resolved.\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 5: Resolve related article slugs
// ─────────────────────────────────────────────────────────────────────────────
echo "\n── Related article resolution ────────────────────────────\n\n";

$article_id_map   = [];
$article_resolved = [];
$article_missing  = [];

// Collect all unique article slugs referenced.
$all_article_slugs = [];
foreach ( $publish_entries as $entry ) {
	if ( ! empty( $entry['cs_related_article'] ) ) {
		foreach ( $entry['cs_related_article'] as $slug ) {
			$all_article_slugs[] = $slug;
		}
	}
}
$all_article_slugs = array_unique( $all_article_slugs );

foreach ( $all_article_slugs as $slug ) {
	$q = new WP_Query( [
		'post_type'      => 'article',
		'post_status'    => 'publish',
		'post_name__in'  => [ $slug ],
		'posts_per_page' => 1,
		'no_found_rows'  => true,
	] );

	if ( ! empty( $q->posts ) ) {
		$article_id_map[ $slug ] = $q->posts[0]->ID;
		$article_resolved[]      = $slug;
		echo "  [resolved] {$slug} → ID {$q->posts[0]->ID}\n";
	} else {
		$article_missing[] = $slug;
		echo "  [missing]  {$slug} — will be omitted from related articles\n";
	}
}

if ( empty( $all_article_slugs ) ) {
	echo "  (no related article slugs referenced)\n";
}

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 6: Snapshot pre-existing published case studies
// ─────────────────────────────────────────────────────────────────────────────
$pre_existing_q = new WP_Query( [
	'post_type'      => 'case_study',
	'post_status'    => 'publish',
	'posts_per_page' => 100,
	'no_found_rows'  => true,
	'fields'         => 'ids',
] );
$pre_existing_published_ids = $pre_existing_q->posts ?: [];
wp_reset_postdata();

echo "\n[OK] Pre-existing published case studies: " . count( $pre_existing_published_ids ) . "\n";

echo "\n── All preflight checks passed ───────────────────────────\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// UNPUBLISH MODE
// ─────────────────────────────────────────────────────────────────────────────
if ( $mode === 'unpublish' ) {
	echo "── Reverting published case studies to draft ─────────────\n\n";

	$reverted = 0;
	$already  = 0;
	$failed   = 0;

	foreach ( $targets as $wp_slug => $data ) {
		if ( $data['action'] !== 'publish' ) {
			continue;
		}

		$post_id = $data['post_id'];
		$current = $data['current_status'];

		if ( $current === 'draft' ) {
			echo "  {$wp_slug} (ID {$post_id}) — already draft\n";
			$already++;
			continue;
		}

		$result = wp_update_post( [ 'ID' => $post_id, 'post_status' => 'draft' ], true );
		if ( is_wp_error( $result ) ) {
			echo "  {$wp_slug} (ID {$post_id}) — FAILED: {$result->get_error_message()}\n";
			$failed++;
		} else {
			echo "  {$wp_slug} (ID {$post_id}) — reverted to draft\n";
			$reverted++;
		}
	}

	echo "\n══════════════════════════════════════════════════════════\n";
	echo "Unpublish Summary\n";
	echo "══════════════════════════════════════════════════════════\n";
	echo "  Reverted:  {$reverted}\n";
	echo "  Already:   {$already}\n";
	echo "  Failed:    {$failed}\n";
	echo "══════════════════════════════════════════════════════════\n\n";
	return;
}

// ─────────────────────────────────────────────────────────────────────────────
// POPULATION PASS — publish entries only
// ─────────────────────────────────────────────────────────────────────────────
echo "── Populating and publishing case studies ─────────────────\n\n";

$populated    = 0;
$published    = 0;
$already_pub  = 0;
$held         = 0;
$failed       = 0;
$failed_list  = [];
$published_ids = [];

$narrative_fields = [
	'cs_intro',
	'cs_challenge',
	'cs_strategic_idea',
	'cs_what_we_built',
	'cs_outcome',
	'cs_outcome_signal',
	'cs_seo_title',
	'cs_meta_desc',
];

$i = 0;
foreach ( $targets as $wp_slug => $data ) {
	$i++;
	$post_id = $data['post_id'];
	$action  = $data['action'];
	$entry   = $data['entry'];
	$label   = "[{$i}/" . count( $targets ) . "] {$wp_slug} (ID {$post_id})";

	// ── Hold entries: ensure draft, do not touch content.
	if ( $action === 'hold' ) {
		if ( $data['current_status'] !== 'draft' ) {
			$result = wp_update_post( [ 'ID' => $post_id, 'post_status' => 'draft' ], true );
			if ( is_wp_error( $result ) ) {
				echo "  {$label} → hold FAILED: {$result->get_error_message()}\n";
				$failed++;
				$failed_list[] = "{$wp_slug}: {$result->get_error_message()}";
				continue;
			}
		}
		echo "  {$label} → hold (draft, content untouched)\n";
		$held++;
		continue;
	}

	// ── Publish entries: populate fields then publish.

	// Populate narrative fields.
	$fields_updated = 0;
	foreach ( $narrative_fields as $field_name ) {
		if ( isset( $entry[ $field_name ] ) && $entry[ $field_name ] !== '' ) {
			update_field( $field_name, $entry[ $field_name ], $post_id );
			$fields_updated++;
		}
	}

	// Set related articles.
	$related_ids = [];
	if ( ! empty( $entry['cs_related_article'] ) ) {
		foreach ( $entry['cs_related_article'] as $art_slug ) {
			if ( isset( $article_id_map[ $art_slug ] ) ) {
				$related_ids[] = $article_id_map[ $art_slug ];
			}
		}
	}
	if ( ! empty( $related_ids ) ) {
		update_field( 'cs_related_article', $related_ids, $post_id );
		echo "  {$label} → {$fields_updated} fields updated, " . count( $related_ids ) . " related articles set\n";
	} else {
		echo "  {$label} → {$fields_updated} fields updated, no related articles\n";
	}
	$populated++;

	// Publish.
	if ( $data['current_status'] === 'publish' ) {
		echo "    → already published\n";
		$already_pub++;
	} else {
		$result = wp_update_post( [ 'ID' => $post_id, 'post_status' => 'publish' ], true );
		if ( is_wp_error( $result ) ) {
			echo "    → publish FAILED: {$result->get_error_message()}\n";
			$failed++;
			$failed_list[] = "{$wp_slug}: {$result->get_error_message()}";
			continue;
		}
		echo "    → published\n";
		$published++;
	}

	$published_ids[] = $post_id;
}

// ─────────────────────────────────────────────────────────────────────────────
// HOMEPAGE CLEANUP — update home_work_cases to published IDs only
// ─────────────────────────────────────────────────────────────────────────────
echo "\n── Homepage Selected Work cleanup ────────────────────────\n\n";

$front_page_id = (int) get_option( 'page_on_front' );
if ( $front_page_id && ! empty( $published_ids ) ) {
	$old_cases = get_field( 'home_work_cases', $front_page_id );
	$old_ids   = [];
	if ( is_array( $old_cases ) ) {
		$old_ids = array_map( function( $p ) { return $p instanceof WP_Post ? $p->ID : (int) $p; }, $old_cases );
	}

	update_field( 'home_work_cases', $published_ids, $front_page_id );
	echo "  Updated home_work_cases on front page (ID {$front_page_id}).\n";
	echo "  Old IDs: " . implode( ', ', $old_ids ) . "\n";
	echo "  New IDs: " . implode( ', ', $published_ids ) . "\n";
} else {
	echo "  [skip] No front page configured or no published case studies.\n";
}

// ─────────────────────────────────────────────────────────────────────────────
// RELATED CASES CLEANUP — remove draft posts from cross-links
// ─────────────────────────────────────────────────────────────────────────────
echo "\n── Related cases cleanup ─────────────────────────────────\n\n";

$hold_ids = [];
foreach ( $targets as $wp_slug => $data ) {
	if ( $data['action'] === 'hold' ) {
		$hold_ids[] = $data['post_id'];
	}
}

foreach ( $targets as $wp_slug => $data ) {
	if ( $data['action'] !== 'publish' ) {
		continue;
	}

	$post_id       = $data['post_id'];
	$related_cases = get_field( 'cs_related_cases', $post_id );

	if ( empty( $related_cases ) ) {
		continue;
	}

	$clean_cases = [];
	$removed     = [];
	foreach ( $related_cases as $rc ) {
		$rc_id = $rc instanceof WP_Post ? $rc->ID : (int) $rc;
		if ( in_array( $rc_id, $hold_ids, true ) ) {
			$removed[] = $rc_id;
		} else {
			$clean_cases[] = $rc_id;
		}
	}

	if ( ! empty( $removed ) ) {
		update_field( 'cs_related_cases', $clean_cases ?: [], $post_id );
		echo "  {$wp_slug}: removed draft IDs " . implode( ', ', $removed ) . " from cs_related_cases\n";
	}
}

// Set cross-links between the 3 published case studies.
if ( count( $published_ids ) >= 2 ) {
	foreach ( $published_ids as $pub_id ) {
		$others = array_values( array_filter( $published_ids, function( $id ) use ( $pub_id ) {
			return $id !== $pub_id;
		} ) );
		// Only set if not already linking to published peers.
		$current = get_field( 'cs_related_cases', $pub_id );
		$current_ids = [];
		if ( is_array( $current ) ) {
			$current_ids = array_map( function( $p ) { return $p instanceof WP_Post ? $p->ID : (int) $p; }, $current );
		}
		// Only update if current links differ from desired.
		sort( $others );
		sort( $current_ids );
		if ( $others !== $current_ids ) {
			update_field( 'cs_related_cases', $others, $pub_id );
			$pub_slug = get_post_field( 'post_name', $pub_id );
			echo "  {$pub_slug}: set cs_related_cases to peer published IDs " . implode( ', ', $others ) . "\n";
		}
	}
}

echo "  Done.\n";

// ─────────────────────────────────────────────────────────────────────────────
// POST-RUN VERIFICATION
// ─────────────────────────────────────────────────────────────────────────────
echo "\n── Post-run status verification ──────────────────────────\n\n";

foreach ( $targets as $wp_slug => $data ) {
	$post_id = $data['post_id'];
	$status  = get_post_status( $post_id );
	$action  = $data['action'];
	$intro   = get_field( 'cs_intro', $post_id );
	$has_content = ( $intro && strpos( $intro, 'SUMMIT_PLACEHOLDER' ) === false ) ? 'YES' : 'placeholder';

	$slug_pad = str_pad( $wp_slug, 45 );
	echo "  {$slug_pad} {$status}  content: {$has_content}  action: {$action}\n";
}

// Safety check: pre-existing published posts.
echo "\n── Pre-existing case study safety check ──────────────────\n\n";

$target_ids        = array_column( $targets, 'post_id' );
$pre_existing_safe = [];
$pre_existing_changed = [];

foreach ( $pre_existing_published_ids as $pre_id ) {
	if ( in_array( $pre_id, $target_ids, true ) ) {
		continue; // One of our targets.
	}
	$pre_post = get_post( $pre_id );
	if ( $pre_post && $pre_post->post_status === 'publish' ) {
		$pre_existing_safe[] = $pre_post->post_name;
	} elseif ( $pre_post ) {
		$pre_existing_changed[] = $pre_post->post_name . " (now: {$pre_post->post_status})";
	}
}

if ( ! empty( $pre_existing_safe ) ) {
	echo "  Untouched pre-existing published case studies:\n";
	foreach ( $pre_existing_safe as $slug ) {
		echo "    - {$slug}\n";
	}
}
if ( ! empty( $pre_existing_changed ) ) {
	echo "  [WARNING] Pre-existing case studies with changed status:\n";
	foreach ( $pre_existing_changed as $info ) {
		echo "    - {$info}\n";
	}
} else {
	echo "  [OK] No pre-existing published case studies were affected.\n";
}

// ─────────────────────────────────────────────────────────────────────────────
// SUMMARY
// ─────────────────────────────────────────────────────────────────────────────
echo "\n══════════════════════════════════════════════════════════\n";
echo "Batch 15 Population Summary\n";
echo "══════════════════════════════════════════════════════════\n";
echo "  Populated:         {$populated}\n";
echo "  Published:         {$published}\n";
echo "  Already published: {$already_pub}\n";
echo "  Held (draft):      {$held}\n";
echo "  Failed:            {$failed}\n";
echo "══════════════════════════════════════════════════════════\n";

if ( ! empty( $article_resolved ) ) {
	echo "\n  Related articles resolved: " . implode( ', ', $article_resolved ) . "\n";
}
if ( ! empty( $article_missing ) ) {
	echo "  Related articles missing:  " . implode( ', ', $article_missing ) . "\n";
}

if ( ! empty( $failed_list ) ) {
	echo "\nFailed:\n";
	foreach ( $failed_list as $f ) {
		echo "  - {$f}\n";
	}
}

echo "\n── Published slugs ──────────────────────────────────────\n\n";
foreach ( $targets as $wp_slug => $data ) {
	if ( get_post_status( $data['post_id'] ) === 'publish' ) {
		echo "  {$wp_slug}\n";
	}
}

echo "\n── Draft slugs ──────────────────────────────────────────\n\n";
foreach ( $targets as $wp_slug => $data ) {
	if ( get_post_status( $data['post_id'] ) !== 'publish' ) {
		echo "  {$wp_slug}\n";
	}
}

echo "\n══════════════════════════════════════════════════════════\n";
echo "Done.\n";
echo "══════════════════════════════════════════════════════════\n\n";
