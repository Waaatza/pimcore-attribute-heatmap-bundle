<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Service\Studio\Heatmap;

use Watza\AttributeHeatmapBundle\Event\Studio\PreResponse\AttributeHeatmapResultEvent;
use Watza\AttributeHeatmapBundle\Hydrator\Studio\Heatmap\HeatmapHydratorInterface;
use Watza\AttributeHeatmapBundle\Schema\Heatmap\AttributeHeatmapResult;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Attribute\AttributeCollectorInterface;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Model\AttributeDescriptor;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Usage\FieldUsageResolverInterface;
use Pimcore\Bundle\StaticResolverBundle\Models\DataObject\ClassDefinitionResolverInterface;
use Pimcore\Bundle\StaticResolverBundle\Models\DataObject\DataObjectResolverInterface;
use Pimcore\Bundle\StudioBackendBundle\Exception\Api\NotFoundException;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final readonly class HeatmapService implements HeatmapServiceInterface
{
    private const int BATCH_SIZE = 200;

    public function __construct(
        private ClassDefinitionResolverInterface $classDefinitionResolver,
        private DataObjectResolverInterface $dataObjectResolver,
        private AttributeCollectorInterface $attributeCollector,
        private FieldUsageResolverInterface $fieldUsageResolver,
        private HeatmapHydratorInterface $heatmapHydrator,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function analyze(string $classId): AttributeHeatmapResult
    {
        $definition = $this->classDefinitionResolver->getById($classId);

        if ($definition === null) {
            throw new NotFoundException('class', $classId);
        }

        $descriptors = $this->attributeCollector->collect($definition);
        $totalCount = $this->countObjects($classId);

        $usedCounts = $this->collectUsageCounts($classId, $descriptors, $totalCount);

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

        return $result;
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
     *
     * @return array<int, int>
     */
    private function collectUsageCounts(string $classId, array $descriptors, int $totalCount): array
    {
        $usedCounts = array_fill(0, count($descriptors), 0);

        if ($totalCount === 0) {
            return $usedCounts;
        }

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
            }
        } finally {
            $this->dataObjectResolver->setGetInheritedValues($previousInheritedValues);
        }

        return $usedCounts;
    }
}