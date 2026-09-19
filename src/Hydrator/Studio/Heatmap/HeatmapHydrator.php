<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Hydrator\Studio\Heatmap;

use Watza\AttributeHeatmapBundle\Schema\Heatmap\AttributeHeatmapResult;
use Watza\AttributeHeatmapBundle\Schema\Heatmap\ClassListItem;
use Watza\AttributeHeatmapBundle\Schema\Heatmap\HeatmapAttribute;
use Watza\AttributeHeatmapBundle\Schema\Heatmap\HeatmapClassInfo;
use Watza\AttributeHeatmapBundle\Schema\Heatmap\HeatmapUsageSummary;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Model\AttributeDescriptor;
use Watza\AttributeHeatmapBundle\Util\Constant\AttributeUsageState;

final readonly class HeatmapHydrator implements HeatmapHydratorInterface
{
    public function hydrateClassInfo(string $classId, string $className, int $objectCount): HeatmapClassInfo
    {
        return new HeatmapClassInfo(
            classId: $classId,
            name: $className,
            objectCount: $objectCount,
        );
    }

    public function hydrate(
        HeatmapClassInfo $classInfo,
        array $descriptors,
        array $usedCounts,
        int $totalCount,
    ): AttributeHeatmapResult {
        $attributes = [];
        $summary = [
            AttributeUsageState::USED->value => 0,
            AttributeUsageState::PARTIALLY_USED->value => 0,
            AttributeUsageState::UNUSED->value => 0,
            AttributeUsageState::NOT_ANALYZABLE->value => 0,
        ];

        foreach ($descriptors as $index => $descriptor) {
            $attribute = $this->hydrateAttribute($descriptor, $usedCounts[$index] ?? 0, $totalCount);

            $attributes[] = $attribute;

            $state = $attribute->getUsageState();
            if (isset($summary[$state->value])) {
                $summary[$state->value]++;
            }
        }

        return new AttributeHeatmapResult(
            classInfo: $classInfo,
            attributes: $attributes,
            usageSummary: new HeatmapUsageSummary(
                total: count($attributes),
                used: $summary[AttributeUsageState::USED->value],
                partiallyUsed: $summary[AttributeUsageState::PARTIALLY_USED->value],
                unused: $summary[AttributeUsageState::UNUSED->value],
                notAnalyzable: $summary[AttributeUsageState::NOT_ANALYZABLE->value],
            ),
        );
    }

    public function hydrateClassListItem(string $classId, string $className, int $objectCount): ClassListItem
    {
        return new ClassListItem(
            id: $classId,
            name: $className,
            objectCount: $objectCount,
        );
    }

    private function hydrateAttribute(AttributeDescriptor $descriptor, int $usedCount, int $totalCount): HeatmapAttribute
    {
        $analyzable = $descriptor->getAnalyzable();
        $ratio = $analyzable && $totalCount > 0 ? round($usedCount / $totalCount, 2) : 0.0;

        return new HeatmapAttribute(
            name: $descriptor->getName(),
            title: $descriptor->getTitle(),
            fieldType: $descriptor->getFieldType(),
            group: $descriptor->getGroup(),
            usageState: $this->resolveUsageState(risingRatio: $ratio, usedCount: $usedCount, analyzable: $analyzable),
            usedCount: $analyzable ? $usedCount : null,
            totalCount: $totalCount,
            usageRatio: $analyzable ? $ratio : null,
        );
    }

    private function resolveUsageState(float $risingRatio, int $usedCount, bool $analyzable): AttributeUsageState
    {
        if (!$analyzable) {
            return AttributeUsageState::NOT_ANALYZABLE;
        }

        if ($risingRatio <= 0.0) {
            return AttributeUsageState::UNUSED;
        }

        if ($usedCount === 0) {
            return AttributeUsageState::UNUSED;
        }

        if ($risingRatio < 1.0) {
            return AttributeUsageState::PARTIALLY_USED;
        }

        return AttributeUsageState::USED;
    }
}