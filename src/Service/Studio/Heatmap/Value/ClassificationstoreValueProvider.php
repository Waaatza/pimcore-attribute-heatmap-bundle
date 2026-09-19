<?php
declare(strict_types=1);

namespace Pimcore\Bundle\AttributeHeatmapBundle\Service\Studio\Heatmap\Value;

use Pimcore\Model\DataObject\Classificationstore;
use Pimcore\Model\DataObject\Concrete;

final readonly class ClassificationstoreValueProvider implements ValueProviderInterface
{
    public function __construct(
        private string $containerFieldName,
        private int $groupId,
        private int $keyId,
        private ?string $language = null,
    ) {
    }

    public function getValues(Concrete $object): iterable
    {
        $container = $object->get($this->containerFieldName);

        if (!$container instanceof Classificationstore) {
            return;
        }

        yield $container->getLocalizedKeyValue($this->groupId, $this->keyId, $this->language ?? 'default');
    }
}