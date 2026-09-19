<?php
declare(strict_types=1);

namespace Pimcore\Bundle\AttributeHeatmapBundle\Event\Studio\PreResponse;

use Pimcore\Bundle\AttributeHeatmapBundle\Schema\Heatmap\ClassItemCollection;
use Pimcore\Bundle\StudioBackendBundle\Event\AbstractPreResponseEvent;

/**
 * @internal
 */
final class ClassListEvent extends AbstractPreResponseEvent
{
    public const string EVENT_NAME = 'pre_response.attribute_heatmap.class_list';

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