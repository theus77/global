let mapsApiPromise;

export function loadGoogleMaps(apiKey, language) {
    if (window.google?.maps?.importLibrary) {
        return Promise.resolve(window.google.maps);
    }
    if (!apiKey) {
        return Promise.reject(new Error('Google Maps API key is missing'));
    }
    if (!mapsApiPromise) {
        mapsApiPromise = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            const url = new URL('https://maps.googleapis.com/maps/api/js');
            url.search = new URLSearchParams({ key: apiKey, v: 'weekly', loading: 'async', callback: '__globalviewMapsReady', language });
            window.__globalviewMapsReady = () => {
                delete window.__globalviewMapsReady;
                resolve(window.google.maps);
            };
            script.onerror = () => {
                delete window.__globalviewMapsReady;
                script.remove();
                mapsApiPromise = undefined;
                reject(new Error('Google Maps could not be loaded'));
            };
            script.src = url.href;
            script.async = true;
            document.head.append(script);
        });
    }
    return mapsApiPromise;
}

export async function fetchMapMarkers(endpoint, viewport, signal) {
    const markers = [];
    const cursors = new Set();
    let after = null;
    do {
        const url = new URL(endpoint, window.location.href);
        Object.entries(viewport).forEach(([key, value]) => url.searchParams.set(key, value));
        if (after) {
            url.searchParams.set('after', after);
        }
        const response = await fetch(url, { signal, headers: { Accept: 'application/json' } });
        if (!response.ok) {
            throw new Error('Map data could not be loaded');
        }
        const page = await response.json();
        if (!Array.isArray(page.markers)) {
            throw new Error('Invalid map data');
        }
        signal.throwIfAborted();
        markers.push(...page.markers);
        after = page.after;
        if (after && (typeof after !== 'string' || cursors.has(after))) {
            throw new Error('Invalid map cursor');
        }
        cursors.add(after);
    } while (after);
    return markers;
}

export function createMapPopup(data, openGallery) {
    const popup = document.createElement('article');
    popup.className = 'location-map-popup';
    const link = document.createElement('a');
    link.href = data.url;
    link.setAttribute('aria-label', `${openGallery} : ${data.label}`);
    const image = document.createElement('img');
    image.src = data.image;
    image.alt = data.imageLabel;
    image.width = 300;
    image.height = 200;
    const caption = document.createElement('span');
    caption.className = 'location-map-popup-caption';
    const title = document.createElement('span');
    title.textContent = data.label;
    caption.append(title);
    if (data.kind !== 'version') {
        const count = document.createElement('span');
        count.className = 'badge rounded-pill';
        count.textContent = String(data.count);
        caption.append(count);
    }
    link.append(image, caption);
    popup.append(link);
    return popup;
}

export function mountLocationMap(element, { Map, InfoWindow }, { AdvancedMarkerElement }) {
    const map = new Map(element, {
        center: { lat: 50.5, lng: 4.5 },
        zoom: 8,
        minZoom: 2,
        maxZoom: 20,
        tilt: 0,
        mapTypeId: 'hybrid',
        mapId: element.dataset.googleMapsId || import.meta.env?.VITE_GOOGLE_MAPS_MAP_ID || 'DEMO_MAP_ID',
        streetViewControl: false,
        fullscreenControl: true
    });
    const status = element.parentElement.querySelector('.location-map-status');
    const retry = status.querySelector('button');
    const infoWindow = new InfoWindow({ disableAutoPan: true, maxWidth: 360 });
    const listeners = [];
    let markers = [];
    let request;
    let generation = 0;
    let timer;

    function setStatus(message, canRetry = false) {
        status.hidden = !message;
        status.querySelector('span').textContent = message;
        retry.hidden = !canRetry;
    }

    function clearMarkers() {
        markers.forEach((marker) => { marker.map = null; });
        markers = [];
        infoWindow.close();
    }

    async function refresh() {
        const bounds = map.getBounds();
        if (!bounds) {
            return;
        }
        request?.abort();
        const currentGeneration = ++generation;
        request = new AbortController();
        const { signal } = request;
        setStatus(element.dataset.connecting);
        element.setAttribute('aria-busy', 'true');
        try {
            const data = await fetchMapMarkers(element.dataset.markersUrl, {
                zoom: Math.floor(map.getZoom()),
                north: bounds.getNorthEast().lat(),
                east: bounds.getNorthEast().lng(),
                south: bounds.getSouthWest().lat(),
                west: bounds.getSouthWest().lng()
            }, signal);
            if (signal.aborted || currentGeneration !== generation) {
                return;
            }
            clearMarkers();
            markers = data.map((group) => {
                const pin = document.createElement('div');
                pin.className = 'location-map-pin';
                if (group.kind !== 'version') {
                    const count = document.createElement('span');
                    count.textContent = String(group.count);
                    pin.append(count);
                }
                const marker = new AdvancedMarkerElement({
                    map,
                    position: { lat: Number(group.lat), lng: Number(group.lng) },
                    title: group.kind === 'version' ? group.label : `${group.label} (${group.count})`,
                    gmpClickable: true
                });
                marker.append(pin);
                marker.addEventListener('gmp-click', () => {
                    infoWindow.setContent(createMapPopup(group, element.dataset.openGallery));
                    infoWindow.open({ map, anchor: marker });
                });
                return marker;
            });
            setStatus(data.length ? '' : element.dataset.noResults);
        } catch (error) {
            if (!signal.aborted && currentGeneration === generation) {
                clearMarkers();
                setStatus(element.dataset.loadError, true);
            }
        } finally {
            if (currentGeneration === generation) {
                element.setAttribute('aria-busy', 'false');
            }
        }
    }

    listeners.push(map.addListener('bounds_changed', () => {
        ++generation;
        request?.abort();
        clearTimeout(timer);
        clearMarkers();
    }));
    listeners.push(map.addListener('idle', () => {
        clearTimeout(timer);
        timer = setTimeout(refresh, 150);
    }));
    retry.onclick = refresh;
    return {
        map,
        refresh,
        destroy() {
            ++generation;
            request?.abort();
            clearTimeout(timer);
            clearMarkers();
            listeners.forEach((listener) => listener.remove());
            retry.onclick = null;
        }
    };
}

export async function setupLocationMap() {
    const element = document.getElementById('location-map');
    if (!element) {
        return;
    }
    const status = element.parentElement.querySelector('.location-map-status');
    const retry = status.querySelector('button');
    async function start() {
        status.hidden = false;
        status.querySelector('span').textContent = element.dataset.connecting;
        retry.hidden = true;
        try {
            const api = await loadGoogleMaps(element.dataset.googleMapsKey || import.meta.env?.VITE_GOOGLE_MAPS_API_KEY, element.dataset.language);
            const [mapsLibrary, markerLibrary] = await Promise.all([api.importLibrary('maps'), api.importLibrary('marker')]);
            return mountLocationMap(element, mapsLibrary, markerLibrary);
        } catch (error) {
            status.querySelector('span').textContent = element.dataset.loadError;
            retry.hidden = false;
            retry.onclick = start;
        }
    }
    return start();
}
