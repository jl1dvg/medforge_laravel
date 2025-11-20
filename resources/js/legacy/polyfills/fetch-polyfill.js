if (typeof window !== 'undefined' && typeof window.fetch === 'undefined') {
    window.fetch = function (url, options = {}) {
        return new Promise(function (resolve, reject) {
            const request = new XMLHttpRequest();
            request.open(options.method || 'GET', url, true);

            if (options.headers) {
                Object.entries(options.headers).forEach(function ([key, value]) {
                    request.setRequestHeader(key, value);
                });
            }

            request.onload = function () {
                const response = {
                    ok: request.status >= 200 && request.status < 300,
                    status: request.status,
                    statusText: request.statusText,
                    text: function () {
                        return Promise.resolve(request.responseText);
                    },
                    json: function () {
                        try {
                            return Promise.resolve(JSON.parse(request.responseText));
                        } catch (error) {
                            return Promise.reject(error);
                        }
                    },
                };
                resolve(response);
            };

            request.onerror = function () {
                reject(new TypeError('Network request failed'));
            };

            request.send(options.body || null);
        });
    };
}
