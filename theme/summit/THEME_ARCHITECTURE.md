# Summit Theme Architecture

This document governs the presentation layer of the Summit WordPress theme.

## Principle

The Summit theme is responsible for presentation only.

It must not register:
- custom post types
- taxonomies
- ACF field groups
- business logic that belongs in the Summit Core plugin

The Summit Core plugin owns content architecture.
The Summit theme owns rendering, layout, templates, and reusable visual parts.

## Theme Folder Structure

summit/
- style.css
- functions.php
- header.php
- footer.php
- index.php

- page-work.php
- page-future-of-luxury.php
- page-tastemakers.php

- single-case_study.php
- single-article.php
- single-podcast_episode.php
- single-download.php
- single-podcast_season.php

- taxonomy-article_category.php
- taxonomy-podcast_theme.php

- inc/
- parts/
  - global/
  - related/

- assets/
  - css/
  - js/

## Folder Responsibilities

### Root template files
These control page-level rendering according to WordPress template hierarchy.

### inc/
Holds supporting theme logic such as:
- seo.php
- template-functions.php
- query-helpers.php

These files support templates but should not duplicate plugin responsibilities.

### parts/global/
Reusable site-wide UI parts such as:
- cta-footer.php
- subscribe.php
- breadcrumbs.php
- hero.php

### parts/related/
Reusable related-content blocks such as:
- articles.php
- case-studies.php
- episodes.php
- downloads.php

## Content Rendering Rules

### Work
The /work page is a native WordPress page using page-work.php.
It queries and renders case_study posts.

### Future of Luxury
The /future-of-luxury page is a native WordPress page using page-future-of-luxury.php.
It queries and renders article posts.

### Tastemakers
The /tastemakers page is a native WordPress page using page-tastemakers.php.
It queries and renders podcast_season and podcast_episode content.

### Single templates
Single templates render individual content objects using ACF fields already registered in the Summit Core plugin.

## ACF Rules

Templates may use:
- get_field()
- the_field()

But must only reference field names that already exist in the Summit Core plugin.

Do not invent new field names in templates.

## SEO Rules

Templates must not inject SEO output via add_action('wp_head', ...) after get_header().

SEO and schema belong in:
- inc/seo.php
or
- functions.php

Templates may document required SEO fields, but should not output them directly unless the theme architecture is explicitly updated.

## Query Rules

Custom queries in page templates must:
- use WP_Query
- call wp_reset_postdata() after custom loops
- avoid corrupting the global $post object

## Design Rules

The Summit theme should feel like a high-trust publishing platform with agency authority.

Priorities:
- clean hierarchy
- restrained typography
- generous spacing
- editorial clarity
- no visual clutter
- no dependency on CSS frameworks unless explicitly approved

## For Claude

When generating or revising theme code:
1. Respect this architecture exactly.
2. Do not create new folders unless instructed.
3. Do not move content architecture into the theme.
4. Do not create archive templates for content types whose primary archive URL is owned by native WordPress pages.
5. Prefer reusable parts in parts/global/ and parts/related/.
6. Keep code production-safe and minimal.