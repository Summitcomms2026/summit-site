<?php
/**
 * Service Page: Experience Design
 * Auto-used by WordPress for the page with slug: experience-design
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

// ── Service-specific content ────────────────────────────────────────────
$service_slug  = 'experience-design';
$service_name  = 'Experience Design';
$service_intro = 'We shape how luxury brands feel at every touchpoint — translating strategic intent into creative experiences that reward attention.';

$capabilities = [
	'Creative Direction',
	'Visual Identity & Design Systems',
	'Packaging & Collateral',
	'Retail & Hospitality Environments',
	'Event & Experiential Design',
	'Content Strategy & Art Direction',
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

			<nav class="service-hero__breadcrumb" aria-label="Service navigation">
				<a href="<?php echo esc_url( home_url( '/what-we-do/' ) ); ?>">What We Do</a>
				<span aria-hidden="true">/</span>
				<span aria-current="page"><?php echo esc_html( $service_name ); ?></span>
			</nav>

			<header class="service-hero__header">
				<h1 class="service-hero__title" id="service-hero-title">
					<?php echo esc_html( $service_name ); ?>
				</h1>
				<p class="service-hero__intro">
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
				<?php foreach ( $capabilities as $cap ) : ?>
				<li class="service-capabilities__item"><?php echo esc_html( $cap ); ?></li>
				<?php endforeach; ?>
			</ul>
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
