#!/usr/bin/env python3
"""The site's visual-asset manifest: the Production Register's photography
sheet, seen from the built site.

usage: python3 tools/visual-asset-manifest.py

Reads   editorial-handover/v1/register/Photography-graphics.csv  (224 panels)
        editorial-handover/v1/build/panels.json, panel-map.json, route-map.json
        site-content/future-of-luxury/media-manifest.json
        site-content/visual-assets/manifest.json   (assets delivered so far)
Writes  editorial-handover/v1/visual-asset-manifest.csv / .json

One row per register panel, so nothing the register flags is lost. Each row
says what kind of visual the panel wants (or that it wants none), what the
site shows there now, how urgent it is, and the file name a delivered image
should carry. The Production Register stays the source; this is the site's
implementation view of it.
"""
import csv
import json
import os
import re
from collections import Counter, OrderedDict

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
HV = os.path.join(ROOT, 'editorial-handover', 'v1')


def load(*p):
    with open(os.path.join(ROOT, *p), encoding='utf-8') as f:
        return json.load(f)


register = list(csv.reader(open(os.path.join(HV, 'register', 'Photography-graphics.csv'), encoding='utf-8')))[1:]
panels = {p['id']: p for p in load('editorial-handover', 'v1', 'build', 'panels.json')}
panel_map = load('editorial-handover', 'v1', 'panel-map.json')
route_map = load('editorial-handover', 'v1', 'route-map.json')
delivered = load('site-content', 'visual-assets', 'manifest.json').get('assets', {})

# The first panel of each page is its opening (hero)
HERO = {r['panels'][0]: r for r in route_map if r.get('canonical') and r['type'] in ('Page', 'front page') and r['panels']}
PAGE_OF = {pid: r for r in route_map for pid in r.get('panels', [])}

LINE = re.compile(r'^(IMAGE|IMAGES|IMAGERY|PHOTO(?: [·+] [^\n]*?)?|PHOTOGRAPHY)\s{2,}(.+)$', re.M)
NONE = re.compile(r'^\s*(none\b|no image|no new image|no photograph|no photographs on|no imagery|not required|none required|none needed|none necessary)', re.I)


def field(text, key):
    m = re.search(r'^' + key + r'\s{2,}(.+)$', text, re.M)
    return m.group(1).strip() if m else ''


def family(route):
    top = (route or '').strip('/').split('/')[0].split('#')[0]
    return {'': 'home', 'solutions': 'solutions', 'the-observatory': 'observatory', 'programmes': 'programmes',
            'research': 'research', 'about': 'about', 'the-future-of-luxury': 'future-of-luxury',
            'lets-talk': 'contact', 'media-enquiries': 'contact', 'footer.php': 'footer'}.get(top, 'utility')


def slug(route):
    s = (route or '').strip('/').split('#')[0].split('/')[-1] or 'home'
    return {'footer.php': 'footer', '{essay}': 'essay'}.get(s, s)


# By Need destination imagery, given on the Solutions index (S02-P003/P004)
NEED_PHOTO = {}
for r in register:
    if r[0] in ('S02-P003', 'S02-P004'):
        for title, text in re.findall(r'^PHOTO · (.+?)\s{2,}(.+)$', r[9], re.M):
            NEED_PHOTO[title.strip()] = (r[0], text.strip())

# What the site shows today, where it shows something
EXISTING = {
    'S01-P001': ('photography', 'Homepage hero photograph (assets/img/top-herophoto.jpg)', 'provisional-existing', 'Development image; not cleared for the new brief'),
    'S01-P007': ('photography', 'The live essays’ photographs and plates (Future of Luxury media system)', 'final-existing', 'Essay media as published'),
    'S08-P002': ('existing-research-art', 'Touch Me Not title page — provisional file, shown on Local only; production shows the typographic title page; The Luxury 100 2027 cover', 'needs-clearance', 'Olivia Dhordain: confirm reproduction permission and credit; then file at assets/img/research/touch-me-not-title-page.jpg'),
    'S08-P006': ('existing-research-art', 'Touch Me Not title page (in the next panel)', 'needs-clearance', 'Approved cover image and illustrations only with clearance (register)'),
    'S08-P007': ('existing-research-art', 'Touch Me Not title page — provisional file, shown on Local only; production shows the typographic title page', 'needs-clearance', 'Olivia Dhordain: confirm reproduction permission and credit'),
    'S08-P008': ('existing-research-art', 'Mnemosyne · Hephaestus plate — Olivia Dhordain; provisional file, Local only', 'needs-clearance', 'Olivia Dhordain: confirm reproduction permission and credit'),
    'S08-P013': ('diagram', 'The empty-chair drawing (theme-drawn)', 'final-existing', 'Summit’s own drawing'),
    'S10-P008': ('existing-research-art', 'Touch Me Not title page — provisional file, shown on Local only; production shows the typographic title page; The Luxury 100 2027 cover', 'needs-clearance', 'Olivia Dhordain: confirm reproduction permission and credit'),
    'S08-P012': ('publication-object', 'The Luxury 100 2027 cover (assets/img/research/luxury-100-2027-cover.jpg), set in the next panel (S08-P014)', 'final-existing', 'In the theme’s approved research folder'),
    'S10-P007': ('photography', 'The Characteristics of Luxury, plate 01 (Summit’s own, from the essay)', 'final-existing', 'Summit’s own work'),
    'S10-P024': ('portrait', 'Portrait of Gregory Gray (assets/img/about/gregory-gray.jpg)', 'provisional-existing', 'Confirm it is the approved portrait and its photographer credit'),
    'S10-P025': ('portrait', 'Typographic monogram — a portrait file at assets/img/about/olivia-dhordain.jpg replaces it', 'needs-procurement', 'Portrait only with Olivia Dhordain’s consent'),
}
INDUSTRIES = ('watches-jewellery', 'fashion-accessories', 'beauty-fragrance', 'wine-spirits', 'automotive',
              'real-estate-hospitality', 'art-collectibles', 'private-banking-wealth-management')
INDUSTRY_HERO_NOTE = 'Development image in the hero band (summit_visual, theme assets/img/solutions/); provisional, not cleared'


def positive(text):
    """The direction without its avoidances ("Avoid portraits of …")."""
    return ' '.join(s for s in re.split(r'(?<=[.;])\s+', text) if not re.match(r'(?i)\s*(avoid|do not|never|no |not |without)', s))


# A reading of each panel's visual brief: (asset type, priority, note).
# P1 defining — homepage, section openings, Solutions heroes, Observatory,
#    the people of About, the major research objects
# P2 important — substantive support that materially improves the page
# P3 optional — the brief allows the panel to stay typographic
# Panels not listed want no visual (their brief says so, or names none).
CURATED = {
    'S01-P001': ('photography', 'P1', ''), 'S01-P002': ('photography', 'P1', ''),
    'S01-P004': ('document', 'P1', 'Today the field shows the Observatory instrument drawing.'),
    'S01-P005': ('photography', 'P3', 'Type-led; one overarching photograph at most.'),
    'S01-P006': ('publication-object', 'P1', 'Three covers: S01-P006-home-1 (Touch Me Not), -2 (The Luxury 100), -3 (The Observatory); today the cards carry typographic marks.'),
    'S01-P007': ('photography', 'P1', ''),
    'S02-P005': ('photography', 'P1', ''), 'S02-P007': ('document', 'P3', 'Later, and only with permission: a redacted diagnostic page.'),
    'S02-P008': ('document', 'P2', ''), 'S03-P001': ('document', 'P1', ''), 'S03-P005': ('photography', 'P2', ''),
    'S03-P006': ('photography', 'P1', ''), 'S03-P009': ('publication-object', 'P2', ''), 'S03-P011': ('photography', 'P1', ''),
    'S03-P013': ('document', 'P3', ''), 'S03-P014': ('existing-research-art', 'P2', 'The Touch Me Not title page exists in the theme; it may serve once cleared.'),
    'S04-P001': ('document', 'P1', ''), 'S04-P002': ('photography', 'P3', ''), 'S04-P003': ('document', 'P2', ''),
    'S04-P005': ('publication-object', 'P3', ''), 'S04-P007': ('photography', 'P1', ''), 'S04-P008': ('photography', 'P3', 'One coherent shoot of three images if any.'),
    'S04-P010': ('document', 'P2', ''), 'S04-P013': ('document', 'P1', ''), 'S04-P015': ('document', 'P2', ''),
    'S04-P016': ('photography', 'P3', 'The essay entries are held; images follow publication.'),
    'S05-P001': ('photography', 'P1', ''), 'S05-P003': ('photography', 'P2', ''), 'S05-P007': ('photography', 'P1', ''),
    'S05-P010': ('photography', 'P3', ''), 'S05-P013': ('document', 'P1', ''), 'S05-P014': ('document', 'P3', ''),
    'S05-P017': ('document', 'P2', ''),
    'S06-P001': ('document', 'P1', ''), 'S06-P003': ('document', 'P2', ''), 'S06-P006': ('photography', 'P3', ''),
    'S06-P008': ('photography', 'P1', ''), 'S06-P015': ('document', 'P1', ''), 'S06-P016': ('photography', 'P3', ''),
    'S06-P022': ('document', 'P1', ''), 'S06-P024': ('photography', 'P3', ''), 'S06-P029': ('document', 'P1', ''),
    'S06-P034': ('publication-object', 'P3', ''),
    'S07-P001': ('photography', 'P1', ''), 'S07-P002': ('photography', 'P3', ''), 'S07-P006': ('photography', 'P2', ''),
    'S07-P009': ('photography', 'P2', ''), 'S07-P014': ('publication-object', 'P2', ''), 'S07-P017': ('publication-object', 'P3', ''),
    'S07-P022': ('document', 'P2', ''), 'S07-P025': ('diagram', 'P3', 'A category table, not an image.'), 'S07-P030': ('photography', 'P2', ''),
    'S07-P033': ('photography', 'P3', ''), 'S07-P038': ('photography', 'P2', ''), 'S07-P046': ('document', 'P2', ''),
    'S07-P049': ('document', 'P3', ''),
    'S08-P001': ('existing-research-art', 'P1', 'A detail of Olivia Dhordain’s existing illustrations would answer the brief once cleared.'),
    'S08-P002': ('existing-research-art', 'P2', ''), 'S08-P006': ('publication-object', 'P1', 'The approved book cover.'),
    'S08-P007': ('existing-research-art', 'P2', ''), 'S08-P008': ('existing-research-art', 'P2', ''),
    'S08-P012': ('publication-object', 'P1', 'The approved Luxury 100 cover.'), 'S08-P013': ('diagram', 'P2', ''),
    'S08-P018': ('publication-object', 'P2', ''), 'S08-P019': ('publication-object', 'P2', 'The New Asian Maison cover.'),
    'S08-P025': ('document', 'P2', ''),
    'S09-P002': ('photography', 'P1', ''),
    'S10-P001': ('portrait', 'P1', ''), 'S10-P002': ('document', 'P3', ''), 'S10-P003': ('photography', 'P3', ''),
    'S10-P006': ('document', 'P2', 'Archival evidence or a dated portrait from the earlier practice; otherwise typography.'),
    'S10-P007': ('photography', 'P3', ''), 'S10-P008': ('existing-research-art', 'P2', ''), 'S10-P017': ('document', 'P2', ''),
    'S10-P019': ('publication-object', 'P3', ''), 'S10-P023': ('portrait', 'P1', ''), 'S10-P024': ('portrait', 'P1', ''),
    'S10-P025': ('portrait', 'P1', ''), 'S10-P029': ('photography', 'P2', ''), 'S10-P031': ('existing-research-art', 'P3', ''),
    'S11-P001': ('portrait', 'P3', 'Typography alone is entirely suitable (brief).'), 'S11-P011': ('photography', 'P3', ''),
    'S11-P016': ('publication-object', 'P3', ''),
    'S13-P001': ('photography', 'P1', ''), 'S13-P008': ('photography', 'P1', ''), 'S13-P015': ('photography', 'P1', ''),
    'S13-P022': ('photography', 'P1', ''), 'S13-P029': ('photography', 'P1', ''), 'S13-P036': ('photography', 'P1', ''),
    'S13-P043': ('photography', 'P1', ''), 'S13-P050': ('photography', 'P1', ''), 'S13-P057': ('photography', 'P1', ''),
    'S13-P064': ('photography', 'P1', ''), 'S13-P071': ('document', 'P1', ''), 'S13-P078': ('photography', 'P1', ''),
    'S13-P085': ('photography', 'P1', ''), 'S13-P092': ('document', 'P1', ''),
}
NOT_WANTED = {'S01-P008': 'The brief prefers no photograph; the site still shows the earlier closing image (cta-mainimage.jpg) — a decision for the design review.'}


def asset_type(text):
    t = positive(text).lower()
    if re.search(r'portrait|founder|headshot', t):
        return 'portrait'
    if re.search(r'olivia dhordain|illustration', t):
        return 'existing-research-art'
    if re.search(r'\bcover\b|book\b|printed pages|white paper|report|printed', t):
        return 'publication-object'
    if re.search(r'diagram|chart|map\b|timeline|schematic', t):
        return 'diagram'
    if re.search(r'plate|editorial art|graphic', t):
        return 'editorial-art'
    if re.search(r'document|manuscript|archival papers|record|pattern book|notebook|ledger|contract|drawing', t):
        return 'document'
    if re.search(r'typographic treatment|plain typographic', t):
        return 'none-required'
    return 'photography'


def aspect(text, hero):
    t = text.lower()
    if 'portrait' in t:
        return 'portrait', '4:5', 'full width beneath the name; 4:5'
    if re.search(r'\bcover\b|book', t):
        return 'portrait', '2:3 (object)', 'beneath the introduction; object kept whole'
    if re.search(r'55/45|45%|40%|60%|split|two-column|one side', t):
        return 'portrait', '4:5', 'after the opening copy, full width (copy precedes image)'
    if re.search(r'full-width|full width|wide|panoramic', t) or hero:
        return 'landscape', '16:9 (band)', 'after the opening copy; recropped 4:5 around the focal point'
    return 'landscape', '3:2', 'after the panel headline, full width'


HOME_SLOT = {
    'S01-P001': 'home hero background (ACF home_hero_image)',
    'S01-P002': 'statement field — image beside the text, 45/55 (key S01-P002)',
    'S01-P004': 'Observatory field — replaces the instrument drawing (key S01-P004)',
    'S01-P005': 'one band above the programme register (key S01-P005)',
    'S01-P006': 'the three research cards — covers replace the marks (keys S01-P006-1, -2, -3)',
    'S01-P007': 'Future of Luxury media system (essay featured images)',
}
ART_SLOT = {'S08-P002', 'S08-P007', 'S08-P008', 'S08-P013', 'S10-P008', 'S10-P024', 'S10-P025', 'S10-P007', 'S08-P012'}


def slot_for(pid, kind, hero, fam):
    if kind == 'none-required':
        return ''
    if pid in HOME_SLOT:
        return HOME_SLOT[pid]
    if pid.startswith('S09'):
        return 'Future of Luxury media system (essay featured images)'
    if pid in ART_SLOT:
        return 'existing artwork / portrait shortcode in the panel (a delivered file replaces it)'
    if hero:
        return 'hero band beneath the opening (key = panel ID)'
    return 'figure after the panel headline (key = panel ID)'


rows = []
for r in register:
    pid, head = r[0], r[2]
    p = panels.get(pid, {})
    pm = panel_map.get(pid, {})
    route = (PAGE_OF.get(pid) or {}).get('canonical') or pm.get('route') or ''
    fam = family(route)
    full = r[9]
    lines = [m[1] for m in LINE.findall(full)]
    image_line = ' '.join(lines) or (r[3] if r[3] and not NONE.match(r[3]) else '')
    layout = field(full, 'LAYOUT') or r[4].split(' | ')[0]
    mobile = field(full, 'MOBILE')
    hero = pid in HERO
    status_note = ''
    # the need pages' hero imagery is given on the Solutions index
    if hero and route.startswith('/solutions/'):
        title = (HERO[pid].get('page') or '').split(' — ')[0]
        for need, (src, text) in NEED_PHOTO.items():
            if need.lower() == title.lower() or need.lower() in (p.get('headline', '') + ' ' + route.replace('-', ' ')).lower():
                image_line = (image_line + ' ' if image_line else '') + text
                status_note = f'Direction from {src} (By Need index).'
    explicit_none = bool(lines and NONE.match(lines[0])) or (not lines and NONE.match(r[3] or ''))
    layout_mentions = bool(re.search(r'\b(image|photo|photograph|portrait|cover|visual|illustration|picture)\b', layout, re.I)) and not re.search(r'without image|text-only|no image', layout, re.I)

    if pm.get('status', '').startswith('internal'):
        kind, status, prio, current, rights = 'none-required', 'not-required', '—', 'Not shown (internal production panel)', ''
    elif pid in NOT_WANTED:
        kind, status, prio, current, rights = 'none-required', 'not-required', '—', 'Closing image (assets/img/cta-mainimage.jpg)', ''
        status_note = NOT_WANTED[pid]
    elif pid not in CURATED:
        kind, status, prio, current, rights = 'none-required', 'not-required', '—', 'Typographic by intent', ''
    else:
        kind, prio, cnote = CURATED[pid]
        status_note = ' '.join(x for x in (status_note, cnote) if x)
        status, current, rights = 'needs-procurement', 'None — the panel is set typographically', r[8] or 'Not cleared'
        if pm.get('status', '').startswith('held') or pid in ('S03-P005', 'S04-P016'):
            status, current = 'held', 'Held with its essay-specific copy (held-content.json)'
        elif pid.startswith('S09') or pid == 'S01-P007':
            status, current, rights = 'final-existing', 'Essay photographs and plates (Future of Luxury media system)', 'Per site-content/future-of-luxury/media-manifest.json'
        elif hero and route.strip('/').split('/')[-1] in INDUSTRIES:
            status, current, rights = 'provisional-existing', INDUSTRY_HERO_NOTE, 'Not cleared'
    if pid in EXISTING:
        kind, current, status, rights = EXISTING[pid][0], EXISTING[pid][1], EXISTING[pid][2], EXISTING[pid][3]
    if pid in delivered:
        current, status = 'Delivered: ' + delivered[pid].get('master', ''), delivered[pid].get('status', 'provisional-existing')
    orient, desk, mob = aspect(image_line + ' ' + layout, hero) if kind != 'none-required' else ('—', '—', '—')
    if mobile and kind != 'none-required':
        mob = mobile
    avoid = '; '.join(s.strip() for s in re.split(r'(?<=[.;])\s+', image_line) if re.match(r'(?i)(avoid|do not|never|no |not )', s.strip()))
    role = 'portrait' if kind == 'portrait' else ('hero' if hero else ('object' if kind in ('publication-object', 'existing-research-art', 'document') else 'support'))
    rows.append(OrderedDict(
        panel=pid, route=route, family=fam, headline=head,
        asset_type=kind,
        editorial_purpose=field(full, 'PURPOSE') or field(full, 'ROLE') or ('Opening image for the page' if hero else ''),
        subject_direction=image_line.strip(),
        avoid=avoid,
        orientation=orient, desktop_aspect=desk, mobile_treatment=mob,
        current_asset=current, status=status, priority=prio,
        rights=rights, file_name=(f'{pid}-{slug(route)}-{role}.jpg' if kind != 'none-required' else ''),
        slot=slot_for(pid, kind, hero, fam),
        notes=' '.join(x for x in (status_note, field(full, 'CAUTION'), field(full, 'STATUS') if kind != 'none-required' else '') if x),
    ))

with open(os.path.join(HV, 'visual-asset-manifest.csv'), 'w', newline='', encoding='utf-8') as f:
    w = csv.DictWriter(f, fieldnames=list(rows[0].keys()))
    w.writeheader()
    w.writerows(rows)
counts = dict(
    register_panels=len(rows),
    by_type=dict(Counter(r['asset_type'] for r in rows)),
    by_status=dict(Counter(r['status'] for r in rows)),
    by_priority=dict(Counter(r['priority'] for r in rows if r['priority'] != '—')),
)
with open(os.path.join(HV, 'visual-asset-manifest.json'), 'w', encoding='utf-8') as f:
    json.dump(dict(description=__doc__.strip().split('\n\n')[2].replace('\n', ' '), counts=counts, rows=rows), f, indent=1, ensure_ascii=False)
    f.write('\n')
print(json.dumps(counts, indent=1))
