<?php
/**
 * Batch 7 Content Seeder — Summit Communication Group
 *
 * Adapted for the Local/GitHub repo architecture:
 *   - Homepage fields use home_* prefix (matching block partials)
 *   - CPTs are registered via ACF UI (no post-types.php)
 *   - Taxonomy slugs: /topic, /theme (repo-specific)
 *
 * Source hierarchy:
 *   1. Local/GitHub repo = implementation truth for templates, routes, CPTs, field names
 *   2. Website_Summit Communication Group-V4.docx = primary truth for page copy
 *   3. md governance docs = structural truth for content model, schema, QA
 *
 * Run via WP-CLI:   wp eval-file plugins/summit-core/seed-content.php
 *
 * Dev-only. Local-only. Do not deploy to production.
 * Idempotent — checks for existing content before creating.
 *
 * @package SummitCore
 */

defined( 'ABSPATH' ) || exit;

// ─────────────────────────────────────────────────────────────────────────────
// Guard: only run once (uses different key from Dropbox seeder)
// ─────────────────────────────────────────────────────────────────────────────
if ( get_option( 'summit_seed_v7_local_complete' ) ) {
	echo "Batch 7 local seed already run. Delete option 'summit_seed_v7_local_complete' to re-run.\n";
	return;
}

// Placeholder constant — used only where V4 doc is silent
if ( ! defined( 'SUMMIT_PLACEHOLDER' ) ) {
	define( 'SUMMIT_PLACEHOLDER', '[TEMP PLACEHOLDER — replace with real content before launch]' );
}

// ─────────────────────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────────────────────
function summit_seed_post( string $post_type, string $slug, array $args ): int {
	$existing = get_page_by_path( $slug, OBJECT, $post_type );
	if ( $existing ) {
		echo "  [skip] {$post_type}: {$slug} (ID {$existing->ID})\n";
		return $existing->ID;
	}
	$defaults = [
		'post_type'   => $post_type,
		'post_name'   => basename( $slug ),
		'post_status' => 'publish',
	];
	$post_id = wp_insert_post( array_merge( $defaults, $args ), true );
	if ( is_wp_error( $post_id ) ) {
		echo "  [error] {$post_type}: {$slug} — {$post_id->get_error_message()}\n";
		return 0;
	}
	echo "  [created] {$post_type}: {$slug} (ID {$post_id})\n";
	return $post_id;
}

function summit_seed_term( string $taxonomy, string $name, string $slug = '' ): int {
	if ( ! $slug ) {
		$slug = sanitize_title( $name );
	}
	$existing = get_term_by( 'slug', $slug, $taxonomy );
	if ( $existing ) {
		return $existing->term_id;
	}
	$result = wp_insert_term( $name, $taxonomy, [ 'slug' => $slug ] );
	if ( is_wp_error( $result ) ) {
		echo "  [error] term {$taxonomy}/{$slug}: {$result->get_error_message()}\n";
		return 0;
	}
	echo "  [created] term {$taxonomy}/{$slug}\n";
	return $result['term_id'];
}

echo "\n======================================================\n";
echo "Summit Batch 7 — Local Repo Seeder (home_* fields)\n";
echo "======================================================\n\n";


// ═════════════════════════════════════════════════════════════════════════════
// 1. TAXONOMY TERMS
// ═════════════════════════════════════════════════════════════════════════════
echo "-- Taxonomy Terms --\n";

$sectors = [];
foreach ( [
	'Hospitality & Travel',
	'Automotive & Yachts',
	'Fashion & Leather Goods',
	'Watches & Jewellery',
	'Perfumes & Cosmetics',
	'Wines & Spirits',
	'Lifestyle Technology',
	'Fine Art & Collectibles',
	'Real Estate',
	'Home & Furniture',
] as $name ) {
	$sectors[ sanitize_title( $name ) ] = summit_seed_term( 'sector', $name );
}

$service_types = [];
foreach ( [
	'Brand Strategy',
	'Experience Design',
	'Digital Transformation',
	'Campaign',
	'Film',
	'Podcast Identity',
] as $name ) {
	$service_types[ sanitize_title( $name ) ] = summit_seed_term( 'service_type', $name );
}

$article_cats = [];
foreach ( [
	'Luxury Strategy',
	'Brand & Positioning',
	'Luxury and Technology',
	'Culture & Media',
	'Hospitality & Travel',
	'Watches & Jewellery',
	'Fashion & Leather Goods',
	'Wealth, Value & Collecting',
	'Sport, Sponsorship & Status',
	'The New Luxury Customer',
] as $name ) {
	$article_cats[ sanitize_title( $name ) ] = summit_seed_term( 'article_category', $name );
}

$formats = [];
foreach ( [
	'Branding',
	'Activism Campaign',
	'Film & Authority Building',
	'Immersive Storytelling',
	'Podcast & Editorial Identity',
] as $name ) {
	$formats[ sanitize_title( $name ) ] = summit_seed_term( 'format', $name );
}

$podcast_themes = [];
foreach ( [ 'Craft', 'Experience' ] as $name ) {
	$podcast_themes[ sanitize_title( $name ) ] = summit_seed_term( 'podcast_theme', $name );
}


// ═════════════════════════════════════════════════════════════════════════════
// 2. WORDPRESS PAGES
// ═════════════════════════════════════════════════════════════════════════════
echo "\n-- WordPress Pages --\n";

$home_id = summit_seed_post( 'page', 'home', [
	'post_title' => 'Home',
] );

$who_we_are_id     = summit_seed_post( 'page', 'who-we-are', [ 'post_title' => 'Who We Are' ] );
$what_we_do_id     = summit_seed_post( 'page', 'what-we-do', [ 'post_title' => 'What We Do' ] );

// Child pages — use hierarchical paths for lookup
$brand_strategy_id = summit_seed_post( 'page', 'what-we-do/brand-strategy', [
	'post_title'  => 'Brand Strategy',
	'post_parent' => $what_we_do_id,
] );
$experience_design_id = summit_seed_post( 'page', 'what-we-do/experience-design', [
	'post_title'  => 'Experience Design',
	'post_parent' => $what_we_do_id,
] );
$digital_transformation_id = summit_seed_post( 'page', 'what-we-do/digital-transformation', [
	'post_title'  => 'Digital Transformation',
	'post_parent' => $what_we_do_id,
] );

$work_id           = summit_seed_post( 'page', 'work', [ 'post_title' => 'Work' ] );
$fol_id            = summit_seed_post( 'page', 'future-of-luxury', [ 'post_title' => 'The Future of Luxury' ] );
$tastemakers_id    = summit_seed_post( 'page', 'tastemakers', [ 'post_title' => 'Tastemakers' ] );
$design_tomorrow_id = summit_seed_post( 'page', 'design-tomorrow', [ 'post_title' => 'Design Tomorrow' ] );
$careers_id        = summit_seed_post( 'page', 'careers', [ 'post_title' => 'Careers' ] );
$client_lounge_id  = summit_seed_post( 'page', 'client-lounge', [ 'post_title' => 'Client Lounge' ] );

// Set static front page
if ( $home_id ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home_id );
	echo "  [set] Static front page -> Home (ID {$home_id})\n";
}


// ═════════════════════════════════════════════════════════════════════════════
// 3. CASE STUDIES
// Source: summit_content_model.md (names), V4 doc (summaries)
// ═════════════════════════════════════════════════════════════════════════════
echo "\n-- Case Studies --\n";

$case_studies = [
	[
		'slug'         => 'teddy-william',
		'title'        => 'Teddy William',
		'sector'       => 'fashion-leather-goods',
		'service_type' => 'brand-strategy',
		'format'       => 'branding',
		'fields'       => [
			'cs_summary'        => 'Brand strategy and identity for an independent British menswear label.',
			'cs_client'         => 'Teddy William',
			'cs_year'           => 2024,
			'cs_intro'          => SUMMIT_PLACEHOLDER,
			'cs_challenge'      => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_strategic_idea' => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_what_we_built'  => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_outcome'        => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_seo_title'      => 'Teddy William | Branding for Luxury Tailoring | Summit Communication Group',
			'cs_meta_desc'      => 'Brand strategy and identity for Teddy William, an independent British menswear label.',
		],
	],
	[
		'slug'         => 'the-fight-against-skin-cancer',
		'title'        => 'The Fight Against Skin Cancer',
		'sector'       => 'private-healthcare',
		'service_type' => 'campaign',
		'format'       => 'activism-campaign',
		'fields'       => [
			'cs_summary'        => 'An awareness campaign connecting leading dermatologists with a broader public audience.',
			'cs_intro'          => SUMMIT_PLACEHOLDER,
			'cs_challenge'      => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_strategic_idea' => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_what_we_built'  => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_outcome'        => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_seo_title'      => 'The Fight Against Skin Cancer | Awareness Campaign | Summit Communication Group',
			'cs_meta_desc'      => 'An integrated awareness campaign connecting leading dermatologists with a broader public audience.',
		],
	],
	[
		'slug'         => 'leading-plastic-surgeons-films',
		'title'        => 'Leading Plastic Surgeons Films',
		'sector'       => 'private-healthcare',
		'service_type' => 'film',
		'format'       => 'film-authority-building',
		'fields'       => [
			'cs_summary'        => 'A documentary film series profiling world-leading plastic surgeons.',
			'cs_intro'          => SUMMIT_PLACEHOLDER,
			'cs_challenge'      => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_strategic_idea' => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_what_we_built'  => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_outcome'        => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_seo_title'      => 'Leading Plastic Surgeons Films | Documentary Series | Summit Communication Group',
			'cs_meta_desc'      => 'A documentary film series profiling the philosophy and craft of world-leading plastic surgeons.',
		],
	],
	[
		'slug'         => 'kaleida-studios',
		'title'        => 'Kaleida Studios',
		'sector'       => 'lifestyle-technology',
		'service_type' => 'experience-design',
		'format'       => 'immersive-storytelling',
		'fields'       => [
			'cs_summary'        => 'Experience design and digital platform for a creative technology studio.',
			'cs_client'         => 'Kaleida Studios',
			'cs_intro'          => SUMMIT_PLACEHOLDER,
			'cs_challenge'      => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_strategic_idea' => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_what_we_built'  => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_outcome'        => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_seo_title'      => 'Kaleida Studios | Experience Design | Summit Communication Group',
			'cs_meta_desc'      => 'Experience design and digital platform for Kaleida Studios, a creative technology studio.',
		],
	],
	[
		'slug'         => 'tastemakers-podcast',
		'title'        => 'Tastemakers Podcast',
		'sector'       => 'culture-media',
		'service_type' => 'podcast-identity',
		'format'       => 'podcast-editorial-identity',
		'fields'       => [
			'cs_summary'        => 'Podcast identity and editorial architecture for the Tastemakers interview series.',
			'cs_client'         => 'Summit Communication Group',
			'cs_intro'          => SUMMIT_PLACEHOLDER,
			'cs_challenge'      => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_strategic_idea' => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_what_we_built'  => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_outcome'        => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_seo_title'      => 'Tastemakers Podcast | Identity & Editorial Architecture | Summit Communication Group',
			'cs_meta_desc'      => 'Podcast identity and editorial architecture for the Tastemakers interview series.',
		],
	],
];

$case_study_ids = [];
foreach ( $case_studies as $cs ) {
	$post_id = summit_seed_post( 'case_study', $cs['slug'], [
		'post_title' => $cs['title'],
	] );
	if ( ! $post_id ) { continue; }
	$case_study_ids[ $cs['slug'] ] = $post_id;

	if ( function_exists( 'update_field' ) ) {
		foreach ( $cs['fields'] as $key => $value ) {
			update_field( $key, $value, $post_id );
		}
	}
	if ( isset( $sectors[ $cs['sector'] ] ) ) {
		wp_set_object_terms( $post_id, (int) $sectors[ $cs['sector'] ], 'sector' );
	}
	if ( isset( $service_types[ $cs['service_type'] ] ) ) {
		wp_set_object_terms( $post_id, (int) $service_types[ $cs['service_type'] ], 'service_type' );
	}
	if ( isset( $formats[ $cs['format'] ] ) ) {
		wp_set_object_terms( $post_id, (int) $formats[ $cs['format'] ], 'format' );
	}
}


// ═════════════════════════════════════════════════════════════════════════════
// 4. ARTICLES (placeholder titles — V4 silent on article content)
// ═════════════════════════════════════════════════════════════════════════════
echo "\n-- Articles --\n";

$articles = [
	[
		'slug'     => 'placeholder-article-luxury-strategy',
		'title'    => '[TEMP PLACEHOLDER] Article — Luxury Strategy',
		'category' => 'luxury-strategy',
		'fields'   => [
			'art_standfirst'       => SUMMIT_PLACEHOLDER,
			'art_read_time'        => 4,
			'art_cornerstone'      => 1,
			'art_featured'         => 1,
			'art_subscribe_prompt' => 1,
			'art_seo_title'        => SUMMIT_PLACEHOLDER,
			'art_meta_desc'        => SUMMIT_PLACEHOLDER,
		],
	],
	[
		'slug'     => 'placeholder-article-brand-positioning',
		'title'    => '[TEMP PLACEHOLDER] Article — Brand & Positioning',
		'category' => 'brand-positioning',
		'fields'   => [
			'art_standfirst'       => SUMMIT_PLACEHOLDER,
			'art_read_time'        => 3,
			'art_cornerstone'      => 1,
			'art_featured'         => 0,
			'art_subscribe_prompt' => 1,
			'art_seo_title'        => SUMMIT_PLACEHOLDER,
			'art_meta_desc'        => SUMMIT_PLACEHOLDER,
		],
	],
	[
		'slug'     => 'placeholder-article-luxury-technology',
		'title'    => '[TEMP PLACEHOLDER] Article — Luxury and Technology',
		'category' => 'luxury-and-technology',
		'fields'   => [
			'art_standfirst'       => SUMMIT_PLACEHOLDER,
			'art_read_time'        => 3,
			'art_cornerstone'      => 0,
			'art_featured'         => 0,
			'art_subscribe_prompt' => 1,
			'art_seo_title'        => SUMMIT_PLACEHOLDER,
			'art_meta_desc'        => SUMMIT_PLACEHOLDER,
		],
	],
	[
		'slug'     => 'placeholder-article-culture-media',
		'title'    => '[TEMP PLACEHOLDER] Article — Culture & Media',
		'category' => 'culture-media',
		'fields'   => [
			'art_standfirst'       => SUMMIT_PLACEHOLDER,
			'art_read_time'        => 3,
			'art_cornerstone'      => 0,
			'art_featured'         => 0,
			'art_subscribe_prompt' => 1,
			'art_seo_title'        => SUMMIT_PLACEHOLDER,
			'art_meta_desc'        => SUMMIT_PLACEHOLDER,
		],
	],
];

$article_ids = [];
foreach ( $articles as $art ) {
	$post_id = summit_seed_post( 'article', $art['slug'], [
		'post_title'   => $art['title'],
		'post_content' => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
	] );
	if ( ! $post_id ) { continue; }
	$article_ids[ $art['slug'] ] = $post_id;

	if ( function_exists( 'update_field' ) ) {
		foreach ( $art['fields'] as $key => $value ) {
			update_field( $key, $value, $post_id );
		}
	}
	if ( isset( $article_cats[ $art['category'] ] ) ) {
		wp_set_object_terms( $post_id, (int) $article_cats[ $art['category'] ], 'article_category' );
	}
}


// ═════════════════════════════════════════════════════════════════════════════
// 5. PODCAST SEASON — Source: summit_content_model.md line 219
// ═════════════════════════════════════════════════════════════════════════════
echo "\n-- Podcast Season --\n";

$season_id = summit_seed_post( 'podcast_season', 'season-one', [
	'post_title' => 'Season One',
] );

if ( $season_id && function_exists( 'update_field' ) ) {
	update_field( 'ps_season_number', 1, $season_id );
	update_field( 'ps_subtitle', 'The New Value Equation', $season_id );
	update_field( 'ps_theme', SUMMIT_PLACEHOLDER, $season_id );
	update_field( 'ps_thesis', SUMMIT_PLACEHOLDER, $season_id );
	update_field( 'ps_is_featured', 1, $season_id );
	update_field( 'ps_seo_title', 'Season One — The New Value Equation', $season_id );
	update_field( 'ps_meta_desc', SUMMIT_PLACEHOLDER, $season_id );
}

if ( $season_id && isset( $podcast_themes['craft'] ) ) {
	wp_set_object_terms( $season_id, [ (int) $podcast_themes['craft'], (int) $podcast_themes['experience'] ], 'podcast_theme' );
}


// ═════════════════════════════════════════════════════════════════════════════
// 6. PODCAST EPISODES — All guest details are TEMP PLACEHOLDER
// ═════════════════════════════════════════════════════════════════════════════
echo "\n-- Podcast Episodes --\n";

$episodes = [
	[
		'slug'  => 'the-great-hangover',
		'title' => 'The Great Hangover',
		'fields' => [
			'ep_episode_number'     => 1,
			'ep_duration'           => SUMMIT_PLACEHOLDER,
			'ep_deck'               => SUMMIT_PLACEHOLDER,
			'ep_guest_name'         => SUMMIT_PLACEHOLDER,
			'ep_guest_title'        => SUMMIT_PLACEHOLDER,
			'ep_guest_company'      => SUMMIT_PLACEHOLDER,
			'ep_guest_bio'          => SUMMIT_PLACEHOLDER,
			'ep_audio_embed'        => '',
			'ep_show_notes'         => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'ep_transcript'         => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'ep_transcript_visible' => 1,
			'ep_is_featured'        => 1,
			'ep_seo_title'          => SUMMIT_PLACEHOLDER,
			'ep_meta_desc'          => SUMMIT_PLACEHOLDER,
		],
	],
	[
		'slug'  => 'price-without-proof',
		'title' => 'Price Without Proof',
		'fields' => [
			'ep_episode_number'     => 2,
			'ep_duration'           => SUMMIT_PLACEHOLDER,
			'ep_deck'               => SUMMIT_PLACEHOLDER,
			'ep_guest_name'         => SUMMIT_PLACEHOLDER,
			'ep_guest_title'        => SUMMIT_PLACEHOLDER,
			'ep_guest_company'      => SUMMIT_PLACEHOLDER,
			'ep_guest_bio'          => SUMMIT_PLACEHOLDER,
			'ep_audio_embed'        => '',
			'ep_show_notes'         => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'ep_transcript'         => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'ep_transcript_visible' => 1,
			'ep_is_featured'        => 0,
			'ep_seo_title'          => SUMMIT_PLACEHOLDER,
			'ep_meta_desc'          => SUMMIT_PLACEHOLDER,
		],
	],
	[
		'slug'  => 'the-grey-market-mirror',
		'title' => 'The Grey Market Mirror',
		'fields' => [
			'ep_episode_number'     => 3,
			'ep_duration'           => SUMMIT_PLACEHOLDER,
			'ep_deck'               => SUMMIT_PLACEHOLDER,
			'ep_guest_name'         => SUMMIT_PLACEHOLDER,
			'ep_guest_title'        => SUMMIT_PLACEHOLDER,
			'ep_guest_company'      => SUMMIT_PLACEHOLDER,
			'ep_guest_bio'          => SUMMIT_PLACEHOLDER,
			'ep_audio_embed'        => '',
			'ep_show_notes'         => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'ep_transcript'         => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'ep_transcript_visible' => 1,
			'ep_is_featured'        => 0,
			'ep_seo_title'          => SUMMIT_PLACEHOLDER,
			'ep_meta_desc'          => SUMMIT_PLACEHOLDER,
		],
	],
];

$episode_ids = [];
foreach ( $episodes as $ep ) {
	$post_id = summit_seed_post( 'podcast_episode', $ep['slug'], [
		'post_title' => $ep['title'],
	] );
	if ( ! $post_id ) { continue; }
	$episode_ids[ $ep['slug'] ] = $post_id;

	if ( function_exists( 'update_field' ) ) {
		update_field( 'ep_season', $season_id, $post_id );
		foreach ( $ep['fields'] as $key => $value ) {
			update_field( $key, $value, $post_id );
		}
	}
	if ( isset( $podcast_themes['craft'] ) ) {
		wp_set_object_terms( $post_id, (int) $podcast_themes['craft'], 'podcast_theme' );
	}
}


// ═════════════════════════════════════════════════════════════════════════════
// 7. DOWNLOAD — Source: summit_content_model.md line 239
// ═════════════════════════════════════════════════════════════════════════════
echo "\n-- Downloads --\n";

$download_id = summit_seed_post( 'download', 'the-characteristics-of-luxury', [
	'post_title' => 'The Characteristics of Luxury',
] );

if ( $download_id && function_exists( 'update_field' ) ) {
	update_field( 'dl_description', SUMMIT_PLACEHOLDER, $download_id );
	update_field( 'dl_file_type', 'PDF', $download_id );
	update_field( 'dl_file_size', SUMMIT_PLACEHOLDER, $download_id );
	update_field( 'dl_cta_label', 'Download the Deck', $download_id );
	update_field( 'dl_gated', 0, $download_id );
	update_field( 'dl_seo_title', 'The Characteristics of Luxury', $download_id );
	update_field( 'dl_meta_desc', SUMMIT_PLACEHOLDER, $download_id );
}


// ═════════════════════════════════════════════════════════════════════════════
// 8. CROSS-CONTENT RELATIONSHIPS
// ═════════════════════════════════════════════════════════════════════════════
echo "\n-- Cross-Content Relationships --\n";

if ( function_exists( 'update_field' ) ) {

	if ( isset( $case_study_ids['the-fight-against-skin-cancer'], $case_study_ids['leading-plastic-surgeons-films'] ) ) {
		update_field( 'cs_related_cases', [ $case_study_ids['leading-plastic-surgeons-films'] ], $case_study_ids['the-fight-against-skin-cancer'] );
		update_field( 'cs_related_cases', [ $case_study_ids['the-fight-against-skin-cancer'] ], $case_study_ids['leading-plastic-surgeons-films'] );
		echo "  [linked] skin cancer <-> plastic surgeons films\n";
	}

	if ( isset( $case_study_ids['teddy-william'], $article_ids['placeholder-article-brand-positioning'] ) ) {
		update_field( 'cs_related_article', [ $article_ids['placeholder-article-brand-positioning'] ], $case_study_ids['teddy-william'] );
		echo "  [linked] teddy-william -> placeholder article (brand positioning)\n";
	}

	if ( isset( $case_study_ids['tastemakers-podcast'], $episode_ids['the-great-hangover'] ) ) {
		update_field( 'cs_related_episode', $episode_ids['the-great-hangover'], $case_study_ids['tastemakers-podcast'] );
		echo "  [linked] tastemakers case study -> the great hangover\n";
	}

	if ( isset( $article_ids['placeholder-article-brand-positioning'], $case_study_ids['teddy-william'] ) ) {
		update_field( 'art_related_case', $case_study_ids['teddy-william'], $article_ids['placeholder-article-brand-positioning'] );
		echo "  [linked] placeholder article (brand positioning) -> teddy william\n";
	}

	if ( isset( $article_ids['placeholder-article-luxury-strategy'], $article_ids['placeholder-article-brand-positioning'] ) ) {
		update_field( 'art_related_articles', [
			$article_ids['placeholder-article-brand-positioning'],
		], $article_ids['placeholder-article-luxury-strategy'] );
		echo "  [linked] placeholder article (luxury strategy) -> placeholder article (brand positioning)\n";
	}

	if ( isset( $article_ids['placeholder-article-luxury-strategy'] ) && $download_id ) {
		update_field( 'art_related_download', $download_id, $article_ids['placeholder-article-luxury-strategy'] );
		echo "  [linked] placeholder article (luxury strategy) -> download\n";
	}

	if ( isset( $episode_ids['the-great-hangover'], $article_ids['placeholder-article-luxury-strategy'] ) ) {
		update_field( 'ep_related_articles', [ $article_ids['placeholder-article-luxury-strategy'] ], $episode_ids['the-great-hangover'] );
		echo "  [linked] the great hangover -> placeholder article (luxury strategy)\n";
	}

	if ( isset( $episode_ids['the-great-hangover'], $episode_ids['price-without-proof'] ) ) {
		update_field( 'ep_related_episodes', [ $episode_ids['price-without-proof'] ], $episode_ids['the-great-hangover'] );
		echo "  [linked] the great hangover -> price without proof\n";
	}

	if ( $season_id && isset( $episode_ids['the-great-hangover'] ) ) {
		update_field( 'ps_featured_episode', $episode_ids['the-great-hangover'], $season_id );
		echo "  [linked] season 1 -> featured: the great hangover\n";
	}

	if ( $season_id && isset( $article_ids['placeholder-article-culture-media'] ) ) {
		update_field( 'ps_related_articles', [ $article_ids['placeholder-article-culture-media'] ], $season_id );
		echo "  [linked] season 1 -> placeholder article (culture media)\n";
	}

	if ( $download_id && isset( $article_ids['placeholder-article-luxury-strategy'] ) ) {
		update_field( 'dl_related_article', [ $article_ids['placeholder-article-luxury-strategy'] ], $download_id );
		echo "  [linked] download -> placeholder article (luxury strategy)\n";
	}
}


// ═════════════════════════════════════════════════════════════════════════════
// 9. HOMEPAGE CURATED FIELDS — using home_* prefix
// Source: V4 website copy doc + summit_soul.md
// ═════════════════════════════════════════════════════════════════════════════
echo "\n-- Homepage Curated Fields (home_*) --\n";

if ( $home_id && function_exists( 'update_field' ) ) {

	// Hero — V4 Panel 1
	update_field( 'home_hero_headline', 'Design Tomorrow', $home_id );
	update_field( 'home_hero_body', 'Summit Communication Group is a design consultancy specialising in brand strategy, experience design and digital transformation for luxury brands.' . "\n\n" . 'We help organisations clarify their position, design exceptional experiences and modernise the digital systems that shape tomorrow\'s luxury landscape.', $home_id );
	update_field( 'home_hero_cta_label', 'Design Tomorrow', $home_id );
	update_field( 'home_hero_cta_url', '/design-tomorrow', $home_id );
	update_field( 'home_hero_cta2_label', 'Explore Our Work', $home_id );
	update_field( 'home_hero_cta2_url', '/work', $home_id );

	// Showreel — V4 Panel 2
	update_field( 'home_showreel_headline', 'A Studio Shaping the Future of Luxury', $home_id );
	update_field( 'home_showreel_body', 'Luxury is evolving. Culture is accelerating. Digital platforms are redefining how brands are experienced.' . "\n\n" . 'This short film introduces the ideas, environments and creative disciplines shaping Summit\'s work.', $home_id );

	// Value Proposition — V4 Panel 3
	update_field( 'home_vp_headline', 'A Design Consultancy for Luxury Brands', $home_id );
	update_field( 'home_vp_body', '<p>Based in London and working internationally, Summit partners with luxury brands, creators and cultural institutions to define distinctive brand positions and build modern brand experiences.</p><p>Our work sits at the intersection of strategy, design and technology across product, place and culture.</p>', $home_id );

	// Capability intro — V4 Panel 4
	update_field( 'home_cap_intro', '', $home_id );

	// Selected Work — curated case studies
	$curated_cases = array_values( array_filter( [
		$case_study_ids['teddy-william'] ?? 0,
		$case_study_ids['kaleida-studios'] ?? 0,
		$case_study_ids['the-fight-against-skin-cancer'] ?? 0,
		$case_study_ids['tastemakers-podcast'] ?? 0,
	] ) );
	if ( $curated_cases ) {
		update_field( 'home_work_cases', $curated_cases, $home_id );
		echo "  [set] home_work_cases -> " . count( $curated_cases ) . " case studies\n";
	}

	// Tastemakers — featured season (not episode)
	if ( $season_id ) {
		update_field( 'home_tastemakers_season', $season_id, $home_id );
		echo "  [set] home_tastemakers_season -> season 1\n";
	}
	// V4 Panel 6
	update_field( 'home_tastemakers_headline', 'Tastemakers', $home_id );
	update_field( 'home_tastemakers_body', 'A podcast exploring the founders, designers and cultural leaders redefining luxury.' . "\n\n" . 'Each episode examines how brand, craft, technology and culture intersect in the creation of modern luxury experiences.', $home_id );

	// Download — V4 Panel 7
	update_field( 'home_download_headline', 'The Characteristics of Luxury', $home_id );
	update_field( 'home_download_body', 'Understanding luxury requires more than market data or trend forecasts. It demands sensitivity to craft, culture and human desire.' . "\n\n" . 'This short visual essay explores the principles that define enduring luxury brands.', $home_id );
	if ( $download_id ) {
		update_field( 'home_download_item', $download_id, $home_id );
		echo "  [set] home_download_item -> download\n";
	}

	// Featured Article — V4 Panel 8 (single post_object)
	update_field( 'home_article_headline', 'The Future of Luxury', $home_id );
	$featured_article_id = $article_ids['placeholder-article-luxury-strategy'] ?? 0;
	if ( $featured_article_id ) {
		update_field( 'home_article_item', $featured_article_id, $home_id );
		echo "  [set] home_article_item -> featured article\n";
	}

	// Sectors — V4 Panel 9
	update_field( 'home_sectors_headline', 'Luxury Sectors', $home_id );
	update_field( 'home_sectors_body', 'Our work spans the industries shaping the modern luxury economy.', $home_id );

	echo "  [set] Homepage curated fields populated (home_*)\n";
}


// ═════════════════════════════════════════════════════════════════════════════
// DONE
// ═════════════════════════════════════════════════════════════════════════════
update_option( 'summit_seed_v7_local_complete', true );

echo "\n======================================================\n";
echo "Batch 7 local seeding complete.\n";
echo "======================================================\n\n";
