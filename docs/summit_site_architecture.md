# summit_site_architecture.md

## Purpose of this file

This file defines the structural logic of Summit Communication Group’s digital ecosystem.

It governs:
- page hierarchy
- navigation logic
- URL structure
- content types
- taxonomies
- template relationships
- CTA pathways
- archive behaviour
- WordPress content modelling
- future editorial and media expansion

If `summit_soul.md` defines what Summit is, and `summit_voice.md` defines how it sounds, this file defines how Summit is organised.

This document should be used when:
- building the WordPress architecture
- defining custom post types
- creating template logic
- planning navigation
- determining internal links
- structuring archives
- deciding how new content types should behave

The site must feel small, controlled and premium.
It must not become sprawling, inconsistent or structurally confused.


## Core Structural Principle

Summit’s website is not a brochure site.

It is a design-led consultancy platform with three connected layers:

### 1. Consultancy Layer
This explains what Summit does, who it serves and why it matters.

### 2. Proof Layer
This shows selected work, strategic thinking and evidence of practice.

### 3. Media Layer
This builds thought leadership, audience ownership and long-term editorial value.

These three layers must reinforce one another.

The consultancy gives Summit commercial credibility.
The proof layer gives it trust.
The media layer gives it authority and future leverage.

The site must never feel like three unrelated businesses living in the same postcode.


## Primary Domain Structure

### Main Domain
- summitcommunication.group

This is the primary home of Summit Communication Group.

It should contain:
- consultancy pages
- selected work
- The Future of Luxury
- core business contact pathways
- Summit brand positioning
- company-level media and editorial identity

### Related Media Property
- tastemakers.fm

This should operate as an endorsed but distinct media property.

Relationship rule:
Tastemakers is an independent media property by Summit Communication Group.

Tastemakers may eventually operate on its own domain and product logic, but its relationship to Summit should remain visible and strategically clear.

The Summit site should still include:
- a Tastemakers landing page
- links to Tastemakers episodes or seasons where relevant
- clear pathways into the media ecosystem


## Site-Wide Principles

### 1. Fewer pages, more weight
The architecture should favour restraint.
Pages should feel intentional and edited.

### 2. Strong parent-child logic
Where deeper detail is needed, use archive and child-page relationships rather than overcrowding parent pages.

### 3. Every major page must have a purpose
No page should exist merely because websites are expected to have one.

### 4. Media and consultancy must feel related
Editorial content should support authority, not distract from the consultancy.

### 5. Growth must be structured
New sections should only be added if they fit the existing information architecture and strategic direction.

### 6. WordPress should operate as a content system, not a page-builder playground
The architecture should be implemented through reusable templates, controlled fields and custom post types where appropriate.


## Global Information Architecture

### Primary Navigation
- Home
- Who We Are
- What We Do
- Work Showcase
- The Future of Luxury
- Get In Touch

### Secondary / Utility Navigation
- Careers
- Client Lounge
- Tastemakers Podcast

### Sticky Top Navigation
Normal state order:
- Menu
- Brand
- Design Tomorrow

### Expanded Mega Menu
The expanded menu should follow the structural logic of Matter Of Form’s mega menu.

It should feel like a designed navigation environment rather than a dropdown.

Required contents:
- sticky top navigation preserved
- primary navigation
- secondary / utility navigation
- selected Future of Luxury article feed
- sticky bottom utility bar

### Sticky Bottom Navigation
- positioning statement
- Start a Conversation
- London +44

This navigation system should remain stable unless there is a major strategic reason to change it.


## Top-Level Page Model

### 1. Home
Purpose:
To introduce Summit, establish authority and direct visitors into the three main systems:
- consultancy
- work
- media

Role in architecture:
The homepage is a choreographed entry point, not the place to explain everything.

### 2. Who We Are
Purpose:
To explain Summit’s philosophy, operating model, cultural stance and authorship.

Role in architecture:
This is the identity page, not a team directory.

### 3. What We Do
Purpose:
To translate intrigue into commercial understanding.

Role in architecture:
This is the main service and conversion page.

### 4. Work Showcase
Purpose:
To show how Summit’s thinking appears in practice.

Role in architecture:
This is a curated archive of selected engagements with child case-study pages.

### 5. The Future of Luxury
Purpose:
To function as Summit’s editorial property and thought-leadership engine.

Role in architecture:
This is not “the blog”.
It is the owned publishing platform.

### 6. Get In Touch
Purpose:
To create a controlled invitation into conversation.

Role in architecture:
This is the main contact conversion page.

### 7. Careers
Purpose:
To attract the right calibre of talent.

Role in architecture:
Selective invitation, not recruitment portal.

### 8. Client Lounge
Purpose:
To provide controlled access to private client resources or account-level areas.

Role in architecture:
Utility layer, not public marketing theatre.

### 9. Tastemakers Podcast
Purpose:
To present the Tastemakers media property within the Summit ecosystem.

Role in architecture:
A bridge page between Summit and the wider Tastemakers world.


## URL Structure

URLs should be clean, durable and human-readable.

### Core Pages
- /
- /who-we-are
- /what-we-do
- /work-showcase
- /the-future-of-luxury
- /design-tomorrow
- /careers
- /client-lounge
- /tastemakers

### Suggested Service Child Pages
- /what-we-do/brand-strategy
- /what-we-do/experience-design
- /what-we-do/digital-transformation

### Work Archive and Child Structure
- /work-showcase/[project-slug]

### Editorial Archive and Child Structure
- /the-future-of-luxury/[article-slug]

### Podcast Structure on Summit
- /tastemakers/[season-slug]
- /tastemakers/episode/[episode-slug]

If Tastemakers later migrates fully to tastemakers.fm, Summit should retain intelligent summary pages and appropriate redirects or cross-links rather than creating orphaned dead ends.

Avoid:
- dates in URLs
- unnecessarily nested structures
- category slugs in public URLs unless strategically useful
- WordPress default post URLs


## WordPress Content Model

The site should use a combination of standard pages and custom post types.

### Standard Pages
Use standard Pages for:
- Home
- Who We Are
- What We Do
- Get In Touch
- Careers
- Client Lounge
- Tastemakers landing page
- any static utility page

### Custom Post Types

#### 1. Case Studies
Purpose:
Selected Work child pages

Slug base:
- /work-showcase/[project-slug]

#### 2. Articles
Purpose:
The Future of Luxury editorial articles

Slug base:
- /the-future-of-luxury/[article-slug]

#### 3. Podcast Seasons
Purpose:
Season pages for Tastemakers

Slug base:
- /tastemakers/[season-slug]

#### 4. Podcast Episodes
Purpose:
Individual episode pages

Slug base:
- /tastemakers/episode/[episode-slug]

#### 5. Downloads / Documents (optional)
Purpose:
Gated or ungated downloadable assets such as:
- The Characteristics of Luxury
- sponsor decks
- selected reports
- future white papers

This can be implemented as a CPT or a controlled media/document object depending on complexity.

### Future Content Types
Do not build these in phase one unless necessary, but leave room for them:
- Guests
- Authors
- Events
- Community posts
- Sponsor pages
- Member profiles


## Taxonomy Model

Taxonomies should remain tight and useful.
Too many taxonomies will make the site feel over-administered.

### For Articles
Recommended categories:
- Luxury Strategy
- Brand & Positioning
- Luxury and Technology
- Culture & Media
- Hospitality & Travel
- Fashion & Leather Goods
- Watches & Jewellery
- Wealth, Value & Collecting
- Sport, Sponsorship & Status
- The New Luxury Customer

Use tags sparingly for:
- AI
- China
- resale
- hospitality
- pricing
- audience
- experience
- craft
- status
- media sovereignty

### For Case Studies
Taxonomies may include:
- service type
- sector
- project format

Recommended controlled vocabularies:
Service type:
- Brand Strategy
- Experience Design
- Digital Transformation
- Campaign
- Film
- Editorial Identity
- Immersive Storytelling

Sector:
- Hospitality & Travel
- Automotive & Yachts
- Fashion & Leather Goods
- Watches & Jewellery
- Perfumes & Cosmetics
- Wines & Spirits
- Lifestyle Technology
- Fine Art & Collectibles
- Real Estate
- Financial Services
- Private Healthcare

Project format:
- Brand Identity
- Website
- Platform
- Campaign
- Film
- Podcast
- Venture Brand

### For Podcast Episodes
Taxonomies may include:
- season
- episode theme
- guest category

Recommended guest categories:
- Founder
- CEO
- Creative Director
- Investor
- Hospitality Operator
- Watch Authority
- Fashion Executive
- Wealth Adviser
- Cultural Commentator
- Brand Strategist


## Template Relationships

The architecture should rely on reusable templates, not one-off page invention.

### Template Types

#### 1. Standard Page Template
Used for:
- Who We Are
- What We Do
- Careers
- Get In Touch
- Client Lounge

#### 2. Homepage Template
Unique but built from reusable components.

#### 3. Work Archive Template
Used for Work Showcase parent page.

#### 4. Case Study Template
Used for all Selected Work child pages.

#### 5. Editorial Archive Template
Used for The Future of Luxury parent/archive page.

#### 6. Article Template
Used for individual editorial articles.

#### 7. Podcast Landing Template
Used for Tastemakers main page.

#### 8. Season Template
Used for Tastemakers season pages.

#### 9. Episode Template
Used for individual episode pages.

#### 10. Utility / Access Template
Used for gated or logged-in pages such as Client Lounge if needed.

No content type should require a bespoke layout every time unless the deviation is strategically justified.


## Component System

Pages should be built from reusable components.

### Core Global Components
- sticky top navigation
- expanded menu overlay
- sticky bottom utility bar
- footer
- newsletter capture module
- CTA footer block

### Standard Content Components
- hero block
- value proposition block
- capability triptych
- quote block
- image-text split
- card grid
- article card
- case-study card
- podcast episode card
- sectors grid
- testimonial slider
- related content block
- showreel/video block
- download block
- subscribe block
- tabbed connect/contact module

### Editorial Components
- article hero
- metadata bar
- rich text body
- pull quote
- in-article media block
- related reading
- author module
- share module
- podcast connection module

### Podcast Components
- episode hero
- player module
- guest module
- transcript block
- show notes block
- season overview grid
- share and subscribe module

These components should be designed systemically so the site can scale without losing coherence.


## CTA Logic

CTAs must feel controlled and consistent.

### Primary Global CTA
- Design Tomorrow

This is the emotional and strategic lead CTA.

### Secondary Conversion CTAs
- Start a Conversation
- Explore Our Work
- Explore What We Do
- Read the Latest Article
- Listen to the Latest Episode
- Download the Deck

### CTA Rules
- one dominant CTA per panel
- avoid CTA clutter
- CTA wording should remain stable across the site
- do not invent endless new CTA phrases unless strategically necessary

The site should guide users into a small number of clear journeys:
- learn about Summit
- understand capabilities
- see proof
- consume thought leadership
- enter conversation


## Internal Linking Logic

Internal linking must reinforce the ecosystem.

### Consultancy to Proof
Service pages should link to relevant case studies.

### Proof to Consultancy
Case studies should link back to What We Do and relevant service pages.

### Consultancy to Media
Service pages may link to relevant Future of Luxury articles where useful.

### Media to Consultancy
Articles and podcast pages should occasionally and tastefully link back to:
- What We Do
- Work Showcase
- Get In Touch

### Media to Media
Articles should link to:
- related articles
- relevant podcast episodes

Episodes should link to:
- relevant articles
- related episodes
- season page

The internal link structure should support:
- better UX
- better SEO
- clearer authority signals
- ecosystem coherence


## Homepage Logic

The homepage is a controlled sequence, not a dumping ground.

Recommended sequence:
1. Hero
2. Showreel
3. Value Proposition
4. What We Do
5. Selected Work
6. Tastemakers
7. The Characteristics of Luxury
8. The Future of Luxury
9. Luxury Sectors
10. Closing CTA

Each homepage panel must do one job well.
No homepage section should exist merely because it was available.

The homepage should introduce the system.
It should not contain the whole system.


## Work Showcase Logic

The Work Showcase section should use a parent-child structure.

### Parent Page
Purpose:
Curated overview of selected work

Includes:
- hero
- showreel
- selected project grid
- strategic framing
- luxury context panel if used carefully
- supporting thinking/download panel
- closing CTA

### Child Case Study Pages
Purpose:
Narrative proof

Includes:
- hero
- challenge
- strategic idea
- what was built
- outcome
- gallery/showreel
- related work
- CTA

Work Showcase must feel curated, not exhaustive.
It should not imitate a large agency’s portfolio inflation tactics.


## The Future of Luxury Logic

The Future of Luxury is an editorial property, not a side blog.

### Archive Page
Purpose:
Editorial index and category authority

Includes:
- hero
- editorial positioning
- featured article
- latest writing
- categories
- Tastemakers panel
- subscribe panel
- owned-media thesis panel if appropriate
- closing CTA

### Article Pages
Purpose:
Thought leadership, search authority and audience capture

Includes:
- article hero
- article body
- author module
- related podcast
- share module
- related reading
- subscribe module
- strategic CTA

Launch rule:
Do not import all historic content blindly.
Begin with a selected archive and expand deliberately.


## Tastemakers Logic

Tastemakers should behave like a serious media property.

### Summit Tastemakers Page
Purpose:
Introduce the show within Summit and route users into the wider media ecosystem

### Season Pages
Purpose:
Present the season argument and episode structure

### Episode Pages
Purpose:
Deliver the listening experience, transcript, show notes and related ecosystem links

Relationship rule:
Tastemakers should feel connected to Summit but not trapped inside it.


## Get In Touch Logic

The public-facing CTA may be “Design Tomorrow”, but the contact architecture should remain clear.

### Preferred URL
- /design-tomorrow

### Interface Logic
Use a tabbed:
- Connect
- Contact

structure.

#### Connect
For:
- new business
- partnerships
- media
- guests
- sponsors
- selected strategic conversations

#### Contact
For:
- office details
- direct email
- London presence
- social/editorial links

This page should feel like a private door, not a helpdesk.


## Client Lounge Logic

Client Lounge is a utility environment.

Purpose may include:
- shared resources
- client-only downloads
- dashboards
- project links
- private documents
- client log-in routes

Client Lounge must not clutter the public site.
It should remain discreet and functional.

If gated access is introduced, it should be done cleanly and with proper permission logic.


## Careers Logic

Careers should remain selective.

It should support:
- current opportunities
- expression of interest
- cultural invitation
- recruitment brand positioning

It should not become:
- a cluttered HR portal
- a generic list of benefits
- a startup culture performance


## SEO Architecture Rules

The site should rank through structure, authority and consistency rather than page sprawl.

### Pillar Pages
Key ranking pillars:
- What We Do
- Brand Strategy
- Experience Design
- Digital Transformation
- The Future of Luxury
- selected major case studies
- major podcast season or episode pages where relevant

### Topic Clusters
Articles and episodes should reinforce:
- luxury strategy
- luxury experience
- luxury technology
- affluent audiences
- media sovereignty
- culture and luxury
- sponsorship and status
- the future of premium value

### URL and Indexation Rules
- clean canonical URLs
- avoid duplicate archives
- avoid thin taxonomy pages
- ensure archives earn their keep
- use strong internal links
- no pointless content pages for SEO theatre


## Schema Logic

Schema should support structure, not become decorative overkill.

### Core Schema Objects Across Site
- Organization
- ProfessionalService
- WebPage
- CollectionPage
- ContactPage
- BreadcrumbList

### Work Section
- CreativeWork
- CaseStudy if supported

### Editorial Section
- Blog
- BlogPosting
- Article
- Person

### Podcast Section
- PodcastSeries
- PodcastEpisode
- AudioObject
- VideoObject where relevant

### Contact
- ContactPoint

Schema should be tied to real content objects, not sprinkled across the site in the hope Google finds it flattering.


## WordPress Governance Rules

### Rule 1
Use custom post types for repeatable content objects.

### Rule 2
Do not rely on free-form WYSIWYG chaos for key templates.

### Rule 3
The theme should express the design system.
The plugin layer should define content structure.

### Rule 4
Avoid architecture that depends on one person manually recreating layout logic per page.

### Rule 5
Do not allow plugin sprawl to define the site structure.

### Rule 6
Every template should remain robust even if stripped of most media.
The architecture must carry the experience.


## Phase Logic

### Phase One
Build:
- core pages
- What We Do child pages
- Work Showcase archive + first case studies
- The Future of Luxury archive + selected articles
- Tastemakers page
- season and episode templates
- Get In Touch
- Careers
- basic Client Lounge placeholder
- primary schema
- controlled CMS structure

### Phase Two
Add:
- expanded article archive
- richer episode archive
- gated downloads
- comments or reactions if appropriate
- guest pages
- sponsor pages
- more robust client area
- richer editorial categorisation

### Phase Three
Add:
- community features
- first-party identity layer
- participation systems
- premium discussion environments
- member profiles
- events and ticketing
- owned audience operating system concepts

The architecture should anticipate later phases without pretending to be all of them on launch.


## Anti-Patterns

The site architecture must avoid:
- too many top-level pages
- duplicative navigation pathways
- archive pages with no reason to exist
- media properties disconnected from consultancy logic
- over-filtered portfolio grids with too little work
- bloated WordPress plugin dependence
- “blog” language that diminishes editorial value
- one-off page inventions that break the system
- content migration without taxonomy discipline
- architecture built around temporary campaign needs
- trying to look large by becoming structurally untidy


## How AI Should Use This File

When generating website structures, copy outlines, page specs or development briefs, AI should use this file to:
- preserve the small-but-weighty architecture
- keep page logic consistent
- respect parent-child relationships
- maintain clean CTA pathways
- support the media-consultancy ecosystem
- avoid unnecessary new sections
- think in reusable templates and content objects
- protect the distinction between Summit and Tastemakers while keeping them strategically related

If a proposed page or feature weakens coherence, it should be challenged before it is added.


## Final Architectural Ethos

Summit’s website should feel like a tightly directed system.

Not sprawling.
Not improvised.
Not dependent on decoration.

Every page should know why it exists.
Every content type should know how it behaves.
Every pathway should move the visitor toward understanding, trust, authority or conversation.

The structure should feel as considered as the brand it represents.