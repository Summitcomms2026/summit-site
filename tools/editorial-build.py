#!/usr/bin/env python3
"""Build the site's tracked page content from Editorial Handover v1.

usage: python3 tools/editorial-build.py

Reads   editorial-handover/v1/build/panels.json      the parsed panels
                                                     (tools/editorial-parse.py)
        editorial-handover/v1/build/site-pages.json  the site's pages
        editorial-handover/v1/build/essays.json      the live essays
        site-content/<section>/manifest.json         page fields kept as they are
                                                     (tone, visual, form …)
Writes  site-content/<section>/<slug>.blocks.html    page content (core blocks)
        site-content/<section>/manifest.json         hero fields from the copy
        editorial-handover/v1/route-map.json         handover route → site route
        editorial-handover/v1/panel-map.json         what became of every panel
        editorial-handover/v1/cta-map.json           every CTA and its destination

The editorial copy is never rewritten: words come from the parsed panels as
they are. The site's current architecture decides where a page lives; the
family's own class vocabulary decides how a panel is set. Each panel keeps
its stable ID as the anchor of its section (id="s02-p006"); the theme adds
data-summit-panel="S02-P006" as the page is shown.

Run it again after any change to the handover; then apply the pages with
tools/site-content-apply.php.
"""
import html
import json
import os
import re
from collections import Counter, OrderedDict

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
HV = os.path.join(ROOT, 'editorial-handover', 'v1')
SC = os.path.join(ROOT, 'site-content')


def load(*parts):
    with open(os.path.join(*parts), encoding='utf-8') as f:
        return json.load(f)


panels = load(HV, 'build', 'panels.json')
P = OrderedDict((p['id'], p) for p in panels)

# Three About panels open on a list whose first entry the parser read as the
# panel's headline (their copy cell begins with a role label, e.g. “PUBLIC
# WEBSITE COPY / WORKING TOGETHER”). The entry goes back into the list; the
# label names the section for assistive technology only.
for pid, label in (('S10-P003', 'Explore About'), ('S10-P013', 'Principles'), ('S10-P030', 'Working Together')):
    p = P[pid]
    p['blocks'] = [dict(type='item', text=p['headline'], html=html.escape(p['headline'], quote=False))] + p['blocks']
    p['headline'], p['hidden_heading'] = label, True

# Pass 13 gives each page's enquiry label in its production notes only
# (“CTA  Talk About a Watch or Jewellery Brief”): the closing panel carries it.
for p in panels:
    if p['id'].startswith('S13-') and p['headline'] == 'Talk It Through' and not any(b['type'] == 'cta' for b in p['blocks']):
        m = re.search(r'^CTA\s+(.+)$', p.get('notes') or '', re.M)
        if m:
            p['blocks'].append(dict(type='cta', labels=[m.group(1).strip()]))
            p['cta_from_notes'] = True
SITE = load(HV, 'build', 'site-pages.json')
ESSAYS = load(HV, 'build', 'essays.json')


# ── Routes ────────────────────────────────────────────────────────────────
def canonical(route):
    r = route.rstrip('/') or '/'
    m = re.match(r'^/solutions/by-(need|industry|function)/(.+)$', r)
    if m:
        return '/solutions/' + m.group(2)
    table = {
        '/solutions/by-need': '/solutions#by-need',
        '/observatory': '/the-observatory',
        '/observatory/the-field': '/the-observatory/field',
        '/observatory/private-briefing': '/the-observatory/request-a-private-briefing',
        '/research/the-luxury-100': '/research/luxury-100',
        '/research/research-programme': '/research/programme',
        '/future-of-luxury': '/the-future-of-luxury',
        '/future-of-luxury/latest': '/the-future-of-luxury#latest',
        '/future-of-luxury/essays': '/the-future-of-luxury#essays-analysis',
        '/future-of-luxury/subscribe': '/the-future-of-luxury#subscribe',
        '/future-of-luxury/[slug]': '/the-future-of-luxury/{essay}/',
        '/_components/footer': 'footer.php',
    }
    if r in table:
        return table[r]
    if r.startswith('/observatory/'):
        return '/the-observatory/' + r.split('/', 2)[2]
    return r


FAMILY = {
    'solutions': ('sol', 'solutions', 'page-solutions.php'),
    'the-observatory': ('obs', 'observatory', 'page-observatory.php'),
    'programmes': ('prg', 'programmes', 'page-programmes.php'),
    'research': ('res', 'research', 'page-research.php'),
    'about': ('ab', 'about', 'page-about.php'),
    'lets-talk': ('ed', 'contact', 'page-contact.php'),
    'media-enquiries': ('ed', 'contact', 'page-contact.php'),
    'client-access': ('ed', 'utility', 'page-shell.php'),
    'careers': ('ed', 'utility', 'page-shell.php'),
    'privacy': ('ed', 'utility', 'page-shell.php'),
    'terms': ('ed', 'utility', 'page-shell.php'),
}


def vocab(f):
    names = dict(sol=('sol-kicker', 'sol-rows', 'sol-row'), obs=('obs-marker', 'obs-rows', 'obs-row'),
                 prg=('prg-mark', 'prg-questions', 'prg-question'), res=('res-head', 'res-findings', 'res-finding'),
                 ab=('ab-marker', 'ab-pressures', 'ab-pressure'), ed=('ed-marker', 'ed-rows', 'ed-row'))[f]
    return dict(chapter=f'{f}-chapter', marker=names[0], lead=f'{f}-lead', rows=names[1], row=names[2],
                link=f'{f}-link', cta=f'{f}-cta', note=f'{f}-note')


CLS = {f: vocab(f) for f in ('sol', 'obs', 'prg', 'res', 'ab', 'ed')}

# Hero eyebrows follow the site's own wayfinding (Section / Page), not the
# panel's role label; the panel's eyebrow repeats the page name.
LENS = {'need': 'By Need', 'industry': 'By Industry', 'function': 'By Function'}
PROGRAMME_ORDER = ['maison-formation', 'editorial-influence', 'digital-growth', 'client-experience', 'agentic-ai', 'brand-ip-stewardship']
OBS_LABEL = {'for-cmos': 'For CMOs', 'for-family-offices-investors': 'For Family Offices & Investors', 'field': 'The Field',
             'how-it-works': 'How It Works', 'request-a-private-briefing': 'Private Briefing'}
FIXED_EYEBROW = {'/the-observatory': 'The Observatory', '/programmes': 'Programmes', '/research': 'Research',
                 '/research/touch-me-not': 'Research / Book', '/research/luxury-100': 'Research / Annual reading',
                 '/research/white-papers': 'Research / White Papers', '/research/programme': 'Research / Programme',
                 '/about': 'About', '/lets-talk': 'Let’s Talk', '/media-enquiries': 'Media Enquiries'}


def page_title(canon):
    for pg in SITE:
        if pg['path'] == canon:
            return html.unescape(pg['title'])
    return canon.strip('/').split('/')[-1].replace('-', ' ').title()


def hero_eyebrow(route, canon):
    m = re.match(r'^/solutions/by-(need|industry|function)/', route)
    if m:
        return 'Solutions / ' + LENS[m.group(1)]
    if canon in FIXED_EYEBROW:
        return FIXED_EYEBROW[canon]
    last = canon.strip('/').split('/')[-1]
    if canon.startswith('/the-observatory/'):
        return 'The Observatory / ' + OBS_LABEL[last]
    if canon.startswith('/programmes/'):
        return 'Programmes / Programme %02d' % (PROGRAMME_ORDER.index(last) + 1)
    if canon.startswith('/about/'):
        return 'About / ' + page_title(canon)
    return ''


# ── Destinations ──────────────────────────────────────────────────────────
def norm(s):
    return re.sub(r'\s+', ' ', html.unescape(s).replace("'", '’')).strip().lower().lstrip('— ').rstrip('.').strip()


TITLES = {}          # title → {section: path}
for pg in SITE:
    TITLES.setdefault(norm(pg['title']), {})[pg['path'].strip('/').split('/')[0]] = pg['path']
ESSAY_BY_TITLE = {norm(e['title']): e['path'] for e in ESSAYS}

SYNONYMS = {
    'the observatory': '/the-observatory', 'observatory': '/the-observatory', 'the field': '/the-observatory/field',
    'the observatory — the field': '/the-observatory/field', 'the observatory — for family offices & investors': '/the-observatory/for-family-offices-investors',
    'the observatory — for cmos': '/the-observatory/for-cmos', 'for cmos': '/the-observatory/for-cmos',
    'for family offices & investors': '/the-observatory/for-family-offices-investors', 'how it works': '/the-observatory/how-it-works',
    'request a private briefing': '/the-observatory/request-a-private-briefing', 'private briefing': '/the-observatory/request-a-private-briefing',
    'the future of luxury': '/the-future-of-luxury', 'essays & analysis': '/the-future-of-luxury#essays-analysis',
    'the new asian maison': '/research/white-papers', 'white papers': '/research/white-papers', 'research programme': '/research/programme',
    'our research': '/research', 'research': '/research', 'programmes': '/programmes', 'our programmes': '/programmes',
    'solutions': '/solutions', 'about': '/about', 'let’s talk': '/lets-talk', 'how we work': '/about/how-we-work',
    'demand quality check': '/solutions/enhance-desire-without-dilution#s03-p008', 'the demand quality check': '/solutions/enhance-desire-without-dilution#s03-p008',
    'the luxury 100': '/research/luxury-100', 'touch me not': '/research/touch-me-not', 'partners & people': '/about/partners-people',
}

BRIEFING = '/the-observatory/request-a-private-briefing'
LABELS = {
    'explore solutions': '/solutions', 'why it matters': '/about/why-it-matters', 'explore needs': '/solutions#by-need',
    'explore functions': '/solutions#by-function', 'explore industries': '/solutions#by-industry', 'select a subject': '/solutions#by-need',
    'explore the observatory': '/the-observatory', 'request a private briefing': BRIEFING, 'explore programmes': '/programmes',
    'view programmes': '/programmes', 'explore research': '/research', 'explore the research': '/research', 'discover research': '/research',
    'read the essays': '/the-future-of-luxury', 'read the future of luxury': '/the-future-of-luxury',
    'more from the future of luxury': '/the-future-of-luxury', 'explore the publication': '/the-future-of-luxury',
    'subscribe': '/the-future-of-luxury#subscribe', 'browse essays': '/the-future-of-luxury#essays-analysis',
    'explore essays & analysis': '/the-future-of-luxury#essays-analysis', 'search essays & analysis': '/the-future-of-luxury#essays-analysis',
    'how we work': '/about/how-we-work', 'explore how we work': '/about/how-we-work', 'meet the people': '/about/partners-people',
    'explore the maison formation programme': '/programmes/maison-formation', 'explore maison formation': '/programmes/maison-formation',
    'try the demand quality check': '/solutions/enhance-desire-without-dilution#s03-p008',
    'explore the demand quality check': '/solutions/enhance-desire-without-dilution#s03-p008',
    'check demand quality': '/solutions/enhance-desire-without-dilution#s03-p008', 'explore demand quality': '/solutions/enhance-desire-without-dilution#s03-p008',
    'explore desire without dilution': '/solutions/enhance-desire-without-dilution', 'explore demand': '/solutions/enhance-desire-without-dilution',
    'explore the luxury 100': '/research/luxury-100', 'explore the canon': '/research/luxury-100', 'explore the 2027 edition': '/research/luxury-100',
    'about touch me not': '/research/touch-me-not', 'read touch me not': '/research/touch-me-not', 'explore the book': '/research/touch-me-not',
    'about the book': '/research/touch-me-not', 'view the contents': '/research/touch-me-not',
    'explore brand & ip stewardship': '/programmes/brand-ip-stewardship', 'explore icon stewardship': '/solutions/strengthen-an-icon',
    'explore experience design': '/solutions/experience-design', 'explore digital commerce': '/solutions/digital-commerce',
    'explore agentic ai': '/programmes/agentic-ai', 'explore client experience': '/programmes/client-experience',
    'explore maison due diligence': '/solutions/maison-due-diligence', 'explore due diligence': '/solutions/maison-due-diligence',
    'explore patrimony & ip': '/solutions/activate-patrimony-ip', 'explore patrimony': '/solutions/activate-patrimony-ip',
    'explore repositioning': '/solutions/reposition-a-maison', 'explore experience': '/solutions/transform-the-client-experience',
    'explore how the observatory works': '/the-observatory/how-it-works', 'explore how it works': '/the-observatory/how-it-works',
    'read how it works': '/the-observatory/how-it-works', 'see the method': '/the-observatory/how-it-works', 'see how it works': '/the-observatory/how-it-works',
    'how the observatory works': '/the-observatory/how-it-works', 'read the method': '/the-observatory/how-it-works',
    'explore investment & acquisitions': '/solutions/investment-acquisitions', 'explore the investor briefing': '/the-observatory/for-family-offices-investors',
    'explore the field': '/the-observatory/field', 'explore the cmo perspective': '/the-observatory/for-cmos', 'read the cmo perspective': '/the-observatory/for-cmos',
    'explore the investor perspective': '/the-observatory/for-family-offices-investors', 'read the investor perspective': '/the-observatory/for-family-offices-investors',
    'explore white papers': '/research/white-papers', 'browse the papers': '/research/white-papers', 'browse white papers': '/research/white-papers',
    'read the new asian maison': '/research/white-papers', 'read the white paper': '/research/white-papers', 'explore the paper': '/research/white-papers',
    'see the research themes': '/research/programme', 'explore applied ai': '/solutions/applied-ai', 'explore related solutions': '/solutions',
    'explore thought leadership': '/solutions/develop-thought-leadership', 'explore the briefing': '/the-observatory/for-family-offices-investors',
    'find your starting point': '/programmes#s07-p002', 'media enquiries': '/media-enquiries', 'let’s talk': '/lets-talk',
    'email summit': 'mailto:hello@summitcommunication.group',
}
# CTAs whose destination depends on the page they sit on
ROUTE_LABELS = {
    ('/careers', 'explore opportunities'): '#s11-p013',
    ('/careers', 'register your interest'): '/lets-talk?intent=career',
    ('/client-access', 'client sign in'): None,            # no client environment exists yet
    ('/client-access', 'contact summit'): '/lets-talk?topic=client-access',
    ('/privacy', 'read the privacy policy'): '#privacy-policy',
    ('/privacy', 'subscription preferences'): None,          # no preference centre exists yet
    ('/privacy', 'privacy enquiry'): '/lets-talk?topic=privacy',
    ('/terms', 'read the terms'): '#terms-of-use',
    ('/lets-talk', 'start a conversation'): '#s11-p002',
    ('/media-enquiries', 'send a media enquiry'): '#s11-p017',
    ('/the-observatory/request-a-private-briefing', 'request a conversation'): '#s06-p037',
    # a paper is sent by a person after a request, not delivered automatically
    ('/research/white-papers', 'request the paper'): '/lets-talk?topic=white-papers&context=The%20New%20Asian%20Maison',
    ('/research/white-papers', 'register interest'): '/lets-talk?topic=white-papers',
}
AUDIENCE = {'/the-observatory/for-cmos': 'cmo', '/the-observatory/for-family-offices-investors': 'investor'}
TOPIC_OVERRIDE = {'/research/programme': 'research-programme', '/solutions/enhance-desire-without-dilution': 'demand-quality'}
ENQUIRY = re.compile(r'^(discuss|talk|request a (?:private )?conversation|start a conversation|have a conversation|propose|suggest a question|'
                     r'suggest a|ask about|send|or send|contact summit|explore opportunities|register your interest)', re.I)
UNPUBLISHED = re.compile(r'(Charvet|Porsche|911)', re.I)

TOPICS = OrderedDict(general='', privacy='Privacy', **{'client-access': 'Client Access', 'the-future-of-luxury': 'The Future of Luxury'})    # topic slug → the page it names, for Let's Talk
cta_log = []
pending = []


def topic_for(canon):
    return TOPIC_OVERRIDE.get(canon) or canon.strip('/').split('/')[-1] or 'general'


def enquiry_url(canon):
    last = canon.strip('/').split('/')[-1]
    if canon.startswith('/programmes/'):
        TOPICS.setdefault('programme:' + last, page_title(canon))
        return '/lets-talk?programme=' + last
    if canon == '/programmes':
        return '/lets-talk?programme=undecided'
    if canon == BRIEFING:
        return '#s06-p037'
    if canon.startswith('/the-observatory'):
        return BRIEFING + ('?audience=' + AUDIENCE[canon] if canon in AUDIENCE else '')
    if canon == '/' or canon.startswith('/about'):
        return '/lets-talk'
    if canon == '/careers':
        return '/lets-talk?intent=career'
    t = topic_for(canon)
    TOPICS.setdefault(t, page_title(canon))
    return '/lets-talk?topic=' + t


def by_title(t, key):
    t = norm(t)
    if t in ESSAY_BY_TITLE:
        return ESSAY_BY_TITLE[t]
    sections = TITLES.get(t)
    if sections:
        if 'programme' in key and 'programmes' in sections:
            return sections['programmes']
        if ('solution' in key or 'function' in key) and 'solutions' in sections:
            return sections['solutions']
        for s in ('solutions', 'the-observatory', 'research', 'programmes', 'about'):
            if s in sections:
                return sections[s]
        return next(iter(sections.values()))
    return SYNONYMS.get(t)


def destination(label, canon, context_title=None, context_text=''):
    """→ (url or None, status)"""
    key = norm(label)
    if (canon, key) in ROUTE_LABELS:
        url = ROUTE_LABELS[(canon, key)]
        return url, 'mapped' if url else 'not-built'
    essayish = re.search(r'\bessay\b', key) and not re.search(r'essays', key)
    if UNPUBLISHED.search(label) or (essayish and UNPUBLISHED.search(context_text or '')):
        return None, 'pending-essay'
    url = None
    if key in ESSAY_BY_TITLE or norm(re.sub(r'^read ', '', label)) in ESSAY_BY_TITLE:
        url = ESSAY_BY_TITLE.get(key) or ESSAY_BY_TITLE[norm(re.sub(r'^read ', '', label))]
    elif essayish:
        return None, 'pending-essay'
    elif context_title and by_title(context_title, key) and not ENQUIRY.match(key):
        url = by_title(context_title, key)
    elif key in LABELS:
        url = LABELS[key]
    elif ENQUIRY.match(key):
        url = enquiry_url(canon)
    else:
        m = re.match(r'^(explore|read|see|view|about) (.+)$', key)
        if m and by_title(m.group(2), key):
            url = by_title(m.group(2), key)
    if not url:
        return None, 'unresolved'
    if url == BRIEFING and canon in AUDIENCE:
        url += '?audience=' + AUDIENCE[canon]
    if url.split('#')[0].split('?')[0] == canon and '#' not in url:
        return '#self:' + label, 'self-anchor'
    return url, 'mapped'


# ── Core block serialisation ──────────────────────────────────────────────
def jattrs(d):
    """Block comment attributes exactly as WordPress serialises them."""
    if not d:
        return ''
    j = json.dumps(d, ensure_ascii=False, separators=(',', ':'))
    j = j.replace('--', '\\u002d\\u002d').replace('<', '\\u003c').replace('>', '\\u003e').replace('&', '\\u0026').replace('\\"', '\\u0022')
    return ' ' + j


def esc(t):
    return t.replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;') if t else ''


def para(inner, cls=None):
    a = {'className': cls} if cls else {}
    c = f' class="{cls}"' if cls else ''
    return f'<!-- wp:paragraph{jattrs(a)} -->\n<p{c}>{inner}</p>\n<!-- /wp:paragraph -->'


def heading(inner, level=2, cls=None):
    a = {}
    if level != 2:
        a['level'] = level
    if cls:
        a['className'] = cls
    c = 'wp-block-heading' + (f' {cls}' if cls else '')
    return f'<!-- wp:heading{jattrs(a)} -->\n<h{level} class="{c}">{inner}</h{level}>\n<!-- /wp:heading -->'


def group(cls, children, tag='div', anchor=None):
    a = {}
    if tag != 'div':
        a['tagName'] = tag
    a['className'] = cls          # the anchor lives in the id attribute (block support)
    idattr = f' id="{anchor}"' if anchor else ''
    inner = '\n\n'.join(c for c in children if c)
    return f'<!-- wp:group{jattrs(a)} -->\n<{tag}{idattr} class="wp-block-group {cls}">{inner}</{tag}>\n<!-- /wp:group -->'


def details(summary, children, cls):
    inner = '\n\n'.join(children)
    return (f'<!-- wp:details {{"className":"{cls}"}} -->\n<details class="wp-block-details {cls}"><summary>{summary}</summary>'
            f'{inner}</details>\n<!-- /wp:details -->')


def shortcode(code):
    return f'<!-- wp:shortcode -->\n{code}\n<!-- /wp:shortcode -->'


def link(label, url, cls=None):
    c = f' class="{cls}"' if cls else ''
    return f'<a href="{html.escape(url, quote=True)}"{c}>{esc(label)}</a>'


# ── Editorial decisions recorded per panel ────────────────────────────────
FAQ_EYEBROWS = {'questions worth asking', 'good questions', 'faqs', 'faq'}
STATUS_FLAG = re.compile(r'\s*Publication approval required\.?\s*$')
FIELD_SPEC = re.compile(r'(\*\s*$|\s\*\s|^\[ \]|\(optional\)\s*$|^Type of request |^Deadline and time zone)')
RELATED_HEADS = {'worth exploring', 'explore further', 'keep reading', 'continue exploring', 'related questions',
                 'useful starting points', 'new to summit?', 'discover the practice', 'where to go next'}

GUIDES = {'S04-P004': 'choice', 'S04-P009': 'choice', 'S05-P004': 'choice', 'S05-P009': 'choice',
          'S03-P012': 'toggle', 'S05-P015': 'checklist'}
DQC = 'S03-P008'
# A choice that only carries the visitor's selection into the enquiry: the
# options are authored, their 40–60 word responses are not (Pass 03). The
# line promising a response waits for them.
CONTEXT_GUIDES = {'S03-P004': 'Choose one. A short response appears beneath the choices, followed by a tailored enquiry link.'}
FORMS = {'S11-P002': 'general', 'S11-P017': 'media', 'S06-P037': 'briefing'}
ASIDE = {'S11-P003': 'S11-P002', 'S11-P019': 'S11-P017'}           # “Prefer Email?” beside its form
FORM_SPEC_ONLY = {'S11-P013'}                                         # careers: proposal form not yet approved
ESSAYS_PENDING = 'it names essays that are not yet published (Chanel and Charvet; Porsche and Ferrari; Louis Vuitton in China)'
# Panels whose authored proposition stands without the unpublished essays:
# the essay-specific part waits (held-content.json); the rest is shown as written.
PARTIAL = {
    'S04-P016': ('The three essay entries wait for publication; the introduction and the link to the publication are shown.',
                 lambda bl: [x for x in bl if x['type'] not in ('item',) and not (x['type'] == 'p' and x['text'].startswith('On '))]),
    'S11-P021': ('Its second paragraph and “Read the Privacy Policy” presuppose an approved Privacy Policy, which does not exist yet; the opening paragraph is shown, and the site’s factual notice follows.',
                 lambda bl: [x for x in bl if x['type'] == 'p'][:1]),
    'S11-P025': ('Its second paragraph and “Read the Terms” presuppose approved Terms, which do not exist yet; the opening paragraph is shown, and the site’s factual notice follows.',
                 lambda bl: [x for x in bl if x['type'] == 'p'][:1]),
    'S13-P011': ('Its second sentence recommends the unpublished Charvet essay and carries a publication condition; the opening sentence is shown.',
                 lambda bl: [dict(x, text=x['text'].split('. ')[0] + '.', html=None) for x in bl[:1]]),
}
HELD = {
    'S07-P017': 'Proof in the Work cites essays as published work; ' + ESSAYS_PENDING + '.',
    'S13-P032': 'The research note pairs with the Porsche essay and carries a publication-approval condition in its copy.',
    'S11-P023': 'Points to contact details “given in the approved Privacy Policy”, which does not exist yet; the site’s notice gives the current route (Let’s Talk).',
    'S08-P021': 'Describes a research-paper request form; no approved paper or delivery service exists yet (register: “Await final approved PDFs”).',
}
HELD_QA = {
    'Can I create an account myself?': 'The answer is an implementation instruction about self-registration, not visitor copy.',
    'How will application details be used?': 'The answer depends on a recruitment privacy notice that does not exist yet.',
    'Do I need to accept analytics cookies to contact Summit?': 'The answer is a test instruction for the consent configuration.',
    'Can I change a newsletter preference later?': 'The answer describes preference controls that must be connected before launch.',
    'Where is my information stored?': 'The answer says the details cannot yet be supplied; it belongs with the approved Privacy Policy.',
    'Can the terms be updated?': 'The answer describes what the approved Terms should state.',
    'Are the papers free to read?': 'The answer describes a proposed access default, not settled terms.',
}
INTERACTION_GAPS = {
    'S06-P023': 'Approved sector summaries for the selector are not supplied.',
    'S07-P033': 'The two practical prompts for each moment are not supplied.',
    'S07-P041': '“Can This Task Be Delegated?” choices and considerations are not supplied.',
    'S07-P002': 'An issue-to-programme selector is proposed; no mapping is supplied — the six programmes are listed instead.',
    'S10-P032': 'The selector’s routes and enquiry summaries are not supplied.',
}
# The site's existing artwork keeps its place beside the copy it illustrated
TMN_COVER = ('[summit_research_artwork name="touch-me-not-title-page" kind="cover" title="Touch Me Not" subtitle="The Mystery of Luxury" '
             'byline="Gregory Gray & Olivia Dhordain" alt="Title page of Touch Me Not: The Mystery of Luxury, by Gregory Gray and Olivia Dhordain, '
             'with a drawing in gold by Olivia Dhordain" credit="Title page drawing by Olivia Dhordain, ink and gold on paper"]')
ART = {
    'S08-P002': ('top', TMN_COVER),
    'S08-P007': ('top', TMN_COVER),
    'S08-P008': ('end', '[summit_research_artwork name="touch-me-not-mnemosyne" kind="plate" line="Mnemosyne · Hephaestus" '
                        'alt="Ink and gold drawing by Olivia Dhordain for Touch Me Not: Mnemosyne and Hephaestus among a dense field of figures and signs" '
                        'credit="Mnemosyne, goddess of language and memory, mother of the nine Muses · Hephaestus, god of the forge and maker of automata. '
                        'Olivia Dhordain, ink and gold on paper."]'),
    'S08-P013': ('top', '[summit_research_chair empty="83"]'),
    'S10-P008': ('end', TMN_COVER.replace(' credit="Title page drawing by Olivia Dhordain, ink and gold on paper"', '')),
}
# Image blocks the site carried before the handover, restored beside the
# copy that now carries the same subject (core/image, as WordPress stores them)
L100_COVER = (
    '<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"res-cover"} -->\n'
    '<figure class="wp-block-image size-full res-cover"><img src="/wp-content/themes/summit/assets/img/research/luxury-100-2027-cover.jpg" '
    'alt="Cover of The Luxury 100, 2027: a field of one hundred squares with one left empty, and the line Ninety-nine objects. One empty chair."/></figure>\n'
    '<!-- /wp:image -->')
RAW_ART = {
    'S08-P002': ('end', L100_COVER),
    'S08-P014': ('top', L100_COVER),
    'S10-P008': ('end', "<!-- wp:image {\"sizeSlug\":\"full\",\"linkDestination\":\"none\",\"className\":\"ab-figure ab-figure\\u002d\\u002dobject\"} -->\n<figure class=\"wp-block-image size-full ab-figure ab-figure--object\"><img src=\"/wp-content/themes/summit/assets/img/research/luxury-100-2027-cover.jpg\" alt=\"The Luxury 100, 2027 reading: the cover\"/></figure>\n<!-- /wp:image -->"),
    'S10-P007': ('end', "<!-- wp:image {\"sizeSlug\":\"full\",\"linkDestination\":\"none\",\"className\":\"ab-figure ab-figure\\u002d\\u002devidence\"} -->\n<figure class=\"wp-block-image size-full ab-figure ab-figure--evidence\"><img src=\"/wp-content/themes/summit/assets/img/fol/characteristics/01.jpg\" alt=\"The Characteristics of Luxury: What do we mean by luxury?\"/><figcaption class=\"wp-element-caption\">The Characteristics of Luxury, Summit’s visual conversation on what makes luxury endure.</figcaption></figure>\n<!-- /wp:image -->"),
}
PROFILES = {'S10-P024': '[summit_about_portrait name="gregory-gray" initials="GG" alt="Gregory Gray"]',
            'S10-P025': '[summit_about_portrait name="olivia-dhordain" initials="OD" alt="Olivia Dhordain"]'}
LINKED_LISTS = {'S10-P003', 'S07-P002'}      # each entry leads to its own page

NOTICES = {'/privacy': ('privacy-policy', 'privacy.notice.html'), '/terms': ('terms-of-use', 'terms.notice.html')}

panel_map = OrderedDict()


def account(pid, status, route, note=''):
    panel_map[pid] = dict(status=status, route=route, **({'note': note} if note else {}))


def finalise(blocks):
    """Paragraphs after the panel's final CTA are notes; others are text."""
    last = max((i for i, b in enumerate(blocks) if b['type'] == 'cta'), default=-1)
    out = []
    for i, b in enumerate(blocks):
        b = dict(b)
        if b['type'] == 'after_cta':
            b['type'] = 'note' if i > last else 'p'
        out.append(b)
    return out


def text_html(b):
    t = STATUS_FLAG.sub('', b['text'])
    if t != b['text']:
        return esc(t)
    return b.get('html') or esc(b['text'])


def titleish(t):
    t = t.strip()
    return len(t.split()) <= 4 and not re.search(r'[.?!:;,]$', t) and t[:1].isupper()


def split_register(blocks):
    """→ (pre, items, post): text before the register, its items, after it."""
    # a short title set as plain text after an entry's line is the next entry
    # (item, line, Title, line — as in Careers' "Discover the Practice")
    blocks = [dict(b, type='item') if b['type'] == 'p' and 1 < i < len(blocks) - 1 and titleish(b['text'])
              and blocks[i - 1]['type'] == 'p' and blocks[i - 2]['type'] == 'item' and blocks[i + 1]['type'] == 'p' else b
              for i, b in enumerate(blocks)]
    idx = [i for i, b in enumerate(blocks) if b['type'] in ('item', 'row')]
    if not idx:
        return blocks, [], []
    first, last = idx[0], idx[-1]
    items, cur = [], None
    for b in blocks[first:last + 1]:
        if b['type'] == 'item' or (b['type'] == 'p' and cur and cur['paras'] and titleish(b['text'])):
            cur = dict(title=b['text'], html=b.get('html') or esc(b['text']), paras=[], ctas=[])
            items.append(cur)
        elif b['type'] == 'row':
            cur = dict(title=b['cells'][0], html=esc(b['cells'][0]), paras=[dict(type='p', text=x) for x in b['cells'][1:]], ctas=list(b['ctas']))
            items.append(cur)
        elif b['type'] in ('p', 'note'):
            cur['paras'].append(b)
        elif b['type'] == 'cta':
            cur['ctas'] += b['labels']
    tail = blocks[last + 1:]
    last_item, others = items[-1], items[:-1]
    k = max([len(i['paras']) for i in others] or [1])
    j = 0
    while j < len(tail) and tail[j]['type'] in ('p', 'note') and len(last_item['paras']) < k:
        last_item['paras'].append(tail[j])
        j += 1
    if any(i['ctas'] for i in others) and j < len(tail) and tail[j]['type'] == 'cta' and not last_item['ctas']:
        last_item['ctas'] += tail[j]['labels']
        j += 1
    post = tail[j:]
    if len(items) > 1 and not any(i['ctas'] for i in items):
        ctas = [b for b in post if b['type'] == 'cta']
        if len(ctas) == 1 and len(ctas[0]['labels']) == len(items):
            for it, lab in zip(items, ctas[0]['labels']):
                it['ctas'] = [lab]
            post = [b for b in post if b is not ctas[0]]
    return blocks[:first], items, post


class Page:
    def __init__(self, canon, route, ids, fam):
        self.canon, self.route, self.ids, self.fam = canon, route, ids, fam
        self.c = CLS[fam]
        self.title = page_title(canon)

    def cta(self, pid, label, cls, context_title=None, context_text='', place='body'):
        url, st = destination(label, self.canon, context_title, context_text)
        cta_log.append(dict(panel=pid, label=label, route=self.canon, url=url, status=st, place=place,
                            **({'context': context_title} if context_title else {})))
        if not url:
            pending.append(dict(panel=pid, route=self.canon, label=label, status=st))
            return ''
        return para(link(label, url), cls)

    def register(self, pid, items, related):
        rows = []
        for it in items:
            text = ' '.join(b['text'] for b in it['paras'])
            ctas = it['ctas']
            if related and not ctas:
                url = by_title(it['title'], '') or LABELS.get(norm(it['title']))
                if not url:
                    pending.append(dict(panel=pid, route=self.canon, label=it['title'], status='no-live-destination'))
                    continue
                kids = [heading(link(it['title'], url), 3)] + [para(text_html(b)) for b in it['paras']]
                cta_log.append(dict(panel=pid, label=it['title'], route=self.canon, url=url, status='mapped-title-link', place='body'))
                rows.append(group(self.c['row'], kids))
                continue
            links = [self.cta(pid, lab, self.c['link'], it['title'], text) for lab in ctas]
            if ctas and not any(links) and related:
                continue        # its only destination is unpublished: the item waits with it
            kids = [heading(it['html'], 3)] + [para(text_html(b)) for b in it['paras']] + links
            rows.append(group(self.c['row'], kids))
        return group(self.c['rows'], rows) if rows else ''

    def sequence(self, pid, blocks, closing, lead_ok, state):
        out = []
        for b in blocks:
            if b['type'] == 'p':
                if FIELD_SPEC.search(b['text']) and (pid in FORMS or pid in FORM_SPEC_ONLY):
                    continue
                cls = self.c['lead'] if (state['first_p'] and lead_ok) else None
                out.append(para(text_html(b), cls))
                state['first_p'] = False
            elif b['type'] == 'note':
                out.append(para(text_html(b), self.c['note']))
            elif b['type'] == 'cta':
                for lab in b['labels']:
                    primary = closing and not state['cta_done']
                    html_ = self.cta(pid, lab, self.c['cta'] if primary else self.c['link'])
                    if html_:
                        state['cta_done'] = True
                    out.append(html_)
        return out

    def chapter(self, p, variant='', closing=False, blocks=None, anchor=None):
        c = self.c
        blocks = finalise(blocks if blocks is not None else p['blocks'])
        kids = []
        if p['eyebrow'] and norm(p['eyebrow']) not in ('opening', norm(p['headline'])):
            kids.append(para(esc(p['eyebrow']), c['marker']))
        if p['id'] in PROFILES:
            return self.profile(p, blocks)
        # About sets each chapter's headline in its statement voice
        h2cls = 'sr-only' if p.get('hidden_heading') else ('ab-statement' if self.fam == 'ab' else None)
        kids.append(heading(esc(p['headline']), cls=h2cls))
        art = ART.get(p['id'])
        if art and art[0] == 'top':
            kids.append(shortcode(art[1]))
        if RAW_ART.get(p['id'], ('',))[0] == 'top':
            kids.append(RAW_ART[p['id']][1])
        state = dict(first_p=True, cta_done=False)
        if p['id'] in GUIDES:
            kind = GUIDES[p['id']]
            pre, items, post = split_register(blocks)
            kids += self.sequence(p['id'], pre, False, True, state)
            opts = [group('summit-guide__option', [heading(it['html'], 3)] + [para(text_html(b)) for b in it['paras']]) for it in items]
            guide = [group('summit-guide__options', opts)]
            if kind == 'checklist':
                label = next((l for b in post if b['type'] == 'cta' for l in b['labels']), '')
                guide.append(para(esc(label), 'summit-guide__action'))
                cta_log.append(dict(panel=p['id'], label=label, route=self.canon, url='(in-page checklist)', status='interaction', place='body'))
                post = [b for b in post if b['type'] != 'cta']
            kids.append(group(f'summit-guide summit-guide--{kind}', guide))
            kids += self.sequence(p['id'], post, closing, False, state)
        elif p['id'] in CONTEXT_GUIDES:
            held_line = CONTEXT_GUIDES[p['id']]
            rest = [x for x in blocks if not (x['type'] == 'p' and x['text'].strip() == held_line)]
            labels = next(x for x in rest if x['type'] == 'item')
            kids += self.sequence(p['id'], [x for x in rest if x['type'] == 'p'], False, True, state)
            kids.append(group('summit-guide summit-guide--context', [para(esc(labels['text']), 'summit-guide__labels')]))
            kids += self.sequence(p['id'], [x for x in rest if x['type'] in ('cta', 'note')], closing, False, state)
        elif p['id'] == DQC:
            pre = [b for b in blocks if b['type'] == 'p'][:1]
            kids += self.sequence(p['id'], pre, False, True, state)
            start = next(b for b in blocks if b['type'] == 'item')
            intro = blocks[blocks.index(start) + 1]
            kids.append(group('summit-dqc', [heading(esc(start['text']), 3), para(text_html(intro)), shortcode('[summit_demand_quality_check]')]))
            kids += [para(text_html(b), c['note']) for b in blocks if b['type'] == 'note']
            cta_log.append(dict(panel=p['id'], label='Review My Answers', route=self.canon, url='(in-page check)', status='interaction', place='body'))
        else:
            pre, items, post = split_register(blocks)
            related = norm(p['headline']) in RELATED_HEADS or norm(p['eyebrow']) == 'related work' or p['id'] in LINKED_LISTS
            lead_ok = bool(items) or sum(1 for b in blocks if b['type'] == 'p') > 1
            kids += self.sequence(p['id'], pre, closing, lead_ok, state)
            if items:
                kids.append(self.register(p['id'], items, related))
            kids += self.sequence(p['id'], post, closing, False, state)
        if art and art[0] == 'end':
            kids.append(shortcode(art[1]))
        raw = RAW_ART.get(p['id'])
        if raw and raw[0] == 'end':
            kids.append(raw[1])
        return group(f"{c['chapter']} {variant}".strip(), kids, tag='section', anchor=anchor or p['id'].lower())

    def profile(self, p, blocks):
        """A person: portrait, role, name, account — the About profile."""
        paras = [b for b in blocks if b['type'] == 'p']
        text = [para(text_html(paras[0]), 'ab-profile__role'), heading(esc(p['headline']), cls='ab-profile__name')]
        text += [para(text_html(b)) for b in paras[1:]]
        text += [self.cta(p['id'], lab, self.c['link']) for b in blocks if b['type'] == 'cta' for lab in b['labels']]
        inner = group('ab-profile', [shortcode(PROFILES[p['id']]), group('ab-profile__text', text)])
        return group(self.c['chapter'], [inner], tag='section', anchor=p['id'].lower())

    def faq(self, grp):
        c = self.c
        head = grp[0]
        eb = head['eyebrow'].strip()
        kids = []
        if norm(head['headline']) == 'questions we hear':
            kids += [para(esc(eb or 'FAQs'), c['marker']), heading(esc(head['headline']))]
        elif norm(eb) in FAQ_EYEBROWS:
            kids.append(heading(esc(eb), cls='ab-statement' if self.fam == 'ab' else None))
        else:
            kids.append(heading('FAQs', cls='sr-only'))       # the handover's own label for these sections
        items = []
        for p in grp:
            for q, answer in faq_pairs(p):
                if q in HELD_QA:
                    account(p['id'] + ' · ' + q, 'held (internal note)', self.canon, HELD_QA[q])
                    continue
                items.append(details(esc(q), [para(a) for a in answer], 'summit-faq__item'))
        kids.append(group('summit-faq', items))
        return group(f"{c['chapter']} {c['chapter']}--faq", kids, tag='section', anchor=head['id'].lower())

    def form_panel(self, p, aside_panel):
        kind = FORMS[p['id']]
        if kind == 'briefing':
            kids = [para(esc(p['eyebrow']), self.c['marker']), heading(esc(p['headline'])), shortcode('[summit_briefing_form]')]
            return group(f"{self.c['chapter']} {self.c['chapter']}--form", kids, tag='section', anchor=p['id'].lower())
        title = p['headline'].replace('"', '&quot;')
        main = [shortcode(f'[summit_contact_form heading="{title}"]')]
        aside = []
        if aside_panel:
            ab = finalise(aside_panel['blocks'])
            aside.append(heading(esc(aside_panel['headline']), 3, 'ct-label'))
            aside += [para(text_html(b)) for b in ab if b['type'] == 'p']
            for b in ab:
                if b['type'] == 'cta':
                    aside += [self.cta(aside_panel['id'], lab, 'ct-link') for lab in b['labels']]
        aside.append(shortcode('[summit_contact_direct]'))
        return group('ct-panel', [group('ct-main', main), group('ct-aside', aside, tag='aside')], tag='section', anchor=p['id'].lower())


def is_faq(p):
    if norm(p['eyebrow']) in FAQ_EYEBROWS:
        return True
    q = p['headline'].strip().endswith('?')
    items = [b for b in p['blocks'] if b['type'] == 'item']
    return bool(q and items and all(b['text'].strip().endswith('?') for b in items) and not any(b['type'] == 'cta' for b in p['blocks']))


def faq_pairs(p):
    pairs, cur = [], None
    if p['headline'].strip().endswith('?'):
        cur = [p['headline'], []]
        pairs.append(cur)
    for b in finalise(p['blocks']):
        if b['type'] == 'item' and b['text'].strip().endswith('?'):
            cur = [b['text'], []]
            pairs.append(cur)
        elif b['type'] in ('p', 'note') and cur is not None:
            cur[1].append(text_html(b))
    return pairs


# ── Pages ─────────────────────────────────────────────────────────────────
page_map = OrderedDict()
for p in panels:
    page_map.setdefault(p['path'], []).append(p['id'])

TEMPLATE_ROUTES = {
    '/': ('front page', 'front-page.php', 'rendered (front page template)'),
    '/solutions': ('Page', 'page-solutions.php', 'rendered (Solutions overview)'),
    '/solutions/by-need': ('lens state', 'page-solutions.php — By Need lens', 'merged (Solutions overview, By Need lens)'),
    '/future-of-luxury': ('archive', 'archive-article.php', 'rendered (archive shell)'),
    '/future-of-luxury/latest': ('archive section', 'archive-article.php #latest', 'rendered (archive shell: Latest)'),
    '/future-of-luxury/essays': ('archive section', 'archive-article.php #essays-analysis', 'rendered (archive shell: Essays & Analysis)'),
    '/future-of-luxury/[slug]': ('article template', 'single-article.php', 'rendered (essay template)'),
    '/future-of-luxury/subscribe': ('archive section', 'archive-article.php #subscribe', 'rendered (archive shell: Subscribe)'),
    '/_components/footer': ('component', 'footer.php', 'rendered (footer component)'),
}

route_map, built = [], OrderedDict()
for route, ids in page_map.items():
    canon = canonical(route)
    first = P[ids[0]]
    if route.startswith('/_internal'):
        route_map.append(dict(page=first['page'], proposed=route, canonical=None, type='internal only', template=None,
                              action='internal only', panels=ids))
        for pid in ids:
            account(pid, 'internal/config', route, 'Production note or configuration; it informs the build and is not shown.')
        continue
    if route in TEMPLATE_ROUTES:
        kind, tpl, _ = TEMPLATE_ROUTES[route]
        action = 'merge into parent (lens state)' if route == '/solutions/by-need' else ('map to anchor' if '#' in canon else 'populate template')
        route_map.append(dict(page=first['page'], proposed=route, canonical=canon, type=kind, template=tpl, action=action, panels=ids))
        continue
    top = canon.strip('/').split('/')[0]
    fam, section, template = FAMILY[top]
    slug = top if section in ('contact', 'utility') else (canon.strip('/').split('/')[-1] if '/' in canon.strip('/') else 'overview')
    route_map.append(dict(page=first['page'], proposed=route, canonical=canon, type='Page', template=template, action='populate', panels=ids))
    built[canon] = dict(route=route, ids=ids, fam=fam, section=section, template=template, slug=slug)

manifests = {}


def manifest(section):
    if section not in manifests:
        f = os.path.join(SC, section, 'manifest.json')
        manifests[section] = load(f) if os.path.exists(f) else {'section': section, 'pages': []}
    return manifests[section]


KEEP = ('summit_tone', 'summit_visual', 'summit_utilities', 'summit_form', 'summit_moment', 'summit_related', 'summit_summary')

for canon, b in built.items():
    pg = Page(canon, b['route'], b['ids'], b['fam'])
    man = manifest(b['section'])
    entry = next((e for e in man['pages'] if e['path'] == canon), None)
    if entry is None:
        entry = dict(path=canon, template=b['template'], meta={})
        man['pages'].append(entry)
    # Hero: the first panel
    hero = P[b['ids'][0]]
    hb = finalise(PARTIAL[hero['id']][1](hero['blocks']) if hero['id'] in PARTIAL else hero['blocks'])
    meta = {k: v for k, v in entry.get('meta', {}).items() if k in KEEP}
    meta['summit_eyebrow'] = hero_eyebrow(b['route'], canon)
    meta['summit_headline'] = hero['headline']
    meta['summit_hero_panel'] = hero['id']          # the slot a delivered opening image fills
    paras = [x for x in hb if x['type'] == 'p']
    if paras:
        meta['summit_standfirst'] = paras[0]['text']
    if len(paras) > 1:
        meta['summit_intro'] = '\n\n'.join(x['text'] for x in paras[1:])
    hero_ctas = [l for x in hb if x['type'] == 'cta' for l in x['labels']]
    if not hero_ctas and hero['id'].startswith('S13-'):
        # Pass 13 names the page's primary CTA in its notes (hero, mid-page, bottom)
        for pid in b['ids']:
            m = re.search(r'^PRIMARY CTA\s+(.+?)\s*→', P[pid].get('notes') or '', re.M)
            if m:
                hero_ctas = [m.group(1).strip()]
                break
    resolved = []
    for lab in hero_ctas:
        url, st = destination(lab, canon)
        cta_log.append(dict(panel=hero['id'], label=lab, route=canon, url=url, status=st, place='hero'))
        if url:
            resolved.append((lab, url))
        else:
            pending.append(dict(panel=hero['id'], route=canon, label=lab, status=st))
    for (lab, url), (kl, ku) in zip(resolved[:2], (('summit_cta_label', 'summit_cta_url'), ('summit_secondary_label', 'summit_secondary_url'))):
        meta[kl], meta[ku] = lab, url
    entry['meta'] = meta
    entry['template'] = b['template']
    entry['source'] = f"{b['slug']}.blocks.html"
    entry.setdefault('title', pg.title)
    account(hero['id'], 'rendered (hero; in part)' if hero['id'] in PARTIAL else 'rendered (hero)', canon, PARTIAL[hero['id']][0] if hero['id'] in PARTIAL else '')

    out = []
    rest = [P[i] for i in b['ids'][1:]]
    k = 0
    while k < len(rest):
        p = rest[k]
        last = k == len(rest) - 1
        if p['id'] in HELD:
            account(p['id'], 'held', canon, HELD[p['id']])
        elif p['id'] in ASIDE:
            pass        # set beside its form
        elif p['id'] in FORMS:
            aside_id = next((a for a, f in ASIDE.items() if f == p['id']), None)
            aside = P[aside_id] if aside_id else None
            out.append(pg.form_panel(p, aside))
            account(p['id'], 'rendered (form)', canon, 'Fields are implemented by the form; their labels follow this panel.')
            if aside:
                account(aside['id'], 'merged', canon, f'Set beside the form ({p["id"]}).')
        elif is_faq(p):
            grp = [p]
            while k + 1 < len(rest) and is_faq(rest[k + 1]) and rest[k + 1]['eyebrow'] == p['eyebrow'] and norm(p['headline']) != 'questions we hear':
                k += 1
                grp.append(rest[k])
            out.append(pg.faq(grp))
            account(grp[0]['id'], 'rendered (FAQ)', canon)
            for g in grp[1:]:
                account(g['id'], 'merged', canon, f'Questions joined to the FAQ at {grp[0]["id"]}.')
        elif p['id'] == 'S03-P005':
            # the copy holds two panels: a Further Reading link (to an essay
            # that is not yet published) and the page's closing invitation
            blocks = p['blocks']
            at = next(i for i, x in enumerate(blocks) if x['type'] == 'item' and x['text'].strip() == x['text'].strip().upper())
            close = dict(p, eyebrow=blocks[at]['text'], headline=blocks[at + 1]['text'])
            out.append(pg.chapter(close, CLS[pg.fam]['chapter'] + '--close', True, blocks[at + 2:], anchor='s03-p005'))
            account(p['id'], 'rendered (closing part); Further Reading held', canon,
                    'The Further Reading part introduces the Porsche essay, which is not published; it waits for it.')
            pending.append(dict(panel=p['id'], route=canon, label='Further Reading — Porsche Called 911. Ferrari Never Forgot It Was Luxury', status='pending-essay'))
        else:
            eb = norm(p['eyebrow'])
            first_cta = next((l for x in p['blocks'] if x['type'] == 'cta' for l in x['labels']), '')
            closing = eb == 'let’s talk' or (last and bool(ENQUIRY.match(norm(first_cta))))
            variant = ''
            if closing:
                variant = CLS[pg.fam]['chapter'] + '--close'
            elif eb in ('research', 'the observatory') or norm(p['headline']) == 'where research helps':
                variant = CLS[pg.fam]['chapter'] + '--tinted'
            out.append(pg.chapter(p, variant, closing, blocks=PARTIAL[p['id']][1](p['blocks']) if p['id'] in PARTIAL else None))
            note = INTERACTION_GAPS.get(p['id'], '')
            status = 'rendered'
            if p['id'] in PARTIAL:
                status, note = 'rendered (in part; essay-specific copy held)', PARTIAL[p['id']][0]
            if p['id'] in GUIDES or p['id'] == DQC:
                status = 'rendered (interaction)'
            elif p['id'] in CONTEXT_GUIDES:
                status = 'rendered (interaction: choice carried to the enquiry; responses not authored)'
                note = 'The four choices carry the selection to Let’s Talk. Held until the 40–60 word responses are authored: “' + CONTEXT_GUIDES[p['id']] + '”'
            elif note:
                status = 'rendered (editorial version; interaction logic not supplied)'
            elif p.get('interaction'):
                status = 'rendered (editorial version; optional interaction not built)'
                note = p['interaction'][:240]
            account(p['id'], status, canon, note)
        k += 1
    if canon in NOTICES:
        anchor, src = NOTICES[canon]
        with open(os.path.join(SC, b['section'], src), encoding='utf-8') as f:
            notice = f.read().strip()
        out.append(group(f"{CLS[pg.fam]['chapter']} {CLS[pg.fam]['chapter']}--notice", [notice], tag='section', anchor=anchor))
    body = '\n\n'.join(x for x in out if x)       # exactly as WordPress stores it: no trailing newline
    # in-page links: to a named section, or the first section after the hero
    anchors = re.findall(r' id="([a-z0-9-]+)"', body)

    def self_anchor(m):
        label = html.unescape(m.group(1))
        words = [w.rstrip('s') for w in re.findall(r'[a-z0-9]+', label.lower()) if w not in
                 {'the', 'a', 'an', 'explore', 'read', 'see', 'browse', 'about', 'view', 'our', 'of', 'to', 'and'}]
        for pid in b['ids'][1:]:
            hay = (P[pid]['eyebrow'] + ' ' + P[pid]['headline']).lower()
            if pid.lower() in anchors and words and all(w in hay for w in words):
                return f'href="#{pid.lower()}"'
        return f'href="#{anchors[0]}"' if anchors else 'href="#main"'

    body = re.sub(r'href="#self:([^"]+)"', self_anchor, body)
    for kl, ku in (('summit_cta_label', 'summit_cta_url'), ('summit_secondary_label', 'summit_secondary_url')):
        if meta.get(ku, '').startswith('#self:'):
            meta[ku] = self_anchor(re.match(r'#self:(.*)', meta[ku]))[6:-1]
    for c in cta_log:
        if c['route'] == canon and (c['url'] or '').startswith('#self:'):
            c['url'], c['status'] = 'in-page section', 'self-anchor'
    missing = [h for h in re.findall(r'href="#([a-z0-9-]+)"', body) if h not in anchors]
    if missing:
        print('WARN missing anchors on', canon, missing)
    os.makedirs(os.path.join(SC, b['section']), exist_ok=True)
    with open(os.path.join(SC, b['section'], entry['source']), 'w', encoding='utf-8') as f:
        f.write(body)

# ── Solutions overview: its hero, the lens lines and the By Need lens ───
# The copy gives the hero no CTA; the Production Register's page CTA map
# does (Find Your Starting Point → By Need; Start a Conversation).
sol = manifest('solutions')
ov = next(e for e in sol['pages'] if e['path'] == '/solutions')
h, need = P['S02-P001'], P['S02-P002']
lines = {norm(b['text']): nb['text'] for b, nb in zip(h['blocks'], h['blocks'][1:]) if b['type'] == 'item'}
ov_meta = {k: v for k, v in ov['meta'].items() if k in KEEP}
ov_meta.update(summit_eyebrow='Solutions', summit_headline=h['headline'],
               summit_standfirst=next(b['text'] for b in h['blocks'] if b['type'] == 'p'),
               summit_cta_label='Find Your Starting Point', summit_cta_url='#by-need',
               summit_secondary_label='Start a Conversation', summit_secondary_url='/lets-talk', summit_hero_panel='S02-P001')
ov_meta['summit_lenses'] = json.dumps({
    'by-need': dict(line=lines['by need'], title=need['headline'], intro=next(b['text'] for b in need['blocks'] if b['type'] == 'p')),
    'by-function': dict(line=lines['by function']),
    'by-industry': dict(line=lines['by industry']),
}, ensure_ascii=False)
ov.update(meta=ov_meta, source='overview.blocks.html', template='page-solutions.php')
with open(os.path.join(SC, 'solutions', 'overview.blocks.html'), 'w', encoding='utf-8') as f:
    f.write(shortcode('[summit_solutions_ways]'))
for lab, url in (('Find Your Starting Point', '#by-need'), ('Start a Conversation', '/lets-talk')):
    cta_log.append(dict(panel='S02-P001', label=lab, route='/solutions', url=url, status='mapped (Production Register CTA map)', place='hero'))
account('S02-P001', 'rendered (hero; lens lines)', '/solutions', 'Hero CTAs from the Production Register page CTA map; the copy gives none.')
account('S02-P002', 'merged (By Need lens intro)', '/solutions#by-need', '“Select a subject” is the lens list itself, not a separate link.')
# each need's line on the lens is its own page's summary
for pid in ('S02-P003', 'S02-P004'):
    p = P[pid]
    pairs = [(p['headline'], next(b['text'] for b in p['blocks'] if b['type'] == 'p'))]
    pairs += [(b['text'], nb['text']) for b, nb in zip(p['blocks'], p['blocks'][1:]) if b['type'] == 'item' and nb['type'] == 'p']
    for title, line in pairs:
        path = by_title(title, 'solution')
        entry = next(e for e in sol['pages'] if e['path'] == path)
        entry['meta']['summit_summary'] = line
    account(pid, 'merged (By Need lens: the need summaries)', '/solutions#by-need', 'Each entry is its page’s summit_summary, shown on the lens.')

# ── Home: the eight Master panels, as structured fields for the front page
# template's own parts (front-page.php). Destinations resolved here.
HOME_LINKS = {'Private Enquiries': BRIEFING}
home_panels = OrderedDict()
for pid in page_map['/']:
    p = P[pid]
    blocks = finalise(p['blocks'])
    entry = dict(eyebrow=p['eyebrow'], headline=p['headline'],
                 paras=[b['text'] for b in blocks if b['type'] == 'p'], rows=[], ctas=[])
    for b in blocks:
        if b['type'] == 'row':
            row = dict(title=b['cells'][0], line=' '.join(b['cells'][1:]))
            url = by_title(b['cells'][0], 'programme' if pid == 'S01-P005' else '')
            for lab in b['ctas']:
                u, st = destination(lab, '/')
                cta_log.append(dict(panel=pid, label=lab, route='/', url=u, status=st, place='home'))
                row.update(cta=lab, url=u)
            if 'url' not in row and url:
                row['url'] = url
                cta_log.append(dict(panel=pid, label=row['title'], route='/', url=url, status='mapped-title-link', place='home'))
            entry['rows'].append(row)
        elif b['type'] == 'cta':
            for lab in b['labels']:
                u, st = (HOME_LINKS[lab], 'mapped') if lab in HOME_LINKS else destination(lab, '/')
                cta_log.append(dict(panel=pid, label=lab, route='/', url=u, status=st, place='home'))
                if u:
                    entry['ctas'].append(dict(label=lab, url=u))
        elif b['type'] == 'item':
            # S01-P007 names three essays that are not yet published: the
            # carousel shows the live essays in their place
            pending.append(dict(panel=pid, route='/', label=b['text'].lstrip('— '), status='pending-essay'))
    home_panels[pid] = entry
    account(pid, 'rendered (front page template)', '/', 'Essay titles replaced by the live essays; none of the three is published.' if pid == 'S01-P007' else '')
home = manifest('home')
hp = next(e for e in home['pages'] if e['path'] == '/home')
h1 = home_panels['S01-P001']
hp['meta'].update(home_hero_headline=h1['headline'], home_hero_body=h1['paras'][0],
                  home_hero_cta_label=h1['ctas'][0]['label'], home_hero_cta_url=h1['ctas'][0]['url'],
                  home_hero_cta2_label=h1['ctas'][1]['label'], home_hero_cta2_url=h1['ctas'][1]['url'])
for k in ('home_cap_1_url', 'home_cap_2_url', 'home_cap_3_url', 'home_sectors_body', 'home_vp_headline'):
    hp['meta'].pop(k, None)
hp['meta']['summit_home'] = json.dumps(home_panels, ensure_ascii=False)
home['description'] = ('Home: the front page\'s own fields (front page /home). The hero keeps its ACF fields; '
                       'summit_home holds the eight Master panels (Editorial Handover v1, S01-P001–P008) that the '
                       'front page\'s parts read. Fields only — no block content. Built by tools/editorial-build.py.')

for section, man in manifests.items():
    with open(os.path.join(SC, section, 'manifest.json'), 'w', encoding='utf-8') as f:
        json.dump(man, f, indent=2, ensure_ascii=False)
        f.write('\n')

# The Future of Luxury is a template, not a page: what became of Pass 09
SEARCH = 'Describes archive search, sorting or filters, which the archive does not have; it returns with them.'
FOL = {
    'S09-P002': ('rendered (archive masthead)', 'Headline in the masthead rail beside the nameplate; both paragraphs; both CTAs.'),
    'S09-P003': ('rendered (archive: lead essay label)', '“From the Archive” labels the lead essay; the paragraph is a production instruction.'),
    'S09-P004': ('held', SEARCH),
    'S09-P005': ('rendered (archive: Latest)', 'Heading and both paragraphs; the lead essay carries its own link.'),
    'S09-P006': ('held', 'Its subject list does not match the archive’s categories; the index shows the real ones.'),
    'S09-P007': ('held (internal note)', 'The answer depends on an approval and notification workflow that does not exist yet.'),
    'S09-P008': ('held (internal note)', 'The answer includes an editorial-policy instruction for future contributors.'),
    'S09-P009': ('rendered (archive FAQ)', ''),
    'S09-P010': ('held', SEARCH),
    'S09-P011': ('internal/config', 'Results specification for a searchable archive.'),
    'S09-P012': ('held', 'Suggested journeys without destinations; they return when each question has its essays.'),
    'S09-P013': ('held', SEARCH), 'S09-P014': ('held', SEARCH), 'S09-P015': ('held (internal note)', 'An editorial-policy instruction.'),
    'S09-P016': ('held', SEARCH),
    'S09-P017': ('internal/config', 'Masthead specification; single-article.php already sets title, standfirst, author, date, theme and sharing.'),
    'S09-P018': ('internal/config', 'Reading-experience specification; the essays are preserved as published.'),
    'S09-P019': ('rendered (essay template: Related Reading, Related Work)', '“Explore [Related Solution]” becomes “Explore Solutions”: no essay-to-solution mapping is supplied.'),
    'S09-P020': ('rendered (archive FAQ)', ''), 'S09-P021': ('rendered (archive FAQ)', ''),
    'S09-P022': ('held (internal note)', 'The answer is an editorial-standards instruction.'),
    'S09-P023': ('rendered (archive: Subscribe)', 'The digest choice waits until a digest exists.'),
    'S09-P024': ('held', 'Describes a confirmation email; the local subscription has no double opt-in yet.'),
    'S09-P025': ('rendered (archive: closing invitation)', ''),
    'S09-P026': ('rendered (archive FAQ)', ''), 'S09-P027': ('held (internal note)', 'Preference links do not exist yet.'),
    'S09-P028': ('rendered (archive FAQ)', ''),
    'S09-P029': ('held (internal note)', 'The answer is a consent-handling instruction.'),
}
FOL.update({
    'S11-P029': ('rendered (footer component)', 'Sections, utility and legal links, email and copyright as written; LinkedIn kept from the navigation config.'),
    'S11-P030': ('merged (footer Subscribe route)', 'The footer carries no consent form (register: “No forms requiring consent in footer”); the form lives at The Future of Luxury #subscribe.'),
    'S11-P031': ('rendered (footer component)', 'Microcopy beside the copyright; Back to Top.'),
})
for pid, (status, note) in FOL.items():
    account(pid, status, canonical(P[pid]['path']), note)

# Panels set by templates rather than page content
for route, ids in page_map.items():
    if route in TEMPLATE_ROUTES:
        for pid in ids:
            panel_map.setdefault(pid, dict(status=TEMPLATE_ROUTES[route][2], route=canonical(route)))

# Let's Talk names the page an enquiry came from
contact = manifest('contact')
lt = next(e for e in contact['pages'] if e['path'] == '/lets-talk')
lt['meta']['summit_topics'] = json.dumps(TOPICS, ensure_ascii=False)
with open(os.path.join(SC, 'contact', 'manifest.json'), 'w', encoding='utf-8') as f:
    json.dump(contact, f, indent=2, ensure_ascii=False)
    f.write('\n')


# ── Held content: what waits, on what, and what happens next ─────────────
# status: publication | asset | functionality | legal | product-decision |
#         superseded | internal-only
PUB = 'Publish the essay in The Future of Luxury (its URL then resolves automatically on the next build).'
HELD_REGISTER = [
    # whole panels
    ('S07-P017', 'panel', 'publication', 'Chanel/Charvet, Porsche/Ferrari and Louis Vuitton essays published', PUB + ' The panel cites all three as published work.', False),
    ('S13-P032', 'panel', 'publication', 'Porsche and Ferrari essay published and archive status approved', PUB + ' The copy itself says to link the essay only once approved.', False),
    ('S08-P021', 'panel', 'asset', 'An approved white paper (PDF) and a way to deliver it', 'Supply the approved paper; then show the panel with a request form that delivers it (register: “Await final approved PDFs”).', False),
    ('S09-P004', 'panel', 'product-decision', 'Archive search', 'Decide whether the archive needs search at its present size (24 essays); the index by subject serves it now.', False),
    ('S09-P006', 'panel', 'superseded', 'The archive’s own subject index', 'Its subject list differs from the live categories; reconcile the taxonomy if the editorial list should replace them.', False),
    ('S09-P007', 'panel', 'functionality', 'New-essay notifications from the mailing provider', 'Connect the mailing provider; then the answer about notifications is true.', False),
    ('S09-P008', 'panel', 'internal-only', None, 'Editorial policy for guest contributors — keep in the editorial guide.', False),
    ('S09-P010', 'panel', 'product-decision', 'Archive search', 'As S09-P004.', False),
    ('S09-P012', 'panel', 'product-decision', 'Curated reading journeys', 'Choose the essays for each of the four questions; then the journeys can link.', False),
    ('S09-P013', 'panel', 'product-decision', 'Archive search', 'As S09-P004.', False),
    ('S09-P014', 'panel', 'product-decision', 'Separate theme and sector filters', 'Decide on archive filters; the answer describes them.', False),
    ('S09-P015', 'panel', 'internal-only', None, 'Editorial correction policy — keep in the editorial guide.', False),
    ('S09-P016', 'panel', 'product-decision', 'Archive search', 'As S09-P004.', False),
    ('S09-P022', 'panel', 'internal-only', None, 'Editorial standards instruction — keep in the editorial guide.', False),
    ('S09-P024', 'panel', 'functionality', 'A confirmation email (double opt-in) from the mailing provider', 'Connect the provider’s confirmation flow; then show this as the subscribe success state.', False),
    ('S09-P027', 'panel', 'functionality', 'Preference and unsubscribe links in each edition', 'Connect the provider’s preference centre.', False),
    ('S09-P029', 'panel', 'internal-only', None, 'Consent-handling instruction — keep with the privacy configuration.', False),
    # single answers within a rendered FAQ
    ('S11-P023', 'panel', 'legal', 'The approved Privacy Policy', 'Publish with the approved policy; its CTA (Privacy Enquiry) then joins it.', False),
    ('S11-P021', 'part: second paragraph and “Read the Privacy Policy”', 'legal', 'The approved Privacy Policy', 'Restore both when the policy is published on this page.', False),
    ('S11-P025', 'part: second paragraph and “Read the Terms”', 'legal', 'The approved Terms', 'Restore both when the Terms are published on this page.', False),
    ('S08-P022', 'answer: Are the papers free to read?', 'product-decision', 'A decision on paper access terms', 'Settle the access terms; the answer currently describes a proposed default.', False),
    ('S11-P010', 'answer: Can I create an account myself?', 'functionality', 'A client environment and its onboarding', 'Answer once Client Access exists.', False),
    ('S11-P015', 'answer: How will application details be used?', 'legal', 'A recruitment privacy notice', 'Publish with the approved notice.', False),
    ('S11-P024', 'answer: Do I need to accept analytics cookies to contact Summit?', 'legal', 'The production consent configuration', 'Answer once the cookie and consent set-up is final and tested.', False),
    ('S11-P024', 'answer: Can I change a newsletter preference later?', 'functionality', 'Provider preference controls', 'As S09-P027.', False),
    ('S11-P024', 'answer: Where is my information stored?', 'legal', 'The approved Privacy Policy', 'Publish with the approved policy.', False),
    ('S11-P028', 'answer: Can the terms be updated?', 'legal', 'The approved Terms', 'Publish with the approved Terms.', False),
    # parts of rendered panels
    ('S01-P007', 'part: three named essays', 'publication', 'The three essays published', 'Home shows the live essays meanwhile; restore the named three when published.', False),
    ('S03-P005', 'part: Further Reading (Porsche essay)', 'publication', 'Porsche and Ferrari essay published', PUB + ' The page’s closing invitation is shown.', False),
    ('S03-P009', 'part: CTA to the Charvet essay', 'publication', 'Chanel/Charvet essay published', PUB, False),
    ('S04-P003', 'part: CTA to the Charvet essay', 'publication', 'Chanel/Charvet essay published', PUB, False),
    ('S04-P016', 'part: three essay entries', 'publication', 'The three essays published', PUB + ' The introduction and link to the publication are shown.', False),
    ('S13-P011', 'part: second sentence (Charvet essay)', 'publication', 'Chanel/Charvet essay published and archive approval', PUB, False),
    ('S13-P012', 'part: related essay entry', 'publication', 'Chanel/Charvet essay published', PUB, False),
    ('S13-P033', 'part: related essay entry', 'publication', 'Porsche and Ferrari essay published', PUB, False),
    ('S03-P004', 'part: “A short response appears…” line', 'functionality', 'Four authored 40–60 word responses', 'Author the responses (interactions.json); then the line and the responses appear together.', False),
    ('S11-P007', 'part: Client Sign In', 'functionality', 'An authenticated client environment', 'Link the CTA when a real sign-in exists; no inert button meanwhile.', False),
    ('S11-P022', 'part: Subscription Preferences', 'functionality', 'Provider preference centre', 'As S09-P027.', False),
    ('S09-P023', 'part: digest choice', 'functionality', 'A monthly digest from the provider', 'Offer the choice once a digest exists.', False),
]
held_out = []
for pid, scope, status, dependency, action, coherence in HELD_REGISTER:
    assert pid in P, pid
    held_out.append(dict(panel=pid, scope=scope, route=canonical(P[pid]['path']), headline=P[pid]['headline'],
                         reason=((panel_map.get(pid + ' · ' + scope[8:]) if scope.startswith('answer: ') else panel_map.get(pid) if scope == 'panel' else None)
                                 or {}).get('note', '') or 'Depends on: ' + (dependency or 'editorial guidance only') + '.',
                         status=status, dependency=dependency,
                         future_action=action, affects_page_coherence=coherence))
assert {h['panel'] for h in held_out if h['scope'] == 'panel'} == {k for k, v in panel_map.items() if v['status'].startswith('held') and ' · ' not in k}, 'held register out of step with the panel map'

def dump(name, data):
    with open(os.path.join(HV, name), 'w', encoding='utf-8') as f:
        json.dump(data, f, indent=1, ensure_ascii=False)
        f.write('\n')


dump('route-map.json', route_map)
dump('panel-map.json', panel_map)
dump('cta-map.json', dict(ctas=cta_log, topics=TOPICS, pending=pending))
dump('held-content.json', dict(
    description='Everything the site deliberately does not show yet, why, and what activates it. Statuses: publication, asset, functionality, legal, product-decision, superseded, internal-only.',
    counts=dict(Counter(h['status'] for h in held_out)), held=held_out))

print('pages built:', len(built))
print('panels:', Counter(v['status'].split(' (')[0].split(';')[0] for k, v in panel_map.items() if ' · ' not in k), 'of', len(panels))
print('held questions:', sum(1 for k in panel_map if ' · ' in k))
print('ctas:', Counter(c['status'] for c in cta_log))
print('unresolved:', sorted({(c['route'], c['label']) for c in cta_log if c['status'] == 'unresolved'}))
print('pending:', [(x['route'], x['label']) for x in pending])
