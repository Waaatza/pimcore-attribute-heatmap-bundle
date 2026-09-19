<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Usage;

interface FieldUsageResolverInterface
{
    public function isUsed(mixed $value): bool;
}