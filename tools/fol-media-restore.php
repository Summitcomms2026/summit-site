<?php
/**
 * The Future of Luxury — restore the canonical essay photographs
 * Location: tools/fol-media-restore.php (manual; never loaded by WordPress)
 *
 * Restores the tracked web masters (site-content/future-of-luxury/media/)
 * into each essay's featured image, as recorded in
 * site-content/future-of-luxury/media-manifest.json. WordPress stays the
 * runtime media system: the master becomes (or refreshes) the essay's
 * featured-image attachment and WordPress generates its sizes.
 *
 * Matching is by article slug, never by attachment ID. Safe to re-run:
 *
 *   - already correct (same file, sizes present)        → left alone
 *   - featured attachment exists, file missing/changed   → master written
 *     back into that attachment, sizes regenerated
 *   - no featured image, but an attachment with the
 *     canonical file name exists                         → re-attached
 *   - nothing to attach                                  → imported once
 *
 * Plate essays (an editorial choice, or no native source) are reported
 * and left alone: their plate appears automatically.
 *
 * Usage (from the repository root):
 *
 *   php tools/fol-media-restore.php /path/to/wp-load.php [--dry-run]
 *   wp eval-file tools/fol-media-restore.php [--dry-run]   (from the site)
 *
 * Local or staging only. Back up the media library before running it
 * against any environment that matters.
 *
 * @package SummitTheme
 */

// ── Bootstrap ────────────────────────────────────────────────────────────
$cli_args = isset( $args ) && is_array( $args ) ? $args : array_slice( $argv ?? [], 1 );
$dry_run  = in_array( '--dry-run', $cli_args, true );
if ( ! defined( 'ABSPATH' ) ) {
    $wp_load = '';
    foreach ( $cli_args as $a ) {
        if ( '--' !== substr( $a, 0, 2 ) ) {
            $wp_load = $a;
        }
    }
    if ( ! $wp_load || ! is_readable( $wp_load ) ) {
        fwrite( STDERR, "usage: php tools/fol-media-restore.php /path/to/wp-load.php [--dry-run]\n" );
        exit( 1 );
    }
    require $wp_load;
}
require_once ABSPATH . 'wp-admin/includes/image.php';

$root     = dirname( __DIR__ );
$manifest = json_decode( (string) file_get_contents( $root . '/site-content/future-of-luxury/media-manifest.json' ), true );
$media    = $root . '/site-content/future-of-luxury/media/';
if ( empty( $manifest['essays'] ) ) {
    fwrite( STDERR, "manifest missing or empty\n" );
    exit( 1 );
}

$say = static function ( string $slug, string $line ): void {
    echo str_pad( substr( $slug, 0, 58 ), 60 ) . $line . "\n";
};

// Has an attachment got its generated sizes on disk?
$sizes_ok = static function ( int $id ): bool {
    $meta = wp_get_attachment_metadata( $id );
    if ( empty( $meta['sizes'] ) ) {
        return false;
    }
    $dir = dirname( (string) get_attached_file( $id ) );
    foreach ( $meta['sizes'] as $size ) {
        if ( ! is_readable( $dir . '/' . $size['file'] ) ) {
            return false;
        }
    }
    return true;
};

// Write the master into an attachment's file and regenerate its sizes.
$refresh = static function ( int $id, string $master ) use ( $dry_run ): void {
    if ( $dry_run ) {
        return;
    }
    $target = (string) get_attached_file( $id );
    wp_mkdir_p( dirname( $target ) );
    copy( $master, $target );
    wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $target ) );
};

// An existing attachment carrying the canonical file name.
$find_by_name = static function ( string $slug ): int {
    global $wpdb;
    $like = '%' . $wpdb->esc_like( '/' . $slug ) . '%';
    $rows = $wpdb->get_col( $wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s",
        $like
    ) );
    foreach ( $rows as $id ) {
        $base = basename( (string) get_post_meta( (int) $id, '_wp_attached_file', true ) );
        if ( in_array( $base, [ $slug . '.jpg', $slug . '-scaled.jpg' ], true ) ) {
            return (int) $id;
        }
    }
    return 0;
};

$direction = function_exists( 'summit_fol_photo_direction' ) ? summit_fol_photo_direction() : null;
$counts    = [ 'ok' => 0, 'refreshed' => 0, 'reattached' => 0, 'imported' => 0, 'plate' => 0, 'problem' => 0 ];

echo $dry_run ? "Dry run — nothing will be written.\n\n" : "\n";

foreach ( $manifest['essays'] as $essay ) {
    $slug = $essay['slug'];
    $post = get_page_by_path( $slug, OBJECT, 'article' );
    if ( ! $post ) {
        $say( $slug, 'PROBLEM: no article with this slug' );
        $counts['problem']++;
        continue;
    }

    if ( 'photo' !== ( $essay['visual'] ?? '' ) ) {
        $say( $slug, 'plate (' . ( $essay['plate'] ?? 'plate' ) . ')' );
        $counts['plate']++;
        continue;
    }

    // The theme's runtime direction should agree with the manifest
    if ( null !== $direction ) {
        $d    = $direction[ $slug ] ?? null;
        $want = [ 'opener' => $essay['opener'], 'focus' => $essay['focus'], 'focus_m' => $essay['focus_mobile'] ?? $essay['focus'], 'home' => $essay['home'] ?? '' ];
        $have = $d ? [ 'opener' => $d['opener'] ?? '', 'focus' => $d['focus'] ?? '', 'focus_m' => $d['focus_m'] ?? ( $d['focus'] ?? '' ), 'home' => $d['home'] ?? '' ] : null;
        if ( $want !== $have ) {
            $say( $slug, 'NOTE: theme direction differs from the manifest (summit_fol_photo_direction())' );
        }
    }

    $master = $media . $essay['master'];
    if ( ! is_readable( $master ) ) {
        $say( $slug, 'PROBLEM: master missing — ' . $essay['master'] );
        $counts['problem']++;
        continue;
    }
    $hash  = md5_file( $master );
    $thumb = (int) get_post_thumbnail_id( $post->ID );

    if ( $thumb ) {
        $file  = (string) get_attached_file( $thumb );
        $base  = basename( $file );
        $named = in_array( $base, [ $slug . '.jpg', $slug . '-scaled.jpg' ], true );
        if ( $named && is_readable( $file ) && md5_file( $file ) === $hash && $sizes_ok( $thumb ) ) {
            $say( $slug, "ok — #$thumb already correct" );
            $counts['ok']++;
            continue;
        }
        if ( $named ) {
            $refresh( $thumb, $master );
            $say( $slug, "refreshed — master written into #$thumb, sizes regenerated" );
            $counts['refreshed']++;
            continue;
        }
    }

    $existing = $find_by_name( $slug );
    if ( $existing ) {
        $file = (string) get_attached_file( $existing );
        if ( ! is_readable( $file ) || md5_file( $file ) !== $hash || ! $sizes_ok( $existing ) ) {
            $refresh( $existing, $master );
        }
        if ( ! $dry_run ) {
            set_post_thumbnail( $post->ID, $existing );
        }
        $say( $slug, "re-attached — existing #$existing set as featured image" );
        $counts['reattached']++;
        continue;
    }

    // Import once, in the upload folder of the essay's own date
    if ( ! $dry_run ) {
        $uploads = wp_upload_dir( get_the_date( 'Y/m', $post ) );
        wp_mkdir_p( $uploads['path'] );
        $target = $uploads['path'] . '/' . $slug . '.jpg';
        copy( $master, $target );
        $id = wp_insert_attachment( [
            'post_mime_type' => 'image/jpeg',
            'post_title'     => get_the_title( $post ),
            'post_status'    => 'inherit',
        ], $target, $post->ID );
        wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $target ) );
        set_post_thumbnail( $post->ID, $id );
        $say( $slug, "imported — new attachment #$id set as featured image" );
    } else {
        $say( $slug, 'would import' );
    }
    $counts['imported']++;
}

echo "\n";
foreach ( $counts as $k => $v ) {
    echo "$k: $v\n";
}
