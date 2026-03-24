<?php
/**
 * Default template fallback.
 *
 * WordPress requires index.php as the ultimate template fallback.
 * Most routes are handled by front-page.php or specific page/single templates.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main class="site-main">
    <div class="container">

        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <article <?php post_class(); ?>>
                    <header>
                        <h1><?php the_title(); ?></h1>
                    </header>
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <p>No content found.</p>
        <?php endif; ?>

    </div>
</main>

<?php
get_footer();
