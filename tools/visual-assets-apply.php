<?php
/**
 * Place delivered photography by panel ID
 * Location: tools/visual-assets-apply.php (manual; never loaded by WordPress)
 *
 * Source of truth: site-content/visual-assets/manifest.json and the web
 * masters in site-content/visual-assets/media/ (see
 * editorial-handover/v1/PHOTOGRAPHY.md). For each registered asset:
 *
 *   key       a panel ID (S13-P015) — or a panel ID and a number where a
 *             panel takes several images (S01-P006-1)
 *   master    the file in media/, named <key>-<page>-<role>.jpg
 *   alt, credit, focus ("50% 40%"), status
 *
 * The master becomes (or refreshes) a WordPress attachment matched by file
 * name — never by ID — and WordPress generates its sizes. The option
 * summit_panel_visuals then tells the theme which attachment fills which
 * slot. Safe to re-run; an unchanged master is left alone.
 *
 * Usage (from the repository root):
 *
 *   php tools/visual-assets-apply.php /path/to/wp-load.php            dry run
 *   php tools/visual-assets-apply.php /path/to/wp-load.php --apply
 *
 * Local or staging only (the same guard as tools/site-content-apply.php).
 *
 * @package SummitTheme
 */

if ( PHP_SAPI !== 'cli' ) {
    exit( "CLI only.\n" );
}
$cli     = array_slice( $argv, 1 );
$wp_load = '';
foreach ( $cli as $a ) {
    if ( '--' !== substr( $a, 0, 2 ) ) {
        $wp_load = $a;
    }
}
if ( ! $wp_load || ! is_readable( $wp_load ) ) {
    fwrite( STDERR, "usage: php tools/visual-assets-apply.php /path/to/wp-load.php [--apply]\n" );
    exit( 1 );
}
$_SERVER['HTTP_HOST']   = $_SERVER['HTTP_HOST'] ?? 'summitsite.local';
$_SERVER['SERVER_NAME'] = $_SERVER['SERVER_NAME'] ?? 'summitsite.local';
$_SERVER['REQUEST_URI'] = '/';
define( 'WP_USE_THEMES', false );
require $wp_load;
require_once ABSPATH . 'wp-admin/includes/image.php';

$apply     = in_array( '--apply', $cli, true );
$site_host = (string) wp_parse_url( get_option( 'siteurl' ), PHP_URL_HOST );
if ( ! preg_match( '/(^localhost$|^127\.0\.0\.1$|\.local$|\.localhost$|\.test$)/', $site_host )
    && ! in_array( wp_get_environment_type(), [ 'local', 'development' ], true ) ) {
    fwrite( STDERR, "ABORT: $site_host is not a local or development site.\n" );
    exit( 1 );
}

$root     = dirname( __DIR__ ) . '/site-content/visual-assets';
$manifest = json_decode( (string) file_get_contents( "$root/manifest.json" ), true );
$assets   = (array) ( $manifest['assets'] ?? [] );
$option   = [];
$problems = 0;
echo ( $apply ? 'APPLY' : 'DRY RUN' ) . " — $site_host — " . count( $assets ) . " asset(s)\n";

foreach ( $assets as $key => $asset ) {
    if ( ! preg_match( '/^S\d{2}-P\d{3}(-\d+)?$/', $key ) ) {
        echo "PROBLEM   $key: not a panel ID\n";
        $problems++;
        continue;
    }
    $master = "$root/media/" . ( $asset['master'] ?? '' );
    if ( empty( $asset['master'] ) || ! is_readable( $master ) || 0 !== strpos( basename( $master ), $key . '-' ) ) {
        echo "PROBLEM   $key: master missing or not named $key-…\n";
        $problems++;
        continue;
    }
    $name = basename( $master );
    $hash = md5_file( $master );
    // an attachment already carrying this file name
    global $wpdb;
    $id = (int) $wpdb->get_var( $wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s ORDER BY post_id DESC LIMIT 1",
        '%' . $wpdb->esc_like( '/' . $name )
    ) );
    $file = $id ? (string) get_attached_file( $id ) : '';
    if ( $id && is_readable( $file ) && md5_file( $file ) === $hash ) {
        echo "ok        $key → #$id\n";
    } elseif ( $id ) {
        echo ( $apply ? 'refreshed' : 'would refresh' ) . " $key → #$id\n";
        if ( $apply ) {
            copy( $master, $file );
            wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
        }
    } else {
        echo ( $apply ? 'imported ' : 'would import' ) . " $key ($name)\n";
        if ( $apply ) {
            $uploads = wp_upload_dir();
            $dir     = trailingslashit( $uploads['basedir'] ) . 'visual-assets';
            wp_mkdir_p( $dir );
            copy( $master, "$dir/$name" );
            $id = (int) wp_insert_attachment( [
                'post_mime_type' => wp_check_filetype( $name )['type'] ?: 'image/jpeg',
                'post_title'     => $key,
                'post_status'    => 'inherit',
            ], "$dir/$name" );
            wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, "$dir/$name" ) );
        }
    }
    if ( $id ) {
        if ( $apply ) {
            update_post_meta( $id, '_wp_attachment_image_alt', (string) ( $asset['alt'] ?? '' ) );
        }
        $option[ $key ] = array_filter( [
            'id'     => $id,
            'alt'    => (string) ( $asset['alt'] ?? '' ),
            'credit' => (string) ( $asset['credit'] ?? '' ),
            'focus'  => (string) ( $asset['focus'] ?? '' ),
        ], static fn( $v ) => '' !== $v );
    }
}

if ( $apply ) {
    update_option( 'summit_panel_visuals', $option, false );
}
echo "\n" . count( $option ) . ' slot(s) ' . ( $apply ? 'registered' : 'would be registered' ) . ( $problems ? "; $problems problem(s)\n" : "\n" );
exit( $problems ? 2 : 0 );
