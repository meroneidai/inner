# InnerPath — Development Tasks

> Tracking file. Update status as each step finishes: `[ ]` → `[x]`

## Defaults
- Locale: Arabic (`ar`) default + English (`en`), RTL, Cairo font
- AI: OpenRouter (default ChatGPT `openai/gpt-4o`) | STT: OpenAI Whisper
- Auth: Laravel Breeze (Blade + Alpine)
- DB: MySQL in `.env` (SQLite for local fallback)

---

## Phase 0 — Project tracking
- [x] Create `task.md` and keep updated

## Phase 1 — Foundation
- [x] Configure `.env` / `.env.example` with third-party keys (OpenAI, Whisper, MySQL, mail, queue, S3)
- [x] Set Arabic as default locale + translation files
- [x] Install Laravel Breeze (Blade)
- [x] Design system (brand colors, Cairo font, RTL layout)
- [x] Public pages (Home, About, How It Works, Privacy, Terms, Contact)
- [x] Auth (register, login, verify, reset)
- [x] User profiles + preferences + onboarding
- [x] Personal dashboard

## Phase 2 — Dream Journal
- [x] Migrations/models for dreams + related entities
- [x] Text dream create / edit / delete / show
- [x] Journal list with search & filters
- [x] Policies & validation

## Phase 3 — AI Analysis
- [x] AI provider interface + OpenAI driver
- [x] Prompt manager + master analysis prompt
- [x] Response validator + structured JSON
- [x] AnalyzeDream job + daily analysis report UI
- [x] Analysis feedback

## Phase 4 — Voice
- [x] Audio upload + private storage
- [x] Transcription provider + Whisper driver
- [x] TranscribeDreamAudio job
- [x] Edit transcript → submit for analysis

## Phase 5 — Longitudinal Analysis
- [x] Symbol frequency + pattern detection
- [x] Weekly reports + GenerateWeeklyReport job
- [x] Monthly reports + GenerateMonthlyReport job
- [x] Charts (Chart.js) for emotional trends

## Phase 6 — Personal Growth
- [x] Development themes / actions / progress
- [x] Reflection exercises
- [x] AI follow-up conversations

## Phase 7 — Admin, API, Tests
- [x] Admin dashboard (stats, users, AI settings, prompts, jobs)
- [x] API v1 (dreams, analysis, reports, symbols, development)
- [x] Feature/unit tests (auth, dreams, isolation, AI validation)
- [x] Deployment documentation

## Phase 8 — Human Inner Profile (`improvment.md`)
Keep existing implementation; integrate HIP as the personal-context layer.

### 8.1 Database & foundation
- [x] Extend `user_profiles` (preferred_language, countries, profile_status, review timestamps)
- [x] Tables: sections, questions, translations, answers, consents, snapshots, observations, history, goals, review_sessions
- [x] Models, relationships, policies, consent service

### 8.2 Question bank
- [x] Seed sections A–N with full question bank
- [x] Arabic + English translations
- [x] Conditional rules + answer types
- [x] Admin question bank CRUD

### 8.3 Onboarding UI
- [x] Welcome screen (Start / Complete later / Skip)
- [x] Multi-step Stage 1 (~20–25 priority questions)
- [x] Save & continue / save & exit / skip section / resume
- [x] Stage 2 deeper optional sections

### 8.4 Profile management UI
- [x] `/profile/inner-world` overview + dimensions
- [x] Edit/delete answers, history, privacy controls
- [x] Export profile / revoke analytical consent

### 8.5 Profile AI engine
- [x] HumanInnerProfileService + snapshot/observation services
- [x] Master profile analysis prompt + JSON validation
- [x] Hypotheses stay unconfirmed until user action

### 8.6 Dream analysis integration
- [x] ProfileContextBuilder (relevant context only)
- [x] Wire into DreamAnalysisService pipeline
- [x] Update dream analysis prompt for personal associations priority

### 8.7 Reports, growth, tests
- [x] Weekly/monthly reports include profile themes
- [x] Growth themes from user-defined goals (inner-world goals + existing growth)
- [x] HIP feature/unit tests
- [x] Update DEPLOY.md notes

## Phase 9 — OpenRouter + auto-analyze + admin prompts
- [x] Default provider OpenRouter (`openai/gpt-4o`) via `.env`
- [x] Admin defaults at `/admin/ai` (DB overrides)
- [x] Auto-analyze on first text; store in DB; no manual AI CTA
- [x] Admin editable analysis prompt + variables at `/admin/prompts`
- [x] Easy registration → dashboard; soft profile-completion CTA
- [x] Pipeline tests (dispatch, OpenRouter fake, failure isolation)

## Phase 10 — Care companion + joyful UI
- [x] Prompt: personal symbol associations + care_tags / support_focus / steps
- [x] Daily care messages (love/confidence/calm) stored in DB
- [x] Weekly/monthly + conversations use OpenRouter when configured
- [x] Voice dialogue (Web Speech in/out) + deep mode with profile context
- [x] UI: Baloo/Cairo/Jakarta, mint-peach joy, emoji nav, motion
- [x] Analysis show: personal associations + growth steps + care chips

## Phase 12 — UI/UX redesign
- [x] Install UI skills into `.cursor/skills`
- [x] Design system refresh (tokens, motion, soft surfaces)
- [x] App shell: sidebar + mobile bottom nav + topbar
- [x] Dashboard / dreams / growth / reports / conversations / public / guest

## Phase 11 — Advanced notifications (in-app + email)
- [x] `notifications` table + preference channels (daily/weekly/monthly/analysis/growth)
- [x] NotificationGateway respects email + in-app toggles
- [x] Daily care, dream reminder, analysis ready, weekly/monthly report, growth reminder
- [x] Scheduler hourly/daily/weekly/monthly wiring
- [x] `/notifications` center + nav badge
- [x] Settings UI for all notification prefs + hours
- [x] Feature tests

---

## Progress log
| Date | Step | Notes |
|------|------|-------|
| 2026-11-25 | Started | Plan execution began |
| 2026-11-25 | Phase 1 | Env, Arabic locale, Breeze, design system, public + auth + dashboard |
| 2026-11-25 | Schema | Full InnerPath migrations + models |
| 2026-11-25 | Services | AI/Whisper/analysis/reports/growth + jobs |
| 2026-11-25 | Phases 2–6 | Journal, analysis UI, voice, reports, growth, chat |
| 2026-11-25 | Phase 7 | Admin, API v1, tests passing, DEPLOY.md |
| 2026-11-25 | Done Phases 0–7 | Core platform complete |
| 2026-11-25 | Phase 8 | Human Inner Profile from improvment.md — DB, Q-bank, onboarding, inner-world, AI context, tests |
| 2026-11-25 | Phase 9 | OpenRouter default, auto-analyze, admin AI/prompts, easy signup |

## Seeded accounts
- Admin: `admin@innerpath.test` / `password`
- User: `user@innerpath.test` / `password`

## Local run
```bash
php artisan serve
php artisan queue:work
# optional: npm run dev
```

Set `OPENROUTER_API_KEY` in `.env` for dream analysis (and `OPENAI_API_KEY` for Whisper).

## Key HIP routes
- `/onboarding/welcome` — multi-step profile start
- `/profile/inner-world` — Human Inner Profile dashboard
- `/admin/questions` — question bank admin
- `/admin/ai` — OpenRouter / analysis defaults
- `/admin/prompts` — editable dream-analysis prompt
