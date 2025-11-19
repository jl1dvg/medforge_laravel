<?php

return [
    'controllers_path' => base_path('controllers'),
    'modules_path' => base_path('modules'),
    'views_path' => base_path('views'),
    'public_path' => public_path(),
    'public_assets_path' => public_path('assets'),

    'modules' => [
        'pacientes' => [
            'name' => 'Pacientes y HC',
            'description' => 'Historial clínico, resumenes y reportes vinculados a cada historia clínica digital.',
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
            'name' => 'Solicitudes y CRM',
            'description' => 'Motor Kanban y formularios de solicitud quirúrgica heredados del stack PHP clásico.',
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
            'name' => 'Facturación',
            'description' => 'Herramientas para prefacturación y reportes IESS/ISSPOL conservadas del sistema previo.',
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
