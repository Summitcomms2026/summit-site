<?php
/**
 * Custom Post Type Registration — Summit Communication Group
 *
 * Registers all Summit CPTs in PHP so they are portable across
 * environments without relying on ACF UI database state.
 *
 * Canonical public URL structure:
 *   /work-showcase                                  case_study archive
 *   /work-showcase/[project-slug]                   case_study single
 *   /the-future-of-luxury                           article archive
 *   /the-future-of-luxury/[article-slug]            article single
 *   /tastemakers                                    podcast_season archive
 *   /tastemakers/[season-slug]                      podcast_season single
 *   /tastemakers/episode/[episode-slug]             podcast_episode single
 *   /downloads/[download-slug]                      download single
 *
 * Landing pages that previously collided with CPT slugs (work, future-of-luxury,
 * tastemakers) have been removed. The CPT archive IS the landing experience.
 *
 * @package SummitCore
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'summit_register_post_types' );

function summit_register_post_types(): void {

	// ── Case Study ───────────────────────────────────────────────────────────
	register_post_type( 'case_study', [
		'labels'       => [
			'name'               => 'Case Studies',
			'singular_name'      => 'Case Study',
			'add_new_item'       => 'Add New Case Study',
			'edit_item'          => 'Edit Case Study',
			'view_item'          => 'View Case Study',
			'search_items'       => 'Search Case Studies',
			'not_found'          => 'No case studies found.',
			'not_found_in_trash' => 'No case studies found in Trash.',
		],
		'public'       => true,
		'has_archive'  => 'work-showcase',
		'rewrite'      => [ 'slug' => 'work-showcase', 'with_front' => false ],
		'menu_icon'    => 'dashicons-portfolio',
		'supports'     => [ 'title', 'thumbnail', 'revisions' ],
		'show_in_rest' => true,
	] );

	// ── Article ──────────────────────────────────────────────────────────────
	register_post_type( 'article', [
		'labels'       => [
			'name'               => 'Articles',
			'singular_name'      => 'Article',
			'add_new_item'       => 'Add New Article',
			'edit_item'          => 'Edit Article',
			'view_item'          => 'View Article',
			'search_items'       => 'Search Articles',
			'not_found'          => 'No articles found.',
			'not_found_in_trash' => 'No articles found in Trash.',
		],
		'public'       => true,
		'has_archive'  => 'the-future-of-luxury',
		'rewrite'      => [ 'slug' => 'the-future-of-luxury', 'with_front' => false ],
		'menu_icon'    => 'dashicons-media-text',
		'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ],
		'show_in_rest' => true,
	] );

	// ── Podcast Season ───────────────────────────────────────────────────────
	register_post_type( 'podcast_season', [
		'labels'       => [
			'name'               => 'Podcast Seasons',
			'singular_name'      => 'Podcast Season',
			'add_new_item'       => 'Add New Season',
			'edit_item'          => 'Edit Season',
			'view_item'          => 'View Season',
			'search_items'       => 'Search Seasons',
			'not_found'          => 'No seasons found.',
			'not_found_in_trash' => 'No seasons found in Trash.',
		],
		'public'       => true,
		'has_archive'  => 'tastemakers',
		'rewrite'      => [ 'slug' => 'tastemakers', 'with_front' => false ],
		'menu_icon'    => 'dashicons-playlist-audio',
		'supports'     => [ 'title', 'thumbnail', 'revisions' ],
		'show_in_rest' => true,
	] );

	// ── Podcast Episode ──────────────────────────────────────────────────────
	register_post_type( 'podcast_episode', [
		'labels'       => [
			'name'               => 'Podcast Episodes',
			'singular_name'      => 'Podcast Episode',
			'add_new_item'       => 'Add New Episode',
			'edit_item'          => 'Edit Episode',
			'view_item'          => 'View Episode',
			'search_items'       => 'Search Episodes',
			'not_found'          => 'No episodes found.',
			'not_found_in_trash' => 'No episodes found in Trash.',
		],
		'public'       => true,
		'has_archive'  => false,
		'rewrite'      => [ 'slug' => 'tastemakers/episode', 'with_front' => false ],
		'menu_icon'    => 'dashicons-microphone',
		'supports'     => [ 'title', 'thumbnail', 'revisions' ],
		'show_in_rest' => true,
	] );

	// ── Download ─────────────────────────────────────────────────────────────
	register_post_type( 'download', [
		'labels'       => [
			'name'               => 'Downloads',
			'singular_name'      => 'Download',
			'add_new_item'       => 'Add New Download',
			'edit_item'          => 'Edit Download',
			'view_item'          => 'View Download',
			'search_items'       => 'Search Downloads',
			'not_found'          => 'No downloads found.',
			'not_found_in_trash' => 'No downloads found in Trash.',
		],
		'public'       => true,
		'has_archive'  => false,
		'rewrite'      => [ 'slug' => 'downloads', 'with_front' => false ],
		'menu_icon'    => 'dashicons-download',
		'supports'     => [ 'title', 'thumbnail', 'revisions' ],
		'show_in_rest' => true,
	] );

	// ── Enquiry (private / admin-only) ───────────────────────────────────────
	register_post_type( 'enquiry', [
		'labels'       => [
			'name'               => 'Enquiries',
			'singular_name'      => 'Enquiry',
			'edit_item'          => 'View Enquiry',
			'search_items'       => 'Search Enquiries',
			'not_found'          => 'No enquiries found.',
		],
		'public'             => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'has_archive'        => false,
		'rewrite'            => false,
		'menu_icon'          => 'dashicons-email-alt',
		'supports'           => [ 'title' ],
		'show_in_rest'       => false,
		'capabilities'       => [
			'create_posts' => 'do_not_allow',
		],
		'map_meta_cap'       => true,
	] );
}

/**
 * Ensure episode rules are checked before season rules.
 *
 * Both podcast_season (tastemakers/[slug]) and podcast_episode
 * (tastemakers/episode/[slug]) share the tastemakers prefix.
 * WordPress generates correct rules for both, but the season
 * catch-all can match the 'episode' literal segment first.
 * This filter moves episode rules above season rules.
 */
add_filter( 'rewrite_rules_array', 'summit_prioritise_episode_rules' );

function summit_prioritise_episode_rules( array $rules ): array {
	$episode_rules = [];
	$other_rules   = [];

	foreach ( $rules as $pattern => $query ) {
		if ( str_starts_with( $pattern, 'tastemakers/episode/' ) ) {
			$episode_rules[ $pattern ] = $query;
		} else {
			$other_rules[ $pattern ] = $query;
		}
	}

	return $episode_rules + $other_rules;
}

/**
 * Redirect provisional dev slugs to canonical URLs.
 *
 * The seeder originally created landing pages at /work, /future-of-luxury,
 * and /tastemakers. Those pages have been removed in favour of CPT archives
 * at the canonical slugs. This catches any lingering requests to the old
 * paths and issues a permanent redirect.
 */
/*
 * Note on provisional dev slugs:
 *
 * The seeder originally created landing pages at /work, /future-of-luxury
 * and /tastemakers. Those pages have been removed in favour of CPT archives
 * at canonical URLs: /work-showcase, /the-future-of-luxury, /tastemakers.
 *
 * Old provisional paths now return 404. These were dev-only paths that
 * were never publicly deployed. No redirect is needed.
 */
