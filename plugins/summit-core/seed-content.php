<?php
/**
 * Batch 8 Content Seeder — Summit Communication Group
 *
 * Launch content hardening: replaces Batch 7 placeholder content with
 * real articles, V4-approved homepage copy, and editorial podcast metadata.
 *
 * Adapted for the Local/GitHub repo architecture:
 *   - Homepage fields use home_* prefix (matching block partials)
 *   - CPTs are registered via ACF UI (no post-types.php)
 *   - Taxonomy slugs: /topic, /theme (repo-specific)
 *
 * Source hierarchy:
 *   1. Local/GitHub repo = implementation truth for templates, routes, CPTs, field names
 *   2. Website_Summit Communication Group-V4.docx = primary truth for page copy
 *   3. LinkedIn native articles = primary truth for Future of Luxury article content
 *   4. Season One Guest Map / Summary = editorial truth for podcast metadata
 *   5. md governance docs = structural truth for content model, schema, QA
 *
 * Run via WP-CLI:   wp eval-file plugins/summit-core/seed-content.php
 *
 * Dev-only. Local-only. Do not deploy to production.
 * Idempotent — checks for existing content before creating.
 * Updates fields on existing posts to support re-runs after content changes.
 *
 * @package SummitCore
 */

defined( 'ABSPATH' ) || exit;

// ─────────────────────────────────────────────────────────────────────────────
// Guard: only run once (uses different key from Dropbox seeder)
// ─────────────────────────────────────────────────────────────────────────────
if ( get_option( 'summit_seed_v8_local_complete' ) ) {
	echo "Batch 8 local seed already run. Delete option 'summit_seed_v8_local_complete' to re-run.\n";
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
echo "Summit Batch 8 — Launch Content Hardening\n";
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
// 4. ARTICLES — Real content from LinkedIn native articles (Future of Luxury series)
// Source: 6 LinkedIn articles by Gregory Gray, March 2026
// ═════════════════════════════════════════════════════════════════════════════
echo "\n-- Articles (6 real LinkedIn articles) --\n";

$articles = [
    [
        'slug'     => 'characteristics-of-luxury-a-visual-conversation',
        'title'    => 'The Characteristics of Luxury: A Visual Conversation',
        'category' => 'luxury-strategy',
        'fields'   => [
            'art_standfirst'       => 'Peter Drucker once argued that because the purpose of business is to create a customer, the business enterprise has only two basic functions: marketing and innovation. It is still one of the sharpest observations ever made about commerce.',
            'art_read_time'        => 12,
            'art_cornerstone'      => 1,
            'art_featured'         => 1,
            'art_subscribe_prompt' => 1,
            'art_seo_title'        => 'The Characteristics of Luxury: A Visual Conversation | Future of Luxury',
            'art_meta_desc'        => 'A visual exploration of the defining characteristics of luxury, from craftsmanship and heritage to scarcity and desire. What makes luxury commercially alive.',
        ],
        'content'  => '<p><strong>Peter Drucker once argued that because the purpose of business is to create a customer, the business enterprise has only two basic functions: marketing and innovation. It is still one of the sharpest observations ever made about commerce. At </strong>Summit Communication Group<strong>, we fully own the marketing half, not in the narrow sense of promotion, but in the broader luxury sense of meaning, perception, experience, distinction and desire. </strong></p>
<p>That position matters more now than it did in easier years. Luxury is entering a more demanding phase. Bain sees a market stabilising rather than surging. McKinsey sees clients becoming more selective, less forgiving, more alert to whether price still feels justified. The old habit of mistaking luxury for expensive product with superior photography is beginning to look rather flimsy. The task now is not merely to make brands visible. It is to make value legible.</p>
<p>This is an adventure through the world of superior quality — and the forces re-organising modern luxury. These images began as a visual conversation about what luxury feels like. Read another way, they also describe the business model of luxury itself: a system built on care, distance, symbolism, craft, time, culture and controlled access.</p>
<p>Each characteristic below is not just poetic value. It is commercially alive.</p>
<blockquote><p><strong>What do we mean by <em>Luxury</em>? </strong>Luxury is that softly blinking light on an Apple monitor that tells you this thing is breathing. Luxury is the proof of effort and care beyond what is necessary.</p></blockquote>
<h2>Luxury</h2>
<p>Luxury begins where necessity ends, but that does not mean it is frivolous. Quite the reverse. Luxury is the visible proof of care beyond what is strictly required. It is the extra calibration, the softer finish, the calmer interface, the detail that serves no urgent practical function yet changes the emotional temperature of the entire encounter. That is why luxury is never merely a high price attached to a useful object. It is usefulness refined by intention, then elevated by restraint.</p>
<p>Commercially, this makes luxury strangely non-comparative. The best houses do not win by being marginally better than competitors on a spreadsheet. They win by constructing a world that feels self-evident, self-contained, almost immune to ordinary comparison. That is what clients are paying for: not just superiority, but removal from the usual marketplace logic. This is often the first strategic problem we solve. We help brands move away from explaining themselves like premium commodities, then towards presenting themselves as singular propositions with a distinct emotional signature.</p>
<blockquote><p><strong>What do <em>Dreams</em> look like in leather and light? </strong>Dreams can be a private night sky stitched into the roof, each fibre-optic star placed by hand so your cabin feels like a universe to inspire greatness from within.</p></blockquote>
<h2>Dreams</h2>
<p>Dreams are the first product luxury sells. The object comes second. A Rolls-Royce starlight headliner, a private salon in a couture house, a watch complication no one strictly needs — these are not rational purchases in the everyday sense. They are invitations into a more composed version of life, where taste is heightened, friction is reduced, then the imagination is given better surroundings in which to operate.</p>
<p>This is why luxury communication works best when it is suggestive rather than exhaustive. The sector has always understood that desire is rarely built by saying too much. It is built through implication, atmosphere, symbolism, then a degree of withholding. Dreams need space around them. From a business perspective, that subtlety is not decorative. It broadens aspiration without flattening exclusivity. At Summit Communication Group, we treat this as a core design principle. The aim is not simply to promote a brand’s offer, but to articulate the dream-world that makes the offer feel inevitable.</p>
<blockquote><p><strong>How is Quality identified in a woman’s jacket?</strong> Quality is the chain stitched into the hem, doing the quiet work of gravity so the silhouette falls properly. Effort concealed, drape controlled, proof you can’t quite point to until you miss it.</p></blockquote>
<h2>Quality</h2>
<p>Quality is not only material excellence. It is the quiet conviction that every element has been thought through sufficiently, then executed without cynicism. It can sit in a jacket’s drape, a clasp’s tension, a site’s loading speed, a concierge’s tone of voice, or the simple absence of things that feel rushed.</p>
<p>The most sophisticated quality signals are often the least theatrical. A garment that hangs correctly because its internal architecture has been respected. A leather edge finished so cleanly it is barely noticed. A retail environment where service feels attentive without becoming invasive. Even the slight irregularity of the human hand can matter here. Luxury quality is not machine sterility. It is controlled excellence with life still in it. This characteristic shapes how we design every touchpoint. Quality should not arrive as a claim in a headline. It should be detectable in the standard of the experience itself.</p>
<blockquote><p><strong>What is Perfection when it refuses to show off?</strong> Perfection is a distortion-free Zaratsu polish, a mirror finish so clean it turns light into a discipline.</p></blockquote>
<h2>Perfection</h2>
<p>Perfection in luxury is not the vulgar pursuit of flawlessness for its own sake. It is the management of detail to such a degree that disorder has been designed out of the experience. Grand Seiko’s distortion-free Zaratsu polish is a useful metaphor. The point is not ornament. The point is disciplined clarity. A surface becomes so exact it stops performing. It simply <em>is</em>.</p>
<p>Yet luxury also knows something modern corporate culture often forgets: perfection does not mean the elimination of humanity. The best luxury objects still carry a trace of the hand, the atelier, the maker, the decision. Their perfection lies in balance, not sterility. Commercially, this matters because perfection is one of the ways a house justifies silence. It does not need to shout when the evidence is embedded in the object. From a brand’s story, from a digital journey, perfection from the way value is perceived. Clarity, at luxury level, is its own form of polish.</p>
<blockquote><p><strong>What is History when it becomes a wearable?</strong> History was born as a family tribute, travelled around the world, now recognised as a kind of coded inheritance you can carry and pass across generations.</p></blockquote>
<h2>History</h2>
<p>A brand is not important merely because it is old. It becomes important when its history continues to produce present value. Louis Vuitton’s trunk-making past still matters because it speaks to movement, craft, endurance, then a family idea turned global code. Cartier’s heritage matters because it is not a museum label pinned to a modern business. It is still a source of trust, symbolism, then social meaning.</p>
<p>The strongest luxury brands understand that heritage is active, not archival. It is a commercial asset only when it still informs product, service, ritual, tone, provenance or place. This is where origin matters too. Swiss, Italian, French, Japanese — geography in luxury is rarely incidental. It behaves like proof. At Summit, we often work with brands that either have deep heritage they are underselling or newer stories they are telling too flatly. Our role is to convert history into present-tense meaning, so it feels like an inheritance with momentum rather than sepia wallpaper.</p>
<blockquote><p><strong>What is Culture when it behaves like a password?</strong> Culture is <em>intrecciato</em> — a weave so recognisable it replaces the need for any logo. Luxury is craft turned into a signature, then left to speak for itself.</p></blockquote>
<h2>Culture</h2>
<p>Culture is what happens when a brand stops being a label, then starts behaving like a language. Bottega Veneta’s intrecciato weave is powerful because it functions almost as a password. Those who know it recognise it instantly. No logo needs to perform the introduction. That is culture at work: literacy replacing display, code replacing explanation.</p>
<p>From a business standpoint, this is priceless. Cultural fluency creates distinction that cannot easily be copied by simply mimicking surface aesthetics. It also allows a brand to move beyond product into identity. Clients do not only buy the object. They borrow a signal, a value system, a professional and social texture that becomes part of how they present themselves to the world. This characteristic often sits at the heart of our brand strategy. We help clients identify the internal codes, references and signatures that make them culturally resonant, then design systems that let those codes travel with elegance rather than overstatement.</p>
<blockquote><p><strong>What is Art engineered to appear effortless?</strong> Art can be where stones appear to float with the metal made almost invisible, as if beauty simply decided to hold itself together.</p></blockquote>
<h2>Art</h2>
<p>Luxury’s relationship with art is not an affectation. It is structural. Art gives luxury something every premium business longs for: cultural capital. When Van Cleef &amp; Arpels makes stones appear to float through its Mystery Set technique, or when Louis Vuitton collaborates with artists in ways that alter the object’s symbolic life, the point is elevation. The object becomes part of a wider cultural conversation.</p>
<p>That shift matters commercially because it moves a brand from seller to curator, from producer to participant in taste itself. Art helps luxury houses appear less transactional, more civilisational. It gives them altitude. It also teaches a valuable operational lesson: the most sophisticated work often looks easy because the engineering has been hidden. We think the best strategy, the best design, the best digital experiences needn\'t feel laboured. They feel composed. Complexity has been handled offstage, so the audience encounters only the result.</p>
<blockquote><p><strong>What would Rarity feel like on your skin? </strong>Rarity can be combed just once in a lifetime from a young goat’s underfleece, a fibre so fine it becomes air you can wear.</p></blockquote>
<h2>Rarity</h2>
<p>Rarity is one of the few characteristics luxury cannot fake indefinitely. It either exists in the material, the time required, the access constraints, the geography, the skill base or the production discipline, or it does not. Loro Piana’s baby cashmere is persuasive because the scarcity is real. Ferrari’s production discipline matters because the scarcity is imposed intentionally. In both cases, rarity becomes a business tool, not a poetic adjective.</p>
<p>This is where luxury separates itself from ordinary premium marketing. Scarcity is not merely low supply. It is low supply managed intelligently enough to protect desirability rather than simply frustrate demand. It must feel justified, not arbitrary. It must create narrative, not just inconvenience.</p>
<p>We help brands identify what is truly scarce about them — materials, expertise, access, location, point of view, relationships, time — then express that rarity in ways that strengthen value. Done well, rarity makes the entire brand seem more exacting.</p>
<blockquote><p><strong>What is Exclusivity when it’s made physical?</strong> Exclusivity is invitation-only, minted from titanium, a piece of access you don’t apply for so much as get quietly admitted to.</p></blockquote>
<h2>Exclusivity</h2>
<p>Exclusivity is not snobbery with better manners. It is architecture. It determines who enters, how they enter, what they are shown, what remains withheld, then how the experience confirms that access itself has value. Hermès understands this instinctively. The Birkin is elusive. The difficulty of acquisition is part of the product. The obstacle course to ownership is not accidental. It is one of the mechanisms by which desire is maintained.</p>
<p>The best luxury brands behave like private clubs, even when they operate at global scale. Not everyone is addressed in the same tone. Not every invitation is open. Not every customer journey should feel frictionless. Some degree of selective admission preserves the charge. At Summit Communication Group, exclusivity informs everything from audience strategy to event design to digital sequencing. We help clients decide not only what should be seen, but by whom, when, then under what conditions. Luxury loses aura when it becomes indiscriminate. Our role is to restore the velvet rope (without making a scene).</p>
<blockquote><p><strong>What is Money when it becomes the narrative?</strong> Money is a six-digit identification, a code on the back label that unlocks the story of that specific bottle, as though provenance is part of the pour.</p></blockquote>
<h2>Money</h2>
<p>Money, in luxury, is never just money. It is symbolic authority. The price is not merely what the item costs to make, distribute or market. It is a statement about the brand’s confidence in its own value, then an invitation for the client to agree. That is why luxury pricing becomes fragile the moment narrative weakens. If provenance, craft, scarcity, service and cultural meaning are not felt properly, price begins to look theatrical in the wrong way.</p>
<p>This is also why luxury houses do not really “compete” on price in the usual sense. They set it as an extension of the world they have built. Fine wine, high jewellery, rare watches, couture, invitation-only experiences — in each case, money is inseparable from story. The client is not only buying the object. They are buying the legitimacy of its premium. We treat price as a communications problem as much as a commercial one. We help brands make value intelligible enough that the premium feels self-evident.</p>
<blockquote><p><strong>How does Craftsmanship become a moat?</strong> Craftsmanship is two needles, waxed thread, opposing passes, a seam that holds even if one stitch fails, because a skilled human hand designed it to endure.</p></blockquote>
<h2>Craftsmanship</h2>
<p>Craftsmanship is where luxury becomes defensible. Taste can be imitated. Advertising can be copied. Even aesthetic codes can be borrowed. Genuine craft is harder to counterfeit because it is embedded in training, repetition, physical intelligence, patience, then the accumulated judgment of hands that know when something is right.</p>
<p>In a market increasingly crowded by expensive things, craftsmanship remains one of the surest ways to separate luxury from simple premium. It turns labour into trust. It gives price a human explanation. It allows the customer to sense that time, skill, then attention have been invested on their behalf. This matters greatly because communication must never flatten labour into lifestyle gloss. Our role is to reveal just enough of the making, the method and the human hand that the premium feels honourable rather than inflated.</p>
<blockquote><p><strong>How does Time sound as a private ceremony?</strong> Time is a slide on the case, tiny hammers striking gongs, the hour turning into music for the person close enough to hear it.</p></blockquote>
<h2>Time</h2>
<p>Time is one of luxury’s purest materials because it is the one thing no client, however wealthy, can produce more of. Patek Philippe understood this brilliantly. A minute repeater does not merely measure time. It turns time into ceremony. Waiting, inheritance, longevity, then slowness itself become part of the value proposition. The object matters. The temporal worldview around it matters more.</p>
<p>This has direct commercial implications. Luxury thrives when it resists the panic of immediacy. Limited editions, waiting lists, made-to-order pieces, private appointments, long-lived objects passed across generations — all of these convert time from inconvenience into prestige. At Summit, we think about time not only in product storytelling, but in the rhythm of the brand experience. The best luxury journeys are rarely rushed.</p>
<blockquote><p><strong>What is luxury driven through the heart of Society?</strong> Society is <em>Cavalcade</em>, a procession of owners and cars moving as one, part road trip, part rolling museum. Ownership becomes membership, then the world gets to watch from the pavement.</p></blockquote>
<h2>Society</h2>
<p>Luxury has always been social, but the more interesting shift is that it is becoming social in a deliberately orchestrated way. Ferrari’s Cavalcade, private client dinners, collector weekends, invitation-only cultural programmes, members’ clubs, patron circles — the object increasingly serves as the price of admission into a community. Ownership becomes membership. Purchase becomes belonging.</p>
<p>This matters because society is one of the ways luxury protects itself from pure transaction. A client who feels part of a world behaves differently from a customer who has merely completed a purchase. Loyalty deepens. Advocacy becomes more natural. The brand gains an ecosystem rather than a sales base. This is central to how we think about experience design. We help brands build environments in which clients encounter one another, not just the product — whether through events, private content, digital ecosystems or invitation structures that turn a brand into a social identity.</p>
<blockquote><p><strong>What is Charity when it’s built into an object?</strong> Charity is a rally to Save the Children, sterling silver and black ceramic, engraved by its cause, with a defined donation per sale that turns purchase into participation.</p></blockquote>
<h2>Charity</h2>
<p>Charity in luxury works only when it feels native to the object and the house. Bvlgari’s Save the Children ring is persuasive because the cause is designed into the product itself. The contribution is clear. The symbolism is direct. Purchase becomes participation, not merely consumption wrapped in a worthy press release.</p>
<p>That distinction matters more than many brands realise. Philanthropy can enhance reputation, but only when it is precise, measurable, then coherent with the brand’s worldview. Otherwise, it quickly begins to smell of conscience laundering. Where trust and authenticity are everything, that is fatal. We approach purpose with a similar discipline. We help clients express social commitments in ways that are integrated, legible and structurally believable — as part of how the brand behaves in the market.</p>
<p><strong>Luxury is often mistaken for ornament, indulgence or price. In practice, it is a far more exacting commercial system than that. It is built from qualities that are emotional in appearance, yet brutally practical in effect: rarity protects margin, craftsmanship protects trust, culture protects distinctiveness, time protects desire, exclusivity protects value. That is why these characteristics matter. They are not the soft side of luxury. They are the operating system.</strong></p>
<p><strong>At </strong>Summit Communication Group<strong>, we operate as a design consultancy specialised in luxury brand strategy, experience design and digital transformation. Our role is to make those qualities legible in the modern market — through brand strategy, experience design and digital transformation that honour what makes a luxury brand valuable in the first place. Not louder. Not busier. Simply more exact.</strong></p>',
    ],

    [
        'slug'     => 'from-hill-of-grace-to-argyle-pink-diamonds-why-australias-luxury-houses-need-a-harder-shell',
        'title'    => 'From Hill of Grace to Argyle pink diamonds: Why Australia’s luxury houses need a harder shell.',
        'category' => 'wealth-value-collecting',
        'fields'   => [
            'art_standfirst'       => 'Can a country possess all the raw ingredients of luxury yet still stop short of building true houses? Why do so many Australian brands have provenance, family depth, rarity, and craftsmanship in abundance, yet remain structurally softer than their European counterparts?',
            'art_read_time'        => 10,
            'art_cornerstone'      => 0,
            'art_featured'         => 0,
            'art_subscribe_prompt' => 1,
            'art_seo_title'        => 'From Hill of Grace to Argyle pink diamonds: Why Australia’s luxury houses need a harder shell. | Future of Luxury',
            'art_meta_desc'        => 'Australia has the raw ingredients of luxury but lacks structural armour. Why the next generation of Australian houses must build harder, more defensible brands.',
        ],
        'content'  => '<p><strong>Can a country possess all the raw ingredients of luxury yet still stop short of building true houses? Why do so many Australian brands have provenance, family depth, rarity, and craftsmanship in abundance, yet remain structurally softer than their European counterparts? And in a market that now rewards ownership, archive intelligence, and institutional discipline, how long can taste alone carry the burden?</strong></p>
<p><strong>In the latest edition of </strong><strong><em>The Future Of Luxury</em></strong><strong> we explore why some of Australia’s most valuable luxury assets are still being narrated like founder stories when they ought to be governed like heirlooms.</strong></p>
<h2>Australia has the substance, just not always the structure</h2>
<p>Australia is not, on paper, a country one instinctively associates with luxury institution-building. It is too direct, too sun-struck, too suspicious of ornament, too inclined to regard self-mythology as something best left to the French or Italians. Yet, this is precisely what makes the thing so interesting. Because when one looks closely, Australia is crawling with luxury-grade material. Not the flimsy kind that can be assembled by a consultant with a beige deck and a strategy workshop in Surry Hills. The real kind. Ancient vines. Finite stones. Remote provenance. Family continuity. A working memory of craft. Objects whose value rests not on noise, but on being terribly difficult to fake convincingly.</p>
<p>And yet, for all that depth, too many Australian brands still behave like admirable premium businesses rather than future houses. They have story without enough structure, scarcity without enough system, legacy without enough legal or experiential architecture around it. The result is a curious imbalance. Australia has luxury substance. What it often lacks is a harder shell.</p>
<blockquote><p>“Australia does not suffer from a shortage of provenance. It suffers from a shortage of institutional discipline around provenance.”</p></blockquote>
<p>That matters more now because the broader luxury market has become less indulgent. McKinsey &amp; Company noted in early 2025 that price increases had accounted for more than 80 per cent of luxury growth in recent years, while volume gains were more modest and value creation was expected to soften. Bain &amp; Company, in its <strong>2025 luxury study</strong>, said the market had entered a period of transition in which reinvention would be necessary if brands were to secure future growth. In plainer English, the old party is over. The music has not stopped entirely, but the room has cooled. In such an environment, brands with genuine substance should have an advantage. The trouble begins when they fail to organise that substance into something commercially and culturally defensible.</p>
<h2>Hill of Grace is not just a wine, it is an argument</h2>
<p>Take HENSCHKE. If Europe had produced Hill of Grace, there would already be three documentaries, a coffee-table book the size of a paving slab, a private members’ tasting society, a waiting list wrapped in theology and probably a tasteful legal memorandum explaining why the phrase itself was part of the patrimony of civilisation. Australia, being Australia, tends to put the bottle on the table and trust that the liquid will do the talking. Often it does. But it need not do all the work alone.</p>
<p>HENSCHKE has more than 150 years of family winemaking history. Hill of Grace itself is rooted in an eight-hectare single-vineyard shiraz site at Eden Valley, with vines first planted in the 1860s and the name deriving from the nearby Gnadenberg Lutheran church. The estate speaks openly of six generations of family stewardship, while the sixth generation, Johann and Justine Henschke, are now visibly entering the story. In 2024, Prue Henschke was inducted into the James Halliday Hall of Fame. In 2025, Henschke won the Golden Vines award for Best Fine Wine Producer in the Rest of the World for a second consecutive year.</p>
<p>That is not merely a product story. It is an inheritance system. It is land, language, ritual, naming, custodianship, succession, and memory, all packed into a bottle and sold by the case. And yet one still senses that too many Australian luxury businesses of this kind are communicating as if heritage were self-evident. Heritage is never self-evident. It must be structured, translated, protected, staged, and renewed.</p>
<p>There is already evidence of what lies dormant inside the HENSCHKE world. The Hill of Grace experience offers access to the ancient vines, the original winery from the 1860s, and rare tastings of the estate’s most storied wines. In other words, the raw material for a world-class collector ecosystem already exists. What is needed is the next layer: a more deliberate architecture around the narrative, the archive, the collector journey, the naming system, and the intergenerational handover. Otherwise, the family risks remaining one of Australia’s great winemaking dynasties while still under-exploiting the full strategic value of being exactly that.</p>
<blockquote><p>“The difference between a revered producer and a true house is not quality alone. It is whether the world around the product has been built with equal seriousness.” <strong>Gregory Gray</strong></p></blockquote>
<h2>The trouble with finite rarity is that it needs a custodian</h2>
<p>Then there is Calleija Jewellers, which may be the cleaner expression of the problem because the scarcity is so obvious that it practically taps you on the shoulder. The Argyle mine closed in 2020. That single fact should have changed the communications architecture of every serious Australian jeweller with credible access to those stones. Some, inevitably, treated it as a nice sales point. Calleija treated it as something closer to stewardship.</p>
<p>Rio Tinto launched its Icon Partner programme in 2022 and appointed John Calleija as one of its first two Icon Partners for the Argyle Pink Diamonds brand, licensing the partners to work with the remaining polished inventory after the mine’s closure. Calleija said he was “enormously proud” to work with Rio Tinto as an Icon Partner. On the company’s own site, Calleija says it is an honour to trace each gemstone “back to the mine of its origin”. This is not a decorative flourish. It is one of the most precise provenance claims available anywhere in luxury jewellery. A stone from a closed mine is not simply rare. It is finite in a way that modern luxury usually only pretends to be.</p>
<p>And that is why it needs a harder shell. Finite rarity requires more than beautiful display cases and a nice line about romance. It requires a disciplined narrative. It requires protection of naming, provenance language, certification logic, design signatures and the customer experience through which all that value is explained and emotionally converted. Otherwise, even a business with access to one of the most extraordinary raw materials in the luxury world risks under-speaking its own significance.</p>
<p>This is where Australian luxury often reveals its curious modesty. European houses would have turned the closure of Argyle into a chaptered mythology with exhibitions, archive drops, editorial series, collector books, documentary fragments and private events staged just shy of religious ceremony. Australia, by contrast, is often still halfway between understatement and under-exploitation. There is honour in understatement. There is less honour in leaving margin on the table because one feels awkward about grandeur.</p>
<h2>Pearls, meanwhile, are hiding in plain sight</h2>
<p>Paspaley presents the same dilemma from a different coast. The company says it has led Australian South Sea pearling for more than 90 years and remains one of the last houses whose divers hand-collect wild pearl oysters from the seabed. Its own history frames the business through Darwin, the Kimberley, natural pearls, and a family story reaching back across generations. On paper, this is catnip: migration, sea, danger, rarity, wilderness, hand-collection, origin, continuity. If a luxury strategist could order raw materials from central casting, he would probably ask for something less perfect, simply to avoid appearing greedy.</p>
<p>Yet the same question persists. Is this history being fully institutionalised, or merely tastefully referenced? There is a difference. One creates atmosphere. The other creates durable enterprise value.</p>
<h2>Europe’s gold standard is not just heritage, but organised heritage</h2>
<p>This is where the old European houses remain the gold standard, not because they possess deeper stories than Australia, but because they have become ruthlessly competent at organising those stories.</p>
<p>Cartier’s Collection, created in 1983, now comprises more than 3,000 pieces and is used not only for exhibitions but to pass down the Maison’s style and culture to designers, craftsmen, and the public. Hermès’ petit h has turned unused house materials into new objects through a formal in-house practice of recreation rather than mere waste reduction. Louis Vuitton continues to use Asnières, the historic family site outside Paris, as both working atelier and symbolic heart of the house, while its Monogram anniversary collections explicitly reinterpret archival codes into new commercial chapters. None of this is nostalgic window-dressing. It is governance disguised as elegance.</p>
<p>That is how grown-up houses behave. They do not merely point at the archive and sigh. They operationalise it. The past becomes an active commercial system: for products, restoration, accessories, merchandising, exhibitions, customer experience, and internal culture. Australia’s best luxury businesses need more of this seriousness. Not pastiche, not gimmickry, not some ghastly “heritage capsule” dreamed up by a brand manager who has mistaken nostalgia for intelligence. Proper archive logic.</p>
<p>If Hill of Grace is a language, what are its chapters? If Argyle provenance is finite, what is the institutional grammar around it? If Paspaley’s world is built on oysters, divers, migration, and the Kimberley, where is the system that turns those facts into owned advantage rather than anecdote? “The archive is not there to flatter the founder. It is there to increase the precision of the brand.”</p>
<h2>Family businesses are entering the dangerous middle</h2>
<p>This is not just a branding issue. It is a governance issue, and Australia is full of family businesses now standing in that awkward, consequential middle stretch between founding myth and institutional maturity. <strong>Grant Thornton’s 2025 Family Business Report</strong> found that only 19 per cent of Australian family businesses had a documented succession plan in place. At the same time, many were already mentoring the rising generation and integrating them into decision-making. In other words, many families understand that a baton is being passed, but not all have decided exactly how, on what terms, or in what language.</p>
<p>That statistic should concentrate the mind rather wonderfully. Because luxury does not merely inherit assets. It inherits meaning, reputation, customer expectation, symbolic codes, and internal myths. A family business can survive a murky handover for years if it sells plumbing supplies. It becomes much harder if it sells rarity.</p>
<p>Henschke is interesting precisely because it appears to be handling this with unusual grace. Johann Henschke’s public voice is already part of the narrative. The family is not pretending time has stopped. It is acknowledging continuity in motion. But that makes the next task more pressing, not less. The passage from one generation to the next is when luxury businesses are most tempted to rely on sentiment. They ought, instead, to become more architectural.</p>
<h2>Australia’s problem is not authenticity, it is translation</h2>
<p>One reason Australian luxury remains under-institutionalised is that it still tends to believe authenticity will naturally translate. It will not. Authenticity is the raw ingredient. Translation is the discipline that makes it legible to the outside world without vulgarising it.</p>
<p>This is especially urgent as luxury consumers become more demanding about what they are buying. McKinsey argued that clients now have a more complex relationship with luxury than ever, and are increasingly interested not only in goods but in luxury experiences. That is not a minor footnote. It means the object alone no longer does the full persuasive labour.</p>
<p>The surrounding world matters more: service, provenance, access, story, ritual, confidence. Australian brands are unusually well positioned for this because they possess what jaded global consumers increasingly crave: actual origin stories, actual remoteness, actual continuity, actual scarcity. But to convert that advantage, they must stop communicating like plucky founder-led businesses and begin designing like future institutions.</p>
<p>At Summit Communication Group , as a design-led communications agency, we see this repeatedly. Australian brands often do the difficult part first.</p>
<p>They build the product, the family continuity, the craftsmanship, the provenance. Then they hesitate at the threshold where all of that needs to be systematised. They have the wine, the stone, the pearl, the land, the memory. What they have not always built is the harder shell around those things: the audience architecture, the strategic narrative, the collector logic, the archive discipline, and the digital infrastructure that turns inherited depth into long-term leverage. “The Australian luxury business often has the jewel before it has the setting.”</p>
<h2>The archive is about to become active</h2>
<p>And now the plot thickens. Because the harder shell Australian luxury needs will not be built by better adjectives alone. It will be built by systems. The next phase of the industry will belong to brands that do not merely preserve their archives, but make them usable: searchable, trainable, interpretable, ownable. Bain and Comité Colbert found that luxury houses have so far adopted fewer than two AI use cases on average, yet each maison is already testing or planning more than five additional AI-powered use cases across the value chain. In parallel, a 2025 Bain-Comité Colbert study found European luxury groups now spend an average of 3.1% of revenue on technology, while 60% expect their technology budgets to rise by more than 5% over the next two to three years. In other words, the machinery is not hypothetical. It is arriving.</p>
<p>What matters is how it is used. The vulgar version of AI is already familiar: synthetic imagery, generic copy, algorithmic wallpaper, a machine making everything look faintly like everything else. That is not where the real advantage lies. The real advantage lies in systems trained on a house’s own materials: its sketches, correspondences, vineyard records, jewellery certificates, workshop notes, archived campaigns, customer histories, and design codes.</p>
<p>At VivaTech 2025, LVMH showcased 15 live Maison-tech projects, including a Louis Vuitton initiative described as “From Trunks to Pixels”, which uses product digitisation and generative AI to turn physical products into scalable digital assets while ensuring “full control and ownership” of the production process. That phrase — full control and ownership — is the whole point.</p>
<p>For Australian luxury, this is the real opportunity hiding in plain sight. Imagine Hill of Grace not merely as a vineyard, but as an intelligent archive: every vintage note, soil variation, tasting profile, family letter, label evolution, and cellar release becoming part of a living system that sharpens product development, collector storytelling, and customer intimacy. Imagine Calleija’s Argyle provenance not merely as a certificate, but as a digital grammar of rarity that can power private client journeys, editorial worlds and future creative direction without surrendering an inch of authenticity. Imagine Paspaley’s pearling history not simply as romance, but as a machine-readable inheritance system that turns memory into leverage. That is where this is heading. Not toward less humanity, but toward better organised humanity.</p>
<blockquote><p>“The next great luxury houses will not use AI to replace imagination. They will use it to liberate the value already trapped inside their own memory.”</p></blockquote>
<p>Luxury loves to speak of timelessness because it sounds expensive. But timelessness in the decade ahead will be awarded less to the brand with the prettiest story than to the one that has turned its story into an operating system. Australia already has many of the raw materials for that future. What it needs now is the confidence to build for it. <strong>The next Australian house will not merely remember its past. It will know how to query it.</strong></p>',
    ],

    [
        'slug'     => 'gen-z-wont-buy-your-myth-unless-they-can-wear-it-verify-it-resell-it',
        'title'    => 'Gen Z won’t buy your myth unless they can wear it, verify it, resell it.',
        'category' => 'the-new-luxury-customer',
        'fields'   => [
            'art_standfirst'       => 'Is Gen Z rejecting luxury or rejecting the industry’s idea of what luxury should mean? If price has outpaced perceived value, what exactly are brands asking young consumers to believe? When peers carry as much authority as experts, who is really doing the selling now?',
            'art_read_time'        => 7,
            'art_cornerstone'      => 0,
            'art_featured'         => 0,
            'art_subscribe_prompt' => 1,
            'art_seo_title'        => 'Gen Z won’t buy your myth unless they can wear it, verify it, resell it. | Future of Luxury',
            'art_meta_desc'        => 'Gen Z is not rejecting luxury. They are rejecting unearned mystique, hollow pricing and one-way storytelling. What brands must change to earn their trust.',
        ],
        'content'  => '<p><strong>Is Gen Z rejecting luxury or rejecting the industry’s idea of what luxury should mean?</strong> <strong>If price has outpaced perceived value, what exactly are brands asking young consumers to believe?</strong> <strong>When peers carry as much authority as experts, who is really doing the selling now?</strong></p>
<p><strong>In this edition of </strong><strong>The Future Of Luxury</strong><strong> from Summit Communication Group, I want to get to the heart of why Gen Z is simultaneously the industry’s most coveted future customer, its most sceptical critic, plus its most efficient distribution channel—then what brand leaders can do when “trust” has become a public, searchable asset.</strong></p>
<h2>Gen Z is not hard to reach, just hard to impress.</h2>
<p>Luxury keeps talking about Gen Z as if they’re a rare species spotted briefly at a festival, then gone until next season. In practice, Gen Z is everywhere—hyper-reachable, constantly online, chronically over-informed. The problem isn’t reach. It’s credibility.</p>
<p>They shop like journalists. They compare like accountants. They judge like jurors. They don’t just want “a story”. They want the footnotes, the receipts, the provenance, the why-now, the why-you, plus the “will I still like this in five years or will it age like a brand’s NFT phase?”</p>
<p>This is what happens when you grow up in a world where a factory video can outrank a campaign, where a resale price graph can puncture a brand myth in seconds, where your peers’ opinions carry more weight than your brand ambassador’s smile.</p>
<p>Bain &amp; Company has been warning the industry in polite consultant language that the generational handover is real: by 2030, Gen Z is expected to account for 25–30% of luxury purchases, with Millennials still the largest cohort at 50–55%. The point is not the exact split. The point is that the next growth engine does not want to be “targeted”. They want to be convinced—by evidence.</p>
<h2>The quiet crisis behind the loud campaigns is perceived value.</h2>
<p>Luxury has raised prices with an enthusiasm that would be admirable if it weren’t so risky. The industry now finds itself in a strange position: louder than ever, less believed than ever.</p>
<p>Vogue’s consumer survey work captured the mood bluntly: many respondents felt luxury no longer delivers value commensurate with its price, with quality concerns rising alongside the price tags. Under-35s, in particular, are more open to secondhand and alternative channels.</p>
<p>Gen Z isn’t necessarily “anti-luxury”. They’re anti-<em>unearned</em> luxury. They will still pay. They just want to know what they’re paying for, beyond the privilege of funding your next round of billboards.</p>
<h2>Resale is the industry’s lie detector, then Depop just got a new megaphone.</h2>
<p>Executives often talk about resale as a channel. Gen Z treats it as a scoreboard. Resale answers the questions luxury marketing tries to avoid: Which products remain desirable when the campaign is gone? Which pieces keep their cultural meaning? Which items were mostly hype, plus a good photographer?</p>
<p>This is why the Depop story matters, not as business gossip, but as a cultural sign. eBay has agreed to acquire Depop for $1.2bn, explicitly to deepen its reach with younger shoppers. Depop reported $1bn in gross merchandise sales in 2025, with 90% of buyers under 34. If you’re a luxury leader, you can roll your eyes at resale. The market is busy building platforms around it.</p>
<h2>Gen Z does not “join brands” they join moments, codes, plus objects they can defend.</h2>
<p>The brands cutting through with Gen Z are often doing something painfully simple: they make their codes legible.</p>
<p>Not every Gen Z customer wants the “full look”. Often they want a single piece that communicates identity, lets them participate, then doesn’t require them to treat the purchase like a minor mortgage.</p>
<p>This is where the Lyst Index becomes useful—not because it’s gospel, but because it tracks the thing that matters: what people actively search for. In Q3 2025, Saint Laurent took the top spot, with Miu Miu close behind, plus Coach and The Row firmly in the mix. Lyst’s own commentary around the period is revealing: in crowded markets, shoppers reward brands with clear cultural codes plus products that communicate those codes with precision.</p>
<p>This is not “Gen Z being fickle”. This is Gen Z rewarding brands that don’t mumble. And yes, sometimes the most viral “luxury signal” is comically small: a charm, a strap, a detail, a code you can wear without pretending you’re above rent.</p>
<h2>Bag charms look silly until you realise they’re a strategy</h2>
<p>If you want to understand Gen Z, don’t start with a mission statement. Start with a bag charm.</p>
<p>The bag charm craze—this maximalist, sentimental, slightly chaotic customisation wave—has become a genuine mechanism for self-expression. Vogue Business noted the rise of “chaotic customisation” and how Gen Z treats accessories as canvases, with #BagCharms activity building serious momentum.</p>
<p>Coach is one of the more instructive examples because it has combined product, culture, plus entry-point economics. Vogue Business reported Coach’s Gen Z momentum driving Tapestry growth, with cult items like the Tabby and Brooklyn bags recruiting new customers, plus the viral cherry charm performing strongly with Gen Z. CNBC reported similarly that bag charms were trending on TikTok, while Coach has been pulling in millions of new customers, with nearly 70% of new North American customers Gen Z and Millennials.</p>
<p>This isn’t “dumbing down” luxury. It’s letting people enter the world without being lectured.</p>
<blockquote><p>“Gen Z consumers at Coach have the highest retention rates of all cohorts, reinforcing the opportunity to build lifetime value with our target customer base.” <strong>Joanne Crevoiserat, CEO, Tapestry</strong></p></blockquote>
<h2>The dupe economy is not a prank, it’s a referendum on price credibility.</h2>
<p>Luxury still frames dupes as a morality play. Gen Z often frames them as financial literacy, plus a referendum on whether the industry’s price-value equation has become insulting.</p>
<p>Barron\'s reporting on China’s “pingti” phenomenon describes Gen Z embracing high-quality dupes, prioritising value, research, plus supply chain transparency—often with the not-so-subtle claim that some replicas come from the “same factories”.</p>
<p>Worse (or better, depending on your appetite for discomfort), the same reporting highlights Chinese brands such as Florasis, Laifen, plus Laopu Gold gaining momentum by combining performance, cultural relevance, plus price logic.</p>
<p>Luxury’s response cannot be to scold. Scolding only confirms the stereotype: out of touch, morally self-satisfied, faintly offended by economics. The response is to make the original indisputably better—design integrity, quality control, service, plus a brand world so coherent that the “why pay more?” question becomes slightly embarrassing to ask.</p>
<h2>Trust has been reallocated from experts to peers, then brands must learn to live with that.</h2>
<p>Edelman’s Trust Barometer captured a modern nuisance for luxury: people increasingly trust peers as much as traditional authorities.</p>
<blockquote><p>“Seventy-four percent say they trust scientists and peers equally for the truth about innovations.” <strong>Edelman Trust Barometer 2024</strong></p></blockquote>
<p>This is not an invitation to hire more influencers. It’s a warning that trust is now distributed, not bestowed. Gen Z is not waiting for your “big reveal”. They are watching how you behave, then letting the group chat decide what it means.</p>
<p>This is also why “share of voice” spending has become a trap. You can buy attention. You cannot buy belief. “To break through, you need to have a strong share of voice.” Scott Roe, CFO and COO, Tapestry. Gen Z hears that and thinks: So you’re paying to interrupt me. Then they ask: What do you have that’s worth interrupting me for?</p>
<h2>Sustainability is not a halo, it’s a competence test.</h2>
<p>Gen Z does not demand perfection. They demand coherence. If you claim heritage, yet can’t explain your materials, you fail. If you claim craft, yet your labour story collapses under scrutiny, you fail. If you claim timelessness, yet quality debates follow your price hikes like a shadow, you fail.</p>
<p>Claire Bergkamp, Textile Exchange’s CEO, put the next phase in plain terms: circularity cannot be outsourced as a moral accessory. “We cannot build a circular textile system on another industry\'s waste… we are going to be left exposed unless we build our own closed models.”</p>
<p>The executive takeaway is simple: sustainability messaging without operational substance increasingly reads as theatre, then Gen Z is not in the mood for theatre unless it comes with impeccable production values plus zero hypocrisy.</p>
<h2>Culture is the new distribution.</h2>
<p>Gen Z doesn’t just buy objects. They buy belonging, proximity, plus experiences they can narrate.</p>
<p>That’s why brands that behave like cultural hosts—creating places, moments, rituals—often win disproportionate attention. Even outside luxury, you can see how modern lifestyle brands turn everyday rituals into cultural currency. Vogue recently covered Blank Street using London Fashion Week\'s the Daily as a platform, transforming coffee into an accessory, plus an identity signal.</p>
<p>Luxury has its own versions of this instinct: cafés, pop-ups, installations, community formats that feel more like participation than persuasion. The winning question isn’t “How do we go viral?” It’s “What do we create that people genuinely want to include in their lives?”</p>
<p>Which leads to the most irritating truth for traditionalists: Gen Z’s loyalty is often earned in the “small luxuries” first—beauty, fragrance, accessories, entry pieces, resale finds—then only later graduates to the grand purchase, if the brand proves it deserves it.</p>
<h2>What should executives actually do without pretending this is a “Gen Z strategy”.</h2>
<p>If you want a checklist, there are plenty of agencies who will sell you one with a pastel diagram. Gen Z will find it, then mock it. The more useful approach is a set of operating principles:</p>
<ol><li><strong>Treat perceived value as a strategic asset, not a pricing inconvenience.</strong> If prices rise, quality, service, plus durability must rise in ways customers can feel.</p>
<p><strong>Make your brand legible in one sentence.</strong> If you can’t explain what your house stands for plainly, Gen Z will define it for you, probably in a tone you won’t enjoy.</p>
<p><strong>Design entry points that carry real brand codes.</strong> Not generic “accessibility”. Identity-rich pieces that feel like the house, not a cash grab.</p>
<p><strong>Use resale as intelligence.</strong> Track what holds value, what becomes a punchline, what disappears. Then design accordingly.</p>
<p><strong>Stop faking community.</strong> Build things worth gathering around: craft, culture, creativity, knowledge, real participation. Community is a byproduct of contribution.</p>
<p><strong>Assume you are being audited.</strong> Because you are. Gen Z doesn’t “cancel”. They simply stop caring—far more lethal.</p>
<h2>The closing truth that will irritate somebody.</h2>
<p>Luxury leaders keep asking how to “speak to Gen Z” as if the generation is waiting quietly for the correct tone of voice. Gen Z is already speaking. Constantly. Publicly. Often brilliantly. Sometimes cruelly.</p>
<p>“Consumers today want to feel part of a community.” says Stéphane de La Faverie, President and CEO, The Estée Lauder Companies Inc.</p>
<p>The brands that win are the ones that stop trying to “talk to” Gen Z, then start building a business Gen Z enjoys defending: products that deserve the price, narratives that can be verified, plus a point of view strong enough to survive the next algorithmic mood swing.</p>',
    ],

    [
        'slug'     => 'hospitality-is-eating-luxurys-lunch-and-leaving-the-receipt-on-the-pillow',
        'title'    => 'Hospitality Is Eating Luxury’s Lunch and Leaving the Receipt on the Pillow.',
        'category' => 'hospitality-travel',
        'fields'   => [
            'art_standfirst'       => 'When did hotels become the most persuasive luxury marketers on earth? Why do affluent customers increasingly trust a “stay” more than a “buy”? What can product brands steal from hospitality’s playbook without looking like they’re cosplaying as a boutique resort?',
            'art_read_time'        => 6,
            'art_cornerstone'      => 0,
            'art_featured'         => 0,
            'art_subscribe_prompt' => 1,
            'art_seo_title'        => 'Hospitality Is Eating Luxury’s Lunch and Leaving the Receipt on the Pillow. | Future of Luxury',
            'art_meta_desc'        => 'Hotels and hospitality brands are becoming the most persuasive luxury marketers on earth. What product-led luxury houses can learn from the experience economy.',
        ],
        'content'  => '<p><strong>When did hotels become the most persuasive luxury marketers on earth?</strong> <strong>Why do affluent customers increasingly trust a “stay” more than a “buy”?</strong> <strong>What can product brands steal from hospitality’s playbook without looking like they’re cosplaying as a boutique resort? In the latest edition of </strong><strong>The Future of Luxury</strong><strong> by </strong>Summit Communication Group <strong>we explore the questions senior executives are being forced to answer in public.</strong></p>
<p>Luxury’s most unnerving pivot is not toward “experiences” as a slogan. It’s toward hospitality as the new proof of value—the place where pricing, service, story, sustainability, privacy, wellness, and cultural relevance are stress-tested in real time. Bain and Altagamma describe a “tectonic shift” as consumers favour experiential indulgence over conspicuous consumption, with hospitality, cruises, and fine dining among the beneficiaries.</p>
<h2>The Experience Is the Product. The Product Is the Souvenir.</h2>
<p>Luxury brands used to sell objects that implied a life. Hospitality sells the life itself, temporarily, at full margin, with no need for your customer to find wardrobe space.</p>
<p>That is the genius of modern luxury hospitality: it’s high-frequency validation. A handbag is judged in the mirror. A hotel is judged every fifteen minutes—arrival, scent, lighting, linen, staff eye-contact, temperature, silence, breakfast timing, spa choreography, check-out diplomacy. Hospitality wins because it performs the brand continuously. If it’s mediocre, you feel it in your bones by dinner. Omer Acar puts it bluntly, with the sort of line product marketers wish they’d coined first.</p>
<blockquote><p>“Luxury retail sells the dream but in hospitality we deliver the dream.” <strong>Omer Acar, CEO, Raffles Hotels &amp; Resorts and Fairmont Hotels &amp; Resorts</strong></p></blockquote>
<p>The implication for the wider luxury sector is awkward: customers now expect proof, not promise. They have grown tired of being told an item is exceptional. They want the brand to behave exceptionally while they are physically inside it.</p>
<h2>Why Hotels Are Becoming the Most Credible Storytellers in Luxury.</h2>
<p>Luxury used to rely on distance: mystery, gatekeeping, controlled visibility. Hospitality relies on proximity: intimacy, familiarity, “we already know how you like your martini.” One is theatre. The other is choreography. This is why hospitality is setting the benchmark. It offers a “full expression of identity” precisely because it can curate the customer’s time, not just their taste.</p>
<p>Aman’s urban expansion in New York is a useful case study in what “benchmark” now means. The proposition is not merely a room; it’s a designed retreat, a wellness complex, an atmosphere engineered to feel private in a public city—at prices that unapologetically frame tranquillity as a premium asset.</p>
<p>This is not escapism. It’s status repositioned as control: control of noise, control of attention, control of time, control of one’s nervous system. Product luxury can gesture at that. Hospitality can deliver it—hour by hour.</p>
<h2>Destination Dining Has Become Luxury’s Most Powerful Brand Platform</h2>
<p>Fine dining has become the language affluent consumers use to say: <em>I have taste, not merely purchasing power.</em></p>
<p>It’s why high-performing luxury properties treat restaurants as brand engines rather than amenities. Raffles Hotels &amp; Resorts examples are telling: the culinary programme becomes a narrative device—local flavour, a travelling chef’s signature, an “only here” sense of place—plus wellness as a parallel storyline, not a bolt-on spa menu.</p>
<p>Hospitality has learned what many product categories still resist: people don’t pay for ingredients, they pay for meaning—and meaning is easiest to sell when it is eaten, remembered, photographed, talked about, repeated.</p>
<h2>The New Luxury Customer Wants a Life System (Not a Logo)</h2>
<p>The luxury market may have held steady in aggregate, but the story underneath is a redistribution of desire. The customer base has shrunk. Big spenders still show up. Aspirational buyers hesitate, then rationalise their retreat with the most morally acceptable sentence in modern consumer life: “I’m spending on experiences.”</p>
<p>Hospitality benefits because it can credibly claim it is not “stuff.” It is memory, wellness, connection, restoration. Even the language sounds like a therapist’s invoice. Convenient.</p>
<p>Julie Bramham describes the same behavioural shift from a neighbouring luxury ecosystem—spirits—where the emphasis moves toward “experience-led” value.</p>
<blockquote><p>“People are really upgrading towards luxury experiences.” <strong>Julie Bramham, Managing Director, Diageo Luxury Group</strong></p></blockquote>
<p>The lesson for fashion, jewellery, beauty, and accessories is not “host more events.” It’s more uncomfortable: stop selling products as objects. Start selling them as <em>moments</em> with a beginning, middle, end, plus social proof. Hospitality does not ask, “Do you want this?” It asks, “How do you want to feel?”</p>
<h2>The Hospitality Playbook Luxury Brands Can Actually Copy</h2>
<p>The great hotel groups have spent decades systemising the art of making people feel anticipated rather than processed, cared for rather than “converted.” They understand that the premium is earned in tiny, repeatable moments—arrival, language, light, timing, discretion—then compounded through consistency. That is why their pricing power survives turbulence. That is why their guests forgive small flaws. That is why a stay can feel more “luxurious” than a purchase that sits guiltily in a wardrobe.</p>
<p>The point for product brands is not to start playing hoteliers. It’s to borrow the underlying discipline: build a world that holds together under scrutiny, remove friction with quiet competence, then deliver an emotional outcome so reliably that customers stop asking what it costs, then start asking when they can come back.</p>
<h2>// 1. Make the brand physical again, then make it feel human</h2>
<p>Hospitality’s advantage is the staff—trained, present, calibrated. Luxury retail has staff too, of course. Yet too often the store feels like a museum with a till.</p>
<p>Clare Hornby, Founder &amp; CEO, ME+EM, makes a sharp point about stores as education and engagement rather than mere distribution. “Our stores… are… the main physical touchpoint for our brand… a tool for engaging with and educating our customer.”</p>
<p>Hospitality doesn’t merely “engage.” It <em>hosts</em>. That verb matters. Hosting implies responsibility for the guest’s comfort, flow, dignity. Luxury brands that behave like hosts will outperform those that behave like security guards beside a display case.</p>
<h2>// 2. Build “only here” into the offer</h2>
<p>Hotels win with “sense of place” logic: the feeling that you cannot replicate the experience elsewhere without losing its meaning. Even when the brand is global, the <em>memory</em> is local.</p>
<p>The retail corollary is not endless localisation theatre. It’s <strong>contextual specificity</strong>: product narratives tied to craft, origin, cultural references, plus experiential layers that cannot be copy-pasted.</p>
<h2>// 3. Treat multi-sensory design as a strategy</h2>
<p>Hospitality understands lighting, scent, acoustics, temperature, texture. Many product brands still treat these as “store design.”</p>
<p>Simon Mitchell, Co-Founder, Sybarite, describes immersive storytelling as the core of a modern luxury experience, with the explicit aim of connecting senses. “Luxury storytelling and immersive hybrid retail is at the heart of Sybarite.”<strong> </strong></p>
<p>Hospitality has already proved the ROI of sensory coherence. Luxury brands should stop pretending it’s optional.</p>
<h2>// 4. Make friction disappear, then charge more for the privilege</h2>
<p>Hospitality is the original friction-removal industry. It also monetises friction removal—early check-in, late check-out, car service, concierge, private dining, personalised itineraries.</p>
<p>Susie McCabe’s language—though about outlet destinations—captures the same logic: experiences plus convenience drive performance.</p>
<blockquote><p>“Our mission is to provide customers with the finest retail experiences possible… delivering even greater convenience and flexibility for our guests.” <strong>Susie McCabe</strong>, <strong>Co-CEO, McArthurGlen Group</strong></p></blockquote>
<p>The broader lesson is blunt: luxury is increasingly the absence of hassle. If your customer is doing work—queueing, chasing, wondering, correcting, regretting—you are not premium. You are just expensive.</p>
<h2>The Unspoken Economic Advantage Hospitality Has</h2>
<p>There’s a macro backdrop that makes hospitality’s rise even more consequential: sticky services inflation. When “services” stay expensive, experiences become simultaneously harder to deliver, easier to justify raising prices for—because labour and service are the point.</p>
<p>Megan Greene has repeatedly highlighted how persistent services inflation can be, driven by wages.</p>
<blockquote><p>“Services inflation… is driven largely by sticky wages.” <strong>Megan Greene, External Member, Monetary Policy Committee, Bank of England</strong></p></blockquote>
<p>Hospitality is essentially a premium wage-to-memory conversion machine. Product luxury, by contrast, is forced to justify price rises through materials, craft, scarcity, brand heat—then gets accused of “greedflation” if the story is unconvincing. Hotels can raise rates and call it “seasonality” with a straight face.</p>
<h2>The Brands Setting the Standard.</h2>
<p>This is not a beauty parade. It’s a pattern-spotting exercise.</p>
<ul><li><strong>Raffles and Fairmont</strong>: brand identity expressed through culinary programming, wellness, narrative-led properties—plus explicit crossover with fashion and retail partnerships as storytelling accelerants.</p>
<p><strong>Aman</strong>: a masterclass in monetising stillness and privacy, with urban tranquillity sold as a scarce commodity.</p>
<p><strong>Luxury retail hubs behaving like destinations</strong>: Singapore’s luxury ecosystem shows how experiential retail concepts, hospitality tie-ins, and in-store lifestyle programming turn a small market into a high-performing testbed.</p>
<p>The signal isn’t the chandelier. It’s whether the brand can create a repeatable emotional outcome: calm, confidence, belonging, intrigue, renewal. Hospitality measures these outcomes obsessively because a guest can defect tomorrow. Product brands should consider adopting the same paranoia.</p>
<h2>What the Wider Luxury Sector Should Do Next</h2>
<ol><li><strong>Redesign the customer journey as a stay, not a sale</strong>: arrival, orientation, ritual, “signature moment,” departure, re-entry.</p>
<p><strong>Build a service doctrine</strong>: not “clienteling,” not “VIP,” but an explicit philosophy of hosting, trained and audited.</p>
<p><strong>Make memory the KPI</strong>: what is the story your customer tells 48 hours later? If it’s about price, you’ve lost.</p>
<p><strong>Treat experiences as product development</strong>: prototype, iterate, retire what’s stale. Hotels do this constantly.</p>
<p><strong>Stop confusing theatre with trust</strong>: the customer can smell performance. Hospitality wins when the care feels real.</p>
<p>The punchline is mildly humiliating: hospitality isn’t “adjacent” to luxury anymore. It’s becoming the luxury category that teaches all the others how to behave.</p>',
    ],

    [
        'slug'     => 'ralph-lauren-the-art-of-selling-through-chaos',
        'title'    => 'Ralph Lauren: The Art of Selling through Chaos.',
        'category' => 'brand-positioning',
        'fields'   => [
            'art_standfirst'       => 'How do you protect margins when geopolitics keeps rewriting your cost base? How do you keep customers loyal when “discretionary” starts behaving like a warning label? How do you grow without flooding the market, then waking up one morning to find your brand on clearance next to yesterday’s ambitions?',
            'art_read_time'        => 6,
            'art_cornerstone'      => 0,
            'art_featured'         => 0,
            'art_subscribe_prompt' => 1,
            'art_seo_title'        => 'Ralph Lauren: The Art of Selling through Chaos. | Future of Luxury',
            'art_meta_desc'        => 'How Ralph Lauren protects margins, defends brand equity and grows through tariff turbulence and macroeconomic volatility. A masterclass in resilience.',
        ],
        'content'  => '<p><strong>How do you protect margins when geopolitics keeps rewriting your cost base?</strong> <strong>How do you keep customers loyal when “discretionary” starts behaving like a warning label?</strong> <strong>How do you grow without flooding the market, then waking up one morning to find your brand on clearance next to yesterday’s ambitions? In the latest edition of </strong><strong>The Future Of Luxury</strong><strong> by </strong><strong>Summit Communication Group</strong><strong> we explore three questions every luxury board now has on speed-dial.</strong></p>
<p>If you want a single, unusually instructive answer hiding in plain sight, look at Ralph Lauren. Not because it’s immune to turbulence. It isn’t. It’s because the company has spent years building a business model that treats volatility as an operating condition, not a surprise party.</p>
<p><strong>Ralph Lauren</strong>’s fiscal 2025 results—revenue up, direct-to-consumer comps strong, average unit retail rising, margins expanding—didn’t happen because the world became kinder. They happened because the brand has been deliberately engineered to sell at full price, stay coherent across channels, then adjust levers quickly when the macro story turns hostile.</p>
<h2>Turbulence is not an event anymore, it is the atmosphere</h2>
<p>Luxury executives now have to plan in a world where tariffs can reprice your supply chain, wars can distort tourism flows, central banks can keep consumers cautious longer than anyone likes to admit, plus social platforms can turn a minor service failure into a reputational bonfire by lunchtime.</p>
<p>The International Monetary Fund has warned that surging tariffs weaken growth, raise inflation, increase uncertainty—without the decency of a clean ending. The OECD has made similar noises about trade barriers and fragmentation dragging on living standards. The result is a luxury market where the “right” strategy is rarely the cleverest idea. It’s the strategy that can keep working when assumptions change mid-quarter.</p>
<p><strong>Ralph Lauren</strong>’s approach is not to predict the weather. It’s to build a house that doesn’t collapse when it rains.</p>
<blockquote><p>“Our Next Great Chapter: Drive plan is grounded in… our brand’s distinctive positioning and desirability… the enduring power of our products across lifestyle categories… and our expanding presence in key cities around the world.” <strong>Patrice Louvet, President and CEO, Ralph Lauren Corporation</strong></p></blockquote>
<h2>What Ralph Lauren sells in a turbulent world</h2>
<h2>Most companies sell products. Ralph Lauren sells a stable idea of taste.</h2>
<p>That sounds poetic until you realise it is also brutally practical. In an anxious economy, consumers don’t only buy objects. They buy reassurance. They buy “I won’t regret this.” They buy “this will still make sense next year.” <strong>Ralph Lauren</strong>’s core product architecture—polos, tailoring codes, American lifestyle symbolism—translates uncertainty into something wearable. It’s the rare brand where familiarity doesn’t feel tired. It feels dependable.</p>
<p>This matters because turbulence doesn’t merely hit demand. It changes the psychology of demand. When consumers become cautious, they become editorial. They buy fewer things. They buy “better” things. They demand fewer reasons to justify themselves. <strong>Ralph Lauren</strong> has quietly been setting itself up for exactly that consumer.</p>
<blockquote><p>“For nearly 60 years, we have stayed true to our vision of timeless style, authenticity, optimism and a life well-lived.” <strong>Ralph Lauren, Executive Chairman and Chief Creative Officer, Ralph Lauren Corporation</strong></p></blockquote>
<h2>The real playbook is not pricing it is permission</h2>
<p>A lot of luxury discussion about turbulence quickly devolves into pricing: raise prices, protect margin, keep the aura intact. Sensible, until you realise that “pricing power” is not a lever you pull. It’s the permission the market grants. <strong>Ralph Lauren</strong> has been earning that permission by doing several unglamorous things consistently:</p>
<ul><li>Reducing discount dependency</p>
<p>Improving quality of sales</p>
<p>Controlling distribution exposure</p>
<p>Investing in brand experiences rather than just campaigns</p>
<p>Using data to keep inventories closer to demand</p>
<p>Vogue Business’s breakdown of <strong>Ralph Lauren</strong>’s “brand elevation” is essentially an autopsy report on how to stop being overexposed without becoming irrelevant, then how to raise AUR without triggering consumer revolt.</p>
<p>The Wall Street Journal reported <strong>Ralph Lauren</strong>’s willingness to raise prices further, helped by stronger demand among more resilient consumers, with management discussing steeper increases to counter tariff pressure. That is not bravado. That’s what happens when your brand has spent years behaving like a premium rather than merely charging like one.</p>
<blockquote><p>“Global Direct-to-Consumer comparable store sales increased… including… high single-digit growth in Average Unit Retail, demonstrating continued strong pricing power.” <strong>Ralph Lauren Corporation, FY2025 earnings release.</strong></p></blockquote>
<h2>Wholesaling without dying of wholesaling</h2>
<p>One of the great corporate tragedies is the brand that believes it is luxury, then distributes itself like a commodity. Wholesale can scale you. It can also dilute you. It can teach customers to wait for markdowns. It can turn a “lifestyle” into a rack.</p>
<p><strong>Ralph Lauren</strong>’s filings are unusually clear about the company’s channel mix and intent: a large global DTC footprint, a wholesale network that still matters, plus an explicit focus on elevating in-store assortment and presentation to drive full-price sell-through.</p>
<p>This is the point executives often miss. The question isn’t “DTC versus wholesale.” It’s “Are we selling a coherent brand experience in every door we choose to exist in?”</p>
<p>When retail collapses hit the headlines—department stores under stress, multi-brand retailers wobbling, luxury’s aspirational tier trading down—the brands that suffer most are those that outsourced their story to third parties, then acted shocked when the story came back misquoted. <strong>Ralph Lauren</strong>’s discipline has been to keep the story legible everywhere, while steadily shifting the centre of gravity toward channels it can control.</p>
<blockquote><p>“Our performance was… balanced across our retail and wholesale channels this holiday, reflecting our growing brand desirability and pricing power globally.” <strong>Patrice Louvet, President and CEO, Ralph Lauren Corporation </strong></p></blockquote>
<h2>Tariffs, geopolitics, supply chain and the art of not panicking</h2>
<p>Turbulence is fatal when it hits margin and you don’t have options.</p>
<p>In May 2025, Reuters reported <strong>Ralph Lauren</strong> weighing further price hikes as tariffs and inflation complicated the outlook, with management also pointing to supply chain diversification as mitigation. Vogue Business described new US tariffs and the shockwaves they send through fashion’s Asia-centric supply chains, underlining how quickly cost structures can be rewritten for the entire sector.</p>
<p>This is where <strong>Ralph Lauren</strong>’s long-game competence shows. The company has been building operational agility—diversifying sourcing, strengthening its balance sheet, then using analytics and AI to reduce inefficiency. Its FY2025 earnings release highlighted more than $2bn in cash and short-term investments alongside “well-positioned inventories.”</p>
<p>Operational agility is not a back-office virtue anymore. It’s a brand asset. If you can keep product availability high while competitors drown in late deliveries or emergency discounting, your customer concludes you are “better” without ever seeing your internal dashboards.</p>
<blockquote><p>“We plan to drive our momentum forward… underpinned by a culture of excellence and agility.” <strong>Patrice Louvet, President and CEO, Ralph Lauren Corporation</strong></p></blockquote>
<h2>The quiet role of product leadership in a volatile world</h2>
<p>If turbulence forces simplification, then product discipline becomes existential.</p>
<p>Ralph Lauren’s structure is revealing here. Halide Alagöz’s remit spans end-to-end product life cycle, from development and merchandising through sourcing and store presentation.  That scope is not decorative. It’s an acknowledgement that in 2026, “creative” and “commercial” cannot be separate religions. You need one system, then you need it to execute globally.</p>
<p>This is also where Ralph Lauren’s lifestyle breadth becomes a strategic hedge. When one category softens, others can carry performance. When apparel demand becomes choppy, fragrance, home, accessories, plus experiences can sustain momentum. That is not dilution when it’s governed well. It’s resilience.</p>
<h2>The selling lesson most luxury leaders don’t want to hear</h2>
<p>In turbulent times, the most seductive mistake is to chase growth by expanding distribution, pushing more volume, relying on promotions to “keep the top line moving.” It works right up until it destroys the brand’s ability to charge full price later. Then you spend years trying to reverse it, which is effectively what many heritage names have been doing since the easy-money era ended.</p>
<p><strong>Ralph Lauren</strong>’s advantage is that it has already been living inside the discipline that turbulence demands:</p>
<ol><li>Sell more at full price, not more at any price</p>
<p>Choose doors rather than accept doors</p>
<p>Protect icons rather than chase novelty</p>
<p>Invest in experience so pricing feels earned</p>
<p>Keep a balance sheet that buys you time</p>
<p>That last point matters more than executives like to admit. Time is the most underpriced luxury asset in business. If you have cash, you can avoid panic. If you avoid panic, you can avoid discounting. If you avoid discounting, you preserve the very pricing power you’re trying to defend.</p>
<h2>How to emerge stronger without becoming boring</h2>
<p><strong>Ralph Lauren</strong>’s model isn’t about retreat. It’s about <em>controlled expansion</em>—winning in key cities, building cohesive consumer ecosystems, using digital and connected retail capabilities, then keeping the brand’s codes intact while broadening relevance.</p>
<p>It’s also about recognising that “selling” in 2026 is not just persuasion. It’s operational reliability plus cultural legitimacy plus a customer experience that makes the premium feel rational.</p>
<p>In a volatile market, customers don’t reward the loudest brand. They reward the brand that feels most <em>certain</em>—not arrogant certainty, but competence. The kind that shows up as the right product, in the right place, at the right moment, delivered with ease.</p>
<p><strong>Ralph Lauren has been practising that competence for years. Turbulence simply makes it easier to see who was swimming naked.</strong></p>',
    ],

    [
        'slug'     => 'what-aupen-tells-us-about-the-next-wave-of-asian-luxury',
        'title'    => 'What Aupen tells us about the next wave of Asian luxury.',
        'category' => 'the-new-luxury-customer',
        'fields'   => [
            'art_standfirst'       => 'Can a luxury brand become famous before it becomes real? What, exactly, does a young Asian house own when celebrity heat arrives before legal certainty? And in an age of AI-trained archives, counterfeit rings, platform dependency and trademark warfare, is taste still enough on its own?',
            'art_read_time'        => 11,
            'art_cornerstone'      => 0,
            'art_featured'         => 0,
            'art_subscribe_prompt' => 1,
            'art_seo_title'        => 'What Aupen tells us about the next wave of Asian luxury. | Future of Luxury',
            'art_meta_desc'        => 'What Singapore-born Aupen reveals about the structural challenges, IP risks and cultural ambitions shaping the next wave of serious Asian luxury houses.',
        ],
        'content'  => '<p><strong>Can a luxury brand become famous before it becomes real? What, exactly, does a young Asian house own when celebrity heat arrives before legal certainty? And in an age of AI-trained archives, counterfeit rings, platform dependency and trademark warfare, is taste still enough on its own? In the latest edition of <em>The Future Of Luxury</em> we explore the small Singapore handbag brand that became, almost overnight, a rather large lesson for the whole region.</strong></p>
<p>There is something so deliciously absurd about the whole AUPEN affair that one is tempted to treat it as fashion farce. A young Singapore handbag label, all sloping leather and asymmetrical poise, finds itself in a trademark brawl with Target over <em>Auden</em>, the American retailer’s intimates and sleepwear line. One can imagine the handbags blushing. Yet this is exactly the sort of ridiculous little drama modern luxury produces when the romance of the thing outruns the legal plumbing. Aupen did not merely wander into a dispute. It wandered into the future.</p>
<p>The future, alas, was waiting with a filing cabinet. In September 2025, Singapore’s Intellectual Property Office said Target had registered “AUDEN” in Singapore and the United States in 2018, that Aupen had applied for “AUPEN” in both jurisdictions in 2023, and that any opposition to the US application would be heard in the United States. CNA, citing USPTO documents, reported that if the case proceeds, a trial would likely fall between August 2026 and April 2027.</p>
<p>That is the comic surface. Beneath it lies the sort of story the luxury industry tells itself it is too refined to need, then studies with the fascinated horror of a duchess watching the maid faint at dinner.</p>
<blockquote><p>“A luxury brand can become famous before it becomes real. That is now one of the central risks of modern fashion.” <strong>Gregory Gray , CEO of Summit Communication Group </strong></p></blockquote>
<h2>Celebrity heat before structural certainty</h2>
<p>Aupen was founded in 2022. It built its reputation on sculptural, logo-light bags with a recognisable sloping silhouette, and it rose with indecent speed once Taylor Swift, Beyoncé, Kylie Jenner and others began carrying them. Its own materials describe it as a Singapore-founded contemporary luxury brand “celebrated for its asymmetrical designs”, which is a neat official phrase for what was really happening: the brand had found a shape that photographs well, reads instantly, and makes a woman in a white T-shirt look as if she has a secret. Commercially speaking, that is half the battle.</p>
<p>Nicholas Tan, who finally stepped out from behind the curtain in late 2024, insisted he stayed hidden at first because he did not want Aupen reduced to an “influencer brand”. It is a smart instinct, because once the founder becomes the entertainment, the product usually gets demoted to supporting actor. “I wanted to really make sure that our brand foundation and our product were solid.” Nicholas Tan</p>
<p>Michael Kors, of all people, supplied a little corroboration from the witness stand during the Tapestry-Capri merger trial. It was the kind of accidental compliment brands cherish because it is uttered under oath and therefore feels less like flattery than market evidence. “When I looked at the brand, the website crashed immediately.” Michael Kors, testifying about Aupen</p>
<p>That is not nothing. When Michael Kors is invoking your name in a courtroom to explain where heat is moving in the handbag market, you are no longer merely an internet moodboard with shipping rates. You have become legible to the system. The trouble begins when a brand becomes legible to the system faster than it becomes legible to itself.</p>
<h2>The unglamorous half of luxury</h2>
<p>The other half of the battle, unfortunately, is less photogenic. It does not appear in paparazzi shots outside <em>Saturday Night Live</em> after-parties. It does not sit on the crook of a celebrity elbow like destiny. It consists of marks, registrations, classes, designs, jurisdictions, contracts, archives, websites, and the sort of paperwork founders tend to regard the way children regard cod liver oil: possibly good for them, certainly vile.</p>
<p>At Summit Communication Group, as a design-led communications agency, we look at this sort of episode and see not a scandal but a sequencing problem. The product moved first. The visibility arrived second. The structural shell lagged behind. In modern luxury, that gap can be fatal. “Visibility is no longer the prize. In modern luxury, ownership is.”</p>
<h2>Paris nods politely</h2>
<p>For a moment, though, the fairy tale behaved itself. In July 2024, Aupen announced a partnership with LVMH Métiers d’Art. It was not an acquisition. It was not equity. It was, if anything, more flattering than that: the sort of arrangement that says, with perfect Parisian ambiguity, <em>we do not own you, but we approve of your existence</em>.</p>
<p>LVMH’s representative praised the brand’s emphasis on traceability and transparency, while Tan told <em>Glossy</em> and other outlets that the attraction was access to better ateliers, tanneries, metalwork specialists, and manufacturing discipline. He also said the company wanted to remain independent while upgrading quality.</p>
<blockquote><p>“Aupen’s bold designs prioritise traceability and transparency.” <strong>LVMH Métiers d’Art statement</strong></p></blockquote>
<p>That line is more revealing than it first appears. “Traceability and transparency” are usually treated as moral accessories in luxury, nice to have around, like a beautifully mannered nephew. In fact they are increasingly central to the commercial proposition. They are part of the answer to the question the modern customer is asking, even when she is too polite to do so aloud: <em>what exactly am I paying for?</em> A shape? A status cue? A story? A manufacturing chain? A claim to continuity? Or merely the privilege of being early to something before everybody else arrives and ruins it?</p>
<p>Tan said, quite sensibly, that the point of the partnership was to produce “true investment pieces you can pass on”, and to “grow organically, versus chase sales”. It is a handsome sentiment. It is also exactly the sort of sentence that sounds most attractive just before the market introduces a founder to some rougher truths about intellectual property, registrability, international expansion, and the difference between a beloved product and a defensible business.</p>
<p>By late 2025, Aupen had laid off staff, scrubbed much of its website and social presence, and become entangled not only in the Target dispute but in a separate public rebuke from Singapore authorities after false statements made by Tan and the company about the trademark saga were formally corrected. One almost felt for everyone involved, in the way one feels for aristocrats who discover that silver can, in fact, be repossessed.</p>
<h2>AUPEN is not an anomaly</h2>
<p>The important thing is that Aupen is not an anomaly. It is a specimen. The luxury market is now in the awkward stretch after its long post-pandemic party, when the lights come up and even the best-dressed guests look faintly accusing. McKinsey noted in January 2025 that price increases had accounted for more than 80 per cent of luxury growth in recent years, while volume gains were more modest.</p>
<p>Bain said in November 2025 that global luxury spending was broadly stable at €1.44 trillion, but that structural shifts were reshaping the market, with margins under pressure and brands needing to defend value without dulling desirability. This is the context in which the next wave of Asian luxury will be built: slower growth, more exacting consumers, less tolerance for lazy price architecture, more scrutiny on quality, and a rising demand that the thing be real as well as chic.</p>
<p>That is why Aupen’s little melodrama matters beyond Aupen. The next generation of Asian luxury brands will be launched into a market that is no longer forgiving in the old way. The age of being discovered, flattered, funded, and automatically forgiven for administrative sins is looking a little tired. The companies that will last are not simply the ones with design instinct. They are the ones with legal architecture, naming discipline, archive logic, category clarity, and the sort of first-party customer infrastructure that does not vanish the moment Meta changes the algorithm and shrugs.</p>
<p>From our vantage point, that is the real divide opening up in luxury now: not old versus new, nor East versus West, but brands with a structure versus brands that are still mistaking velocity for durability.</p>
<h2>What does a young house actually own</h2>
<p>It is worth pausing on the word <em>own</em>. Luxury uses it all the time without ever quite defining it. Brands talk about “owning” their customer, “owning” the moment, “owning” the conversation, all of which is faintly ridiculous and sounds like something said by a man in expensive trainers at a conference in Miami. But the Aupen episode returns us to a more concrete question. What does a young luxury brand actually own? Its name? Its shape? Its archive? Its customer data? Its supply chain logic? Its codes? Its narrative? Its place in the mind? If the answer is “some of that, probably”, then what you have is not yet a house. What you have is a possibility wearing very nice hardware.</p>
<h2>The counterfeit test</h2>
<p>Aupen, to its credit, discovered one version of this rather early through counterfeiting. In March 2025, <em>The Straits Times</em> reported that Chinese authorities had raided a Guangdong factory that was allegedly producing fake Aupen bags worth about one million yuan. Tan’s response was brisk, unillusioned, and rather more useful than many founder monologues about brand purpose.</p>
<blockquote><p>“If anyone sees an Aupen bag listed elsewhere, it’s a counterfeit.” <strong>Nicholas Tan</strong></p></blockquote>
<p>There is a lesson hidden in that episode which the romance merchants of luxury prefer not to discuss. The moment a brand becomes legible enough to be copied, it has already crossed into a different category of economic life. It is no longer only selling objects. It is defending symbols. Aupen learned, in quick succession, that one can be copied from below by counterfeiters and challenged from above by a mass retailer with an older filing. There is no more efficient course in modern luxury than being squeezed simultaneously by Guangdong and Minnesota.</p>
<h2>The archive is not a museum</h2>
<p>And then there is the archive, which many young brands still treat as if it were a problem for old Europeans with too many trunks in the cellar. This is a mistake. In the next phase of luxury, the archive is not a museum. It is infrastructure. Look at Porsche, which has been far more intelligent about this than much of fashion. Its Heritage Design strategy explicitly reinterprets iconographic elements from cars dating back to the 1950s through the 1980s for contemporary limited models, while its more recent reissue of historical fabrics such as Pasha and Pepita turned decades of trim catalogues, production cards, prototypes and even untouched original rolls of material into commercially usable design assets once again. That is the point. The archive is not there merely to be admired by visitors in soft shoes. It is a working source code: for product development, for restoration, for merchandising, for storytelling, and, if a brand has any sense, for margin.</p>
<p>That sentence, if true in practice and not merely elegant in theory, changes the argument completely. Because once a brand’s archive becomes operational rather than ceremonial, the definition of what must be protected becomes much larger. It is not just the trademark on the dust bag. It is the design vocabulary, the provenance language, the archive data, the training set, the internal references, the customer interactions, the signatures that make a brand recognisable even when the logo is nowhere in sight. In such a world, luxury’s future battle is not only about who can make beautiful things. It is about who controls the machine-readable version of their own memory before somebody else does.</p>
<h2>AI is not the villainess, vagueness is</h2>
<p>The old objection, of course, is that AI will flatten everything, turn every brand into a mood board with a CRM, and leave us all dressed by autocomplete. That objection is not wrong. It is merely incomplete. The better question is under what conditions AI becomes a solvent rather than a tool. Bain and Comité Colbert put the matter plainly in their January 2025 report on luxury and artificial intelligence. The technology should be used when it adds differentiation and real value, they argued, and should “augment—not replace—human teams”. Delphine Tour Helin of Yves Saint Laurent Beauté was even more direct.</p>
<blockquote><p>“The rise of generative AI is an inescapable and enduring trend.” <strong>Delphine Tour Helin, Yves Saint Laurent Beauté </strong></p></blockquote>
<p>That is the adult position. Not breathless surrender. Not prim refusal. Controlled adoption in service of distinctiveness. Which is precisely why the Aupen story matters. If a young Asian luxury brand can reach celebrity escape velocity before its legal and structural architecture has properly caught up, imagine what happens once archive-trained AI, formal resale, authenticated circular markets, and copyable design codes all begin operating at the same time. One does not need to be apocalyptic about it. One merely needs to be organised.</p>
<h2>From mood to institution</h2>
<p>There is another reason Aupen is worth studying, and it is slightly impolite. The brand was, for a time, a perfect expression of what social media most rewards: a recognisable silhouette, minimal language, mystery, celebrity validation, and a price low enough to feel aspirational without requiring a family office. It was exquisitely suited to the age of algorithmic glamour. But algorithmic glamour is not the same thing as house-building. A bag can become famous before the company behind it becomes durable. In fact that may now be one of the defining risks of modern luxury. The customer sees the object. The founder sees momentum. The algorithm sees engagement. The system sees exposure. And the lawyers, if they arrive too late, see a preventable mess in very expensive leather.</p>
<p>To say this is not to sneer at Aupen. It is to say that Aupen has had the misfortune to become a case study before it has had the chance to become an institution. Tan, in fairness, has shown a number of the right instincts. He understood silhouette. He resisted logo-thumping. He pursued better manufacturing. He secured meaningful industrial backing without surrendering independence. He seems to understand, genuinely, that product matters more than founder theatre.</p>
<p>But the market has now taught him, rather nastily as markets prefer to do, that product-first is not enough. The next serious Asian luxury brand will need to be product-first, certainly, but also jurisdiction-first, archive-first, design-rights-first, governance-first and customer-data-first. Not because this is glamorous. Because glamour without those things now has the lifespan of a good rumour.</p>
<p>This is also why the easy category labels are beginning to look silly. “Accessible luxury” is one of those terms the industry repeats because it is convenient, though it says almost nothing. It lumps together brands with radically different ambitions, structures, margins, and futures, then acts surprised when one of them decides it would rather not remain in the middlebrow foyer of fashion forever. Tan himself resisted the label, saying he wanted Aupen to carve out its own space rather than be defined by existing industry categories. Sensible. The trouble is that once you reject the old category, you must build the new category yourself. That requires a great deal more than charm.</p>
<p>And Asia, on this point, is not where it was even five years ago. IMD argued in February 2026 that emerging Chinese players are blending traditional luxury codes with technology and customer intimacy in ways that force Western brands to clarify value and adapt culturally. That observation travels beyond China. Across Asia, the question is no longer whether local luxury businesses can produce objects, aesthetics, or atmospherics worthy of global attention. Of course they can. The question is whether they can build institutions around those things before the market, the counterfeiters, the platforms, or the registries teach them a lesson in public.</p>
<h2>The thing survives its moment</h2>
<p>Luxury loves to talk about timelessness because it sounds expensive. What it means, in practice, is rather simpler and much less poetic: the thing survives its moment. The next wave of Asian luxury will not be decided by who can manufacture a bag quickly, seed it to the right women, or flatter Paris into a supply agreement. It will be decided by who can convert taste into owned value before the market, the copycats, the platforms, or the filing offices convert it for them.</p>
<p>Aupen is not the cautionary tale because it aimed too high. It is the cautionary tale because it was trying to become a house in an era that punishes anyone who still thinks a house is mostly a mood. “The next serious Asian house will not merely be desirable. It will be defensible.”</p>',
    ],

];

$article_ids = [];
foreach ( $articles as $art ) {
	$post_id = summit_seed_post( 'article', $art['slug'], [
		'post_title'   => $art['title'],
		'post_content' => $art['content'],
	] );
	if ( ! $post_id ) { continue; }
	$article_ids[ $art['slug'] ] = $post_id;

	// Always update fields (supports re-runs after content changes)
	if ( function_exists( 'update_field' ) ) {
		foreach ( $art['fields'] as $key => $value ) {
			update_field( $key, $value, $post_id );
		}
	}
	// Update post_content on existing posts too
	wp_update_post( [
		'ID'           => $post_id,
		'post_content' => $art['content'],
		'post_title'   => $art['title'],
		'post_name'    => $art['slug'],
	] );

	if ( isset( $article_cats[ $art['category'] ] ) ) {
		wp_set_object_terms( $post_id, (int) $article_cats[ $art['category'] ], 'article_category' );
	}
}


// 5. PODCAST SEASON — Source: summit_content_model.md line 219
// ═════════════════════════════════════════════════════════════════════════════
echo "\n-- Podcast Season --\n";

$season_id = summit_seed_post( 'podcast_season', 'season-one', [
	'post_title' => 'Season One',
] );

if ( $season_id && function_exists( 'update_field' ) ) {
	update_field( 'ps_season_number', 1, $season_id );
	update_field( 'ps_subtitle', 'The New Value Equation', $season_id );
	update_field( 'ps_theme', 'Luxury value is being re-priced in public, with proof becoming a formal requirement rather than a branding flourish.', $season_id );
	update_field( 'ps_thesis', 'The industry can still win, yet it cannot simply keep doing the same things with larger price tags. Season One investigates value under scrutiny: pricing permission, the secondary market as public audit, experience-led growth, craft as engineered system, and the new codes that will define the next era of luxury.', $season_id );
	update_field( 'ps_is_featured', 1, $season_id );
	update_field( 'ps_seo_title', 'Season One — The New Value Equation | Tastemakers', $season_id );
	update_field( 'ps_meta_desc', 'Tastemakers Season One examines how luxury value is being re-priced, from pricing permission and craft to experience-led growth and the new codes of modern luxury.', $season_id );
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
	update_field( 'dl_description', 'A visual essay exploring the defining characteristics of luxury: craft, culture, rarity, exclusivity, time, heritage and the commercial systems that make them enduring. Based on the article series published in The Future of Luxury.', $download_id );
	update_field( 'dl_file_type', 'PDF', $download_id );
	update_field( 'dl_file_size', '', $download_id ); // Awaiting final asset
	update_field( 'dl_cta_label', 'Download the Deck', $download_id );
	update_field( 'dl_gated', 0, $download_id );
	update_field( 'dl_seo_title', 'The Characteristics of Luxury | Summit Communication Group', $download_id );
	update_field( 'dl_meta_desc', 'A visual essay exploring the characteristics that define enduring luxury brands, from craftsmanship and heritage to scarcity, culture and controlled access.', $download_id );
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

	// Case study -> article: Teddy William (branding) -> Ralph Lauren article (brand positioning)
	if ( isset( $case_study_ids['teddy-william'], $article_ids['ralph-lauren-the-art-of-selling-through-chaos'] ) ) {
		update_field( 'cs_related_article', [ $article_ids['ralph-lauren-the-art-of-selling-through-chaos'] ], $case_study_ids['teddy-william'] );
		echo "  [linked] teddy-william -> ralph lauren article\n";
	}

	// Case study -> episode: Tastemakers podcast case study -> The Great Hangover
	if ( isset( $case_study_ids['tastemakers-podcast'], $episode_ids['the-great-hangover'] ) ) {
		update_field( 'cs_related_episode', $episode_ids['the-great-hangover'], $case_study_ids['tastemakers-podcast'] );
		echo "  [linked] tastemakers case study -> the great hangover\n";
	}

	// Article -> case study: Ralph Lauren -> Teddy William
	if ( isset( $article_ids['ralph-lauren-the-art-of-selling-through-chaos'], $case_study_ids['teddy-william'] ) ) {
		update_field( 'art_related_case', $case_study_ids['teddy-william'], $article_ids['ralph-lauren-the-art-of-selling-through-chaos'] );
		echo "  [linked] ralph lauren article -> teddy william\n";
	}

	// Article -> article: Characteristics of Luxury -> Hill of Grace + Aupen
	if ( isset( $article_ids['characteristics-of-luxury-a-visual-conversation'], $article_ids['from-hill-of-grace-to-argyle-pink-diamonds-why-australias-luxury-houses-need-a-harder-shell'] ) ) {
		$related = array_filter( [
			$article_ids['from-hill-of-grace-to-argyle-pink-diamonds-why-australias-luxury-houses-need-a-harder-shell'] ?? 0,
			$article_ids['what-aupen-tells-us-about-the-next-wave-of-asian-luxury'] ?? 0,
		] );
		update_field( 'art_related_articles', array_values( $related ), $article_ids['characteristics-of-luxury-a-visual-conversation'] );
		echo "  [linked] characteristics of luxury -> hill of grace + aupen\n";
	}

	// Article -> download: Characteristics of Luxury -> download deck
	if ( isset( $article_ids['characteristics-of-luxury-a-visual-conversation'] ) && $download_id ) {
		update_field( 'art_related_download', $download_id, $article_ids['characteristics-of-luxury-a-visual-conversation'] );
		echo "  [linked] characteristics of luxury -> download\n";
	}

	// Article -> article: Hospitality -> Characteristics of Luxury
	if ( isset( $article_ids['hospitality-is-eating-luxurys-lunch-and-leaving-the-receipt-on-the-pillow'], $article_ids['characteristics-of-luxury-a-visual-conversation'] ) ) {
		update_field( 'art_related_articles', [
			$article_ids['characteristics-of-luxury-a-visual-conversation'],
		], $article_ids['hospitality-is-eating-luxurys-lunch-and-leaving-the-receipt-on-the-pillow'] );
		echo "  [linked] hospitality article -> characteristics of luxury\n";
	}

	// Episode -> article: The Great Hangover -> Characteristics of Luxury
	if ( isset( $episode_ids['the-great-hangover'], $article_ids['characteristics-of-luxury-a-visual-conversation'] ) ) {
		update_field( 'ep_related_articles', [ $article_ids['characteristics-of-luxury-a-visual-conversation'] ], $episode_ids['the-great-hangover'] );
		echo "  [linked] the great hangover -> characteristics of luxury\n";
	}

	// Episode -> episode: sequential linking
	if ( isset( $episode_ids['the-great-hangover'], $episode_ids['price-without-proof'] ) ) {
		update_field( 'ep_related_episodes', [ $episode_ids['price-without-proof'] ], $episode_ids['the-great-hangover'] );
		echo "  [linked] the great hangover -> price without proof\n";
	}

	// Season -> featured episode
	if ( $season_id && isset( $episode_ids['the-great-hangover'] ) ) {
		update_field( 'ps_featured_episode', $episode_ids['the-great-hangover'], $season_id );
		echo "  [linked] season 1 -> featured: the great hangover\n";
	}

	// Season -> related article: Characteristics of Luxury
	if ( $season_id && isset( $article_ids['characteristics-of-luxury-a-visual-conversation'] ) ) {
		update_field( 'ps_related_articles', [ $article_ids['characteristics-of-luxury-a-visual-conversation'] ], $season_id );
		echo "  [linked] season 1 -> characteristics of luxury\n";
	}

	// Download -> article: deck -> Characteristics of Luxury
	if ( $download_id && isset( $article_ids['characteristics-of-luxury-a-visual-conversation'] ) ) {
		update_field( 'dl_related_article', [ $article_ids['characteristics-of-luxury-a-visual-conversation'] ], $download_id );
		echo "  [linked] download -> characteristics of luxury\n";
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

	// Capabilities — V4 Panel 4: overrides template fallback descriptions
	update_field( 'home_cap_intro', '', $home_id );
	update_field( 'home_cap_1_title', 'Brand Strategy', $home_id );
	update_field( 'home_cap_1_desc', 'Positioning, audience intelligence and brand architecture for luxury brands seeking clarity in a noisy world.', $home_id );
	update_field( 'home_cap_1_url', '/what-we-do/brand-strategy', $home_id );
	update_field( 'home_cap_2_title', 'Experience Design', $home_id );
	update_field( 'home_cap_2_desc', 'Creative direction, storytelling and experience design across digital, product and environment.', $home_id );
	update_field( 'home_cap_2_url', '/what-we-do/experience-design', $home_id );
	update_field( 'home_cap_3_title', 'Digital Transformation', $home_id );
	update_field( 'home_cap_3_desc', 'Digital platforms, data systems and operational infrastructure that power modern luxury organisations.', $home_id );
	update_field( 'home_cap_3_url', '/what-we-do/digital-transformation', $home_id );

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
	$featured_article_id = $article_ids['characteristics-of-luxury-a-visual-conversation'] ?? 0;
	if ( $featured_article_id ) {
		update_field( 'home_article_item', $featured_article_id, $home_id );
		echo "  [set] home_article_item -> characteristics of luxury\n";
	}

	// Sectors — V4 Panel 9
	update_field( 'home_sectors_headline', 'Luxury Sectors', $home_id );
	update_field( 'home_sectors_body', 'Our work spans the industries shaping the modern luxury economy.', $home_id );

	echo "  [set] Homepage curated fields populated (home_*)\n";
}


// ═════════════════════════════════════════════════════════════════════════════
// 10. SERVICE PAGE CONTENT — V4 copy hardening
// Pages already exist from Batch 7; this populates post_content from V4.
// ═════════════════════════════════════════════════════════════════════════════
echo "\n-- Service Page Content (V4 copy) --\n";

// Who We Are
if ( $who_we_are_id ) {
	wp_update_post( [
		'ID'           => $who_we_are_id,
		'post_content' => '<p>Summit Communication Group exists to help luxury brands think more clearly, express themselves more distinctively and evolve more intelligently.</p>'
			. '<p>We bring together brand strategy, experience design and digital transformation in a model built for a more culturally complex, digitally fluid luxury landscape.</p>'
			. '<h2>Luxury Deserves More Than Marketing</h2>'
			. '<p>Luxury is not a category that responds well to noise, formula or generic digital thinking. It requires judgement, restraint and a deeper understanding of how value is perceived.</p>'
			. '<p>Summit was built on the belief that luxury brands need more than campaigns. They need strategic clarity, design intelligence and digital systems capable of carrying the weight of the brand.</p>'
			. '<h2>Built for Precision, Not Sprawl</h2>'
			. '<p>Summit operates as a tightly directed consultancy model, bringing together specialist capability across strategy, design, content and digital execution.</p>'
			. '<p>This structure allows us to stay focused, senior and exacting while assembling the right expertise around each engagement. The result is work that feels coherent from first principle to final touchpoint.</p>',
	] );
	echo "  [updated] who-we-are post_content\n";
}

// What We Do (parent)
if ( $what_we_do_id ) {
	wp_update_post( [
		'ID'           => $what_we_do_id,
		'post_content' => '<p>Summit Communication Group helps luxury brands define sharper positions, create more meaningful experiences and modernise the digital systems that shape relevance, desirability and growth.</p>'
			. '<p>Our work brings together strategic thinking, creative direction and digital capability to help brands move with clarity in a changing luxury landscape.</p>'
			. '<h2>Three Disciplines. One Luxury Standard.</h2>'
			. '<p>Luxury brands do not need disconnected agencies, fragmented suppliers or endless handovers. They need strategic clarity, creative coherence and digital execution working as one.</p>'
			. '<p>That is why Summit is structured around three core disciplines: brand strategy, experience design and digital transformation.</p>',
	] );
	echo "  [updated] what-we-do post_content\n";
}

// Brand Strategy (child)
if ( $brand_strategy_id ) {
	wp_update_post( [
		'ID'           => $brand_strategy_id,
		'post_content' => '<p>Summit Communication Group helps luxury brands define who they are, what they stand for and how they should be understood in an increasingly crowded, fast-moving market.</p>'
			. '<p>Our strategic work brings together positioning, audience intelligence, brand architecture and growth thinking to create the clarity that stronger brands are built on.</p>'
			. '<h2>Clarity Before Expression</h2>'
			. '<p>Too many luxury brands move straight to campaign, content or redesign before resolving the harder question underneath: what precisely should this brand mean to the people who matter most?</p>'
			. '<p>Brand strategy is the discipline that answers that question. It gives leadership teams a sharper point of view, creative teams a stronger brief and the wider business a more coherent basis for growth.</p>'
			. '<h2>Where We Create Strategic Clarity</h2>'
			. '<p>Brand strategy engagements are shaped around the questions that tend to matter most when a luxury organisation is preparing for its next chapter.</p>',
	] );
	echo "  [updated] brand-strategy post_content\n";
}

// Experience Design (child)
if ( $experience_design_id ) {
	wp_update_post( [
		'ID'           => $experience_design_id,
		'post_content' => '<p>Summit Communication Group designs how luxury brands are encountered across digital, editorial, physical and service touchpoints.</p>'
			. '<p>Our approach combines creative direction with human-centred design to create experiences that feel more coherent, more distinctive and more valuable to the people they are meant to move.</p>'
			. '<h2>Luxury Is Experienced in Sequence</h2>'
			. '<p>Luxury is not judged in a single moment. It is judged across a chain of interactions: what the brand promises, how it welcomes, how it reassures, how it guides, how it recovers, how it is remembered.</p>'
			. '<p>That is why experience design matters. It shapes the emotional and practical quality of those interactions so the brand feels intentional from first encounter to lasting recall.</p>'
			. '<h2>Human-Centred Design with a Luxury Standard</h2>'
			. '<p>Our experience design work begins with people rather than channels, then translates brand ambition into a clear experience direction teams can design, deliver and sustain.</p>'
			. '<p>For luxury brands, that work must go further. It must protect perceived value, preserve distinction and translate brand codes into moments that feel effortless rather than over-designed.</p>',
	] );
	echo "  [updated] experience-design post_content\n";
}

// Digital Transformation (child)
if ( $digital_transformation_id ) {
	wp_update_post( [
		'ID'           => $digital_transformation_id,
		'post_content' => '<p>Summit Communication Group helps luxury organisations modernise the digital systems that shape visibility, experience, efficiency and growth.</p>'
			. '<p>From platforms and content operations to CRM, analytics, search and intelligent workflows, our work ensures that elegant brands are supported by infrastructure worthy of them.</p>'
			. '<h2>Digital Should Carry the Weight of the Brand</h2>'
			. '<p>Many luxury brands still suffer from a familiar problem: the frontstage looks refined, while the backstage remains slow, fragmented or opaque.</p>'
			. '<p>Digital transformation is the work of correcting that imbalance. It aligns the systems beneath the brand with the standard the brand claims to represent, so performance, intelligence and experience improve together.</p>'
			. '<h2>Infrastructure, Intelligence and Control</h2>'
			. '<p>We see digital transformation as more than implementation. It is the redesign of how a brand operates across content, platform, data and customer intelligence.</p>'
			. '<p>For luxury organisations, that means building digital ecosystems that feel more coherent, more governable and more secure, while giving teams better visibility into performance, audience behaviour and commercial opportunity.</p>',
	] );
	echo "  [updated] digital-transformation post_content\n";
}

// Design Tomorrow (contact page)
if ( $design_tomorrow_id ) {
	wp_update_post( [
		'ID'           => $design_tomorrow_id,
		'post_content' => '<p>Whether you are building a luxury brand, refining an experience or preparing for a more ambitious digital future, Summit would be glad to begin the conversation.</p>',
	] );
	echo "  [updated] design-tomorrow post_content\n";
}


// ═════════════════════════════════════════════════════════════════════════════
// DONE
// ═════════════════════════════════════════════════════════════════════════════
update_option( 'summit_seed_v8_local_complete', true );

echo "\n======================================================\n";
echo "Batch 8 local seeding complete.\n";
echo "======================================================\n\n";
