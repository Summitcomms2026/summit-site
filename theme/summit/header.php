<?php
/**
 * Theme Header
 *
 * Site-wide header with navigation.
 * Mobile: hamburger toggle reveals stacked nav.
 * Desktop: inline horizontal nav.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;
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

<header class="site-header" role="banner">
    <div class="container container--wide">
        <div class="site-header__inner">

            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"
               class="site-header__logo"
               aria-label="<?php esc_attr_e( 'Summit — Home', 'summit' ); ?>">
                Summit
            </a>

            <button class="site-header__toggle"
                    aria-expanded="false"
                    aria-controls="site-nav"
                    aria-label="<?php esc_attr_e( 'Menu', 'summit' ); ?>"
                    onclick="var n=document.getElementById('site-nav'),o=this.getAttribute('aria-expanded')==='true';this.setAttribute('aria-expanded',o?'false':'true');n.setAttribute('data-open',o?'false':'true');">
                <span class="site-header__toggle-bar"></span>
                <span class="site-header__toggle-bar"></span>
                <span class="site-header__toggle-bar"></span>
            </button>

            <nav class="site-nav" id="site-nav" data-open="false" role="navigation" aria-label="<?php esc_attr_e( 'Primary', 'summit' ); ?>">
                <a href="<?php echo esc_url( home_url( '/who-we-are/' ) ); ?>"
                   class="site-nav__link"
                   <?php if ( is_page( 'who-we-are' ) ) echo 'aria-current="page"'; ?>>
                    Who We Are
                </a>
                <a href="<?php echo esc_url( home_url( '/what-we-do/' ) ); ?>"
                   class="site-nav__link"
                   <?php if ( function_exists( 'summit_is_what_we_do_section' ) && summit_is_what_we_do_section() ) echo 'aria-current="page"'; ?>>
                    What We Do
                </a>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'case_study' ) ); ?>"
                   class="site-nav__link"
                   <?php if ( is_post_type_archive( 'case_study' ) || is_singular( 'case_study' ) ) echo 'aria-current="page"'; ?>>
                    Work
                </a>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'article' ) ); ?>"
                   class="site-nav__link"
                   <?php if ( is_post_type_archive( 'article' ) || is_singular( 'article' ) || is_tax( 'article_category' ) ) echo 'aria-current="page"'; ?>>
                    Editorial
                </a>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'podcast_season' ) ); ?>"
                   class="site-nav__link"
                   <?php if ( is_post_type_archive( 'podcast_season' ) || is_singular( 'podcast_season' ) || is_singular( 'podcast_episode' ) ) echo 'aria-current="page"'; ?>>
                    Tastemakers
                </a>
                <a href="<?php echo esc_url( home_url( '/design-tomorrow/' ) ); ?>"
                   class="site-nav__link"
                   <?php if ( is_page( 'design-tomorrow' ) ) echo 'aria-current="page"'; ?>>
                    Contact
                </a>
            </nav>

        </div>
    </div>
</header>
