# Geographic search

The `emsch_map` route serves the HTML map and its JSON data in all four website
languages. For example: `/fr/carte` and `/fr/carte.json`.

## Google Maps configuration

Enable the Google Maps JavaScript API for the public browser key and provide the
key to Vite before starting the development server or building the assets:

```dotenv
VITE_GOOGLE_MAPS_API_KEY=YOUR_GOOGLE_MAPS_API_KEY
VITE_GOOGLE_MAPS_MAP_ID=your_map_id
```

These variables can be set in the local `.env` file or in the build environment.
Rebuild with `npm run prod` after changing them. The template also supports the
Twig globals `googleMapsApiKey` (or `googleApiKey`) and `googleMapsMapId` when
these are already provided by the runtime. An existing
`google.maps.importLibrary` loader is reused.

The marker implementation uses `AdvancedMarkerElement`. If no map ID is
configured, Google's `DEMO_MAP_ID` is used. The API key is required to display
the
map. [Google Maps loading documentation](https://developers.google.com/maps/documentation/javascript/load-maps-js-api)
and [advanced markers](https://developers.google.com/maps/documentation/javascript/advanced-markers/add-marker).

## Aggregation behavior

| Google Maps zoom | Location levels         |
|------------------|-------------------------|
| 2–6              | 1: country              |
| 7–8              | 2: region/province      |
| 9–11             | 16: municipality        |
| 12–20            | 43 and 47: neighborhood |

The JSON endpoint requires `zoom`, `north`, `south`, `east`, and `west`. It
filters versions by their coordinates inside this bounding box, groups their
nested `locations` by `place_id` at the selected levels, and uses
`reverse_nested` to count distinct versions. Pin positions are the centroid of
the group's versions in the viewport, so a location's geographic center outside
the viewport does not hide the group.

A representative version provides the preview image. Each popup links to the
complete location gallery via the `search` route, preserving the current
language. The gallery can therefore contain more versions than the viewport
count.

Results use composite aggregation pages of 200 groups. The `after` response
field is sent back unchanged to retrieve the next page; the client retrieves
every page before replacing the markers. Map movement cancels the previous
request and prevents stale responses from updating the map.

## Validation

```bash
node --test tests/location-map.test.js
npm run prod
make lint/acc
```

The JavaScript tests cover pagination, stale requests, popup content, empty
results, errors, retry and a missing API key. They use Google Maps and DOM
doubles; a browser with a configured key and the running ElasticMS service is
still needed for integration validation.
