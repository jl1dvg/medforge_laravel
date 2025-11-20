# Legacy controller migration plan

This plan addresses the legacy controllers enumerated in `medforge_bak/controllers` and `medforge_bak/controllers/Services` and proposes a practical order for migrating them into Laravel controllers, services, and routes.

## Goals
- Preserve current behaviors while moving to Laravel conventions (routes in `routes/web.php` or `routes/api.php`, controllers under `app/Http/Controllers`, and request validation classes).
- Isolate business logic in injectable services (e.g., `app/Services`) instead of keeping it in controllers.
- Normalize responses (JSON or Blade views) and middleware (auth, CSRF) per endpoint.

## Recommended migration order
1) **Authentication and session bootstrap**
   - `LoginController.php`, `UserController.php` (session/user handling), and any logout endpoints ensure authenticated flows work early.
   - Create Form Requests for login and user updates; configure auth guards and middleware on routes.
2) **Core patient and clinical flows**
   - `PacienteController.php`, `HistoriaClinica.php`, `Solicitudes`-related controllers (`SolicitudController.php`, `GuardarSolicitudController.php`, `DerivacionController.php`).
   - Move validation to Form Requests and shift persistence to services (e.g., `PacienteService`).
3) **Procedures and protocols**
   - `ProcedimientoController.php`, `ListarProcedimientosController.php`, `GuardarProtocoloController.php`, `ObtenerInsumosProtocoloController.php`, `MoverInsumosController.php`, `IplPlanificadorController.php`.
   - Extract inventory/protocol logic into services; map legacy routes to resourceful routes where feasible.
4) **Billing and reporting**
   - `BillingController.php`, `GuardarPrefacturaController.php`, `BillingService.php`, `ExportService.php`, `PreviewService.php`, `ReporteCirugiasController.php`, `EstadisticaFlujoController.php`.
   - Normalize response formats (PDF/CSV/JSON) and queue heavy exports if needed.
5) **Support and lookup features**
   - `DiagnosticoController.php`, `PalabraClaveController.php`, `RecetasController.php`, `ReglaController.php`, `SugerenciaController.php`, `AIController.php`, `PdfController.php`.
   - Convert helpers to services and centralize shared queries/formatting.
6) **Audit and traceability**
   - `TrazabilidadController.php` and related helpers to ensure auditing remains consistent post-migration.

## Routing and structure suggestions
- Mirror legacy endpoints in `routes/web.php` (for Blade/UI) and `routes/api.php` (for JSON) using route groups with middleware (`auth`, `verified`, `throttle`).
- For controllers with clear CRUD semantics (e.g., `PacienteController.php`, `ProcedimientoController.php`), use `Route::resource` where possible; otherwise, define explicit routes to match legacy URIs.
- Use dedicated Form Requests under `app/Http/Requests` to replicate legacy validation rules and centralize authorization checks.
- Place business logic in services under `app/Services/<Feature>Service.php` and inject them into controllers; migrate helper functions into these services or `app/Support` classes.

## Next actionable steps
- Pick the **authentication block** first (Login/User), build the Laravel auth flow, and backfill tests to confirm login/logout/session state matches the legacy behavior.
- Trace legacy routes for **Paciente** and **Solicitud** flows, document expected payloads/responses, and scaffold controllers + requests + services accordingly.
- Define a mapping table (legacy path → Laravel route/controller@method) as you migrate each block to track parity and simplify QA.
