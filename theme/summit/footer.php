<?php
/**
 * Theme Footer
 *
 * Site-wide footer.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;
?>

<footer class="site-footer section--dark" role="contentinfo">
    <div class="container container--wide">

        <nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer', 'summit' ); ?>">
            <a href="<?php echo esc_url( home_url( '/who-we-are/' ) ); ?>" class="site-footer__nav-link">Who We Are</a>
            <a href="<?php echo esc_url( home_url( '/what-we-do/' ) ); ?>" class="site-footer__nav-link">What We Do</a>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'case_study' ) ); ?>" class="site-footer__nav-link">Work Showcase</a>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'article' ) ); ?>" class="site-footer__nav-link">The Future of Luxury</a>
            <a href="<?php echo esc_url( home_url( '/design-tomorrow/' ) ); ?>" class="site-footer__nav-link">Design Tomorrow</a>
        </nav>

        <div class="site-footer__inner">
            <p class="site-footer__brand">Summit Communication Group</p>
            <p class="site-footer__legal">&copy; <?php echo esc_html( date( 'Y' ) ); ?> Summit Communication Group. All rights reserved.</p>
        </div>

    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
