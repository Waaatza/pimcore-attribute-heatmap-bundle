<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Attribute;

use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Model\AttributeDescriptor;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Value\BlockValueProvider;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Value\ClassificationstoreValueProvider;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Value\DirectValueProvider;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Value\FieldCollectionValueProvider;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Value\ObjectbrickValueProvider;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Value\ValueProviderInterface;
use Pimcore\Bundle\StaticResolverBundle\Lib\ToolResolverInterface;
use Pimcore\Bundle\StaticResolverBundle\Models\DataObject\ClassificationStore\GroupConfigResolverInterface;
use Pimcore\Bundle\StaticResolverBundle\Models\DataObject\ClassificationStore\KeyConfigResolverInterface;
use Pimcore\Bundle\StaticResolverBundle\Models\DataObject\ClassificationStore\ServiceResolverInterface;
use Pimcore\Bundle\StaticResolverBundle\Models\DataObject\FieldCollection\DefinitionResolverInterface as FieldCollectionDefinitionResolverInterface;
use Pimcore\Bundle\StaticResolverBundle\Models\DataObject\Objectbrick\DefinitionResolverInterface as ObjectbrickDefinitionResolverInterface;
use Pimcore\Bundle\StudioBackendBundle\Exception\Api\EnvironmentException;
use Pimcore\Model\DataObject\Classificationstore\GroupConfig;
use Pimcore\Model\DataObject\Classificationstore\GroupConfig\Listing as GroupConfigListing;
use Pimcore\Model\DataObject\Classificationstore\KeyGroupRelation;
use Pimcore\Model\DataObject\Classificationstore\KeyGroupRelation\Listing as KeyGroupRelationListing;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\Layout;
use Pimcore\Model\DataObject\ClassDefinition\Data\Block;
use Pimcore\Model\DataObject\ClassDefinition\Data\Classificationstore;
use Pimcore\Model\DataObject\ClassDefinition\Data\Fieldcollections;
use Pimcore\Model\DataObject\ClassDefinition\Data\Localizedfields;
use Pimcore\Model\DataObject\ClassDefinition\Data\Objectbricks;
use Throwable;

final readonly class AttributeCollector implements AttributeCollectorInterface
{
    private const NOT_ANALYZABLE_FIELD_TYPES = [
        'password',
        'reverseObjectRelation',
    ];

    public function __construct(
        private FieldCollectionDefinitionResolverInterface $fieldCollectionDefinitionResolver,
        private ObjectbrickDefinitionResolverInterface $objectbrickDefinitionResolver,
        private GroupConfigResolverInterface $groupConfigResolver,
        private KeyConfigResolverInterface $keyConfigResolver,
        private ServiceResolverInterface $classificationStoreServiceResolver,
        private ToolResolverInterface $toolResolver,
    ) {
    }

    public function collect(ClassDefinition $definition): array
    {
        $languages = $this->toolResolver->getValidLanguages();
        $descriptors = [];

        foreach ($definition->getFieldDefinitions() as $field) {
            if ($field->getName() === 'id') {
                continue;
            }

            $this->collectField($field, $languages, $descriptors);
        }

        return array_values($descriptors);
    }

    private function collectField(Data $field, array $languages, array &$descriptors): void
    {
        switch (true) {
            case $field instanceof Localizedfields:
                $this->collectLocalizedFields($field, $languages, $descriptors);

                break;
            case $field instanceof Objectbricks:
                $this->collectObjectBricks($field, $languages, $descriptors);

                break;
            case $field instanceof Fieldcollections:
                $this->collectFieldCollections($field, $languages, $descriptors);

                break;
            case $field instanceof Classificationstore:
                $this->collectClassificationStore($field, $descriptors);

                break;
            case $field instanceof Block:
                $this->collectBlock($field, $descriptors);

                break;
            default:
                $this->addLeafDescriptor(
                    $descriptors,
                    name: $field->getName(),
                    title: $field->getTitle() ?: $field->getName(),
                    fieldType: $field->getFieldtype(),
                    group: 'General',
                    provider: new DirectValueProvider($field->getName()),
                );

                break;
        }
    }

    private function collectLocalizedFields(Localizedfields $field, array $languages, array &$descriptors): void
    {
        foreach ($languages as $language) {
            $group = 'Localizedfields (' . $language . ')';

            foreach ($this->resolveDataChildren($field->getChildren()) as $fieldChild) {
                $this->addLeafDescriptor(
                    $descriptors,
                    name: $fieldChild->getName() . '_' . mb_strtolower($language),
                    title: $fieldChild->getTitle() ?: $fieldChild->getName(),
                    fieldType: $fieldChild->getFieldtype(),
                    group: $group,
                    provider: new DirectValueProvider($fieldChild->getName(), $language),
                );
            }
        }
    }

    private function collectObjectBricks(Objectbricks $field, array $languages, array &$descriptors): void
    {
        foreach ($field->getAllowedTypes() as $brickType) {
            $brickDefinition = $this->objectbrickDefinitionResolver->getByKey($brickType);

            if ($brickDefinition === null) {
                continue;
            }

            $group = 'Objectbrick: ' . $brickType;

            foreach ($brickDefinition->getFieldDefinitions() as $brickField) {
                if ($brickField instanceof Localizedfields) {
                    foreach ($languages as $language) {
                        foreach ($brickField->getChildren() as $localizedChild) {
                            $this->addLeafDescriptor(
                                $descriptors,
                                name: $field->getName() . '_' . $brickType . '_'
                                    . $localizedChild->getName() . '_' . mb_strtolower($language),
                                title: $localizedChild->getTitle() ?: $localizedChild->getName(),
                                fieldType: $localizedChild->getFieldtype(),
                                group: $group,
                                provider: new ObjectbrickValueProvider(
                                    $field->getName(),
                                    $brickType,
                                    $localizedChild->getName(),
                                    $language,
                                ),
                            );
                        }
                    }

                    continue;
                }

                $this->addLeafDescriptor(
                    $descriptors,
                    name: $field->getName() . '_' . $brickType . '_' . $brickField->getName(),
                    title: $brickField->getTitle() ?: $brickField->getName(),
                    fieldType: $brickField->getFieldtype() === 'block' ? 'block' : $brickField->getFieldtype(),
                    group: $group,
                    provider: new ObjectbrickValueProvider(
                        $field->getName(),
                        $brickType,
                        $brickField->getName(),
                    ),
                );
            }
        }
    }

    private function collectFieldCollections(Fieldcollections $field, array $languages, array &$descriptors): void
    {
        foreach ($field->getAllowedTypes() as $collectionType) {
            $collectionDefinition = $this->fieldCollectionDefinitionResolver->getByKey($collectionType);

            if ($collectionDefinition === null) {
                continue;
            }

            $group = 'Fieldcollection: ' . $collectionType;

            foreach ($collectionDefinition->getFieldDefinitions() as $collectionField) {
                if ($collectionField instanceof Localizedfields) {
                    foreach ($languages as $language) {
                        foreach ($collectionField->getChildren() as $localizedChild) {
                            $this->addLeafDescriptor(
                                $descriptors,
                                name: $field->getName() . '_' . $collectionType . '_'
                                    . $localizedChild->getName() . '_' . mb_strtolower($language),
                                title: $localizedChild->getTitle() ?: $localizedChild->getName(),
                                fieldType: $localizedChild->getFieldtype(),
                                group: $group,
                                provider: new FieldCollectionValueProvider(
                                    $field->getName(),
                                    $collectionType,
                                    $localizedChild->getName(),
                                    $language,
                                ),
                            );
                        }
                    }

                    continue;
                }

                $this->addLeafDescriptor(
                    $descriptors,
                    name: $field->getName() . '_' . $collectionType . '_' . $collectionField->getName(),
                    title: $collectionField->getTitle() ?: $collectionField->getName(),
                    fieldType: $collectionField->getFieldtype() === 'block' ? 'block' : $collectionField->getFieldtype(),
                    group: $group,
                    provider: new FieldCollectionValueProvider(
                        $field->getName(),
                        $collectionType,
                        $collectionField->getName(),
                    ),
                );
            }
        }
    }

    private function collectClassificationStore(Classificationstore $field, array &$descriptors): void
    {
        $storeId = $field->getStoreId() ?: 1;

        foreach ($this->getStoreGroups($storeId) as $group) {
            if (!$group instanceof GroupConfig) {
                continue;
            }

            $groupLabel = 'Classificationstore: ' . ($group->getName() ?: (string) $group->getId());

            foreach ($this->getGroupKeyRelations($group->getId()) as $relation) {
                if (!$relation instanceof KeyGroupRelation || !$relation->isEnabled()) {
                    continue;
                }

                $keyConfig = $this->keyConfigResolver->getById($relation->getKeyId());

                if ($keyConfig === null) {
                    continue;
                }

                $fieldDefinition = $this->classificationStoreServiceResolver->getFieldDefinitionFromKeyConfig($keyConfig);

                $this->addLeafDescriptor(
                    $descriptors,
                    name: $field->getName() . '_' . $group->getId() . '_' . $relation->getKeyId(),
                    title: $keyConfig->getName() ?: (string) $keyConfig->getId(),
                    fieldType: $fieldDefinition?->getFieldtype() ?? 'classificationstore',
                    group: $groupLabel,
                    provider: new ClassificationstoreValueProvider(
                        $field->getName(),
                        $group->getId(),
                        $relation->getKeyId(),
                    ),
                );
            }
        }
    }

    private function collectBlock(Block $field, array &$descriptors): void
    {
        $group = 'Block: ' . $field->getName();

        foreach ($this->resolveDataChildren($field->getChildren()) as $blockFieldChild) {
            $this->addLeafDescriptor(
                $descriptors,
                name: $field->getName() . '_' . $blockFieldChild->getName(),
                title: $blockFieldChild->getTitle() ?: $blockFieldChild->getName(),
                fieldType: $blockFieldChild instanceof Localizedfields ? 'block' : $blockFieldChild->getFieldtype(),
                group: $group,
                provider: new BlockValueProvider($field->getName(), $blockFieldChild->getName()),
            );
        }
    }

    /**
     * Resolves container children into data fields, descending into nested layout
     * elements (e.g. Fieldset) which group fields visually.
     *
     * @param array<int, mixed> $children
     *
     * @return array<int, Data>
     */
    private function resolveDataChildren(array $children): array
    {
        $resolved = [];

        foreach ($children as $child) {
            if ($child instanceof Data) {
                $resolved[] = $child;

                continue;
            }

            if ($child instanceof Layout) {
                $resolved = [...$resolved, ...$this->resolveDataChildren($child->getChildren())];
            }
        }

        return $resolved;
    }

    /**
     * @param array<int, AttributeDescriptor> $descriptors
     */
    private function addLeafDescriptor(
        array &$descriptors,
        string $name,
        string $title,
        string $fieldType,
        string $group,
        ?ValueProviderInterface $provider,
    ): void {
        $analyzable = $provider !== null
            && !in_array($fieldType, self::NOT_ANALYZABLE_FIELD_TYPES, true);

        $descriptors[] = new AttributeDescriptor(
            name: $name,
            title: $title,
            fieldType: $fieldType,
            group: $group,
            analyzable: $analyzable,
            valueProvider: $analyzable ? $provider : null,
        );
    }

    /**
     * @return GroupConfig[]
     */
    private function getStoreGroups(int $storeId): array
    {
        try {
            $listing = new GroupConfigListing();
            $listing->setCondition('storeId = ?', [$storeId]);

            return $listing->load();
        } catch (Throwable) {
            throw new EnvironmentException('Could not load classification store groups.');
        }
    }

    /**
     * @return KeyGroupRelation[]
     */
    private function getGroupKeyRelations(int $groupId): array
    {
        try {
            $listing = new KeyGroupRelationListing();
            $listing->setCondition('groupId = ?', [$groupId]);

            return $listing->load();
        } catch (Throwable) {
            throw new EnvironmentException('Could not load classification store key relations.');
        }
    }
}
