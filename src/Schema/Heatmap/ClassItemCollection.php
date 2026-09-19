<?php
declare(strict_types=1);

namespace Pimcore\Bundle\AttributeHeatmapBundle\Schema\Heatmap;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Pimcore\Bundle\StudioBackendBundle\Util\Schema\AdditionalAttributesInterface;
use Pimcore\Bundle\StudioBackendBundle\Util\Trait\AdditionalAttributesTrait;

#[Schema(
    title: 'Bundle Attribute Heatmap Class List Response',
    description: 'A list of data object classes available for the heatmap analysis.',
    required: ['items', 'totalItems'],
    type: 'object',
)]
final class ClassItemCollection implements AdditionalAttributesInterface
{
    use AdditionalAttributesTrait;

    /**
     * @param array<int, ClassListItem> $items
     */
    public function __construct(
        #[Property(description: 'List of classes', type: 'array', items: new Items(ref: ClassListItem::class))]
        private readonly array $items,
        #[Property(description: 'Total number of classes', type: 'integer', example: 3)]
        private readonly int $totalItems,
    ) {
    }

    /**
     * @return array<int, ClassListItem>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }
}