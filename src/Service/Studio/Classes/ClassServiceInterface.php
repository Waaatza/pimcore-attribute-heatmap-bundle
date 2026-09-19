<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Service\Studio\Classes;

use Watza\AttributeHeatmapBundle\Schema\Heatmap\ClassItemCollection;

interface ClassServiceInterface
{
    public function getClasses(): ClassItemCollection;
}