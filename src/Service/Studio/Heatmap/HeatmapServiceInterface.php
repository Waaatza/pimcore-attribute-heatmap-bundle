<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Service\Studio\Heatmap;

use Watza\AttributeHeatmapBundle\Schema\Heatmap\AttributeHeatmapResult;

interface HeatmapServiceInterface
{
    /**
     * Analyzes the usage of every attribute for the objects of the given class.
     *
     * @throws \Pimcore\Bundle\StudioBackendBundle\Exception\Api\NotFoundException
     */
    public function analyze(string $classId): AttributeHeatmapResult;
}