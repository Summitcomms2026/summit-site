<?php
/**
 * ACF Field Group Registration — Summit Communication Group
 *
 * Registers all Advanced Custom Fields field groups via acf_add_local_field_group().
 * Fields are defined in PHP so they are version-controlled, never dependent on
 * the database, and never lost if the ACF GUI is misconfigured or the database
 * is rebuilt.
 *
 * Field groups registered here:
 *   1. summit_case_study_fields     — case_study post type
 *   2. summit_article_fields        — article post type
 *   3. summit_podcast_season_fields — podcast_season post type
 *   4. summit_podcast_episode_fields — podcast_episode post type
 *   5. summit_download_fields       — download post type
 *   6. summit_enquiry_fields        — enquiry post type (private admin object)
 *   7. summit_homepage_fields       — front page (static front page)
 *
 * Governance:
 *   summit_content_model.md         — field specifications and validation rules
 *   summit_content_architecture.docx — field tables with types, required flags, notes
 *
 * Conventions used throughout:
 *   - Field keys use the pattern: field_summit_{post_type_abbrev}_{field_name}
 *   - Required fields (per summit_content_model.md Validation Rules) set required => 1
 *   - Admin-only instructional text uses 'message' field type
 *   - Tabs group fields into logical sections for editor clarity
 *   - Relationship fields use post_object (single) or relationship (multi)
 *   - All SEO fields collected in a dedicated SEO tab per content type
 *
 * @package SummitCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register all ACF field groups.
 * Hooked to acf/init to ensure ACF is loaded before registration runs.
 */
add_action( 'acf/init', 'summit_core_register_acf_fields' );

function summit_core_register_acf_fields(): void {
	// Guard — if ACF is not active, exit silently.
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	summit_acf_case_study();
	summit_acf_article();
	summit_acf_podcast_season();
	summit_acf_podcast_episode();
	summit_acf_download();
	summit_acf_enquiry();
	summit_acf_homepage();
}


// ═════════════════════════════════════════════════════════════════════════════
// 1. CASE STUDY
// Post type: case_study | Slug base: /work/[project-slug]
// ═════════════════════════════════════════════════════════════════════════════

function summit_acf_case_study(): void {
	acf_add_local_field_group( [
		'key'                   => 'group_summit_case_study',
		'title'                 => 'Case Study Fields',
		'fields'                => [

			// ── Tab: Project ──────────────────────────────────────────────

			[
				'key'   => 'field_cs_tab_project',
				'label' => 'Project',
				'type'  => 'tab',
				'placement' => 'top',
			],
			[
				'key'           => 'field_cs_summary',
				'label'         => 'Project Summary',
				'name'          => 'cs_summary',
				'type'          => 'text',
				'required'      => 1,
				'instructions'  => 'One sentence. Used in archive cards and grids. Keep under 120 characters.',
				'maxlength'     => 160,
				'placeholder'   => 'e.g. Brand strategy and identity for a London-based luxury watch retailer.',
				'wrapper'       => [ 'width' => '100' ],
			],
			[
				'key'           => 'field_cs_client',
				'label'         => 'Client / Brand Name',
				'name'          => 'cs_client',
				'type'          => 'text',
				'required'      => 0,
				'instructions'  => 'Leave blank if the client is under NDA or confidentiality agreement.',
				'placeholder'   => 'e.g. Kaleida Studios',
			],
			[
				'key'           => 'field_cs_year',
				'label'         => 'Project Year',
				'name'          => 'cs_year',
				'type'          => 'number',
				'required'      => 0,
				'instructions'  => 'Four-digit year. Used for ordering and display.',
				'min'           => 2000,
				'max'           => 2099,
				'step'          => 1,
				'placeholder'   => '2024',
				'wrapper'       => [ 'width' => '25' ],
			],
			[
				'key'           => 'field_cs_geographic',
				'label'         => 'Geographic Relevance',
				'name'          => 'cs_geographic',
				'type'          => 'text',
				'required'      => 0,
				'instructions'  => 'Optional. e.g. London, Global, Middle East.',
				'placeholder'   => 'e.g. London',
				'wrapper'       => [ 'width' => '25' ],
			],

			// ── Tab: Narrative ────────────────────────────────────────────

			[
				'key'   => 'field_cs_tab_narrative',
				'label' => 'Narrative',
				'type'  => 'tab',
			],
			[
				'key'           => 'field_cs_intro',
				'label'         => 'Short Introduction',
				'name'          => 'cs_intro',
				'type'          => 'textarea',
				'required'      => 1,
				'instructions'  => 'Two or three sentences. Frames the engagement before the structured sections. Appears in the case study hero.',
				'rows'          => 3,
				'new_lines'     => 'br',
			],
			[
				'key'           => 'field_cs_challenge',
				'label'         => 'Challenge',
				'name'          => 'cs_challenge',
				'type'          => 'wysiwyg',
				'required'      => 1,
				'instructions'  => 'What was the problem, gap or opportunity Summit was engaged to address?',
				'tabs'          => 'all',
				'toolbar'       => 'basic',
				'media_upload'  => 0,
				'delay'         => 0,
			],
			[
				'key'           => 'field_cs_strategic_idea',
				'label'         => 'Strategic Idea',
				'name'          => 'cs_strategic_idea',
				'type'          => 'wysiwyg',
				'required'      => 1,
				'instructions'  => 'The core thinking Summit brought. What was the insight, reframe or decisive idea?',
				'tabs'          => 'all',
				'toolbar'       => 'basic',
				'media_upload'  => 0,
				'delay'         => 0,
			],
			[
				'key'           => 'field_cs_what_we_built',
				'label'         => 'What We Built',
				'name'          => 'cs_what_we_built',
				'type'          => 'wysiwyg',
				'required'      => 1,
				'instructions'  => 'Describe the work — what was made, designed or delivered.',
				'tabs'          => 'all',
				'toolbar'       => 'basic',
				'media_upload'  => 0,
				'delay'         => 0,
			],
			[
				'key'           => 'field_cs_outcome',
				'label'         => 'Outcome',
				'name'          => 'cs_outcome',
				'type'          => 'wysiwyg',
				'required'      => 1,
				'instructions'  => 'What changed? Be specific and honest. Outcome signals are more credible than vague claims.',
				'tabs'          => 'all',
				'toolbar'       => 'basic',
				'media_upload'  => 0,
				'delay'         => 0,
			],
			[
				'key'           => 'field_cs_outcome_signal',
				'label'         => 'Outcome Signal (short)',
				'name'          => 'cs_outcome_signal',
				'type'          => 'text',
				'required'      => 0,
				'instructions'  => 'Optional one-line proof point for use in cards or summary panels. e.g. "+40% organic reach within 60 days."',
				'placeholder'   => 'e.g. Identity launched across 12 markets.',
			],

			// ── Tab: Media ────────────────────────────────────────────────

			[
				'key'   => 'field_cs_tab_media',
				'label' => 'Media',
				'type'  => 'tab',
			],
			[
				'key'           => 'field_cs_gallery',
				'label'         => 'Project Gallery',
				'name'          => 'cs_gallery',
				'type'          => 'gallery',
				'required'      => 0,
				'instructions'  => 'Upload project images in final presentation order. Add meaningful alt text to each image.',
				'min'           => 0,
				'max'           => 20,
				'insert'        => 'append',
				'library'       => 'all',
				'min_width'     => 1200,
				'min_height'    => 0,
				'mime_types'    => 'jpg,jpeg,png,webp',
			],
			[
				'key'           => 'field_cs_video_embed',
				'label'         => 'Video Embed',
				'name'          => 'cs_video_embed',
				'type'          => 'text',
				'required'      => 0,
				'instructions'  => 'JW Player embed code, URL, or iframe. Used for showreel or case study film. Leave blank if no video.',
				'placeholder'   => 'https://content.jwplatform.com/players/...',
			],

			// ── Tab: Relationships ────────────────────────────────────────

			[
				'key'   => 'field_cs_tab_relationships',
				'label' => 'Relationships',
				'type'  => 'tab',
			],
			[
				'key'               => 'field_cs_related_cases',
				'label'             => 'Related Case Studies',
				'name'              => 'cs_related_cases',
				'type'              => 'relationship',
				'required'          => 0,
				'instructions'      => 'Select up to 3 related case studies. These appear in the Related Work block.',
				'post_type'         => [ 'case_study' ],
				'filters'           => [ 'search' ],
				'elements'          => [ 'featured_image' ],
				'min'               => 0,
				'max'               => 3,
				'return_format'     => 'object',
			],
			[
				'key'               => 'field_cs_related_article',
				'label'             => 'Related Articles',
				'name'              => 'cs_related_article',
				'type'              => 'relationship',
				'required'          => 0,
				'instructions'      => 'Link up to 2 Future of Luxury articles that relate to this project.',
				'post_type'         => [ 'article' ],
				'filters'           => [ 'search' ],
				'elements'          => [ 'featured_image' ],
				'min'               => 0,
				'max'               => 2,
				'return_format'     => 'object',
			],
			[
				'key'               => 'field_cs_related_episode',
				'label'             => 'Related Podcast Episode',
				'name'              => 'cs_related_episode',
				'type'              => 'post_object',
				'required'          => 0,
				'instructions'      => 'Optional. Link one Tastemakers episode where the same themes arise.',
				'post_type'         => [ 'podcast_episode' ],
				'allow_null'        => 1,
				'multiple'          => 0,
				'return_format'     => 'object',
				'ui'                => 1,
			],

			// ── Tab: CTA ──────────────────────────────────────────────────

			[
				'key'   => 'field_cs_tab_cta',
				'label' => 'CTA',
				'type'  => 'tab',
			],
			[
				'key'           => 'field_cs_cta_label',
				'label'         => 'Primary CTA Label',
				'name'          => 'cs_cta_label',
				'type'          => 'text',
				'required'      => 0,
				'instructions'  => 'Overrides the default "Start a Conversation" CTA at the foot of this case study. Leave blank to use the default.',
				'placeholder'   => 'e.g. Discuss a similar project',
				'wrapper'       => [ 'width' => '50' ],
			],
			[
				'key'           => 'field_cs_cta_url',
				'label'         => 'Primary CTA URL',
				'name'          => 'cs_cta_url',
				'type'          => 'url',
				'required'      => 0,
				'instructions'  => 'Leave blank to default to /design-tomorrow.',
				'placeholder'   => 'https://summitcommunication.group/design-tomorrow',
				'wrapper'       => [ 'width' => '50' ],
			],

			// ── Tab: SEO ──────────────────────────────────────────────────

			[
				'key'   => 'field_cs_tab_seo',
				'label' => 'SEO',
				'type'  => 'tab',
			],
			[
				'key'           => 'field_cs_seo_title',
				'label'         => 'SEO Title',
				'name'          => 'cs_seo_title',
				'type'          => 'text',
				'required'      => 1,
				'instructions'  => 'Target 50–60 characters. Will appear in browser tab and search results. Should include the project name and a key phrase.',
				'maxlength'     => 70,
				'placeholder'   => 'e.g. Kaleida Studios — Brand Identity | Summit',
				'wrapper'       => [ 'width' => '100' ],
			],
			[
				'key'           => 'field_cs_meta_desc',
				'label'         => 'Meta Description',
				'name'          => 'cs_meta_desc',
				'type'          => 'textarea',
				'required'      => 1,
				'instructions'  => 'Target 140–160 characters. Summarises the project for search results. Do not copy the summary line verbatim.',
				'rows'          => 3,
				'maxlength'     => 200,
				'new_lines'     => '',
			],
			[
				'key'           => 'field_cs_og_image',
				'label'         => 'Open Graph Image',
				'name'          => 'cs_og_image',
				'type'          => 'image',
				'required'      => 1,
				'instructions'  => 'Minimum 1200×630px. Used when the URL is shared on LinkedIn, Slack, etc. Use the strongest project visual.',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'library'       => 'all',
				'min_width'     => 1200,
				'min_height'    => 630,
			],
			[
				'key'           => 'field_cs_canonical',
				'label'         => 'Canonical URL',
				'name'          => 'cs_canonical',
				'type'          => 'url',
				'required'      => 0,
				'instructions'  => 'Only set this if the canonical version of this page lives at a different URL. Leave blank in almost all cases.',
				'placeholder'   => 'https://summitcommunication.group/work/...',
			],
			[
				'key'           => 'field_cs_noindex',
				'label'         => 'Noindex this page',
				'name'          => 'cs_noindex',
				'type'          => 'true_false',
				'required'      => 0,
				'instructions'  => 'Tick to exclude this case study from search engine indexing. Use for draft/private work only.',
				'ui'            => 1,
				'default_value' => 0,
				'ui_on_text'    => 'Noindex',
				'ui_off_text'   => 'Index',
			],
		],
		'location'              => [
			[ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'case_study' ] ],
		],
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'seamless',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => [ 'the_content' ], // Narrative handled in ACF wysiwyg fields
		'active'                => true,
	] );
}


// ═════════════════════════════════════════════════════════════════════════════
// 2. ARTICLE
// Post type: article | Slug base: /future-of-luxury/[article-slug]
// ═════════════════════════════════════════════════════════════════════════════

function summit_acf_article(): void {
	acf_add_local_field_group( [
		'key'                   => 'group_summit_article',
		'title'                 => 'Article Fields',
		'fields'                => [

			// ── Tab: Editorial ────────────────────────────────────────────

			[
				'key'   => 'field_art_tab_editorial',
				'label' => 'Editorial',
				'type'  => 'tab',
				'placement' => 'top',
			],
			[
				'key'           => 'field_art_standfirst',
				'label'         => 'Standfirst / Deck',
				'name'          => 'art_standfirst',
				'type'          => 'textarea',
				'required'      => 1,
				'instructions'  => 'Two or three sentences. Appears beneath the headline in the article hero and in article cards. This is not the excerpt — it is the editorial introduction.',
				'rows'          => 3,
				'new_lines'     => 'br',
				'maxlength'     => 400,
			],
			[
				'key'           => 'field_art_read_time',
				'label'         => 'Read Time (minutes)',
				'name'          => 'art_read_time',
				'type'          => 'number',
				'required'      => 0,
				'instructions'  => 'Approximate reading time in minutes. Displayed in the metadata bar. Typically auto-calculated; override here if needed.',
				'min'           => 1,
				'max'           => 60,
				'step'          => 1,
				'placeholder'   => '6',
				'wrapper'       => [ 'width' => '20' ],
			],
			[
				'key'           => 'field_art_source_note',
				'label'         => 'Source Note',
				'name'          => 'art_source_note',
				'type'          => 'text',
				'required'      => 0,
				'instructions'  => 'Internal use only. Not displayed publicly. Note if this was adapted from a LinkedIn newsletter issue or other source.',
				'placeholder'   => 'e.g. Adapted from LinkedIn Newsletter issue 42, March 2024',
				'wrapper'       => [ 'width' => '80' ],
			],

			// ── Tab: Flags ────────────────────────────────────────────────

			[
				'key'   => 'field_art_tab_flags',
				'label' => 'Flags',
				'type'  => 'tab',
			],
			[
				'key'           => 'field_art_cornerstone',
				'label'         => 'Cornerstone Article',
				'name'          => 'art_cornerstone',
				'type'          => 'true_false',
				'required'      => 0,
				'instructions'  => 'Mark as a cornerstone piece — a definitive article on its topic. Used for SEO topic cluster logic and internal linking priority.',
				'ui'            => 1,
				'default_value' => 0,
				'ui_on_text'    => 'Cornerstone',
				'ui_off_text'   => 'Standard',
				'wrapper'       => [ 'width' => '33' ],
			],
			[
				'key'           => 'field_art_featured',
				'label'         => 'Featured Article',
				'name'          => 'art_featured',
				'type'          => 'true_false',
				'required'      => 0,
				'instructions'  => 'Pin this article to the featured slot on The Future of Luxury archive page. Only one article should be featured at a time.',
				'ui'            => 1,
				'default_value' => 0,
				'ui_on_text'    => 'Featured',
				'ui_off_text'   => 'Standard',
				'wrapper'       => [ 'width' => '33' ],
			],
			[
				'key'           => 'field_art_subscribe_prompt',
				'label'         => 'Show Subscribe Prompt',
				'name'          => 'art_subscribe_prompt',
				'type'          => 'true_false',
				'required'      => 0,
				'instructions'  => 'Show the newsletter subscribe module at the foot of this article. On by default.',
				'ui'            => 1,
				'default_value' => 1,
				'ui_on_text'    => 'Show',
				'ui_off_text'   => 'Hide',
				'wrapper'       => [ 'width' => '33' ],
			],

			// ── Tab: Relationships ────────────────────────────────────────

			[
				'key'   => 'field_art_tab_relationships',
				'label' => 'Relationships',
				'type'  => 'tab',
			],
			[
				'key'               => 'field_art_related_articles',
				'label'             => 'Related Articles',
				'name'              => 'art_related_articles',
				'type'              => 'relationship',
				'required'          => 0,
				'instructions'      => 'Select up to 3 related articles. These appear in the Related Reading block at the foot of the article.',
				'post_type'         => [ 'article' ],
				'filters'           => [ 'search', 'taxonomy' ],
				'taxonomy'          => [ 'article_category' ],
				'elements'          => [ 'featured_image' ],
				'min'               => 0,
				'max'               => 3,
				'return_format'     => 'object',
			],
			[
				'key'               => 'field_art_related_case',
				'label'             => 'Related Case Study',
				'name'              => 'art_related_case',
				'type'              => 'post_object',
				'required'          => 0,
				'instructions'      => 'Optional. Link one case study where the article\'s themes are demonstrated in practice.',
				'post_type'         => [ 'case_study' ],
				'allow_null'        => 1,
				'multiple'          => 0,
				'return_format'     => 'object',
				'ui'                => 1,
			],
			[
				'key'               => 'field_art_related_episode',
				'label'             => 'Related Podcast Episode',
				'name'              => 'art_related_episode',
				'type'              => 'post_object',
				'required'          => 0,
				'instructions'      => 'Optional. Link one Tastemakers episode that deepens or extends this article.',
				'post_type'         => [ 'podcast_episode' ],
				'allow_null'        => 1,
				'multiple'          => 0,
				'return_format'     => 'object',
				'ui'                => 1,
			],
			[
				'key'               => 'field_art_related_download',
				'label'             => 'Related Download',
				'name'              => 'art_related_download',
				'type'              => 'post_object',
				'required'          => 0,
				'instructions'      => 'Optional. Link one downloadable asset relevant to the article.',
				'post_type'         => [ 'download' ],
				'allow_null'        => 1,
				'multiple'          => 0,
				'return_format'     => 'object',
				'ui'                => 1,
			],

			// ── Tab: SEO ──────────────────────────────────────────────────

			[
				'key'   => 'field_art_tab_seo',
				'label' => 'SEO',
				'type'  => 'tab',
			],
			[
				'key'           => 'field_art_seo_title',
				'label'         => 'SEO Title',
				'name'          => 'art_seo_title',
				'type'          => 'text',
				'required'      => 1,
				'instructions'  => 'Target 50–60 characters. Appears in browser tab and search results.',
				'maxlength'     => 70,
				'placeholder'   => 'e.g. Why Luxury Brands Lose on Price | The Future of Luxury',
				'wrapper'       => [ 'width' => '100' ],
			],
			[
				'key'           => 'field_art_meta_desc',
				'label'         => 'Meta Description',
				'name'          => 'art_meta_desc',
				'type'          => 'textarea',
				'required'      => 1,
				'instructions'  => 'Target 140–160 characters. This is what appears in Google search results beneath the title.',
				'rows'          => 3,
				'maxlength'     => 200,
				'new_lines'     => '',
			],
			[
				'key'           => 'field_art_og_image',
				'label'         => 'Open Graph Image',
				'name'          => 'art_og_image',
				'type'          => 'image',
				'required'      => 1,
				'instructions'  => 'Minimum 1200×630px. Used when this article is shared on social or messaging platforms.',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'library'       => 'all',
				'min_width'     => 1200,
				'min_height'    => 630,
			],
			[
				'key'           => 'field_art_canonical',
				'label'         => 'Canonical URL',
				'name'          => 'art_canonical',
				'type'          => 'url',
				'required'      => 0,
				'instructions'  => 'Set only if this article was originally published at a different URL (e.g. LinkedIn Newsletter) and that version is the canonical source. Leave blank in most cases.',
				'placeholder'   => 'https://www.linkedin.com/pulse/...',
			],
			[
				'key'           => 'field_art_noindex',
				'label'         => 'Noindex this article',
				'name'          => 'art_noindex',
				'type'          => 'true_false',
				'required'      => 0,
				'instructions'  => 'Tick to exclude from search engines. Use during migration review before articles are ready to index.',
				'ui'            => 1,
				'default_value' => 0,
				'ui_on_text'    => 'Noindex',
				'ui_off_text'   => 'Index',
			],
		],
		'location'              => [
			[ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'article' ] ],
		],
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'seamless',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => [],
		'active'                => true,
	] );
}


// ═════════════════════════════════════════════════════════════════════════════
// 3. PODCAST SEASON
// Post type: podcast_season | Slug base: /tastemakers/[season-slug]
// ═════════════════════════════════════════════════════════════════════════════

function summit_acf_podcast_season(): void {
	acf_add_local_field_group( [
		'key'                   => 'group_summit_podcast_season',
		'title'                 => 'Podcast Season Fields',
		'fields'                => [

			// ── Tab: Season Identity ──────────────────────────────────────

			[
				'key'   => 'field_ps_tab_identity',
				'label' => 'Season Identity',
				'type'  => 'tab',
				'placement' => 'top',
			],
			[
				'key'           => 'field_ps_season_number',
				'label'         => 'Season Number',
				'name'          => 'ps_season_number',
				'type'          => 'number',
				'required'      => 1,
				'instructions'  => 'Numeric. Governs ordering across the archive.',
				'min'           => 1,
				'max'           => 99,
				'step'          => 1,
				'placeholder'   => '1',
				'wrapper'       => [ 'width' => '20' ],
			],
			[
				'key'           => 'field_ps_subtitle',
				'label'         => 'Season Subtitle',
				'name'          => 'ps_subtitle',
				'type'          => 'text',
				'required'      => 0,
				'instructions'  => 'The season\'s editorial title. e.g. "The New Value Equation".',
				'placeholder'   => 'e.g. The New Value Equation',
				'wrapper'       => [ 'width' => '80' ],
			],
			[
				'key'           => 'field_ps_theme',
				'label'         => 'Central Theme',
				'name'          => 'ps_theme',
				'type'          => 'text',
				'required'      => 1,
				'instructions'  => 'One sentence. The unifying argument or question this season explores.',
				'placeholder'   => 'e.g. What does value actually mean in modern luxury?',
			],
			[
				'key'           => 'field_ps_thesis',
				'label'         => 'Season Thesis',
				'name'          => 'ps_thesis',
				'type'          => 'textarea',
				'required'      => 0,
				'instructions'  => 'Extended editorial framing. Two to four paragraphs. Displayed on the season landing page beneath the hero.',
				'rows'          => 5,
				'new_lines'     => 'wpautop',
			],
			[
				'key'           => 'field_ps_artwork',
				'label'         => 'Season Artwork',
				'name'          => 'ps_artwork',
				'type'          => 'image',
				'required'      => 1,
				'instructions'  => 'Season cover artwork. Minimum 3000×3000px for podcast directory compatibility (Apple Podcasts, Spotify). Also used as the featured image.',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'library'       => 'all',
				'min_width'     => 3000,
				'min_height'    => 3000,
			],

			// ── Tab: Media & Platforms ────────────────────────────────────

			[
				'key'   => 'field_ps_tab_media',
				'label' => 'Media & Platforms',
				'type'  => 'tab',
			],
			[
				'key'           => 'field_ps_trailer_embed',
				'label'         => 'Season Trailer Embed',
				'name'          => 'ps_trailer_embed',
				'type'          => 'text',
				'required'      => 0,
				'instructions'  => 'JW Player URL or audio embed code for the season trailer. Displayed in the season hero.',
				'placeholder'   => 'https://content.jwplatform.com/players/...',
			],
			[
				'key'               => 'field_ps_platform_links',
				'label'             => 'Platform Links',
				'name'              => 'ps_platform_links',
				'type'              => 'repeater',
				'required'          => 0,
				'instructions'      => 'Add listening platform links. Each row is one platform.',
				'min'               => 0,
				'max'               => 8,
				'layout'            => 'table',
				'button_label'      => 'Add Platform',
				'sub_fields'        => [
					[
						'key'           => 'field_ps_platform_name',
						'label'         => 'Platform',
						'name'          => 'platform_name',
						'type'          => 'select',
						'required'      => 1,
						'choices'       => [
							'apple'     => 'Apple Podcasts',
							'spotify'   => 'Spotify',
							'overcast'  => 'Overcast',
							'pocket'    => 'Pocket Casts',
							'amazon'    => 'Amazon Music',
							'youtube'   => 'YouTube',
							'rss'       => 'RSS Feed',
							'other'     => 'Other',
						],
						'default_value' => 'apple',
						'wrapper'       => [ 'width' => '30' ],
					],
					[
						'key'           => 'field_ps_platform_url',
						'label'         => 'URL',
						'name'          => 'platform_url',
						'type'          => 'url',
						'required'      => 1,
						'placeholder'   => 'https://podcasts.apple.com/...',
						'wrapper'       => [ 'width' => '70' ],
					],
				],
			],

			// ── Tab: Relationships & Flags ────────────────────────────────

			[
				'key'   => 'field_ps_tab_meta',
				'label' => 'Relationships & Flags',
				'type'  => 'tab',
			],
			[
				'key'               => 'field_ps_featured_episode',
				'label'             => 'Featured Episode',
				'name'              => 'ps_featured_episode',
				'type'              => 'post_object',
				'required'          => 0,
				'instructions'      => 'Pinned episode for the season page hero. Typically the best episode of the season.',
				'post_type'         => [ 'podcast_episode' ],
				'allow_null'        => 1,
				'multiple'          => 0,
				'return_format'     => 'object',
				'ui'                => 1,
			],
			[
				'key'               => 'field_ps_related_articles',
				'label'             => 'Related Articles',
				'name'              => 'ps_related_articles',
				'type'              => 'relationship',
				'required'          => 0,
				'instructions'      => 'Link up to 3 Future of Luxury articles that extend the season\'s themes.',
				'post_type'         => [ 'article' ],
				'filters'           => [ 'search' ],
				'elements'          => [ 'featured_image' ],
				'min'               => 0,
				'max'               => 3,
				'return_format'     => 'object',
			],
			[
				'key'           => 'field_ps_is_featured',
				'label'         => 'Current / Active Season',
				'name'          => 'ps_is_featured',
				'type'          => 'true_false',
				'required'      => 0,
				'instructions'  => 'Mark this as the current active season. It will be surfaced prominently on the Tastemakers landing page. Only one season should be marked active.',
				'ui'            => 1,
				'default_value' => 0,
				'ui_on_text'    => 'Active',
				'ui_off_text'   => 'Archive',
			],

			// ── Tab: SEO ──────────────────────────────────────────────────

			[
				'key'   => 'field_ps_tab_seo',
				'label' => 'SEO',
				'type'  => 'tab',
			],
			[
				'key'           => 'field_ps_seo_title',
				'label'         => 'SEO Title',
				'name'          => 'ps_seo_title',
				'type'          => 'text',
				'required'      => 1,
				'instructions'  => 'Target 50–60 characters.',
				'maxlength'     => 70,
				'placeholder'   => 'e.g. Season One: The New Value Equation | Tastemakers',
				'wrapper'       => [ 'width' => '100' ],
			],
			[
				'key'           => 'field_ps_meta_desc',
				'label'         => 'Meta Description',
				'name'          => 'ps_meta_desc',
				'type'          => 'textarea',
				'required'      => 1,
				'instructions'  => 'Target 140–160 characters.',
				'rows'          => 3,
				'maxlength'     => 200,
				'new_lines'     => '',
			],
			[
				'key'           => 'field_ps_og_image',
				'label'         => 'Open Graph Image',
				'name'          => 'ps_og_image',
				'type'          => 'image',
				'required'      => 1,
				'instructions'  => 'Minimum 1200×630px. Typically the season artwork cropped to landscape.',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'library'       => 'all',
				'min_width'     => 1200,
				'min_height'    => 630,
			],
		],
		'location'              => [
			[ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'podcast_season' ] ],
		],
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'seamless',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => [],
		'active'                => true,
	] );
}


// ═════════════════════════════════════════════════════════════════════════════
// 4. PODCAST EPISODE
// Post type: podcast_episode | Slug base: /tastemakers/episodes/[episode-slug]
// ═════════════════════════════════════════════════════════════════════════════

function summit_acf_podcast_episode(): void {
	acf_add_local_field_group( [
		'key'                   => 'group_summit_podcast_episode',
		'title'                 => 'Podcast Episode Fields',
		'fields'                => [

			// ── Tab: Episode Identity ─────────────────────────────────────

			[
				'key'   => 'field_ep_tab_identity',
				'label' => 'Episode Identity',
				'type'  => 'tab',
				'placement' => 'top',
			],
			[
				'key'               => 'field_ep_season',
				'label'             => 'Season',
				'name'              => 'ep_season',
				'type'              => 'post_object',
				'required'          => 1,
				'instructions'      => 'Select the season this episode belongs to. Required before publishing.',
				'post_type'         => [ 'podcast_season' ],
				'allow_null'        => 0,
				'multiple'          => 0,
				'return_format'     => 'object',
				'ui'                => 1,
				'wrapper'           => [ 'width' => '50' ],
			],
			[
				'key'           => 'field_ep_episode_number',
				'label'         => 'Episode Number',
				'name'          => 'ep_episode_number',
				'type'          => 'number',
				'required'      => 1,
				'instructions'  => 'Episode number within the season.',
				'min'           => 1,
				'max'           => 999,
				'step'          => 1,
				'placeholder'   => '1',
				'wrapper'       => [ 'width' => '25' ],
			],
			[
				'key'           => 'field_ep_duration',
				'label'         => 'Duration',
				'name'          => 'ep_duration',
				'type'          => 'text',
				'required'      => 0,
				'instructions'  => 'Display string. e.g. "48 min" or "1 hr 4 min".',
				'placeholder'   => '48 min',
				'wrapper'       => [ 'width' => '25' ],
			],
			[
				'key'           => 'field_ep_deck',
				'label'         => 'Episode Deck / Standfirst',
				'name'          => 'ep_deck',
				'type'          => 'textarea',
				'required'      => 0,
				'instructions'  => 'Two or three sentences. Appears in the episode hero and episode cards.',
				'rows'          => 3,
				'new_lines'     => 'br',
				'maxlength'     => 400,
			],
			[
				'key'           => 'field_ep_pull_quote',
				'label'         => 'Pull Quote',
				'name'          => 'ep_pull_quote',
				'type'          => 'textarea',
				'required'      => 0,
				'instructions'  => 'A strong quote from the conversation. Used in the pull quote block on the episode page and in social promotional assets.',
				'rows'          => 3,
				'new_lines'     => 'br',
			],

			// ── Tab: Guest ────────────────────────────────────────────────

			[
				'key'   => 'field_ep_tab_guest',
				'label' => 'Guest',
				'type'  => 'tab',
			],
			[
				'key'           => 'field_ep_guest_name',
				'label'         => 'Guest Name',
				'name'          => 'ep_guest_name',
				'type'          => 'text',
				'required'      => 1,
				'instructions'  => 'Full name as it should appear publicly.',
				'placeholder'   => 'e.g. Sarah Andelman',
				'wrapper'       => [ 'width' => '50' ],
			],
			[
				'key'           => 'field_ep_guest_title',
				'label'         => 'Guest Title',
				'name'          => 'ep_guest_title',
				'type'          => 'text',
				'required'      => 1,
				'instructions'  => 'Job title or primary role.',
				'placeholder'   => 'e.g. Founder',
				'wrapper'       => [ 'width' => '50' ],
			],
			[
				'key'           => 'field_ep_guest_company',
				'label'         => 'Guest Company / Organisation',
				'name'          => 'ep_guest_company',
				'type'          => 'text',
				'required'      => 1,
				'instructions'  => 'Current primary organisation.',
				'placeholder'   => 'e.g. Colette',
				'wrapper'       => [ 'width' => '50' ],
			],
			[
				'key'           => 'field_ep_guest_bio',
				'label'         => 'Guest Biography',
				'name'          => 'ep_guest_bio',
				'type'          => 'textarea',
				'required'      => 0,
				'instructions'  => 'Two to three sentences. Displayed in the guest module on the episode page.',
				'rows'          => 4,
				'new_lines'     => 'br',
			],
			[
				'key'           => 'field_ep_guest_portrait',
				'label'         => 'Guest Portrait',
				'name'          => 'ep_guest_portrait',
				'type'          => 'image',
				'required'      => 0,
				'instructions'  => 'Minimum 600×600px. Used in the guest module. Square crop preferred.',
				'return_format' => 'array',
				'preview_size'  => 'thumbnail',
				'library'       => 'all',
				'min_width'     => 600,
				'min_height'    => 600,
			],

			// ── Tab: Media ────────────────────────────────────────────────

			[
				'key'   => 'field_ep_tab_media',
				'label' => 'Media',
				'type'  => 'tab',
			],
			[
				'key'           => 'field_ep_audio_embed',
				'label'         => 'Audio Embed',
				'name'          => 'ep_audio_embed',
				'type'          => 'text',
				'required'      => 1,
				'instructions'  => 'Required before publishing. Accepts a direct audio file URL, an embed code, or a JW Player URL. This is the primary listening source on the episode page.',
				'placeholder'   => 'https://... or embed code',
			],
			[
				'key'           => 'field_ep_video_embed',
				'label'         => 'Video Embed',
				'name'          => 'ep_video_embed',
				'type'          => 'text',
				'required'      => 0,
				'instructions'  => 'Optional. JW Player URL or embed code for the video version of the episode.',
				'placeholder'   => 'https://content.jwplatform.com/players/...',
			],
			[
				'key'               => 'field_ep_platform_links',
				'label'             => 'Platform Links',
				'name'              => 'ep_platform_links',
				'type'              => 'repeater',
				'required'          => 0,
				'instructions'      => 'Episode-specific platform links. Leave blank to inherit from the season.',
				'min'               => 0,
				'max'               => 8,
				'layout'            => 'table',
				'button_label'      => 'Add Platform',
				'sub_fields'        => [
					[
						'key'           => 'field_ep_platform_name',
						'label'         => 'Platform',
						'name'          => 'platform_name',
						'type'          => 'select',
						'required'      => 1,
						'choices'       => [
							'apple'     => 'Apple Podcasts',
							'spotify'   => 'Spotify',
							'overcast'  => 'Overcast',
							'pocket'    => 'Pocket Casts',
							'amazon'    => 'Amazon Music',
							'youtube'   => 'YouTube',
							'rss'       => 'RSS Feed',
							'other'     => 'Other',
						],
						'default_value' => 'apple',
						'wrapper'       => [ 'width' => '30' ],
					],
					[
						'key'           => 'field_ep_platform_url',
						'label'         => 'URL',
						'name'          => 'platform_url',
						'type'          => 'url',
						'required'      => 1,
						'placeholder'   => 'https://podcasts.apple.com/...',
						'wrapper'       => [ 'width' => '70' ],
					],
				],
			],
			[
				'key'           => 'field_ep_sponsor_credits',
				'label'         => 'Sponsor Credits',
				'name'          => 'ep_sponsor_credits',
				'type'          => 'textarea',
				'required'      => 0,
				'instructions'  => 'Optional. Sponsor acknowledgement text for this episode. Not displayed publicly unless the show notes template renders it.',
				'rows'          => 2,
				'new_lines'     => 'br',
			],

			// ── Tab: Content ──────────────────────────────────────────────

			[
				'key'   => 'field_ep_tab_content',
				'label' => 'Content',
				'type'  => 'tab',
			],
			[
				'key'           => 'field_ep_show_notes',
				'label'         => 'Show Notes',
				'name'          => 'ep_show_notes',
				'type'          => 'wysiwyg',
				'required'      => 0,
				'instructions'  => 'Key topics, timestamps, references and links from the episode. Shown in the show notes block.',
				'tabs'          => 'all',
				'toolbar'       => 'basic',
				'media_upload'  => 0,
				'delay'         => 0,
			],
			[
				'key'           => 'field_ep_transcript',
				'label'         => 'Transcript',
				'name'          => 'ep_transcript',
				'type'          => 'wysiwyg',
				'required'      => 1,
				'instructions'  => 'Required before publishing. Must be a cleaned, speaker-labelled transcript — not raw machine output. Format: Speaker Name: dialogue. Use H2 for major section breaks if appropriate.',
				'tabs'          => 'all',
				'toolbar'       => 'full',
				'media_upload'  => 0,
				'delay'         => 1,
			],
			[
				'key'           => 'field_ep_transcript_visible',
				'label'         => 'Transcript Visible',
				'name'          => 'ep_transcript_visible',
				'type'          => 'true_false',
				'required'      => 0,
				'instructions'  => 'Show the transcript publicly on the episode page. Disable during production if the transcript is not yet clean.',
				'ui'            => 1,
				'default_value' => 1,
				'ui_on_text'    => 'Visible',
				'ui_off_text'   => 'Hidden',
			],

			// ── Tab: Relationships & Flags ────────────────────────────────

			[
				'key'   => 'field_ep_tab_relationships',
				'label' => 'Relationships & Flags',
				'type'  => 'tab',
			],
			[
				'key'               => 'field_ep_related_articles',
				'label'             => 'Related Articles',
				'name'              => 'ep_related_articles',
				'type'              => 'relationship',
				'required'          => 0,
				'instructions'      => 'Link up to 2 Future of Luxury articles that extend the episode themes.',
				'post_type'         => [ 'article' ],
				'filters'           => [ 'search' ],
				'elements'          => [ 'featured_image' ],
				'min'               => 0,
				'max'               => 2,
				'return_format'     => 'object',
			],
			[
				'key'               => 'field_ep_related_episodes',
				'label'             => 'Related Episodes',
				'name'              => 'ep_related_episodes',
				'type'              => 'relationship',
				'required'          => 0,
				'instructions'      => 'Link up to 3 related episodes. These appear in the Related Episodes block.',
				'post_type'         => [ 'podcast_episode' ],
				'filters'           => [ 'search' ],
				'elements'          => [ 'featured_image' ],
				'min'               => 0,
				'max'               => 3,
				'return_format'     => 'object',
			],
			[
				'key'               => 'field_ep_related_download',
				'label'             => 'Related Download',
				'name'              => 'ep_related_download',
				'type'              => 'post_object',
				'required'          => 0,
				'instructions'      => 'Optional. Link one downloadable asset relevant to this episode.',
				'post_type'         => [ 'download' ],
				'allow_null'        => 1,
				'multiple'          => 0,
				'return_format'     => 'object',
				'ui'                => 1,
			],
			[
				'key'           => 'field_ep_is_featured',
				'label'         => 'Featured Episode',
				'name'          => 'ep_is_featured',
				'type'          => 'true_false',
				'required'      => 0,
				'instructions'  => 'Pin this episode for featured placement on the season or Tastemakers landing page.',
				'ui'            => 1,
				'default_value' => 0,
				'ui_on_text'    => 'Featured',
				'ui_off_text'   => 'Standard',
			],

			// ── Tab: SEO ──────────────────────────────────────────────────

			[
				'key'   => 'field_ep_tab_seo',
				'label' => 'SEO',
				'type'  => 'tab',
			],
			[
				'key'           => 'field_ep_seo_title',
				'label'         => 'SEO Title',
				'name'          => 'ep_seo_title',
				'type'          => 'text',
				'required'      => 1,
				'instructions'  => 'Target 50–60 characters. Include guest name and episode topic.',
				'maxlength'     => 70,
				'placeholder'   => 'e.g. Sarah Andelman on the Future of Concept Retail | Tastemakers',
				'wrapper'       => [ 'width' => '100' ],
			],
			[
				'key'           => 'field_ep_meta_desc',
				'label'         => 'Meta Description',
				'name'          => 'ep_meta_desc',
				'type'          => 'textarea',
				'required'      => 1,
				'instructions'  => 'Target 140–160 characters.',
				'rows'          => 3,
				'maxlength'     => 200,
				'new_lines'     => '',
			],
			[
				'key'           => 'field_ep_og_image',
				'label'         => 'Open Graph Image',
				'name'          => 'ep_og_image',
				'type'          => 'image',
				'required'      => 1,
				'instructions'  => 'Minimum 1200×630px. Typically episode artwork or a portrait of the guest.',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'library'       => 'all',
				'min_width'     => 1200,
				'min_height'    => 630,
			],
			[
				'key'           => 'field_ep_canonical',
				'label'         => 'Canonical URL',
				'name'          => 'ep_canonical',
				'type'          => 'url',
				'required'      => 0,
				'instructions'  => 'Leave blank in almost all cases.',
				'placeholder'   => 'https://tastemakers.fm/episodes/...',
			],
		],
		'location'              => [
			[ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'podcast_episode' ] ],
		],
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'seamless',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => [],
		'active'                => true,
	] );
}


// ═════════════════════════════════════════════════════════════════════════════
// 5. DOWNLOAD
// Post type: download | Slug base: /downloads/[document-slug]
// ═════════════════════════════════════════════════════════════════════════════

function summit_acf_download(): void {
	acf_add_local_field_group( [
		'key'                   => 'group_summit_download',
		'title'                 => 'Download Fields',
		'fields'                => [

			// ── Tab: Asset ────────────────────────────────────────────────

			[
				'key'   => 'field_dl_tab_asset',
				'label' => 'Asset',
				'type'  => 'tab',
				'placement' => 'top',
			],
			[
				'key'           => 'field_dl_description',
				'label'         => 'Description',
				'name'          => 'dl_description',
				'type'          => 'textarea',
				'required'      => 1,
				'instructions'  => 'Two to four sentences. Explains what this asset is and why it is worth downloading. Shown in download cards and the download landing page.',
				'rows'          => 4,
				'new_lines'     => 'br',
			],
			[
				'key'           => 'field_dl_file',
				'label'         => 'File',
				'name'          => 'dl_file',
				'type'          => 'file',
				'required'      => 1,
				'instructions'  => 'Upload the download file. PDF is preferred. The file URL is used directly for ungated downloads.',
				'return_format' => 'array',
				'library'       => 'all',
				'mime_types'    => 'pdf,epub,zip,doc,docx',
			],
			[
				'key'           => 'field_dl_file_type',
				'label'         => 'File Type Label',
				'name'          => 'dl_file_type',
				'type'          => 'select',
				'required'      => 1,
				'instructions'  => 'Displayed on the download card badge.',
				'choices'       => [
					'PDF'   => 'PDF',
					'EPUB'  => 'EPUB',
					'ZIP'   => 'ZIP',
					'DOCX'  => 'DOCX',
					'Other' => 'Other',
				],
				'default_value' => 'PDF',
				'wrapper'       => [ 'width' => '25' ],
			],
			[
				'key'           => 'field_dl_file_size',
				'label'         => 'File Size',
				'name'          => 'dl_file_size',
				'type'          => 'text',
				'required'      => 0,
				'instructions'  => 'Display string. e.g. "4.2 MB". Optional — shown on download card if provided.',
				'placeholder'   => 'e.g. 4.2 MB',
				'wrapper'       => [ 'width' => '25' ],
			],
			[
				'key'           => 'field_dl_cover_image',
				'label'         => 'Cover Image',
				'name'          => 'dl_cover_image',
				'type'          => 'image',
				'required'      => 0,
				'instructions'  => 'Visual cover for the download card. Minimum 600×800px (portrait preferred). Used as the featured image fallback if not set separately.',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'library'       => 'all',
				'min_width'     => 600,
				'min_height'    => 600,
			],
			[
				'key'           => 'field_dl_cta_label',
				'label'         => 'Download CTA Label',
				'name'          => 'dl_cta_label',
				'type'          => 'text',
				'required'      => 0,
				'instructions'  => 'Overrides the default "Download" button label.',
				'placeholder'   => 'e.g. Download the Report',
				'wrapper'       => [ 'width' => '50' ],
			],
			[
				'key'           => 'field_dl_gated',
				'label'         => 'Gated Download',
				'name'          => 'dl_gated',
				'type'          => 'true_false',
				'required'      => 0,
				'instructions'  => 'When enabled, the download requires email capture before the file is accessible. Gate logic connects to ActiveCampaign. Off by default in v1.0.',
				'ui'            => 1,
				'default_value' => 0,
				'ui_on_text'    => 'Gated',
				'ui_off_text'   => 'Free',
				'wrapper'       => [ 'width' => '50' ],
			],

			// ── Tab: Relationships ────────────────────────────────────────

			[
				'key'   => 'field_dl_tab_relationships',
				'label' => 'Relationships',
				'type'  => 'tab',
			],
			[
				'key'               => 'field_dl_related_article',
				'label'             => 'Related Articles',
				'name'              => 'dl_related_article',
				'type'              => 'relationship',
				'required'          => 0,
				'instructions'      => 'Link up to 2 articles that extend or contextualise this download.',
				'post_type'         => [ 'article' ],
				'filters'           => [ 'search' ],
				'elements'          => [ 'featured_image' ],
				'min'               => 0,
				'max'               => 2,
				'return_format'     => 'object',
			],
			[
				'key'               => 'field_dl_related_case',
				'label'             => 'Related Case Study',
				'name'              => 'dl_related_case',
				'type'              => 'post_object',
				'required'          => 0,
				'instructions'      => 'Optional. Link a case study where this asset was used or is relevant.',
				'post_type'         => [ 'case_study' ],
				'allow_null'        => 1,
				'multiple'          => 0,
				'return_format'     => 'object',
				'ui'                => 1,
			],
			[
				'key'               => 'field_dl_related_episode',
				'label'             => 'Related Podcast Episode',
				'name'              => 'dl_related_episode',
				'type'              => 'post_object',
				'required'          => 0,
				'instructions'      => 'Optional. Link a Tastemakers episode where the download is referenced or relevant.',
				'post_type'         => [ 'podcast_episode' ],
				'allow_null'        => 1,
				'multiple'          => 0,
				'return_format'     => 'object',
				'ui'                => 1,
			],

			// ── Tab: SEO ──────────────────────────────────────────────────

			[
				'key'   => 'field_dl_tab_seo',
				'label' => 'SEO',
				'type'  => 'tab',
			],
			[
				'key'           => 'field_dl_seo_title',
				'label'         => 'SEO Title',
				'name'          => 'dl_seo_title',
				'type'          => 'text',
				'required'      => 1,
				'instructions'  => 'Target 50–60 characters.',
				'maxlength'     => 70,
				'placeholder'   => 'e.g. The Characteristics of Luxury — Free Download | Summit',
				'wrapper'       => [ 'width' => '100' ],
			],
			[
				'key'           => 'field_dl_meta_desc',
				'label'         => 'Meta Description',
				'name'          => 'dl_meta_desc',
				'type'          => 'textarea',
				'required'      => 1,
				'instructions'  => 'Target 140–160 characters.',
				'rows'          => 3,
				'maxlength'     => 200,
				'new_lines'     => '',
			],
			[
				'key'           => 'field_dl_og_image',
				'label'         => 'Open Graph Image',
				'name'          => 'dl_og_image',
				'type'          => 'image',
				'required'      => 1,
				'instructions'  => 'Minimum 1200×630px. Typically the cover image cropped to landscape.',
				'return_format' => 'array',
				'preview_size'  => 'medium',
				'library'       => 'all',
				'min_width'     => 1200,
				'min_height'    => 630,
			],
		],
		'location'              => [
			[ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'download' ] ],
		],
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'seamless',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => [],
		'active'                => true,
	] );
}


// ═════════════════════════════════════════════════════════════════════════════
// 6. ENQUIRY
// Post type: enquiry (private, admin-only — public => false)
// This is an internal CRM record, not a front-end content type.
// Primary record lives in ActiveCampaign; this is the WP admin convenience log.
// ═════════════════════════════════════════════════════════════════════════════

function summit_acf_enquiry(): void {
	acf_add_local_field_group( [
		'key'                   => 'group_summit_enquiry',
		'title'                 => 'Enquiry Details',
		'fields'                => [

			// ── Tab: Contact ──────────────────────────────────────────────

			[
				'key'   => 'field_enq_tab_contact',
				'label' => 'Contact',
				'type'  => 'tab',
				'placement' => 'top',
			],
			[
				'key'           => 'field_enq_first_name',
				'label'         => 'First Name',
				'name'          => 'enq_first_name',
				'type'          => 'text',
				'required'      => 1,
				'wrapper'       => [ 'width' => '50' ],
			],
			[
				'key'           => 'field_enq_last_name',
				'label'         => 'Last Name',
				'name'          => 'enq_last_name',
				'type'          => 'text',
				'required'      => 1,
				'wrapper'       => [ 'width' => '50' ],
			],
			[
				'key'           => 'field_enq_email',
				'label'         => 'Email Address',
				'name'          => 'enq_email',
				'type'          => 'email',
				'required'      => 1,
				'wrapper'       => [ 'width' => '50' ],
			],
			[
				'key'           => 'field_enq_phone',
				'label'         => 'Phone Number',
				'name'          => 'enq_phone',
				'type'          => 'text',
				'required'      => 0,
				'wrapper'       => [ 'width' => '50' ],
			],
			[
				'key'           => 'field_enq_company',
				'label'         => 'Company',
				'name'          => 'enq_company',
				'type'          => 'text',
				'required'      => 0,
				'wrapper'       => [ 'width' => '50' ],
			],
			[
				'key'           => 'field_enq_job_title',
				'label'         => 'Job Title',
				'name'          => 'enq_job_title',
				'type'          => 'text',
				'required'      => 0,
				'wrapper'       => [ 'width' => '50' ],
			],

			// ── Tab: Enquiry Detail ───────────────────────────────────────

			[
				'key'   => 'field_enq_tab_detail',
				'label' => 'Enquiry Detail',
				'type'  => 'tab',
			],
			[
				'key'           => 'field_enq_type',
				'label'         => 'Enquiry Type',
				'name'          => 'enq_type',
				'type'          => 'select',
				'required'      => 1,
				'instructions'  => 'The enquiry category as submitted.',
				'choices'       => [
					'brand_strategy'        => 'Brand Strategy',
					'experience_design'     => 'Experience Design',
					'digital_transformation'=> 'Digital Transformation',
					'work_showcase'         => 'Work Showcase Enquiry',
					'tastemakers'           => 'Tastemakers Podcast',
					'media_speaking'        => 'Media or Speaking',
					'partnership'           => 'Partnership',
					'other'                 => 'Other',
				],
				'default_value' => 'other',
				'allow_null'    => 0,
				'ui'            => 1,
			],
			[
				'key'           => 'field_enq_project_overview',
				'label'         => 'Project Overview',
				'name'          => 'enq_project_overview',
				'type'          => 'textarea',
				'required'      => 0,
				'instructions'  => 'As submitted via the form.',
				'rows'          => 5,
				'new_lines'     => 'br',
			],
			[
				'key'           => 'field_enq_budget',
				'label'         => 'Budget Range',
				'name'          => 'enq_budget',
				'type'          => 'select',
				'required'      => 0,
				'instructions'  => 'As selected on the form.',
				'choices'       => [
					'under_10k'     => 'Under £10k',
					'10k_25k'       => '£10k – £25k',
					'25k_50k'       => '£25k – £50k',
					'50k_100k'      => '£50k – £100k',
					'100k_250k'     => '£100k – £250k',
					'over_250k'     => 'Over £250k',
					'tbc'           => 'To be discussed',
				],
				'allow_null'    => 1,
				'ui'            => 1,
				'wrapper'       => [ 'width' => '50' ],
			],
			[
				'key'           => 'field_enq_timeline',
				'label'         => 'Timeline',
				'name'          => 'enq_timeline',
				'type'          => 'select',
				'required'      => 0,
				'instructions'  => 'As selected on the form.',
				'choices'       => [
					'asap'          => 'As soon as possible',
					'1_3_months'    => '1–3 months',
					'3_6_months'    => '3–6 months',
					'6_plus_months' => '6+ months',
					'exploratory'   => 'Exploratory — no fixed timeline',
				],
				'allow_null'    => 1,
				'ui'            => 1,
				'wrapper'       => [ 'width' => '50' ],
			],
			[
				'key'           => 'field_enq_form_source',
				'label'         => 'Form Source',
				'name'          => 'enq_form_source',
				'type'          => 'text',
				'required'      => 0,
				'instructions'  => 'Which form or page originated this enquiry. Populated automatically where possible.',
				'placeholder'   => 'e.g. /design-tomorrow — Connect tab',
				'wrapper'       => [ 'width' => '50' ],
			],
			[
				'key'           => 'field_enq_submission_date',
				'label'         => 'Submission Date',
				'name'          => 'enq_submission_date',
				'type'          => 'date_time_picker',
				'required'      => 0,
				'instructions'  => 'Set automatically on form submission.',
				'display_format'=> 'd/m/Y H:i',
				'return_format' => 'd/m/Y H:i',
				'first_day'     => 1,
				'wrapper'       => [ 'width' => '50' ],
			],

			// ── Tab: Internal Handling ────────────────────────────────────

			[
				'key'   => 'field_enq_tab_internal',
				'label' => 'Internal Handling',
				'type'  => 'tab',
			],
			[
				'key'           => 'field_enq_status',
				'label'         => 'Status',
				'name'          => 'enq_status',
				'type'          => 'select',
				'required'      => 1,
				'instructions'  => 'Internal handling status. Update as the enquiry progresses.',
				'choices'       => [
					'new'           => 'New',
					'reviewed'      => 'Reviewed',
					'qualified'     => 'Qualified',
					'responded'     => 'Responded',
					'scheduled'     => 'Scheduled',
					'closed'        => 'Closed',
					'archived'      => 'Archived',
				],
				'default_value' => 'new',
				'allow_null'    => 0,
				'ui'            => 1,
				'wrapper'       => [ 'width' => '33' ],
			],
			[
				'key'           => 'field_enq_owner',
				'label'         => 'Owner',
				'name'          => 'enq_owner',
				'type'          => 'user',
				'required'      => 0,
				'instructions'  => 'Assign to a team member for follow-up.',
				'role'          => [ 'administrator', 'editor' ],
				'allow_null'    => 1,
				'multiple'      => 0,
				'return_format' => 'array',
				'wrapper'       => [ 'width' => '33' ],
			],
			[
				'key'           => 'field_enq_ac_contact_id',
				'label'         => 'ActiveCampaign Contact ID',
				'name'          => 'enq_ac_contact_id',
				'type'          => 'text',
				'required'      => 0,
				'instructions'  => 'The corresponding contact ID in ActiveCampaign. Populated automatically if the form integration writes it here.',
				'placeholder'   => 'e.g. 10842',
				'wrapper'       => [ 'width' => '33' ],
			],
			[
				'key'           => 'field_enq_notes',
				'label'         => 'Internal Notes',
				'name'          => 'enq_notes',
				'type'          => 'wysiwyg',
				'required'      => 0,
				'instructions'  => 'Internal notes, follow-up log, call summaries. Not visible to the enquirer.',
				'tabs'          => 'all',
				'toolbar'       => 'basic',
				'media_upload'  => 0,
				'delay'         => 0,
			],
		],
		'location'              => [
			[ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'enquiry' ] ],
		],
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'seamless',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => [
			'the_content',
			'excerpt',
			'discussion',
			'comments',
			'slug',
			'author',
			'format',
			'page_attributes',
			'featured_image',
			'tags',
			'send-trackbacks',
		],
		'active'                => true,
	] );
}


// ═════════════════════════════════════════════════════════════════════════════
// 7. HOMEPAGE
// Front page (static front page set in Settings → Reading)
// Template: front-page.php
// ═════════════════════════════════════════════════════════════════════════════

function summit_acf_homepage(): void {
	acf_add_local_field_group( [
		'key'                   => 'group_summit_homepage',
		'title'                 => 'Homepage Fields',
		'fields'                => [

			// ── Hero ──────────────────────────────────────────────────────

			[
				'key'   => 'field_hp_tab_hero',
				'label' => 'Hero',
				'type'  => 'tab',
				'placement' => 'top',
			],
			[
				'key'           => 'field_hp_hero_headline',
				'label'         => 'Hero Headline',
				'name'          => 'hp_hero_headline',
				'type'          => 'text',
				'required'      => 1,
				'instructions'  => 'Primary homepage headline. Keep under 60 characters.',
				'maxlength'     => 80,
				'placeholder'   => 'e.g. Design Tomorrow',
			],
			[
				'key'           => 'field_hp_hero_subline',
				'label'         => 'Hero Subline',
				'name'          => 'hp_hero_subline',
				'type'          => 'textarea',
				'required'      => 0,
				'instructions'  => 'Supporting sentence beneath the headline. 1–2 sentences.',
				'rows'          => 2,
				'new_lines'     => '',
			],
			[
				'key'           => 'field_hp_hero_cta_label',
				'label'         => 'Hero CTA Label',
				'name'          => 'hp_hero_cta_label',
				'type'          => 'text',
				'required'      => 0,
				'default_value' => 'Design Tomorrow',
			],
			[
				'key'           => 'field_hp_hero_cta_url',
				'label'         => 'Hero CTA URL',
				'name'          => 'hp_hero_cta_url',
				'type'          => 'url',
				'required'      => 0,
				'default_value' => '/design-tomorrow',
			],
			[
				'key'           => 'field_hp_hero_secondary_cta_label',
				'label'         => 'Hero Secondary CTA Label',
				'name'          => 'hp_hero_secondary_cta_label',
				'type'          => 'text',
				'required'      => 0,
				'default_value' => 'Explore Our Work',
				'instructions'  => 'V4: secondary action beneath the hero headline.',
			],
			[
				'key'           => 'field_hp_hero_secondary_cta_url',
				'label'         => 'Hero Secondary CTA URL',
				'name'          => 'hp_hero_secondary_cta_url',
				'type'          => 'url',
				'required'      => 0,
				'default_value' => '/work',
			],

			// ── Showreel ──────────────────────────────────────────────────

			[
				'key'   => 'field_hp_tab_showreel',
				'label' => 'Showreel',
				'type'  => 'tab',
				'placement' => 'top',
			],
			[
				'key'           => 'field_hp_showreel_headline',
				'label'         => 'Showreel Headline',
				'name'          => 'hp_showreel_headline',
				'type'          => 'text',
				'required'      => 0,
				'instructions'  => 'V4: "A Studio Shaping the Future of Luxury"',
			],
			[
				'key'           => 'field_hp_showreel_embed',
				'label'         => 'Showreel Embed',
				'name'          => 'hp_showreel_embed',
				'type'          => 'textarea',
				'required'      => 0,
				'instructions'  => 'JW Player embed code or iframe. Leave blank to hide the showreel section.',
				'rows'          => 3,
				'new_lines'     => '',
			],
			[
				'key'   => 'field_hp_tab_value',
				'label' => 'Value Proposition',
				'type'  => 'tab',
				'placement' => 'top',
			],
			[
				'key'           => 'field_hp_value_headline',
				'label'         => 'Value Headline',
				'name'          => 'hp_value_headline',
				'type'          => 'text',
				'required'      => 0,
				'instructions'  => 'Short strategic headline for the value proposition section.',
			],
			[
				'key'           => 'field_hp_value_body',
				'label'         => 'Value Body',
				'name'          => 'hp_value_body',
				'type'          => 'textarea',
				'required'      => 0,
				'instructions'  => '2–3 sentences. What Summit does and why it matters.',
				'rows'          => 3,
				'new_lines'     => '',
			],
			[
				'key'   => 'field_hp_tab_services',
				'label' => 'Services',
				'type'  => 'tab',
				'placement' => 'top',
			],
			[
				'key'           => 'field_hp_services_headline',
				'label'         => 'Services Headline',
				'name'          => 'hp_services_headline',
				'type'          => 'text',
				'required'      => 0,
				'default_value' => 'What We Do',
			],
			[
				'key'           => 'field_hp_services_intro',
				'label'         => 'Services Intro',
				'name'          => 'hp_services_intro',
				'type'          => 'text',
				'required'      => 0,
				'instructions'  => 'One-line intro above the services triptych.',
			],

			// ── Curated Content ───────────────────────────────────────────

			[
				'key'   => 'field_hp_tab_curated',
				'label' => 'Curated Content',
				'type'  => 'tab',
				'placement' => 'top',
			],
			[
				'key'          => 'field_hp_curated_message',
				'label'        => '',
				'type'         => 'message',
				'message'      => 'Select specific content to feature on the homepage. If left empty, the homepage will fall back to the most recent published entries.',
			],
			[
				'key'           => 'field_hp_featured_cases',
				'label'         => 'Selected Work',
				'name'          => 'hp_featured_cases',
				'type'          => 'relationship',
				'required'      => 0,
				'instructions'  => 'Choose up to 4 case studies. Fallback: 4 most recent.',
				'post_type'     => [ 'case_study' ],
				'filters'       => [ 'search' ],
				'return_format' => 'object',
				'min'           => 0,
				'max'           => 4,
			],
			[
				'key'           => 'field_hp_featured_episode',
				'label'         => 'Featured Episode',
				'name'          => 'hp_featured_episode',
				'type'          => 'post_object',
				'required'      => 0,
				'instructions'  => 'Pin one episode. Fallback: latest from featured season.',
				'post_type'     => [ 'podcast_episode' ],
				'return_format' => 'object',
				'allow_null'    => 1,
			],
			[
				'key'           => 'field_hp_featured_articles',
				'label'         => 'Featured Articles',
				'name'          => 'hp_featured_articles',
				'type'          => 'relationship',
				'required'      => 0,
				'instructions'  => 'Choose up to 3 articles. Fallback: 3 most recent.',
				'post_type'     => [ 'article' ],
				'filters'       => [ 'search' ],
				'return_format' => 'object',
				'min'           => 0,
				'max'           => 3,
			],

			// ── Download ──────────────────────────────────────────────────

			[
				'key'   => 'field_hp_tab_download',
				'label' => 'Download',
				'type'  => 'tab',
				'placement' => 'top',
			],
			[
				'key'           => 'field_hp_download_headline',
				'label'         => 'Download Section Headline',
				'name'          => 'hp_download_headline',
				'type'          => 'text',
				'required'      => 0,
				'instructions'  => 'e.g. "The Characteristics of Luxury"',
			],
			[
				'key'           => 'field_hp_download_body',
				'label'         => 'Download Section Body',
				'name'          => 'hp_download_body',
				'type'          => 'textarea',
				'required'      => 0,
				'rows'          => 2,
				'new_lines'     => '',
			],
			[
				'key'           => 'field_hp_download_asset',
				'label'         => 'Download Asset',
				'name'          => 'hp_download_asset',
				'type'          => 'post_object',
				'required'      => 0,
				'instructions'  => 'Link to a download entry. The CTA label comes from the download\'s own field.',
				'post_type'     => [ 'download' ],
				'return_format' => 'object',
				'allow_null'    => 1,
			],
			[
				'key'   => 'field_hp_tab_sectors',
				'label' => 'Sectors',
				'type'  => 'tab',
				'placement' => 'top',
			],
			[
				'key'           => 'field_hp_sectors_headline',
				'label'         => 'Sectors Headline',
				'name'          => 'hp_sectors_headline',
				'type'          => 'text',
				'required'      => 0,
				'default_value' => 'Luxury Sectors',
				'instructions'  => 'Headline for the sector grid. The sectors themselves are structural (hardcoded in the template).',
			],
			[
				'key'   => 'field_hp_tab_cta',
				'label' => 'Closing CTA',
				'type'  => 'tab',
				'placement' => 'top',
			],
			[
				'key'           => 'field_hp_cta_headline',
				'label'         => 'CTA Headline',
				'name'          => 'hp_cta_headline',
				'type'          => 'text',
				'required'      => 0,
				'default_value' => "Let's Design Tomorrow",
			],
			[
				'key'           => 'field_hp_cta_body',
				'label'         => 'CTA Body',
				'name'          => 'hp_cta_body',
				'type'          => 'textarea',
				'required'      => 0,
				'rows'          => 2,
				'new_lines'     => '',
			],
			[
				'key'           => 'field_hp_cta_label',
				'label'         => 'CTA Button Label',
				'name'          => 'hp_cta_label',
				'type'          => 'text',
				'required'      => 0,
				'default_value' => 'Start a Conversation',
			],
			[
				'key'           => 'field_hp_cta_url',
				'label'         => 'CTA Button URL',
				'name'          => 'hp_cta_url',
				'type'          => 'url',
				'required'      => 0,
				'default_value' => '/design-tomorrow',
			],
		],
		'location'              => [
			[
				[
					'param'    => 'page_type',
					'operator' => '==',
					'value'    => 'front_page',
				],
			],
		],
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
	] );
}
