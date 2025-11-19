const LEGACY_ATTRIBUTE = 'data-legacy-src';

const ensureDocument = () => typeof document !== 'undefined';

const normalize = (path) => path.replace(/^\/+/, '');

export const legacyPublicUrl = (path) => new URL(`../../public/${normalize(path)}`, import.meta.url).href;

const scriptExists = (normalized) => {
    if (!ensureDocument()) {
        return false;
    }

    return Array.from(document.querySelectorAll(`[${LEGACY_ATTRIBUTE}]`)).some(
        (script) => script.getAttribute(LEGACY_ATTRIBUTE) === normalized,
    );
};

export const injectLegacyScripts = async (paths = []) => {
    if (!ensureDocument()) {
        return;
    }

    for (const entry of paths) {
        const normalized = normalize(entry);

        if (scriptExists(normalized)) {
            continue;
        }

        await new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = legacyPublicUrl(normalized);
            script.setAttribute(LEGACY_ATTRIBUTE, normalized);
            script.async = false;
            script.onload = resolve;
            script.onerror = () => reject(new Error(`No se pudo cargar el recurso legacy: ${normalized}`));
            document.head.appendChild(script);
        });
    }
};
