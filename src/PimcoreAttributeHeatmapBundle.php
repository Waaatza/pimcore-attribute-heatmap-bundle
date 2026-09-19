<?php
declare(strict_types=1);

namespace Pimcore\Bundle\AttributeHeatmapBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class PimcoreAttributeHeatmapBundle extends AbstractPimcoreBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}