<?php

return [
    'controllers_path' => base_path('controllers'),
    'modules_path' => base_path('modules'),
    'views_path' => base_path('views'),
    'public_path' => public_path(),
    'public_assets_path' => public_path('assets'),

    'modules' => [
        'pacientes' => [
            'enabled' => env('LEGACY_MODULE_PACIENTES_ENABLED', true),
            'name' => 'Pacientes y HC',
            'description' => 'Historial clínico, resumenes y reportes vinculados a cada historia clínica digital.',
            'risk' => 'Cualquier caída deja a admisiones y médicos sin acceso al historial clínico consolidado.',
            'legacy_entry' => 'PacienteController.php',
            'folder' => null,
            'views' => [
                'pacientes',
            ],
            'routes' => [
                'web' => [
                    ['method' => 'GET', 'uri' => '/pacientes'],
                    ['method' => 'POST', 'uri' => '/pacientes/datatable'],
                ],
                'api' => [
                    ['method' => 'GET', 'uri' => '/api/pacientes/{hcNumber}'],
                    ['method' => 'POST', 'uri' => '/api/solicitudes/kanban_data.php'],
                ],
            ],
            'assets' => [
                'styles' => [
                    'css/vendors_css.css',
                    'assets/vendor_components/apexcharts-bundle/dist/apexcharts.css',
                    'assets/vendor_components/horizontal-timeline/css/horizontal-timeline.css',
                ],
                'scripts' => [
                    'js/pages/patient-detail.js',
                    'assets/vendor_components/apexcharts-bundle/dist/apexcharts.js',
                    'assets/vendor_components/horizontal-timeline/js/horizontal-timeline.js',
                ],
            ],
        ],
        'solicitudes' => [
            'enabled' => env('LEGACY_MODULE_SOLICITUDES_ENABLED', true),
            'name' => 'Solicitudes y CRM',
            'description' => 'Motor Kanban y formularios de solicitud quirúrgica heredados del stack PHP clásico.',
            'risk' => 'Interrupciones paralizan el seguimiento de solicitudes y afectan coordinación de quirófano.',
            'legacy_entry' => 'SolicitudController.php',
            'folder' => 'Flowmaker',
            'views' => [
                'pacientes/flujo',
                'billing',
            ],
            'routes' => [
                'web' => [
                    ['method' => 'GET', 'uri' => '/solicitudes'],
                    ['method' => 'GET', 'uri' => '/solicitudes/kanban'],
                ],
                'api' => [
                    ['method' => 'POST', 'uri' => '/api/solicitudes/guardar.php'],
                    ['method' => 'GET', 'uri' => '/api/solicitudes/kanban_data.php'],
                ],
            ],
            'assets' => [
                'styles' => [
                    'css/vendors_css.css',
                    'css/horizontal-menu.css',
                ],
                'scripts' => [
                    'js/pages/solicitudes/index.js',
                    'js/pages/solicitudes/kanban/index.js',
                ],
            ],
        ],
        'billing' => [
            'enabled' => env('LEGACY_MODULE_BILLING_ENABLED', true),
            'name' => 'Facturación',
            'description' => 'Herramientas para prefacturación y reportes IESS/ISSPOL conservadas del sistema previo.',
            'risk' => 'Errores impactan entregables oficiales y comprometen la recaudación mensual.',
            'legacy_entry' => 'BillingController.php',
            'folder' => 'Billing',
            'views' => [
                'billing',
            ],
            'routes' => [
                'web' => [
                    ['method' => 'GET', 'uri' => '/billing'],
                    ['method' => 'GET', 'uri' => '/billing/detalle'],
                ],
                'api' => [
                    ['method' => 'POST', 'uri' => '/api/billing/guardar_billing.php'],
                    ['method' => 'POST', 'uri' => '/api/billing/insertar_billing_main.php'],
                ],
            ],
            'assets' => [
                'styles' => [
                    'css/vendors_css.css',
                    'css/style.css',
                ],
                'scripts' => [
                    'js/pages/patient-detail.js',
                    'assets/vendor_components/bootstrap/dist/js/bootstrap.bundle.min.js',
                ],
            ],
        ],
    ],
];
