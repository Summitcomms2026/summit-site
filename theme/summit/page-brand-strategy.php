<?php
/**
 * Service Page: Brand Strategy
 * Auto-used by WordPress for the page with slug: brand-strategy
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

// ── Service-specific content ────────────────────────────────────────────
$service_slug  = 'brand-strategy';
$service_name  = 'Brand Strategy';
$service_intro = 'Summit helps luxury brands define where they stand and why it matters — building strategic foundations for enduring relevance.';

$capabilities = [
	'Positioning & Brand Platform',
	'Brand Architecture',
	'Competitive & Market Analysis',
	'Naming & Verbal Identity',
	'Luxury Market Entry Strategy',
	'Brand Governance & Guidelines',
];

// ── Related case studies: by service_type, fallback to recent ───────────
$service_cases = new WP_Query( [
	'post_type'      => 'case_study',
	'post_status'    => 'publish',
	'posts_per_page' => 3,
	'tax_query'      => [ [
		'taxonomy' => 'service_type',
		'field'    => 'slug',
		'terms'    => $service_slug,
	] ],
] );

// Fallback: if no case studies tagged with this service type, show recent.
if ( ! $service_cases->have_posts() ) {
	$service_cases = new WP_Query( [
		'post_type'      => 'case_study',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'orderby'        => 'date',
		'order'          => 'DESC',
	] );
}

// ── Related articles: recent ────────────────────────────────────────────
$service_articles = new WP_Query( [
	'post_type'      => 'article',
	'post_status'    => 'publish',
	'posts_per_page' => 3,
	'orderby'        => 'date',
	'order'          => 'DESC',
] );
?>

<main id="main" class="site-main site-main--service" role="main">

	<!-- ── Hero ────────────────────────────────────────────────────────── -->
	<section class="service-hero" aria-labelledby="service-hero-title">
		<div class="container container--medium">

			<nav class="service-hero__breadcrumb" aria-label="Service navigation" data-reveal="fade">
				<a href="<?php echo esc_url( home_url( '/what-we-do/' ) ); ?>">What We Do</a>
				<span aria-hidden="true">/</span>
				<span aria-current="page"><?php echo esc_html( $service_name ); ?></span>
			</nav>

			<header class="service-hero__header">
				<h1 class="service-hero__title" id="service-hero-title" data-reveal>
					<?php echo esc_html( $service_name ); ?>
				</h1>
				<p class="service-hero__intro" data-reveal data-reveal-delay="1">
					<?php echo esc_html( $service_intro ); ?>
				</p>
			</header>

		</div>
	</section>

	<!-- ── Capabilities ────────────────────────────────────────────────── -->
	<section class="service-capabilities" aria-label="Capabilities">
		<div class="container container--narrow">
			<h2 class="service-capabilities__heading">What this includes</h2>
			<ul class="service-capabilities__list">
				<?php foreach ( $capabilities as $cap_i => $cap ) : ?>
				<li class="service-capabilities__item"<?php if ( $cap_i === 0 ) echo ' data-reveal data-reveal-delay="1"'; elseif ( $cap_i === 1 ) echo ' data-reveal data-reveal-delay="2"'; elseif ( $cap_i === 2 ) echo ' data-reveal data-reveal-delay="3"'; ?>><?php echo esc_html( $cap ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<!-- ── Our Approach ────────────────────────────────────────────────── -->
	<section class="service-approach section--padded section--tinted" aria-label="Our Approach">
		<div class="container container--medium">
			<p class="eyebrow" data-reveal="fade">Methodology</p>
			<h2 data-reveal>Clarity before execution.</h2>
			<p class="service-approach__body" data-reveal data-reveal-delay="1">We begin every brand strategy engagement with deep discovery — mapping the competitive landscape, understanding the brand's authentic territory, and aligning stakeholders on a shared strategic vision. The result is a platform that is both creatively inspiring and commercially actionable.</p>
		</div>
	</section>

	<!-- ── Related case studies ─────────────────────────────────────────── -->
	<?php if ( $service_cases->have_posts() ) :
		get_template_part( 'parts/related/case-studies', null, [
			'posts'    => $service_cases->posts,
			'headline' => 'Related Work',
		] );
	endif;
	wp_reset_postdata();
	?>

	<!-- ── Related articles ─────────────────────────────────────────────── -->
	<?php if ( $service_articles->have_posts() ) :
		get_template_part( 'parts/related/articles', null, [
			'posts'    => $service_articles->posts,
			'headline' => 'Related Thinking',
		] );
	endif;
	wp_reset_postdata();
	?>

	<!-- ── CTA footer ───────────────────────────────────────────────────── -->
	<?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php
get_footer();
