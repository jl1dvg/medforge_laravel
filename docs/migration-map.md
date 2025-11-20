# Legacy → Laravel migration map

This document summarizes how the legacy PHP application is structured and how each moving part should land inside a conventional Laravel project. It also highlights risky spots (direct superglobals, tight coupling to PDO, global state, etc.) so we know what must be rewritten while migrating.

## Directory responsibilities and dependencies

### `controllers/`
* **Responsibility.** Stand-alone PHP entrypoints that include `bootstrap.php`, read `$_POST`/`$_SERVER`, run SQL through the global `$pdo`, and then echo or redirect. They encapsulate flows such as login, patient CRUD, billing, traceability, etc., without using Laravel routing or middleware. Example: `LoginController.php` checks `$_SERVER['REQUEST_METHOD']`, reads `$_POST`, queries `users`, mutates `$_SESSION` and calls `header('Location: /dashboard')`.【F:controllers/LoginController.php†L1-L37】
* **Dependencies.** Global `$pdo`, procedural helpers, modules (e.g., `GuardarConsultaController` instantiates `Modules\Examenes\Services\ConsultaExamenSyncService`), and the `Core\Auth`/`Core\Permissions` utilities loaded via `bootstrap.php`.
* **Laravel destination.** Convert each file into a proper controller under `app/Http/Controllers`, using dependency injection for repositories/services and Form Request objects for validation. Routes go into `routes/web.php` or feature-specific route files.
* **Rewrite needs.** Replace direct `$_POST`, header redirects, and manual sessions with Laravel’s request/response/session APIs. All PDO queries must be rewritten to Eloquent/Query Builder to leverage database configuration and connection pooling.

### `helpers/`
* **Responsibility.** Procedural helpers for formatting, PDF generation, logging, tariff lookups, AI integration, search builders, etc. They are required directly from controllers and views to keep business logic concise. Legacy examples like `obtenerNombrePaciente` have been replaced by container-resolved services (`App\\Services\\PatientNameService::getFullName`) to avoid global state.【F:app/Services/PatientNameService.php†L1-L29】
* **Dependencies.** Plain functions that expect raw `PDO`, filesystem paths, or third-party SDKs (OpenAI, PDF libraries). Some rely on globals (e.g., `$pdo` or `$_SESSION`) implicitly.
* **Laravel destination.** Convert into service classes or helper classes within `app/Support`, `app/Services`, or `app/Actions`, registered via Laravel’s service container. Cross-cutting utilities (logging, formatting) can become invokable services or traits.
* **Rewrite needs.** Replace manual PDO usage with repositories or injected models. Wrap external SDK calls with configurable services so credentials live in `config/*.php` and `.env`. Remove reliance on globals by injecting dependencies.

### `models/`
* **Responsibility.** Thin data-access classes encapsulating raw SQL over PDO for billing, solicitudes, protocolos, trazabilidad, etc. `Models\SolicitudModel`, for instance, builds long SQL strings, performs dynamic filters, and returns associative arrays.【F:models/SolicitudModel.php†L1-L140】
* **Dependencies.** Constructor-injected `PDO` plus tight coupling to the legacy schema (table names, JSON columns, manual joins). They also interoperate with helpers and controllers by returning arrays.
* **Laravel destination.** Promote entities into Eloquent models under `app/Models` (e.g., `Solicitud`, `PatientData`, `ConsultaData`). Complex read models can move into repositories or query objects in `app/Repositories`.
* **Rewrite needs.** Re-express SQL as Eloquent scopes or query builders, utilize relationships, casts for JSON columns, and leverage migrations/seeders. Some methods currently serialize arrays to JSON manually; these should use casts.

### `modules/`
* **Responsibility.** Feature packages (Auth, Billing, Pacientes, Agenda, etc.) each containing sub-controllers, services, `routes.php`, and sometimes Blade-like views. Routing is handled by a custom `Core\Router` where each route closure receives the raw `\PDO` instance. Example: `modules/Auth/routes.php` registers `/auth/login`/`logout` and instantiates `AuthController` manually with `$pdo`.【F:modules/Auth/routes.php†L1-L20】
* **Dependencies.** Custom router, manual DI, shared helpers, and the same PDO connection. Views inside modules often include files from `views/`.
* **Laravel destination.** Merge controllers into `app/Http/Controllers`, move routes into `routes/web.php` (possibly grouped by feature), relocate services into `app/Services`, and convert module views into Blade templates within `resources/views/modules/*`.
* **Rewrite needs.** Replace the custom router with Laravel routing/middleware, inject dependencies through constructors, and convert module-specific helpers to service classes. Because module views often expect globals, they must be rewritten to accept data arrays passed from controllers.

### `scrapping/`
* **Responsibility.** Stand-alone Python scripts (`scrapping_agenda.py`, `scrape_log_admision.py`, etc.) that scrape external hospital systems and push data into the API. For instance, `scrapping_agenda.py` logs into a remote site via `requests`, parses HTML, classifies procedures, and posts JSON payloads to `/api/proyecciones/guardar.php`.【F:scrapping/scrapping_agenda.py†L1-L160】
* **Dependencies.** Python’s `requests`, `BeautifulSoup`, inline credentials, and direct HTTP calls to the legacy API endpoints.
* **Laravel destination.** Convert the ingestion logic into Laravel console commands (`app/Console/Commands`) or queued jobs, using first-party HTTP clients and storing credentials in `.env`. If Python must remain, wrap it with a job runner triggered by Laravel.
* **Rewrite needs.** Remove hard-coded credentials, adopt Laravel’s scheduler/queue for execution, and replace direct API posts with repository/service calls so data lands in the database via Laravel models.

### `views/`
* **Responsibility.** Raw PHP templates composing the UI. `views/layout.php` bootstraps HTML, determines whether the current view is an auth screen, reads `$_SESSION`, hits the global `$pdo` to fetch user data, and includes partials manually.【F:views/layout.php†L1-L200】
* **Dependencies.** Globals (`$_SESSION`, `$GLOBALS['pdo']`), helper functions defined inline, and direct includes of other PHP files.
* **Laravel destination.** Migrate to Blade templates under `resources/views` (layout, components, partials). Shared components (header, navbar, notification panel) become Blade includes or components, and scripts/styles should leverage Laravel Mix/Vite.
* **Rewrite needs.** Remove inline PHP logic that queries the database; instead, pass data from controllers/view models. Replace manual asset helpers with Laravel’s `@vite`/`mix`. Sessions should use Laravel’s `Auth` facade.

## Mapping summary

| Legacy directory | Responsibility snapshot | Laravel destination | Rewrite blockers |
| --- | --- | --- | --- |
| `controllers/` | HTTP entrypoints that couple routing, validation, DB, and responses. | `app/Http/Controllers`, `routes/web.php`, dedicated Form Requests. | Uses `$_POST`, manual redirects, globals, so every action needs refactoring to Laravel requests/responses. |
| `helpers/` | Procedural utilities for formatting, billing, PDF, AI, tracing, etc. | `app/Support`, `app/Services`, or `app/Actions` bound in the container. | Expect raw `PDO` or globals; they must become classes with injected dependencies/config. |
| `models/` | PDO-based repositories returning arrays. | Eloquent models (`app/Models`) + repositories/query objects. | SQL must be rewritten to Query Builder, JSON handling to casts, relationships defined. |
| `modules/` | Feature bundles containing ad-hoc routes/controllers/views/services. | Split between `routes/*.php`, `app/Http/Controllers`, `app/Services`, `resources/views/modules/*`. | Custom router and manual dependency wiring must be replaced with Laravel’s routing + DI; module views expect globals. |
| `scrapping/` | Python scrapers that seed data via HTTP. | Laravel console commands/jobs (or well-defined external workers triggered by Laravel). | Hard-coded credentials and direct API posts require redesign to use Laravel’s scheduler/config + repositories. |
| `views/` | PHP templates with includes and DB calls. | Blade templates in `resources/views`, Blade components, and layouts. | Inline DB queries, session access, and helper definitions must move to controllers/components. |

## Modules priority and migration order

| Priority | Module / area | Why it is critical | Key dependencies | Laravel landing zone |
| --- | --- | --- | --- | --- |
| 1 | **Auth & Login** (controllers `LoginController.php`, `modules/Auth/*`) | Blocks every other feature because sessions/permissions originate here. Relies on `Core\Auth`, manual sessions, and raw PDO queries with password verification.【F:controllers/LoginController.php†L1-L37】【F:modules/Auth/routes.php†L1-L20】 | Core router, PDO, session globals. | `app/Http/Controllers/Auth`, `routes/auth.php`, Laravel `Auth` scaffolding, `resources/views/auth`. |
| 2 | **Dashboard + Layout** (`controllers/DashboardController.php`, `views/layout.php`) | Shared shell for most modules; currently fetches user data directly from `$GLOBALS['pdo']` inside the view, so nothing else can render without it.【F:views/layout.php†L91-L160】 | Global PDO, session data, partial includes. | Blade layout + middleware-protected dashboard routes. |
| 3 | **Solicitudes / Pacientes** (`controllers/SolicitudController.php`, `models/SolicitudModel.php`, modules `solicitudes`, `Pacientes`) | Core clinical workflow (turnero, agendas). Complex SQL and helper usage make it risky; needs repository + Eloquent relationships.【F:models/SolicitudModel.php†L17-L140】 | PDO models, helpers, modules services. | `app/Models/Solicitud`, `app/Repositories/SolicitudRepository`, controllers + Blade views. |
| 4 | **Billing / Facturación** (`controllers/BillingController.php`, `models/Billing*`, `helpers/FacturacionHelper.php`) | Direct impact on revenue; includes multiple models and helpers for tariffs and invoices. Needs cohesive domain services before other financial modules can migrate. | Multiple PDO models, tariff helpers, PDF generation. | Domain services under `app/Services/Billing`, Eloquent models for billing tables, Blade invoices. |
| 5 | **Trazabilidad / Reporting / Notifications** (`controllers/TrazabilidadController.php`, modules `Reporting`, `Notifications`, helpers `JsonLogger.php`) | Provides auditing and alerts; depends on migrated auth and solicitudes data. Once core flows run on Laravel, traceability should be ported to maintain compliance. | Logging helpers, modules, existing controllers. | `app/Events`, `app/Notifications`, queued jobs, Blade reports. |
| 6 | **Scrapping inputs** (`scrapping/*.py`) | After Laravel exposes APIs/models, ingestion must switch to Laravel commands so schedules and credentials are centralized. | External HTTP, inline secrets. | `app/Console/Commands`, queue workers, HTTP client. |

The order (Auth → Layout/Dashboard → Solicitudes/Pacientes → Billing → Traceability/Reporting → Scrapers) ensures that foundational concerns (authentication, shared UI, core domain data) are migrated before dependent domains. Each phase should include rewriting helpers/models into services/repositories so subsequent modules can reuse them without legacy globals.
