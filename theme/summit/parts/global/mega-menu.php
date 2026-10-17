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
 * Implements the approved composition study (scratch/mega-menu-study,
 * states A / B / C at 1440×900). A horizontal tree:
 *   PRIMARY (right-aligned to an axis) | BRANCH | LEAVES      RAIL
 *
 *   Primary — six section rows. Each row selects its section inside the
 *             menu (click / tap / Enter / Space); nothing opens on hover.
 *   Branch  — the section's children from the config, introduced by a
 *             vertical rule. For Solutions the branch holds the three
 *             taxonomy selectors.
 *   Leaves  — the destinations of the selected Solutions taxonomy, behind
 *             a second rule.
 *   Rail    — the live Future of Luxury articles (summit_get_menu_articles()),
 *             which stay at the far right on wide desktop.
 *
 * Each branch sits in the DOM directly after its row, and each leaf list
 * directly after its taxonomy selector, so keyboard and reader order runs
 * section → children → destinations → next section. Desktop CSS lifts
 * them into columns beside the axis; below 960px they expand in place.
 *
 * Interaction state is managed by assets/js/nav.js.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$menu_articles = summit_get_menu_articles();
$primary       = summit_nav( 'primary' ) ?: [];
$utility       = summit_nav( 'utility' ) ?: [];

// The disclosure clue: the thin text arrow from the approved study,
// attached to its label. Decorative — the whole row is the control.
$nav_arrow = '<span class="mega-menu__arrow" aria-hidden="true">&rarr;</span>';

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

            <!-- Navigation: primary tree + utility -->
            <div class="mega-menu__nav-col">

                <nav class="mega-menu__primary-nav"
                     aria-label="<?php esc_attr_e( 'Primary navigation', 'summit' ); ?>">
                    <ul class="mega-menu__primary-list" role="list">

                        <?php foreach ( $primary as $key => $section ) :
                            $groups   = $section['groups'] ?? [];
                            $children = $section['children'] ?? [];
                            $panel_id = 'mega-menu-panel-' . $key;
                            $current  = $current_attr( $section['path'] );

                            // The Future of Luxury also owns its article singles and category views.
                            if ( ! $current && ! empty( $section['editorial_rail'] )
                                && ( is_singular( 'article' ) || is_tax( 'article_category' ) ) ) {
                                $current = 'true';
                            }
                        ?>
                        <li class="mega-menu__primary-item<?php echo $current ? ' is-current' : ''; ?>"
                            data-section="<?php echo esc_attr( $key ); ?>">

                            <button class="mega-menu__primary-toggle"
                                    type="button"
                                    aria-controls="<?php echo esc_attr( $panel_id ); ?>"
                                    aria-expanded="false">
                                <span class="mega-menu__primary-label"><?php echo esc_html( $section['label'] ); ?></span>
                                <?php echo $nav_arrow; // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?>
                            </button>

                            <!-- Branch: <?php echo esc_html( $section['label'] ); ?> -->
                            <div class="mega-menu__panel<?php echo $groups ? ' has-leaves' : ''; ?>"
                                 id="<?php echo esc_attr( $panel_id ); ?>"
                                 data-panel="<?php echo esc_attr( $key ); ?>"
                                 inert>
                                <div class="mega-menu__panel-inner">
                                    <ul class="mega-menu__branch<?php echo $groups ? ' mega-menu__branch--taxonomy' : ''; ?>"
                                        role="list"
                                        aria-label="<?php echo esc_attr( $section['label'] ); ?>">

                                        <?php
                                        // The section's own page, as the Observatory's "Overview" leaf does in
                                        // the config, wherever the section does not already list itself.
                                        if ( ! in_array( $section['path'], array_column( $children, 'path' ), true ) ) : ?>
                                        <li class="mega-menu__branch-item">
                                            <a href="<?php echo esc_url( summit_nav_url( $section ) ); ?>"
                                               class="mega-menu__branch-link"
                                               <?php if ( $current_attr( $section['path'], true ) ) : ?>aria-current="page"<?php endif; ?>>
                                                <?php esc_html_e( 'Overview', 'summit' ); ?>
                                            </a>
                                        </li>
                                        <?php endif; ?>

                                        <?php foreach ( $groups as $group_key => $group ) :
                                            $leaves_id = 'mega-menu-leaves-' . $key . '-' . $group_key;
                                        ?>
                                        <li class="mega-menu__branch-item" data-group="<?php echo esc_attr( $group_key ); ?>">
                                            <button class="mega-menu__branch-toggle"
                                                    type="button"
                                                    aria-controls="<?php echo esc_attr( $leaves_id ); ?>"
                                                    aria-expanded="false">
                                                <span class="mega-menu__branch-label"><?php echo esc_html( $group['label'] ); ?></span>
                                                <?php echo $nav_arrow; // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?>
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

                                        <?php foreach ( $children as $child ) :
                                            $is_action = ! empty( $child['action'] );
                                        ?>
                                        <li class="mega-menu__branch-item<?php echo $is_action ? ' is-action' : ''; ?>">
                                            <a href="<?php echo esc_url( summit_nav_url( $child ) ); ?>"
                                               class="mega-menu__branch-link"
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


            <!-- The Future of Luxury rail — the composition's second anchor -->
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
            $thumb_id  = (int) get_post_thumbnail_id( $menu_article->ID );
            $thumb_url = $thumb_id && summit_attachment_file_exists( $thumb_id ) ? get_the_post_thumbnail_url( $menu_article->ID, 'medium' ) : '';

            if ( ! $read_time ) {
                $read_time = summit_estimated_read_time( $menu_article->ID );
            }
        ?>
        <li class="mega-menu__editorial-item">
            <a href="<?php echo esc_url( get_permalink( $menu_article->ID ) ); ?>"
               class="mega-menu__editorial-link <?php echo $thumb_id ? 'has-thumb' : 'no-thumb'; ?>">

                <?php if ( $thumb_id ) : ?>
                <span class="mega-menu__editorial-thumb" aria-hidden="true">
                    <?php if ( $thumb_url ) : ?>
                    <img src="<?php echo esc_url( $thumb_url ); ?>" alt="">
                    <?php else : ?>
                    <?php echo summit_image_fallback( $menu_article->ID ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?>
                    <?php endif; ?>
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
                <a href="<?php echo esc_url( summit_nav_url( summit_nav( 'cta' ) ) ); ?>"
                   class="mega-menu__bottom-cta">
                    Start a conversation
                </a>
                <span class="mega-menu__bottom-location">WhatsApp&nbsp;+61&nbsp;430&nbsp;388&nbsp;662</span>
            </div>
        </div>
    </div><!-- /.mega-menu__bottom-bar -->

</div><!-- /.mega-menu -->
