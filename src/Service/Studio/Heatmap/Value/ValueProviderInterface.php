<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Value;

use Pimcore\Model\DataObject\Concrete;

interface ValueProviderInterface
{
    /**
     * Yields the raw values of a single attribute for the given object.
     * Multiple values are yielded for container fields (e.g. one value per item).
     *
     * @return iterable<mixed>
     */
    public function getValues(Concrete $object): iterable;
}