<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Value;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection;

final readonly class FieldCollectionValueProvider implements ValueProviderInterface
{
    public function __construct(
        private string $containerFieldName,
        private string $collectionType,
        private string $fieldName,
        private ?string $language = null,
    ) {
    }

    public function getValues(Concrete $object): iterable
    {
        $container = $object->get($this->containerFieldName);

        if (!$container instanceof Fieldcollection) {
            return;
        }

        foreach ($container->getItems() as $item) {
            if ($item->getType() !== $this->collectionType) {
                continue;
            }

            yield $item->get($this->fieldName, $this->language);
        }
    }

    public function getContainerFieldName(): string
    {
        return $this->containerFieldName;
    }

    public function getCollectionType(): string
    {
        return $this->collectionType;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }
}