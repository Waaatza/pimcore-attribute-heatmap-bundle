# Pimcore Attribute Heatmap Bundle

A [Pimcore Studio](https://pimcore.com/) bundle that analyzes **data object attribute usage** and visualizes it as a **heatmap**. For every attribute of a chosen object class, the bundle iterates over all objects (and variants) and determines how many of them have a value set. The result is shown per attribute, grouped by the containing structure (General, Localizedfields per language, Objectbricks, Fieldcollections, Classificationstore, Blocks).

## Features

- Class picker with live object counts
- On-demand analysis when opening the widget (batch processing, `200` objects per iteration)
- Per-attribute usage state: `used`, `partially used`, `unused`, `not analyzable`
- Usage summary tags and a legend
- Attribute names displayed in a per-group heatmap grid with tooltips
- Handles nested structures:
  - Localizedfields (analyzed **per language**, e.g. `name_en`, `name_de`, `name_fr`)
  - Objectbricks and Fieldcollections (name includes the brick/collection type)
  - Blocks (per field name)
  - Classificationstore (grouped by store group)
- Extensible via interfaces:
  - `AttributeCollectorInterface` / `ValueProviderInterface` (`Service\Studio\Heatmap\Value`)
  - `FieldUsageResolverInterface` (`Service\Studio\Heatmap\Usage`)
  - `HeatmapHydratorInterface` (`Hydrator\Studio\Heatmap`)
- Pre-response events (`pre_response.attribute_heatmap.class_list` and `pre_response.attribute_heatmap.result`) allow third parties to modify the API responses.

This branch targets the **Pimcore 2025.4 release train** (`pimcore/pimcore` `^12.3.9`, Studio `^2025.4`). For the current Pimcore 2026 release train, see the `main` branch.

## Requirements

- PHP `>= 8.2`
- Pimcore `^12.3.9` (2025.4)
- Pimcore Studio UI `^2025.4`
- `pimcore/studio-backend-bundle` `^2025.4`
- `pimcore/static-resolver-bundle` `^3.6.2`

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
4. The heatmap groups attributes by their structure and colors them by usage state:

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
- Objects are iterated in batches of `200` with inherited values disabled; the batch size can be tuned via the `BATCH_SIZE` constant in `HeatmapService`.
- A value counts as *used* when it is not null/empty. Field-type specific heuristics live in `FieldUsageResolver` (scalars, arrays, `QuantityValue`/`InputQuantityValue`, consent checkboxes, etc.).
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