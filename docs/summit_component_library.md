# summit_component_library.md

## Purpose of this file

This file defines the reusable front-end components that make up Summit Communication Group’s digital system.

It governs:
- component inventory
- component purpose
- component hierarchy
- content expectations
- visual behaviour
- layout logic
- interaction rules
- responsive rules
- editorial and media modules
- utility modules
- reuse discipline

If:
- `summit_soul.md` defines what Summit is
- `summit_voice.md` defines how Summit sounds
- `summit_site_architecture.md` defines how Summit is organised
- `summit_build_system.md` defines how Summit is built
- `summit_design_system.md` defines how Summit looks and feels

then this file defines the actual kit of parts from which the site is assembled.

This document should be used by:
- designers
- developers
- technical leads
- AI systems generating layouts or templates
- content teams preparing structured page specifications

Rule:
Pages should be composed from this library wherever possible.
Do not invent new components casually.
If a new component is genuinely needed, it should be added here deliberately rather than improvised in one corner of the site and forgotten like a bad decision at Cannes.


## Core Component Philosophy

Summit’s component system should feel:
- restrained
- reusable
- structurally intelligent
- typographically led
- calm
- premium
- editorial
- durable

Components should:
- do one job clearly
- support hierarchy
- survive without decorative media
- work across light and dark contexts where appropriate
- scale across the site without losing character

Components should not:
- become miniature page builders
- contain endless optional states
- rely on media to feel valuable
- imitate generic SaaS UI patterns
- be so “flexible” they become incoherent


## Component Categories

Summit’s components fall into five groups:

### 1. Global Components
Used across the entire site.

### 2. Core Page Components
Used on strategic and consultancy pages.

### 3. Proof Components
Used in Work Showcase and case studies.

### 4. Editorial Components
Used in The Future of Luxury.

### 5. Podcast / Media Components
Used in Tastemakers and related media surfaces.

### 6. Utility Components
Used for contact, subscriptions, gated spaces and other supporting logic.


## Component Naming Principle

Use clear functional names.

Good:
- `hero_block`
- `showreel_block`
- `article_card`
- `episode_player`
- `connect_contact_tabs`

Avoid:
- `fancy-panel-2`
- `feature-box-alt-dark`
- `luxury-card-pro`
- anything that sounds like a plugin marketplace crime

Names should describe purpose, not mood.


## Component Usage Rules

1. Every component must have a clear purpose.
2. Every component should have a predictable structure.
3. Variants should be limited and intentional.
4. Responsive behaviour must be defined.
5. Components should inherit the design system, not override it.
6. Rich media should be optional unless the component is specifically media-led.
7. Copy-heavy components must respect Summit voice.
8. Components should support structured content where relevant.

---

## Global Components

### 1. `sticky_top_navigation`

**Purpose:**  
Global navigation anchor in the normal state.

**Required content:**
- Menu
- Brand
- Design Tomorrow

**Order is prescriptive:**
- Menu
- Brand
- Design Tomorrow

**Behaviour:**
- sticky
- visually stable
- minimal
- always legible
- should remain calm on scroll

**Design notes:**
- lightweight but authoritative
- no visual clutter
- should feel architectural rather than app-like

**Responsive notes:**
- remains present across breakpoints
- spacing and tap area must adapt cleanly on mobile

---

### 2. `expanded_mega_menu`

**Purpose:**  
Full designed navigation environment.

**Structural inspiration:**  
Matter Of Form’s expanded mega menu behaviour and feel.

**Required areas:**
- preserved top navigation
- primary navigation
- secondary / utility navigation
- selected Future of Luxury feed
- bottom utility layer

**Primary navigation:**
- Who We Are
- What We Do
- Work Showcase
- The Future of Luxury
- Get In Touch

**Secondary / utility navigation:**
- Careers
- Client Lounge
- Tastemakers Podcast

**Bottom utility layer:**
- positioning statement
- Start a Conversation
- London +44

**Behaviour:**
- opens as a designed overlay, not a dropdown
- calm motion
- clear hierarchy
- should feel spatial rather than merely stacked

**Responsive notes:**
- must remain elegant on mobile
- hierarchy must survive collapse
- editorial feed may reduce in density, but should not vanish without intent

---

### 3. `sticky_bottom_bar`

**Purpose:**  
Persistent lower utility and positioning strip.

**Required content:**
- Summit positioning line
- Start a Conversation
- London +44

**Behaviour:**
- fixed or sticky as designed
- should feel integrated, not bolted on
- must not overwhelm the viewport

**Use case:**
- supports brand positioning and conversion without shouting

---

### 4. `site_footer`

**Purpose:**  
Global footer with calm exit structure.

**Expected content:**
- core navigation
- utility links
- legal links
- social links where appropriate
- copyright / company information
- optional newsletter prompt if not redundant

**Design notes:**
- should feel controlled and quiet
- no bloated sitemap dumping ground

---

### 5. `cta_footer_block`

**Purpose:**  
Page-ending conversion block.

**Typical use:**
- Start a Conversation
- Explore Our Work
- Read The Future of Luxury
- Explore Season One

**Structure:**
- headline
- supporting text
- primary CTA
- optional secondary CTA

**Rule:**
One clear action, not a buffet of nervous options.

---

## Core Page Components

### 6. `hero_block`

**Purpose:**  
Open a page with clear hierarchy and brand confidence.

**Typical content:**
- eyebrow / category
- H1
- body copy or standfirst
- primary CTA
- optional secondary CTA

**Variants:**
- text-only
- text + media
- text + background image/video
- text + split layout

**Design notes:**
- large negative space
- strong headline
- copy should sit well below the headline, not breathe down its neck

---

### 7. `showreel_block`

**Purpose:**  
Present Summit’s cinematic or visual statement.

**Typical content:**
- H2
- body copy
- embedded video / showreel
- watch CTA

**Use cases:**
- homepage
- work showcase
- selected brand or media pages

**Rule:**
Showreel should feel like an authored statement, not a random video player somebody forgot to style.

---

### 8. `value_proposition_block`

**Purpose:**  
State Summit’s positioning in plain strategic terms.

**Typical content:**
- H2
- paragraph or two
- no unnecessary complexity

**Use cases:**
- homepage
- Who We Are
- What We Do

**Design notes:**
- can be text-led
- should rely on spacing and typography more than decoration

---

### 9. `capability_triptych`

**Purpose:**  
Present the three core pillars:
- Brand Strategy
- Experience Design
- Digital Transformation

**Structure:**
- section intro
- three columns or cards
- each item includes:
  - title
  - short description
  - CTA

**Rule:**
This component is central to Summit’s logic and should appear with great consistency.

---

### 10. `three_column_feature_block`

**Purpose:**  
General-purpose three-column structured information block.

**Use cases:**
- service pillars
- reasons to work with Summit
- cultural values
- audience segments
- editorial themes

**Structure:**
- optional section intro
- three aligned entries
- each entry includes:
  - title
  - short copy
  - optional link

**Design notes:**
- should feel spacious and elegant
- not dashboard-like

---

### 11. `split_media_text_block`

**Purpose:**  
Present text and media side-by-side with calm asymmetry.

**Variants:**
- media left / text right
- text left / media right

**Use cases:**
- homepage
- Who We Are
- What We Do
- case studies

**Rule:**
Do not let the layout become mechanically alternating merely to prove you can.

---

### 12. `sectors_grid`

**Purpose:**  
Display Summit’s sector coverage within the luxury economy.

**Expected items:**
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

**Design notes:**
- should support semantic clarity
- may be presented as a grid, list or cluster block
- should remain elegant, not directory-like

---

### 13. `quote_block`

**Purpose:**  
Highlight a strong line of thinking, testimonial excerpt or strategic provocation.

**Use cases:**
- homepage
- case studies
- editorial pages
- podcast pages

**Variants:**
- strategic quote
- client quote
- founder line
- manifesto line

**Rule:**
Use sparingly. Quotes should punctuate rhythm, not turn the site into a collection of framed opinions.

---

### 14. `testimonial_slider`

**Purpose:**  
Display selected client endorsements.

**Structure:**
- quote
- name
- title
- organisation

**Rule:**
Only use if the testimonial quality is high enough.
Luxury clients can smell filler testimonials from across the room.

---

### 15. `download_block`

**Purpose:**  
Promote downloadable assets such as The Characteristics of Luxury.

**Structure:**
- title
- short explanation
- asset image / cover
- CTA

**Use cases:**
- homepage
- Work Showcase
- The Future of Luxury
- selected media or proof pages

---

## Proof Components

### 16. `case_study_card`

**Purpose:**  
Present a single selected work item within an archive or related block.

**Typical content:**
- image or visual
- title
- category
- short description
- CTA

**Use cases:**
- Work Showcase
- related work blocks
- homepage selected work panel

**Design notes:**
- must feel editorial and premium
- not like a generic portfolio tile

---

### 17. `case_study_archive_grid`

**Purpose:**  
Arrange multiple case study cards in a controlled archive.

**Variants:**
- 2-column
- 3-column
- staggered or mixed hierarchy where appropriate

**Rule:**
The archive must feel curated.
Do not use filters unless there is enough work to justify them.

---

### 18. `challenge_solution_outcome_block`

**Purpose:**  
Summarise the narrative arc of a case study.

**Structure:**
- Challenge
- Strategic Idea
- What We Built / Outcome

**Use case:**
- case study pages

**Design notes:**
- may use three-column logic or stacked sections
- should be clear, not overdesigned

---

### 19. `project_gallery_block`

**Purpose:**  
Display stills, mock-ups, interface frames or visual proof.

**Variants:**
- full-width image sequence
- editorial gallery
- stacked media
- mixed image/video gallery

**Rule:**
Should feel calm and intentional, not like a gallery plugin wandered in uninvited.

---

## Editorial Components

### 20. `article_card`

**Purpose:**  
Present a Future of Luxury article within an archive, feature block or related content module.

**Typical content:**
- image
- headline
- category
- date
- read time
- excerpt
- CTA

**Design notes:**
- headline hierarchy matters more than thumbnail theatrics
- metadata must remain quiet

---

### 21. `featured_article_block`

**Purpose:**  
Highlight one editorial piece with greater prominence.

**Structure:**
- image
- category
- headline
- excerpt
- metadata
- CTA

**Use cases:**
- The Future of Luxury landing page
- homepage
- selected content intersections

---

### 22. `article_hero`

**Purpose:**  
Open an editorial article page.

**Required content:**
- category
- H1
- standfirst / deck
- author
- publish date
- read time
- featured image

**Rule:**
This should feel premium and readable, not like a default blog template dressed up for a funeral.

---

### 23. `article_metadata_bar`

**Purpose:**  
Provide clean metadata context.

**Typical items:**
- category
- author
- date
- read time

**Design notes:**
- quiet
- crisp
- never visually louder than the headline

---

### 24. `rich_text_body`

**Purpose:**  
Render long-form editorial content.

**Requirements:**
- strong reading width
- generous line-height
- clean heading hierarchy
- support for:
  - paragraphs
  - H2/H3
  - links
  - lists
  - blockquotes
  - inline images
  - pull quotes

**Rule:**
Reading experience is part of the luxury proposition.

---

### 25. `pull_quote_block`

**Purpose:**  
Interrupt long-form reading with a typographic pause.

**Use cases:**
- articles
- case studies
- podcast transcripts
- manifesto pages

**Rule:**
Should feel like a deliberate editorial choice, not a design-school reflex.

---

### 26. `author_module`

**Purpose:**  
Present Gregory Gray or another authorised author entity.

**Typical content:**
- author image
- short bio
- related article count / link
- link to broader profile or related section

**Use cases:**
- article pages
- selected editorial intersections

---

### 27. `related_reading_block`

**Purpose:**  
Surface 3–4 relevant editorial pieces.

**Structure:**
- section title
- article cards

**Logic:**
- category relevance
- thematic relevance
- strategic linking

---

### 28. `subscribe_module`

**Purpose:**  
Capture first-party audience data.

**Use cases:**
- homepage
- The Future of Luxury
- article pages
- Tastemakers
- episode pages

**Structure:**
- headline
- short value statement
- form
- consent note if needed

**Rule:**
Must feel designed and intentional, not like a plugin form in a borrowed jacket.

---

## Podcast / Media Components

### 29. `podcast_landing_hero`

**Purpose:**  
Open the Tastemakers page with series-level clarity.

**Structure:**
- eyebrow
- series title
- description
- primary CTA
- secondary CTA
- optional cover art or motion element

---

### 30. `season_overview_grid`

**Purpose:**  
Summarise season episodes or themes.

**Structure:**
- season intro
- episode or theme cards

**Use cases:**
- Tastemakers landing
- season pages

---

### 31. `episode_card`

**Purpose:**  
Present an individual episode in a grid or list.

**Content:**
- title
- season / episode number
- guest
- duration
- summary
- CTA

---

### 32. `episode_hero`

**Purpose:**  
Open a podcast episode page.

**Required content:**
- series label
- season / episode number
- H1
- deck
- host / guest metadata
- duration
- artwork
- listening CTA

---

### 33. `episode_player_block`

**Purpose:**  
Present audio or video playback with minimal friction.

**Requirements:**
- clean player integration
- platform links where relevant
- optional transcript jump link

**Rule:**
Should feel integrated into the page, not like an embedded orphan.

---

### 34. `guest_module`

**Purpose:**  
Introduce the episode guest clearly and credibly.

**Content:**
- image
- name
- title
- organisation
- short biography

**Use case:**
- episode pages
- season pages where appropriate

---

### 35. `transcript_block`

**Purpose:**  
Render searchable, readable transcript content.

**Requirements:**
- good reading width
- speaker hierarchy if used
- optional section dividers
- optional time references later

**Rule:**
Transcripts are not a dumping ground. They are a strategic SEO and usability asset.

---

### 36. `show_notes_block`

**Purpose:**  
Provide editorial summary and referenced topics.

**Content:**
- summary
- key themes
- references
- related links

---

### 37. `related_episode_block`

**Purpose:**  
Link users deeper into the series.

**Structure:**
- previous / next or 3 related episodes
- clear episode cards

---

## Utility Components

### 38. `connect_contact_tabs`

**Purpose:**  
Present the Design Tomorrow / Get In Touch interaction model.

**Tabs:**
- Connect
- Contact

**Connect contents:**
- enquiry form
- what happens next
- who this is for

**Contact contents:**
- direct details
- London presence
- editorial / social links

**Rule:**
This component is strategically important and should feel finished, not utilitarian.

---

### 39. `contact_details_block`

**Purpose:**  
Display structured contact information.

**Use cases:**
- Get In Touch
- footer or utility areas where appropriate

**Typical items:**
- email
- phone
- London
- LinkedIn
- relevant editorial links

---

### 40. `form_confirmation_block`

**Purpose:**  
Provide a calm, branded post-submission state.

**Rule:**
No chirpy software optimism.
It should feel composed and human.

---

### 41. `gated_access_block`

**Purpose:**  
Handle access-controlled invitations into Client Lounge or protected assets.

**Use cases:**
- Client Lounge
- selected documents
- future member environments

---

### 42. `utility_link_cluster`

**Purpose:**  
Present calm clusters of secondary links.

**Use cases:**
- footer
- menu overlay
- contact pages
- editorial pathways

---

## Component States and Variants

Each component should define only the states it genuinely needs.

Typical states may include:
- light
- dark
- media-led
- text-led
- compact
- featured
- static
- hover-active
- loading
- form-success
- disabled where needed

Rule:
Do not create twelve variants for the pleasure of future indecision.

If a component needs too many states, the component itself may be badly defined.

---

## Responsive Rules for Components

Every component must define:
- mobile layout
- tablet layout
- desktop layout

Questions each component should answer:
1. Does hierarchy survive on mobile?
2. Does spacing remain elegant?
3. Does interaction remain clear?
4. Does media remain stable?
5. Does the component still feel like Summit when compressed?

No component is complete until it behaves properly across breakpoints.

---

## Content Discipline Rules

Components should not encourage bad content habits.

They should:
- support structured fields where useful
- make hierarchy obvious
- reduce the temptation for editorial clutter
- discourage vague copy inflation
- preserve rhythm and restraint

The component library is partly a design system and partly a behavioural corrective.

---

## When to Create a New Component

A new component should only be created when:
- an existing component genuinely cannot do the job
- the new use case is likely to recur
- the difference is structural, not merely cosmetic
- it improves the system rather than complicates it

Before creating a new component, ask:
1. Is this actually new, or merely a variant?
2. Will this recur across more than one page?
3. Does it strengthen or weaken coherence?
4. Could this be solved by better content inside an existing component?

If in doubt, resist invention.

---

## Anti-Patterns

Avoid:
- bespoke one-off panels that break the system
- over-parameterised blocks
- visually noisy cards
- decorative hover states
- plugin-generated modules that do not match the design language
- cloned components with microscopic differences
- layouts that depend on perfect imagery
- content blocks that invite lazy copy padding

If a component looks flexible but produces weak pages, it is not a good component.

---

## Success Criteria

The component library is successful when:
- the site can be built largely from these parts
- pages feel varied without feeling inconsistent
- developers know what each module is for
- content teams know what each module needs
- motion, spacing and typography remain coherent
- the site can grow without becoming visually unruly

In short:
The component library should make Summit feel directed, not assembled.

---

## How AI Should Use This File

When generating layouts, templates, page specs or implementation logic for Summit, AI should:
- use this library as the default source of page-building parts
- avoid inventing unnecessary new modules
- pair the right component with the right content purpose
- preserve restraint and hierarchy
- ensure components remain compatible with the build and design systems

If a proposed component is not clearly needed, do not create it.

---

## Final Component Ethos

Summit’s component library should feel like a sharply edited wardrobe.

Enough range to dress the whole house well.  
No unnecessary costumes.  
No cheap duplicates.  
No panic purchases.