<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Value;

use Pimcore\Model\DataObject\Concrete;

final readonly class DirectValueProvider implements ValueProviderInterface
{
    public function __construct(
        private string $fieldName,
        private ?string $language = null,
    ) {
    }

    public function getValues(Concrete $object): iterable
    {
        yield $object->get($this->fieldName, $this->language);
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