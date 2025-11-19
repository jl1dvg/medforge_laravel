import { injectLegacyScripts } from './injector.js';

const coreScripts = [
    'js/vendors.min.js',
    'js/pages/chat-popup.js',
    'assets/icons/feather-icons/feather.min.js',
    'js/jquery.smartmenus.js',
    'js/menus.js',
    'js/pages/global-search.js',
    'js/template.js',
];

if (typeof document !== 'undefined') {
    injectLegacyScripts(coreScripts)
        .then(() => {
            document.dispatchEvent(
                new CustomEvent('legacy:core-scripts-ready', {
                    detail: coreScripts,
                }),
            );
        })
        .catch((error) => console.error(error));
}
