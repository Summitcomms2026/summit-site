<?php
/**
 * Batch 14 — Editorial Readiness Check
 *
 * Read-only QA script. Checks all 21 Batch 11 cornerstone articles
 * for publication readiness. Reports blockers and warnings separately.
 * Does not modify any content or metadata.
 *
 * Run via WP-CLI:
 *   wp eval-file plugins/summit-core/check-editorial-readiness.php --path=app/public
 *
 * @package SummitCore
 */

defined( 'ABSPATH' ) || exit;

echo "\n══════════════════════════════════════════════════════════\n";
echo "Batch 14 — Editorial Readiness Check\n";
echo "══════════════════════════════════════════════════════════\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 1: Load Batch 11 manifest
// ─────────────────────────────────────────────────────────────────────────────
echo "── Preflight checks ──────────────────────────────────────\n\n";

$manifest_path = dirname( __FILE__ ) . '/data/batch-11-launch-set.json';
if ( ! file_exists( $manifest_path ) || ! is_readable( $manifest_path ) ) {
	echo "[ABORT] Batch 11 manifest missing or unreadable: {$manifest_path}\n";
	return;
}

$manifest = json_decode( file_get_contents( $manifest_path ), true );
if ( ! is_array( $manifest ) || empty( $manifest ) ) {
	echo "[ABORT] Batch 11 manifest is empty or invalid JSON.\n";
	return;
}

echo "[OK] Manifest loaded: " . count( $manifest ) . " entries.\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 2: ACF availability
// ─────────────────────────────────────────────────────────────────────────────
if ( ! function_exists( 'get_field' ) ) {
	echo "[ABORT] ACF is not active.\n";
	return;
}
echo "[OK] ACF is active.\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 3: Resolve all target posts
// ─────────────────────────────────────────────────────────────────────────────
$targets       = [];
$resolve_fails = [];

foreach ( $manifest as $m_entry ) {
	$wp_slug     = $m_entry['wp_slug'];
	$import_slug = $m_entry['import_slug'];

	$q = new WP_Query( [
		'post_type'      => 'article',
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

	$stored_import_slug = get_post_meta( $post_id, '_summit_import_slug', true );
	if ( $stored_import_slug !== $import_slug ) {
		$resolve_fails[] = "{$wp_slug} (ID {$post_id}): _summit_import_slug mismatch";
		continue;
	}

	$targets[ $wp_slug ] = [
		'post_id'     => $post_id,
		'post'        => $post,
		'import_slug' => $import_slug,
		'category'    => $m_entry['article_category'] ?? '',
	];
}

if ( ! empty( $resolve_fails ) ) {
	echo "[ABORT] Could not resolve all target posts:\n";
	foreach ( $resolve_fails as $fail ) {
		echo "  - {$fail}\n";
	}
	return;
}

echo "[OK] All " . count( $targets ) . " target posts resolved.\n";

echo "\n── All preflight checks passed ───────────────────────────\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// EDITORIAL CHECKS
// ─────────────────────────────────────────────────────────────────────────────
echo "── Running editorial checks ─────────────────────────────\n\n";

$results          = [];
$all_blockers     = [];
$all_warnings     = [];
$blocker_count    = 0;
$warning_count    = 0;
$pass_count       = 0;

$i = 0;
foreach ( $targets as $wp_slug => $data ) {
	$i++;
	$post_id  = $data['post_id'];
	$post     = $data['post'];
	$blockers = [];
	$warnings = [];

	// 1. Standfirst.
	$standfirst = get_field( 'art_standfirst', $post_id );
	if ( empty( $standfirst ) ) {
		$blockers[] = 'Missing standfirst';
	} else {
		$sf_len = mb_strlen( $standfirst );
		if ( $sf_len < 50 ) {
			$warnings[] = "Standfirst short ({$sf_len} chars, prefer 50+)";
		} elseif ( $sf_len > 400 ) {
			$warnings[] = "Standfirst long ({$sf_len} chars, prefer ≤400)";
		}
	}

	// 2. Read time.
	$read_time = get_field( 'art_read_time', $post_id );
	if ( empty( $read_time ) || (int) $read_time <= 0 ) {
		$warnings[] = 'Read time missing or zero';
	}

	// 3. Category assignment.
	$cat_terms = get_the_terms( $post_id, 'article_category' );
	if ( is_wp_error( $cat_terms ) || empty( $cat_terms ) ) {
		$blockers[] = 'Missing category assignment';
	} else {
		// Cross-check with manifest expected category.
		// WordPress stores & as &amp; in term names, so decode for comparison.
		$assigned_names = array_map( 'html_entity_decode', wp_list_pluck( $cat_terms, 'name' ) );
		$expected_cat   = html_entity_decode( $data['category'] );
		if ( $expected_cat && ! in_array( $expected_cat, $assigned_names, true ) ) {
			$warnings[] = "Category mismatch: expected '{$expected_cat}', got '" . implode( ', ', $assigned_names ) . "'";
		}
	}

	// 4. Featured image.
	if ( ! has_post_thumbnail( $post_id ) ) {
		$blockers[] = 'Missing featured image';
	}

	// 5. OG image.
	$og_image = get_field( 'art_og_image', $post_id );
	if ( empty( $og_image ) ) {
		$blockers[] = 'Missing art_og_image';
	} else {
		$og_url = is_array( $og_image ) ? ( $og_image['url'] ?? '' ) : '';
		if ( $og_url && strpos( $og_url, '/wp-content/uploads/' ) === false ) {
			$warnings[] = "OG image URL may be external: {$og_url}";
		}
	}

	// 6. SEO title.
	$seo_title = get_field( 'art_seo_title', $post_id );
	if ( empty( $seo_title ) ) {
		$blockers[] = 'Missing SEO title';
	}

	// 7. Meta description.
	$meta_desc = get_field( 'art_meta_desc', $post_id );
	if ( empty( $meta_desc ) ) {
		$blockers[] = 'Missing meta description';
	}

	// 8. Post content.
	if ( empty( trim( $post->post_content ) ) ) {
		$blockers[] = 'Empty post content';
	} else {
		$word_count = str_word_count( wp_strip_all_tags( $post->post_content ) );
		if ( $word_count < 200 ) {
			$warnings[] = "Content short ({$word_count} words)";
		}
	}

	// Record results.
	$slug_pad = str_pad( $wp_slug, 65 );
	if ( ! empty( $blockers ) ) {
		echo "  [{$i}/" . count( $targets ) . "] {$slug_pad} BLOCKED\n";
		foreach ( $blockers as $b ) {
			echo "    [BLOCKER] {$b}\n";
		}
		$blocker_count += count( $blockers );
		$all_blockers[ $wp_slug ] = $blockers;
	} elseif ( ! empty( $warnings ) ) {
		echo "  [{$i}/" . count( $targets ) . "] {$slug_pad} WARN\n";
		$pass_count++;
	} else {
		echo "  [{$i}/" . count( $targets ) . "] {$slug_pad} PASS\n";
		$pass_count++;
	}

	foreach ( $warnings as $w ) {
		echo "    [warn] {$w}\n";
	}
	if ( ! empty( $warnings ) ) {
		$warning_count += count( $warnings );
		$all_warnings[ $wp_slug ] = $warnings;
	}

	$results[ $wp_slug ] = [
		'blockers' => $blockers,
		'warnings' => $warnings,
	];
}

// ─────────────────────────────────────────────────────────────────────────────
// GROUPED SUMMARY
// ─────────────────────────────────────────────────────────────────────────────
echo "\n══════════════════════════════════════════════════════════\n";
echo "Editorial Readiness Summary\n";
echo "══════════════════════════════════════════════════════════\n";
echo "  Total articles:    " . count( $targets ) . "\n";
echo "  Pass (no blockers): {$pass_count}\n";
echo "  Total blockers:    {$blocker_count}\n";
echo "  Total warnings:    {$warning_count}\n";
echo "══════════════════════════════════════════════════════════\n";

if ( ! empty( $all_blockers ) ) {
	echo "\n── Blockers by article ──────────────────────────────────\n\n";
	foreach ( $all_blockers as $slug => $issues ) {
		echo "  {$slug}:\n";
		foreach ( $issues as $issue ) {
			echo "    - {$issue}\n";
		}
	}
}

if ( ! empty( $all_warnings ) ) {
	echo "\n── Warnings by article ──────────────────────────────────\n\n";
	foreach ( $all_warnings as $slug => $issues ) {
		echo "  {$slug}:\n";
		foreach ( $issues as $issue ) {
			echo "    - {$issue}\n";
		}
	}
}

// Group issues by type for easy batch fixing.
if ( ! empty( $all_blockers ) || ! empty( $all_warnings ) ) {
	echo "\n── Issues grouped by type ───────────────────────────────\n\n";

	$by_type = [];
	foreach ( $all_blockers as $slug => $issues ) {
		foreach ( $issues as $issue ) {
			$by_type[ $issue ][] = $slug;
		}
	}
	foreach ( $all_warnings as $slug => $issues ) {
		foreach ( $issues as $issue ) {
			$by_type[ $issue ][] = $slug;
		}
	}

	foreach ( $by_type as $issue => $slugs ) {
		echo "  {$issue} (" . count( $slugs ) . "):\n";
		foreach ( $slugs as $slug ) {
			echo "    - {$slug}\n";
		}
		echo "\n";
	}
}

$verdict = ( $blocker_count === 0 ) ? 'READY TO PUBLISH' : 'NOT READY — fix blockers first';
echo "══════════════════════════════════════════════════════════\n";
echo "Verdict: {$verdict}\n";
echo "══════════════════════════════════════════════════════════\n\n";
