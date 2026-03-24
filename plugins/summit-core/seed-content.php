<?php
/**
 * Batch 7 Content Seeder — Summit Communication Group
 *
 * Source hierarchy:
 *   1. main branch = implemented truth for routes, templates, CPTs, taxonomies, field names
 *   2. Website_Summit Communication Group-V4.docx = primary truth for page copy,
 *      homepage panels, headings, CTAs, summaries, and launch-set content
 *   3. md governance docs = structural truth for content model, schema, migration, QA
 *
 * Where V4 provides actual copy, it is used verbatim.
 * Where V4 is silent, TEMP PLACEHOLDER is used.
 * No fabricated narratives, transcripts, guest details, or commercial outcomes.
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
// Guard: only run once
// ─────────────────────────────────────────────────────────────────────────────
if ( get_option( 'summit_seed_v7_complete' ) ) {
	echo "Batch 7 seed already run. Delete option 'summit_seed_v7_complete' to re-run.\n";
	return;
}

// Placeholder constant — used only where V4 doc is silent
define( 'SUMMIT_PLACEHOLDER', '[TEMP PLACEHOLDER — replace with real content before launch]' );

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
		'post_name'   => $slug,
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

echo "\n══════════════════════════════════════════════════\n";
echo "Summit Batch 7 — V4 Source-Faithful Seeder\n";
echo "══════════════════════════════════════════════════\n\n";


// ═════════════════════════════════════════════════════════════════════════════
// 1. TAXONOMY TERMS
// Source: V4 doc (sector lists from homepage + service pages),
//         summit_content_model.md (article categories, formats, service types)
// ═════════════════════════════════════════════════════════════════════════════
echo "── Taxonomy Terms ──\n";

// V4 sectors — from homepage Panel 9 and What We Do Panel 8
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

// V4 case study categories — from Work page Panel 4
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

// V4 editorial categories — from Future of Luxury Panel 5
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

// V4 case study format labels — from Work page Panel 4
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
foreach ( [
	'Craft',
	'Experience',
] as $name ) {
	$podcast_themes[ sanitize_title( $name ) ] = summit_seed_term( 'podcast_theme', $name );
}


// ═════════════════════════════════════════════════════════════════════════════
// 2. WORDPRESS PAGES
// Source: V4 doc (page titles and hierarchy), main branch (URL slugs)
// ═════════════════════════════════════════════════════════════════════════════
echo "\n── WordPress Pages ──\n";

$home_id = summit_seed_post( 'page', 'home', [
	'post_title' => 'Home',
] );

$who_we_are_id = summit_seed_post( 'page', 'who-we-are', [
	'post_title' => 'Who We Are',
] );

$what_we_do_id = summit_seed_post( 'page', 'what-we-do', [
	'post_title' => 'What We Do',
] );

// Child pages require full hierarchical path for get_page_by_path()
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

$work_id = summit_seed_post( 'page', 'work', [
	'post_title' => 'Work',
] );

$fol_id = summit_seed_post( 'page', 'future-of-luxury', [
	'post_title' => 'The Future of Luxury',
] );

$tastemakers_id = summit_seed_post( 'page', 'tastemakers', [
	'post_title' => 'Tastemakers',
] );

$design_tomorrow_id = summit_seed_post( 'page', 'design-tomorrow', [
	'post_title' => 'Design Tomorrow',
] );

$careers_id = summit_seed_post( 'page', 'careers', [
	'post_title' => 'Careers',
] );

$client_lounge_id = summit_seed_post( 'page', 'client-lounge', [
	'post_title' => 'Client Lounge',
] );

// Set static front page
if ( $home_id ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home_id );
	echo "  [set] Static front page → Home (ID {$home_id})\n";
}


// ═════════════════════════════════════════════════════════════════════════════
// 3. CASE STUDIES
// Source: V4 doc, Work page Panel 4 — "Selected Engagements" launch set
// Titles, slugs, one-line descriptions and format labels from V4.
// Narrative fields (challenge, idea, built, outcome) are TEMP PLACEHOLDER.
// ═════════════════════════════════════════════════════════════════════════════
echo "\n── Case Studies ──\n";

$case_studies = [
	[
		// V4: "Teddy William / Branding / Brand identity for a London tailoring
		// proposition shaped around precision, heritage and distinction."
		'slug'         => 'teddy-william',
		'title'        => 'Teddy William',
		'sector'       => 'fashion-leather-goods',
		'service_type' => 'brand-strategy',
		'format'       => 'branding',
		'fields'       => [
			'cs_summary'        => 'Brand identity for a London tailoring proposition shaped around precision, heritage and distinction.',
			'cs_client'         => 'Teddy William',
			'cs_intro'          => SUMMIT_PLACEHOLDER,
			'cs_challenge'      => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_strategic_idea' => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_what_we_built'  => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_outcome'        => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_seo_title'      => 'Teddy William | Branding for Luxury Tailoring | Summit Communication Group',
			'cs_meta_desc'      => 'Brand identity for a London tailoring proposition shaped around precision, heritage and distinction. A Summit Communication Group case study.',
		],
	],
	[
		// V4: "The Fight Against Skin Cancer / Activism Campaign /
		// A campaign designed to turn awareness into emotional clarity and public action."
		'slug'         => 'the-fight-against-skin-cancer',
		'title'        => 'The Fight Against Skin Cancer',
		'sector'       => 'hospitality-travel', // closest available; V4 does not specify
		'service_type' => 'campaign',
		'format'       => 'activism-campaign',
		'fields'       => [
			'cs_summary'        => 'A campaign designed to turn awareness into emotional clarity and public action.',
			'cs_intro'          => SUMMIT_PLACEHOLDER,
			'cs_challenge'      => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_strategic_idea' => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_what_we_built'  => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_outcome'        => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_seo_title'      => 'The Fight Against Skin Cancer | Activism Campaign | Summit Communication Group',
			'cs_meta_desc'      => 'A campaign designed to turn awareness into emotional clarity and public action. A Summit Communication Group case study.',
		],
	],
	[
		// V4: "Leading Plastic Surgeons Films / Film & Authority Building /
		// A premium film platform designed to elevate the authority and digital
		// presence of leading surgeons."
		'slug'         => 'leading-plastic-surgeons-films',
		'title'        => 'Leading Plastic Surgeons Films',
		'sector'       => 'hospitality-travel', // closest available; V4 does not specify
		'service_type' => 'film',
		'format'       => 'film-authority-building',
		'fields'       => [
			'cs_summary'        => 'A premium film platform designed to elevate the authority and digital presence of leading surgeons.',
			'cs_intro'          => SUMMIT_PLACEHOLDER,
			'cs_challenge'      => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_strategic_idea' => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_what_we_built'  => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_outcome'        => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_seo_title'      => 'Leading Plastic Surgeons Films | Film & Authority Building | Summit Communication Group',
			'cs_meta_desc'      => 'A premium film platform designed to elevate the authority and digital presence of leading surgeons. A Summit Communication Group case study.',
		],
	],
	[
		// V4: "Kaleida Studios / Immersive Storytelling /
		// Brand world and strategic narrative for a next-generation storytelling venture."
		'slug'         => 'kaleida-studios',
		'title'        => 'Kaleida Studios',
		'sector'       => 'lifestyle-technology',
		'service_type' => 'experience-design',
		'format'       => 'immersive-storytelling',
		'fields'       => [
			'cs_summary'        => 'Brand world and strategic narrative for a next-generation storytelling venture.',
			'cs_client'         => 'Kaleida Studios',
			'cs_intro'          => SUMMIT_PLACEHOLDER,
			'cs_challenge'      => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_strategic_idea' => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_what_we_built'  => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_outcome'        => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_seo_title'      => 'Kaleida Studios | Immersive Storytelling | Summit Communication Group',
			'cs_meta_desc'      => 'Brand world and strategic narrative for a next-generation storytelling venture. A Summit Communication Group case study.',
		],
	],
	[
		// V4: "Tastemakers Podcast / Podcast & Editorial Identity /
		// Identity and platform thinking for a show examining the future of
		// luxury and culture."
		'slug'         => 'tastemakers-podcast',
		'title'        => 'Tastemakers Podcast',
		'sector'       => 'lifestyle-technology', // closest available for culture/media
		'service_type' => 'podcast-identity',
		'format'       => 'podcast-editorial-identity',
		'fields'       => [
			'cs_summary'        => 'Identity and platform thinking for a show examining the future of luxury and culture.',
			'cs_client'         => 'Summit Communication Group',
			'cs_intro'          => SUMMIT_PLACEHOLDER,
			'cs_challenge'      => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_strategic_idea' => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_what_we_built'  => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_outcome'        => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'cs_seo_title'      => 'Tastemakers Podcast | Podcast & Editorial Identity | Summit Communication Group',
			'cs_meta_desc'      => 'Identity and platform thinking for a show examining the future of luxury and culture. A Summit Communication Group case study.',
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
// 4. ARTICLES
// Source: V4 doc does not provide individual article titles or bodies.
// Neither do the md governance docs. Titles are explicitly marked as
// TEMP PLACEHOLDER. These are structural stubs for template validation only.
// ═════════════════════════════════════════════════════════════════════════════
echo "\n── Articles ──\n";

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
// 5. PODCAST SEASON
// Source: V4 doc, Tastemakers page Panel 4 — Season One
// Subtitle, theme description and thesis from V4.
// ═════════════════════════════════════════════════════════════════════════════
echo "\n── Podcast Season ──\n";

$season_id = summit_seed_post( 'podcast_season', 'season-one', [
	'post_title' => 'Season One',
] );

if ( $season_id && function_exists( 'update_field' ) ) {
	update_field( 'ps_season_number', 1, $season_id );
	// V4: "Season One / The New Value Equation"
	update_field( 'ps_subtitle', 'The New Value Equation', $season_id );
	// V4: "Season One explores the changing economics of belief in modern luxury."
	update_field( 'ps_theme', 'Season One explores the changing economics of belief in modern luxury.', $season_id );
	// V4: "The season\'s central thesis is simple: luxury has not collapsed. It has
	// sobered up. The winners will be the brands that can still prove value under scrutiny."
	update_field( 'ps_thesis', 'Luxury has not collapsed. It has sobered up. The winners will be the brands that can still prove value under scrutiny.', $season_id );
	update_field( 'ps_is_featured', 1, $season_id );
	update_field( 'ps_seo_title', 'Season One — The New Value Equation | Tastemakers Podcast', $season_id );
	update_field( 'ps_meta_desc', 'Season One explores the changing economics of belief in modern luxury across fifteen episodes on price fatigue, grey markets, experiential indulgence, AI, circularity and the new codes of timelessness.', $season_id );
}

if ( $season_id && isset( $podcast_themes['craft'] ) ) {
	wp_set_object_terms( $season_id, [ (int) $podcast_themes['craft'], (int) $podcast_themes['experience'] ], 'podcast_theme' );
}


// ═════════════════════════════════════════════════════════════════════════════
// 6. PODCAST EPISODES
// Source: V4 doc, Tastemakers page Panel 5 — "Season at a Glance"
// Episode titles from V4. Seeding first 3 of 15 for minimum validation set.
// Guest details, transcripts, show notes remain TEMP PLACEHOLDER.
// ═════════════════════════════════════════════════════════════════════════════
echo "\n── Podcast Episodes ──\n";

$episodes = [
	[
		// V4: Episode 1 title "The Great Hangover"
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
			'ep_seo_title'          => 'The Great Hangover | Tastemakers Podcast | Season One',
			'ep_meta_desc'          => SUMMIT_PLACEHOLDER,
		],
	],
	[
		// V4: Episode 2 title "Price Without Proof"
		'slug'  => 'price-without-proof',
		'title' => 'Price Without Proof',
		'fields' => [
			'ep_episode_number'     => 2,
			'ep_duration'           => SUMMIT_PLACEHOLDER,
			// V4 example deck: "A conversation on price fatigue, premium proof
			// and why luxury brands now need harder evidence behind the premium."
			'ep_deck'               => 'A conversation on price fatigue, premium proof and why luxury brands now need harder evidence behind the premium.',
			'ep_guest_name'         => SUMMIT_PLACEHOLDER,
			'ep_guest_title'        => SUMMIT_PLACEHOLDER,
			'ep_guest_company'      => SUMMIT_PLACEHOLDER,
			'ep_guest_bio'          => SUMMIT_PLACEHOLDER,
			'ep_audio_embed'        => '',
			'ep_show_notes'         => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'ep_transcript'         => '<p>' . SUMMIT_PLACEHOLDER . '</p>',
			'ep_transcript_visible' => 1,
			'ep_is_featured'        => 0,
			'ep_seo_title'          => 'Price Without Proof | Tastemakers Podcast | Season One',
			'ep_meta_desc'          => 'A Tastemakers conversation on price fatigue, premium proof and the new rules of value in modern luxury.',
		],
	],
	[
		// V4: Episode 3 title "The Grey Market Mirror"
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
			'ep_seo_title'          => 'The Grey Market Mirror | Tastemakers Podcast | Season One',
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
// 7. DOWNLOAD
// Source: V4 doc, homepage Panel 7 + Work page Panel 7
// Title and body copy from V4.
// ═════════════════════════════════════════════════════════════════════════════
echo "\n── Downloads ──\n";

$download_id = summit_seed_post( 'download', 'the-characteristics-of-luxury', [
	'post_title' => 'The Characteristics of Luxury',
] );

if ( $download_id && function_exists( 'update_field' ) ) {
	// V4 homepage Panel 7: "Understanding luxury requires more than market data
	// or trend forecasts. It demands sensitivity to craft, culture and human desire.
	// This short visual essay explores the principles that define enduring luxury brands."
	update_field( 'dl_description', 'Understanding luxury requires more than market data or trend forecasts. It demands sensitivity to craft, culture and human desire. This short visual essay explores the principles that define enduring luxury brands.', $download_id );
	update_field( 'dl_file_type', 'PDF', $download_id );
	update_field( 'dl_file_size', SUMMIT_PLACEHOLDER, $download_id );
	// V4 homepage Panel 7 CTA: "Download the Deck →"
	update_field( 'dl_cta_label', 'Download the Deck', $download_id );
	update_field( 'dl_gated', 0, $download_id );
	update_field( 'dl_seo_title', 'The Characteristics of Luxury | Summit Communication Group', $download_id );
	update_field( 'dl_meta_desc', 'A visual essay exploring the principles that define enduring luxury brands, from craft and culture to human desire.', $download_id );
}


// ═════════════════════════════════════════════════════════════════════════════
// 8. CROSS-CONTENT RELATIONSHIPS
// Structural wiring only — validates relationship field rendering.
// ═════════════════════════════════════════════════════════════════════════════
echo "\n── Cross-Content Relationships ──\n";

if ( function_exists( 'update_field' ) ) {

	// Case study ↔ case study
	if ( isset( $case_study_ids['the-fight-against-skin-cancer'], $case_study_ids['leading-plastic-surgeons-films'] ) ) {
		update_field( 'cs_related_cases', [ $case_study_ids['leading-plastic-surgeons-films'] ], $case_study_ids['the-fight-against-skin-cancer'] );
		update_field( 'cs_related_cases', [ $case_study_ids['the-fight-against-skin-cancer'] ], $case_study_ids['leading-plastic-surgeons-films'] );
		echo "  [linked] skin cancer ↔ plastic surgeons films\n";
	}

	// Case study → article
	if ( isset( $case_study_ids['teddy-william'], $article_ids['placeholder-article-brand-positioning'] ) ) {
		update_field( 'cs_related_article', [ $article_ids['placeholder-article-brand-positioning'] ], $case_study_ids['teddy-william'] );
		echo "  [linked] teddy-william → placeholder article (brand positioning)\n";
	}

	// Case study → episode
	if ( isset( $case_study_ids['tastemakers-podcast'], $episode_ids['the-great-hangover'] ) ) {
		update_field( 'cs_related_episode', $episode_ids['the-great-hangover'], $case_study_ids['tastemakers-podcast'] );
		echo "  [linked] tastemakers case study → the great hangover\n";
	}

	// Article → case study
	if ( isset( $article_ids['placeholder-article-brand-positioning'], $case_study_ids['teddy-william'] ) ) {
		update_field( 'art_related_case', $case_study_ids['teddy-william'], $article_ids['placeholder-article-brand-positioning'] );
		echo "  [linked] placeholder article (brand positioning) → teddy william\n";
	}

	// Article → related articles
	if ( isset( $article_ids['placeholder-article-luxury-strategy'], $article_ids['placeholder-article-brand-positioning'] ) ) {
		update_field( 'art_related_articles', [
			$article_ids['placeholder-article-brand-positioning'],
		], $article_ids['placeholder-article-luxury-strategy'] );
		echo "  [linked] placeholder article (luxury strategy) → placeholder article (brand positioning)\n";
	}

	// Article → download
	if ( isset( $article_ids['placeholder-article-luxury-strategy'] ) && $download_id ) {
		update_field( 'art_related_download', $download_id, $article_ids['placeholder-article-luxury-strategy'] );
		echo "  [linked] placeholder article (luxury strategy) → download\n";
	}

	// Episode → related articles
	if ( isset( $episode_ids['the-great-hangover'], $article_ids['placeholder-article-luxury-strategy'] ) ) {
		update_field( 'ep_related_articles', [ $article_ids['placeholder-article-luxury-strategy'] ], $episode_ids['the-great-hangover'] );
		echo "  [linked] the great hangover → placeholder article (luxury strategy)\n";
	}

	// Episode → related episodes
	if ( isset( $episode_ids['the-great-hangover'], $episode_ids['price-without-proof'] ) ) {
		update_field( 'ep_related_episodes', [ $episode_ids['price-without-proof'] ], $episode_ids['the-great-hangover'] );
		echo "  [linked] the great hangover → price without proof\n";
	}

	// Season → featured episode
	if ( $season_id && isset( $episode_ids['the-great-hangover'] ) ) {
		update_field( 'ps_featured_episode', $episode_ids['the-great-hangover'], $season_id );
		echo "  [linked] season 1 → featured: the great hangover\n";
	}

	// Season → related articles
	if ( $season_id && isset( $article_ids['placeholder-article-culture-media'] ) ) {
		update_field( 'ps_related_articles', [ $article_ids['placeholder-article-culture-media'] ], $season_id );
		echo "  [linked] season 1 → placeholder article (culture media)\n";
	}

	// Download → related article
	if ( $download_id && isset( $article_ids['placeholder-article-luxury-strategy'] ) ) {
		update_field( 'dl_related_article', [ $article_ids['placeholder-article-luxury-strategy'] ], $download_id );
		echo "  [linked] download → placeholder article (luxury strategy)\n";
	}
}


// ═════════════════════════════════════════════════════════════════════════════
// 9. HOMEPAGE CURATED FIELDS
// Source: V4 doc, homepage Panels 1–10 (verbatim copy)
// Uses relative URLs — no home_url() stored in DB.
// ═════════════════════════════════════════════════════════════════════════════
echo "\n── Homepage Curated Fields ──\n";

if ( $home_id && function_exists( 'update_field' ) ) {

	// ── Panel 1: Hero ──
	// V4 H1: "Design Tomorrow"
	update_field( 'hp_hero_headline', 'Design Tomorrow', $home_id );
	// V4 body: two paragraphs
	update_field( 'hp_hero_subline', 'Summit Communication Group is a design consultancy specialising in brand strategy, experience design and digital transformation for luxury brands.', $home_id );
	// V4 primary CTA: "Design Tomorrow →"
	update_field( 'hp_hero_cta_label', 'Design Tomorrow', $home_id );
	update_field( 'hp_hero_cta_url', '/design-tomorrow', $home_id );
	// V4 secondary CTA: "Explore Our Work →"
	update_field( 'hp_hero_secondary_cta_label', 'Explore Our Work', $home_id );
	update_field( 'hp_hero_secondary_cta_url', '/work', $home_id );

	// ── Panel 2: Showreel ──
	// V4 H2: "A Studio Shaping the Future of Luxury"
	update_field( 'hp_showreel_headline', 'A Studio Shaping the Future of Luxury', $home_id );
	// V4 showreel embed left empty — no video URL in doc
	update_field( 'hp_showreel_embed', '', $home_id );

	// ── Panel 3: Value Proposition ──
	// V4 H2: "A Design Consultancy for Luxury Brands"
	update_field( 'hp_value_headline', 'A Design Consultancy for Luxury Brands', $home_id );
	// V4 body copy
	update_field( 'hp_value_body', 'Based in London and working internationally, Summit partners with luxury brands, creators and cultural institutions to define distinctive brand positions and build modern brand experiences. Our work sits at the intersection of strategy, design and technology across product, place and culture.', $home_id );

	// ── Panel 4: What We Do ──
	// V4 H2: "What We Do"
	update_field( 'hp_services_headline', 'What We Do', $home_id );
	update_field( 'hp_services_intro', '', $home_id );

	// ── Panel 5: Selected Work ──
	$curated_cases = array_values( array_filter( [
		$case_study_ids['teddy-william'] ?? 0,
		$case_study_ids['the-fight-against-skin-cancer'] ?? 0,
		$case_study_ids['leading-plastic-surgeons-films'] ?? 0,
		$case_study_ids['kaleida-studios'] ?? 0,
		$case_study_ids['tastemakers-podcast'] ?? 0,
	] ) );
	if ( $curated_cases ) {
		update_field( 'hp_featured_cases', $curated_cases, $home_id );
		echo "  [set] hp_featured_cases → " . count( $curated_cases ) . " case studies\n";
	}

	// ── Panel 6: Tastemakers ──
	$featured_ep = $episode_ids['the-great-hangover'] ?? 0;
	if ( $featured_ep ) {
		update_field( 'hp_featured_episode', $featured_ep, $home_id );
		echo "  [set] hp_featured_episode → the great hangover\n";
	}

	// ── Panel 7: Download ──
	// V4 H2: "The Characteristics of Luxury"
	update_field( 'hp_download_headline', 'The Characteristics of Luxury', $home_id );
	// V4 body copy
	update_field( 'hp_download_body', 'Understanding luxury requires more than market data or trend forecasts. It demands sensitivity to craft, culture and human desire. This short visual essay explores the principles that define enduring luxury brands.', $home_id );
	if ( $download_id ) {
		update_field( 'hp_download_asset', $download_id, $home_id );
		echo "  [set] hp_download_asset → download\n";
	}

	// ── Panel 8: Future of Luxury ──
	$curated_articles = array_values( array_filter( [
		$article_ids['placeholder-article-luxury-strategy'] ?? 0,
		$article_ids['placeholder-article-brand-positioning'] ?? 0,
		$article_ids['placeholder-article-luxury-technology'] ?? 0,
	] ) );
	if ( $curated_articles ) {
		update_field( 'hp_featured_articles', $curated_articles, $home_id );
		echo "  [set] hp_featured_articles → " . count( $curated_articles ) . " articles\n";
	}

	// ── Panel 9: Sectors ──
	// V4 H2: "Luxury Sectors"
	update_field( 'hp_sectors_headline', 'Luxury Sectors', $home_id );

	// ── Panel 10: Closing CTA ──
	// V4 H2: "Let's Design Tomorrow"
	update_field( 'hp_cta_headline', "Let's Design Tomorrow", $home_id );
	// V4 body copy
	update_field( 'hp_cta_body', 'If you are building a luxury brand, transforming an experience or preparing for the next chapter of growth, we would welcome the conversation.', $home_id );
	// V4 primary CTA: "Start a Conversation →"
	update_field( 'hp_cta_label', 'Start a Conversation', $home_id );
	update_field( 'hp_cta_url', '/design-tomorrow', $home_id );

	echo "  [set] Homepage curated fields populated from V4\n";
}


// ═════════════════════════════════════════════════════════════════════════════
// DONE
// ═════════════════════════════════════════════════════════════════════════════
update_option( 'summit_seed_v7_complete', true );

echo "\n══════════════════════════════════════════════════\n";
echo "Batch 7 seeding complete.\n";
echo "══════════════════════════════════════════════════\n\n";
