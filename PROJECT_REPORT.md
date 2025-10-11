# IntelliTask — Project Report

Date: 2025-10-11

## 1. Introduction

IntelliTask is an AI-assisted task management application built with Laravel 10 and Livewire. It helps users create, organize, and complete tasks efficiently, and leverages Google Gemini to generate actionable sub-task suggestions from a high-level description. The project uses Vite + Tailwind CSS for a modern, responsive UI and supports SQLite for quick local setup.

## 2. Objectives

- Provide a simple, fast UI for creating and managing personal tasks.
- Use AI to break down complex tasks into smaller, actionable items.
- Keep the stack lightweight and developer-friendly (Laravel 10, Livewire, Tailwind, Vite).
- Ensure data correctness (auth scoping, policies) and clean ordering via model observers.
- Allow future extension (queuing, rate limiting, auditing) via config flags.

Key success metrics (suggested):

- Time-to-first-task creation under 30 seconds for new users.
- >80% successful AI suggestion generations without manual edits (in mock or real mode).
- 0 auth leaks across users in policy checks (verified via tests).
- p95 Livewire action latency under 200ms for CRUD on local/dev setups.

## 3. Problem Definition

Users often struggle to translate complex goals into concrete next steps. Existing task tools can be manual or generic. IntelliTask targets:

- Rapid task capture and completion tracking.
- Automated sub-task suggestion (AI) to reduce planning overhead.
- Clean, minimal workflow focused on personal productivity.

## 4. Existing System vs Proposed System

- Existing/manual approach:
  - Users write to-do lists without structure and must plan sub-steps themselves.
  - No automated guidance; prone to procrastination and unclear next actions.

- Proposed (IntelliTask):
  - AI converts a single description into curated, selectable sub-tasks.
  - Livewire UI for fast, in-place create/edit/toggle/delete flows.
  - Enforced ownership, safe defaults, and automatic priority ordering.

## 5. System Overview

Core components:

- Livewire components: `TaskManager` (CRUD, filters, pagination), `AISuggestion` (AI generation + selection).
- Models: `Task`, `User` with relationships and helpers.
- Policies: `TaskPolicy` ensures users only access their own tasks.
- Observers/Model hooks: `TaskObserver` and `Task::boot()` normalize status and maintain `priority_order`.
- Services: `GeminiService` talks to Google Gemini for task breakdown.
- Job: `ProcessAITaskSuggestion` (optional queue-based generation).
- Config: `config/ai.php`, `config/services.php`, `config/tasks.php` control feature flags, limits, and API settings.

## 6. System Architecture

- Presentation: Blade + Livewire + Tailwind + Alpine.js (for micro-interactions in the AI view).
- Application: Livewire components handle validation, actions, and render data-bound views.
- Domain/Data: Eloquent models and relationships; observers maintain ordering and invariants.
- Integration: `GeminiService` formats prompt -> calls Google Generative Language API -> parses JSON -> returns strings.
- Optional async: `ProcessAITaskSuggestion` job can cache results for polling if queues are enabled.

Request/Action flow (typical):

1. Authenticated user interacts with Livewire component.
2. Component validates input, invokes Eloquent actions and/or `GeminiService`.
3. Models/observers normalize state and enforce ordering.
4. Component re-renders with updated state (tasks list, counts, pagination).

AI suggestion sequence (synchronous):

1. User enters a high-level description in `AISuggestion`.
2. Component checks config flags (enabled/mock/cache TTL/max suggestions).
3. If mock: generate keyword-based suggestions locally.
4. Else: compute cache key by `md5(description)` and attempt `Cache::remember`.
5. On cache miss: call `GeminiService::generateTaskBreakdown()`; store results.
6. Normalize suggestions to array of strings; display and allow selection.
7. On confirmation: create tasks for selected suggestions for the current user.

Caching behavior:

- Cache key: `ai_suggestions:{md5(description)}`.
- TTL: from `config('ai.cache_ttl')`, default 3600 seconds.
- Invalidation: implicit via TTL; regenerating with same description returns cached list until expiry.
- Rate limit: suggested via `config('ai.rate_limit')` (policy left to future enhancement).

## 7. Modules Description

- Authentication & Authorization
  - Laravel Breeze-based auth scaffolding (dev dependency) and `routes/auth.php`.
  - `TaskPolicy` restricts task access by `user_id`.

- Task Management
  - Create, edit, delete, toggle status (pending/completed).
  - Counts and filters for All/Pending/Completed with pagination.

- AI Suggestions
  - `AISuggestion` Livewire component collects description and generates sub-tasks using `GeminiService` or mock mode.
  - Caching layer to avoid duplicate requests; configurable size and TTL.

- Ordering & Priority
  - `priority_order` for pending tasks; auto-assigned and re-sequenced on updates/deletions.

- Observability & Controls
  - Config flags in `config/ai.php`: enable/disable AI, mock mode, caching, rate limiting hints, logging.

- Frontend Layer
  - Tailwind utilities, glassmorphism cards, gradient accents, responsive layout.
  - Alpine.js for light inline interactivity in the AI suggestions panel.

## 8. Backend Design (Laravel MVC)

- Models
  - `App\Models\Task`: fillable fields, scopes (`pending`, `completed`, `forUser`), helpers (`isPending`, `isCompleted`), validation rules, priority management.
  - `App\Models\User`: relationship `tasks()`, convenience accessors and scopes.

- Livewire Components (Controller-like)
  - `App\Livewire\TaskManager`: CRUD, filters, counts, pagination, auth scoping.
  - `App\Livewire\AISuggestion`: AI call, caching, selection, and bulk task creation.

- Policies/Observers
  - `App\Policies\TaskPolicy`: per-user access.
  - `App\Observers\TaskObserver` and model hooks: default status, priority maintenance.

- Services/Jobs
  - `App\Services\GeminiService`: prompt formatting, API call, response parsing.
  - `App\Jobs\ProcessAITaskSuggestion`: queue-ready pipeline with cache handoff.

## 9. AI Integration & Workflow

- Configuration: `config/services.php` -> `services.gemini` (`GEMINI_API_KEY`, `GEMINI_MODEL`, `GEMINI_API_URL`, `GEMINI_TIMEOUT`). Feature flags in `config/ai.php` (mock, cache, limits, logging).
- Prompting: `GeminiService::formatTaskPrompt()` asks for a JSON array of strings representing sub-tasks.
- Transport: Laravel HTTP client posts to Google Generative Language API (v1beta) for `generateContent`.
- Parsing: Extract text from `candidates[0].content.parts[0].text`, strip code fences, decode JSON, normalize to strings.
- Caching: `AISuggestion` uses `Cache::remember` keyed by description hash with configurable TTL.
- Error handling: logs on failures, returns friendly message in UI. Job pathway marks cache as failed for polling UIs.
- Security: API key from `.env`; avoid logging prompts/responses in production unless explicitly enabled.

Error cases and fallbacks:

- Missing API key: `GeminiService` constructor throws; UI surfaces a friendly error.
- Timeout or network error: HTTP client fails; component shows retry option and keeps UI responsive.
- Non-JSON response or fenced code: parser trims and attempts `json_decode`; falls back to empty list.
- 429 (rate limit) from provider: treat as transient; suggest retry/backoff to the user.
- 5xx from provider: log and present error alert; mock mode can be used for demos.

Security & privacy:

- API keys are stored in environment variables (never committed to VCS).
- Optional logging flags (`ai.log_requests`, `ai.audit_enabled`) should remain off in production by default.
- Content filtering can be toggled via `ai.content_filter` to reduce unsafe prompts.

## 10. Frontend Design (Vite + Tailwind)

- Build tooling: Vite for dev server and production bundling; Tailwind (with `@tailwindcss/forms`), PostCSS, Autoprefixer.
- UI patterns: glass cards, gradients, pulse glows, badges, icons; dark-on-image background (`public/Images/background.jpg`).
- Interactivity: Livewire bindings for instant updates; Alpine.js for edit/copy toggles in the AI panel.
- Components: custom classes like `btn`, `btn-gradient`, `card-glass`, `badge`, etc., defined in project CSS.

Accessibility and UX details:

- Inputs have explicit labels and clear focus states to aid keyboard navigation.
- Color contrast targets legibility over the background image; placeholders use muted tones.
- Motion (glow/pulse) is subtle and non-blocking; avoid excessive animation for accessibility.
- Pagination uses standard Livewire links with accessible navigation semantics.

## 11. Database Design

- Entities
  - `users`: id, name, email (unique), email_verified_at, password, remember_token, timestamps.
  - `tasks`: id, user_id (FK users.id cascade delete), title, description (nullable), status enum ['pending','completed'] with default 'pending', `priority_order` int default 0, timestamps.

- Relationships
  - User 1—* Task via `tasks.user_id`.

- Behavior
  - When tasks move to completed, pending tasks are re-indexed; when reverting to pending, next priority is assigned.

Indexes & constraints:

- Foreign key: `tasks.user_id` references `users.id` with `onDelete('cascade')` for cleanup.
- Suggested composite index for queries: (`user_id`, `status`, `priority_order`) to speed filters and ordering.
- `email` on `users` is unique to prevent duplicates.

## 12. User Interface & UX Design

- Dashboard shows counts (All/Pending/Completed) and filterable list with pagination.
- Quick toggles for AI Suggestions panel and manual task creation form.
- Inline edit for tasks; destructive actions confirm; completed tasks are struck-through.
- Accessibility: clear label associations, focus styles, keyboard friendly controls where possible.

## 13. Testing & Validation

- Framework: PHPUnit + Laravel testing utilities; Livewire component testing available.
- Suggested coverage (examples):
  - Task model: status helpers, priority sequencing (`getNextPriorityOrder`, `reorderTasksForUser`).
  - Policy: deny cross-user access to tasks.
  - Livewire: create/update/delete flows, pagination, filters, and status toggling.
  - Service: parse various Gemini response shapes (including codefenced JSON) and error paths.
- Manual checks: see `TESTING_CHECKLIST.md` for UI smoke tests.

Edge cases to test:

- Empty or too-short description rejected by validation in `AISuggestion`.
- Toggle status rapidly (pending<->completed) maintains correct priority order.
- Deleting a pending task reindexes remaining tasks for the user.
- Access control: user cannot edit/delete another user’s task (policy enforced).

Example PHPUnit test names (suggested):

- `test_user_can_create_task_and_see_it_in_list`
- `test_toggle_task_status_updates_counts_and_priority`
- `test_policy_denies_access_to_other_users_tasks`
- `test_ai_parser_handles_fenced_json`
- `test_ai_mock_mode_generates_keyword_based_suggestions`

## 14. Result Analysis

- Qualitatively, AI-assisted breakdown reduces the friction in planning complex tasks by turning them into clear next steps.
- The Livewire approach yields a snappy UX without maintaining a separate API frontend.
- Priority ordering keeps the pending list tidy and meaningful without extra user effort.

## 15. Deployment Process

Prerequisites: PHP ≥ 8.1, Composer, Node ≥ 18, a database (SQLite/MySQL). Set environment variables for Gemini.

Example `.env` keys:

- GEMINI_API_KEY=your_key
- GEMINI_MODEL=gemini-pro
- GEMINI_API_URL=<https://generativelanguage.googleapis.com>
- GEMINI_TIMEOUT=30
- AI_SUGGESTIONS_ENABLED=true
- AI_MOCK_RESPONSES=false
- AI_CACHE_ENABLED=true
- AI_CACHE_TTL=3600

Typical steps (Windows/PowerShell):

1. Install dependencies (PHP and Node) and copy `.env` from `.env.example`.
2. `php artisan key:generate`
3. Configure DB (SQLite example) and run `php artisan migrate --seed`.
4. Build assets: `npm install` then `npm run build` (or `npm run dev` in development).
5. Configure web server to serve `public/` (or run `php artisan serve`).
6. For queues (optional): set `QUEUE_CONNECTION=database|redis`, run a worker.

PowerShell quickstart (optional):

```powershell
# Backend
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed

# Frontend
npm ci
npm run build

# Serve (dev)
php artisan serve

# Queue worker (optional, if using queues)
php artisan queue:work --tries=3
```

## 16. Screenshot Page

Insert screenshots in this section (reference paths under `public/Images/`):

- Dashboard overview — tasks list and filters.
- AI Suggestions panel with generated sub-tasks.
- Create/edit task modal/form.
- Mobile viewport of the dashboard.

Example:

- ![Background](public/Images/background.jpg)
- ![Logo](public/Images/IntelliTask%20logo.png)

## 17. Limitations

- AI suggestions depend on external API availability and quotas.
- No multi-user sharing/teams; scoped to a single authenticated user.
- Simple two-state status model; no due dates/labels/reminders.
- Drag-and-drop prioritization is not implemented (priority is auto-managed).
- Offline usage is not supported.

Additional considerations:

- No notifications (email/push) for due tasks or reminders.
- No real-time collaboration or shared workspaces.

## 18. Future Enhancements

- Task metadata: due dates, labels/tags, attachments, reminders/notifications.
- Drag-and-drop sorting of pending tasks with persisted order.
- Team collaboration, sharing, and role-based permissions.
- Rich analytics and activity timeline.
- Background job flow for AI with progress/polling UI; rate limiting and retries by user.
- i18n and accessibility audits.

Additional ideas:

- Progressive Web App (PWA) mode and offline caching.
- Calendar and external integrations (Google Calendar, Outlook) for scheduling tasks.
- Notifications via mail and web push; digest summaries.
- Data export/import (CSV/JSON) and basic backups.

## 19. Conclusion

IntelliTask combines a streamlined Livewire experience with AI-assisted planning. The architecture is intentionally simple, with clear seams for growth (queues, policies, caching, configuration), making it suitable for personal productivity while remaining a strong foundation for more advanced features.

## 20. References

- Laravel Framework: <https://laravel.com/docs/10.x>
- Livewire: <https://livewire.laravel.com/>
- Google Generative Language API (Gemini): <https://ai.google.dev/>
- Tailwind CSS: <https://tailwindcss.com/>
- Vite: <https://vitejs.dev/>
- Alpine.js: <https://alpinejs.dev/>
