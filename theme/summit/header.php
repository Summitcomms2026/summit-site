<?php
/**
 * Header Template
 * Location: header.php
 *
 * Outputs the document <head> and the global site header, which consists of:
 *   1. Sticky top navigation (normal state) — Menu | Brand | Design Tomorrow
 *   2. Mega menu overlay — full navigation environment, hidden until triggered
 *   3. Sticky bottom utility bar — positioning statement | CTA | London +44
 *
 * The <title> tag is NOT manually written here.
 * inc/seo.php calls add_theme_support('title-tag') and WordPress outputs
 * the <title> element inside wp_head() automatically.
 *
 * The mega menu article feed is a lightweight WP_Query for the 3 most
 * recent published articles. It is run in functions.php via a helper and
 * cached with wp_cache_get() so it does not repeat on every header call.
 *
 * Navigation links use hardcoded v1 routes as defined in the brief.
 * These are filtered through home_url() so they are always relative to
 * the site root regardless of environment (local, staging, production).
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$menu_articles = summit_get_menu_articles();
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
                <span class="site-nav__menu-label" aria-hidden="true">Menu</span>
                <span class="site-nav__menu-icon" aria-hidden="true">
                    <span></span>
                    <span></span>
                </span>
            </button>

            <!-- Brand / wordmark (centre) -->
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"
               class="site-nav__brand"
               aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> — Home">
                <?php get_template_part( 'parts/global/logo' ); ?>
            </a>

            <!-- Primary CTA (right) -->
            <a href="<?php echo esc_url( home_url( '/design-tomorrow' ) ); ?>"
               class="site-nav__cta btn btn--primary">
                Design Tomorrow
            </a>

        </div>
    </div><!-- /.site-nav -->


    <!-- ── Mega menu overlay ────────────────────────────────────────── -->
    <!--
        The mega menu is a full-viewport overlay that opens on Menu click.
        It is hidden via aria-hidden="true" and visibility:hidden in CSS.
        JavaScript toggles aria-expanded on the trigger button (for
        assistive technology) and sets aria-hidden="false" on the overlay.
        CSS opens the overlay by responding to:
            .mega-menu[aria-hidden="false"]
        via opacity + visibility + pointer-events transitions.

        This avoids any display:none toggle that would cause layout flash.
        The overlay uses visibility + opacity + pointer-events for a
        calm, lag-free reveal.
    -->
    <div class="mega-menu"
         id="mega-menu"
         role="dialog"
         aria-modal="true"
         aria-label="Navigation menu"
         aria-hidden="true">

        <!-- Preserved top navigation inside the overlay -->
        <div class="mega-menu__nav-bar">
            <div class="mega-menu__nav-bar-inner">

                <button class="mega-menu__close-btn site-nav__menu-btn site-nav__menu-btn--open"
                        id="menu-close-btn"
                        type="button"
                        aria-controls="mega-menu"
                        aria-label="Close navigation menu">
                    <span class="site-nav__menu-label" aria-hidden="true">Close</span>
                    <span class="site-nav__menu-icon site-nav__menu-icon--cross" aria-hidden="true">
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"
                   class="site-nav__brand"
                   tabindex="-1"
                   aria-hidden="true">
                    <?php get_template_part( 'parts/global/logo' ); ?>
                </a>

                <a href="<?php echo esc_url( home_url( '/design-tomorrow' ) ); ?>"
                   class="site-nav__cta btn btn--primary"
                   tabindex="-1">
                    Design Tomorrow
                </a>

            </div>
        </div><!-- /.mega-menu__nav-bar -->


        <!-- Mega menu body -->
        <div class="mega-menu__body">
            <div class="mega-menu__body-inner">

                <!-- Left column: primary + secondary navigation -->
                <div class="mega-menu__nav-col">

                    <!-- Primary navigation -->
                    <nav class="mega-menu__primary-nav"
                         aria-label="Primary navigation">
                        <ul class="mega-menu__primary-list" role="list">

                          <li class="mega-menu__primary-item <?php echo is_page( 'who-we-are' ) ? 'is-current' : ''; ?>">
                                <a href="<?php echo esc_url( home_url( '/who-we-are' ) ); ?>"
                                   class="mega-menu__primary-link"
                                   <?php echo is_page( 'who-we-are' ) ? 'aria-current="page"' : ''; ?>>
                                    Who We Are
                                </a>
                            </li>

                            <li class="mega-menu__primary-item <?php echo is_page( 'what-we-do' ) ? 'is-current' : ''; ?>">
                                <a href="<?php echo esc_url( home_url( '/what-we-do' ) ); ?>"
                                   class="mega-menu__primary-link"
                                   <?php echo is_page( 'what-we-do' ) ? 'aria-current="page"' : ''; ?>>
                                    What We Do
                                </a>
                            </li>

                            <li class="mega-menu__primary-item <?php echo is_page( 'work' ) || get_post_type() === 'case_study' ? 'is-current' : ''; ?>">
                                <a href="<?php echo esc_url( home_url( '/work' ) ); ?>"
                                   class="mega-menu__primary-link"
                                   <?php echo ( is_page( 'work' ) || get_post_type() === 'case_study' ) ? 'aria-current="page"' : ''; ?>>
                                    Selected Portfolio
                                </a>
                            </li>

                            <li class="mega-menu__primary-item <?php echo ( is_post_type_archive( 'article' ) || get_post_type() === 'article' ) ? 'is-current' : ''; ?>">
                                <a href="<?php echo esc_url( get_post_type_archive_link( 'article' ) ); ?>"
                                   class="mega-menu__primary-link"
                                   <?php echo ( is_post_type_archive( 'article' ) || get_post_type() === 'article' ) ? 'aria-current="page"' : ''; ?>>
                                    The Future of Luxury
                                </a>
                            </li>

                            <li class="mega-menu__primary-item <?php echo is_page( 'design-tomorrow' ) ? 'is-current' : ''; ?>">
                                <a href="<?php echo esc_url( home_url( '/design-tomorrow' ) ); ?>"
                                   class="mega-menu__primary-link mega-menu__primary-link--cta"
                                   <?php echo is_page( 'design-tomorrow' ) ? 'aria-current="page"' : ''; ?>>
                                    Let's Talk
                                </a>
                            </li>

                        </ul>
                    </nav><!-- /.mega-menu__primary-nav -->

                    <!-- Secondary / utility navigation -->
                    <nav class="mega-menu__secondary-nav"
                         aria-label="Secondary navigation">
                        <ul class="mega-menu__secondary-list" role="list">

                            <li>
                                <a href="<?php echo esc_url( home_url( '/careers' ) ); ?>"
                                   class="mega-menu__secondary-link"
                                   <?php echo is_page( 'careers' ) ? 'aria-current="page"' : ''; ?>>
                                    Careers
                                </a>
                            </li>

                            <li>
                                <a href="<?php echo esc_url( home_url( '/client-lounge' ) ); ?>"
                                   class="mega-menu__secondary-link"
                                   <?php echo is_page( 'client-lounge' ) ? 'aria-current="page"' : ''; ?>>
                                    Client Lounge
                                </a>
                            </li>

                            <li>
                                <a href="<?php echo esc_url( home_url( '/tastemakers' ) ); ?>"
                                   class="mega-menu__secondary-link"
                                   <?php echo ( is_page( 'tastemakers' ) || get_post_type() === 'podcast_season' || get_post_type() === 'podcast_episode' ) ? 'aria-current="page"' : ''; ?>>
                                    Tastemakers
                                </a>
                            </li>

                        </ul>
                    </nav><!-- /.mega-menu__secondary-nav -->

                </div><!-- /.mega-menu__nav-col -->


                <!-- Right column: editorial feed -->
<?php if ( ! empty( $menu_articles ) ) : ?>
<div class="mega-menu__editorial-col" aria-label="Recent articles">

    <p class="mega-menu__editorial-label">
        <a href="<?php echo esc_url( get_post_type_archive_link( 'article' ) ); ?>">
            The Future of Luxury
        </a>
    </p>

    <ul class="mega-menu__editorial-list" role="list">
        <?php foreach ( $menu_articles as $menu_article ) :
            $cats      = get_the_terms( $menu_article->ID, 'article_category' );
            $cat_name  = ( ! is_wp_error( $cats ) && ! empty( $cats ) ) ? $cats[0]->name : '';
            $read_time = get_field( 'art_read_time', $menu_article->ID );
            $pub_date  = get_the_date( 'j M Y', $menu_article->ID );
            $pub_iso   = get_the_date( 'c', $menu_article->ID );
            $thumb_url = get_the_post_thumbnail_url( $menu_article->ID, 'medium' );

            if ( ! $read_time ) {
                $read_time = summit_estimated_read_time( $menu_article->ID );
            }
        ?>
        <li class="mega-menu__editorial-item">
            <a href="<?php echo esc_url( get_permalink( $menu_article->ID ) ); ?>"
               class="mega-menu__editorial-link <?php echo $thumb_url ? 'has-thumb' : 'no-thumb'; ?>">

                <?php if ( $thumb_url ) : ?>
                <span class="mega-menu__editorial-thumb" aria-hidden="true">
                    <img src="<?php echo esc_url( $thumb_url ); ?>" alt="">
                </span>
                <?php endif; ?>

                <div class="mega-menu__editorial-meta">
                    <time datetime="<?php echo esc_attr( $pub_iso ); ?>">
                        <?php echo esc_html( $pub_date ); ?>
                    </time>
                    <span><?php echo absint( $read_time ); ?> min read</span>
                </div>

                <span class="mega-menu__editorial-title">
                    <?php echo esc_html( get_the_title( $menu_article->ID ) ); ?>
                </span>

                <?php if ( $cat_name ) : ?>
                <span class="mega-menu__editorial-cat">
                    <?php echo esc_html( $cat_name ); ?>
                </span>
                <?php endif; ?>

            </a>
        </li>
        <?php endforeach; ?>
    </ul>

    <a href="<?php echo esc_url( get_post_type_archive_link( 'article' ) ); ?>"
       class="mega-menu__editorial-all">
        All articles
    </a>

</div>
<?php endif; ?>

            </div><!-- /.mega-menu__body-inner -->
        </div><!-- /.mega-menu__body -->


        <!-- Mega menu bottom utility bar (inside overlay) -->
        <div class="mega-menu__bottom-bar">
            <div class="mega-menu__bottom-bar-inner">
                <p class="mega-menu__bottom-positioning">
                    Independent design consultancy for luxury brands.
                </p>
                <div class="mega-menu__bottom-actions">
                    <a href="<?php echo esc_url( home_url( '/design-tomorrow' ) ); ?>"
                       class="mega-menu__bottom-cta">
                        Start a Conversation
                    </a>
                    <span class="mega-menu__bottom-location">London&nbsp;+44</span>
                </div>
            </div>
        </div><!-- /.mega-menu__bottom-bar -->

    </div><!-- /.mega-menu -->

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
            Independent design consultancy for luxury brands.
        </p>

        <div class="sticky-bottom-bar__actions">
            <a href="<?php echo esc_url( home_url( '/design-tomorrow' ) ); ?>"
               class="sticky-bottom-bar__cta">
                Start a Conversation
            </a>
            <span class="sticky-bottom-bar__separator" aria-hidden="true">/</span>
            <a href="tel:+44" class="sticky-bottom-bar__location">
                London&nbsp;+44
            </a>
        </div>

    </div>
</div><!-- /.sticky-bottom-bar -->


<!-- ══════════════════════════════════════════════════════════════════════
     PAGE CONTENT WRAPPER
     The #main anchor is targeted by the skip-link.
     ══════════════════════════════════════════════════════════════════════ -->
<div class="site-content" id="main">
