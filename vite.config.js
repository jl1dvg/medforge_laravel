import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/legacy.css',
                'resources/css/legacy-auth.css',
                'resources/css/legacy-dashboard.css',
                'resources/css/vendor/datatables.css',
                'resources/js/app.js',
                'resources/js/legacy/index.js',
                'resources/js/pages/pacientes/index.js',
                'resources/js/pages/pacientes/show.js',
                'resources/js/legacy/patients-legacy.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
