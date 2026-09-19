<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Schema\Heatmap;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Pimcore\Bundle\StudioBackendBundle\Util\Schema\AdditionalAttributesInterface;
use Pimcore\Bundle\StudioBackendBundle\Util\Trait\AdditionalAttributesTrait;

#[Schema(
    title: 'Bundle Attribute Heatmap Result Response',
    description: 'The full heatmap analysis result for a data object class.',
    required: ['classInfo', 'attributes', 'usageSummary'],
    type: 'object',
)]
final class AttributeHeatmapResult implements AdditionalAttributesInterface
{
    use AdditionalAttributesTrait;

    /**
     * @param array<int, HeatmapAttribute> $attributes
     */
    public function __construct(
        #[Property(description: 'Context information about the analyzed class', ref: HeatmapClassInfo::class)]
        private readonly HeatmapClassInfo $classInfo,
        #[Property(
            description: 'Usage data for every attribute of the class',
            type: 'array',
            items: new Items(ref: HeatmapAttribute::class),
        )]
        private readonly array $attributes,
        #[Property(description: 'Aggregated usage summary', ref: HeatmapUsageSummary::class)]
        private readonly HeatmapUsageSummary $usageSummary,
    ) {
    }

    public function getClassInfo(): HeatmapClassInfo
    {
        return $this->classInfo;
    }

    /**
     * @return array<int, HeatmapAttribute>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getUsageSummary(): HeatmapUsageSummary
    {
        return $this->usageSummary;
    }
}