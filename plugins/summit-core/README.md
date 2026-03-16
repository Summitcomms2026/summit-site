# summit-core

Summit Communication Group — Core WordPress Plugin  
Version 1.0.0

---

## Purpose

`summit-core` is the content architecture layer for the Summit Communication
Group website. It registers all custom post types, taxonomies, meta fields and
admin enhancements that need to survive a theme change.

**Nothing in this plugin is decorative. Everything is structural.**

---

## Governance

This plugin must be read alongside the following documents in `/governance/`:

| File | Governs |
|------|---------|
| `summit_build_system.md` | Architectural rules and build philosophy |
| `summit_content_model.md` | Content type definitions and field specifications |
| `summit_site_architecture.md` | URL structure and slug logic |
| `summit_prompting_rules.md` | How to brief Claude Code on future changes |

When making changes to this plugin, read the relevant governance file first.

---

## File Structure

```
plugins/summit-core/
├── summit-core.php          Main plugin file. Loads modules, handles activation.
├── inc/
│   ├── post-types.php       CPT registration (Step 2)
│   ├── taxonomies.php       Taxonomy registration (Step 3)
│   ├── fields.php           ACF field groups — v1.0 next step (not yet)
│   ├── schema.php           Structured data output — future
│   ├── admin.php            Admin columns, filters, enhancements — future
│   └── enquiries.php        Private enquiry CPT and status logic — future
└── README.md                This file
```

---

## Registered Content Types

| Post Type Key    | Admin Label      | Slug Base                        | Archive |
|------------------|------------------|----------------------------------|---------|
| `case_study`     | Case Studies     | `/work/[project-slug]`           | Off     |
| `article`        | Articles         | `/future-of-luxury/[slug]`       | Off     |
| `podcast_season` | Podcast Seasons  | `/tastemakers/[season-slug]`     | Off     |
| `podcast_episode`| Podcast Episodes | `/tastemakers/episodes/[slug]`   | Off     |
| `download`       | Downloads        | `/downloads/[document-slug]`     | Off     |

**Why are archives disabled?**  
Archive display is handled by parent `wp_page` templates using custom
`WP_Query`. This prevents duplicate archive URLs competing with the governed
page slugs, and gives full template control without rewrite-rule overrides.

---

## Registered Taxonomies

| Taxonomy Key        | Admin Label        | Used By                              | Style      |
|---------------------|--------------------|--------------------------------------|------------|
| `sector`            | Sectors            | `case_study`, `download`             | Hierarchical |
| `service_type`      | Service Types      | `case_study`                         | Flat       |
| `article_category`  | Article Categories | `article`                            | Hierarchical |
| `format`            | Formats            | `case_study`, `download`             | Flat       |
| `podcast_theme`     | Podcast Themes     | `podcast_episode`, `podcast_season`  | Flat       |

All taxonomies are seeded with default terms on activation. Terms can be
edited in the admin. Do not delete the governed vocabulary terms without
updating the architecture document.

---

## Installation

1. Place the `summit-core/` folder in `plugins/`.
2. Activate via WP Admin → Plugins.
3. Activation hook registers all CPTs/taxonomies and flushes rewrite rules.
4. Verify by visiting **Settings → Permalinks** and clicking Save (safe
   second flush if needed).
5. Confirm each CPT appears in the WP Admin sidebar.

---

## After Activation — Verification Checklist

- [ ] Case Studies appears in WP Admin sidebar
- [ ] Articles appears in WP Admin sidebar
- [ ] Podcast Seasons appears in WP Admin sidebar
- [ ] Podcast Episodes appears in WP Admin sidebar
- [ ] Downloads appears in WP Admin sidebar
- [ ] Sectors taxonomy visible under Case Studies
- [ ] Service Types taxonomy visible under Case Studies
- [ ] Article Categories taxonomy visible under Articles
- [ ] Formats taxonomy visible under Case Studies
- [ ] Podcast Themes taxonomy visible under Podcast Episodes
- [ ] Default terms seeded in each taxonomy
- [ ] Create one test case_study — confirm `/work/test-case` resolves
- [ ] Create one test article — confirm `/future-of-luxury/test-article` resolves
- [ ] Create one test episode — confirm `/tastemakers/episodes/test-episode` resolves

---

## Next Step

**Step 4:** Register ACF Pro field groups for all five CPTs.  
File: `inc/fields.php`  
Reference: Section 5 of `summit_content_architecture.docx` and `summit_content_model.md`

---

## Rules

- Do not move CPT or taxonomy registration into the theme.
- Do not add `has_archive => true` to any CPT without architectural review.
- Do not change slug bases after content has been published without a redirect plan.
- Do not add new taxonomies without updating `summit_content_model.md`.
