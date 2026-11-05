import assert from 'node:assert/strict';
import { test } from 'node:test';
import { fetchMapMarkers, mountLocationMap, setupLocationMap } from '../src/js/module/location-map.js';

class Element {
    constructor() {
        this.children = [];
        this.attributes = {};
        this.dataset = {};
    }
    append(...children) { this.children.push(...children); }
    setAttribute(key, value) { this.attributes[key] = value; }
    querySelector(selector) { return this.parts?.[selector]; }
}

function fixture() {
    const status = new Element();
    status.parts = { span: new Element(), button: new Element() };
    const element = new Element();
    element.dataset = {
        markersUrl: '/fr/carte.json', connecting: 'Loading', loadError: 'Error',
        noResults: 'No photos', openGallery: 'View gallery'
    };
    element.parentElement = { querySelector: () => status };
    const points = [];
    const map = {
        zoom: 10,
        handlers: {},
        getZoom() { return this.zoom; },
        getBounds() {
            return {
                getNorthEast: () => ({ lat: () => 51.5, lng: () => 6.4 }),
                getSouthWest: () => ({ lat: () => 49.5, lng: () => 2.5 })
            };
        },
        addListener(name, callback) {
            this.handlers[name] = callback;
            return { remove: () => { delete this.handlers[name]; } };
        }
    };
    const info = {
        close() { this.opened = false; },
        setContent(content) { this.content = content; },
        open(options) { this.opened = options; }
    };
    class Map { constructor() { return map; } }
    class InfoWindow { constructor() { return info; } }
    class AdvancedMarkerElement extends Element {
        constructor(options) { super(); Object.assign(this, options); points.push(this); }
        addListener(name, callback) { this[name] = callback; }
    }
    globalThis.window = { location: { href: 'https://example.test/fr/carte' } };
    globalThis.document = { createElement: () => new Element() };
    return { element, status, points, map, info, libraries: [{ Map, InfoWindow }, { AdvancedMarkerElement }] };
}

const group = {
    id: 'place-id', label: 'Commune <test>', count: 42, lat: 50.8, lng: 4.3,
    url: '/fr/chercher?location=place-id', image: '/preview.webp', imageLabel: 'Photo "test"'
};
const response = (markers, after = null) => ({ ok: true, json: async () => ({ markers, after }) });
const controller = (f) => mountLocationMap(f.element, ...f.libraries);

test('pagination preserves the viewport and the server cursor', async () => {
    fixture();
    const urls = [];
    globalThis.fetch = async (url) => {
        urls.push(url);
        return urls.length === 1 ? response([group], 'globalview_place:cursor') : response([{ ...group, id: 'second' }]);
    };
    const viewport = { zoom: 10, north: 51.5, south: 49.5, east: 6.4, west: 2.5 };
    const markers = await fetchMapMarkers('/fr/carte.json', viewport, new AbortController().signal);
    assert.equal(markers.length, 2);
    assert.equal(urls[1].searchParams.get('after'), 'globalview_place:cursor');
    for (const [key, value] of Object.entries(viewport)) assert.equal(urls[1].searchParams.get(key), String(value));
});

test('invalid pages and repeated cursors fail rather than silently omitting results', async () => {
    fixture();
    globalThis.fetch = async () => response([], 'globalview_place:same');
    await assert.rejects(fetchMapMarkers('/fr/carte.json', {}, new AbortController().signal), /cursor/);
    globalThis.fetch = async () => ({ ok: true, json: async () => ({}) });
    await assert.rejects(fetchMapMarkers('/fr/carte.json', {}, new AbortController().signal), /Invalid map data/);
    globalThis.fetch = async () => ({ ok: false });
    await assert.rejects(fetchMapMarkers('/fr/carte.json', {}, new AbortController().signal), /could not be loaded/);
});

test('pins show counts and their popup links the photo and label to the location gallery', async () => {
    const f = fixture();
    globalThis.fetch = async () => response([group]);
    const view = controller(f);
    await view.refresh();
    assert.equal(f.points.length, 1);
    assert.equal(f.points[0].children[0].children[0].textContent, '42');
    f.points[0].click();
    const link = f.info.content.children[0];
    assert.equal(link.href, group.url);
    assert.equal(link.children[0].src, group.image);
    assert.equal(link.children[0].alt, group.imageLabel);
    assert.equal(link.children[1].children[0].textContent, group.label);
    assert.equal(f.status.hidden, true);
    view.destroy();
});

test('movement aborts pending requests and ignores late responses', async () => {
    const f = fixture();
    const pending = [];
    globalThis.fetch = (_url, { signal }) => new Promise((resolve) => pending.push({ signal, resolve }));
    const view = controller(f);
    const first = view.refresh();
    f.map.handlers.bounds_changed();
    assert.equal(pending[0].signal.aborted, true);
    f.map.zoom = 13;
    const second = view.refresh();
    pending[1].resolve(response([group]));
    await second;
    pending[0].resolve(response([{ ...group, count: 999 }]));
    await first;
    assert.equal(f.points.length, 1);
    assert.equal(f.points[0].title, `${group.label} (42)`);
    assert.equal(f.element.attributes['aria-busy'], 'false');
    view.destroy();
});

test('empty areas, errors and retry update the visible status', async () => {
    const f = fixture();
    const view = controller(f);
    globalThis.fetch = async () => response([]);
    await view.refresh();
    assert.equal(f.status.parts.span.textContent, 'No photos');
    assert.equal(f.status.parts.button.hidden, true);
    globalThis.fetch = async () => { throw new Error('Offline'); };
    await view.refresh();
    assert.equal(f.status.parts.span.textContent, 'Error');
    assert.equal(f.status.parts.button.hidden, false);
    globalThis.fetch = async () => response([group]);
    await f.status.parts.button.onclick();
    assert.equal(f.status.hidden, true);
    view.destroy();
    assert.equal(f.points[0].map, null);
});

test('a missing API key leaves an understandable error and retry action', async () => {
    const f = fixture();
    globalThis.document.getElementById = () => f.element;
    await setupLocationMap();
    assert.equal(f.status.parts.span.textContent, 'Error');
    assert.equal(f.status.parts.button.hidden, false);
});
