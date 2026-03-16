# summit_prompting_rules.md

## Purpose of this file

This file defines how AI systems should be briefed, governed and used across the Summit Communication Group project.

It governs:
- how to prompt ChatGPT
- how to prompt Claude Code
- which governance files must be read first
- how to request pages, templates and components
- how to request revisions
- how to avoid architectural drift
- how to separate strategy from implementation
- how to preserve Summit voice and design discipline
- how to work efficiently across long projects
- how to decide when AI output is ready for review

If:
- `summit_soul.md` defines what Summit is
- `summit_voice.md` defines how Summit sounds
- `summit_site_architecture.md` defines how Summit is organised
- `summit_build_system.md` defines how Summit is built
- `summit_design_system.md` defines how Summit looks and feels
- `summit_component_library.md` defines the reusable interface parts
- `summit_content_model.md` defines the structured content objects
- `summit_migration_rules.md` defines how content enters the system
- `summit_editorial_system.md` defines how Summit publishes
- `summit_integration_stack.md` defines the approved platform stack
- `summit_measurement_plan.md` defines how success is measured
- `summit_qa_checklist.md` defines quality control standards

then this file defines how AI should be instructed to work within that system.

This is the operating manual for AI collaboration on Summit.

**Rule:**  
AI should extend the system, not improvise past it.

---

## Prompting Philosophy

Summit should use AI in a disciplined split-role model.

### ChatGPT
Use for:
- strategy
- governance documents
- page specifications
- information architecture
- content structure
- UX logic
- editorial planning
- critique
- revision logic
- AI-to-AI briefing

### Claude Code
Use for:
- implementation
- theme and plugin development
- front-end code
- WordPress logic
- templates
- components
- refactors
- debugging
- local build execution

### Principle
ChatGPT should think.  
Claude Code should build.

Do not ask Claude Code to invent Summit’s strategic architecture from scratch.  
Do not ask ChatGPT to pretend it has compiled and tested the code.

Each system has a lane. Keep it in it.

---

## Core Prompting Rules

1. Always ground AI in the relevant governance files first.
2. Always define the task clearly.
3. Always specify the output format you want.
4. Always tell the AI what not to change when stability matters.
5. Always distinguish between:
   - strategy
   - specification
   - implementation
   - revision
   - QA
6. Never assume AI remembers the entire project perfectly unless the relevant files are in context.
7. Never ask for “something cool” when what you really need is a system decision.
8. When in doubt, reduce ambiguity rather than increase flourish.

---

## Required Context Before Major Tasks

Before asking AI to perform any substantial Summit task, ensure it has access to the relevant governance context.

### Minimum Core Context
For most tasks, AI should read:
- `summit_soul.md`
- `summit_voice.md`
- `summit_site_architecture.md`

### Build and Design Tasks
Also read:
- `summit_build_system.md`
- `summit_design_system.md`
- `summit_component_library.md`

### Content and CMS Tasks
Also read:
- `summit_content_model.md`
- `summit_migration_rules.md`

### Editorial and Media Tasks
Also read:
- `summit_editorial_system.md`
- `summit_integration_stack.md`

### Analytics and QA Tasks
Also read:
- `summit_measurement_plan.md`
- `summit_qa_checklist.md`

### Rule
If the AI has not read the right documents, the task is under-briefed.

---

## Prompt Structure Template

A strong Summit prompt should usually contain five parts.

### 1. Task Type
State what kind of task this is:
- strategy
- page spec
- component spec
- content model
- implementation
- revision
- QA
- migration
- editorial planning

### 2. Relevant Context
State which documents govern the task.

Example:
- Read `summit_soul.md`, `summit_voice.md`, `summit_design_system.md` and `summit_component_library.md` first.

### 3. Objective
State what success looks like.

Example:
- Create a case study page template that feels premium, restrained and structurally aligned with the Summit system.

### 4. Constraints
State what must not drift.

Example:
- Do not invent new components unless absolutely necessary.
- Keep the top navigation order as Menu / Brand / Design Tomorrow.
- Preserve B2B-first positioning.

### 5. Output Format
Specify exactly what you want back.

Example:
- Return this as a clean Markdown block.
- Return this as a WordPress template implementation plan.
- Return a diff-ready component specification.
- Return a numbered QA list.

### Rule
The clearer the frame, the better the result.

---

## When to Use ChatGPT

Use ChatGPT when the task requires:
- judgement
- interpretation
- structure
- repositioning
- editorial shaping
- argument
- design logic
- synthesis
- critique
- strategic rewriting

### Examples
- Write a new governance document
- Refine a page specification
- Rationalise a service architecture
- Create a content taxonomy
- Turn rough thinking into a Claude brief
- Diagnose why a page concept feels off
- Propose a cleaner component system
- Rewrite imported content into Summit voice
- Review whether a new page belongs in the architecture

### Rule
If the task requires taste, framing or synthesis, start with ChatGPT.

---

## When to Use Claude Code

Use Claude Code when the task requires:
- WordPress implementation
- theme creation or updates
- plugin creation or updates
- template coding
- component coding
- CSS / JS implementation
- rendering logic
- debugging
- refactors
- file creation inside the project

### Examples
- Build the mega menu
- Create the article template
- Implement the case study content type
- Build the episode player block
- Refactor component spacing
- Fix schema output
- Wire ActiveCampaign form submission
- Implement JW Player in showreel blocks

### Rule
If the task needs files changed, built or tested, use Claude Code.

---

## How to Brief ChatGPT

### Good ChatGPT Prompt Pattern
- identify the task
- identify the relevant Summit docs
- provide source material if relevant
- define the audience
- define the output structure
- define what should be preserved
- ask for one clear deliverable

### Example
“Read `summit_soul.md`, `summit_voice.md`, `summit_design_system.md` and `summit_component_library.md`. I need a specification for the Work Showcase parent page. It should feel selective, premium and structurally aligned with Summit. Do not invent unnecessary components. Return a clean Markdown page spec with headings, panel logic, content requirements and CTA rules.”

### Bad ChatGPT Prompt Pattern
- vague request
- no context
- no defined output
- no constraints
- asks for strategy and implementation in one breath
- uses words like “cool”, “premium”, “fancy” without defining structural needs

### Rule
If ChatGPT gives a mushy answer, the prompt was probably mushy first.

---

## How to Brief Claude Code

### Good Claude Code Prompt Pattern
- state that this is an implementation task
- tell it which governance files to read
- define which files or folders it should work in
- define the exact thing to build or change
- define what must remain unchanged
- define the expected completion state

### Example
“Read `summit_build_system.md`, `summit_design_system.md`, `summit_component_library.md` and `summit_content_model.md`. In the WordPress theme, implement the `article_card` and `featured_article_block` components for the Future of Luxury archive. Do not alter navigation or global tokens. Reuse existing spacing and card patterns where possible. Return a summary of files changed and any follow-up issues.”

### Bad Claude Code Prompt Pattern
- “Make the blog better”
- “Redesign this whole thing”
- “Just improve it”
- “Make it look premium”
- no references to governance files
- no protection against drift

### Rule
Claude Code is strongest when it has a bounded brief, not a poetic challenge.

---

## The AI-to-AI Workflow

Summit should use a deliberate AI-to-AI relay.

### Step 1 — Think in ChatGPT
Use ChatGPT to:
- clarify the task
- structure the problem
- define the output
- create the implementation brief

### Step 2 — Build in Claude Code
Use Claude Code to:
- implement the work
- edit the files
- test or inspect the result
- summarise changes

### Step 3 — Review in ChatGPT
Return to ChatGPT to:
- critique the result
- diagnose drift
- refine next-step instructions
- write the next implementation brief

### Rule
Do not make one system pretend to be both the board and the bricklayer.

---

## Task Types and Recommended Prompting Patterns

## 1. Governance Document Task
Use ChatGPT.

### Prompt should include:
- document name
- purpose of document
- which existing docs it should align with
- tone requirements
- output format: one clean Markdown block

---

## 2. Page Specification Task
Use ChatGPT first.

### Prompt should include:
- page name
- page role in the site
- relevant design and architecture docs
- target audience
- required panels or component logic
- SEO / schema notes if relevant
- output format

---

## 3. Component Definition Task
Use ChatGPT first, then Claude Code.

### Prompt should include:
- component name
- purpose
- where it appears
- content structure
- expected variants
- responsive requirements
- interaction notes
- relationship to design system

---

## 4. WordPress Build Task
Use Claude Code.

### Prompt should include:
- relevant docs
- target file or folder
- content type or template being implemented
- what must not be touched
- whether the task is new build, refactor or fix
- expected summary of changes

---

## 5. Content Migration Task
Use ChatGPT first for selection / rewrite logic, then Claude Code or manual import workflow.

### Prompt should include:
- source content
- destination content type
- taxonomy mapping
- rewrite level
- canonical considerations
- internal linking requirements
- output format

---

## 6. Editorial Planning Task
Use ChatGPT.

### Prompt should include:
- editorial property
- target audience
- publishing objective
- relevant editorial categories
- content formats
- cadence or campaign context
- output structure

---

## 7. QA / Review Task
Use ChatGPT for critique, Claude Code for fixes.

### Prompt should include:
- relevant QA categories
- page or component under review
- screenshots or source where relevant
- severity model if desired
- whether you want diagnosis only or diagnosis plus fix brief

---

## Revision Rules

When revising existing work, always tell the AI what kind of revision this is.

### Types of Revision
- structural revision
- tonal revision
- simplification
- tightening
- expansion
- implementation correction
- QA fix
- integration update

### Good Revision Prompt
- identify the existing object
- identify what is staying
- identify what is changing
- identify what success looks like

### Example
“Keep the overall structure of `summit_build_system.md`, but tighten the navigation implementation section so it becomes more prescriptive and less conversational.”

### Rule
Do not say “improve this” unless you enjoy unlicensed experimentation.

---

## Prompting Constraints That Matter For Summit

These should be repeated whenever relevant.

### Strategic Constraints
- Summit is a design consultancy for luxury brands
- B2B first, future B2C later
- British in tone and manner
- international audience
- restrained, premium, intelligent
- not sprawling
- every section should carry weight

### Design Constraints
- negative space matters
- typography carries emotional authority
- dark/light rhythm matters
- motion must be calm and precise
- mega menu logic is prescriptive
- top nav order must remain:
  - Menu
  - Brand
  - Design Tomorrow

### Content Constraints
- structured content over freestyle chaos
- editorial quality over volume
- selected migration, not bulk dumping
- podcast pages are editorial objects, not mere embeds
- case studies must feel selective

### Build Constraints
- WordPress from the start
- no page-builder sprawl
- no plugin collage
- system first
- performance matters
- accessibility matters
- runtime integrations should remain controlled

### Rule
Remind AI of the constraints most likely to drift in the task at hand.
Do not paste the whole constitution every time unless necessary.

---

## Prompting for File Edits

When asking AI to edit a governance file, use this structure:

### Recommended Pattern
- file name
- whether you want:
  - amendment
  - insertion
  - rewrite
  - final clean block
- what should stay
- what should change
- output format

### Example
“Amend `summit_content_model.md` lightly. Keep the structure. Add references to ActiveCampaign, JW Player, Riverside and Zoom only where they materially affect content structure. Return paste-ready Markdown insertions.”

### Rule
Say whether you want:
- a patch
- a replacement section
- a full rewritten file

Otherwise AI may choose the most dramatic option for sport.

---

## Prompting for Code Changes

When asking Claude Code to change build files, always include:

- relevant governance docs to read
- files or folders to touch
- files or systems not to touch
- whether change is additive or refactor
- whether backward compatibility matters
- how to report results

### Example
“Read `summit_build_system.md`, `summit_design_system.md` and `summit_component_library.md`. In the theme only, implement the expanded mega menu overlay. Do not change the sticky top navigation order. Do not change footer logic. Summarise files changed and flag unresolved issues.”

---

## Prompting for Critique

When asking ChatGPT to review existing work, always define the lens.

### Useful Critique Lenses
- strategic clarity
- design consistency
- editorial quality
- CMS usability
- conversion logic
- SEO structure
- migration readiness
- QA readiness

### Example
“Critique this homepage panel structure through the lens of Summit’s small-but-weighty positioning, negative-space discipline and B2B luxury audience.”

### Rule
Without a lens, critique becomes general opinion dressed as insight.

---

## Prompting for Output Format

Always specify the output format you want.

### Common Summit Output Formats
- one clean Markdown block
- paste-ready insertion
- full replacement section
- numbered implementation plan
- page specification
- component specification
- QA checklist
- table of fields
- taxonomy recommendation
- AI-to-AI brief for Claude Code

### Rule
Half of AI frustration is output mismatch, not thinking failure.

---

## Working Across Long Threads

Long AI threads degrade.

### Good Practice
- start fresh threads for major new tasks
- restate the relevant docs
- use short summaries from previous work
- ask for final clean blocks when a document is stabilised
- do not rely on AI to remember every subtle prior nuance across endless turns

### Good ChatGPT Practice
When a strategy thread gets too long:
- ask for a clean summary
- ask for the current best version
- carry that forward into a fresh thread

### Good Claude Code Practice
When an implementation thread gets too long:
- start a fresh session
- reattach the relevant docs
- reissue a bounded implementation brief

### Rule
Fresh context beats swollen memory.

---

## When AI Output Is Ready

AI output is ready for review when:
- it follows the relevant governance files
- it is structurally coherent
- it is specific
- it preserves constraints
- it does not drift into generic language
- it is in the correct format
- it is actually usable by the next step in the workflow

### It is not ready when:
- it sounds clever but vague
- it changes unrelated parts of the system
- it introduces new ideas without reason
- it ignores Summit’s tone or architecture
- it requires another prompt just to decode what it meant

---

## Red Flags in AI Output

Watch for:
- generic “luxury brand” fluff
- startup-style overclaiming
- empty adjectives
- unexplained architecture changes
- invented content types
- invented components
- overcomplicated martech suggestions
- wrong navigation order
- page-builder logic creeping in
- too much sameness across page suggestions
- confidence without structure

If you see two or more of these, the output needs revision.

---

## Anti-Patterns

Avoid:
- prompting without the right docs
- asking ChatGPT to code blind
- asking Claude Code to strategy-solve without architecture
- saying “make it better” without criteria
- allowing AI to alter core governance casually
- treating first drafts as final
- using one endlessly long thread for everything
- asking for “premium” without specifying what premium means in Summit terms

If AI starts sounding like a generic agency site generator, the prompt has failed the project.

---

## Success Criteria

Prompting is successful when:
- the right AI does the right job
- governance is preserved
- output is immediately usable
- revisions are efficient
- drift is minimised
- the team can repeat good results
- Summit grows more coherent as more AI work is done, not less

In short:  
Good prompting should make AI feel like a disciplined collaborator, not a clever intern left alone with the keys.

---

## Final Prompting Ethos

Summit should brief AI the way a good creative director briefs a serious team.

Clearly.  
Specifically.  
With taste.  
With boundaries.  
And without the faint hope that vagueness will somehow produce precision.