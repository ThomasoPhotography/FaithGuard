# FaithGuard — Website Version Plans & Architectural Roadmap

## 1. Roadmap Context & Overview
FaithGuard is a specialized web platform designed to assist individuals in overcoming addiction through faith-based guidance, structured pastoral stages, accountability logging, and community encouragement. 

This document serves as the master versioning plan and technical roadmap. It synthesizes the development evolution from early static prototypes

(`v0.0.0`–`v0.0.5`)

to dynamic script-backed templates

(`v0.1.0`–`v0.1.3`)

through the feature-dense Alpha release

(`v0.1.4-alpha`)

and charts a structured course toward production readiness.

---

## 2. Current Baseline Version
- **Current Stable/Active Branch:** `v0.1.4-alpha`
- **Status:** Active Feature Integration & Hardening Phase
- **Database Schema:** `faithguard_db.sql` (V2 Schema with CSRF, scripture cache, and pastoral journey relational structures)

---

## 3. Primary Tech Stack Code & Environment

| Layer | Technologies & Libraries | Key Components / Architectural Conventions |
| :--- | :--- | :--- |
| **Backend / Server** | PHP 8.x (Native procedural + Service OOP) | `FaithGuardRepository.php`, `BibleService.php`, `CsrfTokenGenerator.php`, `quizComparisonService.php` |
| **Database** | MySQL (Relational) | `faithguard_db.sql` V2; foreign keys, UTC timestamp standard, table caching (`scripture_cache`, `csrf_tokens`) |
| **Frontend UI/UX** | HTML5, SCSS/CSS3, Modular JavaScript (ES6+ IIFE) | Bootstrap Responsive Grid (`col-md-*`), Warm Ember design palette, componentized scripts (`auth.js`, `quiz.js`, `scripture-modal.js`) |
| **Security Layer** | Custom DB-backed CSRF Engine, Argon2 Password Hashing, Session Guards | Embedded Biblical Character CSRF tokens (`{ts}_{dbId}_{bibleHex}_{rand}`), HTTPS server hardening via `.htaccess` |
| **External APIs** | RESTful Scripture API | Central Scripture Language Resolver (NRSVUE, NBV21, NLD1939), server-side API key isolation in `Database` config |

---

## 4. Product Vision & Value Proposition
> *"Overcoming addiction through Christ & Protecting your digital faith with hope and redemption."*

FaithGuard provides a spiritually grounded, data-informed sanctuary. Rather than treating recovery through sterile checklists or detached statistics, FaithGuard links psychological habit disruption with pastoral care (Hope, Discipline, Repentance, Grace). Every assessment, check-in, and scripture lookup reinforces continuous spiritual accompaniment, secure accountability, and community interaction without compromising user privacy. The community resources are also released by the overseer of FaithGuard, namely WWTW.

---

## 5. Version Release History & Evolution

```
[Phase 0: Static Scaffolding] -> [Phase 1: Architecture Separation] -> [Phase 2: Alpha Modernization]
```
```
(v0.0.0 - v0.0.5) -> (v0.1.0 - v0.1.3) -> (v0.1.4-alpha - Current)
```

### Phase 0: Static Prototyping & Layout Genesis (`v0.0.0` – `v0.0.5`)
- **Focus:** Establishing visual brand identity, basic DOM interactions, and initial HTML mockups.
- **Milestones:**
  - `v0.0.0` – `v0.0.2`: Initial scaffolding, basic JavaScript event bindings, CTA, and testimonial sections.
  - `v0.0.3` – `v0.0.4`: Conversion of static scripts to structured data-regions, container responsiveness.
  - `v0.0.5` – `v0.0.5-Alpha`: Introduction of "Warm Ember" color palette (`#E9B48A`, `#956959`, `#5C352C`, `#2A1717`, `#3C3C34`), core brand assets, and quiz-to-resource redirection.

### Phase 1: Dynamic Architecture & Endpoint Separation (`v0.0.6-alpha` – `v0.1.3-beta`)
- **Focus:** Backend database integration, repository pattern introduction, and modular authentication.
- **Milestones:**
  - `v0.0.6-alpha`: First database integration (`faithguard_db.sql`), initial `repository.php` and auth helpers.
  - `v0.1.0`: File restructuring: `src/` to `api/`, `img/` to `uploads/`. Dedicated CRUD endpoints for messages, posts, progress check-ins, and resources.
  - `v0.1.1` – `v0.1.2`: Path standardizations (`DOCUMENT_ROOT`), index migration from HTML to PHP, initial unit queries added to `FaithGuardRepository`.
  - `v0.1.3-alpha` – `v0.1.3-beta`: Authentication flow fixes, login session visibility in dynamic navbars, layout stabilization across mobile and desktop.

### Phase 2: Feature-Dense Hardening & Pastoral Engine (`v0.1.4-alpha` – Current)
- **Focus:** Advanced pastoral journeys, secure CSRF management, scripture caching, and complete user/admin dashboards.
- **Milestones:**
  - **Biblical CSRF System:** Database-backed token validation with encoded biblical references across 56 books.
  - **Pastoral Journey Integration:** Multi-stage recovery trajectories (Hope, Discipline, Repentance, Grace) dynamically mapped to quiz assessment severity.
  - **Scripture Resolver & Modal:** Central language resolver mapping EN/NL locales to accurate Bible translations with server-side caching.
  - **Comparative Analytics:** Integration of `quizComparisonService.php`, visual attempt timelines, and Last vs. Previous attempt comparison cards.

---

## 6. Detailed Version Matrix & Roadmaps

| Version | Status | Milestone Focus | Deliverables & Scope |
| :--- | :--- | :--- | :--- |
| **v0.1.4-alpha** | **Current** | Stabilization & Baseline Feature Completion | - Full CRUD repository<br>- Biblical CSRF tokens<br>- Scripture caching engine<br>- User & Admin dynamic dashboard grids |
| **v0.1.4-beta** | Planned | Connectivity Loading & Baseline Connection Feature | - Skeleton screens & progressive visual loading states<br>- Pre-connect / DNS-prefetch for external resources & APIs<br>- Asset minification, bundling & deferred script loading (`defer`/`async`)<br>- Centralized connection resilience & latency fallbacks |
| **v0.1.4-charlie** | Planned | Security Audit & Modular Decoupling | - Strict input sanitization & parameterized queries verification<br>- Separation of API business logic from direct repository access<br>- HTTP security headers (CSP, HSTS, X-Frame-Options)<br>- Session lifecycle hardening & rate limiting enforcement |
| **v0.1.5-alpha** | Planned | Community & Pastoral Mentorship Beta | - Structured pastoral guidance pathways & milestone tracking<br>- Moderated community discussion threads & prayer boards<br>- Role-based mentor/mentee access controls<br>- Direct messaging and report triage workflows |
| **v0.1.5-beta** | Planned | Offline Resilience & PWA Capabilities | - Service Worker caching for core app shell & static assets<br>- LocalStorage/IndexedDB sync queue for offline journaling & quiz entries<br>- Web App Manifest configuration & installable PWA prompts<br>- Background sync when connectivity is restored |
| **v0.1.6** | Future Target | Production Launch | - Comprehensive end-to-end load & stress testing<br>- Production error tracking, telemetry & logging pipeline<br>- Final SEO, accessibility (WCAG AA), and cross-browser audit<br>- Automated deployment pipeline & backup automation |

---

## 7. Current Baseline Review (`v0.1.4-alpha`)
`v0.1.4-alpha` represents a significant functional milestone. The shift from static template files to dynamic, repository-driven dashboard workflows provides a cohesive foundation:
- **Strengths:** 
  - Centralized persistence logic within `FaithGuardRepository.php`.
  - Cryptographically resilient CSRF token generator combining timestamps, unique DB IDs, and biblical character markers.
  - Rich user telemetry: quiz history timelines, contextual scripture retrieval, and milestone tracking.
- **Structural Vulnerabilities:**
  - Rapid iterative expansion resulted in duplicated logic across API scripts and inline dashboard queries.
  - Heavy dependence on `!important` declarations in CSS/SCSS to resolve styling specificity issues.
  - High volume of scattered logging calls and quick fixes accumulated during active alpha testing.

---

## 8. Current Baseline Problems

1. **CSS Specificity Wars & Style Leaks:**
   - Multiple patches relied on `!important` overrides for dropdowns, buttons, and profile grids (`main.scss`).
2. **Scattered Path Resolution:**
   - Inconsistencies between `__DIR__`, `DOCUMENT_ROOT`, and relative file inclusions in certain secondary endpoints.
3. **Frontend Script Fragmentation:**
   - Over ten discrete JavaScript files loaded globally without an asset bundler, increasing HTTP overhead.
4. **Error Handling & Verbose Debug Remnants:**
   - Numerous temporary debug statements and unstandardized API response structures across registration and login endpoints.

---

## 9. Current Priority Problems & Targeted Fixes

```
[Problem: SCSS !important bloat]    --> [Action: Restructure BEM modular component partials]
[Problem: Script loading overhead]   --> [Action: Implement core bundle & lazy script loading]
[Problem: Inconsistent API returns] --> [Action: Enforce standardized jsonResponse helper]
```

### Action 1: SCSS Normalization
- **Issue:** Dropdown buttons, modals, and navigation elements utilize `!important` to force styling.
- **Fix:** Refactor `main.scss` into modular partials (`_nav.scss`, `_modal.scss`, `_grid.scss`) following strict BEM naming conventions, removing all override flags.

### Action 2: Standardized API Middleware
- **Issue:** Several endpoints manually handle error JSON responses and headers instead of using `api/base.php`.
- **Fix:** Route all API responses through `jsonResponse($data, $statusCode)` with mandatory structured payloads:
  ```json
  { "success": true, "data": {}, "error": null, "timestamp": 1720864000 }
  ```

### Action 3: Database & Auth Cleanup
- **Issue:** Leftover dummy testing entities (e.g., test user ID 9999) and unconsumed CSRF tokens in development databases.
- **Fix:** Build an automated maintenance worker in `FaithGuardRepository::cleanupExpiredCsrfTokens()` and schedule daily cron execution.

---

## 10. Future Priority Problems & Mitigations

### 1. Scaling Scripture API Rate Limits
- **Risk:** High concurrent user activity triggering third-party Bible API rate thresholds.
- **Mitigation:** Optimize `scripture_cache` database queries with cache warmup routines for high-frequency verses and common pastoral tags.

### 2. Moderation Workflow Bottlenecks
- **Risk:** Increased community prayer and message volume overwhelming admin review capacities.
- **Mitigation:** Implement proactive content filtering for flagged keywords, automated risk scoring, and tiered moderator permissions.

### 3. Internationalization (i18n) Synchronization
- **Risk:** Multi-language expansion causing UI breaks due to variable text length.
- **Mitigation:** Create dedicated dictionary mapping files (`locales/en.json`, `locales/nl.json`) integrated with the Scripture resolver.

---

## 11. Expected Version Goals: Next Minor Release (`v0.1.5-alpha`)
- [ ] Complete decoupling of inline SQL calls in UI templates; migrate 100% of data reads to `FaithGuardRepository`.
- [ ] Implement a unified frontend modal manager to replace isolated scripts (`scripture-modal.js`, `profile-timeline-modal.js`, `profile-pastoral-modal.js`).
- [ ] Standardize CSRF token lifecycle across all AJAX requests via `X-CSRF-Token` headers.
- [ ] Remove all legacy debug hooks and redundant testing routes before entering beta.

---

## 12. Future Version Vision: Production Release (`v1.0.0`)
FaithGuard `v1.0.0` will deliver a hardened, cloud-ready pastoral platform featuring:
- **End-to-End Encrypted Journaling:** Zero-knowledge encryption for private recovery reflections.
- **Adaptive Recovery Engine:** Machine-assisted pastoral resource pathways matching individual recovery velocity and struggle severity.
- **Offline Progressive Web App (PWA):** Immediate daily scripture meditation, emergency prayer access, and offline check-in journaling without active internet connectivity.
- **Audited Compliance:** Full GDPR and CCPA compliance with granular cookie preferences, automated data export, and self-service account purges.