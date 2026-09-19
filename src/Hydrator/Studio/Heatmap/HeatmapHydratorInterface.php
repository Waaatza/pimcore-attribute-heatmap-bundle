<?php
declare(strict_types=1);

namespace Pimcore\Bundle\AttributeHeatmapBundle\Hydrator\Studio\Heatmap;

use Pimcore\Bundle\AttributeHeatmapBundle\Schema\Heatmap\AttributeHeatmapResult;
use Pimcore\Bundle\AttributeHeatmapBundle\Schema\Heatmap\ClassListItem;
use Pimcore\Bundle\AttributeHeatmapBundle\Schema\Heatmap\HeatmapClassInfo;
use Pimcore\Bundle\AttributeHeatmapBundle\Service\Studio\Heatmap\Model\AttributeDescriptor;

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