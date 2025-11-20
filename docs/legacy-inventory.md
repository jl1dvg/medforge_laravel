# Legacy component inventory (medforge_bak)

## Entry points and routing
- `medforge_bak/public/index.php` bootstraps the legacy app, wires a minimal `Core\Router`, registers module routes, and forwards a few hardcoded billing/reporting endpoints before returning 404s for unknown paths.
- `medforge_bak/core/Router.php` stores GET/POST route callbacks, supports simple dynamic segments with `{}` placeholders, and dispatches routes against the incoming method/path.
- `medforge_bak/core/ModuleLoader.php` auto-loads each subfolder in `medforge_bak/modules`, requiring optional `index.php` and registering route definitions from `routes.php` when present.
- `medforge_bak/api` exposes JSON endpoints per feature (e.g., `consultas/guardar.php` decodes request bodies, instantiates the corresponding controller, and returns JSON responses).

## Controllers (root namespace)
- HTTP/feature controllers in `medforge_bak/controllers`: `AIController.php`, `BillingController.php`, `DashboardController.php`, `DerivacionController.php`, `DiagnosticoController.php`, `EstadisticaFlujoController.php`, `GuardarConsultaController.php`, `GuardarPrefacturaController.php`, `GuardarProtocoloController.php`, `GuardarProyeccionController.php`, `GuardarSolicitudController.php`, `HistoriaClinica.php`, `IplPlanificadorController.php`, `ListarProcedimientosController.php`, `LoginController.php`, `MoverInsumosController.php`, `ObtenerInsumosProtocoloController.php`, `PacienteController.php`, `PalabraClaveController.php`, `PdfController.php`, `ProcedimientoController.php`, `RecetasController.php`, `ReglaController.php`, `ReporteCirugiasController.php`, `SolicitudController.php`, `SugerenciaController.php`, `TrazabilidadController.php`, `UserController.php`.
- Service-style controllers under `medforge_bak/controllers/Services`: `BillingService.php`, `ExportService.php`, `PreviewService.php`.

## Models
- Data-access classes in `medforge_bak/models`: `BillingAnestesiaModel.php`, `BillingDerechosModel.php`, `BillingInsumosModel.php`, `BillingMainModel.php`, `BillingOxigenoModel.php`, `BillingProcedimientosModel.php`, `Cirugia.php`, `CodeCategory.php`, `CodeType.php`, `DiagnosticoModel.php`, `EstadisticaFlujoModel.php`, `IplPlanificadorModel.php`, `PalabraClaveModel.php`, `Price.php`, `PriceLevel.php`, `ProcedimientoModel.php`, `ProtocoloModel.php`, `RecetaModel.php`, `RelatedCode.php`, `SettingsModel.php`, `SettingsRepository.php`, `SolicitudModel.php`, `Tarifario.php`, `TrazabilidadModel.php`, `UserModel.php`.

## Helpers and utilities
- Helper classes in `medforge_bak/helpers`: `CodeService.php`, `CorsHelper.php`, `FacturacionHelper.php`, `InformesHelper.php`, `IplHelper.php`, `JsonLogger.php`, `OpenAIHelper.php`, `PacientesHelper.php`, `PdfGenerator.php`, `ProtocoloHelper.php`, `SearchBuilder.php`, `SettingsHelper.php`, `SolicitudHelper.php`, `TarifarioHelper.php`, `format.php`, `trazabilidad_helpers.php`.

## Views
- Legacy views/templates reside in `medforge_bak/views`, organized into subfolders such as `billing`, `components`, `errors`, `ipl`, `pacientes`, `partials`, `pdf`, `recetas`, `reglas`, `reportes`, `users`, plus layout files like `layout.php` and `layout-turnero.php` and auth pages (`login.php`, `logout.php`, `main.php`).

## Modules
- Feature modules live in `medforge_bak/modules`, each optionally exposing routes via `routes.php` and boot code via `index.php`. Module folders include `AI`, `Agenda`, `Auth`, `Billing`, `CRM`, `Cirugias`, `CiveExtension`, `Codes`, `CronManager`, `Dashboard`, `Doctores`, `EditorProtocolos`, `Flowmaker`, `IdentityVerification`, `Insumos`, `KPI`, `Mail`, `Notifications`, `Pacientes`, `Reporting`, `Search`, `Settings`, `Shared`, `Usuarios`, `WhatsApp`, `Wpbox`, `examenes`, `solicitudes`, and others; for example, `modules/Auth/routes.php` registers login/logout endpoints against the legacy router.
