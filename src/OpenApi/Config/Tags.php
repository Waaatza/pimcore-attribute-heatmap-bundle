<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\OpenApi\Config;

use OpenApi\Attributes\Tag;

#[Tag(
    name: Tags::AttributeHeatmap->value,
    description: 'bundle_tag_attribute_heatmap_description',
)]
/**
 * @internal
 */
enum Tags: string
{
    case AttributeHeatmap = 'Bundle Attribute Heatmap';
}