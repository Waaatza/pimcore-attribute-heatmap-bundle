<?php
declare(strict_types=1);

namespace Pimcore\Bundle\AttributeHeatmapBundle\Service\Studio\Heatmap;

use Pimcore\Bundle\AttributeHeatmapBundle\Schema\Heatmap\AttributeHeatmapResult;

interface HeatmapServiceInterface
{
    /**
     * Analyzes the usage of every attribute for the objects of the given class.
     *
     * @throws \Pimcore\Bundle\StudioBackendBundle\Exception\Api\NotFoundException
     */
    public function analyze(string $classId): AttributeHeatmapResult;
}