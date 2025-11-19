import { injectLegacyScripts } from '../../legacy/injector.js';
import { initSolicitudModal } from './solicitudes-modal.js';

const patientScripts = [
    'assets/vendor_components/apexcharts-bundle/dist/apexcharts.js',
    'assets/vendor_components/horizontal-timeline/js/horizontal-timeline.js',
];

initSolicitudModal();

if (typeof document !== 'undefined') {
    injectLegacyScripts(patientScripts)
        .then(() => {
            document.dispatchEvent(
                new CustomEvent('legacy:pacientes:scripts-ready', {
                    detail: patientScripts,
                }),
            );
        })
        .catch((error) => console.error(error));
}
