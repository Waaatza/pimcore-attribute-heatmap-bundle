<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Service\Studio\Classes;

use Watza\AttributeHeatmapBundle\Event\Studio\PreResponse\ClassListEvent;
use Watza\AttributeHeatmapBundle\Hydrator\Studio\Heatmap\HeatmapHydratorInterface;
use Watza\AttributeHeatmapBundle\Schema\Heatmap\ClassItemCollection;
use Pimcore\Bundle\StaticResolverBundle\Models\DataObject\DataObjectResolverInterface;
use Pimcore\Bundle\StudioBackendBundle\Exception\Api\EnvironmentException;
use Pimcore\Model\DataObject\ClassDefinition;
use Throwable;
use function count;

final readonly class ClassService implements ClassServiceInterface
{
    public function __construct(
        private DataObjectResolverInterface $dataObjectResolver,
        private HeatmapHydratorInterface $hydrator,
        private \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function getClasses(): ClassItemCollection
    {
        $items = [];

        foreach ($this->loadClassDefinitions() as $definition) {
            $items[] = $this->hydrator->hydrateClassListItem(
                classId: $definition->getId(),
                className: $definition->getName(),
                objectCount: $this->countObjects($definition),
            );
        }

        $collection = new ClassItemCollection(
            items: $items,
            totalItems: count($items),
        );

        $this->eventDispatcher->dispatch(
            new ClassListEvent($collection),
            ClassListEvent::EVENT_NAME,
        );

        return $collection;
    }

    /**
     * @return ClassDefinition[]
     */
    private function loadClassDefinitions(): array
    {
        try {
            $listing = new ClassDefinition\Listing();
            $listing->setOrderKey('name');
            $listing->setOrder('ASC');

            return $listing->load();
        } catch (Throwable) {
            throw new EnvironmentException('Could not load data object classes.');
        }
    }

    private function countObjects(ClassDefinition $definition): int
    {
        $listing = $this->dataObjectResolver->getList();
        $listing->setCondition('classId = ?', [$definition->getId()]);
        $listing->setUnpublished(true);
        $listing->setObjectTypes(['object', 'variant']);

        return $listing->count();
    }
}
