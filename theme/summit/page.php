<?php
/**
 * Default Page Template
 * Used for standard pages such as:
 * - Who We Are
 * - Careers
 * - Client Lounge
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main" class="site-main site-main--page" role="main">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>

			<article <?php post_class( 'page-template-default__article' ); ?>>

				<section class="page-hero">
					<div class="container container--medium">
						<header class="page-hero__header">
							<h1 class="page-hero__title"><?php the_title(); ?></h1>

							<?php
							$intro = function_exists( 'get_field' ) ? get_field( 'page_intro' ) : '';
							if ( $intro ) :
							?>
								<p class="page-hero__intro">
									<?php echo esc_html( $intro ); ?>
								</p>
							<?php endif; ?>
						</header>
					</div>
				</section>

				<section class="page-content">
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
