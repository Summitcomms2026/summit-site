<?php
/**
 * Header Template
 * Location: header.php
 *
 * Outputs the document <head> and the global site header, which consists of:
 *   1. Sticky top navigation (normal state) — Menu | Brand | Let’s Talk
 *   2. Mega menu overlay — parts/global/mega-menu.php
 *   3. Sticky bottom utility bar — positioning statement | CTA | WhatsApp
 *
 * The <title> tag is NOT manually written here.
 * inc/seo.php calls add_theme_support('title-tag') and WordPress outputs
 * the <title> element inside wp_head() automatically.
 *
 * Navigation labels and destinations come from inc/navigation-config.php
 * (summit_nav()); the primary CTA is summit_nav( 'cta' ). Paths are passed
 * through home_url() so they resolve in every environment.
 *
 * While the menu is open, .site-nav stays on top of the overlay and its
 * menu button becomes the close control (see assets/js/nav.js).
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$nav_cta = summit_nav( 'cta' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a href="#main" class="skip-link screen-reader-text">Skip to content</a>

<!-- ══════════════════════════════════════════════════════════════════════
     SITE HEADER
     ══════════════════════════════════════════════════════════════════════ -->
<header class="site-header" id="site-header" role="banner">

    <!-- ── Sticky top navigation — normal state ─────────────────────── -->
    <div class="site-nav" id="site-nav">
        <div class="site-nav__inner">

            <!-- Menu trigger (left) -->
            <button class="site-nav__menu-btn"
                    id="menu-btn"
                    type="button"
                    aria-controls="mega-menu"
                    aria-expanded="false"
                    aria-label="Open navigation menu">
                <span class="site-nav__menu-icon" aria-hidden="true">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
                <span class="site-nav__menu-label" aria-hidden="true">Menu</span>
            </button>

            <!-- Brand / wordmark (centre) -->
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"
               class="site-nav__brand"
               aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> — Home">
                <?php get_template_part( 'parts/global/logo' ); ?>
            </a>

            <!-- Primary CTA (right) -->
            <?php if ( $nav_cta ) : ?>
            <a href="<?php echo esc_url( summit_nav_url( $nav_cta ) ); ?>"
               class="site-nav__cta btn btn--primary"
               <?php if ( summit_nav_is_current( $nav_cta['path'], true ) ) : ?>aria-current="page"<?php endif; ?>>
                <?php echo esc_html( $nav_cta['label'] ); ?>
            </a>
            <?php endif; ?>

        </div>
    </div><!-- /.site-nav -->


    <!-- ── Mega menu overlay ────────────────────────────────────────── -->
    <!--
        Full-viewport navigation environment, built from the navigation
        config. Hidden via aria-hidden="true" + visibility:hidden in CSS;
        nav.js toggles aria-expanded on the trigger and aria-hidden on the
        overlay, and CSS opens it via .mega-menu[aria-hidden="false"]
        (opacity + visibility + pointer-events — no display toggle).
    -->
    <?php get_template_part( 'parts/global/mega-menu' ); ?>

    <!-- Overlay backdrop — click to close -->
    <div class="mega-menu-backdrop" id="mega-menu-backdrop" aria-hidden="true"></div>

</header><!-- /.site-header -->


<!-- ══════════════════════════════════════════════════════════════════════
     STICKY BOTTOM UTILITY BAR
     Lives outside .site-header so it stays fixed to viewport bottom
     independently of the header stacking context.
     ══════════════════════════════════════════════════════════════════════ -->
<div class="sticky-bottom-bar" id="sticky-bottom-bar" aria-label="Site utility bar">
    <div class="sticky-bottom-bar__inner">

        <p class="sticky-bottom-bar__positioning">
            An independent design consultancy<br>for tomorrow's luxury brands
        </p>

        <div class="sticky-bottom-bar__actions">
            <a href="<?php echo esc_url( home_url( '/design-tomorrow' ) ); ?>"
               class="sticky-bottom-bar__cta">
                Start a conversation
            </a>
            <span class="sticky-bottom-bar__separator" aria-hidden="true">/</span>
            <a href="https://wa.me/61430388662" class="sticky-bottom-bar__location">
                WhatsApp&nbsp;+61&nbsp;430&nbsp;388&nbsp;662
            </a>
        </div>

    </div>
</div><!-- /.sticky-bottom-bar -->


<!-- ══════════════════════════════════════════════════════════════════════
     PAGE CONTENT WRAPPER
     The #main anchor is targeted by the skip-link.
     ══════════════════════════════════════════════════════════════════════ -->
<div class="site-content" id="main">
