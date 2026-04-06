<?php
/**
 * Part: Logo / Wordmark
 * Location: parts/global/logo.php
 *
 * Outputs the Summit wordmark with compass star icon.
 * Called from header.php (twice — nav bar and mega menu bar) and footer.php.
 *
 * Inlines the SVG via file_get_contents() so currentColor works and
 * there is no extra HTTP request. The fill is swapped from #fff to
 * currentColor at runtime so the logo adapts to light/dark contexts.
 *
 * To update the brand mark, replace:
 *   theme/summit/assets/img/summit_logo.svg
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

$logo_path = get_template_directory() . '/assets/img/summit_logo.svg';
?>
<span class="site-logo" aria-hidden="true">
<?php
if ( file_exists( $logo_path ) ) {
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo str_replace( 'fill="#fff"', 'fill="currentColor"', file_get_contents( $logo_path ) );
} else {
    // Fallback text if SVG missing
    echo '<span style="font-weight:500;letter-spacing:0.3em;font-size:13px;">SUMMIT</span>';
}
?>
</span>
