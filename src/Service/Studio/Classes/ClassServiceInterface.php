<?php
declare(strict_types=1);

namespace Pimcore\Bundle\AttributeHeatmapBundle\Service\Studio\Classes;

use Pimcore\Bundle\AttributeHeatmapBundle\Schema\Heatmap\ClassItemCollection;

interface ClassServiceInterface
{
    public function getClasses(): ClassItemCollection;
}