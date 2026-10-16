<?php
/**
 * Part: Action
 * Location: parts/global/action.php
 *
 * One action in Summit's two related action languages:
 *
 *   'cta'     — primary contextual call to action (Let's Talk, Request a
 *               Private Briefing, Discuss a Programme …). Uses the compact
 *               Summit button language established on the homepage.
 *   'cta-alt' — secondary contextual call to action (outlined).
 *   'utility' — editorial publication tools (Share, LinkedIn, Email, Copy
 *               Link, Print, Download). Small type, thin line icon,
 *               no chrome.
 *
 * Usage:
 *   get_template_part( 'parts/global/action', null, [
 *       'type'  => 'utility',
 *       'mode'  => 'copy',
 *   ] );
 *
 * $args:
 *   type        'cta' | 'cta-alt' | 'utility'            (default 'utility')
 *   mode        'link' | 'external' | 'email' | 'linkedin' | 'share' |
 *               'copy' | 'print' | 'download' | 'bookmark' (default 'link')
 *   label       Visible label. Defaults per mode for utilities.
 *   url         Destination (link / external / download). Share, copy,
 *               email and LinkedIn default to the current page.
 *   title       Page title used for share / email subjects.
 *   icon        Icon key; defaults to the mode's icon for utilities.
 *               Pass false for none.
 *   download    Download filename (mode 'download').
 *   aria_label  Fuller accessible name where the label is terse.
 *   class       Extra CSS class(es).
 *
 * Share, copy and print are progressively enhanced by assets/js/actions.js,
 * which this part enqueues only when such an action is rendered.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args( $args ?? [], [
    'type'       => 'utility',
    'mode'       => 'link',
    'label'      => '',
    'url'        => '',
    'title'      => '',
    'icon'       => null,
    'download'   => '',
    'aria_label' => '',
    'class'      => '',
] );

$type = in_array( $args['type'], [ 'cta', 'cta-alt', 'utility' ], true ) ? $args['type'] : 'utility';
$mode = $args['mode'];

// Current page context for share / email / LinkedIn / copy.
$page_url   = is_singular() ? wp_get_canonical_url() : '';
$page_url   = $page_url ?: home_url( add_query_arg( [] ) );
$page_title = $args['title'] ?: ( is_singular() ? get_the_title() : wp_get_document_title() );
$page_title = html_entity_decode( wp_strip_all_tags( $page_title ), ENT_QUOTES, 'UTF-8' );

$default_labels = [
    'share'    => __( 'Share', 'summit' ),
    'linkedin' => __( 'LinkedIn', 'summit' ),
    'email'    => __( 'Email', 'summit' ),
    'copy'     => __( 'Copy Link', 'summit' ),
    'print'    => __( 'Print', 'summit' ),
    'download' => __( 'Download', 'summit' ),
    'bookmark' => __( 'Save', 'summit' ),
];
$label = $args['label'] ?: ( $default_labels[ $mode ] ?? '' );

// One optically consistent family: 24px grid, 1.25 stroke, currentColor.
$icons = [
    'share'    => '<path d="M12 3.5v11M8.5 7 12 3.5 15.5 7"/><path d="M8 10.5H6.5A1.5 1.5 0 0 0 5 12v7a1.5 1.5 0 0 0 1.5 1.5h11A1.5 1.5 0 0 0 19 19v-7a1.5 1.5 0 0 0-1.5-1.5H16"/>',
    'linkedin' => '<rect x="3.5" y="3.5" width="17" height="17" rx="2"/><path d="M8.5 10.5v6M8.5 7.6v.1M12 16.5v-6M12 13.2c0-1.6 1-2.7 2.3-2.7s2.2.9 2.2 2.6v3.4"/>',
    'email'    => '<rect x="3.5" y="5.5" width="17" height="13" rx="1.5"/><path d="m4 6.5 8 6 8-6"/>',
    'copy'     => '<path d="M10.5 13.5a3.5 3.5 0 0 0 5 0l3-3a3.5 3.5 0 0 0-5-5l-1 1"/><path d="M13.5 10.5a3.5 3.5 0 0 0-5 0l-3 3a3.5 3.5 0 0 0 5 5l1-1"/>',
    'print'    => '<path d="M7.5 9V3.5h9V9"/><rect x="3.5" y="9" width="17" height="7.5" rx="1.5"/><path d="M7.5 14h9v6.5h-9z"/>',
    'download' => '<path d="M12 3.5v11M8 10.5l4 4 4-4M5 20.5h14"/>',
    'bookmark' => '<path d="M6.5 3.5h11v17L12 16.5l-5.5 4z"/>',
];

$icon_key = ( null === $args['icon'] ) ? ( 'utility' === $type ? $mode : false ) : $args['icon'];
$icon_svg = ( $icon_key && isset( $icons[ $icon_key ] ) )
    ? '<svg class="action__icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false">' . $icons[ $icon_key ] . '</svg>'
    : '';

$classes = [ 'action', 'action--' . $type, 'action--' . $mode ];
if ( 'cta' === $type ) {
    $classes = array_merge( [ 'btn', 'btn--primary', 'btn--compact' ], $classes );
} elseif ( 'cta-alt' === $type ) {
    $classes = array_merge( [ 'btn', 'btn--secondary', 'btn--compact' ], $classes );
}
if ( $args['class'] ) {
    $classes[] = $args['class'];
}
$class_attr = implode( ' ', array_map( 'sanitize_html_class', $classes ) );

$inner = $icon_svg . '<span class="action__label">' . esc_html( $label ) . '</span>';
$aria  = $args['aria_label'] ? ' aria-label="' . esc_attr( $args['aria_label'] ) . '"' : '';

// Behaviours that need the small enhancement script.
if ( in_array( $mode, [ 'share', 'copy', 'print' ], true ) ) {
    wp_enqueue_script(
        'summit-actions',
        get_template_directory_uri() . '/assets/js/actions.js',
        [],
        wp_get_theme()->get( 'Version' ),
        true
    );
}

switch ( $mode ) {

    case 'share':
        // Web Share where available; otherwise reveals the fallback set
        // rendered beside it by action-group.php. Hidden until JS runs.
        printf(
            '<button type="button" class="%1$s" data-action="share" data-url="%2$s" data-title="%3$s"%4$s hidden>%5$s</button>',
            esc_attr( $class_attr ),
            esc_url( $page_url ),
            esc_attr( $page_title ),
            $aria, // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above
            $inner // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above
        );
        break;

    case 'copy':
        // Needs the clipboard — hidden until JS confirms it can work.
        printf(
            '<button type="button" class="%1$s" data-action="copy" data-url="%2$s"%3$s hidden>%4$s<span class="action__status visually-hidden" role="status" aria-live="polite"></span></button>',
            esc_attr( $class_attr ),
            esc_url( $args['url'] ?: $page_url ),
            $aria ?: ' aria-label="' . esc_attr__( 'Copy link to this page', 'summit' ) . '"', // phpcs:ignore WordPress.Security.EscapeOutput
            $inner // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above
        );
        break;

    case 'print':
        printf(
            '<button type="button" class="%1$s" data-action="print"%2$s hidden>%3$s</button>',
            esc_attr( $class_attr ),
            $aria ?: ' aria-label="' . esc_attr__( 'Print this page', 'summit' ) . '"', // phpcs:ignore WordPress.Security.EscapeOutput
            $inner // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above
        );
        break;

    case 'email':
        $mailto = 'mailto:?subject=' . rawurlencode( $page_title ) . '&body=' . rawurlencode( $page_title . "\n\n" . ( $args['url'] ?: $page_url ) );
        printf(
            '<a class="%1$s" href="%2$s"%3$s>%4$s</a>',
            esc_attr( $class_attr ),
            esc_url( $mailto, [ 'mailto' ] ),
            $aria ?: ' aria-label="' . esc_attr__( 'Email a link to this page', 'summit' ) . '"', // phpcs:ignore WordPress.Security.EscapeOutput
            $inner // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above
        );
        break;

    case 'linkedin':
        // Default: share the current page. Pass 'url' to link to a profile instead.
        $href = $args['url'] ?: 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode( $page_url );
        printf(
            '<a class="%1$s" href="%2$s" target="_blank" rel="noopener"%3$s>%4$s</a>',
            esc_attr( $class_attr ),
            esc_url( $href ),
            $aria ?: ' aria-label="' . esc_attr__( 'Share on LinkedIn (opens in a new tab)', 'summit' ) . '"', // phpcs:ignore WordPress.Security.EscapeOutput
            $inner // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above
        );
        break;

    case 'download':
        printf(
            '<a class="%1$s" href="%2$s" download%3$s%4$s>%5$s</a>',
            esc_attr( $class_attr ),
            esc_url( $args['url'] ),
            $args['download'] ? '="' . esc_attr( $args['download'] ) . '"' : '',
            $aria, // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above
            $inner // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above
        );
        break;

    case 'external':
        printf(
            '<a class="%1$s" href="%2$s" target="_blank" rel="noopener"%3$s>%4$s</a>',
            esc_attr( $class_attr ),
            esc_url( $args['url'] ),
            $aria ?: ' aria-label="' . esc_attr( sprintf( /* translators: %s: link label */ __( '%s (opens in a new tab)', 'summit' ), $label ) ) . '"', // phpcs:ignore WordPress.Security.EscapeOutput
            $inner // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above
        );
        break;

    case 'bookmark':
        // Supported conceptually; not wired anywhere in Phase One.
        printf(
            '<button type="button" class="%1$s" data-action="bookmark"%2$s hidden>%3$s</button>',
            esc_attr( $class_attr ),
            $aria, // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above
            $inner // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above
        );
        break;

    default: // 'link'
        printf(
            '<a class="%1$s" href="%2$s"%3$s>%4$s</a>',
            esc_attr( $class_attr ),
            esc_url( $args['url'] ),
            $aria, // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above
            $inner // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above
        );
}
