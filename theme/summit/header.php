<?php
/**
 * Theme Header
 *
 * Site-wide header with navigation.
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

            <nav class="site-nav" role="navigation" aria-label="<?php esc_attr_e( 'Primary', 'summit' ); ?>">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'case_study' ) ); ?>"
                   class="site-nav__link"
                   <?php if ( is_post_type_archive( 'case_study' ) || is_singular( 'case_study' ) ) echo 'aria-current="page"'; ?>>
                    Work
                </a>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'article' ) ); ?>"
                   class="site-nav__link"
                   <?php if ( is_post_type_archive( 'article' ) || is_singular( 'article' ) ) echo 'aria-current="page"'; ?>>
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
