<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Event\Studio\PreResponse;

use Watza\AttributeHeatmapBundle\Schema\Heatmap\ClassItemCollection;
use Pimcore\Bundle\StudioBackendBundle\Event\AbstractPreResponseEvent;

/**
 * @internal
 */
final class ClassListEvent extends AbstractPreResponseEvent
{
    public const EVENT_NAME = 'pre_response.attribute_heatmap.class_list';

    public function __construct(
        private readonly ClassItemCollection $collection,
    ) {
        parent::__construct($this->collection);
    }

    public function getCollection(): ClassItemCollection
    {
        return $this->collection;
    }
}