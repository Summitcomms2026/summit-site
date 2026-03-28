<?php
/**
 * 404 Template
 *
 * Branded error page with clear recovery paths.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main" class="site-main" role="main">

    <section class="error-404 section--dark section--padded" aria-labelledby="error-404-heading">
        <div class="container container--medium">
            <div class="error-404__inner text-center">

                <p class="error-404__eyebrow eyebrow">404</p>

                <h1 class="error-404__heading" id="error-404-heading">
                    Page not found
                </h1>

                <p class="error-404__body">
                    The page you requested does not exist or has been moved.
                </p>

                <nav class="error-404__nav" aria-label="<?php esc_attr_e( 'Recovery links', 'summit' ); ?>">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--primary">
                        Return Home
                    </a>
                    <a href="<?php echo esc_url( home_url( '/what-we-do/' ) ); ?>" class="btn btn--secondary">
                        What We Do
                    </a>
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'case_study' ) ); ?>" class="btn btn--secondary">
                        Work Showcase
                    </a>
                    <a href="<?php echo esc_url( home_url( '/design-tomorrow/' ) ); ?>" class="btn btn--secondary">
                        Design Tomorrow
                    </a>
                </nav>

            </div>
        </div>
    </section>

</main>

<?php
get_footer();
