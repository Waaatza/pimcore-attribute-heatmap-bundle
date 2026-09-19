<?php
declare(strict_types=1);

namespace Pimcore\Bundle\AttributeHeatmapBundle\Schema\Heatmap;

use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Pimcore\Bundle\AttributeHeatmapBundle\Util\Constant\AttributeUsageState;

#[Schema(
    title: 'Bundle Attribute Heatmap Attribute',
    description: 'Usage data of a single data object attribute.',
    required: ['name', 'title', 'fieldType', 'group', 'usageState', 'usedCount', 'totalCount', 'usageRatio'],
    type: 'object',
)]
final readonly class HeatmapAttribute
{
    public function __construct(
        #[Property(description: 'Technical name of the attribute', type: 'string', example: 'attack')]
        private string $name,
        #[Property(description: 'Human readable title of the attribute', type: 'string', example: 'Attack')]
        private string $title,
        #[Property(description: 'Field type of the attribute', type: 'string', example: 'numeric')]
        private string $fieldType,
        #[Property(description: 'Group the attribute belongs to', type: 'string', example: 'Stats')]
        private string $group,
        #[Property(description: 'Usage state of the attribute', type: 'string', example: 'partiallyUsed')]
        private AttributeUsageState $usageState,
        #[Property(description: 'Number of objects holding a value', type: 'integer', example: 42)]
        private int $usedCount,
        #[Property(description: 'Total number of analyzed objects', type: 'integer', example: 87)]
        private int $totalCount,
        #[Property(description: 'Ratio of used objects to total objects', type: 'number', example: 0.48)]
        private float $usageRatio,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getFieldType(): string
    {
        return $this->fieldType;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getUsageState(): AttributeUsageState
    {
        return $this->usageState;
    }

    public function getUsedCount(): int
    {
        return $this->usedCount;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getUsageRatio(): float
    {
        return $this->usageRatio;
    }
}