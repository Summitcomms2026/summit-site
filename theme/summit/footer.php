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
        <div class="site-footer__inner">
            <p class="site-footer__brand">Summit Communication Group</p>
            <p class="site-footer__legal">&copy; <?php echo esc_html( date( 'Y' ) ); ?> Summit Communication Group. All rights reserved.</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
