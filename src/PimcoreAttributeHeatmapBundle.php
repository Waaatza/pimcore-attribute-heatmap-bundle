<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

/**
 * @internal
 */
final class PimcoreAttributeHeatmapBundle extends AbstractPimcoreBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
