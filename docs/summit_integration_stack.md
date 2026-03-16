# summit_integration_stack.md

## Purpose of this file

This file defines Summit Communication Group’s operational integration stack.

It governs:
- infrastructure platforms
- runtime integrations
- editorial and production toolchain
- measurement and analytics stack
- CRM and automation stack
- media delivery stack
- search and reporting stack
- creative workflow tools
- phase priority
- ownership expectations
- implementation notes
- dependencies and risks

If:
- `summit_soul.md` defines what Summit is
- `summit_voice.md` defines how Summit sounds
- `summit_site_architecture.md` defines how Summit is organised
- `summit_build_system.md` defines how Summit is built
- `summit_design_system.md` defines how Summit looks and feels
- `summit_component_library.md` defines the reusable interface parts
- `summit_content_model.md` defines the structured content objects
- `summit_migration_rules.md` defines how content enters the system

then this file defines the external platforms and software that support the ecosystem in practice.

This is the operational source of truth for third-party systems.

Rule:
The integration stack should support the system, not quietly become the system.


## Integration Philosophy

Summit should treat external platforms as belonging to one of four categories:

1. **Core runtime infrastructure**
   Systems that materially affect hosting, delivery, security, forms, video or measurement on the live site.

2. **Production and source systems**
   Systems that generate or prepare content which is later published on the site.

3. **Operational intelligence and distribution**
   Systems that support SEO, reporting, scheduling, analysis and distribution beyond the site.

4. **Creative workflow**
   Systems that support the production and management of visual, motion and branded assets.

Rule:
Not every tool belongs in the front-end build.
Some tools are integrations.
Some are source systems.
Some are simply part of the operating environment around the site.


## Integration Categories

### Category A — Core Runtime Infrastructure
- AWS Enterprise Hosting
- JW Player
- ActiveCampaign
- Google Analytics

### Category B — Production and Source Systems
- Riverside.fm
- Zoom

### Category C — Search, Reporting and Distribution
- SEMrush
- Buffer

### Category D — Creative Workflow and Asset Systems
- Shutterstock
- Adobe Creative Cloud


## Integration Decision Rules

Before integrating any external platform into the live site, ask:

1. Is this runtime-critical or merely operational?
2. Does this improve performance, publishing, conversion or measurement?
3. Does this create a dependency the site can survive if removed?
4. Does this belong in phase one or later?
5. Does this affect content modelling, component design or migration rules?
6. Does this introduce brand or UX compromise?
7. Who owns this platform operationally?

If these questions are not answered, the integration is not ready.


## Platform Register

## 1. AWS Enterprise Hosting

### Category
Core runtime infrastructure

### Primary Purpose
Hosting, delivery, DNS, edge performance and security.

### Likely Summit Uses
- production hosting environment
- staging environment
- CDN delivery
- DNS and routing
- security and request filtering
- backup and deployment infrastructure

### Website Dependency Level
Critical

### Phase
Phase one

### Runtime or Workflow
Runtime

### Expected Touchpoints
- hosting
- deployment
- CDN
- DNS
- security
- caching strategy

### Related Governance Docs
- `summit_build_system.md`
- `summit_site_architecture.md`

### Ownership
Technical lead / infrastructure owner

### Notes
AWS should be treated as infrastructure, not as a brand decision.
The front end should never reveal the complexity of the hosting stack unless something has gone very badly indeed.


## 2. JW Player

### Category
Core runtime infrastructure

### Primary Purpose
Native video hosting, delivery, playback and analytics.

### Likely Summit Uses
- homepage showreel
- case study film embeds
- Summit brand films
- Tastemakers trailers
- media delivery on editorial pages
- future video performance tracking

### Website Dependency Level
High for premium video experiences

### Phase
Phase one for showreel / selected video
Expanded use in phase two

### Runtime or Workflow
Runtime

### Expected Touchpoints
- `showreel_block`
- `project_gallery_block`
- `episode_player_block`
- hero media panels
- video analytics layer
- possible CTA overlays around video assets

### Related Governance Docs
- `summit_build_system.md`
- `summit_component_library.md`
- `summit_content_model.md`

### Ownership
Technical lead + media owner

### Notes
JW Player should be preferred over generic third-party clutter where Summit wants a more controlled native video experience.


## 3. ActiveCampaign

### Category
Core runtime infrastructure

### Primary Purpose
CRM, email capture, automations, lead management and newsletter workflows.

### Likely Summit Uses
- newsletter sign-up
- enquiry routing and follow-up
- lead nurture sequences
- Tastemakers subscriber journeys
- The Future of Luxury audience capture
- CRM visibility for inbound opportunities
- behavioural automations where strategically useful

### Website Dependency Level
High for lead capture and owned audience growth

### Phase
Phase one

### Runtime or Workflow
Runtime + operational

### Expected Touchpoints
- `subscribe_module`
- `connect_contact_tabs`
- gated download forms
- enquiry forms
- newsletter confirmations
- automated follow-up sequences
- contact tagging and segmentation

### Related Governance Docs
- `summit_build_system.md`
- `summit_content_model.md`
- `summit_migration_rules.md`

### Ownership
Marketing / CRM owner

### Notes
ActiveCampaign should be the core first-party audience and lead engine.
Do not treat it as “just email”.
Its use should remain structured and intentional.


## 4. Google Analytics

### Category
Core runtime infrastructure

### Primary Purpose
Measurement, events, conversions and behavioural analysis.

### Likely Summit Uses
- page-level engagement measurement
- scroll and interaction measurement
- enquiry conversion tracking
- download conversion tracking
- newsletter sign-up tracking
- video engagement event tracking
- podcast page engagement measurement
- future funnel and attribution analysis

### Website Dependency Level
High

### Phase
Phase one

### Runtime or Workflow
Runtime

### Expected Touchpoints
- all public pages
- forms
- downloads
- article engagement
- case study engagement
- podcast engagement
- video events

### Related Governance Docs
- `summit_build_system.md`
- future `summit_measurement_plan.md`

### Ownership
Analytics owner / technical marketing lead

### Notes
GA4 should be configured deliberately around Summit’s actual strategic events.
Do not leave it as a default installation that knows slightly too much about pageviews and nothing useful about intent.


## 5. Riverside.fm

### Category
Production and source systems

### Primary Purpose
Podcast recording, transcript generation, clips, show-note source material and social derivatives.

### Likely Summit Uses
- Tastemakers recording
- transcript source
- social clip generation
- show-note drafting
- promotional cutdowns
- host and guest content preparation

### Website Dependency Level
Medium

### Phase
Phase one for podcast production workflow

### Runtime or Workflow
Workflow / source system

### Expected Touchpoints
- `podcast_episode`
- `transcript_block`
- `show_notes_block`
- social media cutdown workflows
- migration of transcripts and summaries into CMS

### Related Governance Docs
- `summit_content_model.md`
- `summit_migration_rules.md`
- future `summit_editorial_system.md`

### Ownership
Podcast producer / media owner

### Notes
Riverside should feed the site.
It should not become the final destination for information the site itself ought to own.


## 6. SEMrush

### Category
Search, reporting and distribution

### Primary Purpose
SEO research, technical audits, rank tracking and reporting.

### Likely Summit Uses
- technical SEO auditing
- keyword clustering
- article opportunity mapping
- service-page optimisation research
- position tracking
- reporting against visibility goals
- Search Console and GA4 blended analysis

### Website Dependency Level
Medium

### Phase
Phase one for planning and audit
Ongoing post-launch

### Runtime or Workflow
Operational

### Expected Touchpoints
- service-page optimisation
- editorial topic development
- site audits
- visibility reporting
- post-launch issue tracking

### Related Governance Docs
- `summit_build_system.md`
- `summit_migration_rules.md`
- future `summit_measurement_plan.md`
- future `summit_editorial_system.md`

### Ownership
SEO owner / strategist

### Notes
SEMrush should shape editorial and technical priorities, not tempt the team into publishing low-grade SEO filler under the banner of strategy.


## 7. Buffer

### Category
Search, reporting and distribution

### Primary Purpose
Social scheduling, publishing support and channel analytics.

### Likely Summit Uses
- Future of Luxury article distribution
- Tastemakers clip distribution
- LinkedIn scheduling
- multi-platform social cadence
- post-level analytics
- simplified team scheduling workflow

### Website Dependency Level
Low to medium

### Phase
Phase one for distribution support

### Runtime or Workflow
Operational

### Expected Touchpoints
- article publication workflow
- podcast clip workflow
- newsletter amplification
- social repurposing

### Related Governance Docs
- future `summit_editorial_system.md`
- future `summit_measurement_plan.md`

### Ownership
Editorial / social owner

### Notes
Buffer is not part of the website architecture itself.
It is part of how Summit gets the most out of what the site publishes.


## 8. Zoom

### Category
Production and source systems

### Primary Purpose
Video calls, webinars, meeting summaries, event capture and future lead-generation events.

### Likely Summit Uses
- client calls
- webinar events
- workshop sessions
- founder interviews
- transcript / summary source material
- future registration-led event pages
- webinar follow-up flows

### Website Dependency Level
Medium

### Phase
Phase one for source workflow
Phase two for webinar/event integrations

### Runtime or Workflow
Workflow first, later partial runtime/event integration

### Expected Touchpoints
- future event landing pages
- registration forms
- webinar replay pages
- transcript and summary migration
- CRM follow-up flows through ActiveCampaign

### Related Governance Docs
- `summit_content_model.md`
- `summit_migration_rules.md`
- future `summit_editorial_system.md`

### Ownership
Operations / editorial / events owner

### Notes
Zoom is primarily a source and event system.
It should be integrated where useful, but the Summit site should remain the polished front of house.


## 9. Shutterstock

### Category
Creative workflow and asset systems

### Primary Purpose
Licensed stock imagery, footage and workflow support inside creative tools.

### Likely Summit Uses
- concept development
- placeholder or editorial imagery
- moodboards
- motion support assets
- web imagery support where original assets are unavailable
- Adobe workflow integration

### Website Dependency Level
Low

### Phase
Operational from phase one

### Runtime or Workflow
Workflow

### Expected Touchpoints
- design production
- editorial support imagery
- deck creation
- motion and visual testing

### Related Governance Docs
- `summit_design_system.md` only indirectly
- future creative workflow documentation if needed

### Ownership
Creative lead / design team

### Notes
Shutterstock supports output quality, but should never become a crutch for generic visual language.
Licensed is not the same thing as distinctive.


## 10. Adobe Creative Cloud

### Category
Creative workflow and asset systems

### Primary Purpose
Design, video, motion, shared libraries, fonts and brand asset management.

### Likely Summit Uses
- web design
- showreel production
- motion design
- deck design
- image preparation
- typography management
- shared brand libraries
- collaboration across the team
- maintaining asset consistency

### Website Dependency Level
Medium as a workflow system, low as a runtime dependency

### Phase
Operational from phase one

### Runtime or Workflow
Workflow

### Expected Touchpoints
- design system implementation
- asset production
- creative libraries
- font decisions
- visual QA
- export and handoff preparation

### Related Governance Docs
- `summit_design_system.md`
- future creative workflow documentation if needed

### Ownership
Creative lead / design team

### Notes
Adobe is part of the creative operating environment, not the website runtime.
Its greatest value for Summit is consistency, asset control and speed of production.


## Integration Priority Matrix

### Phase One — Essential Runtime / Launch Critical
- AWS Enterprise Hosting
- JW Player
- ActiveCampaign
- Google Analytics

### Phase One — Essential Workflow / Production
- Riverside.fm
- Zoom
- Adobe Creative Cloud
- Shutterstock

### Phase One — Strategic Operations
- SEMrush
- Buffer

### Phase Two — Expanded Integrations
- deeper JW Player analytics usage
- more advanced ActiveCampaign automations
- Zoom webinar registration loops
- richer GA4 event models
- broader SEMrush reporting structures

### Phase Three — Advanced Ecosystem
- richer audience identity logic
- more advanced CRM orchestration
- premium webinar/member workflows
- deeper media attribution systems


## Integration Ownership Model

Each platform should have a named internal owner.

### Required Ownership Fields
- platform owner
- backup owner
- technical owner
- business purpose
- renewal owner
- implementation status
- phase
- dependencies
- related docs

Rule:
Unowned platforms become expensive folklore.


## Runtime vs Workflow Distinction

### Runtime-Critical Platforms
These affect the live user experience directly:
- AWS
- JW Player
- ActiveCampaign
- Google Analytics

### Workflow-Critical Platforms
These affect production, publishing or distribution:
- Riverside.fm
- Zoom
- SEMrush
- Buffer
- Shutterstock
- Adobe Creative Cloud

Rule:
Keep runtime dependencies tight.
Workflow tools can be broader, provided they do not quietly colonise the front end.


## Data and Dependency Notes

### Data Capture Systems
- ActiveCampaign
- Google Analytics
- Zoom, where registration is used
- JW Player analytics, where enabled

### Source Asset Systems
- Riverside.fm
- Zoom
- Adobe Creative Cloud
- Shutterstock

### Risk Principle
If a system captures audience, lead or behavioural data, its role should be documented more carefully than a tool that merely helps produce images.

Audience systems deserve governance.
Moodboards, while lovely, deserve rather less.


## Integration Rules for Existing Governance Docs

### `summit_build_system.md`
Should lightly reference:
- AWS
- JW Player
- ActiveCampaign
- Google Analytics

### `summit_content_model.md`
Should lightly reference:
- ActiveCampaign
- JW Player
- Riverside.fm
- Zoom

### `summit_migration_rules.md`
Should lightly reference:
- Riverside.fm
- Zoom

### Future `summit_editorial_system.md`
Should reference:
- Riverside.fm
- Buffer
- ActiveCampaign
- Zoom
- SEMrush

### Future `summit_measurement_plan.md`
Should reference:
- Google Analytics
- ActiveCampaign
- SEMrush
- JW Player where relevant


## Integration Anti-Patterns

Avoid:
- integrating a platform because it sounds enterprise-y
- allowing vendor capabilities to dictate Summit’s architecture
- burying critical logic inside third-party dashboards no one checks
- treating workflow tools as if they are runtime requirements
- allowing too many tools to overlap meaninglessly
- adding martech before defining the actual measurement plan
- letting forms, video or CRM behaviour fragment across multiple platforms without reason

If the stack becomes harder to explain than the website itself, it has become too clever by half.


## Success Criteria

The integration stack is successful when:
- each platform has a clear job
- runtime dependencies are controlled
- editorial and production systems feed the site cleanly
- measurement is structured
- audience capture is intentional
- the stack supports owned media value rather than platform dependence
- the team knows which tool to use for which job
- removal of one non-critical tool does not produce organisational collapse

In short:
The stack should make Summit more sovereign, not more entangled.


## How AI Should Use This File

When recommending integrations, workflows or implementation logic for Summit, AI should:
- treat this file as the source of truth for approved platforms
- distinguish between runtime and workflow systems
- avoid inventing unnecessary new tools
- recommend integrations only where they improve clarity, performance, publishing, conversion or measurement
- preserve Summit’s control over its own audience and content

If a suggested tool duplicates an existing capability without strategic gain, it should be challenged.


## Final Integration Ethos

Summit should use external platforms the way a well-run house uses staff and suppliers.

Everyone should have a role.  
Nobody should run the place by accident.