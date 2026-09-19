<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\OpenApi\Config;

use Pimcore\Bundle\StudioBackendBundle\Controller\AbstractApiController;

/**
 * @internal
 */
final class Prefix
{
    public const BUNDLE = AbstractApiController::PREFIX . '/bundle/attribute-heatmap';
}