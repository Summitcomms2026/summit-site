# summit_page_specs.md

## Purpose of this file

This file defines the master specification framework for Summit Communication Group’s core pages and templates.

It governs:
- page inventory
- page purpose
- page hierarchy
- page-specific strategic role
- panel sequencing
- component usage
- SEO expectations
- schema expectations
- CTA logic
- relationship mapping
- approval discipline
- handoff structure for design and development

If:
- `summit_soul.md` defines what Summit is
- `summit_voice.md` defines how Summit sounds
- `summit_site_architecture.md` defines how Summit is organised
- `summit_build_system.md` defines how Summit is built
- `summit_design_system.md` defines how Summit looks and feels
- `summit_component_library.md` defines the reusable interface parts
- `summit_content_model.md` defines the structured content objects
- `summit_editorial_system.md` defines how Summit publishes
- `summit_measurement_plan.md` defines how success is measured
- `summit_qa_checklist.md` defines quality control standards

then this file defines the approved page-level blueprint for the site.

This is the source of truth for what pages exist, what they are for and how they should be specified before build.

**Rule:**  
No important page should be designed or built from vibes alone.  
Every significant page should have an approved spec.

---

## What This File Is

This file is:
- a master page-specification index
- a page governance document
- a handoff aid for design and development
- a consistency guardrail

This file is **not**:
- the full written copy for every page
- the CMS content entry itself
- a visual design file
- a substitute for detailed component or template implementation

Where needed, individual pages may later have their own deeper spec files.  
This document defines the canonical starting structure.

---

## Page Specification Philosophy

Summit’s site should feel:
- compact
- deliberate
- high-trust
- architected
- selective

Therefore each page specification should answer:

1. Why does this page exist?
2. Who is it for?
3. What should the user understand by the end?
4. What should the user do next?
5. Which components should be used?
6. What should not appear on the page?
7. How does the page support the wider Summit ecosystem?

**Rule:**  
A page earns its place by carrying strategic weight.  
If it does not, it probably does not belong.

---

## Master Page Inventory

Summit’s approved page inventory for phase one is:

### Core Strategic Pages
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

### Work and Proof Templates
- Case Study Template

### Editorial Pages
- The Future of Luxury
- Article Template

### Media Pages
- Tastemakers
- Season Template
- Episode Template

### Optional Utility / Expansion Pages Later
- Download / Document Template
- Event Page Template
- Author Page Template
- Guest Page Template

---

## Standard Page Spec Format

Every Summit page spec should use the following structure.

### 1. Page Title
Official internal name of the page.

### 2. Strategic Purpose
Why the page exists.

### 3. Primary Audience
Who the page is primarily for.

### 4. Secondary Audience
Who else may use it.

### 5. Desired Outcome
What the visitor should understand, feel or do.

### 6. Template Type
Which page or content template it uses.

### 7. Key Messages
What the page must communicate.

### 8. Component Sequence
Which approved components appear and in what order.

### 9. CTA Logic
Primary and secondary next actions.

### 10. SEO / Schema Notes
Core keyword, metadata intent, schema type, indexing notes.

### 11. Relationships
Which pages, content objects or systems it should connect to.

### 12. Exclusions
What should not appear on the page.

### 13. Status
Draft / approved / in build / live.

---

## Global Page Rules

These rules apply to all Summit pages unless explicitly overridden.

### 1. Every Page Must Have a Clear Strategic Role
No filler pages.  
No “nice to have” pages that say nothing.

### 2. One Dominant Idea Per Page
A page may be rich.  
It should not be confused.

### 3. CTA Logic Must Be Clean
Every major page should know what it wants the visitor to do next.

### 4. Components Must Come from the Approved Library
Do not invent bespoke panels carelessly.

### 5. SEO Must Support Structure, Not Distort It
No page should become ugly in the name of keywords.

### 6. Internal Linking Must Be Intentional
Every important page should connect to related proof, editorial or media where relevant.

### 7. The Page Must Feel Like Summit
This sounds obvious. It routinely fails in practice.

---

## Page Specifications

# 1. Home

## Strategic Purpose
To establish Summit’s positioning immediately, introduce the world of the brand, signal authority, show selective proof and route visitors into services, work, editorial and contact.

## Primary Audience
Senior decision-makers in luxury and adjacent premium sectors.

## Secondary Audience
Creators, agencies, cultural operators, media contacts, prospective collaborators and future subscribers.

## Desired Outcome
The user should understand that Summit is a design consultancy for luxury brands specialising in brand strategy, experience design and digital transformation, with a serious editorial and media point of view.

## Template Type
Homepage template

## Key Messages
- Summit is strategically sharp and creatively sophisticated.
- Summit operates at the intersection of luxury, design, technology and culture.
- Summit has a point of view, not just a service list.
- Summit is selective, contemporary and internationally credible.
- Summit is building something larger than a brochure site.

## Suggested Component Sequence
1. `hero_block`
2. `showreel_block`
3. `value_proposition_block`
4. `capability_triptych`
5. `case_study_archive_grid` or selected work panel
6. `podcast_landing_hero` or Tastemakers feature panel
7. `download_block`
8. `featured_article_block` or Future of Luxury panel
9. `sectors_grid`
10. `cta_footer_block`

## CTA Logic
Primary CTA:
- Design Tomorrow

Secondary CTA:
- Explore Our Work
- View All Articles
- Listen to Tastemakers

## SEO / Schema Notes
Primary focus:
- luxury brand strategy
- luxury experience design
- luxury digital transformation

Schema:
- Organization
- ProfessionalService
- WebPage
- VideoObject where relevant

## Relationships
Should route clearly to:
- What We Do
- Work Showcase
- The Future of Luxury
- Tastemakers
- Design Tomorrow

## Exclusions
- bloated agency claims
- overlong service explanation
- too many testimonials
- weak proof panels
- excessive visual noise

## Status
Approved framework

---

# 2. Who We Are

## Strategic Purpose
To define Summit’s identity, worldview and cultural position without lapsing into generic agency biography.

## Primary Audience
Prospective clients assessing credibility and fit.

## Secondary Audience
Media, collaborators, future hires and culturally aligned visitors.

## Desired Outcome
The visitor should understand what Summit believes, how it thinks and why its point of view is distinct.

## Template Type
Standard strategic page template

## Key Messages
- Summit is strategically independent and culturally literate.
- Summit is built around luxury, story, design and transformation.
- Summit has a strong editorial and future-facing worldview.
- Gregory Gray’s thought leadership is an asset, but the page is about the house, not just the founder.

## Suggested Component Sequence
1. `hero_block`
2. `value_proposition_block`
3. `split_media_text_block` for founding point of view
4. `three_column_feature_block` for principles / values
5. founder interview panel
6. team montage / culture panel
7. selected article or thought-leadership panel
8. `cta_footer_block`

## CTA Logic
Primary CTA:
- Explore What We Do

Secondary CTA:
- Read The Future of Luxury
- Start a Conversation

## SEO / Schema Notes
Primary focus:
- luxury brand consultancy
- design consultancy for luxury brands
- Gregory Gray
- luxury thought leadership

Schema:
- AboutPage
- Organization
- Person
- Article
- VideoObject where applicable

## Relationships
Should connect to:
- What We Do
- Work Showcase
- The Future of Luxury
- Design Tomorrow

## Exclusions
- long staff biographies
- agency clichés about passion and creativity
- internal org-chart energy
- excessive autobiography

## Status
Approved framework

---

# 3. What We Do

## Strategic Purpose
To define Summit’s service logic clearly and elegantly through its three core pillars.

## Primary Audience
Prospective clients evaluating service fit.

## Secondary Audience
Agencies, collaborators, partners and internal stakeholders.

## Desired Outcome
The visitor should understand Summit’s three-part service architecture and be able to route quickly into the most relevant pillar.

## Template Type
Strategic landing page template

## Key Messages
- Summit’s offer is structured, not sprawling.
- The three pillars are the core operating logic.
- Summit works across luxury sectors with strategic coherence.
- Services sit at the intersection of design, brand and transformation.

## Suggested Component Sequence
1. `hero_block`
2. `capability_triptych`
3. supporting intro on how the three pillars work together
4. `three_column_feature_block` or service depth panel
5. sectors or audience relevance panel
6. selected work proof block
7. related editorial / ideas block
8. `cta_footer_block`

## CTA Logic
Primary CTA:
- Explore Brand Strategy
- Explore Experience Design
- Explore Digital Transformation

Secondary CTA:
- View Selected Work
- Start a Conversation

## SEO / Schema Notes
Primary focus:
- luxury brand strategy
- luxury experience design
- luxury digital transformation

Schema:
- WebPage
- Service
- ProfessionalService

## Relationships
Should connect to:
- Brand Strategy
- Experience Design
- Digital Transformation
- Work Showcase
- Design Tomorrow

## Exclusions
- over-expanded service laundry lists on the landing page
- duplicated copy across pillar pages
- vague consultancy language

## Status
Approved framework

---

# 4. Brand Strategy

## Strategic Purpose
To define Summit’s brand strategy offer as a clear consulting discipline for luxury brands.

## Primary Audience
Luxury clients seeking positioning, clarity and strategic direction.

## Secondary Audience
Agencies or collaborators seeking specialist strategic depth.

## Desired Outcome
The visitor should understand what Summit means by brand strategy and why it matters commercially and culturally.

## Template Type
Service child page template

## Key Messages
- strategy clarifies distinction
- audience understanding matters
- brand position shapes experience and growth
- luxury requires sharper thinking, not louder messaging

## Suggested Component Sequence
1. `hero_block`
2. service proposition block
3. `three_column_feature_block` for capabilities
4. audience / sector relevance block
5. selected case studies
6. related Future of Luxury articles
7. `cta_footer_block`

## CTA Logic
Primary CTA:
- Start a Conversation

Secondary CTA:
- View Related Work
- Read Related Thinking

## SEO / Schema Notes
Primary focus:
- luxury brand strategy
- luxury brand positioning
- luxury consultancy

Schema:
- Service
- WebPage

## Relationships
Should connect to:
- Work Showcase
- case studies tagged Brand Strategy
- related Future of Luxury articles
- Design Tomorrow

## Exclusions
- generic brand jargon
- brand identity confusion unless intentionally linked

## Status
Approved framework

---

# 5. Experience Design

## Strategic Purpose
To define Summit’s offer around experience, storytelling and customer journey design.

## Primary Audience
Luxury operators looking to refine digital, physical or hybrid brand experiences.

## Secondary Audience
Agencies and collaborators seeking specialist experience depth.

## Desired Outcome
The visitor should understand that Summit’s view of experience design includes customer choreography, storytelling and interface logic.

## Template Type
Service child page template

## Key Messages
- experience is how luxury becomes legible
- design is not only visual, but behavioural
- digital and physical touchpoints should feel coherent
- storytelling and interaction shape perception

## Suggested Component Sequence
1. `hero_block`
2. service proposition block
3. experience capabilities block
4. split media / text proof or philosophy panel
5. selected work panel
6. related article / podcast block
7. `cta_footer_block`

## CTA Logic
Primary CTA:
- Start a Conversation

Secondary CTA:
- Explore Our Work
- Read The Future of Luxury

## SEO / Schema Notes
Primary focus:
- luxury experience design
- luxury customer experience
- luxury digital experience

Schema:
- Service
- WebPage

## Relationships
Should connect to:
- relevant case studies
- editorial pieces on experience and luxury
- Design Tomorrow

## Exclusions
- empty UX jargon
- over-technical process diagrams unless genuinely useful

## Status
Approved framework

---

# 6. Digital Transformation

## Strategic Purpose
To define Summit’s offer around digital systems, platforms, data and modernisation for luxury organisations.

## Primary Audience
Luxury businesses seeking digital clarity, infrastructure or transformation.

## Secondary Audience
Partner agencies and operators requiring strategic digital depth.

## Desired Outcome
The visitor should understand that Summit’s digital transformation offer is elegant, strategic and operational, not merely technical.

## Template Type
Service child page template

## Key Messages
- transformation is not just software adoption
- luxury brands need better systems as well as better stories
- digital infrastructure shapes modern brand experience
- clarity, performance and platform coherence matter

## Suggested Component Sequence
1. `hero_block`
2. service proposition block
3. capability block
4. systems / platform logic block
5. related work block
6. related editorial or media block
7. `cta_footer_block`

## CTA Logic
Primary CTA:
- Start a Conversation

Secondary CTA:
- View Related Work
- Explore Summit’s Thinking

## SEO / Schema Notes
Primary focus:
- luxury digital transformation
- luxury digital consultancy
- luxury platform strategy

Schema:
- Service
- WebPage

## Relationships
Should connect to:
- relevant case studies
- relevant editorial
- Design Tomorrow

## Exclusions
- generic digital-agency language
- overly technical implementation detail for this level

## Status
Approved framework

---

# 7. Work Showcase

## Strategic Purpose
To curate Summit’s proof and show the quality, range and intelligence of its work.

## Primary Audience
Prospective clients evaluating credibility.

## Secondary Audience
Collaborators, media, future hires and culturally aligned visitors.

## Desired Outcome
The visitor should understand Summit’s work through selective, well-framed proof rather than volume.

## Template Type
Work archive template

## Key Messages
- Summit’s work is selective
- categories span strategy, design and media
- proof matters, but curation matters more
- Summit can frame work intelligently even where the client list is still growing

## Suggested Component Sequence
1. `hero_block`
2. selected work grid
3. showreel or film-led panel
4. strategic framing / approach panel
5. download or visual essay panel
6. logos / signals of category adjacency
7. `cta_footer_block`

## CTA Logic
Primary CTA:
- View Project

Secondary CTA:
- Start a Conversation
- Read Related Thinking

## SEO / Schema Notes
Primary focus:
- luxury consultancy work
- luxury case studies
- luxury branding and digital work

Schema:
- CollectionPage
- CreativeWork
- ItemList

## Relationships
Should connect to:
- case study pages
- services
- relevant Future of Luxury pieces
- Design Tomorrow

## Exclusions
- weak projects
- filler entries
- over-claiming prestige

## Status
Approved framework

---

# 8. Case Study Template

## Strategic Purpose
To present an individual project as selective, strategic proof.

## Primary Audience
Prospective clients assessing Summit’s capability.

## Secondary Audience
Editors, collaborators, future clients researching fit.

## Desired Outcome
The visitor should understand what the challenge was, what Summit saw, what Summit built and what that meant.

## Template Type
Case study template

## Key Messages
- Summit understands the problem deeply
- the work is strategic and deliberate
- the result has relevance beyond aesthetics

## Suggested Component Sequence
1. case study hero
2. project summary
3. challenge
4. strategic idea
5. what we built
6. outcome
7. project gallery / media
8. related case studies
9. related service page
10. `cta_footer_block`

## CTA Logic
Primary CTA:
- Start a Conversation

Secondary CTA:
- View Related Work
- Explore Relevant Service

## SEO / Schema Notes
Schema:
- CreativeWork
- CaseStudy where supported

## Relationships
Must connect to:
- relevant services
- related work
- potentially relevant articles or podcast pages

## Exclusions
- generic agency bragging
- vague outcomes without context
- bloated before/after theatre without strategic meaning

## Status
Approved framework

---

# 9. The Future of Luxury

## Strategic Purpose
To operate as Summit’s flagship editorial archive and thought-leadership property.

## Primary Audience
Executives, founders, strategists, operators and tastemakers in global luxury and adjacent sectors.

## Secondary Audience
Journalists, creators, investors and culturally engaged readers.

## Desired Outcome
The visitor should understand that Summit has a serious point of view on luxury, culture, technology and value.

## Template Type
Editorial archive landing template

## Key Messages
- Summit publishes serious thinking
- the archive is curated and category-aware
- this is an owned editorial destination, not a blog afterthought

## Suggested Component Sequence
1. `hero_block`
2. featured article block
3. editorial archive grid
4. category or theme navigation
5. related download or deck block
6. related Tastemakers panel
7. subscribe module
8. `cta_footer_block`

## CTA Logic
Primary CTA:
- Read Article

Secondary CTA:
- Subscribe
- Explore Tastemakers

## SEO / Schema Notes
Primary focus:
- future of luxury
- luxury strategy
- luxury thought leadership

Schema:
- Blog
- CollectionPage
- ItemList

## Relationships
Should connect to:
- article pages
- services
- Tastemakers
- downloads
- Design Tomorrow where contextually appropriate

## Exclusions
- bulk content dumps
- weak archive items
- obvious republished social filler

## Status
Approved framework

---

# 10. Article Template

## Strategic Purpose
To present a Future of Luxury article as premium long-form editorial.

## Primary Audience
Readers interested in luxury strategy, culture and change.

## Secondary Audience
Prospective clients, media, collaborators and subscribers.

## Desired Outcome
The visitor should read comfortably, trust the piece and move deeper into the Summit ecosystem.

## Template Type
Article template

## Key Messages
Dependent on article, but structurally should reinforce:
- authority
- clarity
- strategic relevance
- editorial quality

## Suggested Component Sequence
1. `article_hero`
2. `article_metadata_bar`
3. `rich_text_body`
4. `pull_quote_block` where relevant
5. author module
6. related reading block
7. subscribe module
8. optional service or podcast relevance block

## CTA Logic
Primary CTA:
- Read Related Article
- Subscribe

Secondary CTA:
- Explore Tastemakers
- Start a Conversation, only where contextually earned

## SEO / Schema Notes
Schema:
- Article
- BlogPosting
- Person

## Relationships
Should connect to:
- related articles
- relevant service pages
- relevant case studies
- relevant episodes
- downloads

## Exclusions
- aggressive sales interruptions
- poor transcript-like formatting
- weak metadata discipline

## Status
Approved framework

---

# 11. Tastemakers

## Strategic Purpose
To present Tastemakers as a serious media property rather than a side-project podcast page.

## Primary Audience
Luxury, culture and strategy audiences interested in serious conversation.

## Secondary Audience
Guests, sponsors, collaborators, subscribers and future community members.

## Desired Outcome
The visitor should understand the proposition of Tastemakers, explore seasons and subscribe.

## Template Type
Podcast landing template

## Key Messages
- Tastemakers is editorially led
- it sits naturally within the Summit world
- it creates recurring value through guests, seasons and ideas

## Suggested Component Sequence
1. `podcast_landing_hero`
2. featured season or featured episode panel
3. season overview grid
4. proposition / thesis panel
5. guest / network signal block
6. related article panel
7. subscribe module
8. `cta_footer_block`

## CTA Logic
Primary CTA:
- Listen to the Podcast

Secondary CTA:
- Explore Season One
- Subscribe

## SEO / Schema Notes
Schema:
- PodcastSeries
- CollectionPage

## Relationships
Should connect to:
- seasons
- episodes
- related articles
- newsletter / ActiveCampaign capture
- future sponsor routes where appropriate

## Exclusions
- generic podcast embed pages
- weak summaries
- cluttered platform badges everywhere

## Status
Approved framework

---

# 12. Season Template

## Strategic Purpose
To frame a season as an editorial product with a clear thesis and thematic coherence.

## Primary Audience
Listeners and readers exploring the Tastemakers archive.

## Secondary Audience
Guests, sponsors, editors and prospective subscribers.

## Desired Outcome
The visitor should understand what the season is about and move naturally into episodes.

## Template Type
Season template

## Key Messages
- each season has a point of view
- episodes belong to a coherent argument
- the season is worth following as a whole

## Suggested Component Sequence
1. season hero
2. season thesis / intro
3. featured episode
4. episode grid
5. guest universe or theme block
6. related editorial block
7. subscribe module
8. `cta_footer_block`

## CTA Logic
Primary CTA:
- Listen to an Episode

Secondary CTA:
- Subscribe
- Read Related Article

## SEO / Schema Notes
Schema:
- PodcastSeries or related podcast collection object
- ItemList

## Relationships
Should connect to:
- Tastemakers landing page
- episodes
- related articles
- future downloads or sponsor pages where relevant

## Exclusions
- flat episode dumps
- no-thesis seasons
- weak summaries

## Status
Approved framework

---

# 13. Episode Template

## Strategic Purpose
To present a single episode as a premium editorial/media page.

## Primary Audience
Listeners, readers, subscribers and topic-specific visitors.

## Secondary Audience
Guests, sponsors, media and search visitors.

## Desired Outcome
The visitor should listen, read, subscribe and move deeper into Summit content.

## Template Type
Episode template

## Key Messages
- the conversation matters
- the guest matters
- the topic has broader relevance
- the page is part of a larger ecosystem

## Suggested Component Sequence
1. `episode_hero`
2. `episode_player_block`
3. guest module
4. show notes block
5. transcript block
6. related episode block
7. related article block
8. subscribe module
9. `cta_footer_block`

## CTA Logic
Primary CTA:
- Listen Now

Secondary CTA:
- Read the Transcript
- Explore Related Episode
- Subscribe

## SEO / Schema Notes
Schema:
- PodcastEpisode
- AudioObject
- VideoObject where relevant

## Relationships
Should connect to:
- season page
- series page
- related articles
- related episodes
- downloads where relevant

## Exclusions
- raw transcript dumping
- empty guest modules
- poor audio-player integration

## Status
Approved framework

---

# 14. Get In Touch / Design Tomorrow

## Strategic Purpose
To create a premium, intuitive conversion environment for conversations, enquiries and relationship starts.

## Primary Audience
Qualified prospective clients and collaborators.

## Secondary Audience
Media contacts, guests, speaking requests and selected partners.

## Desired Outcome
The visitor should know how to contact Summit and feel comfortable doing so.

## Template Type
Contact / conversion template

## Key Messages
- Summit welcomes serious conversation
- there is a clear route for enquiries
- this is thoughtful contact design, not an afterthought form

## Suggested Component Sequence
1. `hero_block`
2. `connect_contact_tabs`
3. contact details block
4. expectation / what happens next block
5. optional related proof or editorial context block
6. `cta_footer_block` if appropriate

## CTA Logic
Primary CTA:
- Submit Enquiry

Secondary CTA:
- Contact Directly
- Explore Related Work

## SEO / Schema Notes
Primary focus:
- luxury consultancy contact
- design consultancy contact
- London contact

Schema:
- ContactPage
- ContactPoint

## Relationships
Should connect to:
- ActiveCampaign / CRM flow
- relevant service pages
- selected proof if useful
- Summit contact details and utility links

## Exclusions
- ugly default forms
- overlong qualification flows
- friction-heavy submission logic
- generic contact-page emptiness

## Status
Approved framework

---

# 15. Careers

## Strategic Purpose
To present Summit as a selective place to contribute, not a generic recruitment portal.

## Primary Audience
Potential hires, collaborators and aligned contributors.

## Secondary Audience
General brand readers curious about culture.

## Desired Outcome
The visitor should understand what kind of people Summit values and how to express interest.

## Template Type
Standard strategic page template

## Key Messages
- Summit values taste, rigour and initiative
- Summit is selective
- Summit’s culture matters
- roles may be evolving rather than always formally listed

## Suggested Component Sequence
1. `hero_block`
2. culture / values block
3. role or opportunity block
4. working style / expectations block
5. expression of interest form or CTA
6. `cta_footer_block`

## CTA Logic
Primary CTA:
- Express Interest

Secondary CTA:
- Learn More About Summit

## SEO / Schema Notes
Schema:
- WebPage
- JobPosting only if formal role listings exist

## Relationships
Should connect to:
- Who We Are
- contact or application flow

## Exclusions
- corporate recruitment clichés
- fake “we’re a family” nonsense
- overbuilt careers infrastructure before needed

## Status
Approved framework

---

# 16. Client Lounge

## Strategic Purpose
To provide a controlled front-end for private or semi-private client-facing resources if needed.

## Primary Audience
Current clients and selected stakeholders.

## Secondary Audience
None, ideally.

## Desired Outcome
The visitor should access relevant resources cleanly and securely.

## Template Type
Utility / gated-access template

## Key Messages
- this is purposeful
- this is controlled
- this supports client experience

## Suggested Component Sequence
1. intro / utility hero
2. gated access block or sign-in logic
3. resource overview or lounge navigation
4. support / contact block

## CTA Logic
Primary CTA:
- Enter Client Lounge
or
- Request Access

## SEO / Schema Notes
Usually:
- noindex
- protected or semi-protected
- minimal public metadata

## Relationships
Should connect to:
- account logic later if implemented
- private resource structures
- contact support

## Exclusions
- public content clutter
- pretending this is a full portal before it exists

## Status
Approved framework

---

## Page Relationships Matrix

At a minimum, the following relationships should exist:

### Home
Links to:
- What We Do
- Work Showcase
- The Future of Luxury
- Tastemakers
- Design Tomorrow

### Who We Are
Links to:
- What We Do
- The Future of Luxury
- Design Tomorrow

### What We Do
Links to:
- Brand Strategy
- Experience Design
- Digital Transformation
- Work Showcase
- Design Tomorrow

### Service Pages
Link to:
- related case studies
- related articles
- Design Tomorrow

### Work Showcase
Links to:
- case study pages
- relevant service pages
- Design Tomorrow

### The Future of Luxury
Links to:
- article pages
- related services
- Tastemakers
- subscribe routes

### Article Pages
Link to:
- related articles
- relevant services
- relevant episodes
- subscribe routes

### Tastemakers
Links to:
- season pages
- episode pages
- related editorial
- subscribe routes

### Episode Pages
Link to:
- season page
- related episodes
- related articles
- subscribe routes

### Contact Page
Links to:
- services
- work
- key contact routes

---

## Approval Rules

A page spec should not be treated as approved until:
- its purpose is clear
- its CTA logic is clear
- its component sequence is plausible
- its relationships are defined
- its SEO role is understood
- it does not duplicate another page’s job

### Approval Status Labels
- Draft
- In Review
- Approved
- In Build
- Live
- Needs Revision

---

## Exclusions and Anti-Patterns

Avoid:
- pages with overlapping purpose
- pages with no clear CTA
- building pages before their role is defined
- writing copy before structure is agreed
- inventing components at page-spec stage unless truly necessary
- letting SEO distort the page’s strategic role
- allowing a page to become a general dumping ground

If a page cannot explain itself in one sentence, it may not yet deserve to exist.

---

## Success Criteria

The page-spec system is successful when:
- every important page has a defined job
- designers and developers can build with confidence
- pages feel distinct but coherent
- conversion routes are clear
- the site remains compact but weighty
- editorial, consultancy and media pages connect intelligently
- Summit grows through architecture rather than accumulation

In short:  
The page-spec system is successful when no important page feels accidental.

---

## How AI Should Use This File

When generating page-specific work for Summit, AI should:
- start from the relevant approved page spec
- preserve the page’s strategic purpose
- use approved components
- respect page-level exclusions
- maintain relationship logic across the site
- avoid introducing extra panels without good reason
- treat CTA logic as part of the page’s structure, not an afterthought

If a proposed page version weakens clarity, overlap or cohesion, it should be revised.

---

## Final Page Ethos

Summit’s pages should feel like rooms in a well-designed house.

Each has its own function.  
Each has its own atmosphere.  
Each connects intelligently to the next.  
None exist merely to fill the floor plan.