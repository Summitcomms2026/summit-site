<?php
/**
 * Batch 11 — Editorial Cornerstone Import
 *
 * Imports a curated launch set of Future of Luxury articles from the
 * prepared dataset, controlled by a manifest file.
 *
 * Run via WP-CLI:
 *   wp eval-file plugins/summit-core/import-articles.php /path/to/articles_import_ready.json --path=app/public
 *
 * Or define the constant SUMMIT_IMPORT_JSON before running:
 *   wp eval-file plugins/summit-core/import-articles.php --path=app/public
 *
 * Dev-only. Local-only. Do not deploy to production.
 *
 * @package SummitCore
 */

defined( 'ABSPATH' ) || exit;

// ─────────────────────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Generate a standfirst from body text.
 * Takes first ~300 chars, trims to last sentence boundary.
 */
function summit_generate_standfirst( string $body_text ): string {
	$text = trim( strip_tags( $body_text ) );
	if ( strlen( $text ) <= 300 ) {
		return $text;
	}
	$excerpt = substr( $text, 0, 380 );
	// Find last sentence boundary (. ! ? followed by space or end).
	if ( preg_match( '/^(.+[.!?])(?:\s|$)/Us', $excerpt, $m ) ) {
		$result = $m[1];
		if ( strlen( $result ) >= 60 ) {
			return substr( $result, 0, 400 );
		}
	}
	// Fallback: trim to last space within 300 chars.
	$trimmed = substr( $text, 0, 300 );
	$last_space = strrpos( $trimmed, ' ' );
	if ( $last_space ) {
		$trimmed = substr( $trimmed, 0, $last_space );
	}
	return $trimmed . '…';
}

/**
 * Build SEO title: "{title} | The Future of Luxury" truncated to 60 chars.
 */
function summit_seo_title( string $title ): string {
	$suffix = ' | The Future of Luxury';
	$max    = 60;
	$full   = $title . $suffix;
	if ( strlen( $full ) <= $max ) {
		return $full;
	}
	$available = $max - strlen( $suffix ) - 1; // -1 for ellipsis
	if ( $available < 10 ) {
		return substr( $full, 0, $max );
	}
	return substr( $title, 0, $available ) . '…' . $suffix;
}

echo "\n══════════════════════════════════════════════════════════\n";
echo "Batch 11 — Editorial Cornerstone Import\n";
echo "══════════════════════════════════════════════════════════\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 1: Dataset path resolution (deterministic)
// ─────────────────────────────────────────────────────────────────────────────
echo "── Preflight checks ──────────────────────────────────────\n\n";

$dataset_path = null;
if ( isset( $args ) && ! empty( $args[0] ) ) {
	$dataset_path = $args[0];
} elseif ( defined( 'SUMMIT_IMPORT_JSON' ) ) {
	$dataset_path = SUMMIT_IMPORT_JSON;
}

if ( ! $dataset_path ) {
	echo "[ABORT] No dataset path provided.\n";
	echo "  Pass path as CLI argument:\n";
	echo "    wp eval-file plugins/summit-core/import-articles.php /path/to/articles_import_ready.json\n";
	echo "  Or define constant SUMMIT_IMPORT_JSON.\n";
	return;
}

if ( ! file_exists( $dataset_path ) || ! is_readable( $dataset_path ) ) {
	echo "[ABORT] Dataset file missing or unreadable: {$dataset_path}\n";
	return;
}

echo "[OK] Dataset path: {$dataset_path}\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 2: ACF availability
// ─────────────────────────────────────────────────────────────────────────────
if ( ! function_exists( 'update_field' ) ) {
	echo "[ABORT] ACF is not active. update_field() not available.\n";
	echo "  Ensure Advanced Custom Fields plugin is active.\n";
	return;
}
echo "[OK] ACF is active.\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 3: Author resolution
// ─────────────────────────────────────────────────────────────────────────────
$author_id = null;

// Try display_name first.
$users = get_users( [ 'search' => 'Gregory Gray', 'search_columns' => [ 'display_name' ] ] );
if ( ! empty( $users ) ) {
	$author_id = $users[0]->ID;
}

// Try user_login.
if ( ! $author_id ) {
	$user = get_user_by( 'login', 'gregory.gray' );
	if ( $user ) {
		$author_id = $user->ID;
	}
}
if ( ! $author_id ) {
	$user = get_user_by( 'login', 'gregorygray' );
	if ( $user ) {
		$author_id = $user->ID;
	}
}

// Try email containing gregory.
if ( ! $author_id ) {
	$users = get_users( [ 'search' => '*gregory*', 'search_columns' => [ 'user_email' ] ] );
	if ( ! empty( $users ) ) {
		$author_id = $users[0]->ID;
	}
}

if ( ! $author_id ) {
	echo "[ABORT] Cannot resolve author 'Gregory Gray'.\n";
	echo "  Searched: display_name, user_login (gregory.gray, gregorygray), email (*gregory*).\n";
	echo "  Do NOT fall back to admin or user ID 1.\n";
	return;
}
echo "[OK] Author resolved: Gregory Gray (user ID {$author_id}).\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 4: Read dataset
// ─────────────────────────────────────────────────────────────────────────────
$dataset_raw = file_get_contents( $dataset_path );
$dataset     = json_decode( $dataset_raw, true );

if ( ! is_array( $dataset ) || empty( $dataset ) ) {
	echo "[ABORT] Dataset is empty or invalid JSON.\n";
	return;
}

// Index dataset by import_slug.
$dataset_index = [];
foreach ( $dataset as $record ) {
	if ( isset( $record['import_slug'] ) ) {
		$dataset_index[ $record['import_slug'] ] = $record;
	}
}
echo "[OK] Dataset loaded: " . count( $dataset_index ) . " articles indexed.\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 5: Read manifest
// ─────────────────────────────────────────────────────────────────────────────
$manifest_path = dirname( __FILE__ ) . '/data/batch-11-launch-set.json';
if ( ! file_exists( $manifest_path ) ) {
	echo "[ABORT] Manifest missing: {$manifest_path}\n";
	return;
}

$manifest_raw = file_get_contents( $manifest_path );
$manifest     = json_decode( $manifest_raw, true );

if ( ! is_array( $manifest ) || empty( $manifest ) ) {
	echo "[ABORT] Manifest is empty or invalid JSON.\n";
	return;
}
echo "[OK] Manifest loaded: " . count( $manifest ) . " articles selected.\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 6: Cross-reference manifest → dataset
// ─────────────────────────────────────────────────────────────────────────────
$missing_slugs = [];
foreach ( $manifest as $entry ) {
	if ( ! isset( $dataset_index[ $entry['import_slug'] ] ) ) {
		$missing_slugs[] = $entry['import_slug'];
	}
}
if ( ! empty( $missing_slugs ) ) {
	echo "[ABORT] Manifest slugs not found in dataset:\n";
	foreach ( $missing_slugs as $s ) {
		echo "  - {$s}\n";
	}
	return;
}
echo "[OK] All manifest slugs found in dataset.\n";

// ─────────────────────────────────────────────────────────────────────────────
// PREFLIGHT 7: Taxonomy term verification
// ─────────────────────────────────────────────────────────────────────────────
$required_cats = array_unique( array_column( $manifest, 'article_category' ) );
$missing_cats  = [];
foreach ( $required_cats as $cat_name ) {
	$term = get_term_by( 'name', $cat_name, 'article_category' );
	if ( ! $term ) {
		$missing_cats[] = $cat_name;
	}
}
if ( ! empty( $missing_cats ) ) {
	echo "[ABORT] Missing article_category terms (not auto-created):\n";
	foreach ( $missing_cats as $c ) {
		echo "  - {$c}\n";
	}
	echo "  Create these terms before running the import.\n";
	return;
}
echo "[OK] All " . count( $required_cats ) . " article_category terms exist.\n";

echo "\n── All preflight checks passed ───────────────────────────\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// IMPORT PASS
// ─────────────────────────────────────────────────────────────────────────────
echo "── Importing articles ────────────────────────────────────\n\n";

$created  = 0;
$skipped  = 0;
$errors   = 0;
$post_map = []; // wp_slug → post_id for relationship pass.

foreach ( $manifest as $i => $entry ) {
	$import_slug = $entry['import_slug'];
	$wp_slug     = rtrim( $entry['wp_slug'] ?? $import_slug, '-' );
	$record      = $dataset_index[ $import_slug ];
	$num         = $i + 1;

	echo "  [{$num}/" . count( $manifest ) . "] {$wp_slug}\n";

	// Idempotency check.
	$existing = get_posts( [
		'post_type'   => 'article',
		'post_status' => 'any',
		'meta_key'    => '_summit_import_slug',
		'meta_value'  => $import_slug,
		'numberposts' => 1,
	] );
	if ( ! empty( $existing ) ) {
		echo "    [skip] Already exists (ID {$existing[0]->ID}).\n";
		$skipped++;
		$post_map[ $wp_slug ] = $existing[0]->ID;
		continue;
	}

	// Prepare fields.
	$title      = $record['article_title'] ?? '';
	$body_html  = $record['body_html_final'] ?? '';
	$body_text  = $record['body_text'] ?? strip_tags( $body_html );
	$word_count = (int) ( $record['word_count'] ?? str_word_count( $body_text ) );
	$eff_date   = $record['effective_date'] ?? '';
	$linkedin   = $record['linkedin_url'] ?? '';
	$hero_url   = $record['hero_image_url'] ?? '';
	$hero_alt   = $record['hero_image_alt'] ?? '';

	// Standfirst.
	$standfirst = '';
	if ( ! empty( $entry['standfirst_override'] ) ) {
		$standfirst = $entry['standfirst_override'];
	} else {
		$standfirst = summit_generate_standfirst( $body_text );
	}

	// Post date.
	$post_date = '';
	if ( $eff_date ) {
		$post_date = $eff_date . ' 00:00:00';
	}

	// Create post.
	$post_id = wp_insert_post( [
		'post_type'    => 'article',
		'post_title'   => $title,
		'post_name'    => $wp_slug,
		'post_content' => $body_html,
		'post_date'    => $post_date,
		'post_author'  => $author_id,
		'post_status'  => 'draft',
	], true );

	if ( is_wp_error( $post_id ) ) {
		echo "    [error] {$post_id->get_error_message()}\n";
		$errors++;
		continue;
	}

	// Machine meta (not ACF).
	update_post_meta( $post_id, '_summit_import_slug', $import_slug );
	update_post_meta( $post_id, '_summit_source_url', $linkedin );
	update_post_meta( $post_id, '_summit_hero_url', $hero_url );
	update_post_meta( $post_id, '_summit_hero_alt', $hero_alt );

	// ACF fields.
	$read_time = (int) ceil( $word_count / 238 );
	$seo_title = summit_seo_title( $title );
	$meta_desc = mb_substr( $standfirst, 0, 160 );

	update_field( 'art_standfirst',       $standfirst, $post_id );
	update_field( 'art_read_time',        $read_time, $post_id );
	update_field( 'art_cornerstone',      $entry['cornerstone'] ? 1 : 0, $post_id );
	update_field( 'art_featured',         $entry['featured'] ? 1 : 0, $post_id );
	update_field( 'art_subscribe_prompt',  1, $post_id );
	update_field( 'art_seo_title',        $seo_title, $post_id );
	update_field( 'art_meta_desc',        $meta_desc, $post_id );
	update_field( 'art_canonical',        '', $post_id );
	update_field( 'art_source_note',      "Imported from Future of Luxury editorial archive. Source: {$linkedin}", $post_id );

	// Taxonomy.
	$cat_name = $entry['article_category'];
	$term     = get_term_by( 'name', $cat_name, 'article_category' );
	if ( $term ) {
		wp_set_object_terms( $post_id, (int) $term->term_id, 'article_category' );
	}

	echo "    [created] ID {$post_id} — {$title}\n";
	$created++;
	$post_map[ $wp_slug ] = $post_id;
}

echo "\n── Import pass complete ──────────────────────────────────\n";
echo "  Created: {$created} | Skipped: {$skipped} | Errors: {$errors}\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// RELATIONSHIP PASS
// ─────────────────────────────────────────────────────────────────────────────
echo "── Setting relationships ─────────────────────────────────\n\n";

$rels_set = 0;
foreach ( $manifest as $entry ) {
	$wp_slug      = rtrim( $entry['wp_slug'] ?? $entry['import_slug'], '-' );
	$related_slugs = $entry['related_slugs'] ?? [];

	if ( empty( $related_slugs ) || ! isset( $post_map[ $wp_slug ] ) ) {
		continue;
	}

	$post_id     = $post_map[ $wp_slug ];
	$related_ids = [];

	foreach ( $related_slugs as $rel_slug ) {
		if ( isset( $post_map[ $rel_slug ] ) ) {
			$related_ids[] = $post_map[ $rel_slug ];
		} else {
			// Try lookup by post_name.
			$found = get_posts( [
				'post_type'   => 'article',
				'post_status' => 'any',
				'name'        => $rel_slug,
				'numberposts' => 1,
			] );
			if ( ! empty( $found ) ) {
				$related_ids[] = $found[0]->ID;
				$post_map[ $rel_slug ] = $found[0]->ID;
			}
		}
	}

	if ( ! empty( $related_ids ) ) {
		update_field( 'art_related_articles', $related_ids, $post_id );
		echo "  [{$wp_slug}] → " . count( $related_ids ) . " related article(s)\n";
		$rels_set++;
	}
}

echo "\n  Relationships set: {$rels_set}\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// PLACEHOLDER CLEANUP (only on full success)
// ─────────────────────────────────────────────────────────────────────────────
$expected_count = count( $manifest );
$placeholder_slugs = [
	'placeholder-article-luxury-strategy',
	'placeholder-article-brand-positioning',
	'placeholder-article-luxury-technology',
	'placeholder-article-culture-media',
];

if ( $errors === 0 && ( $created + $skipped ) === $expected_count ) {
	echo "── Placeholder cleanup ──────────────────────────────────\n\n";
	$deleted = 0;
	foreach ( $placeholder_slugs as $ph_slug ) {
		$ph = get_page_by_path( $ph_slug, OBJECT, 'article' );
		if ( $ph ) {
			wp_delete_post( $ph->ID, true );
			echo "  [deleted] {$ph_slug} (ID {$ph->ID})\n";
			$deleted++;
		} else {
			echo "  [skip] {$ph_slug} — not found.\n";
		}
	}
	echo "\n  Placeholders deleted: {$deleted}\n\n";
} else {
	echo "── Placeholder cleanup SKIPPED ──────────────────────────\n";
	echo "  Import had errors or incomplete count. Placeholders preserved.\n\n";
}

// ─────────────────────────────────────────────────────────────────────────────
// SUMMARY
// ─────────────────────────────────────────────────────────────────────────────
echo "══════════════════════════════════════════════════════════\n";
echo "Batch 11 Import Summary\n";
echo "══════════════════════════════════════════════════════════\n";
echo "  Articles created:       {$created}\n";
echo "  Articles skipped:       {$skipped}\n";
echo "  Articles errored:       {$errors}\n";
echo "  Relationships set:      {$rels_set}\n";
echo "  Expected total:         {$expected_count}\n";
echo "══════════════════════════════════════════════════════════\n\n";
