<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Usage;

final readonly class SqlUsageResult
{
    /**
     * @param array<int, int> $usedCounts
     * @param array<int, int> $remainingIndexes
     */
    public function __construct(
        private array $usedCounts,
        private array $remainingIndexes,
    ) {
    }

    /**
     * @return array<int, int>
     */
    public function getUsedCounts(): array
    {
        return $this->usedCounts;
    }

    /**
     * Indexes of descriptors that still need object-based usage counting.
     *
     * @return array<int, int>
     */
    public function getRemainingIndexes(): array
    {
        return $this->remainingIndexes;
    }
}