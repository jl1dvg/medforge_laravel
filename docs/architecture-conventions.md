# Convenciones de organización para la migración a Laravel

Estas pautas aseguran que las piezas migradas desde el legado aterricen en namespaces claros y sigan convenciones RESTful.

## Namespaces de servicios y acciones
* Los servicios de dominio y de infraestructura se ubican en `app/Services`. Subcarpetas por dominio (por ejemplo, `app/Services/Pacientes`) evitan mezclar casos de uso clínico con utilidades compartidas.
* Acciones invocables o command objects viven en `app/Actions`. Úsalas para orquestar un caso de uso puntual (por ejemplo, crear/actualizar una entidad o ejecutar un workflow) y así mantener los controladores delgados.

## Controladores y rutas
* Todos los controladores siguen el formato `PascalCaseController` dentro de `app/Http/Controllers` (o subcarpetas por dominio/versión de API).
* Las rutas deben exponerse con verbos y recursos RESTful siempre que sea viable: `index/show/store/update/destroy` y, cuando aplique, `Route::resource` o `Route::apiResource` con nombres explícitos.
* Evita rutas ad-hoc (`/doSomething.php`); agrupa por prefijos de módulo y aplica middleware en el grupo.

## Vistas Blade
* Cada módulo ubica sus vistas en `resources/views/<modulo>/`. Mantén el layout compartido en `resources/views/layouts/` y usa componentes/parciales para cabeceras, menús y formularios.
* No coloques lógica de consulta en las vistas; todos los datos deben llegar preparados desde el controlador/acción/servicio correspondiente.

## Helpers reutilizables
* Refactoriza helpers procedurales de `helpers/` o módulos legados a servicios inyectables o traits bajo `app/Support`.
* Los helpers que requieran estado/configuración deben ser clases registradas en el contenedor (por ejemplo, a través de proveedores o auto-resolución). Traits en `app/Support` sirven para compartir pequeñas utilidades sin dependencias.
