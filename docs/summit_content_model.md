# summit_content_model.md

## Purpose of this file

This file defines Summit Communication Group’s content model.

It governs:
- content types
- field structures
- taxonomies
- relationships
- slug logic
- publishing rules
- editorial statuses
- CMS behaviour
- future extensibility

If:
- `summit_soul.md` defines what Summit is
- `summit_voice.md` defines how Summit sounds
- `summit_site_architecture.md` defines how Summit is organised
- `summit_build_system.md` defines how Summit is built
- `summit_design_system.md` defines how Summit looks and feels
- `summit_component_library.md` defines the reusable interface parts

then this file defines the structured content objects that feed the system.

This is the source of truth for WordPress content architecture.

**Rule:**  
A page may feel editorial. A page may feel cinematic. A page may feel bespoke.  
Its underlying content model should still be clear, structured and repeatable.

---

## Content Model Philosophy

Summit’s content model must support three distinct but connected functions:

1. **Consultancy**
   - strategic pages
   - service pages
   - case studies
   - contact pathways

2. **Editorial**
   - The Future of Luxury
   - long-form thought leadership
   - category-based archive growth
   - search authority and audience capture

3. **Media**
   - Tastemakers series
   - seasons
   - episodes
   - transcripts
   - guest credibility
   - future community and membership layers

The content model should make these three functions feel like one ecosystem rather than three adjacent businesses pretending not to know each other.

---

## Core Content Principles

1. Structure should be explicit.
2. Repeating content should live in structured fields, not in freestyle page-builder blocks.
3. Relationships matter as much as fields.
4. Slugs should be stable.
5. SEO metadata should be manageable per content object.
6. Rich media should be supported without becoming mandatory.
7. The model should support current needs without overbuilding speculative future fantasies.
8. Admin users should know what each content type is for without needing a séance.

## Content Systems and Platform Dependencies

Some Summit content types are shaped by external systems that materially affect how content is captured, prepared or delivered.

### ActiveCampaign
ActiveCampaign affects:
- newsletter sign-up modules
- enquiry forms
- gated download flows
- subscriber journeys
- CRM-linked audience capture

Where content objects involve forms, subscriptions, gated assets or lead capture, ActiveCampaign should be considered part of the content pathway.

### JW Player
JW Player affects:
- showreel content
- brand film embeds
- case study film delivery
- Tastemakers trailers
- selected editorial video assets

Where video is treated as a first-class content object rather than a casual embed, the content model should support JW Player delivery cleanly.

### Riverside.fm
Riverside is a source and production system for:
- podcast recordings
- transcripts
- show notes
- clips
- summary material

Its outputs should feed structured podcast season and episode objects rather than remain external fragments.

### Zoom
Zoom is a source and event system for:
- webinar recordings
- founder interviews
- workshop summaries
- event transcripts
- future registration-led content and replay pages

Where Zoom outputs become site content, they should be structured into proper Summit content objects rather than treated as loose attachments.

---

## Primary Content Types

Summit should use the following core content types.

### Standard Pages
Used for:
- Home
- Who We Are
- What We Do
- Brand Strategy
- Experience Design
- Digital Transformation
- Work Showcase (parent)
- The Future of Luxury (landing)
- Tastemakers (landing)
- Get In Touch / Design Tomorrow
- Careers
- Client Lounge
- utility pages

### Custom Content Types
- Case Studies
- Articles
- Podcast Series
- Podcast Seasons
- Podcast Episodes
- Downloads / Documents

### Future Content Types, only if needed
- Guests
- Authors
- Events
- Sponsors
- Community Posts
- Topic Hubs

---

## Content Type Overview

### 1. `page`
**Purpose:**  
Structured strategic and navigational pages.

**Examples:**
- Home
- Who We Are
- What We Do
- Get In Touch
- Careers

**Notes:**  
Pages should be template-led where possible.  
They should not become undisciplined containers for random content experiments.

---

### 2. `case_study`
**Purpose:**  
Selected Work child pages telling the story of an individual engagement through challenge, strategic idea, execution and outcome.

**Examples:**
- Teddy William
- The Fight Against Skin Cancer
- Leading Plastic Surgeons Films
- Kaleida Studios
- Tastemakers Podcast

---

### 3. `article`
**Purpose:**  
Long-form editorial content within The Future of Luxury. Launch should begin with 12–24 selected cornerstone articles before broader archive expansion.

**Examples:**
- luxury commentary pieces
- interviews
- analysis essays
- category thought leadership

If an article includes gated follow-up, newsletter capture or campaign-driven audience conversion, the article model should support integration with ActiveCampaign without compromising the editorial reading experience.

---

### 4. `podcast_series`
**Purpose:**  
Top-level podcast property. For Summit this is currently Tastemakers.

**Examples:**
- Tastemakers

---

### 5. `podcast_season`
**Purpose:**  
A season within a series. Season One is already articulated as a structured editorial product with theme, description, episode count and guest universe.

**Examples:**
- Season One — The New Value Equation

---

### 6. `podcast_episode`
**Purpose:**  
The core media content unit for tastemakers.fm, with explicit episode-level fields and page requirements already defined in the specification.

**Examples:**
- Price Without Proof
- The Great Hangover
- The Grey Market Mirror

---

### 7. `download`
**Purpose:**  
Downloadable strategic or editorial assets.

**Examples:**
- The Characteristics of Luxury
- decks
- reports
- sponsor packs
- gated PDFs later

---

## Detailed Content Types

## 1. Standard Pages

### Required Core Fields
- title
- slug
- page template
- status
- hero eyebrow (optional)
- hero headline
- hero body copy
- SEO title
- meta description
- Open Graph title
- Open Graph description
- Open Graph image
- canonical URL (optional)
- noindex toggle
- structured sections / page-builder fields only where governed

### Optional Fields
- secondary headline
- showreel embed
- CTA override
- related content block
- body sections using approved components only

### Relationships
- may relate to case studies
- may relate to articles
- may relate to podcast content
- may relate to downloads

### Notes
Pages should usually be treated as template assemblies, not as entirely freeform documents.

---

## 2. Case Studies

### Required Core Fields
- title
- slug
- status
- featured image
- project summary line
- sector
- what we did
- short intro
- challenge
- strategic idea
- what we built
- outcome
- primary CTA label
- primary CTA URL
- SEO title
- meta description
- Open Graph image

### Recommended Structured Fields
- project category
- service categories delivered
- client / brand name
- year
- geographic relevance
- optional outcome signal
- gallery
- embedded video / showreel
- related case studies
- related service page
- related article
- related podcast episode

### Content Blocks / Structured Long Fields
- challenge body
- strategic idea body
- what we built body
- outcome body
- gallery caption notes
- related work intro

### Taxonomies
- sector
- service type
- format

### Suggested Service Type Terms
- Brand Strategy
- Brand Identity
- Experience Design
- Digital Transformation
- Campaign
- Film
- Editorial Platform
- Podcast Identity
- Immersive Storytelling

### Suggested Format Terms
- Identity
- Campaign
- Film
- Website
- Platform
- Editorial
- Podcast
- Strategy

### Relationships
- many-to-many with service pages
- many-to-many with articles
- many-to-many with podcast episodes
- many-to-many with downloads

### Slug Logic
- `/work/[project-slug]`

### Notes
Case studies should remain curated.  
Not every client engagement deserves canonisation.

---

## 3. Articles

### Required Core Fields
- title
- slug
- status
- author
- publish date
- updated date
- standfirst / deck
- featured image
- article body
- category
- read time
- SEO title
- meta description
- Open Graph image

### Recommended Fields
- subtitle (optional)
- excerpt
- canonical URL
- source note if adapted from LinkedIn newsletter
- related articles
- related service page
- related case study
- related podcast episode
- subscribe prompt toggle
- featured toggle
- cornerstone toggle

### Taxonomies
- article category
- tags

### Recommended Article Categories
Derived from the editorial architecture already defined for The Future of Luxury:
- Luxury Strategy
- Brand & Positioning
- Luxury and Technology
- Culture & Media
- Hospitality & Travel
- Watches & Jewellery
- Fashion & Leather Goods
- Wealth, Value & Collecting
- Sport, Sponsorship & Status
- The New Luxury Customer

### Relationships
- author → person / author entity
- article ↔ article
- article ↔ case study
- article ↔ podcast episode
- article ↔ download
- article ↔ service page

### Slug Logic
- `/future-of-luxury/[article-slug]`
or
- `/insights/[article-slug]`

Choose one and keep it stable.

### Launch Rule
At launch, only selected cornerstone articles should be migrated, not all 114 pieces.

### Notes
This is where editorial discipline matters.  
An archive is a strategic asset, not a skip bin.

---

## 4. Podcast Series

### Required Core Fields
- series title
- series description
- host
- cover artwork
- trailer
- platform links
- subscribe embed
- sponsor information
- featured season
- SEO title
- meta description
- Open Graph image

### Additional Recommended Fields
- slug
- status
- hero headline
- hero body copy
- primary CTA
- secondary CTA
- host intro video
- guest information URL
- sponsor information URL

### Relationships
- one-to-many with podcast seasons
- one-to-many with podcast episodes
- one-to-many with articles
- one-to-many with downloads

### Slug Logic
- `/podcast`
or, where domain-specific:
- root on `tastemakers.fm`

### Notes
At present, Summit likely only needs one series object.  
Still worth modelling properly.

---

## 5. Podcast Seasons

### Required Core Fields
- season title
- season subtitle
- season description
- theme
- episode count
- hero image
- trailer
- guest list
- season status
- featured season toggle
- SEO title
- meta description
- Open Graph image

### Additional Recommended Fields
- slug
- season number
- season intro
- central thesis
- season artwork
- featured episode
- guest universe intro
- platform links
- subscribe prompt

### Relationships
- belongs to one podcast series
- has many podcast episodes
- may relate to many articles
- may relate to many downloads

### Slug Logic
- `/podcast/season-one`
or
- `/season-one`

Keep it elegant.

### Notes
Seasons should behave like structured editorial products, not playlist folders.

---

## 6. Podcast Episodes

### Required Core Fields
- episode title
- episode number
- season number
- guest name
- guest title
- guest company
- duration
- publish date
- episode summary
- transcript
- audio embed
- video embed
- show notes
- related article
- related episode
- quote pullout
- sponsor credits
- platform links
- SEO title
- meta description
- schema fields

### Additional Recommended Fields
- slug
- deck / standfirst
- featured artwork
- featured artwork alt text
- guest portrait
- guest bio
- canonical URL
- Open Graph image
- CTA settings
- logline
- cinematic arc
- transcript timestamps
- chapter markers
- quote cards
- downloadable assets
- transcript visibility toggle
- comments enabled toggle
- featured episode toggle
- series trailer cross-link
- guest enquiry attribution
- sponsor attribution

### Recommended Relationships
- belongs to one podcast series
- belongs to one podcast season
- may relate to one or more articles
- may relate to one or more episodes
- may relate to one or more downloads
- may relate to one guest entity later

### Slug Logic
- `/episodes/[episode-slug]`
with an optional season-aware alternative:
- `/season-one/[episode-slug]`

Dates should not be included in the public URL.

### Editorial Workflow States
- draft
- in review
- scheduled
- published
- archived

### Roles
- administrator
- editor
- producer
- contributor
- reviewer

### Notes
Episodes are not mere embeds.  
They are editorial pages with audio attached.

---

## 7. Downloads / Documents

### Required Core Fields
- title
- slug
- status
- document type
- cover image
- short description
- file URL
- file type
- file size
- SEO title
- meta description
- Open Graph image

If a download is gated, subscribed or part of a nurture flow, its content object should support a relationship to ActiveCampaign-driven form behaviour and audience capture.

### Recommended Fields
- gated toggle
- download CTA label
- related article
- related case study
- related podcast episode
- sector relevance
- service relevance

### Taxonomies
- document type
- sector
- service area

### Examples
- visual essays
- decks
- PDF briefings
- sponsor packs
- future reports

### Slug Logic
- `/downloads/[document-slug]`

---

## Supporting Entities

These may begin as taxonomies or simple structured fields, then later become content types if needed.

## 8. Authors

Initially:
- Gregory Gray can remain a structured author/person entity attached to article and podcast content.

Later:
- create author pages if multiple meaningful authors emerge.

### Required Fields
- name
- slug
- short bio
- portrait
- title
- LinkedIn URL
- SEO title
- meta description

---

## 9. Guests

Initially:
- guest details may remain as episode fields.

Later:
- convert to proper guest entities if there is a strategic need for:
  - guest archive pages
  - recurring guests
  - guest-sector pages
  - guest search

### Required Fields if created
- name
- slug
- title
- company
- bio
- portrait
- website / profile URL
- sector tags

---

## 10. Contact Enquiries

Where practical, enquiry capture should support structured routing into ActiveCampaign so that Summit’s CRM, automations and lead-management logic remain connected to the website rather than sitting in administrative exile.

This is not a front-end content type, but it is a meaningful admin object.

The Design Tomorrow / Get In Touch page already defines a structured enquiry model including:
- first name
- last name
- company
- job title
- email
- phone number
- enquiry topic
- brief project overview
- budget range
- timeline
- enquiry type options spanning Brand Strategy, Experience Design, Digital Transformation, Work Showcase Enquiry, Tastemakers Podcast, Media or Speaking, Partnership and Other

### Suggested Internal Enquiry Object
- enquiry ID
- submission date
- form source
- enquiry type
- contact details
- company
- budget range
- timeline
- project overview
- owner
- status
- notes

### Suggested Enquiry Statuses
- new
- reviewed
- qualified
- responded
- scheduled
- closed
- archived

This is optional in WordPress itself, but useful if form handling becomes more serious.

---

## Taxonomy Model

Taxonomies must remain restrained.

### Required Taxonomies

#### `sector`
Used for:
- case studies
- service pages where needed
- downloads
- later possibly articles and episodes

**Recommended terms:**
- Hospitality & Travel
- Automotive & Yachts
- Lifestyle Technology
- Fashion & Leather Goods
- Watches & Jewellery
- Perfumes & Cosmetics
- Wines & Spirits
- Home & Furniture
- Fine Art & Collectibles
- Real Estate
- Financial Services
- Private Healthcare

#### `service_type`
Used for:
- case studies
- possibly selected downloads

**Recommended terms:**
- Brand Strategy
- Experience Design
- Digital Transformation
- Branding
- Campaign
- Film
- Editorial
- Platform
- Podcast Identity

#### `article_category`
Used for:
- articles only

Keep this tightly governed.

#### `format`
Used for:
- case studies
- downloads
- possibly editorial features later

**Examples:**
- Article
- Interview
- Case Study
- Film
- Podcast
- Deck
- Report

#### `podcast_theme`
Used for:
- episodes
- seasons

**Examples:**
- Pricing
- Craft
- Experience
- AI
- Retail
- Wealth
- China
- Circularity

### Rule
Tags are acceptable for editorial nuance.  
They are not an excuse for taxonomic horticulture.

---

## Relationship Model

This is where the system becomes strategically useful.

### Required Relationships

#### Page Relationships
- service page → related case studies
- service page → related articles
- service page → related podcast episodes

#### Case Study Relationships
- case study → one or more service pages
- case study → one or more sectors
- case study → related case studies
- case study → related article(s)
- case study → related episode(s)

#### Article Relationships
- article → article category
- article → author
- article → related articles
- article → related service page
- article → related case study
- article → related episode
- article → related download

#### Podcast Relationships
- series → seasons
- season → episodes
- episode → related article
- episode → related episodes
- episode → guest
- episode → download
- episode → season
- episode → series

#### Download Relationships
- download → related article(s)
- download → related case study(ies)
- download → related episode(s)

### Strategic Intent
The point is not merely navigation.  
It is to create a dense internal ecosystem between:
- consultancy proof
- editorial authority
- media property
- audience capture

This is how the site stops being a brochure and starts behaving like an owned platform.

---

## Slug Rules

Slugs should be:
- short
- stable
- descriptive
- lower-case
- hyphenated
- free of dates unless absolutely necessary

### Recommended Structures

#### Pages
- `/who-we-are`
- `/what-we-do`
- `/work`
- `/future-of-luxury`
- `/design-tomorrow`

#### Service Pages
- `/what-we-do/brand-strategy`
- `/what-we-do/experience-design`
- `/what-we-do/digital-transformation`

#### Case Studies
- `/work/[project-slug]`

#### Articles
- `/future-of-luxury/[article-slug]`

#### Podcast
- series root on `tastemakers.fm` if implemented separately
- season pages: `/season-one`
- episode pages: `/episodes/[episode-slug]`

#### Downloads
- `/downloads/[document-slug]`

### Rule
Do not keep changing URL structures because somebody had a better feeling after lunch.

---

## SEO Field Model

Every indexable content object should support:

### Required SEO Fields
- SEO title
- meta description
- Open Graph title
- Open Graph description
- Open Graph image
- canonical URL
- index / noindex toggle

### Recommended SEO Support Fields
- primary keyword focus
- secondary keyword focus
- schema type hint
- breadcrumb label override if needed

### Note
SEO metadata should be manageable cleanly in the CMS.  
Not hidden in a plugin crypt somewhere behind a hedge.

---

## Status Model

### Standard Statuses
- draft
- in review
- scheduled
- published
- archived

### Content-Type Specific Notes
- case studies may require a `private / internal only` state before publication
- articles may need a `migration review` state during import
- podcast episodes may need a `transcript pending` state internally, even if not public

---

## Admin Role Expectations

At minimum, the following roles should exist for podcast and editorial workflow:
- administrator
- editor
- producer
- contributor
- reviewer

### Working Interpretation
- **administrator**: full system control
- **editor**: publish, revise, schedule
- **producer**: manage podcast episode inputs and assets
- **contributor**: draft content
- **reviewer**: quality control / approval

This can be adapted to WordPress roles, but the logic should remain.

---

## Launch Content Rules

### Consultancy Site
Launch with:
- all key pages
- selected work parent
- initial case study set
- showreel
- contact flow
- key download

### The Future of Luxury
Launch with:
- selected cornerstone articles only
- strongest categories only
- refined formatting
- canonical strategy decided before migration
- broader archive in phase two

### Tastemakers
Launch with:
- series page
- season one page
- selected episode pages or complete season structure depending readiness
- transcripts in HTML
- subscribe path
- guest / sponsor route

---

## Validation Rules

### Case Studies
Must not publish without:
- title
- slug
- sector
- what we did
- challenge
- strategic idea
- what we built
- outcome
- SEO title
- meta description

### Articles
Must not publish without:
- title
- slug
- author
- publish date
- standfirst
- body
- category
- SEO title
- meta description

### Podcast Seasons
Must not publish without:
- season title
- season number
- description
- theme
- featured image or artwork
- SEO title
- meta description

### Podcast Episodes
Must not publish without:
- title
- slug
- season
- episode number
- guest name
- summary
- publish date
- audio embed
- transcript
- SEO title
- meta description

Podcast episode production may originate in Riverside, while webinar-style or interview-derived media may originate in Zoom. In both cases, the final published object should be a clean Summit episode page with structured transcript, summary, media and relationship fields. Where video trailers or selected native video assets are used, the model should support JW Player cleanly.

### Downloads
Must not publish without:
- title
- file
- description
- file type
- SEO title
- meta description

---

## Future-Proofing

This model should leave room for:
- author pages
- guest pages
- sponsor pages
- user accounts
- comments / reactions
- saved episodes
- member circles
- premium content
- contributor workflows
- topic hubs
- audience identity layer

Do not build all of this now.  
Just avoid building in a way that makes it impossible later.

---

## Anti-Patterns

Avoid:
- using pages where a structured content type is needed
- duplicating fields across different content types without logic
- stuffing important metadata into free-text blobs
- letting tags multiply like rabbits in a vineyard
- treating podcast episodes as mere embeds
- migrating all 114 articles blindly
- building contact pathways that capture data poorly
- mixing consultancy proof and editorial content without relationships

If the CMS becomes confusing, the content model is failing.

---

## Success Criteria

The content model is successful when:
- editors understand where content belongs
- developers know how to structure templates
- AI systems can generate against stable field logic
- consultancy, editorial and podcast properties interlink cleanly
- SEO metadata is manageable
- archive growth remains disciplined
- the site can scale without becoming an administrative swamp

In short:  
The content model should make Summit feel more organised as it grows, not less.

---

## How AI Should Use This File

When generating WordPress structures, field maps, templates or migration logic for Summit, AI should:
- treat this file as the source of truth for content objects
- prefer structured fields over freeform improvisation
- maintain relationships across consultancy, editorial and media content
- protect slug stability
- avoid inventing unnecessary content types
- support future expansion without overbuilding now

If a proposed content structure makes the CMS noisier or less coherent, revise it.

---

## Final Content Ethos

Summit’s content model should behave like a well-run private house.

Everything has its room.  
Everything has its purpose.  
The doors connect intelligently.  
Nothing important is left in the hallway.