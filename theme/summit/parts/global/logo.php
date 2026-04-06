<?php
/**
 * Part: Logo / Wordmark
 * Location: parts/global/logo.php
 *
 * Outputs the Summit wordmark with a compass/star icon.
 * Called from header.php (twice — nav bar and mega menu bar) and footer.php.
 *
 * The SVG uses currentColor so it inherits the parent element's text
 * colour automatically — works on both light and dark backgrounds without
 * duplication.
 *
 * To replace with the final brand mark:
 *   1. Drop the final SVG into assets/images/summit-wordmark.svg
 *   2. Replace the inline SVG below with file_get_contents() of that file
 *   3. Ensure the new SVG uses currentColor for fills/strokes
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;
?>
<span class="site-logo" aria-hidden="true">
    <svg class="site-logo__svg"
         viewBox="0 0 170 18"
         xmlns="http://www.w3.org/2000/svg"
         role="img"
         aria-label="Summit">
        <text x="0"
              y="13"
              font-family="-apple-system, 'SF Pro Display', 'Segoe UI', system-ui, sans-serif"
              font-size="12"
              font-weight="400"
              letter-spacing="5"
              fill="currentColor">SUMMIT</text>
        <!-- Compass star — delicate 4-point star, easy to swap for final brand asset -->
        <g transform="translate(118, 8)" fill="currentColor">
            <path d="M0-4.5 .8-.8 4.5 0 .8.8 0 4.5-.8.8-4.5 0-.8-.8Z" opacity="0.8"/>
        </g>
    </svg>
</span>
