<?php
declare(strict_types=1);

namespace Pimcore\Bundle\AttributeHeatmapBundle\Service\Studio\Heatmap\Attribute;

use Pimcore\Bundle\AttributeHeatmapBundle\Service\Studio\Heatmap\Model\AttributeDescriptor;
use Pimcore\Model\DataObject\ClassDefinition;

interface AttributeCollectorInterface
{
    /**
     * Collects all attributes of the given class definition as descriptors.
     * Nested structures (localized fields, objectbricks, fieldcollections,
     * blocks and classification stores) are flattened into single attributes.
     *
     * @return AttributeDescriptor[]
     */
    public function collect(ClassDefinition $definition): array;
}