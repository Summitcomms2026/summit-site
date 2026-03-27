<?php
/**
 * What We Do Page Template
 * Auto-used by WordPress for the page with slug: what-we-do
 *
 * Sections:
 *   1. Hero — positioning headline, intro, CTAs
 *   2. Service cards — 3-column grid linking to sub-pages
 *   3. Featured work — recent case studies
 *   4. Page content — editor body (the_content)
 *   5. CTA footer — Design Tomorrow
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

$design_tomorrow_url = home_url( '/design-tomorrow/' );
$work_url            = home_url( '/work/' );
?>

<main id="main" class="site-main site-main--what-we-do" role="main">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>

			<article <?php post_class( 'page-what-we-do' ); ?>>

				<!-- ── 1. Hero ──────────────────────────────────────────────── -->
				<section class="page-hero page-hero--what-we-do">
					<div class="container container--medium">
						<header class="page-hero__header">
							<p class="page-hero__eyebrow">What We Do</p>

							<h1 class="page-hero__title">
								Brand Strategy, Experience Design and Digital Transformation for Luxury Brands
							</h1>

							<div class="page-hero__intro">
								<p>
									Summit Communication Group helps luxury brands define sharper positions, create more meaningful experiences and modernise the digital systems that shape relevance, desirability and growth.
								</p>

								<p>
									Our work brings together strategic thinking, creative direction and digital capability to help brands move with clarity in a changing luxury landscape.
								</p>
							</div>

							<div class="page-hero__actions">
								<a class="btn btn--primary" href="<?php echo esc_url( $design_tomorrow_url ); ?>">
									Start a Conversation
								</a>

								<a class="btn btn--secondary" href="<?php echo esc_url( $work_url ); ?>">
									Explore Our Work
								</a>
							</div>
						</header>
					</div>
				</section>

				<!-- ── 2. Service cards ─────────────────────────────────────── -->
				<section class="service-cards" aria-label="Our services">
					<div class="container container--medium">
						<div class="service-cards__grid">

							<a href="<?php echo esc_url( home_url( '/what-we-do/brand-strategy/' ) ); ?>"
							   class="service-card">
								<h2 class="service-card__title">Brand Strategy</h2>
								<p class="service-card__description">
									Sharper positioning for luxury brands navigating change.
								</p>
								<span class="service-card__link" aria-hidden="true">Learn More</span>
							</a>

							<a href="<?php echo esc_url( home_url( '/what-we-do/experience-design/' ) ); ?>"
							   class="service-card">
								<h2 class="service-card__title">Experience Design</h2>
								<p class="service-card__description">
									Creative direction that elevates every touchpoint.
								</p>
								<span class="service-card__link" aria-hidden="true">Learn More</span>
							</a>

							<a href="<?php echo esc_url( home_url( '/what-we-do/digital-transformation/' ) ); ?>"
							   class="service-card">
								<h2 class="service-card__title">Digital Transformation</h2>
								<p class="service-card__description">
									Modern systems for relevance, desirability and growth.
								</p>
								<span class="service-card__link" aria-hidden="true">Learn More</span>
							</a>

						</div>
					</div>
				</section>

				<!-- ── 3. Featured work ─────────────────────────────────────── -->
				<?php
				$featured_cases = new WP_Query( [
					'post_type'      => 'case_study',
					'post_status'    => 'publish',
					'posts_per_page' => 3,
					'orderby'        => 'date',
					'order'          => 'DESC',
				] );

				if ( $featured_cases->have_posts() ) :
					get_template_part( 'parts/related/case-studies', null, [
						'posts'    => $featured_cases->posts,
						'headline' => 'Selected Work',
					] );
				endif;
				wp_reset_postdata();
				?>

				<!-- ── 4. Page content ──────────────────────────────────────── -->
				<?php if ( trim( get_the_content() ) ) : ?>
				<section class="page-content page-content--what-we-do">
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

	<!-- ── 5. CTA footer ────────────────────────────────────────── -->
	<?php get_template_part( 'parts/global/cta-footer' ); ?>

</main>

<?php
get_footer();
