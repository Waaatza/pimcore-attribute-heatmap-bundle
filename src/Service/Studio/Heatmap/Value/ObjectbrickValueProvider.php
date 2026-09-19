<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Value;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Objectbrick;

final readonly class ObjectbrickValueProvider implements ValueProviderInterface
{
    public function __construct(
        private string $containerFieldName,
        private string $brickType,
        private string $fieldName,
        private ?string $language = null,
    ) {
    }

    public function getValues(Concrete $object): iterable
    {
        $container = $object->get($this->containerFieldName);

        if (!$container instanceof Objectbrick) {
            return;
        }

        foreach ($container->getItems() as $item) {
            if ($item->getType() !== $this->brickType) {
                continue;
            }

            yield $item->get($this->fieldName, $this->language);
        }
    }

    public function getContainerFieldName(): string
    {
        return $this->containerFieldName;
    }

    public function getBrickType(): string
    {
        return $this->brickType;
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