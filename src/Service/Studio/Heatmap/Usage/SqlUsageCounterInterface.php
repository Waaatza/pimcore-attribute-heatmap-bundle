<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Usage;

use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Model\AttributeDescriptor;
use Pimcore\Model\DataObject\ClassDefinition;

interface SqlUsageCounterInterface
{
    /**
     * @param array<int, AttributeDescriptor> $descriptors
     */
    public function count(string $classId, ClassDefinition $definition, array $descriptors): SqlUsageResult;
}