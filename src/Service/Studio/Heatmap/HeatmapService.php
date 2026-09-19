<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Service\Studio\Heatmap;

use Watza\AttributeHeatmapBundle\Event\Studio\PreResponse\AttributeHeatmapResultEvent;
use Watza\AttributeHeatmapBundle\Hydrator\Studio\Heatmap\HeatmapHydratorInterface;
use Watza\AttributeHeatmapBundle\Schema\Heatmap\AttributeHeatmapResult;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Attribute\AttributeCollectorInterface;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Model\AttributeDescriptor;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Usage\FieldUsageResolverInterface;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Usage\SqlUsageCounterInterface;
use Pimcore\Bundle\StaticResolverBundle\Models\DataObject\ClassDefinitionResolverInterface;
use Pimcore\Bundle\StaticResolverBundle\Models\DataObject\DataObjectResolverInterface;
use Pimcore\Bundle\StudioBackendBundle\Exception\Api\NotFoundException;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final readonly class HeatmapService implements HeatmapServiceInterface
{
    private const BATCH_SIZE = 200;

    public function __construct(
        private ClassDefinitionResolverInterface $classDefinitionResolver,
        private DataObjectResolverInterface $dataObjectResolver,
        private AttributeCollectorInterface $attributeCollector,
        private FieldUsageResolverInterface $fieldUsageResolver,
        private SqlUsageCounterInterface $sqlUsageCounter,
        private HeatmapHydratorInterface $heatmapHydrator,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function analyze(string $classId, ?callable $onProgress = null): AttributeHeatmapResult
    {
        $definition = $this->classDefinitionResolver->getById($classId);

        if ($definition === null) {
            throw new NotFoundException('class', $classId);
        }

        $descriptors = $this->attributeCollector->collect($definition);
        $this->emitProgress($onProgress, 5, 'collect');

        $totalCount = $this->countObjects($classId);
        $this->emitProgress($onProgress, 8, 'count');

        $usedCounts = $this->collectUsageCounts($classId, $definition, $descriptors, $totalCount, $onProgress);
        $this->emitProgress($onProgress, 94, 'hydrate');

        $classInfo = $this->heatmapHydrator->hydrateClassInfo(
            classId: $classId,
            className: $definition->getName(),
            objectCount: $totalCount,
        );

        $result = $this->heatmapHydrator->hydrate(
            classInfo: $classInfo,
            descriptors: $descriptors,
            usedCounts: $usedCounts,
            totalCount: $totalCount,
        );

        $this->eventDispatcher->dispatch(
            new AttributeHeatmapResultEvent($result),
            AttributeHeatmapResultEvent::EVENT_NAME,
        );

        $this->emitProgress($onProgress, 100, 'done');

        return $result;
    }

    /**
     * @param callable(int $percent, string $phase): void|null $onProgress
     */
    private function emitProgress(?callable $onProgress, int $percent, string $phase): void
    {
        $onProgress?->__invoke($percent, $phase);
    }

    private function countObjects(string $classId): int
    {
        $listing = $this->dataObjectResolver->getList();
        $listing->setCondition('classId = ?', [$classId]);
        $listing->setUnpublished(true);
        $listing->setObjectTypes(['object', 'variant']);

        return $listing->count();
    }

    /**
     * @param array<int, AttributeDescriptor> $descriptors
     * @param callable(int $percent, string $phase): void|null $onProgress
     *
     * @return array<int, int>
     */
    private function collectUsageCounts(
        string $classId,
        ClassDefinition $definition,
        array $descriptors,
        int $totalCount,
        ?callable $onProgress = null,
    ): array {
        if ($totalCount === 0) {
            return array_fill(0, count($descriptors), 0);
        }

        $usageResult = $this->sqlUsageCounter->count($classId, $definition, $descriptors);
        $usedCounts = $usageResult->getUsedCounts();

        $remainingIndexes = $usageResult->getRemainingIndexes();

        if ($remainingIndexes === []) {
            $this->emitProgress($onProgress, 92, 'objects');

            return $usedCounts;
        }

        $pending = array_fill_keys($remainingIndexes, true);

        $previousInheritedValues = $this->dataObjectResolver->getGetInheritedValues();
        $this->dataObjectResolver->setGetInheritedValues(false);

        try {
            $offset = 0;

            while ($offset < $totalCount) {
                $listing = $this->dataObjectResolver->getList();
                $listing->setCondition('classId = ?', [$classId]);
                $listing->setUnpublished(true);
                $listing->setObjectTypes(['object', 'variant']);
                $listing->setLimit(self::BATCH_SIZE);
                $listing->setOffset($offset);

                $objects = $listing->getObjects();

                if ($objects === []) {
                    break;
                }

                foreach ($objects as $object) {
                    if (!$object instanceof Concrete) {
                        continue;
                    }

                    foreach ($descriptors as $index => $descriptor) {
                        if (!isset($pending[$index])) {
                            continue;
                        }

                        if (!$descriptor->getAnalyzable() || $descriptor->getValueProvider() === null) {
                            continue;
                        }

                        foreach ($descriptor->getValueProvider()->getValues($object) as $value) {
                            if ($this->fieldUsageResolver->isUsed($value)) {
                                $usedCounts[$index]++;
                                break;
                            }
                        }
                    }
                }

                $offset += self::BATCH_SIZE;

                $processed = min($offset, $totalCount);
                $percent = (int) round(12 + ($processed / $totalCount) * 80);
                $this->emitProgress($onProgress, min($percent, 92), 'objects');
            }
        } finally {
            $this->dataObjectResolver->setGetInheritedValues($previousInheritedValues);
        }

        return $usedCounts;
    }
}