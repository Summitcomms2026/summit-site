<?php
/**
 * Part: Logo / Wordmark
 * Location: parts/global/logo.php
 *
 * Outputs the Summit wordmark. Called from header.php (twice — nav bar
 * and mega menu bar) and footer.php.
 *
 * V1.0: renders as an SVG text wordmark using the theme's font stack.
 * Replace with a proper SVG asset once the final brand mark is supplied.
 *
 * The SVG uses currentColor so it inherits the parent element's text
 * colour automatically — works on both light and dark backgrounds without
 * duplication.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;
?>
<span class="site-logo" aria-hidden="true">
    <svg class="site-logo__svg"
         viewBox="0 0 120 20"
         xmlns="http://www.w3.org/2000/svg"
         role="img"
         aria-label="Summit">
        <text x="0"
              y="16"
              font-family="-apple-system, 'SF Pro Display', 'Segoe UI', system-ui, sans-serif"
              font-size="14"
              font-weight="400"
              letter-spacing="0.12"
              fill="currentColor">Summit</text>
    </svg>
</span>
<?php
/*
 * To replace this SVG wordmark with a proper asset:
 *   1. Add the SVG file to assets/images/summit-wordmark.svg
 *   2. Replace the <span>/<svg> above with:
 *
 *   $logo_path = get_template_directory() . '/assets/images/summit-wordmark.svg';
 *   if ( file_exists( $logo_path ) ) {
 *       // phpcs:ignore WordPress.Security.EscapeOutput
 *       echo file_get_contents( $logo_path );
 *   }
 *
 * Using file_get_contents() inlines the SVG directly, which allows
 * CSS currentColor to work and avoids an extra HTTP request.
 */
