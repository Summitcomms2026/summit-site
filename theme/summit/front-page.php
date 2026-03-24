<?php
/**
 * Template: Homepage (Front Page)
 * Location: front-page.php
 *
 * WordPress template hierarchy: front-page.php takes priority when a static
 * front page is set in Settings → Reading, regardless of the page slug.
 *
 * Implements the Summit 10-panel homepage sequence:
 *   1. Hero
 *   2. Showreel
 *   3. Value Proposition
 *   4. What We Do (services triptych)
 *   5. Selected Work (curated case studies with fallback)
 *   6. Tastemakers (curated episode with fallback)
 *   7. The Characteristics of Luxury (download)
 *   8. The Future of Luxury (curated articles with fallback)
 *   9. Luxury Sectors
 *  10. Closing CTA
 *
 * ACF field group: Homepage Fields (group_summit_homepage)
 * All curated content sections use relationship/post_object fields with
 * sensible fallback queries so the homepage renders even before curation.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( have_posts() ) {
	the_post();
}

// ─────────────────────────────────────────────────────────────────────────────
// Safe ACF field helper — returns $default on any failure (missing ACF,
// unregistered field group, partial init, or internal ACF error).
// ─────────────────────────────────────────────────────────────────────────────
$summit_field = function ( string $name, $default = '', $post_id = false ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}
	try {
		$value = $post_id ? get_field( $name, $post_id ) : get_field( $name );
	} catch ( \Throwable $e ) {
		return $default;
	}
	return ( $value !== null && $value !== false && $value !== '' ) ? $value : $default;
};

// ─────────────────────────────────────────────────────────────────────────────
// Field retrieval with defaults
// ─────────────────────────────────────────────────────────────────────────────
$hero_headline = $summit_field( 'hp_hero_headline', 'Brand Strategy, Experience Design and Digital Transformation for Luxury Brands' );
$hero_subline  = $summit_field( 'hp_hero_subline' );
$hero_cta_label = $summit_field( 'hp_hero_cta_label', 'Design Tomorrow' );
$hero_cta_url   = $summit_field( 'hp_hero_cta_url', home_url( '/design-tomorrow' ) );
$hero_secondary_label = $summit_field( 'hp_hero_secondary_cta_label', 'Explore Our Work' );
$hero_secondary_url   = $summit_field( 'hp_hero_secondary_cta_url', home_url( '/work' ) );

$showreel_headline = $summit_field( 'hp_showreel_headline' );
$showreel_embed    = $summit_field( 'hp_showreel_embed' );

$value_headline = $summit_field( 'hp_value_headline' );
$value_body     = $summit_field( 'hp_value_body' );

$services_headline = $summit_field( 'hp_services_headline', 'What We Do' );
$services_intro    = $summit_field( 'hp_services_intro' );

$download_headline = $summit_field( 'hp_download_headline' );
$download_body     = $summit_field( 'hp_download_body' );
$download_asset    = $summit_field( 'hp_download_asset', null );

$sectors_headline  = $summit_field( 'hp_sectors_headline', 'Luxury Sectors' );

$cta_headline = $summit_field( 'hp_cta_headline', "Let's Design Tomorrow" );
$cta_body     = $summit_field( 'hp_cta_body' );
$cta_label    = $summit_field( 'hp_cta_label', 'Start a Conversation' );
$cta_url      = $summit_field( 'hp_cta_url', home_url( '/design-tomorrow' ) );

// ─────────────────────────────────────────────────────────────────────────────
// Curated content: Selected Work
// ACF relationship field → fallback to 4 most recent case studies
// ─────────────────────────────────────────────────────────────────────────────
$featured_cases = $summit_field( 'hp_featured_cases', null );
if ( empty( $featured_cases ) ) {
	$fallback_cases = new WP_Query( [
		'post_type'      => 'case_study',
		'posts_per_page' => 4,
		'orderby'        => [ 'menu_order' => 'ASC', 'date' => 'DESC' ],
		'no_found_rows'  => true,
	] );
	$featured_cases = $fallback_cases->posts;
	wp_reset_postdata();
}

// ─────────────────────────────────────────────────────────────────────────────
// Curated content: Tastemakers featured episode
// ACF post_object → fallback to latest episode from featured season
// ─────────────────────────────────────────────────────────────────────────────
$featured_episode = $summit_field( 'hp_featured_episode', null );
if ( ! $featured_episode ) {
	// Find the featured season
	$season_query = new WP_Query( [
		'post_type'      => 'podcast_season',
		'posts_per_page' => 1,
		'no_found_rows'  => true,
		'meta_query'     => [
			[ 'key' => 'ps_is_featured', 'value' => '1', 'compare' => '=' ],
		],
	] );
	$active_season = $season_query->have_posts() ? $season_query->posts[0] : null;
	wp_reset_postdata();

	if ( $active_season ) {
		$ep_query = new WP_Query( [
			'post_type'      => 'podcast_episode',
			'posts_per_page' => 1,
			'no_found_rows'  => true,
			'meta_query'     => [
				[ 'key' => 'ep_season', 'value' => $active_season->ID, 'compare' => '=' ],
			],
			'orderby'        => 'meta_value_num',
			'meta_key'       => 'ep_episode_number',
			'order'          => 'DESC',
		] );
		$featured_episode = $ep_query->have_posts() ? $ep_query->posts[0] : null;
		wp_reset_postdata();
	}
}

// ─────────────────────────────────────────────────────────────────────────────
// Curated content: Future of Luxury articles
// ACF relationship field → fallback to 3 most recent articles
// ─────────────────────────────────────────────────────────────────────────────
$featured_articles = $summit_field( 'hp_featured_articles', null );
if ( empty( $featured_articles ) ) {
	$fallback_articles = new WP_Query( [
		'post_type'      => 'article',
		'posts_per_page' => 3,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'no_found_rows'  => true,
	] );
	$featured_articles = $fallback_articles->posts;
	wp_reset_postdata();
}

// ─────────────────────────────────────────────────────────────────────────────
// Sector list — structural, not content-dependent
// ─────────────────────────────────────────────────────────────────────────────
// V4 doc, homepage Panel 9 — sector list
$sectors_list = [
	'Hospitality & Travel',
	'Automotive & Yachts',
	'Fashion & Leather Goods',
	'Watches & Jewellery',
	'Perfumes & Cosmetics',
	'Wines & Spirits',
	'Lifestyle Technology',
	'Fine Art & Collectibles',
	'Real Estate',
];

// Service pages — V4 doc, homepage Panel 4 descriptions
$services = [
	[
		'title' => 'Brand Strategy',
		'desc'  => 'Positioning, audience intelligence and brand architecture for luxury brands seeking clarity in a noisy world.',
		'url'   => home_url( '/what-we-do/brand-strategy' ),
		'cta'   => 'Explore Brand Strategy',
	],
	[
		'title' => 'Experience Design',
		'desc'  => 'Creative direction, storytelling and experience design across digital, product and environment.',
		'url'   => home_url( '/what-we-do/experience-design' ),
		'cta'   => 'Explore Experience Design',
	],
	[
		'title' => 'Digital Transformation',
		'desc'  => 'Digital platforms, data systems and operational infrastructure that power modern luxury organisations.',
		'url'   => home_url( '/what-we-do/digital-transformation' ),
		'cta'   => 'Explore Digital Transformation',
	],
];
?>

<main id="main" class="site-main site-main--home" role="main">


<!-- ══════════════════════════════════════════════════════════════════════
     1. HERO
     ══════════════════════════════════════════════════════════════════════ -->
<?php if ( $hero_headline ) : ?>
<section class="home-hero" aria-labelledby="home-hero-title">
	<div class="container container--wide">
		<div class="home-hero__inner">
			<h1 class="home-hero__headline" id="home-hero-title">
				<?php echo esc_html( $hero_headline ); ?>
			</h1>
			<?php if ( $hero_subline ) : ?>
			<p class="home-hero__subline">
				<?php echo esc_html( $hero_subline ); ?>
			</p>
			<?php endif; ?>
			<?php if ( $hero_cta_label && $hero_cta_url ) : ?>
			<div class="home-hero__actions">
				<a href="<?php echo esc_url( $hero_cta_url ); ?>" class="btn btn--primary home-hero__cta">
					<?php echo esc_html( $hero_cta_label ); ?>
				</a>
				<?php if ( $hero_secondary_label && $hero_secondary_url ) : ?>
				<a href="<?php echo esc_url( $hero_secondary_url ); ?>" class="btn btn--secondary home-hero__cta-secondary">
					<?php echo esc_html( $hero_secondary_label ); ?>
				</a>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>


<!-- ══════════════════════════════════════════════════════════════════════
     2. SHOWREEL
     ══════════════════════════════════════════════════════════════════════ -->
<?php if ( $showreel_embed ) : ?>
<section class="home-showreel" aria-label="Summit showreel">
	<div class="container container--wide">
		<div class="home-showreel__embed">
			<?php echo $showreel_embed; // phpcs:ignore -- trusted admin embed ?>
		</div>
	</div>
</section>
<?php endif; ?>


<!-- ══════════════════════════════════════════════════════════════════════
     3. VALUE PROPOSITION
     ══════════════════════════════════════════════════════════════════════ -->
<?php if ( $value_headline || $value_body ) : ?>
<section class="home-value" aria-labelledby="home-value-title">
	<div class="container container--medium">
		<div class="home-value__inner">
			<?php if ( $value_headline ) : ?>
			<h2 class="home-value__headline" id="home-value-title">
				<?php echo esc_html( $value_headline ); ?>
			</h2>
			<?php endif; ?>
			<?php if ( $value_body ) : ?>
			<p class="home-value__body">
				<?php echo esc_html( $value_body ); ?>
			</p>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>


<!-- ══════════════════════════════════════════════════════════════════════
     4. WHAT WE DO — SERVICES TRIPTYCH
     ══════════════════════════════════════════════════════════════════════ -->
<section class="home-services" aria-labelledby="home-services-title">
	<div class="container container--medium">
		<header class="home-services__header">
			<h2 class="home-services__headline" id="home-services-title">
				<?php echo esc_html( $services_headline ); ?>
			</h2>
			<?php if ( $services_intro ) : ?>
			<p class="home-services__intro">
				<?php echo esc_html( $services_intro ); ?>
			</p>
			<?php endif; ?>
		</header>

		<div class="home-services__grid">
			<?php foreach ( $services as $service ) : ?>
			<div class="home-services__item">
				<h3 class="home-services__item-title">
					<?php echo esc_html( $service['title'] ); ?>
				</h3>
				<p class="home-services__item-desc">
					<?php echo esc_html( $service['desc'] ); ?>
				</p>
				<a href="<?php echo esc_url( $service['url'] ); ?>" class="home-services__item-link">
					<?php echo esc_html( $service['cta'] ?? 'Learn More' ); ?> &rarr;
				</a>
			</div>
			<?php endforeach; ?>
		</div>

		<a href="<?php echo esc_url( home_url( '/what-we-do' ) ); ?>" class="btn btn--secondary home-services__cta">
			Explore What We Do
		</a>
	</div>
</section>


<!-- ══════════════════════════════════════════════════════════════════════
     5. SELECTED WORK
     ══════════════════════════════════════════════════════════════════════ -->
<?php if ( ! empty( $featured_cases ) ) : ?>
<section class="home-work" aria-labelledby="home-work-title">
	<div class="container container--medium">
		<header class="home-work__header">
			<h2 class="home-work__headline" id="home-work-title">Selected Work</h2>
		</header>

		<div class="home-work__grid">
			<?php foreach ( $featured_cases as $case ) :
				$cs_id      = is_object( $case ) ? $case->ID : $case;
				$cs_title   = get_the_title( $cs_id );
				$cs_summary = $summit_field( 'cs_summary', '', $cs_id );
				$cs_link    = get_permalink( $cs_id );
				$cs_thumb   = get_the_post_thumbnail( $cs_id, 'large', [ 'class' => 'home-work__card-img', 'loading' => 'lazy' ] );
			?>
			<article class="home-work__card">
				<?php if ( $cs_thumb ) : ?>
				<a href="<?php echo esc_url( $cs_link ); ?>" class="home-work__card-media" aria-hidden="true" tabindex="-1">
					<?php echo $cs_thumb; ?>
				</a>
				<?php endif; ?>
				<div class="home-work__card-body">
					<h3 class="home-work__card-title">
						<a href="<?php echo esc_url( $cs_link ); ?>">
							<?php echo esc_html( $cs_title ); ?>
						</a>
					</h3>
					<?php if ( $cs_summary ) : ?>
					<p class="home-work__card-summary">
						<?php echo esc_html( $cs_summary ); ?>
					</p>
					<?php endif; ?>
				</div>
			</article>
			<?php endforeach; ?>
		</div>

		<a href="<?php echo esc_url( home_url( '/work' ) ); ?>" class="btn btn--secondary home-work__cta">
			Explore Our Work
		</a>
	</div>
</section>
<?php endif; ?>


<!-- ══════════════════════════════════════════════════════════════════════
     6. TASTEMAKERS
     ══════════════════════════════════════════════════════════════════════ -->
<?php if ( $featured_episode ) :
	$ep_id          = is_object( $featured_episode ) ? $featured_episode->ID : $featured_episode;
	$ep_title       = get_the_title( $ep_id );
	$ep_deck        = $summit_field( 'ep_deck', '', $ep_id );
	$ep_guest_name  = $summit_field( 'ep_guest_name', '', $ep_id );
	$ep_guest_title = $summit_field( 'ep_guest_title', '', $ep_id );
	$ep_guest_co    = $summit_field( 'ep_guest_company', '', $ep_id );
	$ep_link        = get_permalink( $ep_id );
?>
<section class="home-tastemakers" aria-labelledby="home-tastemakers-title">
	<div class="container container--medium">
		<header class="home-tastemakers__header">
			<h2 class="home-tastemakers__headline" id="home-tastemakers-title">Tastemakers</h2>
			<!-- V4 homepage Panel 6 body -->
			<p class="home-tastemakers__intro">
				A podcast exploring the founders, designers and cultural leaders redefining luxury. Each episode examines how brand, craft, technology and culture intersect in the creation of modern luxury experiences.
			</p>
		</header>

		<div class="home-tastemakers__featured">
			<div class="home-tastemakers__episode">
				<h3 class="home-tastemakers__episode-title">
					<a href="<?php echo esc_url( $ep_link ); ?>">
						<?php echo esc_html( $ep_title ); ?>
					</a>
				</h3>
				<?php if ( $ep_deck ) : ?>
				<p class="home-tastemakers__episode-deck">
					<?php echo esc_html( $ep_deck ); ?>
				</p>
				<?php endif; ?>
				<?php if ( $ep_guest_name ) : ?>
				<p class="home-tastemakers__guest">
					<?php echo esc_html( $ep_guest_name ); ?>
					<?php if ( $ep_guest_title ) : ?>
						<span class="home-tastemakers__guest-role"><?php echo esc_html( $ep_guest_title ); ?><?php if ( $ep_guest_co ) : ?>, <?php echo esc_html( $ep_guest_co ); ?><?php endif; ?></span>
					<?php endif; ?>
				</p>
				<?php endif; ?>
				<a href="<?php echo esc_url( $ep_link ); ?>" class="btn btn--secondary">
					Listen Now
				</a>
			</div>
		</div>

		<a href="<?php echo esc_url( home_url( '/tastemakers' ) ); ?>" class="btn btn--secondary home-tastemakers__cta">
			Explore Tastemakers
		</a>
	</div>
</section>
<?php endif; ?>


<!-- ══════════════════════════════════════════════════════════════════════
     7. THE CHARACTERISTICS OF LUXURY — DOWNLOAD
     ══════════════════════════════════════════════════════════════════════ -->
<?php if ( $download_headline || $download_asset ) :
	$dl_id   = null;
	$dl_link = '';
	$dl_cta  = 'Download the Deck';
	if ( $download_asset ) {
		$dl_id   = is_object( $download_asset ) ? $download_asset->ID : $download_asset;
		$dl_link = get_permalink( $dl_id );
		$dl_cta  = $summit_field( 'dl_cta_label', $dl_cta, $dl_id );
	}
?>
<section class="home-download" aria-labelledby="home-download-title">
	<div class="container container--medium">
		<div class="home-download__inner">
			<?php if ( $download_headline ) : ?>
			<h2 class="home-download__headline" id="home-download-title">
				<?php echo esc_html( $download_headline ); ?>
			</h2>
			<?php endif; ?>
			<?php if ( $download_body ) : ?>
			<p class="home-download__body">
				<?php echo esc_html( $download_body ); ?>
			</p>
			<?php endif; ?>
			<?php if ( $dl_link ) : ?>
			<a href="<?php echo esc_url( $dl_link ); ?>" class="btn btn--primary home-download__cta">
				<?php echo esc_html( $dl_cta ); ?>
			</a>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>


<!-- ══════════════════════════════════════════════════════════════════════
     8. THE FUTURE OF LUXURY — CURATED ARTICLES
     ══════════════════════════════════════════════════════════════════════ -->
<?php if ( ! empty( $featured_articles ) ) : ?>
<section class="home-editorial" aria-labelledby="home-editorial-title">
	<div class="container container--medium">
		<header class="home-editorial__header">
			<h2 class="home-editorial__headline" id="home-editorial-title">The Future of Luxury</h2>
			<!-- V4 homepage Panel 8 body -->
			<p class="home-editorial__intro">
				Essays, interviews and commentary exploring the forces reshaping luxury brands, experiences and markets.
			</p>
		</header>

		<div class="home-editorial__grid">
			<?php foreach ( $featured_articles as $article ) :
				$art_id        = is_object( $article ) ? $article->ID : $article;
				$art_title     = get_the_title( $art_id );
				$art_standfirst = $summit_field( 'art_standfirst', '', $art_id );
				$art_link      = get_permalink( $art_id );
				$art_thumb     = get_the_post_thumbnail( $art_id, 'medium_large', [ 'class' => 'home-editorial__card-img', 'loading' => 'lazy' ] );

				// Category
				$art_cats     = wp_get_object_terms( $art_id, 'article_category' );
				$art_cat_name = ! empty( $art_cats ) && ! is_wp_error( $art_cats ) ? $art_cats[0]->name : '';
			?>
			<article class="home-editorial__card">
				<?php if ( $art_thumb ) : ?>
				<a href="<?php echo esc_url( $art_link ); ?>" class="home-editorial__card-media" aria-hidden="true" tabindex="-1">
					<?php echo $art_thumb; ?>
				</a>
				<?php endif; ?>
				<div class="home-editorial__card-body">
					<?php if ( $art_cat_name ) : ?>
					<span class="home-editorial__card-category">
						<?php echo esc_html( $art_cat_name ); ?>
					</span>
					<?php endif; ?>
					<h3 class="home-editorial__card-title">
						<a href="<?php echo esc_url( $art_link ); ?>">
							<?php echo esc_html( $art_title ); ?>
						</a>
					</h3>
					<?php if ( $art_standfirst ) : ?>
					<p class="home-editorial__card-standfirst">
						<?php echo esc_html( wp_trim_words( $art_standfirst, 25, '…' ) ); ?>
					</p>
					<?php endif; ?>
				</div>
			</article>
			<?php endforeach; ?>
		</div>

		<a href="<?php echo esc_url( home_url( '/future-of-luxury' ) ); ?>" class="btn btn--secondary home-editorial__cta">
			Read The Future of Luxury
		</a>
	</div>
</section>
<?php endif; ?>


<!-- ══════════════════════════════════════════════════════════════════════
     9. LUXURY SECTORS
     ══════════════════════════════════════════════════════════════════════ -->
<section class="home-sectors" aria-labelledby="home-sectors-title">
	<div class="container container--medium">
		<h2 class="home-sectors__headline" id="home-sectors-title">
			<?php echo esc_html( $sectors_headline ); ?>
		</h2>
		<ul class="home-sectors__list" role="list">
			<?php foreach ( $sectors_list as $sector_name ) : ?>
			<li class="home-sectors__item">
				<?php echo esc_html( $sector_name ); ?>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>


<!-- ══════════════════════════════════════════════════════════════════════
     10. CLOSING CTA
     ══════════════════════════════════════════════════════════════════════ -->
<?php
set_query_var( 'summit_cta_headline', $cta_headline );
set_query_var( 'summit_cta_sub', $cta_body );
set_query_var( 'summit_cta_label', $cta_label );
set_query_var( 'summit_cta_url', $cta_url );
get_template_part( 'parts/global/cta-footer' );
?>


</main>

<?php
get_footer();
