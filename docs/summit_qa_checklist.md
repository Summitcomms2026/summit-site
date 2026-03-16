# summit_qa_checklist.md

## Purpose of this file

This file defines Summit Communication Group’s quality assurance checklist for staging, launch and post-launch review.

It governs:
- visual QA
- responsive QA
- content QA
- CMS usability QA
- SEO QA
- schema QA
- performance QA
- accessibility QA
- form and CRM QA
- video and podcast QA
- analytics QA
- browser QA
- launch readiness
- post-launch verification

If:
- `summit_soul.md` defines what Summit is
- `summit_voice.md` defines how Summit sounds
- `summit_site_architecture.md` defines how Summit is organised
- `summit_build_system.md` defines how Summit is built
- `summit_design_system.md` defines how Summit looks and feels
- `summit_component_library.md` defines the reusable interface parts
- `summit_content_model.md` defines the structured content objects
- `summit_measurement_plan.md` defines how success is measured

then this file defines how Summit determines whether the work is genuinely ready.

This is the source of truth for quality control and launch discipline.

**Rule:**  
A page is not finished because it exists.  
A page is finished when it works, reads, measures and behaves as intended.

---

## QA Philosophy

Summit’s QA process should protect four things at once:

1. **Brand quality**  
   Does it feel like Summit?

2. **Technical quality**  
   Does it work properly?

3. **Editorial quality**  
   Does it read well and make sense?

4. **Commercial quality**  
   Does it support conversion, authority and trust?

QA is not the last-minute search for bugs by tired people in dim light.  
It is the final discipline that stops a premium project leaving the building half-dressed.

---

## QA Stages

### 1. Component QA
Check components individually before page assembly.

### 2. Template QA
Check full templates using realistic content.

### 3. Page QA
Check each real page in staging.

### 4. Sitewide QA
Check navigation, links, consistency, metadata, performance and cross-page logic.

### 5. Launch QA
Check production environment, redirects, indexing, forms, analytics and integrations.

### 6. Post-Launch QA
Check live behaviour after release.

---

## QA Status Labels

Use these labels internally:

- `PASS`
- `MINOR FIX`
- `MAJOR FIX`
- `BLOCKER`
- `DEFERRED`

### Definitions

**PASS**  
Ready with no material issues.

**MINOR FIX**  
Small issue that should be corrected but does not block launch.

**MAJOR FIX**  
Significant issue that weakens quality, UX or function and should be resolved before launch.

**BLOCKER**  
Launch should not proceed until this is resolved.

**DEFERRED**  
Known issue intentionally postponed to later phase, documented clearly.

---

## Global QA Rules

Before launch, every major page and template must be checked for:
- brand fit
- structural clarity
- responsive behaviour
- content quality
- accessibility
- metadata
- measurement
- performance
- technical integrity

**Rule:**  
If a problem appears in more than one place, it is rarely a page problem.  
It is usually a system problem wearing a page’s name badge.

---

## 1. Visual QA Checklist

### Brand and Design Fit
- Does the page feel recognisably like Summit?
- Is the layout calm, premium and controlled?
- Does negative space feel intentional rather than empty?
- Does the dark/light rhythm feel deliberate?
- Does the page avoid luxury cliché visuals?
- Does the interface feel more editorial than templated?

### Typography
- Is the heading hierarchy correct and visually clear?
- Are line lengths readable?
- Is body text spaced comfortably?
- Are emphasis styles used with restraint?
- Are serif emphasis moments consistent where used?
- Do headings, labels and metadata feel coherent across pages?

### Component Consistency
- Do repeated components look and behave consistently?
- Are cards aligned properly?
- Are spacing relationships consistent across similar sections?
- Are button styles consistent?
- Are link styles consistent?
- Are form styles consistent?

### Motion and Interaction
- Does motion feel calm and precise?
- Are hover states elegant rather than noisy?
- Are reveals and transitions smooth?
- Does the mega menu feel like a designed environment?
- Does motion support hierarchy rather than distract from it?

### Visual Polish
- Are images cropped properly?
- Are video frames or placeholders visually clean?
- Are icons and micro-elements aligned?
- Are empty states or fallback states visually acceptable?
- Are there any awkward gaps, overlaps or broken alignments?

---

## 2. Responsive QA Checklist

Test at minimum:
- desktop
- laptop
- tablet
- mobile

### Navigation
- Does sticky top navigation remain clear at every breakpoint?
- Is the order correct: Menu / Brand / Design Tomorrow?
- Does the mega menu open and close properly on mobile?
- Does the bottom utility layer remain legible and usable?
- Are tap targets large enough?

### Layout
- Does hierarchy survive on mobile?
- Do columns collapse cleanly?
- Do cards stack well?
- Do split media-text layouts remain elegant when stacked?
- Are there awkward tablet states?

### Typography
- Are display headlines still readable on smaller screens?
- Does body copy remain comfortable to read?
- Do metadata lines wrap cleanly?
- Are buttons still legible and usable?

### Media
- Do videos scale correctly?
- Do images crop or resize acceptably?
- Do embeds maintain aspect ratio?
- Do transcript and article layouts remain readable on mobile?

### Forms
- Are fields easy to use on mobile?
- Are labels readable?
- Does form validation behave well?
- Are consent or helper notes readable?

---

## 3. Content QA Checklist

### General Copy Quality
- Is the tone consistent with Summit voice?
- Is spelling in English / British form where intended?
- Are grammar and punctuation clean?
- Are headlines sharp and on-brand?
- Is there any filler copy that should be tightened?

### Structural Quality
- Does the page have a clear purpose?
- Is the headline doing the right job?
- Is body copy sequenced logically?
- Are section intros clear?
- Do CTAs make sense in context?

### Editorial Pages
- Are article intros strong?
- Is subheading structure clean?
- Are pull quotes placed intelligently?
- Are related links relevant?
- Does the article still read well without social context?

### Podcast Pages
- Is the episode summary strong?
- Is guest information accurate?
- Is the transcript readable and cleaned?
- Are show notes useful rather than perfunctory?

### Case Studies
- Is the strategic narrative clear?
- Is the work presented honestly and selectively?
- Does the case study feel specific rather than generic?
- Are challenge, strategic idea, build and outcome sections all present?

### Metadata on Page
- Are dates correct?
- Is read time sensible?
- Are labels and categories accurate?
- Are CTA labels correct?

---

## 4. CMS Usability QA Checklist

### Editor Experience
- Can editors tell what content type they are editing?
- Are the right fields visible for the right content object?
- Are irrelevant fields hidden?
- Is field naming clear?
- Are instructions or field descriptions helpful where needed?

### Structured Content Integrity
- Are required fields enforced where appropriate?
- Are relationships easy to select?
- Are taxonomy fields clean and usable?
- Is slug control sensible?
- Is SEO metadata easy to complete?

### Workflow
- Can editors save drafts cleanly?
- Can content be previewed reliably?
- Can updates be made without breaking layout logic?
- Are reusable components behaving predictably in the CMS?
- Is there any admin clutter that should be removed?

### Role and Permissions
- Are access permissions appropriate?
- Can contributors do what they need without risking structural damage?
- Are higher-risk controls limited to the right people?

---

## 5. Navigation and Sitewide UX QA Checklist

### Navigation Logic
- Do all primary navigation links work?
- Do all utility navigation links work?
- Are active states correct where used?
- Does the mega menu reflect the correct content and hierarchy?
- Is the Start a Conversation / Design Tomorrow route consistent?

### Sitewide Journey Logic
- Can users move logically between:
  - service pages
  - case studies
  - articles
  - podcast pages
  - contact routes

- Are related content blocks actually relevant?
- Is there unnecessary dead-end behaviour?
- Do key pages support next-step movement?

### Internal Linking
- Do internal links work?
- Are no pages orphaned accidentally?
- Are article-to-service and service-to-case-study links functioning?
- Are related episodes and related articles linked correctly?

---

## 6. SEO QA Checklist

### Technical SEO
- Does every page have one clear H1?
- Is heading hierarchy valid?
- Are URLs clean and correct?
- Are canonical tags set correctly?
- Are index / noindex settings correct?
- Is the XML sitemap behaving correctly?
- Are robots directives correct?

### Metadata
- Does every key page have:
  - SEO title
  - meta description
  - Open Graph title
  - Open Graph description
  - Open Graph image

- Are metadata fields unique where they should be?
- Are titles and descriptions sensible in length?

### Archive and Content Logic
- Are article categories correct?
- Are taxonomies controlled and not messy?
- Are duplicate or thin pages being indexed unintentionally?
- Are cornerstone pages identifiable and internally supported?

### Search Readiness
- Are priority service pages optimised properly?
- Are article clusters internally linked?
- Are case studies indexable where intended?
- Are redirects in place for replaced URLs?

---

## 7. Schema QA Checklist

### Core Schema
- Is Organization schema present and correct?
- Is ProfessionalService schema present where intended?
- Is BreadcrumbList working correctly?
- Are ContactPoint details correct?

### Editorial Schema
- Are Article / BlogPosting objects rendering correctly?
- Are author fields mapped properly?
- Are publish and updated dates correct?

### Podcast Schema
- Is PodcastSeries present where intended?
- Is PodcastEpisode present on episode pages?
- Are AudioObject / VideoObject fields valid where used?

### Work / Creative Schema
- Are CreativeWork or equivalent objects mapped correctly where intended?

### General Rule
- Is schema tied to real content fields rather than hard-coded nonsense?
- Are there any duplicate or conflicting schema objects?

---

## 8. Performance QA Checklist

### Core Performance
- Are pages loading quickly enough?
- Are large images compressed appropriately?
- Are videos handled efficiently?
- Are scripts controlled?
- Is there unnecessary front-end bloat?

### Real User Experience
- Does the homepage feel responsive?
- Do article pages remain fast despite long content?
- Do episode pages remain stable despite media and transcript load?
- Does the mega menu open smoothly?
- Are forms responsive?

### Technical Checks
- Are caching rules working?
- Is CDN delivery behaving as expected?
- Are there any obvious layout shifts?
- Are there any blocking resources that should be reduced?

### Rule
Luxury does not excuse slowness.  
“Immersive” is often just a flattering word for “late”.

---

## 9. Accessibility QA Checklist

### Structure
- Is semantic HTML used correctly?
- Is heading order logical?
- Are lists and buttons used appropriately?

### Keyboard and Focus
- Can the site be navigated with a keyboard?
- Are focus states visible?
- Can menus, tabs and forms be used without a mouse?

### Text and Contrast
- Is text readable?
- Is colour contrast acceptable?
- Are links distinguishable?
- Are captions and metadata still legible?

### Media Accessibility
- Do images have alt text?
- Are transcripts available where expected?
- Are video captions or supporting text present where needed?

### Forms
- Are labels correctly associated?
- Are errors understandable?
- Are required fields clear?

---

## 10. Form and CRM QA Checklist

### Form Behaviour
- Do forms submit correctly?
- Are success states clear?
- Are validation messages useful?
- Are spam protections working?

### Routing
- Are enquiry types routing properly?
- Are internal notifications going to the right place?
- Are submissions entering ActiveCampaign correctly where intended?
- Are tags, automations or sequences applied correctly?

### User Experience
- Does the form feel on-brand?
- Does the Connect / Contact tab logic work?
- Are mobile form interactions smooth?

### Data Integrity
- Are submission values captured correctly?
- Are duplicate submissions handled sensibly?
- Is there logging where needed?

---

## 11. Video and Podcast QA Checklist

### JW Player / Video
- Does the player load correctly?
- Do videos play on all relevant devices?
- Are poster frames correct?
- Are controls acceptable?
- Are playback analytics firing where intended?

### Showreels
- Does the showreel support the page rather than overwhelm it?
- Does it perform acceptably?
- Does it fail gracefully if the asset is unavailable?

### Podcast Pages
- Does the episode player work?
- Is the transcript rendered correctly?
- Are guest fields correct?
- Are season and episode relationships correct?
- Are related episodes working?

### Media Integrity
- Are broken embeds avoided?
- Are fallback states sensible?
- Are file or source links current?

---

## 12. Analytics QA Checklist

### Google Analytics
- Is GA4 installed correctly?
- Are pageviews recording?
- Are custom events firing correctly?
- Are primary conversions configured correctly?
- Are duplicate events avoided?

### Event Integrity
- Are CTA clicks tracked correctly?
- Are newsletter sign-ups tracked correctly?
- Are downloads tracked correctly?
- Are video events tracked correctly?
- Are podcast events tracked where intended?

### Attribution Hygiene
- Are UTM links working?
- Are campaign names consistent?
- Is source / medium classification sensible?

### ActiveCampaign
- Are sign-up forms mapped correctly?
- Are automations triggered correctly?
- Are tags and lists applied properly?
- Are internal follow-up flows working?

---

## 13. Browser and Device QA Checklist

Test across major modern environments, including:
- Chrome
- Safari
- Firefox
- Edge
- iPhone Safari
- Android Chrome where relevant

### Checks
- layout consistency
- font rendering
- menu behaviour
- form behaviour
- media playback
- sticky elements
- tabbed modules
- transcript display
- article reading experience

### Rule
A page that works beautifully only in the browser of the person who built it is not finished.  
It is merely loyal.

---

## 14. Launch Readiness Checklist

Before launch, confirm:

### Content
- all launch pages are final
- no placeholder text remains
- no obvious dummy imagery remains
- no staging links remain
- no lorem ipsum has survived by stealth

### Technical
- forms work
- analytics work
- SEO metadata is in place
- schema is in place
- performance is acceptable
- redirects are configured
- XML sitemap is correct
- robots settings are correct
- favicons and social images are in place

### Brand
- design quality holds across the site
- motion is stable
- editorial quality is high
- navigation feels complete
- the site feels small-but-weighty, not unfinished

### Operational
- owners know how to publish
- backup and rollback plans exist
- post-launch checks are assigned
- integrations are documented
- key contacts know launch timing

---

## 15. Post-Launch QA Checklist

Within 24–72 hours of launch, confirm:

- forms are still submitting
- ActiveCampaign is still capturing properly
- GA4 events are recording properly
- no critical 404s have appeared
- redirects are functioning
- pages are indexing as intended
- no production-only styling bugs have emerged
- media assets still load correctly
- no performance collapse has occurred
- newsletter and contact flows remain operational

Within 7–14 days, confirm:
- traffic quality is sensible
- conversions are firing
- search console issues are reviewed
- any browser/device bugs are triaged
- any deferred fixes are properly documented

---

## QA Ownership Model

At minimum, QA should have clear responsibility across:

- design QA
- editorial QA
- CMS QA
- technical QA
- SEO/schema QA
- analytics QA
- CRM/form QA
- launch QA

### Rule
If everyone “sort of” owns QA, no one does.

---

## Deferred Issues Log

Any issue not fixed before launch must be logged with:
- issue name
- page or template affected
- severity
- reason deferred
- owner
- target resolution date

### Rule
Deferred should mean scheduled later.  
Not buried with ceremony.

---

## Anti-Patterns

Avoid:
- launching with “we’ll fix it live”
- assuming staging equals production
- signing off based on desktop only
- skipping CMS usability checks
- leaving metadata to the end
- launching without testing analytics properly
- trusting embeds without checking them on mobile
- confusing visual beauty with overall readiness

If the site looks lovely but does not convert, measure, load or publish properly, it is not ready.

---

## Success Criteria

QA is successful when:
- the live site feels intentional and polished
- pages behave properly across devices
- content reads cleanly
- forms and integrations work
- analytics and conversions are trustworthy
- editors can use the CMS without fear
- no major brand, technical or usability issues undermine the launch

In short:  
QA is successful when Summit feels finished in the ways that matter, not merely attractive in a screenshot.

---

## How AI Should Use This File

When reviewing pages, templates, components or launch readiness for Summit, AI should:
- use this checklist as the quality standard
- flag issues by severity
- distinguish between blocker, major, minor and deferred
- review both brand quality and technical integrity
- avoid praising a page merely because it looks expensive
- treat content, UX, measurement and performance as equally real parts of quality

If a page fails in more than one major category, it should not be treated as ready.

---

## Final QA Ethos

Summit should review its work the way a good editor, a good designer and a good operator inspect a room before guests arrive.

Quietly.  
Thoroughly.  
Without excuses.  
Before the door opens.