<?php
/**
 * Part: About — a portrait, or its typographic stand-in
 * Location: parts/about/portrait.php
 * Used by: page-about.php via [summit_about_portrait]
 *
 * Shows assets/img/about/<name>.jpg when the file exists. Otherwise a
 * monogram on a paper field in the same 4:5 frame, so a portrait added
 * later replaces it without any change of layout.
 *
 * $args:
 *   name      file name without extension
 *   initials  the monogram (e.g. "OD")
 *   alt       the person's name, for the image's alt text
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$name     = sanitize_file_name( $args['name'] ?? '' );
$file     = $name ? get_template_directory() . '/assets/img/about/' . $name . '.jpg' : '';
$initials = (string) ( $args['initials'] ?? '' );
$alt      = (string) ( $args['alt'] ?? '' );
?>
<?php if ( $file && is_readable( $file ) ) :
    [ $w, $h ] = (array) getimagesize( $file ) + [ 0, 0 ]; ?>
<figure class="ab-portrait">
    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/about/' . $name . '.jpg' ); ?>"
         alt="<?php echo esc_attr( $alt ); ?>"
         width="<?php echo esc_attr( (string) $w ); ?>" height="<?php echo esc_attr( (string) $h ); ?>"
         loading="lazy" decoding="async">
</figure>
<?php else : ?>
<div class="ab-portrait ab-portrait--set" aria-hidden="true">
    <span class="ab-portrait__initials"><?php echo esc_html( $initials ); ?></span>
</div>
<?php endif; ?>
