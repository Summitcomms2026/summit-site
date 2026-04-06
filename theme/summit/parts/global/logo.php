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
         viewBox="0 0 155 20"
         xmlns="http://www.w3.org/2000/svg"
         role="img"
         aria-label="Summit">
        <text x="0"
              y="15"
              font-family="-apple-system, 'SF Pro Display', 'Segoe UI', system-ui, sans-serif"
              font-size="13.5"
              font-weight="500"
              letter-spacing="3.5"
              fill="currentColor">SUMMIT</text>
        <!-- Compass star — 4-point elongated star, easy to swap for final brand asset -->
        <g transform="translate(113, 10)" fill="currentColor">
            <path d="M0-6 1.2-1.2 6 0 1.2 1.2 0 6-1.2 1.2-6 0-1.2-1.2Z" opacity="0.85"/>
        </g>
    </svg>
</span>
