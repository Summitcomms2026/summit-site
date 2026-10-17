<?php
/**
 * Apply the tracked page content to a WordPress site
 * Location: tools/site-content-apply.php (manual; never loaded by WordPress)
 *
 * Source of truth: site-content/<section>/manifest.json and its
 * *.blocks.html files (built from the editorial handover by
 * tools/editorial-build.py). WordPress stays the runtime editor; this puts
 * the recorded content back when a page needs recovering or a new build is
 * ready. It never runs on a page request.
 *
 * Pages are matched by path, never by ID, and never created here. For each
 * page in the manifest that exists at its path:
 *
 *   post_content        the page's source file (entries without a source
 *                       set fields only)
 *   _wp_page_template   the manifest template
 *   meta                each manifest meta key; hero keys the manifest no
 *                       longer gives are removed, so a page never keeps a
 *                       stale CTA
 *
 * Before anything is written the page's previous content, template and
 * fields are saved as JSON (default: scratch/site-content-backups/).
 * After writing, the stored content is compared with the source and every
 * block is parsed: an empty block name or a mismatch is reported.
 *
 * Usage (from the repository root):
 *
 *   php tools/site-content-apply.php /path/to/wp-load.php                    dry run, every section
 *   php tools/site-content-apply.php /path/to/wp-load.php --section=solutions [/solutions/applied-ai …]
 *   php tools/site-content-apply.php /path/to/wp-load.php --apply [--section=…] [path …]
 *   php tools/site-content-apply.php … --backup-dir=/somewhere/else
 *
 * Local or staging only: it refuses a site whose address is not a local
 * development host (.local, .test, localhost) unless WP_ENVIRONMENT_TYPE is
 * 'local' or 'development'.
 *
 * @package SummitTheme
 */

if ( PHP_SAPI !== 'cli' ) {
    exit( "CLI only.\n" );
}
$cli = array_slice( $argv, 1 );
$wp_load = '';
foreach ( $cli as $a ) {
    if ( '--' !== substr( $a, 0, 2 ) && '/' === substr( $a, 0, 1 ) && '.php' === substr( $a, -4 ) ) {
        $wp_load = $a;
        break;
    }
}
if ( ! $wp_load || ! is_readable( $wp_load ) ) {
    fwrite( STDERR, "usage: php tools/site-content-apply.php /path/to/wp-load.php [--apply] [--section=name] [path …]\n" );
    exit( 1 );
}
$host = 'summitsite.local';
$_SERVER['HTTP_HOST']   = $_SERVER['HTTP_HOST'] ?? $host;
$_SERVER['SERVER_NAME'] = $_SERVER['SERVER_NAME'] ?? $host;
$_SERVER['REQUEST_URI'] = '/';
define( 'WP_USE_THEMES', false );
require $wp_load;

$apply    = in_array( '--apply', $cli, true );
$sections = [];
$backups  = dirname( __DIR__ ) . '/scratch/site-content-backups';
foreach ( $cli as $a ) {
    if ( 0 === strpos( $a, '--section=' ) ) {
        $sections[] = preg_replace( '/[^a-z0-9-]/', '', substr( $a, 10 ) );
    } elseif ( 0 === strpos( $a, '--backup-dir=' ) ) {
        $backups = rtrim( substr( $a, 13 ), '/' );
    }
}
$only = array_values( array_filter( $cli, static fn( $a ) => '/' === substr( $a, 0, 1 ) && $a !== $wp_load ) );

// ── Guard: development sites only ────────────────────────────────────────
$site_host = (string) wp_parse_url( get_option( 'siteurl' ), PHP_URL_HOST );
$is_dev    = (bool) preg_match( '/(^localhost$|^127\.0\.0\.1$|\.local$|\.localhost$|\.test$)/', $site_host )
    || in_array( wp_get_environment_type(), [ 'local', 'development' ], true );
if ( ! $is_dev ) {
    fwrite( STDERR, "ABORT: $site_host is not a local or development site.\n" );
    exit( 1 );
}

$root = dirname( __DIR__ ) . '/site-content';
if ( ! $sections ) {
    foreach ( glob( "$root/*/manifest.json" ) as $m ) {
        $sections[] = basename( dirname( $m ) );
    }
}

// Hero fields the build writes: removed when a manifest no longer gives them.
$managed = [ 'summit_eyebrow', 'summit_headline', 'summit_standfirst', 'summit_intro',
    'summit_cta_label', 'summit_cta_url', 'summit_secondary_label', 'summit_secondary_url' ];

echo ( $apply ? 'APPLY' : 'DRY RUN' ) . " — $site_host\n";
kses_remove_filters();
$problems = 0;

foreach ( $sections as $section ) {
    $src      = "$root/$section";
    $manifest = json_decode( (string) @file_get_contents( "$src/manifest.json" ), true );
    if ( empty( $manifest['pages'] ) ) {
        if ( ! isset( $manifest['assets'] ) ) {      // visual-assets/ has its own tool
            fwrite( STDERR, "skip $section: manifest missing or empty\n" );
        }
        continue;
    }
    echo "\n[$section]\n";
    foreach ( $manifest['pages'] as $entry ) {
        if ( $only && ! in_array( $entry['path'], $only, true ) ) {
            continue;
        }
        if ( empty( $entry['meta'] ) && empty( $entry['source'] ) ) {
            continue;
        }
        $page = get_page_by_path( trim( $entry['path'], '/' ), OBJECT, 'page' );
        if ( ! $page ) {
            printf( "MISSING   %s (pages are not created here)\n", $entry['path'] );
            $problems++;
            continue;
        }
        $meta      = $entry['meta'] ?? [];
        $has_src   = ! empty( $entry['source'] );
        $content   = $has_src ? (string) file_get_contents( "$src/{$entry['source']}" ) : $page->post_content;
        $parsed    = parse_blocks( $content );
        $blocks    = count( array_filter( $parsed, static fn( $b ) => $b['blockName'] ) );
        $stray     = count( array_filter( $parsed, static fn( $b ) => ! $b['blockName'] && trim( $b['innerHTML'] ) ) );
        $drop      = $has_src || isset( $meta['summit_headline'] )
            ? array_values( array_filter( $managed, static fn( $k ) => ! array_key_exists( $k, $meta ) && '' !== get_post_meta( $page->ID, $k, true ) ) )
            : [];
        $note      = $stray ? " · $stray stray HTML" : '';
        $problems += $stray ? 1 : 0;

        if ( ! $apply ) {
            printf( "would set %-52s %3d blocks%s%s%s\n", $entry['path'], $blocks,
                $has_src ? ( $content === $page->post_content ? ' · content unchanged' : ' · content differs' ) : ' · fields only',
                $drop ? ' · remove ' . implode( ', ', $drop ) : '', $note );
            continue;
        }

        wp_mkdir_p( $backups );
        $backup = [
            'path'         => $entry['path'],
            'post_content' => $page->post_content,
            'template'     => get_post_meta( $page->ID, '_wp_page_template', true ),
            'meta'         => [],
        ];
        foreach ( array_unique( array_merge( array_keys( $meta ), $drop ) ) as $k ) {
            $backup['meta'][ $k ] = get_post_meta( $page->ID, $k, true );
        }
        file_put_contents(
            sprintf( '%s/%s-%s-%s.json', $backups, $section, $page->post_name, gmdate( 'Ymd-His' ) ),
            json_encode( $backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
        );

        if ( $has_src && $content !== $page->post_content ) {
            $id = wp_update_post( [ 'ID' => $page->ID, 'post_content' => wp_slash( $content ) ], true );
            if ( is_wp_error( $id ) ) {
                printf( "ERROR     %s %s\n", $entry['path'], $id->get_error_message() );
                $problems++;
                continue;
            }
        }
        if ( ! empty( $entry['template'] ) ) {
            update_post_meta( $page->ID, '_wp_page_template', $entry['template'] );
        }
        foreach ( $meta as $k => $v ) {
            update_post_meta( $page->ID, $k, wp_slash( $v ) );
        }
        foreach ( $drop as $k ) {
            delete_post_meta( $page->ID, $k );
        }
        $saved = get_post_field( 'post_content', $page->ID, 'raw' );
        $same  = ! $has_src || $saved === $content;
        $problems += $same ? 0 : 1;
        printf( "updated   %-52s %3d blocks · %s%s%s\n", $entry['path'], $blocks,
            $has_src ? 'content ' . ( $same ? 'identical' : 'DIFFERS' ) : 'fields only',
            $drop ? ' · removed ' . implode( ', ', $drop ) : '', $note );
    }
}

echo "\n" . ( $problems ? "$problems problem(s)\n" : "no problems\n" );
exit( $problems ? 2 : 0 );
