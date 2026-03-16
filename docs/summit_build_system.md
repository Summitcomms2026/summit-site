# summit_build_system.md

## Purpose of this file

This file defines how Summit Communication Group’s digital ecosystem should be built.

It governs:
- build philosophy
- development workflow
- WordPress implementation rules
- theme and plugin responsibilities
- content modelling
- field architecture
- component system
- responsive behaviour
- performance expectations
- accessibility standards
- schema implementation
- deployment workflow
- AI-assisted development workflow
- quality assurance and launch discipline

If:
- `summit_soul.md` defines what Summit is
- `summit_voice.md` defines how Summit sounds
- `summit_site_architecture.md` defines how Summit is organised
- `summit_design_system.md` defines how Summit looks and feels

then this file defines how Summit is actually built.

This document should be used by:
- developers
- technical leads
- AI coding systems
- designers translating layouts into components
- project managers coordinating build phases

The build must feel disciplined, elegant and durable.  
It must not become a WordPress improvisation exercise.

---

## Core Build Philosophy

Summit is primarily a web development project in the best sense:
- system-led
- structurally intelligent
- typographically disciplined
- interaction-aware
- content-driven
- media-enhanced rather than media-dependent

The site must still feel strong if photography and video are removed.

That means the real build priorities are:
- layout system
- component system
- typography system
- motion system
- content model
- template logic
- semantic HTML
- performance
- accessibility
- editorial publishing capability

Media should amplify the experience.  
It must not rescue weak structure.

---

## Non-Negotiables

1. Build in WordPress from the start.
2. Do not build the site as a static HTML prototype and “convert it later”.
3. Use WordPress as a structured content operating system, not a page-builder playground.
4. Build a bespoke theme or tightly controlled custom theme system.
5. Content structure must live in code, not in scattered admin improvisation.
6. Reusable templates and components are mandatory.
7. No plugin sprawl.
8. No visual-builder dependency if it can be avoided.
9. The architecture must support consultancy, editorial and podcast/media layers from the outset.
10. The system must remain elegant under growth.

---

## Recommended Technology Model

## Core Runtime Integrations

The following platforms are approved as Summit’s primary runtime integration stack where they materially support hosting, video delivery, audience capture and measurement.

### AWS Enterprise Hosting
AWS is the primary infrastructure layer for:
- hosting
- staging and production environments
- CDN / delivery support
- DNS and routing
- security and request filtering
- backups and deployment discipline

AWS should be treated as infrastructure, not as a front-end feature.

### JW Player
JW Player is the preferred native video platform for:
- Summit showreels
- case study films
- brand videos
- Tastemakers trailers
- selected editorial video embeds

Where premium native video delivery is required, JW Player should be preferred over generic third-party embed clutter.

### ActiveCampaign
ActiveCampaign is the primary platform for:
- newsletter capture
- CRM
- lead routing
- sequenced automations
- audience segmentation
- enquiry follow-up
- owned audience growth

It should be treated as Summit’s core first-party audience and lead-management engine.

### Google Analytics
Google Analytics is the primary measurement layer for:
- page engagement
- form conversions
- downloads
- newsletter sign-ups
- content performance
- selected video and podcast engagement events

Analytics should be configured around Summit’s actual strategic events, not left as a default installation with vague pageview trivia.

### Primary CMS
- WordPress

**Reason:**  
The team already knows it, and it is suitable for a structured content and publishing environment when used properly.

### Local Development
- LocalWP for local WordPress environment management

**Reason:**  
This keeps the local environment simple and close to the workflow already proven by Charlie.

### Code Editing / AI Build Environment
- Claude Code as the implementation engine
- ChatGPT as the strategic and briefing engine
- local project folder with governance files available to both

### Source Control
- Git
- GitHub or equivalent repository host

### Theme Approach
**Preferred:**
- custom block theme or restrained custom hybrid theme

**Acceptable:**
- custom classic theme with Gutenberg support if that better suits the team’s implementation confidence

**Avoid:**
- heavy off-the-shelf premium themes
- Elementor-led builds unless absolutely unavoidable
- Divi-style architecture
- anything that makes long-term control worse

### Field System
**Recommended:**
- controlled field architecture using either:
  - custom native metadata and blocks
  - or ACF Pro where it meaningfully speeds structured admin without compromising the codebase

**Rule:**  
Use fields to enforce consistency, not to create admin chaos.

### Search / SEO Plugin Layer

Google Analytics should remain the primary analytics layer, with SEO tooling kept separate from runtime architecture.

Do not let the plugin define the architecture.

### Caching / Performance
AWS infrastructure and CDN logic should be considered part of the performance strategy from the outset.
Use a clean caching and optimisation layer at the hosting level and, where necessary, a minimal WordPress caching setup.  
Performance should be solved systemically, not by piling on five optimisation plugins.

---

## AI-Assisted Workflow Philosophy

Summit’s build process should follow the logic demonstrated in Charlie’s working method.

### ChatGPT is the strategic brain
Use ChatGPT for:
- governance files
- page specifications
- component logic
- field definitions
- template logic
- SEO structure
- schema planning
- acceptance criteria
- content migration rules
- critique and review

### Claude Code is the coding engine
Use Claude Code for:
- creating and editing theme files
- creating and editing plugin files
- building templates
- implementing components
- wiring WordPress logic
- implementing schema output
- writing front-end styles and interactions
- refactoring and debugging

### WordPress is the operating environment
Use WordPress for:
- managing structured content
- publishing pages and articles
- managing case studies
- managing podcast seasons and episodes
- future editorial workflow

### Principle
Do not ask Claude Code to decide the brand or information architecture.  
Do not ask ChatGPT to pretend it has compiled and tested the theme.  
Each system should do the job it is strongest at.

---

## Governance Files Required in Project Root

The following files should exist in the project root and remain readable by Claude Code:

- `summit_soul.md`
- `summit_voice.md`
- `summit_site_architecture.md`
- `summit_build_system.md`
- `summit_design_system.md`

**Optional but strongly recommended next:**
- `summit_component_library.md`
- `summit_content_model.md`
- `summit_migration_rules.md`

These files form Claude’s governing context.  
They are not decorative.  
They are the project’s operating constitution.

---

## Suggested Project Folder Structure

This is a logical structure, not a strict absolute.

```text
summit-site/
├── governance/
│   ├── summit_soul.md
│   ├── summit_voice.md
│   ├── summit_site_architecture.md
│   ├── summit_build_system.md
│   ├── summit_design_system.md
│   ├── summit_component_library.md
│   └── summit_content_model.md
├── theme/
│   └── summit-theme/
├── plugin/
│   └── summit-core/
├── content/
│   ├── migration/
│   ├── article-drafts/
│   ├── case-studies/
│   └── podcast/
├── assets/
│   ├── logos/
│   ├── placeholders/
│   ├── showreel/
│   └── imagery/
├── docs/
│   ├── page-specs/
│   ├── qa/
│   └── launch/
└── README.md

**Rule:**  
Keep content logic, build logic and governance logic separated but connected.

---

## WordPress Build Architecture

The build should be split into two main coded layers.

### 1. Theme Layer

**Responsible for:**
- presentation
- templates
- components
- layout
- typography
- interactions
- front-end rendering
- template parts
- global styles
- responsive behaviour

### 2. Core Plugin Layer

**Responsible for:**
- custom post types
- taxonomies
- meta fields
- admin enhancements
- schema logic where appropriate
- content rules
- reusable back-end logic
- future API or integration logic

**Rule:**  
Do not bury important content structures inside the theme if they should survive a theme change.

Content model belongs in the core plugin layer wherever practical.

---

## Navigation Implementation Rule

The navigation is a prescriptive part of the build.

**Required sticky top navigation normal state order:**
- Menu
- Brand
- Design Tomorrow

The expanded mega menu should be implemented to match the structural behaviour and designed feel of Matter Of Form’s mega menu.

This requirement is prescriptive, not optional.

**Implementation expectations:**
- the sticky top navigation remains visible and stable
- the expanded mega menu opens as a designed environment, not a dropdown
- primary navigation is clearly separated from utility navigation
- editorial feed content can live within the expanded state
- the bottom utility layer remains integrated into the menu experience
- motion must feel calm, precise and architectural

Developers should not reorder or reinterpret this navigation without explicit approval.

---

## Recommended WordPress Content Types

These should be implemented in code, not manually improvised.

### Standard Pages

Use for:
- Home
- Who We Are
- What We Do
- Get In Touch / Design Tomorrow
- Careers
- Client Lounge
- Tastemakers landing page
- static utility pages

### Custom Post Types

#### 1. Case Studies
**Purpose:**  
Selected Work child pages

#### 2. Articles
**Purpose:**  
The Future of Luxury article pages

#### 3. Podcast Seasons
**Purpose:**  
Tastemakers season pages

#### 4. Podcast Episodes
**Purpose:**  
Tastemakers episode pages

#### 5. Downloads / Documents (optional)
**Purpose:**  
Decks, downloadable essays, sponsor packs, gated assets

**Future only if needed:**
- Guests
- Events
- Authors
- Community posts

Do not create speculative post types without a clear use case.

---

## Required Field Architecture

Every repeatable content object must have structured fields.

### Case Study Fields
- title
- slug
- excerpt
- featured image
- project category
- service type
- sector
- client or brand name
- challenge
- strategic idea
- what was built
- outcome
- gallery
- video / showreel link
- related case studies
- CTA override if needed
- SEO title
- meta description
- Open Graph image

### Article Fields
- title
- slug
- standfirst / deck
- featured image
- article category
- tags
- author
- published date
- updated date
- article body
- read time
- related articles
- related podcast episode
- canonical URL
- SEO title
- meta description
- Open Graph image

### Podcast Season Fields
- title
- slug
- subtitle
- season number
- season theme
- season description
- hero artwork
- trailer
- episode list
- guest map / season summary assets
- SEO title
- meta description
- Open Graph image

### Podcast Episode Fields
- title
- slug
- season relation
- episode number
- guest name
- guest title
- guest company
- deck
- featured artwork
- duration
- publish date
- episode summary
- audio embed / source
- video embed if relevant
- transcript
- show notes
- key themes
- related article
- related episodes
- sponsor note if needed
- SEO title
- meta description
- Open Graph image

### Contact / Connect Form Fields

Form logic should be implemented cleanly and mapped intentionally.  
Do not let the form plugin become the architecture.

If using a form system, it should support:
- routing by enquiry type
- structured admin notifications
- graceful confirmation state
- spam protection
- optional CRM integration later

---

## Taxonomy Rules

Taxonomies must remain controlled.

### Articles
Use:
- categories
- light tags

### Case Studies
Use:
- service type
- sector
- format

### Episodes
Use:
- season
- episode theme
- guest category

**Rule:**  
Do not create a jungle of taxonomies for the pleasure of taxonomy.

---

## Template Map

The site should be built from reusable templates.

### 1. Homepage Template
Unique arrangement of reusable sections.

### 2. Standard Page Template
For most strategic pages.

### 3. Service Child Page Template
For:
- Brand Strategy
- Experience Design
- Digital Transformation

### 4. Work Archive Template
For Work Showcase parent page.

### 5. Case Study Template
For all work child pages.

### 6. Editorial Archive Template
For The Future of Luxury landing / archive.

### 7. Article Template
For article pages.

### 8. Podcast Landing Template
For Tastemakers main page.

### 9. Season Template
For podcast season pages.

### 10. Episode Template
For podcast episode pages.

### 11. Utility Template
For gated, access-controlled or admin-adjacent front-end pages.

No major page type should require hand-built one-off code unless there is a compelling strategic reason.

---

## Component System Requirements

Video-led components such as showreels, trailers and selected film panels should be built to support JW Player embeds cleanly, without breaking layout rhythm or performance standards.

### Global Components
- sticky top navigation
- expanded mega menu overlay
- sticky bottom bar
- footer
- CTA footer block
- subscribe module

### Core Page Components
- hero
- split image-text block
- value proposition block
- capabilities grid
- sectors grid
- quote block
- stats / proof block
- showreel block
- download block
- featured content grid
- testimonial block
- tabbed interface
- related content block

### Editorial Components
- article hero
- article metadata bar
- rich text body
- pull quote
- inline media module
- author block
- share block
- subscribe block
- related reading block

### Podcast Components
- episode hero
- player block
- season overview grid
- episode grid
- guest module
- transcript block
- show notes block
- related content module

Components should be:
- reusable
- responsive
- cleanly styled
- documented
- not over-parameterised

Avoid building components with so many options they become miniature bureaucracies.

---

## Design System Expectations

The front-end must be governed by a design system even if the visual design evolves.

At minimum, define:
- breakpoints
- spacing scale
- container widths
- type scale
- heading hierarchy
- button styles
- link styles
- card styles
- form styles
- colour tokens
- section rhythm
- animation durations
- hover states
- focus states

**Rule:**  
The design system should make the site feel composed, not templated.

---

## Motion and Interaction Rules

Motion is part of the architecture.

Define and standardise:
- menu open / close transitions
- hero transitions
- card hover behaviour
- reveal motion
- tab switching behaviour
- video handling
- loading states
- form confirmation states

Motion should feel:
- controlled
- deliberate
- elegant
- calm

It should not feel:
- flashy
- startup-ish
- excessive
- like a design student discovered easing curves last Thursday

**Rule:**  
If the motion draws attention to itself more than the content, it is too loud.

---

## Responsive Behaviour Rules

The site must be designed and built responsively from the start.

### Mobile Rules
- navigation must remain elegant and clear
- content hierarchy must survive
- cards must stack cleanly
- no hidden desktop dependency
- tap targets must be appropriate
- long-form reading must remain comfortable
- media embeds must remain stable

### Tablet Rules
- preserve hierarchy
- preserve spacing discipline
- avoid awkward intermediate states

### Desktop Rules
- use space generously
- allow structure to breathe
- prioritise compositional clarity over empty grandeur

**Rule:**  
Do not treat mobile as the sad leftovers of desktop.  
Design and test it intentionally.

---

## Performance Standards

Summit should feel premium and fast.

### Performance Targets
- strong Core Web Vitals
- efficient Time to First Byte
- controlled script weight
- compressed images
- lazy loading where appropriate
- responsive image sizes
- lightweight embeds
- no unnecessary plugin or JS bloat

### Performance Philosophy

Solve performance by:
- good architecture
- clean code
- good hosting
- intelligent asset handling

Not by:
- bolting on seven optimisation plugins after making a mess

**Rule:**  
Luxury does not excuse slowness.

---

## Accessibility Standards

Accessibility is part of quality.

Minimum requirements:
- semantic HTML
- heading hierarchy
- keyboard navigation
- focus states
- alt text support
- sufficient contrast
- readable text sizing
- accessible forms
- labelled inputs
- transcript availability for podcast content where possible
- captions or supporting text for video where relevant

**Rule:**  
Do not build a beautiful site that quietly excludes people.

---

## Schema and Structured Data Rules

Schema should be implemented cleanly and only where it corresponds to real content objects.

### Core
- Organization
- ProfessionalService
- WebPage
- ContactPage
- ContactPoint
- BreadcrumbList

### Work
- CreativeWork
- CaseStudy where appropriate

### Editorial
- Blog
- BlogPosting
- Article
- Person

### Podcast
- PodcastSeries
- PodcastEpisode
- AudioObject
- VideoObject where relevant

Schema should be output from structured content, not hard-coded vanity fragments.

---

## SEO Build Rules

Form submissions, newsletter sign-ups, downloads and other meaningful conversions should also be measurable through Google Analytics and, where relevant, coordinated with ActiveCampaign.

The build should support organic authority without degrading the experience.

### Technical SEO Requirements
- clean permalinks
- one H1 per page
- correct heading hierarchy
- canonical URLs
- XML sitemap
- robots logic
- index / noindex controls where necessary
- Open Graph metadata
- clean archive behaviour
- strong internal linking
- no thin pages created for the sake of it

### Content Architecture SEO Rules
- pillar pages for service areas
- article topic clustering
- meaningful archive pages only
- related content modules
- article-to-service and service-to-proof links

**Rule:**  
SEO is built into the structure.  
It is not sprinkled on afterwards like parsley.

---

## Build Workflow

### Phase 0 — Strategic Setup

Before coding:
- finalise governance files
- finalise page specifications
- confirm phase one scope
- confirm content model
- confirm template list
- confirm domain and hosting approach

### Phase 1 — Foundation

Build:
- local WordPress environment
- repository
- theme scaffold
- core plugin scaffold
- CPTs and taxonomies
- field architecture
- base styles
- global navigation
- global footer
- design tokens

### Phase 2 — Core Templates

Build:
- standard page template
- homepage template
- work archive and case study template
- article archive and article template
- podcast landing, season and episode templates
- contact page logic

### Phase 3 — Content Integration

Load:
- core pages
- first case studies
- selected Future of Luxury articles
- Tastemakers season and episode content
- downloadable deck assets

### Phase 4 — QA and Refinement

Check:
- content rendering
- responsive behaviour
- typography
- motion
- performance
- accessibility
- schema
- SEO metadata
- internal links
- forms
- browser behaviour

### Phase 5 — Launch

- staging sign-off
- production deployment
- analytics setup
- Search Console / indexing setup
- redirects if needed
- post-launch bug monitoring

### Phase 6 — Post-Launch Enhancement

- additional archive import
- richer podcast layer
- gated assets
- community features later
- audience ownership systems later

---

## Git and Branching Rules

Use Git properly.

**Recommended:**
- `main` for production-ready branch
- `develop` for active integration
- feature branches for major work:
  - `feature/homepage`
  - `feature/case-studies`
  - `feature/articles`
  - `feature/podcast`
  - `feature/forms`

**Rule:**  
Do not let Claude Code make direct reckless changes against the wrong branch without review.

Use separate sessions for distinct tasks where possible.

---

## Claude Code Working Rules

Claude Code should work inside a defined project directory with access to:
- governance files
- theme files
- plugin files
- relevant assets

Claude Code should:
- read the governance files before major tasks
- create or update files in the correct layer
- explain planned changes before broad refactors where helpful
- prefer clean, maintainable code over heroic mess
- avoid introducing unnecessary dependencies
- respect WordPress conventions where sensible

Claude Code should not:
- invent architecture contrary to governance files
- introduce bloated frameworks for the sake of it
- improvise random plugins to solve solvable problems
- create page-specific hacks that should be component logic

Use fresh Claude sessions for:
- new major feature work
- discrete refactors
- focused debugging
- template-specific implementation

Do not leave every decision trapped inside one endlessly swollen thread.

---

## ChatGPT Working Rules

ChatGPT should be used to:
- write and refine governance files
- turn page intentions into page specs
- define field requirements
- define acceptance criteria
- create review checklists
- critique Claude output
- diagnose where copy or structure has drifted
- generate AI-to-AI briefs where needed

ChatGPT should not be treated as the system that “just builds the theme”.  
Its role is architectural intelligence, not direct compile-and-test implementation.

---

## QA Requirements

Every build phase should be reviewed against:
- Summit soul
- Summit voice
- Summit site architecture
- Summit design system
- component consistency
- mobile behaviour
- performance
- accessibility
- CMS usability

QA should include:
- visual QA
- content QA
- responsive QA
- schema QA
- SEO QA
- browser QA
- admin usability QA

**Rule:**  
A visually impressive page that is awkward to edit is not finished.

---

## CMS Usability Rules

The admin experience should be deliberate.

Editors should:
- know where to put content
- see the right fields for the right content type
- not be overwhelmed by irrelevant options
- not need to rebuild layout logic manually
- not need a developer for every normal editorial action

The CMS must support:
- clarity
- repeatability
- controlled flexibility

This matters especially for:
- articles
- case studies
- podcast episodes
- season pages
- downloads

---

## Hosting and Deployment Principles

Use a hosting environment suited to:
- WordPress performance
- staging and production separation
- backups
- security
- SSL
- caching
- deployment discipline

**Minimum environment expectations:**
- staging site
- production site
- regular backups
- version-controlled deployment process where possible
- clean rollback path

Security must be treated as operational hygiene, not heroism after a hack.

---

## Future-Proofing Rules

The build should anticipate future additions without pretending to be them now.

Leave room for:
- audience identity layer
- richer editorial publishing tools
- gated content
- member logic
- comments or reactions
- event integration
- CRM integration
- community features
- expanded Tastemakers environment
- owned audience operating system concepts

But do not build speculative complexity into phase one unless it genuinely reduces future pain.

---

## Anti-Patterns

Avoid:
- building the site page-by-page without a system
- starting in HTML and hoping WordPress will sort itself out later
- over-reliance on page builders
- excessive plugin dependence
- free-form admin fields everywhere
- duplicated logic across templates
- inconsistent component behaviour
- over-designed motion
- architecture dependent on hero media
- coding without governance context
- unreviewed AI-generated sprawl
- one person manually holding the whole structure together with good intentions

If a build decision makes the system less coherent, it is probably wrong.

---

## Build Success Criteria

The build is successful when:
- the site feels elegant even in wireframe
- the structure remains coherent without media
- WordPress editing is controlled and usable
- consultancy, proof and media layers feel connected
- pages behave consistently
- the theme is maintainable
- the plugin layer is clean
- the site is fast
- the site is accessible
- the site is scalable without becoming a muddle
- new content can be added without inventing new logic every week

In short:  
The build is successful when Summit feels like a directed digital system, not a handsome accident.

---

## How AI Should Use This File

When building for Summit, AI should:
- follow the governance hierarchy
- respect WordPress as the operating environment
- build structured content models first
- think in templates and components
- prioritise elegance, maintainability and performance
- keep the build restrained and intentional
- avoid architecture that depends on decorative media
- make future growth possible without making phase one bloated

If a proposed implementation weakens clarity, durability or control, it should be revised.

---

## Final Build Ethos

Summit should be built like a serious digital product with a luxury front end.

Not a brochure site.  
Not a plugin collage.  
Not a design concept waiting for engineering to rescue it.

The system beneath the brand should be as intelligent as the brand itself.