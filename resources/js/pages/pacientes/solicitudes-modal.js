import axios from 'axios';

const DEFAULT_DOCUMENT_FILTER = 'ultimos_3_meses';

const isValidDate = (value) => value instanceof Date && !Number.isNaN(value.getTime());

const filterDocuments = (filter) => {
    const items = document.querySelectorAll('.media-list .media');
    if (!items.length) {
        return;
    }

    const now = new Date();

    items.forEach((item) => {
        const dateElement = item.querySelector('.text-fade');
        const dateText = dateElement ? dateElement.textContent.trim() : '';
        const itemDate = dateText ? new Date(dateText) : null;
        let showItem = true;

        if (isValidDate(itemDate)) {
            switch (filter) {
                case 'ultimo_mes': {
                    const lastMonth = new Date(now);
                    lastMonth.setMonth(now.getMonth() - 1);
                    showItem = itemDate >= lastMonth;
                    break;
                }
                case 'ultimos_3_meses': {
                    const last3Months = new Date(now);
                    last3Months.setMonth(now.getMonth() - 3);
                    showItem = itemDate >= last3Months;
                    break;
                }
                case 'ultimos_6_meses': {
                    const last6Months = new Date(now);
                    last6Months.setMonth(now.getMonth() - 6);
                    showItem = itemDate >= last6Months;
                    break;
                }
                default:
                    showItem = true;
            }
        }

        item.style.display = showItem ? 'flex' : 'none';
    });
};

const descargarPDFsSeparados = (formId, hcNumber) => {
    if (!formId || !hcNumber) {
        return false;
    }

    const paginas = ['protocolo', '005', 'medicamentos', 'signos_vitales', 'insumos', 'saveqx', 'transanestesico'];
    let index = 0;

    const abrirVentana = () => {
        if (index >= paginas.length) {
            return;
        }

        const pagina = paginas[index];
        const url = `/reports/protocolo/pdf?form_id=${encodeURIComponent(formId)}&hc_number=${encodeURIComponent(
            hcNumber,
        )}&modo=separado&pagina=${encodeURIComponent(pagina)}`;

        const ventana = window.open(url, '_blank');
        const tiempoEspera = pagina === 'transanestesico' ? 9000 : 2500;

        window.setTimeout(() => {
            ventana?.close();
            index += 1;
            window.setTimeout(abrirVentana, 300);
        }, tiempoEspera);
    };

    abrirVentana();
    return false;
};

const parseDiagnosticos = (diagnosticos) => {
    if (!diagnosticos) {
        return [];
    }

    if (Array.isArray(diagnosticos)) {
        return diagnosticos;
    }

    try {
        return JSON.parse(diagnosticos);
    } catch (error) {
        console.warn('No se pudo parsear la lista de diagnósticos', error);
        return [];
    }
};

const formatDiagnosticos = (diagnosticos) => {
    if (!diagnosticos.length) {
        return '—';
    }

    return diagnosticos
        .map((diagnostico, index) => {
            const id = diagnostico?.idDiagnostico ?? 'N/A';
            const ojo = diagnostico?.ojo ?? '—';
            return `${index + 1}. ${id} (${ojo})`;
        })
        .join('<br>');
};

const formatFecha = (fecha) => {
    if (!fecha) {
        return '—';
    }

    const date = new Date(fecha);
    if (!isValidDate(date)) {
        return fecha;
    }

    return date.toLocaleDateString('es-EC');
};

const actualizarSemaforo = (estado, fechaStr) => {
    const semaforo = document.getElementById('modalSemaforo');
    if (!semaforo) {
        return;
    }

    let color = 'gray';
    if (estado?.toLowerCase() === 'recibido' && fechaStr) {
        const fechaSolicitud = new Date(fechaStr);
        const hoy = new Date();
        if (isValidDate(fechaSolicitud)) {
            const diffDias = Math.floor((hoy - fechaSolicitud) / (1000 * 60 * 60 * 24));
            if (diffDias > 14) {
                color = 'red';
            } else if (diffDias > 7) {
                color = 'yellow';
            } else {
                color = 'green';
            }
        }
    }

    semaforo.style.backgroundColor = color;
};

const assignWindowHelpers = () => {
    if (typeof window === 'undefined') {
        return;
    }

    window.filterDocuments = (filter) => filterDocuments(filter ?? DEFAULT_DOCUMENT_FILTER);
    window.descargarPDFsSeparados = descargarPDFsSeparados;
};

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

const loadSolicitudDetalle = async (hcNumber, formId) => {
    const { data } = await axios.get(`/api/pacientes/${encodeURIComponent(hcNumber)}/solicitudes/${encodeURIComponent(formId)}`);
    return data;
};

const hydrateSolicitudModal = (data) => {
    const fechaEl = document.getElementById('modalFecha');
    if (fechaEl) {
        fechaEl.textContent = formatFecha(data?.fecha);
    }

    const procedimientoEl = document.getElementById('modalProcedimiento');
    if (procedimientoEl) {
        procedimientoEl.textContent = data?.procedimiento ?? '—';
    }

    const diagnosticoEl = document.getElementById('modalDiagnostico');
    if (diagnosticoEl) {
        diagnosticoEl.innerHTML = formatDiagnosticos(parseDiagnosticos(data?.diagnosticos));
    }

    const doctorEl = document.getElementById('modalDoctor');
    if (doctorEl) {
        doctorEl.textContent = data?.doctor ?? '—';
    }

    const descripcionEl = document.getElementById('modalDescripcion');
    if (descripcionEl) {
        descripcionEl.textContent = data?.plan ?? '—';
    }

    const ojoEl = document.getElementById('modalOjo');
    if (ojoEl) {
        ojoEl.textContent = data?.ojo ?? '—';
    }

    const estadoEl = document.getElementById('modalEstado');
    if (estadoEl) {
        estadoEl.textContent = data?.estado ?? '—';
    }

    const motivoEl = document.getElementById('modalMotivo');
    if (motivoEl) {
        motivoEl.textContent = data?.motivo_consulta ?? '—';
    }

    const enfermedadEl = document.getElementById('modalEnfermedad');
    if (enfermedadEl) {
        enfermedadEl.textContent = data?.enfermedad_actual ?? '—';
    }

    actualizarSemaforo(data?.estado ?? '', data?.fecha ?? '');
};

export const initSolicitudModal = () => {
    assignWindowHelpers();

    ready(() => {
        filterDocuments(DEFAULT_DOCUMENT_FILTER);

        const modal = document.getElementById('modalSolicitud');
        if (!modal) {
            return;
        }

        modal.addEventListener('show.bs.modal', async (event) => {
            const button = event.relatedTarget;
            if (!button) {
                return;
            }

            const hcNumber = button.getAttribute('data-hc');
            const formId = button.getAttribute('data-form-id');
            if (!hcNumber || !formId) {
                return;
            }

            try {
                const data = await loadSolicitudDetalle(hcNumber, formId);
                hydrateSolicitudModal(data);
            } catch (error) {
                console.error('No se pudo cargar los detalles de la solicitud', error);
            }
        });
    });
};
