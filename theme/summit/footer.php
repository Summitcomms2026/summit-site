<?php
/**
 * Theme Footer
 *
 * Site-wide footer in the approved design (panel11), carrying the
 * resolved Summit architecture. Labels and destinations come from the
 * navigation config (inc/navigation-config.php):
 *
 *   brand | primary sections | utility | Let's Talk + Subscribe
 *   ─────────────────────────────────────────────────────────────
 *   copyright                         Privacy · Terms   LinkedIn
 *
 * Legacy routes (Who We Are, What We Do, Selected Portfolio, Tastemakers,
 * Design Tomorrow) remain live but are no longer surfaced here.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$footer_primary   = summit_nav( 'primary' ) ?: [];
$footer_utility   = summit_nav( 'utility' ) ?: [];
$footer_legal     = summit_nav( 'legal' ) ?: [];
$footer_social    = summit_nav( 'social' ) ?: [];
$footer_cta       = summit_nav( 'cta' );
$footer_subscribe = summit_nav( 'subscribe' );
?>

</div><!-- /.site-content -->

<footer class="site-footer" role="contentinfo">
    <div class="container container--wide">

        <div class="site-footer__inner">

            <!-- Brand column (logo only) -->
            <div class="site-footer__brand-col">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"
                   class="site-footer__brand" aria-label="Summit — Home">
                    <?php get_template_part( 'parts/global/logo' ); ?>
                </a>
            </div>

            <!-- Primary: the six sections -->
            <nav class="site-footer__nav site-footer__nav--primary" aria-label="<?php esc_attr_e( 'Footer primary navigation', 'summit' ); ?>">
                <ul class="site-footer__nav-list" role="list">
                    <?php foreach ( $footer_primary as $section ) : ?>
                    <li><a href="<?php echo esc_url( summit_nav_url( $section ) ); ?>" class="site-footer__nav-link"><?php echo esc_html( $section['label'] ); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <!-- Utility -->
            <nav class="site-footer__nav site-footer__nav--secondary" aria-label="<?php esc_attr_e( 'Footer secondary navigation', 'summit' ); ?>">
                <ul class="site-footer__nav-list" role="list">
                    <?php foreach ( $footer_utility as $item ) : ?>
                    <li><a href="<?php echo esc_url( summit_nav_url( $item ) ); ?>" class="site-footer__nav-link"><?php echo esc_html( $item['label'] ); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <!-- Actions: the conversation, then the publication -->
            <div class="site-footer__actions-col">
                <?php if ( $footer_cta ) : ?>
                <a href="<?php echo esc_url( summit_nav_url( $footer_cta ) ); ?>"
                   class="site-footer__cta">
                    <?php echo esc_html( $footer_cta['label'] ); ?>
                </a>
                <?php endif; ?>

                <?php if ( $footer_subscribe ) : ?>
                <a href="<?php echo esc_url( summit_nav_url( $footer_subscribe ) ); ?>"
                   class="site-footer__nav-link"
                   aria-label="<?php esc_attr_e( 'Subscribe to The Future of Luxury', 'summit' ); ?>">
                    <?php echo esc_html( $footer_subscribe['label'] ); ?>
                </a>
                <?php endif; ?>
            </div>

        </div>

        <div class="site-footer__base">
            <p class="site-footer__copyright">
                &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Summit Communication Group. All Rights Reserved.
                <br>Summit&reg; is a registered trademark of Summit Communication Group.
            </p>
            <div class="site-footer__social">
                <nav aria-label="<?php esc_attr_e( 'Legal', 'summit' ); ?>">
                    <ul class="site-footer__legal-list" role="list">
                        <?php foreach ( $footer_legal as $item ) : ?>
                        <li><a href="<?php echo esc_url( summit_nav_url( $item ) ); ?>" class="site-footer__legal-link"><?php echo esc_html( $item['label'] ); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
                <?php foreach ( $footer_social as $item ) : ?>
                <a href="<?php echo esc_url( summit_nav_url( $item ) ); ?>" class="site-footer__social-icon"
                   aria-label="<?php echo esc_attr( sprintf( /* translators: %s: network name */ __( '%s (opens in a new tab)', 'summit' ), $item['label'] ) ); ?>"
                   rel="noopener" target="_blank">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" fill="currentColor"/></svg>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
