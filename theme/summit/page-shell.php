<?php
/**
 * Template Name: Internal Page Shell
 * Location: page-shell.php
 *
 * The reusable grammar for Summit's interior pages (Phase One, Stage F):
 *
 *   hero     eyebrow · H1 · standfirst · contextual CTA · utility actions
 *   body     the page's own content (when it has any)
 *   index    where this page sits — the section's pages, or its siblings
 *   footer   the global CTA footer (Let's Talk)
 *
 * Context comes from the navigation config (inc/navigation-config.php):
 * the section a page belongs to supplies its eyebrow, its contextual CTA
 * (a section child flagged 'action', e.g. Request a Private Briefing) and
 * its index. Nothing about the IA is restated here.
 *
 * Optional per-page fields (post meta, set in the editor's Custom Fields):
 *   summit_tone         paper (default) | dark | tinted
 *   summit_standfirst   short standfirst beneath the title
 *   summit_utilities    comma list of utility actions, e.g. "share, print"
 *
 * Styles: assets/css/page-shell.css (enqueued by this template only).
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$page_id = get_queried_object_id();
$path    = '/' . trim( (string) get_page_uri( $page_id ), '/' );

/*
 * Where does this page sit in the navigation architecture?
 * Returns [ section key, section, group label, sibling list ] or null.
 */
$context = ( static function ( string $path ): ?array {
    foreach ( summit_nav( 'primary' ) ?: [] as $key => $section ) {
        if ( $section['path'] === $path ) {
            return [ 'key' => $key, 'section' => $section, 'role' => 'parent', 'group' => '', 'siblings' => [] ];
        }
        foreach ( $section['groups'] ?? [] as $group ) {
            foreach ( $group['children'] as $child ) {
                if ( $child['path'] === $path ) {
                    return [ 'key' => $key, 'section' => $section, 'role' => 'child', 'group' => $group['label'], 'siblings' => $group['children'] ];
                }
            }
        }
        foreach ( $section['children'] ?? [] as $child ) {
            if ( $child['path'] === $path && $child['path'] !== $section['path'] ) {
                return [ 'key' => $key, 'section' => $section, 'role' => 'child', 'group' => '', 'siblings' => $section['children'] ];
            }
        }
    }
    return null;
} )( $path );

// Per-page fields
$tone       = get_post_meta( $page_id, 'summit_tone', true ) ?: 'paper';
$standfirst = get_post_meta( $page_id, 'summit_standfirst', true );
$utilities  = array_filter( array_map( 'trim', explode( ',', (string) get_post_meta( $page_id, 'summit_utilities', true ) ) ) );

// Hero actions: the section's contextual CTA (not on its own page), then utilities
$actions = [];
if ( $context ) {
    foreach ( $context['section']['children'] ?? [] as $child ) {
        if ( ! empty( $child['action'] ) && $child['path'] !== $path ) {
            $actions[] = [ 'type' => 'cta', 'label' => $child['label'], 'url' => summit_nav_url( $child ) ];
        }
    }
}
foreach ( $utilities as $mode ) {
    $actions[] = [ 'type' => 'utility', 'mode' => sanitize_key( $mode ) ];
}

// Eyebrow: the section (and its group), except where the title already says it
$eyebrow = [];
if ( $context ) {
    $eyebrow[] = $context['section']['label'];
    if ( $context['group'] ) {
        $eyebrow[] = $context['group'];
    }
}
// Pages outside the sections (utility and legal) carry the institution's name.
if ( ! $eyebrow ) {
    $eyebrow[] = get_bloginfo( 'name' );
}

// Index: a parent lists its pages; a child lists its siblings
$index_heading = '';
$index_groups  = [];
if ( $context && 'parent' === $context['role'] ) {
    if ( ! empty( $context['section']['groups'] ) ) {
        foreach ( $context['section']['groups'] as $group ) {
            $index_groups[] = [ 'label' => $group['label'], 'items' => $group['children'] ];
        }
    } else {
        // An index of related pages: never the section's own page, and never
        // an action destination the hero already offers as its CTA.
        $items = array_filter(
            $context['section']['children'] ?? [],
            static fn( $c ) => $c['path'] !== $path && empty( $c['action'] )
        );
        if ( $items ) {
            $index_groups[] = [ 'label' => '', 'items' => array_values( $items ) ];
        }
    }
    /* translators: %s: section name */
    $index_heading = sprintf( __( 'Within %s', 'summit' ), $context['section']['label'] );
} elseif ( $context ) {
    $items = array_filter( $context['siblings'], static fn( $c ) => $c['path'] !== $path && $c['path'] !== $context['section']['path'] );
    if ( $items ) {
        $index_groups[] = [ 'label' => '', 'items' => array_values( $items ) ];
    }
    $index_heading = $context['group']
        /* translators: %s: sub-section name, e.g. "By Industry" */
        ? sprintf( __( 'Also %s', 'summit' ), lcfirst( $context['group'] ) )
        /* translators: %s: section name */
        : sprintf( __( 'Also in %s', 'summit' ), $context['section']['label'] );
}

$is_lets_talk = summit_nav_is_current( summit_nav( 'cta' )['path'] ?? '', true );

// Shell styles + body classes (scoped to this template)
add_action( 'wp_enqueue_scripts', static function () {
    wp_enqueue_style(
        'summit-page-shell',
        get_template_directory_uri() . '/assets/css/page-shell.css',
        [ 'summit-navigation' ],  // after the header styles it scopes
        wp_get_theme()->get( 'Version' )
    );
} );

add_filter( 'body_class', static function ( array $classes ) use ( $tone ): array {
    $classes[] = 'summit-shell';
    if ( 'dark' === $tone ) {
        $classes[] = 'summit-shell--dark';  // header sits transparent over the dark hero
    }
    return $classes;
} );

get_header();
?>

<main class="site-main site-main--shell">
    <?php while ( have_posts() ) : the_post(); ?>

    <article <?php post_class( 'shell' ); ?>>

        <?php get_template_part( 'parts/global/page-hero', null, [
            'eyebrow'    => $eyebrow,
            'title'      => get_the_title(),
            'standfirst' => $standfirst,
            'tone'       => $tone,
            'actions'    => $actions,
        ] ); ?>

        <?php if ( trim( get_the_content() ) ) : ?>
        <section class="shell-body">
            <div class="container container--medium">
                <div class="shell-body__inner">
                    <?php the_content(); ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <?php if ( $index_groups ) : ?>
        <nav class="shell-index<?php echo count( $index_groups ) > 1 ? ' shell-index--grouped' : ''; ?>"
             aria-label="<?php echo esc_attr( $index_heading ); ?>">
            <div class="container container--medium">

                <p class="shell-index__heading" data-reveal="fade">
                    <?php if ( 'child' === ( $context['role'] ?? '' ) ) : ?>
                    <a class="shell-index__parent" href="<?php echo esc_url( summit_nav_url( $context['section'] ) ); ?>"><?php echo esc_html( $context['section']['label'] ); ?></a>
                    <span class="shell-index__sep" aria-hidden="true">/</span>
                    <?php endif; ?>
                    <span><?php echo esc_html( $index_heading ); ?></span>
                </p>

                <div class="shell-index__groups">
                    <?php foreach ( $index_groups as $g => $group ) : ?>
                    <div class="shell-index__group" data-reveal data-reveal-delay="<?php echo esc_attr( (string) min( 3, $g + 1 ) ); ?>">
                        <?php if ( $group['label'] ) : ?>
                        <p class="shell-index__group-label"><?php echo esc_html( $group['label'] ); ?></p>
                        <?php endif; ?>
                        <ul class="shell-index__list" role="list">
                            <?php foreach ( $group['items'] as $item ) : ?>
                            <li>
                                <a class="shell-index__link<?php echo ! empty( $item['action'] ) ? ' is-action' : ''; ?>"
                                   href="<?php echo esc_url( summit_nav_url( $item ) ); ?>">
                                    <span class="shell-index__label"><?php echo esc_html( $item['label'] ); ?></span>
                                    <span class="shell-index__arrow" aria-hidden="true">&rarr;</span>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </nav>
        <?php endif; ?>

    </article>

    <?php endwhile; ?>

    <?php
    // The global CTA footer (its defaults carry the canonical Let's Talk);
    // not repeated on the Let's Talk page itself.
    if ( ! $is_lets_talk ) {
        get_template_part( 'parts/global/cta-footer' );
    }
    ?>
</main>

<?php
get_footer();
