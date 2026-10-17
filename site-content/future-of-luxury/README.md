# The Future of Luxury: media

- `media/`: the canonical web master for each essay published with a photograph. JPEG, sRGB, at most 2400 px wide, no metadata. One file per essay, named by its slug.
- `media-manifest.json`: the editorial decision for each of the 24 published essays:
  - photograph (with grade, opener and focus);
  - plate as an **editorial choice** (a native photograph exists; the plate is stronger);
  - plate because there is **no native source**.

WordPress serves the images: each master is the essay's featured image, and WordPress generates the sizes. To rebuild the media on a clean or migrated site:

```
php tools/fol-media-restore.php /path/to/wp-load.php --dry-run
php tools/fol-media-restore.php /path/to/wp-load.php
```

The tool matches essays by slug and is safe to re-run:
- it leaves correct images alone;
- it rewrites a missing file into its existing attachment;
- it re-attaches an existing attachment;
- it imports only when nothing exists.

Whenever a featured file is missing, the essay shows its plate automatically.

Runtime art direction (opener, homepage, focus) lives in `theme/summit/inc/editorial-art.php` (`summit_fol_photo_direction()`). Keep it in step with the manifest; the tool reports any difference. The source archive, rejected candidates and rights notes are kept outside the repository.
