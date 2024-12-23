// Dashboard UI Optimizations
document.addEventListener('DOMContentLoaded', function() {
    // Lazy load images
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });
    images.forEach(img => imageObserver.observe(img));

    // Defer non-critical JavaScript
    function loadDeferredScripts() {
        const deferredScripts = document.querySelectorAll('script[data-defer]');
        deferredScripts.forEach(script => {
            const newScript = document.createElement('script');
            newScript.src = script.getAttribute('data-defer');
            document.body.appendChild(newScript);
            script.remove();
        });
    }
    if (window.requestIdleCallback) {
        requestIdleCallback(loadDeferredScripts);
    } else {
        setTimeout(loadDeferredScripts, 2000);
    }

    // Progressive loading for dashboard widgets
    const widgets = document.querySelectorAll('.dashboard-widget');
    let loadedWidgets = 0;
    const widgetObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const widget = entry.target;
                const url = widget.dataset.url;
                if (url) {
                    fetch(url)
                        .then(response => response.text())
                        .then(html => {
                            widget.innerHTML = html;
                            loadedWidgets++;
                            if (loadedWidgets === widgets.length) {
                                observer.disconnect();
                            }
                        });
                }
                observer.unobserve(widget);
            }
        });
    });
    widgets.forEach(widget => widgetObserver.observe(widget));

    // Cache API implementation for dashboard data
    if ('caches' in window) {
        const CACHE_NAME = 'dashboard-cache-v1';
        const CACHE_DURATION = 5 * 60 * 1000; // 5 minutes

        async function fetchWithCache(url) {
            const cache = await caches.open(CACHE_NAME);
            const cachedResponse = await cache.match(url);
            
            if (cachedResponse) {
                const cachedData = await cachedResponse.json();
                if (Date.now() - cachedData.timestamp < CACHE_DURATION) {
                    return cachedData.data;
                }
            }

            const response = await fetch(url);
            const data = await response.json();
            await cache.put(url, new Response(JSON.stringify({
                data: data,
                timestamp: Date.now()
            })));
            return data;
        }

        // Implement cache for dashboard API endpoints
        window.dashboardCache = {
            getData: fetchWithCache
        };
    }
});
