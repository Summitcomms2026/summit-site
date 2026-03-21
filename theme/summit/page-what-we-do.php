<?php
/**
 * What We Do Page Template
 * Auto-used by WordPress for the page with slug: what-we-do
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

				<section class="page-content page-content--what-we-do">
					<div class="container container--medium">
						<div class="page-content__inner">
							<?php the_content(); ?>
						</div>
					</div>
				</section>

			</article>

		<?php endwhile; ?>
	<?php endif; ?>
</main>

<?php
get_footer();