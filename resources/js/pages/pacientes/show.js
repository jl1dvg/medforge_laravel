import { injectLegacyScripts } from '../../legacy/injector.js';

const patientScripts = [
    'assets/vendor_components/apexcharts-bundle/dist/apexcharts.js',
    'assets/vendor_components/horizontal-timeline/js/horizontal-timeline.js',
    'js/pages/patient-detail.js',
];

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
