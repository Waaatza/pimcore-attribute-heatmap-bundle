<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Model;

use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Value\ValueProviderInterface;

/**
 * @internal
 */
final readonly class AttributeDescriptor
{
    public function __construct(
        private string $name,
        private string $title,
        private string $fieldType,
        private string $group,
        private bool $analyzable = true,
        private ?ValueProviderInterface $valueProvider = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getFieldType(): string
    {
        return $this->fieldType;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getAnalyzable(): bool
    {
        return $this->analyzable;
    }

    public function getValueProvider(): ?ValueProviderInterface
    {
        return $this->valueProvider;
    }
}