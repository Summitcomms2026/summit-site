<?php
/**
 * Who We Are Page Template
 * Auto-used by WordPress for the page with slug: who-we-are
 *
 * Sections:
 *   1. Hero — positioning headline, intro
 *   2. Philosophy — luxury deserves more than marketing
 *   3. Operating model — built for precision
 *   4. Why clients work with us — 3 value cards
 *   5. Selected work — recent case studies
 *   6. Page content — editor body (the_content)
 *   7. CTA footer — Design Tomorrow
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

// ── Copy ────────────────────────────────────────────────────────────────

$hero_headline = 'An Independent Consultancy for Luxury Brands';

$hero_intro = [
	'Summit Communication Group works with luxury brands that want sharper positioning, stronger creative expression and modern digital systems.',
	'We bring together strategic thinking, creative direction and digital capability — operating as a focused, specialist team rather than a traditional agency.',
];

$philosophy_heading = 'Luxury Deserves More Than Marketing';
$philosophy_body    = [
	'Luxury brands succeed when they are clear about what they stand for. Not louder, not more visible — clearer. Strategy without substance is noise. Creativity without direction is decoration.',
	'We believe the most valuable work happens when brand strategy, creative execution and digital infrastructure are considered together — not handed between separate teams with separate agendas.',
];

$model_heading = 'Built for Precision, Not Sprawl';
$model_body    = [
	'Summit operates as a consultancy, not a holding group. Every engagement is led by senior practitioners who stay involved from strategy through to delivery.',
	'We do not maintain large bench teams or layer account management between the client and the work. This model keeps thinking sharp, communication direct and outcomes accountable.',
];

$values = [
	[
		'title'       => 'Clarity Over Volume',
		'description' => 'We help brands articulate what makes them distinctive — then express that with discipline across every channel and touchpoint.',
	],
	[
		'title'       => 'Senior-Led, End to End',
		'description' => 'The people who shape the strategy are the same people who guide the creative and oversee the build. No hand-offs, no dilution.',
	],
	[
		'title'       => 'Long-Term Value',
		'description' => 'We design brand systems and digital platforms that appreciate over time — not campaigns that expire on delivery.',
	],
];
?>

<main id="main" class="site-main site-main--who-we-are" role="main">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>

			<article <?php post_class( 'page-who-we-are' ); ?>>

				<!-- ── 1. Hero ──────────────────────────────────────────────── -->
				<section class="page-hero page-hero--who-we-are">
					<div class="container container--medium">
						<header class="page-hero__header">
							<p class="page-hero__eyebrow">Who We Are</p>

							<h1 class="page-hero__title">
								<?php echo esc_html( $hero_headline ); ?>
							</h1>

							<div class="page-hero__intro">
								<?php foreach ( $hero_intro as $paragraph ) : ?>
									<p><?php echo esc_html( $paragraph ); ?></p>
								<?php endforeach; ?>
							</div>
						</header>
					</div>
				</section>

				<!-- ── 2. Philosophy ────────────────────────────────────────── -->
				<section class="wwa-section section--padded" aria-labelledby="wwa-philosophy-heading">
					<div class="container container--narrow">
						<h2 class="wwa-section__heading" id="wwa-philosophy-heading">
							<?php echo esc_html( $philosophy_heading ); ?>
						</h2>
						<div class="wwa-section__body">
							<?php foreach ( $philosophy_body as $paragraph ) : ?>
								<p><?php echo esc_html( $paragraph ); ?></p>
							<?php endforeach; ?>
						</div>
					</div>
				</section>

				<!-- ── 3. Operating model ───────────────────────────────────── -->
				<section class="wwa-section wwa-section--tinted section--padded" aria-labelledby="wwa-model-heading">
					<div class="container container--narrow">
						<h2 class="wwa-section__heading" id="wwa-model-heading">
							<?php echo esc_html( $model_heading ); ?>
						</h2>
						<div class="wwa-section__body">
							<?php foreach ( $model_body as $paragraph ) : ?>
								<p><?php echo esc_html( $paragraph ); ?></p>
							<?php endforeach; ?>
						</div>
					</div>
				</section>

				<!-- ── 4. Why clients work with us ──────────────────────────── -->
				<section class="wwa-values section--padded" aria-labelledby="wwa-values-heading">
					<div class="container container--medium">
						<h2 class="wwa-values__heading" id="wwa-values-heading">
							Why Clients Work With Us
						</h2>
						<div class="service-cards__grid">
							<?php foreach ( $values as $value ) : ?>
							<div class="service-card service-card--static">
								<h3 class="service-card__title"><?php echo esc_html( $value['title'] ); ?></h3>
								<p class="service-card__description"><?php echo esc_html( $value['description'] ); ?></p>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
				</section>

				<!-- ── 5. Selected work ─────────────────────────────────────── -->
				<?php
				$selected_cases = new WP_Query( [
					'post_type'      => 'case_study',
					'post_status'    => 'publish',
					'posts_per_page' => 3,
					'orderby'        => 'date',
					'order'          => 'DESC',
				] );

				if ( $selected_cases->have_posts() ) :
					get_template_part( 'parts/related/case-studies', null, [
						'posts'    => $selected_cases->posts,
						'headline' => 'The Thinking in Practice',
					] );
				endif;
				wp_reset_postdata();
				?>

				<!-- ── 6. Page content (optional) ───────────────────────────── -->
				<?php if ( trim( get_the_content() ) ) : ?>
				<section class="page-content page-content--who-we-are">
					<div class="container container--medium">
						<div class="page-content__inner">
							<?php the_content(); ?>
						</div>
					</div>
				</section>
				<?php endif; ?>

			</article>

		<?php endwhile; ?>
	<?php endif; ?>

	<!-- ── 7. CTA footer ────────────────────────────────────────── -->
	<?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php
get_footer();
