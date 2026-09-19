<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Value;

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

    public function getContainerFieldName(): string
    {
        return $this->containerFieldName;
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function getKeyId(): int
    {
        return $this->keyId;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }
}