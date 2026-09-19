# Pimcore Attribute Heatmap Bundle

A [Pimcore Studio](https://pimcore.com/) bundle that analyzes **data object attribute usage** and visualizes it as a **heatmap and bar chart**. For every attribute of a chosen object class, the bundle determines how many objects (and variants) have a value set. Counting happens via SQL aggregates on the class data tables, so even large classes are analyzed quickly without loading every object. The result is shown per attribute, grouped by the containing structure (General, Localizedfields per language, Objectbricks, Fieldcollections, Classificationstore, Blocks).

## Features

- Class picker with live object counts
- On-demand analysis when opening the widget (SQL aggregates on the class data tables, no full object hydration)
- Per-attribute usage state: `used`, `partially used`, `unused`, `not analyzable`
- Usage summary tags and a legend
- Attribute names with field type and usage ratio displayed in a per-group, responsive heatmap grid with tooltips
- Bar chart view with group filtering and sorting by usage or attribute name
- Handles nested structures:
  - Localizedfields (analyzed **per language**, e.g. `name_en`, `name_de`, `name_fr`)
  - Objectbricks and Fieldcollections (name includes the brick/collection type)
  - Blocks (per field name)
  - Classificationstore (grouped by store group)
- Extensible via interfaces:
  - `AttributeCollectorInterface` / `ValueProviderInterface` (`Service\Studio\Heatmap\Value`)
  - `FieldUsageResolverInterface` (`Service\Studio\Heatmap\Usage`)
  - `SqlUsageCounterInterface` (`Service\Studio\Heatmap\Usage`)
  - `HeatmapHydratorInterface` (`Hydrator\Studio\Heatmap`)
- Pre-response events (`pre_response.attribute_heatmap.class_list` and `pre_response.attribute_heatmap.result`) allow third parties to modify the API responses.

## Requirements

- PHP `>= 8.2`
- Pimcore `^11.0 || ^12.0 || ^2026.1`
- Pimcore Studio UI `^2026.2`
- `pimcore/studio-backend-bundle` `^2026.2`
- `pimcore/static-resolver-bundle` `^2026.2`

## Installation

In the composer.json of your Pimcore application, register the repository and require the bundle (all other dependencies are resolved automatically):

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/Waaatza/pimcore-attribute-heatmap-bundle"
    }
  ],
  "require": {
    "watza/attribute-heatmap-bundle": "^0.1"
  }
}
```

```bash
composer update watza/attribute-heatmap-bundle
```

The bundle is enabled automatically via `extra.pimcore.bundles` in its composer.json. Run `bin/console cache:clear` afterwards.

## Usage

1. Log in to Pimcore Studio.
2. Navigate to **Data Management → Attribute Heatmap**.
3. Select an object class.
4. Switch between the heatmap and chart tabs. The heatmap groups attributes by their structure and colors them by usage state; the chart supports group filtering and sorting by usage or name.

| State           | Meaning                                               |
| --------------- | ----------------------------------------------------- |
| Used            | value set on every object                             |
| Partially used  | value set on some, but not all objects                |
| Unused          | value set on no object                                |
| Not analyzable  | field type cannot be analyzed (e.g. password, reverse object relation) |

Hover an attribute tile to see the exact `used / total` count.

## How the analysis works

- The heatmap list is exposed at `GET /pimcore-studio/api/bundle/attribute-heatmap/classes`.
- The result is exposed at `GET /pimcore-studio/api/bundle/attribute-heatmap/classes/{classId}/heatmap`.
- Progress and the final result are streamed as server-sent events at `GET /pimcore-studio/api/bundle/attribute-heatmap/classes/{classId}/heatmap/stream`.
- Usage counting runs in `SqlUsageCounter`: it builds one `COUNT(...)` aggregate per attribute against the resolved class data tables (`object_store_…`, localized, relations, plus the dedicated brick/fieldcollection/classificationstore/block tables). Field-type specific predicates decide what counts as *used* (e.g. checkboxes check for `'1'`, numerics for `IS NOT NULL`, relation columns for non-empty serialized values).
- Attributes that cannot be mapped to a table column (e.g. unknown/legacy table layouts) fall back to an object loop in batches of `200` with inherited values disabled; the batch size can be tuned via the `BATCH_SIZE` constant in `HeatmapService`.
- The object loop reuses the same semantics: a value counts as *used* when it is not null/empty. Field-type specific heuristics live in `FieldUsageResolver` (scalars, arrays, `QuantityValue`/`InputQuantityValue`, consent checkboxes, etc.).
- Not analyzable field types: `password`, `reverseObjectRelation`.

## Development

The frontend lives in `assets/` and is built with Rsbuild + module federation:

```bash
cd assets
npm install
npm run build          # production build into ../public/build/{buildId}
npm run dev-server     # development (port 3034)
```

The bundle exposes its UI entrypoint via `WebpackEntryPointProvider` and registers the widget and navigation entry automatically.

## License

This bundle is published under the **MIT License** – see [LICENSE.md](LICENSE.md).
