<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Hydrator\Studio\Heatmap;

use Watza\AttributeHeatmapBundle\Schema\Heatmap\AttributeHeatmapResult;
use Watza\AttributeHeatmapBundle\Schema\Heatmap\ClassListItem;
use Watza\AttributeHeatmapBundle\Schema\Heatmap\HeatmapClassInfo;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Model\AttributeDescriptor;

interface HeatmapHydratorInterface
{
    public function hydrateClassInfo(string $classId, string $className, int $objectCount): HeatmapClassInfo;

    /**
     * @param array<int, AttributeDescriptor> $descriptors
     * @param array<int, int> $usedCounts
     */
    public function hydrate(
        HeatmapClassInfo $classInfo,
        array $descriptors,
        array $usedCounts,
        int $totalCount,
    ): AttributeHeatmapResult;

    public function hydrateClassListItem(string $classId, string $className, int $objectCount): ClassListItem;
}