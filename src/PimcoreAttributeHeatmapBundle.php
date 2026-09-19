<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
final class PimcoreAttributeHeatmapBundle extends AbstractPimcoreBundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->setParameter('pimcore_attribute_heatmap.bundle_path', $this->getPath());
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
