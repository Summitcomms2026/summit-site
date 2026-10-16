<?php
/**
 * Part: Research — artwork, or its typographic stand-in
 * Location: parts/research/artwork.php
 * Used by: page-research.php via [summit_research_artwork]
 *
 * Shows a work of art only when its approved file is present; otherwise a
 * designed stand-in in the same frame, so the page never shows a broken
 * image and replacing the stand-in is a matter of adding the file.
 *
 *   assets/img/research/<name>.jpg              approved, published with the theme
 *   assets/img/research/provisional/<name>.jpg  development only (git-ignored),
 *                                               and shown only where the
 *                                               environment type is 'local'
 *
 * The credit is printed only when the artwork itself is shown.
 *
 * $args:
 *   name      file name without extension
 *   kind      'cover' (typographic title page) | 'plate' (paper field)
 *   alt       alt text for the artwork
 *   credit    caption/credit, shown with the artwork only
 *   title, subtitle, byline   the typographic title page (kind 'cover')
 *   line      the paper field's line (kind 'plate')
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$a = wp_parse_args( $args ?? [], [
    'name' => '', 'kind' => 'cover', 'alt' => '', 'credit' => '',
    'title' => '', 'subtitle' => '', 'byline' => '', 'line' => '',
] );

$dir   = get_template_directory() . '/assets/img/research/';
$uri   = get_template_directory_uri() . '/assets/img/research/';
$name  = sanitize_file_name( $a['name'] );
$file  = '';
$state = '';
$sources = [ '' => 'approved' ];
if ( 'local' === wp_get_environment_type() ) {
    $sources['provisional/'] = 'provisional';
}
foreach ( $sources as $sub => $label ) {
    if ( $name && is_readable( $dir . $sub . $name . '.jpg' ) ) {
        $file  = $uri . $sub . $name . '.jpg';
        $state = $label;
        break;
    }
}
$kind = 'plate' === $a['kind'] ? 'plate' : 'cover';
?>
<?php if ( $file ) : ?>
<figure class="res-art res-art--<?php echo esc_attr( $kind ); ?>" data-asset="<?php echo esc_attr( $state ); ?>">
    <img src="<?php echo esc_url( $file ); ?>" alt="<?php echo esc_attr( $a['alt'] ); ?>" loading="lazy" decoding="async">
    <?php if ( $a['credit'] ) : ?>
    <figcaption><?php echo esc_html( $a['credit'] ); ?></figcaption>
    <?php endif; ?>
</figure>
<?php elseif ( 'cover' === $kind ) : ?>
<div class="res-art res-art--cover res-art--set" role="img" aria-label="<?php echo esc_attr( trim( $a['title'] . ': ' . $a['subtitle'] . ', ' . $a['byline'], ' :,' ) ); ?>">
    <span class="res-art__title"><?php echo esc_html( $a['title'] ); ?></span>
    <span class="res-art__rule" aria-hidden="true"></span>
    <span class="res-art__subtitle"><?php echo esc_html( $a['subtitle'] ); ?></span>
    <span class="res-art__byline"><?php echo esc_html( $a['byline'] ); ?></span>
</div>
<?php else : ?>
<div class="res-art res-art--plate res-art--set" aria-hidden="true">
    <span class="res-art__line"><?php echo esc_html( $a['line'] ); ?></span>
</div>
<?php endif; ?>
