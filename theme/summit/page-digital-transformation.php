<?php
/**
 * Service Page: Digital Transformation
 * Auto-used by WordPress for the page with slug: digital-transformation
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

// ── Service-specific content ────────────────────────────────────────────
$service_slug  = 'digital-transformation';
$service_name  = 'Digital Transformation';
$service_intro = 'We help luxury brands modernise the digital systems that connect them to their audiences — building platforms for growth without compromising craft.';

$capabilities = [
	'Digital Strategy & Roadmapping',
	'Website Design & E-Commerce',
	'CRM & Client Engagement',
	'Data, Analytics & Insight',
	'Platform Architecture',
	'Digital Operations & Governance',
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
			<h2 data-reveal>Performance without compromise.</h2>
			<p class="service-approach__body" data-reveal data-reveal-delay="1">We assess the full digital ecosystem — from platforms and data architecture to content workflows and commerce infrastructure — before a line of code is written. The result is transformation that respects the pace and precision luxury demands, delivering measurable capability improvements without disrupting what makes a brand exceptional.</p>
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
