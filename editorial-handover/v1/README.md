# Summit Editorial Handover v1 — in the repository

The editorial handover (Summit Communication Group, Claude Code handover v1)
and what the site made of it. The copy is authoritative; the current site
architecture decides where it lives.

## What is tracked here

| Path | What it is |
|---|---|
| `HANDOVER-README.md` | The handover's own README (source order, editorial rules, safety) |
| `pages/*.md` | One Markdown handover per route, copy and production notes |
| `data/all_panels.json` | The 410 source panels (`Sxx-Pxxx`) |
| `data/routes.json` | The handover's proposed routes |
| `data/*CTA_Routing*.csv`, `*Cross_Sell*.csv`, `*Channel_Activation*.json` | Routing, cross-sell and channel configuration |
| `register/*.csv` | The Production Register's sheets, exported as CSV (page map, CTA map, forms, channels, FAQs, interactions, photography, related work, publishing decisions) |
| `build/panels.json` | The panels parsed from the source passes, with structure (items, rows, CTAs, notes) — `tools/editorial-parse.py` |
| `build/site-pages.json`, `build/essays.json` | The site's pages and live essays the build resolves links against |
| `route-map.json` | Every handover route → canonical site route, type, template and action |
| `panel-map.json` | What became of all 410 panels: rendered, merged, held (with reason), internal/config |
| `cta-map.json` | Every CTA, its destination and status; the topics Let's Talk names; what waits on publication |
| `interactions.json` | The interaction patterns as built, and where the handover's logic is not supplied |

## What stays outside

Not committed (sizes and sources recorded here, the files kept with the
original pack):

- the handover ZIP (`Summit_ClaudeCode_Editorial_Handover_v1.zip`);
- `Summit_Website_Editorial_Master_v1.docx` (184 KB) and the source-pass DOCX
  files (`source-passes/`, 14 files, 736 KB) — the build reads the parsed
  result in `build/panels.json`;
- `Summit_Website_Production_Register_v1.xlsx` (296 KB) — its sheets are in
  `register/` (the full-panels sheet duplicates `all_panels.json`);
- `The_Future_of_Luxury_Editorial_Master (1).xlsx` — the 143-article archive
  workbook (1.3 MB). It is an intake and preservation record: the archive is
  not bulk-published.

## How the site is built from it

```
python3 tools/editorial-parse.py <unzipped handover> editorial-handover/v1/build/panels.json   # only when the source passes change
python3 tools/editorial-build.py                                                                # writes site-content/ and the maps
php tools/site-content-apply.php /path/to/wp-load.php [--section=…] [path …]                   # dry run
php tools/site-content-apply.php /path/to/wp-load.php --apply                                  # Local / staging only
```

The build is deterministic: running it twice writes identical files. Block
markup is serialised exactly as WordPress stores it (every page validates
and round-trips in the block editor). Each panel's section carries its ID as
its anchor (`id="s02-p006"`); the theme adds `data-summit-panel="S02-P006"`.
IDs are never shown.

## Rules the build applies

- Words come from the panels unchanged (British English, punctuation,
  headline case). Compositions adapt; copy does not.
- An essay is linked only when it is live. Copy that names unpublished
  essays, a not-yet-approved paper, or features the site does not have
  (archive search, a preference centre, client sign-in) is held and recorded.
- Answers written as production instructions ("should be tested before…")
  are held as internal notes.
- Enquiry CTAs carry their context to Let's Talk (`?topic=`, `?programme=`,
  `?intent=`, `?context=`); the visitor can edit it.
- Approved direct routes only: +61 2 9127 9005, +44 20 7011 8509,
  hello@summitcommunication.group. No SMS or WhatsApp until confirmed.
