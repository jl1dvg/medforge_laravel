import './jquery-2.1.4';

if (typeof window !== 'undefined') {
    window.$ = window.$ || window.jQuery;
    window.jQuery = window.jQuery || window.$;
}
