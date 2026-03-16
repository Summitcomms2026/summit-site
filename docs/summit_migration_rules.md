# summit_migration_rules.md

## Purpose of this file

This file defines how existing and future content should be migrated into Summit Communication Group’s digital ecosystem.

It governs:
- migration scope
- source selection
- content triage
- import rules
- rewriting rules
- formatting cleanup
- image handling
- taxonomy mapping
- slug and URL decisions
- canonical strategy
- metadata handling
- internal linking
- quality control
- post-migration review

If:
- `summit_soul.md` defines what Summit is
- `summit_voice.md` defines how Summit sounds
- `summit_site_architecture.md` defines how Summit is organised
- `summit_build_system.md` defines how Summit is built
- `summit_design_system.md` defines how Summit looks and feels
- `summit_component_library.md` defines the reusable interface parts
- `summit_content_model.md` defines the structured content objects

then this file defines how legacy and source content enters the system cleanly.

This is the source of truth for migration discipline.

Rule:
Migration is not dumping.
Migration is editorial selection, structural cleanup and strategic republishing.


## Migration Philosophy

Summit should not import content merely because it exists.

Every migrated item should justify its place by doing one or more of the following:
- strengthening category authority
- supporting SEO
- expressing Summit’s worldview
- deepening The Future of Luxury archive
- supporting Tastemakers
- creating internal links across consultancy, editorial and media
- helping build owned audience value

Summit is not trying to preserve the internet in amber.
It is building a sharper, more sovereign archive.


## Migration Priorities

### Priority 1 — Strategic Pages
These are not migrated in the conventional sense.
They are authored or rebuilt cleanly inside the new CMS.

Includes:
- Home
- Who We Are
- What We Do
- Brand Strategy
- Experience Design
- Digital Transformation
- Work Showcase
- Get In Touch / Design Tomorrow
- Careers
- Client Lounge
- Tastemakers landing page

### Priority 2 — Future of Luxury Editorial Archive
This is the highest-priority true migration stream.

Primary source:
- LinkedIn newsletter archive
- selected long-form articles already written by Gregory

### Priority 3 — Tastemakers
This may involve:
- season notes
- episode summaries
- transcripts
- guest information
- sponsor information
- future show notes

### Priority 4 — Case Studies
These may be migrated from:
- existing decks
- proposal materials
- PDFs
- working docs
- existing websites or project notes

### Priority 5 — Downloads / Documents
These may be added as curated assets after core editorial and case study migration is stable.


## Source Types

## Source Platform Notes

Some migration sources originate in production systems rather than traditional documents.

### Riverside.fm
Riverside should be treated as an approved source system for:
- podcast transcripts
- episode summaries
- show notes
- clips and derivative media assets

Riverside outputs should always be cleaned, structured and adapted into Summit’s content model before publication.

### Zoom
Zoom should be treated as an approved source system for:
- webinar recordings
- interview recordings
- meeting summaries
- transcript material
- workshop-derived editorial or event content

Zoom outputs should also be normalised before publication.
No raw webinar debris should wander onto the site unsupervised.

### Accepted Source Types
- LinkedIn newsletter articles
- Google Docs / Word documents
- approved draft files
- transcripts
- case study notes
- decks and reports
- approved PDFs
- manually entered strategic page content

### Caution Sources
These may be used, but require cleanup:
- LinkedIn post formatting
- copied web page HTML
- transcript dumps
- pitch copy
- old agency-style pages
- image-heavy PDFs with poor text structure

### Disallowed as Direct Imports
Do not blindly import:
- raw social posts as finished articles
- duplicate article versions
- messy transcripts without editorial treatment
- old pages that conflict with current positioning
- content written in an out-of-date brand voice
- anything included merely because it is “there”

Rule:
Source abundance is not a reason to lower standards.


## The Future of Luxury Migration Rules

This is the most important migration stream.

### Core Rule
Do not import all 114 LinkedIn newsletter articles at launch.

Launch should begin with a curated set of cornerstone pieces.

### Recommended Phase One Range
- 12 to 24 cornerstone articles

### Selection Criteria
Choose articles that are:
- still strategically relevant
- strongest in writing quality
- strongest in category authority
- aligned with Summit’s luxury-first positioning
- useful for search and topic clustering
- useful for internal linking into services, media or downloads

### Avoid in Phase One
- weaker or repetitive articles
- pieces too dependent on timing that has passed
- pieces with thin substance
- pieces that duplicate a stronger article on the same theme
- pieces that need major rewriting before they deserve republication

### Editorial Categories Must Be Applied During Migration
Every article must be assigned:
- one primary article category
- optional tags, used sparingly

Do not migrate category chaos.

### Cornerstone Flag
Selected anchor pieces should be marked as:
- cornerstone
- featured where appropriate

### Migration Result
The new editorial archive should feel intentionally launched, not bulk-imported.


## Tastemakers Migration Rules

Where source material originates in Riverside, transcripts and notes should be treated as production inputs rather than publish-ready content. They must be cleaned, structured and mapped into the podcast episode model before going live.

### Core Rule
Treat Tastemakers as a structured media property, not a pile of episode embeds.

### Sources May Include
- season summaries
- episode summaries
- transcripts
- guest biographies
- host notes
- sponsor materials
- planning documents

### Each Episode Should Be Migrated As
- a structured podcast episode object
- not a generic post
- not a raw embed page
- not merely transcript storage

### Minimum Viable Episode Migration
An episode should not go live unless it has:
- title
- slug
- season
- episode number
- guest name
- summary
- publish date
- audio source
- transcript
- SEO title
- meta description

### Transcripts
Transcripts should be:
- cleaned
- lightly edited for readability where necessary
- structurally formatted
- speaker-labelled if useful
- not dumped as raw machine text unless unavoidable

### Season Pages
Season pages should be authored as editorial landing pages, not autogenerated lists.

### Episode-to-Article Linking
Where useful, connect episodes to:
- relevant Future of Luxury articles
- relevant service pages
- related episodes
- downloads

This is how media starts behaving like an ecosystem.


## Case Study Migration Rules

### Core Rule
Case studies should be rebuilt, not merely imported.

Source material may include:
- deck copy
- PDFs
- old website text
- project notes
- presentation material
- proposal descriptions

But the final published case study should follow the Summit case study structure:
- challenge
- strategic idea
- what we built
- outcome

### Do Not Import Case Studies That
- lack a clear strategic narrative
- overstate the prestige of the work
- read like generic agency boast pages
- have too little substance to warrant publication

### Case Study Tone
Case studies should feel:
- elegant
- selective
- strategically literate
- specific without oversharing
- proof-led rather than chest-thumping

### Required Rebuild
Even if source copy exists, each case study should be reviewed and usually rewritten into the new structure.

Migration is therefore often adaptation, not duplication.


## Downloads / Documents Migration Rules

### Core Rule
Only migrate documents worth presenting as assets.

Examples:
- The Characteristics of Luxury
- sponsor packs
- selected strategic decks
- future reports
- visual essays

### Requirements
Each migrated document should have:
- title
- short description
- file
- cover image if possible
- document type
- related content links
- clean slug
- SEO metadata

### Avoid
- uploading internal-looking PDFs without context
- files with poor naming
- duplicate versions
- random archive debris


## Rewriting Rules

Migration does not always mean verbatim republication.

### Use Verbatim Only When
- the piece is already strong
- the tone is still on-brand
- the structure maps well to the new system
- no major factual or strategic updates are needed

### Use Light Rewrite When
- the article is strong but needs:
  - tightening
  - stronger intro
  - cleaner subheads
  - internal linking
  - better metadata
  - better CTA placement

### Use Heavy Rewrite When
- the core idea is strong but:
  - the voice is off
  - the structure is weak
  - the article is too LinkedIn-shaped
  - the logic is repetitive
  - the article needs adapting to web reading and SEO

### Do Not Migrate When
- the piece adds no strategic value
- a stronger version already exists
- the piece would need total reconstruction
- it no longer represents Summit well

Rule:
Respect the archive, but do not become sentimental about mediocre copy.


## Formatting Cleanup Rules

All migrated content must be normalised.

This applies especially to Riverside and Zoom source material, where raw transcripts, auto-generated summaries and speaker labels often need editorial normalisation before they are fit for publication.

### Required Cleanup
- remove platform artefacts
- remove duplicated line breaks
- remove odd unicode or pasted formatting junk
- repair heading hierarchy
- convert weak list formatting into proper HTML/Markdown structure
- remove social-media phrasing where inappropriate
- remove in-platform prompts such as “subscribe on LinkedIn” where no longer relevant
- ensure quotes, apostrophes and punctuation are clean
- ensure British spelling where needed

### Long-Form Article Rules
- clear H2 structure
- optional H3 where needed
- consistent paragraph spacing
- no giant walls of text without editorial pacing
- no random bolding from legacy platforms

### Transcript Rules
- structure speaker names consistently
- correct obvious transcription errors where practical
- remove accidental verbal litter where it adds nothing
- preserve substance without preserving chaos


## Image Handling Rules

### Featured Images
Every migrated article, case study, season and episode should have:
- one featured image or artwork where possible
- appropriate Open Graph image
- alt text

### Legacy Images
If LinkedIn or old source images are low quality or awkwardly cropped:
- replace
- reframe
- redesign
- or omit until a proper image exists

### Rule
Do not let poor inherited imagery cheapen a strong archive.

### Galleries
For case studies or visual essays:
- select only strong images
- preserve intentional ordering
- avoid uploading every available frame like a panicked archivist


## Metadata Rules

Every migrated object must receive clean metadata.

### Required
- title
- slug
- SEO title
- meta description
- Open Graph image
- publish date
- author where relevant
- category where relevant

### Optional But Valuable
- updated date
- canonical URL
- featured flag
- cornerstone flag
- read time
- related content

### Rule
Metadata is not a postscript.
It is part of the migration job.


## Canonical and Source Strategy

This matters especially for LinkedIn newsletter content.

### If Article Is Republishing Existing LinkedIn Content
Decide whether Summit or LinkedIn is the preferred canonical home.

### Recommended Strategic Direction
Over time, Summit should become the primary owned archive.

That means:
- republishing selected pieces on Summit
- updating and improving them where appropriate
- using Summit as the long-term authoritative home

### Practical Rule
Do not leave canonical handling ambiguous.
Define:
- whether the new Summit page is canonical
- whether LinkedIn remains the source version
- whether revised Summit versions are sufficiently distinct to stand on their own

### Notes
This decision should be made consistently, not article by article in a fit of mood.


## Slug Migration Rules

### Core Rule
Slugs should be cleaned before publication.

### Requirements
- lower-case
- hyphenated
- concise
- durable
- free from platform residue
- no dates unless absolutely necessary

### Avoid
- copying ugly platform slugs blindly
- changing slugs after publication without redirect planning
- inconsistent structures across content types

### Redirect Rule
If an existing Summit URL changes:
- create a redirect
- document it
- do not trust memory


## Taxonomy Mapping Rules

Migration should include category and tag discipline.

### For Articles
Each article must have:
- one primary category
- optional tags only where useful

### For Case Studies
Apply:
- sector
- service type
- format

### For Episodes
Apply:
- season
- episode theme
- guest category where relevant

### Rule
Do not migrate source tags blindly from LinkedIn or other systems.
They are usually too messy, too platform-shaped or too numerous.


## Internal Linking Rules

Migration is a chance to create a proper internal ecosystem.

### Articles Should Link To
- related articles
- relevant service pages
- related case studies
- relevant podcast episodes
- downloads where useful

### Case Studies Should Link To
- related service pages
- related work
- relevant articles
- related podcast content where useful

### Podcast Episodes Should Link To
- related episodes
- season page
- relevant articles
- relevant service pages where appropriate

### Rule
Add internal links intentionally during migration.
Do not leave this until “later”, which is usually the professional cousin of never.


## Migration Workflow

If source material comes from Riverside or Zoom, include an additional source-validation step before import to confirm transcript quality, speaker clarity, structural completeness and whether the material belongs as an article, episode, event asset or internal note.

### Step 1 — Inventory
Create a source inventory:
- title
- source type
- current URL if any
- proposed content type
- quality rating
- migration priority
- rewrite level needed

### Step 2 — Triage
Label each item:
- migrate now
- migrate later
- rewrite before migration
- do not migrate

### Step 3 — Structure
Map each approved item into the Summit content model:
- content type
- taxonomy
- fields
- relationships
- slug
- metadata

### Step 4 — Clean
Normalise formatting, rewrite as needed, prepare images and metadata.

### Step 5 — Import
Enter or import into WordPress carefully.

### Step 6 — QA
Check:
- formatting
- metadata
- internal links
- responsive behaviour
- image quality
- taxonomy
- slug
- schema output where relevant

### Step 7 — Publish
Only publish after review, not merely after import.

Rule:
Imported is not the same as finished.


## Migration QA Checklist

Every migrated item should be checked for:
- correct content type
- correct title
- clean slug
- correct category / taxonomy
- strong featured image
- clean formatting
- proper heading hierarchy
- good metadata
- internal links added
- CTA logic appropriate
- no legacy platform debris
- mobile readability
- schema support where relevant

If two or more of these fail, the item is not ready.


## Priority Labels

Use the following internal migration labels:

- `P1` = launch essential
- `P2` = phase two strong asset
- `P3` = archive candidate later
- `REWRITE` = worth keeping, but needs work
- `HOLD` = do not migrate yet
- `DROP` = do not migrate

This keeps the migration programme from becoming an emotional support group for old files.


## Anti-Patterns

Avoid:
- migrating all editorial content at once
- treating LinkedIn as a CMS export rather than a publishing platform with quirks
- importing raw transcripts without review
- copying old agency copy into the new site unchanged
- creating duplicate article versions without canonical logic
- preserving weak tags
- preserving weak slugs
- preserving weak images
- migrating content because it feels wasteful not to

If migration increases noise faster than it increases value, it is failing.


## Success Criteria

Migration is successful when:
- the new archive feels sharper than the old one
- every migrated item feels chosen
- formatting is clean
- metadata is complete
- editorial quality rises rather than falls
- consultancy, editorial and media content connect intelligently
- the new CMS is cleaner than the old source environment
- Summit becomes a stronger owned destination than the platforms it is migrating from

In short:
Migration is successful when the new system feels authored, not transferred.


## How AI Should Use This File

When migrating content for Summit, AI should:
- prioritise selection over volume
- prefer cleanup and structure over blind copying
- map every item to the correct content type
- preserve Summit’s current voice, not the historical weakness of the source
- support clean slugs, metadata and internal links
- recommend against migration where the source is weak
- flag where rewriting is needed before publication

If a source item weakens the archive, AI should say so plainly.


## Final Migration Ethos

Summit should migrate content the way a good editor prepares a collected works edition.

Not everything goes in.  
Not everything stays as it was.  
What survives should deserve to.