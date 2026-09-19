<?php
declare(strict_types=1);

namespace Pimcore\Bundle\AttributeHeatmapBundle\Schema\Heatmap;

use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Pimcore\Bundle\StudioBackendBundle\Util\Schema\AdditionalAttributesInterface;
use Pimcore\Bundle\StudioBackendBundle\Util\Trait\AdditionalAttributesTrait;

#[Schema(
    title: 'Bundle Attribute Heatmap Class List Item',
    description: 'A data object class selectable for the heatmap analysis.',
    required: ['id', 'name', 'objectCount'],
    type: 'object',
)]
final class ClassListItem implements AdditionalAttributesInterface
{
    use AdditionalAttributesTrait;

    public function __construct(
        #[Property(description: 'Technical ID of the class', type: 'string', example: 'pokemon')]
        private readonly string $id,
        #[Property(description: 'Name of the class', type: 'string', example: 'Pokemon')]
        private readonly string $name,
        #[Property(description: 'Number of objects of the class', type: 'integer', example: 87)]
        private readonly int $objectCount,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getObjectCount(): int
    {
        return $this->objectCount;
    }
}