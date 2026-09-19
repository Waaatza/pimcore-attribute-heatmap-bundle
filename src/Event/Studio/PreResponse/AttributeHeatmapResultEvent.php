<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Event\Studio\PreResponse;

use Watza\AttributeHeatmapBundle\Schema\Heatmap\AttributeHeatmapResult;
use Pimcore\Bundle\StudioBackendBundle\Event\AbstractPreResponseEvent;

/**
 * @internal
 */
final class AttributeHeatmapResultEvent extends AbstractPreResponseEvent
{
    public const EVENT_NAME = 'pre_response.attribute_heatmap.result';

    public function __construct(
        private readonly AttributeHeatmapResult $result,
    ) {
        parent::__construct($this->result);
    }

    public function getResult(): AttributeHeatmapResult
    {
        return $this->result;
    }
}