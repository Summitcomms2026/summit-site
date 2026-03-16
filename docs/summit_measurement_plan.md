# summit_measurement_plan.md

## Purpose of this file

This file defines how Summit Communication Group should measure the performance of its website, editorial ecosystem and audience-growth systems.

It governs:
- measurement philosophy
- primary business objectives
- KPI framework
- event tracking
- conversion definitions
- attribution logic
- newsletter and CRM measurement
- video and podcast measurement
- editorial measurement
- SEO measurement
- dashboard logic
- reporting cadence
- ownership and review
- data hygiene
- privacy and implementation discipline

If:
- `summit_soul.md` defines what Summit is
- `summit_voice.md` defines how Summit sounds
- `summit_site_architecture.md` defines how Summit is organised
- `summit_build_system.md` defines how Summit is built
- `summit_content_model.md` defines the structured content objects
- `summit_editorial_system.md` defines how Summit publishes
- `summit_integration_stack.md` defines the approved platforms

then this file defines how Summit should know whether any of it is actually working.

This is the source of truth for measurement, performance interpretation and reporting discipline.

**Rule:**  
Summit should measure what matters, not merely what flickers.

---

## Measurement Philosophy

Summit is not building a high-volume commodity site.  
It is building:
- a consultancy
- an editorial property
- a media platform
- an owned audience system

That means measurement must reflect:
- quality of attention
- strength of audience relationship
- commercial intent
- authority growth
- contribution to pipeline
- long-term archive value

Summit should not optimise for vanity metrics alone.

### Bad measurement habits
- pageviews without context
- social reach mistaken for strategic progress
- shallow engagement presented as proof of relevance
- traffic spikes that do not translate into audience or opportunity
- newsletter growth without audience quality
- downloads without downstream action
- video plays without completion or intent

### Good measurement habits
- measuring qualified actions
- measuring repeat behaviour
- measuring progression through the ecosystem
- measuring content that compounds over time
- measuring which assets create known audience value
- measuring which journeys create real commercial conversations

---

## Measurement Objectives

Summit’s measurement system should answer six core questions.

### 1. Are the right people finding Summit?
This is about:
- traffic quality
- source quality
- search visibility
- audience fit
- geography
- new vs returning behaviour

### 2. Are they engaging meaningfully?
This is about:
- time and scroll behaviour
- article depth
- video engagement
- podcast engagement
- repeat session quality
- internal movement across the ecosystem

### 3. Are they becoming known audience members?
This is about:
- newsletter sign-ups
- form submissions
- gated asset registrations
- CRM captures
- segmentation quality

### 4. Are they moving toward commercial intent?
This is about:
- contact enquiries
- Design Tomorrow submissions
- service-page visits after editorial consumption
- case-study interaction
- return visits from known prospects

### 5. Is the editorial system compounding authority?
This is about:
- article cluster performance
- episode discoverability
- search growth
- subscriber growth
- archive strength
- repeat readership

### 6. Is the site itself healthy?
This is about:
- technical performance
- Core Web Vitals
- broken journeys
- form failures
- video failures
- measurement integrity

---

## Primary Measurement Platforms

### Google Analytics
Primary platform for:
- event tracking
- page engagement
- conversion tracking
- user journeys
- landing-page performance
- content interaction
- source / medium analysis

### ActiveCampaign
Primary platform for:
- newsletter growth
- list quality
- automation performance
- form-to-CRM flows
- lead progression
- segmentation and nurture outcomes

### JW Player
Primary platform for:
- video play starts
- video completion behaviour
- engagement by asset
- selected CTA and media-level analytics

### SEMrush
Primary platform for:
- search visibility
- keyword tracking
- topic opportunity
- technical SEO audits
- ranking trends

### Buffer
Secondary operational layer for:
- social distribution performance
- post-level amplification review

### Rule
Google Analytics is the behavioural spine.  
ActiveCampaign is the audience and CRM spine.  
SEMrush is the search spine.  
JW Player is the native video spine.

---

## Primary KPI Framework

Summit should review performance through four KPI layers.

## Layer 1 — Business Outcomes

### Core Business KPIs
- qualified contact enquiries
- Design Tomorrow submissions
- newsletter sign-ups
- known audience growth
- gated asset registrations
- webinar / event registrations when live
- qualified CRM records created
- content-assisted enquiries

These are the main measures of strategic success.

---

## Layer 2 — Audience and Relationship KPIs

### Core Audience KPIs
- new users from target geographies
- returning user rate
- engaged sessions
- newsletter conversion rate
- subscriber growth rate
- repeat article readership
- repeat episode listeners / page visitors
- known audience growth by source
- list quality and engagement in ActiveCampaign

These measure whether Summit is building an audience rather than borrowing one.

---

## Layer 3 — Content KPIs

### Editorial KPIs
- article views
- article engagement rate
- average engaged time on article pages
- scroll depth on long-form articles
- article-to-article click-through
- article-to-service click-through
- article-to-sign-up conversion
- article-to-enquiry assist

### Podcast KPIs
- episode page views
- audio play starts
- trailer plays
- transcript engagement
- episode completion signals where available
- episode-to-article click-through
- episode-to-sign-up conversion
- season page performance

### Proof / Case Study KPIs
- case-study page views
- case-study click-through from service pages
- case-study assisted conversions
- showreel engagement
- video completion on work pages

---

## Layer 4 — Technical and Search KPIs

### Technical KPIs
- Core Web Vitals
- page load performance
- broken form rate
- video load success
- 404 errors
- internal search issues if later introduced
- tracking integrity

### Search KPIs
- non-branded organic traffic
- branded organic traffic
- rankings for priority service terms
- rankings for editorial cluster terms
- growth in indexed pages
- topic cluster visibility
- click-through rate from search
- organic entrances to articles and service pages

---

## Conversion Framework

Summit should define conversions at three levels.

## Level 1 — Primary Conversions
These are commercially meaningful.

- Design Tomorrow form submission
- contact form submission
- high-intent enquiry form submission
- webinar registration when applicable
- strategic consultation booking if implemented later

These should be configured as primary conversions in Google Analytics and reflected in ActiveCampaign.

---

## Level 2 — Secondary Conversions
These indicate audience capture and ecosystem entry.

- newsletter sign-up
- gated document download registration
- podcast subscriber click-out where trackable
- season launch sign-up
- event interest registration
- guest / sponsor expression of interest where applicable

These matter because Summit’s long-term media and audience model depends on them.

---

## Level 3 — Micro-Conversions
These show progression and intent.

- article completes high scroll threshold
- related article click
- service page click from editorial
- case-study click from service page
- showreel play
- video completion threshold
- episode play start
- transcript expand
- CTA button click
- outbound platform click where strategically useful

These should not be treated as end goals, but they are useful signals of movement.

---

## Core Event Model

All events should follow a clean, consistent naming convention.

### Naming Principle
Use lower-case snake_case or lower-case event labels consistently.

Recommended style:
- `newsletter_signup`
- `design_tomorrow_submit`
- `case_study_view`
- `showreel_play`
- `episode_play_start`

Do not mix:
- camelCase
- random spaces
- vendor defaults
- emotionally improvised event naming from late-night enthusiasm

---

## Core GA4 Event Groups

## 1. Page and Session Events

### Required
- page_view
- session_start
- first_visit
- user_engagement

### Recommended Custom Page Context Parameters
- content_type
- page_template
- article_category
- service_type
- sector
- podcast_series
- podcast_season
- podcast_episode
- featured_status

These parameters make reporting much more useful later.

---

## 2. CTA and Navigation Events

### Required Custom Events
- primary_cta_click
- secondary_cta_click
- footer_cta_click
- sticky_nav_click
- mega_menu_open
- mega_menu_link_click
- bottom_bar_click

### Recommended Parameters
- cta_label
- cta_location
- destination_url
- page_type
- content_type

---

## 3. Form and Audience Capture Events

### Required
- form_start
- form_submit
- newsletter_signup
- gated_download_submit
- webinar_registration_submit
- contact_submit
- design_tomorrow_submit

### Recommended Parameters
- form_name
- enquiry_type
- page_location
- content_context
- lead_magnet_name
- campaign_name if relevant

### Rule
Only successful submissions should count as conversions.
Not button mashing and hope.

---

## 4. Editorial Events

### Required
- article_view
- article_scroll_50
- article_scroll_75
- article_scroll_90
- article_related_click
- article_service_click
- article_case_study_click
- article_signup_click

### Recommended Parameters
- article_title
- article_category
- author_name
- cornerstone_status
- content_cluster
- read_time_band

### Notes
Use a pragmatic scroll-depth model.
Do not create a cathedral of events no one will ever review.

---

## 5. Case Study and Work Events

### Required
- case_study_view
- case_study_related_click
- case_study_video_play
- case_study_cta_click
- work_archive_filter_click if filters are later introduced

### Recommended Parameters
- project_name
- sector
- service_type
- format
- featured_status

---

## 6. Video Events

### Required
- video_play_start
- video_progress_25
- video_progress_50
- video_progress_75
- video_complete

### Recommended Scope
Use for:
- homepage showreel
- case-study films
- brand films
- episode trailers
- selected editorial videos

### Recommended Parameters
- video_title
- video_type
- page_location
- content_context
- player_platform

### Notes
Where possible, align this with JW Player analytics rather than creating two worlds that nod politely at each other and never speak.

---

## 7. Podcast and Media Events

### Required
- episode_view
- episode_play_start
- episode_progress_25
- episode_progress_50
- episode_progress_75
- episode_complete where technically feasible
- transcript_expand
- related_episode_click
- season_page_view
- trailer_play

### Recommended Parameters
- series_name
- season_name
- episode_title
- guest_name
- theme
- page_type

### Notes
Not all audio completion data may be equally easy to capture depending on implementation.
Use a sensible model rather than promising clairvoyance.

---

## 8. Download Events

### Required
- download_view
- download_click
- gated_download_submit
- download_complete if technically feasible

### Recommended Parameters
- document_name
- document_type
- gated_status
- page_context
- related_content_type

---

## ActiveCampaign Measurement Model

ActiveCampaign should not operate as a parallel universe.

It should be used to measure:
- new subscriber acquisition
- source-level subscriber growth
- form source
- lead magnet source
- automation entry
- automation completion
- engagement by segment
- enquiry progression where relevant

### Required ActiveCampaign Data Points
- list joins
- tag assignment
- source attribution
- form source
- campaign source
- automation starts
- automation completions
- email open rate
- click-through rate
- unsubscribes
- qualified lead status where used

### Recommended Summit Segments
- The Future of Luxury subscribers
- Tastemakers subscribers
- consultancy enquiries
- download registrants
- hospitality interest
- fashion interest
- technology interest
- investor / finance interest
- healthcare interest
- media / speaking interest

### Rule
Segmentation should be useful, not elaborate.
A taxonomy with no behavioural logic is merely admin cosplay.

---

## Attribution Model

Summit should use a practical attribution approach rather than an ideological one.

### Recommended Approach
Use a blended view including:
- first-touch source
- session source
- conversion-assisting content
- last meaningful interaction

### Practical Questions to Answer
- Which channel introduced the user?
- Which content built trust?
- Which page captured the lead?
- Which content assisted the eventual enquiry?

### Summit-Specific Attribution Priorities
Measure:
- article-assisted enquiry
- case-study-assisted enquiry
- episode-assisted sign-up
- showreel-assisted contact
- download-assisted contact
- newsletter-assisted return visit

### Rule
The person who closed the deal is not always the first page the user saw.
Summit’s measurement plan must respect the long game.

---

## UTM and Campaign Rules

All externally distributed content should use a disciplined UTM model.

### Required UTM Fields
- source
- medium
- campaign

### Optional UTM Fields
- content
- term

### Recommended Examples
- `utm_source=linkedin`
- `utm_medium=social`
- `utm_campaign=future_of_luxury_article_launch`

- `utm_source=activecampaign`
- `utm_medium=email`
- `utm_campaign=tastemakers_season_one_launch`

### Rule
Do not let UTMs become an improv club.

Keep naming:
- lower-case
- stable
- readable
- documented

---

## Search Measurement Plan

SEMrush and Google Analytics should work together.

### Core Search Questions
- Which priority service terms are improving?
- Which editorial themes are gaining authority?
- Which articles bring in qualified organic traffic?
- Which search entrances convert into sign-ups or enquiries?
- Which content clusters deserve further expansion?

### Priority Search Reporting Areas
- luxury brand strategy terms
- luxury experience design terms
- luxury digital transformation terms
- future of luxury editorial terms
- category-specific thought leadership terms
- podcast and guest discoverability terms where relevant

### Search KPI Review
Monthly at minimum:
- keyword movement
- organic traffic movement
- landing-page movement
- click-through rate trends
- top-performing content clusters
- declining but important pages

---

## Dashboard Model

Summit should use a small number of useful dashboards.

## Dashboard 1 — Executive Summary
Audience:
- Gregory
- senior leadership

Should show:
- total qualified enquiries
- newsletter growth
- top-performing articles
- top-performing case studies
- top-performing episodes
- primary conversion trends
- organic growth trend
- return visitor trend

Purpose:
A clear strategic view, not a technologist’s aquarium.

---

## Dashboard 2 — Editorial Dashboard
Audience:
- editorial lead
- producer
- strategist

Should show:
- article performance
- episode performance
- topic cluster performance
- article-to-sign-up conversion
- episode-to-sign-up conversion
- related content click-through
- archive winners and laggards

Purpose:
Improve publishing judgement and topic selection.

---

## Dashboard 3 — Conversion and CRM Dashboard
Audience:
- Gregory
- operations
- CRM owner

Should show:
- form submissions by type
- Design Tomorrow submissions
- source-to-enquiry mapping
- lead magnet performance
- ActiveCampaign list growth
- automation performance
- subscriber quality by source

Purpose:
Understand what is building pipeline and known audience.

---

## Dashboard 4 — Technical and Search Dashboard
Audience:
- technical lead
- SEO lead

Should show:
- Core Web Vitals
- 404s / broken journeys
- indexation issues
- priority rankings
- traffic by landing page
- search cluster performance
- top organic entrances
- underperforming important pages

Purpose:
Keep the machine healthy.

---

## Reporting Cadence

### Weekly
Review:
- top content
- sign-ups
- enquiries
- active campaigns
- obvious anomalies
- major distribution outcomes

### Monthly
Review:
- KPI dashboard
- editorial performance
- search growth
- conversion trends
- audience growth
- CRM progression
- page and content assists

### Quarterly
Review:
- topic clusters
- archive strength
- content formats
- audience quality
- season performance
- channel mix
- whether the measurement model itself needs adjustment

### Rule
Measure frequently enough to learn.  
Not so frequently that everyone starts hallucinating significance in Tuesday.

---

## Ownership and Accountability

Each measurement area should have an owner.

### Required Owners
- analytics owner
- CRM / ActiveCampaign owner
- SEO owner
- editorial owner
- technical owner
- executive reviewer

### Ownership Logic
- Google Analytics: analytics / technical lead
- ActiveCampaign: CRM / marketing owner
- SEMrush: SEO owner
- JW Player analytics: technical / media owner
- editorial performance review: editorial lead
- commercial interpretation: Gregory / leadership

### Rule
Metrics without ownership are just decorative numbers.

---

## Privacy and Consent Notes

Summit should implement measurement in a way that is:
- legally appropriate
- transparent
- proportionate
- respectful of user trust

### Requirements
- consent logic where applicable
- privacy policy clarity
- form clarity around data capture
- no unnecessary tracking for the sake of it
- no hidden behavioural overreach

### Rule
Premium audiences do not enjoy being treated like inventory.

---

## Data Hygiene Rules

### Required Discipline
- consistent event naming
- stable UTM naming
- documented conversion definitions
- duplicate event prevention
- test before launch
- test after launch
- annotate major campaign or site changes
- avoid tracking things nobody intends to review

### Rule
Dirty data is often worse than no data, because it encourages false confidence.

---

## Measurement Anti-Patterns

Avoid:
- measuring everything
- reporting pageviews without quality signals
- calling every click engagement
- treating newsletter growth as success if list quality collapses
- tracking beautiful events no one uses
- setting conversions before defining what they mean
- changing event names midstream without documentation
- building dashboards nobody reads
- confusing motion in the chart with progress in the business

If the measurement system becomes more complicated than the decisions it supports, it has gone too far.

---

## Success Criteria

The measurement plan is successful when:
- Summit knows which channels bring the right audience
- Summit knows which content builds authority
- Summit knows which assets create known audience value
- Summit knows what drives enquiries
- Summit knows what is technically broken
- Summit can separate vanity from signal
- reporting helps decisions rather than merely filling meetings
- the archive can be judged by contribution, not sentiment

In short:  
The measurement system is successful when it gives Summit clear judgement, not just more numbers.

---

## How AI Should Use This File

When recommending analytics, tracking, dashboards or reporting logic for Summit, AI should:
- prioritise strategic signal over metric abundance
- preserve clean naming conventions
- support GA4 as the behavioural core
- support ActiveCampaign as the audience and CRM core
- support SEMrush as the search intelligence layer
- support JW Player where video analytics matter
- recommend events and conversions only where they support actual decisions

If a tracking proposal creates noise faster than insight, simplify it.

---

## Final Measurement Ethos

Summit should measure the way a serious operator reads a room.

Not everything loud is important.  
Not everything quiet is insignificant.  
The point is to know where the real signal lives.