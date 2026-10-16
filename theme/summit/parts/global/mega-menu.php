<?php
/**
 * Part: Mega Menu
 * Location: parts/global/mega-menu.php
 * Called from: header.php
 *
 * The full-screen navigation environment. Labels and destinations come
 * from inc/navigation-config.php (summit_nav()); nothing here restates
 * the IA by hand.
 *
 * A progressive tree, read left to right on wide desktop:
 *   1. Primary   — the six destinations (always real links).
 *   2. Branch    — the active section's children, or for Solutions its
 *                  three taxonomy controls (By Industry / Function / Need).
 *   3. Leaves    — the destinations of the active Solutions taxonomy.
 *   4. Rail      — the live Future of Luxury articles, which stay put
 *                  while the tree opens to their left.
 *
 * Each branch sits in the DOM directly after its parent link, and each
 * leaf list directly after its taxonomy control, so keyboard and reader
 * order runs parent → children → destinations → next parent. Desktop CSS
 * lifts branches and leaves into their columns; below 960px they expand
 * in place.
 *
 * Interaction state is managed by assets/js/nav.js.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$menu_articles = summit_get_menu_articles();
$primary       = summit_nav( 'primary' ) ?: [];
$utility       = summit_nav( 'utility' ) ?: [];

// Sections whose single child level reads as a numbered register.
$numbered_sections = [ 'observatory' ];

/**
 * aria-current value for a config path: 'page' on the page itself,
 * 'true' anywhere beneath it, '' otherwise.
 */
$current_attr = static function ( string $path, bool $exact = false ): string {
    if ( false !== strpos( $path, '#' ) ) {
        return '';
    }
    if ( summit_nav_is_current( $path, true ) ) {
        return 'page';
    }
    return ( ! $exact && summit_nav_is_current( $path ) ) ? 'true' : '';
};
?>
<div class="mega-menu"
     id="mega-menu"
     aria-hidden="true"
     data-section="">

    <!-- Reserves the header band; the live .site-nav sits above it -->
    <div class="mega-menu__nav-bar" aria-hidden="true"></div>


    <!-- Mega menu body -->
    <div class="mega-menu__body">
        <div class="mega-menu__body-inner">

            <!-- Left column: primary + utility navigation -->
            <div class="mega-menu__nav-col">

                <nav class="mega-menu__primary-nav"
                     aria-label="<?php esc_attr_e( 'Primary navigation', 'summit' ); ?>">
                    <ul class="mega-menu__primary-list" role="list">

                        <?php foreach ( $primary as $key => $section ) :
                            $groups   = $section['groups'] ?? [];
                            $children = $section['children'] ?? [];
                            $has_tree = $groups || $children;
                            $panel_id = 'mega-menu-panel-' . $key;
                            $current  = $current_attr( $section['path'] );

                            // The Future of Luxury also owns its article singles and category views.
                            if ( ! $current && ! empty( $section['editorial_rail'] )
                                && ( is_singular( 'article' ) || is_tax( 'article_category' ) ) ) {
                                $current = 'true';
                            }
                        ?>
                        <li class="mega-menu__primary-item<?php echo $has_tree ? ' has-panel' : ''; ?><?php echo $current ? ' is-current' : ''; ?>"
                            data-section="<?php echo esc_attr( $key ); ?>">

                            <div class="mega-menu__primary-row">
                                <a href="<?php echo esc_url( summit_nav_url( $section ) ); ?>"
                                   class="mega-menu__primary-link"
                                   <?php if ( $has_tree ) : ?>aria-controls="<?php echo esc_attr( $panel_id ); ?>"<?php endif; ?>
                                   <?php if ( $current ) : ?>aria-current="<?php echo esc_attr( $current ); ?>"<?php endif; ?>>
                                    <?php echo esc_html( $section['label'] ); ?>
                                </a>

                                <?php if ( $has_tree ) : ?>
                                <button class="mega-menu__primary-toggle"
                                        type="button"
                                        aria-controls="<?php echo esc_attr( $panel_id ); ?>"
                                        aria-expanded="false">
                                    <span class="visually-hidden">
                                        <?php
                                        /* translators: %s: navigation section name */
                                        echo esc_html( sprintf( __( 'Pages in %s', 'summit' ), $section['label'] ) );
                                        ?>
                                    </span>
                                    <span class="mega-menu__toggle-icon" aria-hidden="true">
                                        <span></span>
                                        <span></span>
                                    </span>
                                </button>
                                <?php endif; ?>
                            </div>

                            <?php if ( $has_tree ) : ?>
                            <!-- Branch: <?php echo esc_html( $section['label'] ); ?> -->
                            <div class="mega-menu__panel<?php echo $groups ? ' has-leaves' : ''; ?>"
                                 id="<?php echo esc_attr( $panel_id ); ?>"
                                 data-panel="<?php echo esc_attr( $key ); ?>"
                                 inert>
                                <div class="mega-menu__panel-inner">

                                <?php if ( $groups ) : ?>
                                    <ul class="mega-menu__branch mega-menu__branch--taxonomy" role="list">
                                        <?php foreach ( $groups as $group_key => $group ) :
                                            $leaves_id = 'mega-menu-leaves-' . $key . '-' . $group_key;
                                        ?>
                                        <li class="mega-menu__branch-item" data-group="<?php echo esc_attr( $group_key ); ?>">
                                            <button class="mega-menu__branch-toggle"
                                                    type="button"
                                                    aria-controls="<?php echo esc_attr( $leaves_id ); ?>"
                                                    aria-expanded="false">
                                                <span class="mega-menu__branch-label"><?php echo esc_html( $group['label'] ); ?></span>
                                                <span class="mega-menu__branch-count" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', count( $group['children'] ) ) ); ?></span>
                                                <span class="mega-menu__toggle-icon" aria-hidden="true">
                                                    <span></span>
                                                    <span></span>
                                                </span>
                                            </button>

                                            <div class="mega-menu__leaves" id="<?php echo esc_attr( $leaves_id ); ?>" inert>
                                                <div class="mega-menu__leaves-inner">
                                                    <ul class="mega-menu__leaf-list" role="list"
                                                        aria-label="<?php echo esc_attr( $section['label'] . ' — ' . $group['label'] ); ?>">
                                                        <?php foreach ( $group['children'] as $child ) : ?>
                                                        <li>
                                                            <a href="<?php echo esc_url( summit_nav_url( $child ) ); ?>"
                                                               class="mega-menu__leaf-link"
                                                               <?php if ( $current_attr( $child['path'], true ) ) : ?>aria-current="page"<?php endif; ?>>
                                                                <?php echo esc_html( $child['label'] ); ?>
                                                            </a>
                                                        </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>

                                <?php else :
                                    $numbered = in_array( $key, $numbered_sections, true );
                                    $n        = 0;
                                ?>
                                    <ul class="mega-menu__branch<?php echo $numbered ? ' mega-menu__branch--numbered' : ''; ?>" role="list">
                                        <?php foreach ( $children as $child ) :
                                            $is_action = ! empty( $child['action'] );
                                        ?>
                                        <li class="mega-menu__branch-item<?php echo $is_action ? ' is-action' : ''; ?>">
                                            <a href="<?php echo esc_url( summit_nav_url( $child ) ); ?>"
                                               class="mega-menu__branch-link"
                                               <?php if ( $current_attr( $child['path'], true ) ) : ?>aria-current="page"<?php endif; ?>>
                                                <?php if ( $numbered && ! $is_action ) : ?>
                                                <span class="mega-menu__branch-num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', ++$n ) ); ?></span>
                                                <?php endif; ?>
                                                <span class="mega-menu__branch-label"><?php echo esc_html( $child['label'] ); ?></span>
                                            </a>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>

                                </div>
                            </div>
                            <?php endif; ?>

                        </li>
                        <?php endforeach; ?>

                    </ul>
                </nav><!-- /.mega-menu__primary-nav -->

                <!-- Utility navigation -->
                <nav class="mega-menu__secondary-nav"
                     aria-label="<?php esc_attr_e( 'Secondary navigation', 'summit' ); ?>">
                    <ul class="mega-menu__secondary-list" role="list">
                        <?php foreach ( $utility as $item ) :
                            $item_current = $current_attr( $item['path'] ?? '', true );
                        ?>
                        <li>
                            <a href="<?php echo esc_url( summit_nav_url( $item ) ); ?>"
                               class="mega-menu__secondary-link"
                               <?php if ( $item_current ) : ?>aria-current="page"<?php endif; ?>>
                                <?php echo esc_html( $item['label'] ); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </nav><!-- /.mega-menu__secondary-nav -->

            </div><!-- /.mega-menu__nav-col -->


            <!-- Right column: editorial feed — persistent; the tree opens to its left -->
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
                An independent design consultancy<br>for tomorrow's luxury brands
            </p>
            <div class="mega-menu__bottom-actions">
                <a href="<?php echo esc_url( home_url( '/design-tomorrow' ) ); ?>"
                   class="mega-menu__bottom-cta">
                    Start a conversation
                </a>
                <span class="mega-menu__bottom-location">WhatsApp&nbsp;+61&nbsp;430&nbsp;388&nbsp;662</span>
            </div>
        </div>
    </div><!-- /.mega-menu__bottom-bar -->

</div><!-- /.mega-menu -->
