<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @internal
 */
final class PimcoreAttributeHeatmapExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $container->setParameter('pimcore_attribute_heatmap.bundle_path', \dirname(__DIR__, 2));

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');
    }
}