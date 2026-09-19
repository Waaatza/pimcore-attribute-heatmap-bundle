<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Value;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\BlockElement;

final readonly class BlockValueProvider implements ValueProviderInterface
{
    public function __construct(
        private string $containerFieldName,
        private string $fieldName,
    ) {
    }

    public function getValues(Concrete $object): iterable
    {
        $data = $object->get($this->containerFieldName);

        if (!is_array($data)) {
            return;
        }

        foreach ($data as $blockElements) {
            if (!is_array($blockElements)) {
                continue;
            }

            foreach ($blockElements as $blockElement) {
                if (!$blockElement instanceof BlockElement) {
                    continue;
                }

                if ($blockElement->getName() !== $this->fieldName) {
                    continue;
                }

                yield $blockElement->getData();
            }
        }
    }

    public function getContainerFieldName(): string
    {
        return $this->containerFieldName;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}