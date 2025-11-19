import axios from 'axios';
import { injectLegacyScripts } from '../../legacy/injector.js';

const DATATABLE_SCRIPTS = ['assets/vendor_components/datatable/datatables.min.js'];

const ready = (callback) => {
    if (typeof document === 'undefined') {
        return;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback, { once: true });
    } else {
        callback();
    }
};

const initDataTable = async () => {
    const tableElement = document.getElementById('pacientes-table');
    if (!tableElement) {
        return;
    }

    try {
        await injectLegacyScripts(DATATABLE_SCRIPTS);
    } catch (error) {
        console.error('No se pudo cargar DataTables', error);
        return;
    }

    if (typeof window.$ === 'undefined' || typeof window.$.fn.DataTable !== 'function') {
        console.warn('jQuery o DataTables no están disponibles en la ventana.');
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

    window.$(tableElement).DataTable({
        serverSide: true,
        processing: true,
        ajax: (requestData, callback) => {
            axios
                .post('/pacientes/datatable', requestData, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                })
                .then((response) => {
                    callback(response.data);
                })
                .catch((error) => {
                    console.error('No se pudo cargar la tabla de pacientes', error);
                    callback({
                        data: [],
                        recordsTotal: 0,
                        recordsFiltered: 0,
                        draw: requestData?.draw ?? 1,
                    });
                });
        },
        columns: [
            { data: 'hc_number' },
            { data: 'ultima_fecha' },
            { data: 'full_name' },
            { data: 'afiliacion' },
            { data: 'estado_html', orderable: false, searchable: false },
            { data: 'acciones_html', orderable: false, searchable: false },
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json',
        },
    });
};

ready(initDataTable);
