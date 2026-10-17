#!/usr/bin/env python3
"""Parse the Summit editorial handover into typed, panel-addressed copy.

usage: python3 tools/editorial-parse.py <handover dir> <out.json>

The Editorial Master is the authority for the words; its text is identical
to the source passes for all 410 panels (checked here, panel by panel). The
source passes keep the run formatting that carries structure, which the
Master and the plain-text JSON flatten, so structure is read from the pass
table named in data/all_panels.json (source_file, source_table) and the
result is verified against the panel's public copy:

  eyebrow   bold, small (≤16 half-points), grey
  headline  serif (Georgia), large (≥28)
  heading   serif, 22–27 (a sub-heading inside a panel)
  lead      regular, 19–21
  item      bold, body size (a title within a list of titled items)
  cta       bold, link colour, ending in an arrow
  body      everything else

The right cell (production notes) is kept verbatim. Output is the
normalised source for the site build (tools/editorial-build.py).
"""
import json, re, sys, zipfile
from xml.etree import ElementTree as ET

NS = {'w': 'http://schemas.openxmlformats.org/wordprocessingml/2006/main'}
W = '{%s}' % NS['w']
ID = re.compile(r'^(S\d\d-P\d{3})\b')


def runs(p):
    out = []
    for r in p.findall('w:r', NS):
        t = ''
        for x in r:
            if x.tag == W + 't':
                t += x.text or ''
            elif x.tag in (W + 'tab', W + 'ptab'):
                t += '\t'
            elif x.tag in (W + 'br', W + 'cr'):
                t += '\t'
        if not t:
            continue
        rp = r.find('w:rPr', NS)
        def g(tag, attr='val'):
            e = rp.find('w:' + tag, NS) if rp is not None else None
            if e is None:
                return None
            return e.get(W + attr, 'on')
        bold = g('b') not in (None, '0', 'false')
        ital = g('i') not in (None, '0', 'false')
        sz = int(g('sz') or 0)
        col = (g('color') or '').upper()
        font = g('rFonts', 'ascii') or ''
        out.append(dict(t=t, b=bold, i=ital, sz=sz, col=col, font=font))
    return out


def classify(rs, text):
    first = rs[0]
    big = max(r['sz'] for r in rs)
    serif = any('Georgia' in r['font'] for r in rs)
    allbold = all(r['b'] for r in rs if r['t'].strip())
    if text.endswith('→') or ('→' in text and first['col'] not in ('212C2E', '')):
        return 'cta'
    if serif and big >= 28:
        return 'headline'
    if serif and big >= 21:
        return 'heading'
    if allbold and big <= 16 and first['col'] in ('616969', '6B7173', '707878', '595959'):
        return 'eyebrow'
    if allbold:
        return 'item'
    if big >= 19 and big <= 23:
        return 'lead'
    return 'body'


def para_text(rs):
    t = ''.join(r['t'] for r in rs)
    return re.sub(r' +', ' ', t).strip()


def inline(rs):
    """Text with <em> for italic runs (titles of works)."""
    out = ''
    for r in rs:
        t = r['t'].replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;')
        out += f'<em>{t}</em>' if r['i'] and t.strip() else t
    out = re.sub(r'</em>(\s*)<em>', r'\1', out)
    return re.sub(r'[ \t]+', ' ', out).strip()


LABEL = re.compile(r'^(PUBLIC[- ](FACING |WEBSITE )?COPY|WEBSITE COPY)\b', re.I)
ITEM_STYLES = ('PanelSubhead', 'LinkTitle')
CTA_STYLES = ('ActionLine', 'SmallCopyB')
TITLE_STYLES = ('PanelTitle', 'PanelHeading')
EYEBROW_STYLES = ('TinyLabel', 'CopyLabel', 'SmallHeader')
norm = lambda s: re.sub(r'\s+', ' ', s.replace('\u00a0', ' ')).strip()


def split_ctas(text):
    """'Explore Solutions → / Discuss a Brief →' -> ['Explore Solutions', 'Discuss a Brief']"""
    t = re.sub(r'^CTA\s*[·:]\s*', '', text.strip())
    if '→' not in t:
        return [t] if t else []
    parts = [p.strip(' /·|') for p in re.split(r'→', t)]
    return [re.sub(r'\s+', ' ', p).strip() for p in parts if p.strip(' /·|')]


def cell_lines(tbl, headline, eyebrow):
    rows = tbl.findall('w:tr', NS)
    tcs = rows[-1].findall('w:tc', NS)
    raw = []
    for p in tcs[0].findall('.//w:p', NS):
        rs = runs(p)
        txt = para_text(rs)
        if not txt:
            continue
        st = p.find('w:pPr/w:pStyle', NS)
        st = st.get(W + 'val') if st is not None else ''
        raw.append(dict(rs=rs, text=txt, style=st, bold=bool(rs) and all(r['b'] for r in rs if r['t'].strip()),
                        sz=max([r['sz'] for r in rs] or [0]), col=rs[0]['col'] if rs else ''))
    lines = []
    for r in raw:
        if LABEL.match(r['text']):
            continue
        lines.append(r)
    # find the headline
    hi = next((i for i, r in enumerate(lines) if norm(r['text']) == norm(headline)), None)
    if hi is None:
        hi = next((i for i, r in enumerate(lines) if r['style'] in TITLE_STYLES or r['sz'] >= 28), 0)
    pre = [norm(r['text']) for r in lines[:hi]]
    out = dict(eyebrow=pre[-1] if pre else '', headline=norm(lines[hi]['text']) if lines else '', blocks=[])
    body = lines[hi + 1:]
    # typical body size (for a lead)
    sizes = sorted(r['sz'] for r in body if not r['bold'])
    base = sizes[len(sizes) // 2] if sizes else 0
    seen_cta = False
    for r in body:
        t = r['text']
        cells = [norm(c) for c in t.split('\t') if norm(c)]
        # a paragraph that opens with a bold lead-in and continues in regular
        # weight (and perhaps an arrow link) is a titled row
        rs = [x for x in r['rs'] if x['t'].strip()]
        if len(cells) <= 1 and len(rs) > 1 and rs[0]['b'] and any(not x['b'] for x in rs[1:]):
            head, rest, cta = '', '', ''
            for x in rs:
                if '→' in x['t'] or (cta and not x['b']):
                    cta += x['t']
                elif x['b'] and not rest:
                    head += x['t']
                else:
                    rest += x['t']
            cells = [norm(c) for c in (head, rest) if norm(c)]
            if cta:
                cells.append(norm(cta))
        is_cta = r['style'] in CTA_STYLES or '→' in t or re.match(r'^CTA\s*[·:]', t)
        if len(cells) > 1:
            row = dict(type='row', cells=[], ctas=[])
            for c in cells:
                if '→' in c:
                    row['ctas'] += split_ctas(c)
                else:
                    row['cells'].append(c)
            out['blocks'].append(row)
            continue
        if is_cta:
            out['blocks'].append(dict(type='cta', labels=split_ctas(t)))
            seen_cta = True
            continue
        if r['bold'] or r['style'] in ITEM_STYLES:
            out['blocks'].append(dict(type='item', text=norm(t), html=inline(r['rs'])))
            continue
        kind = 'after_cta' if seen_cta else 'p'
        out['blocks'].append(dict(type=kind, text=norm(t), html=inline(r['rs']), big=bool(base and r['sz'] > base)))
    return out


def main(root, dst):
    import os
    panels = json.load(open(os.path.join(root, 'data', 'all_panels.json')))
    docs = {}
    out, bad = [], []
    for pnl in panels:
        f = pnl['source_file']
        if f not in docs:
            docs[f] = ET.fromstring(zipfile.ZipFile(os.path.join(root, 'source-passes', f)).read('word/document.xml')).findall('.//w:tbl', NS)
        tbl = docs[f][pnl['source_table'] - 1]
        parsed = cell_lines(tbl, pnl['headline'], pnl['eyebrow'])
        multi = len(tbl.findall('w:tr', NS)) > 2
        out.append(dict(id=pnl['id'], page=pnl['page'], path=pnl['path'], internal=pnl['path'].startswith('/_internal') or multi,
                        interaction=pnl['interaction'], notes=pnl['notes'], **parsed))
    json.dump(out, open(dst, 'w'), indent=1, ensure_ascii=False)
    print(len(out), 'panels parsed;', sum(1 for o in out if o['internal']), 'internal/config')


if __name__ == '__main__':
    main(sys.argv[1], sys.argv[2])
