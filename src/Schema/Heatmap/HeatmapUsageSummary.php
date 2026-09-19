<?php
declare(strict_types=1);

namespace Pimcore\Bundle\AttributeHeatmapBundle\Schema\Heatmap;

use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    title: 'Bundle Attribute Heatmap Usage Summary',
    description: 'Aggregated summary of the attribute usage for a class.',
    required: ['total', 'used', 'partiallyUsed', 'unused', 'notAnalyzable'],
    type: 'object',
)]
final readonly class HeatmapUsageSummary
{
    public function __construct(
        #[Property(description: 'Total number of analyzable attributes', type: 'integer', example: 12)]
        private int $total,
        #[Property(description: 'Number of fully used attributes', type: 'integer', example: 5)]
        private int $used,
        #[Property(description: 'Number of partially used attributes', type: 'integer', example: 4)]
        private int $partiallyUsed,
        #[Property(description: 'Number of unused attributes', type: 'integer', example: 2)]
        private int $unused,
        #[Property(description: 'Number of attributes that cannot be analyzed', type: 'integer', example: 1)]
        private int $notAnalyzable,
    ) {
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getUsed(): int
    {
        return $this->used;
    }

    public function getPartiallyUsed(): int
    {
        return $this->partiallyUsed;
    }

    public function getUnused(): int
    {
        return $this->unused;
    }

    public function getNotAnalyzable(): int
    {
        return $this->notAnalyzable;
    }
}