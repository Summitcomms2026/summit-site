# Photography — from delivered file to the page

The working register is **`visual-asset-manifest.csv`** (also `.json`): one row
for each of the 224 panels the Production Register flags, keyed by panel ID.
98 rows ask for a visual; 126 do not (their brief says no image, or the row is
a layout review only). Each wanted row gives the subject, what to avoid, the
orientation and aspects, what the site shows there today, its status, its
priority (P1 defining · P2 important · P3 optional), rights, the slot it
fills and the file name it should arrive with.

Regenerate it after any change: `python3 tools/visual-asset-manifest.py`.

## 1. Name the file by panel ID

```
<panel>-<page>-<role>.jpg        S13-P015-beauty-fragrance-hero.jpg
                                 S06-P003-the-observatory-support.jpg
<panel>-<n>-<page>-<role>.jpg    S01-P006-1-home-object.jpg   (a panel taking several)
```

The manifest's `file_name` column gives the exact name. A delivery called just
`S13-P015.jpg` is unambiguous too: the panel ID is the key.

## 2. Make the web master

- sRGB JPEG, quality ~82, long edge 2400 px (hero bands) or 1800 px
  (everything else); no upscaling.
- Keep the whole frame: the site crops with `object-fit` and a focal point,
  so do not pre-crop. Note the focal point (e.g. `50% 40%`).
- Put it in `site-content/visual-assets/media/`.

## 3. Register it

Add an entry to `site-content/visual-assets/manifest.json`:

```json
"S13-P015": {
  "master": "S13-P015-beauty-fragrance-hero.jpg",
  "alt": "What the image shows, in a sentence.",
  "credit": "Photographer / owner, as it must appear (or empty)",
  "focus": "50% 40%",
  "status": "provisional-existing"
}
```

`status` follows the manifest: `provisional-existing` until rights are
confirmed, then `final-existing`. Record the owner, licence and permitted use
in the same entry (`"rights": "…"`) — the site never shows an image whose
rights are unknown on a public environment.

## 4. Place it

```
php tools/visual-assets-apply.php /path/to/wp-load.php            # dry run
php tools/visual-assets-apply.php /path/to/wp-load.php --apply
python3 tools/visual-asset-manifest.py                             # the register picks it up
```

The master becomes a WordPress attachment (matched by file name, never by
ID); WordPress makes the responsive sizes. The option `summit_panel_visuals`
tells the theme which attachment fills which slot:

| Slot | Where it renders |
|---|---|
| a page's opening panel | a band beneath the hero (`parts/global/page-hero.php`); on industry pages it replaces the development image |
| any other panel | a figure after the panel's headline, inside its own section (`data-summit-panel="Sxx-Pxxx"`) |
| `S01-P002` / `S01-P004` / `S01-P005` / `S01-P006-1…3` | the homepage's statement field, Observatory field, programme band and research-card covers |
| `S01-P001`, `S01-P008` | the homepage hero and closing images (ACF / theme assets) |
| Touch Me Not, The Luxury 100, portraits | the existing artwork files: `theme/summit/assets/img/research/<name>.jpg`, `assets/img/about/<name>.jpg` — replacing the file updates the panel |
| Future of Luxury essays | their own media system (`site-content/future-of-luxury/`) |

A panel that already carries artwork, a portrait or a cover is updated by
replacing that file, never by adding a second image.

## Until then

Nothing is registered, so every slot renders nothing and the page stays
typographic: no grey boxes, no "image coming soon", no stock fill.
