<?php
/**
 * Page Dependency Guards
 *
 * Displays an admin notice when critical slug-dependent pages are missing.
 * These pages are required for page-{slug}.php templates to resolve.
 *
 * Note: The following routes are now served by CPT archives (not pages)
 * and no longer need page-based guards:
 *   /work-showcase       → case_study archive
 *   /the-future-of-luxury → article archive
 *   /tastemakers          → podcast_season archive
 *
 * Informational only — no redirects, no auto-creation, no blocking.
 *
 * @package SummitTheme
 */

defined( 'ABSPATH' ) || exit;

// No page-based guards are currently required.
// CPT archive routes are handled by their registered has_archive slugs.
// This file is retained for future page-based guards if needed.
