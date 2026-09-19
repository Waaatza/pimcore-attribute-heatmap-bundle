<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Schema\Heatmap;

use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    title: 'Bundle Attribute Heatmap Class Info',
    description: 'Context information about the analyzed data object class.',
    required: ['classId', 'name', 'objectCount'],
    type: 'object',
)]
final readonly class HeatmapClassInfo
{
    public function __construct(
        #[Property(description: 'Technical ID of the class', type: 'string', example: 'pokemon')]
        private string $classId,
        #[Property(description: 'Name of the class', type: 'string', example: 'Pokemon')]
        private string $name,
        #[Property(description: 'Number of analyzed objects', type: 'integer', example: 87)]
        private int $objectCount,
    ) {
    }

    public function getClassId(): string
    {
        return $this->classId;
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