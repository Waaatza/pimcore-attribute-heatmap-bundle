<?php
declare(strict_types=1);

namespace Pimcore\Bundle\AttributeHeatmapBundle\Service\Studio\Heatmap\Usage;

interface FieldUsageResolverInterface
{
    public function isUsed(mixed $value): bool;
}