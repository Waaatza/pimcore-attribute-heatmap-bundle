<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Usage;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use Pimcore\Model\DataObject\ClassDefinition;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Model\AttributeDescriptor;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Value\BlockValueProvider;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Value\ClassificationstoreValueProvider;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Value\DirectValueProvider;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Value\FieldCollectionValueProvider;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Value\ObjectbrickValueProvider;

/**
 * Counts "attribute used" occurrences directly on the class data tables via SQL
 * aggregates. This avoids loading and hydrating every single object, which is
 * orders of magnitude faster for large classes. Besides plain columns, localized
 * fields, relations and container types (object bricks, field collections,
 * classification store, blocks) are counted on their dedicated tables. Only
 * descriptors that cannot be mapped to a column of the resolved tables fall back
 * to the object-based loop.
 *
 * @internal
 */
final readonly class SqlUsageCounter implements SqlUsageCounterInterface
{
    private const string DATA_TABLE_PREFIX = 'object_store_';

    private const string DATA_TABLE_LEGACY_PREFIX = 'object_';

    private const string LOCALIZED_TABLE_PREFIX = 'object_localized_data_';

    private const string LOCALIZED_TABLE_LEGACY_PREFIX = 'object_localized_';

    private const string RELATIONS_TABLE_PREFIX = 'object_relations_';

    private const string CLASSIFICATION_STORE_TABLE_PREFIX = 'object_classificationstore_data_';

    private const string BLOCK_FIELD_TYPE = 'block';

    private const array RELATION_FIELD_TYPES = [
        'manyToManyRelation',
        'advancedManyToManyRelation',
        'manyToManyObjectRelation',
        'advancedManyToManyObjectRelation',
        'manyToManyAssetRelation',
        'advancedManyToManyAssetRelation',
        'manyToManyDocumentRelation',
        'advancedManyToManyDocumentRelation',
    ];

    private const array CHECKBOX_FIELD_TYPES = [
        'checkbox',
        'booleanSelect',
    ];

    private const array NUMERIC_FIELD_TYPES = [
        'numeric',
        'slider',
        'quantityValue',
        'inputQuantityValue',
        'calculator',
        'rgbaColor',
        'time',
    ];

    public function __construct(private readonly Connection $connection)
    {
    }

    public function count(string $classId, ClassDefinition $definition, array $descriptors): SqlUsageResult
    {
        $usedCounts = array_fill(0, count($descriptors), 0);
        $remainingIndexes = [];

        $dataTable = $this->resolveTable($classId, self::DATA_TABLE_PREFIX, self::DATA_TABLE_LEGACY_PREFIX);
        $localizedTable = $this->resolveTable($classId, self::LOCALIZED_TABLE_PREFIX, self::LOCALIZED_TABLE_LEGACY_PREFIX);

        $columnsByTable = [];
        $brickStoreTables = [];
        $brickLocalizedTables = [];
        $collectionTables = [];
        $collectionLocalizedTables = [];

        $directCounts = [];
        $localizedCounts = [];
        $relationCounts = [];
        $blockCounts = [];
        $localizedBlockCounts = [];
        $brickCounts = [];
        $brickLocalizedCounts = [];
        $collectionCounts = [];
        $collectionLocalizedCounts = [];
        $classificationCounts = [];

        foreach ($descriptors as $index => $descriptor) {
            $provider = $descriptor->getValueProvider();

            if (!$descriptor->getAnalyzable() || $provider === null) {
                continue;
            }

            if ($provider instanceof DirectValueProvider) {
                $this->collectDirect(
                    $descriptor,
                    $provider,
                    $index,
                    $dataTable,
                    $localizedTable,
                    $columnsByTable,
                    $directCounts,
                    $localizedCounts,
                    $relationCounts,
                    $remainingIndexes,
                );

                continue;
            }

            if ($provider instanceof ObjectbrickValueProvider) {
                $this->collectContainer(
                    type: $provider->getBrickType(),
                    providerLanguage: $provider->getLanguage(),
                    providerFieldName: $provider->getFieldName(),
                    fieldType: $descriptor->getFieldType(),
                    index: $index,
                    classId: $classId,
                    containerFieldName: $provider->getContainerFieldName(),
                    columnsByTable: $columnsByTable,
                    storeTables: $brickStoreTables,
                    localizedTables: $brickLocalizedTables,
                    storeCounts: $brickCounts,
                    localizedCounts: $brickLocalizedCounts,
                    storeResolver: fn (string $classId, string $type): ?string => $this->resolveBrickTable($classId, $type),
                    localizedResolver: fn (string $classId, string $type): ?string => $this->resolveBrickLocalizedTable($classId, $type),
                    remainingIndexes: $remainingIndexes,
                );

                continue;
            }

            if ($provider instanceof FieldCollectionValueProvider) {
                $this->collectContainer(
                    type: $provider->getCollectionType(),
                    providerLanguage: $provider->getLanguage(),
                    providerFieldName: $provider->getFieldName(),
                    fieldType: $descriptor->getFieldType(),
                    index: $index,
                    classId: $classId,
                    containerFieldName: $provider->getContainerFieldName(),
                    columnsByTable: $columnsByTable,
                    storeTables: $collectionTables,
                    localizedTables: $collectionLocalizedTables,
                    storeCounts: $collectionCounts,
                    localizedCounts: $collectionLocalizedCounts,
                    storeResolver: fn (string $classId, string $type): ?string => $this->resolveCollectionTable($classId, $type),
                    localizedResolver: fn (string $classId, string $type): ?string => $this->resolveCollectionLocalizedTable($classId, $type),
                    remainingIndexes: $remainingIndexes,
                );

                continue;
            }

            if ($provider instanceof ClassificationstoreValueProvider) {
                $this->collectClassificationStore(
                    $descriptor,
                    $provider,
                    $index,
                    $classId,
                    $columnsByTable,
                    $classificationCounts,
                    $remainingIndexes,
                );

                continue;
            }

            if ($provider instanceof BlockValueProvider) {
                $this->collectBlock(
                    $descriptor,
                    $provider,
                    $index,
                    $dataTable,
                    $localizedTable,
                    $columnsByTable,
                    $blockCounts,
                    $localizedBlockCounts,
                    $remainingIndexes,
                );
            }
        }

        if ($dataTable !== null) {
            $this->countDirect($dataTable, $directCounts, $usedCounts);
            $this->countBlocks($dataTable, $blockCounts, false, $usedCounts);
        }

        if ($localizedTable !== null) {
            $this->countLocalized($localizedTable, $localizedCounts, $usedCounts);
            $this->countBlocks($localizedTable, $localizedBlockCounts, true, $usedCounts);
        }

        $relationsTable = $this->resolveTable($classId, self::RELATIONS_TABLE_PREFIX, self::RELATIONS_TABLE_PREFIX);

        if ($relationsTable !== null) {
            $this->countRelations($relationsTable, $relationCounts, $usedCounts);
        }

        foreach ($brickCounts as $brickType => $brickCount) {
            $this->countContainer($brickStoreTables[$brickType] ?? null, $brickCount, $usedCounts);
        }

        foreach ($brickLocalizedCounts as $brickType => $brickCount) {
            $this->countContainer($brickLocalizedTables[$brickType] ?? null, $brickCount, $usedCounts);
        }

        foreach ($collectionCounts as $collectionType => $collectionCount) {
            $this->countContainer($collectionTables[$collectionType] ?? null, $collectionCount, $usedCounts);
        }

        foreach ($collectionLocalizedCounts as $collectionType => $collectionCount) {
            $this->countContainer($collectionLocalizedTables[$collectionType] ?? null, $collectionCount, $usedCounts);
        }

        if ($classificationCounts !== []) {
            $this->countContainer($this->classificationStoreTable($classId), $classificationCounts, $usedCounts);
        }

        return new SqlUsageResult($usedCounts, $remainingIndexes);
    }

    /**
     * @param array<string, array<int, string>> $columnsByTable
     * @param array<int, array{index: int, predicate: string}> $directCounts
     * @param array<int, array{index: int, predicate: string, language: string}> $localizedCounts
     * @param array<int, array{index: int, fieldname: string}> $relationCounts
     * @param array<int, int> $remainingIndexes
     */
    private function collectDirect(
        AttributeDescriptor $descriptor,
        DirectValueProvider $provider,
        int $index,
        ?string $dataTable,
        ?string $localizedTable,
        array &$columnsByTable,
        array &$directCounts,
        array &$localizedCounts,
        array &$relationCounts,
        array &$remainingIndexes,
    ): void {
        $fieldType = $descriptor->getFieldType();
        $language = $provider->getLanguage();

        if ($language === null && in_array($fieldType, self::RELATION_FIELD_TYPES, true)) {
            $relationCounts[] = [
                'index' => $index,
                'fieldname' => $provider->getFieldName(),
            ];

            return;
        }

        $table = $language !== null ? $localizedTable : $dataTable;

        if ($table === null) {
            $remainingIndexes[] = $index;

            return;
        }

        $column = $this->findColumn($columnsByTable[$table] ??= $this->getColumns($table), $provider->getFieldName());

        if ($column === null) {
            $remainingIndexes[] = $index;

            return;
        }

        $count = [
            'index' => $index,
            'predicate' => $this->buildPredicate($column, $fieldType),
        ];

        if ($language !== null) {
            $localizedCounts[] = [...$count, 'language' => $language];

            return;
        }

        $directCounts[] = $count;
    }

    /**
     * @param array<string, array<int, string>> $columnsByTable
     * @param array<string, string|null> $storeTables
     * @param array<string, string|null> $localizedTables
     * @param array<string, array<int, array{index: int, condition: string, params: array<int, mixed>}>> $storeCounts
     * @param array<string, array<int, array{index: int, condition: string, params: array<int, mixed>}>> $localizedCounts
     * @param callable(string, string): ?string $storeResolver
     * @param callable(string, string): ?string $localizedResolver
     * @param array<int, int> $remainingIndexes
     */
    private function collectContainer(
        string $type,
        ?string $providerLanguage,
        string $providerFieldName,
        string $fieldType,
        int $index,
        string $classId,
        string $containerFieldName,
        array &$columnsByTable,
        array &$storeTables,
        array &$localizedTables,
        array &$storeCounts,
        array &$localizedCounts,
        callable $storeResolver,
        callable $localizedResolver,
        array &$remainingIndexes,
    ): void {
        if ($providerLanguage !== null) {
            if (!array_key_exists($type, $localizedTables)) {
                $localizedTables[$type] = $localizedResolver($classId, $type);
            }

            $table = $localizedTables[$type];

            if ($table === null) {
                return;
            }

            $column = $this->findColumn($columnsByTable[$table] ??= $this->getColumns($table), $providerFieldName);

            if ($column === null) {
                $remainingIndexes[] = $index;

                return;
            }

            $localizedCounts[$type][] = $this->buildContainerCondition(
                $index,
                $containerFieldName,
                $this->buildContainerPredicate($column, $fieldType),
                [['column' => 'language', 'value' => $providerLanguage]],
            );

            return;
        }

        if (!array_key_exists($type, $storeTables)) {
            $storeTables[$type] = $storeResolver($classId, $type);
        }

        $table = $storeTables[$type];

        if ($table === null) {
            return;
        }

        $column = $this->findColumn($columnsByTable[$table] ??= $this->getColumns($table), $providerFieldName);

        if ($column === null) {
            $remainingIndexes[] = $index;

            return;
        }

        $storeCounts[$type][] = $this->buildContainerCondition(
            $index,
            $containerFieldName,
            $this->buildContainerPredicate($column, $fieldType),
        );
    }

    /**
     * @param array<string, array<int, string>> $columnsByTable
     * @param array<int, array{index: int, condition: string, params: array<int, mixed>}> $classificationCounts
     * @param array<int, int> $remainingIndexes
     */
    private function collectClassificationStore(
        AttributeDescriptor $descriptor,
        ClassificationstoreValueProvider $provider,
        int $index,
        string $classId,
        array &$columnsByTable,
        array &$classificationCounts,
        array &$remainingIndexes,
    ): void {
        $table = $this->classificationStoreTable($classId);

        if ($table === null) {
            return;
        }

        $valueColumn = $this->findColumn($columnsByTable[$table] ??= $this->getColumns($table), 'value');

        if ($valueColumn === null) {
            $remainingIndexes[] = $index;

            return;
        }

        $classificationCounts[] = $this->buildContainerCondition(
            $index,
            $provider->getContainerFieldName(),
            $this->buildPredicate($valueColumn, $descriptor->getFieldType()),
            [
                ['column' => 'groupId', 'value' => $provider->getGroupId()],
                ['column' => 'keyId', 'value' => $provider->getKeyId()],
                ['column' => 'language', 'value' => $provider->getLanguage() ?? 'default'],
            ],
        );
    }

    /**
     * A block child that is a Localizedfields is serialized into the localized
     * data table, all other block children live in the data table. Both are one
     * PHP-serialized blob column named after the block container field.
     *
     * @param array<string, array<int, string>> $columnsByTable
     * @param array<int, array{index: int, container: string, child: string}> $blockCounts
     * @param array<int, array{index: int, container: string, child: string}> $localizedBlockCounts
     * @param array<int, int> $remainingIndexes
     */
    private function collectBlock(
        AttributeDescriptor $descriptor,
        BlockValueProvider $provider,
        int $index,
        ?string $dataTable,
        ?string $localizedTable,
        array &$columnsByTable,
        array &$blockCounts,
        array &$localizedBlockCounts,
        array &$remainingIndexes,
    ): void {
        if ($descriptor->getFieldType() === self::BLOCK_FIELD_TYPE) {
            if ($localizedTable === null
                || $this->findColumn($columnsByTable[$localizedTable] ??= $this->getColumns($localizedTable), $provider->getContainerFieldName()) === null) {
                $remainingIndexes[] = $index;

                return;
            }

            $localizedBlockCounts[] = [
                'index' => $index,
                'container' => $provider->getContainerFieldName(),
                'child' => $provider->getFieldName(),
            ];

            return;
        }

        if ($dataTable === null
            || $this->findColumn($columnsByTable[$dataTable] ??= $this->getColumns($dataTable), $provider->getContainerFieldName()) === null) {
            $remainingIndexes[] = $index;

            return;
        }

        $blockCounts[] = [
            'index' => $index,
            'container' => $provider->getContainerFieldName(),
            'child' => $provider->getFieldName(),
        ];
    }

    /**
     * @param array<int, array{index: int, predicate: string}> $counts
     * @param array<int, int> $usedCounts
     */
    private function countDirect(string $table, array $counts, array &$usedCounts): void
    {
        if ($counts === []) {
            return;
        }

        $selects = array_map(
            static fn (array $count): string => 'SUM(CASE WHEN ' . $count['predicate'] . ' THEN 1 ELSE 0 END) AS c' . $count['index'],
            $counts,
        );

        $sql = 'SELECT ' . implode(', ', $selects) . ' FROM ' . $this->connection->quoteIdentifier($table);
        $row = $this->connection->executeQuery($sql)->fetchAssociative();

        if ($row === false) {
            return;
        }

        foreach ($counts as $count) {
            $usedCounts[$count['index']] = (int) ($row['c' . $count['index']] ?? 0);
        }
    }

    /**
     * @param array<int, array{index: int, predicate: string, language: string}> $counts
     * @param array<int, int> $usedCounts
     */
    private function countLocalized(string $table, array $counts, array &$usedCounts): void
    {
        if ($counts === []) {
            return;
        }

        $selects = array_map(
            static fn (array $count): string => 'SUM(CASE WHEN ' . $count['predicate'] . ' THEN 1 ELSE 0 END) AS c' . $count['index'],
            $counts,
        );

        $sql = 'SELECT language, ' . implode(', ', $selects)
            . ' FROM ' . $this->connection->quoteIdentifier($table)
            . ' GROUP BY language';

        $rows = $this->connection->executeQuery($sql)->fetchAllAssociative();

        foreach ($rows as $row) {
            foreach ($counts as $count) {
                if ($count['language'] === $row['language']) {
                    $usedCounts[$count['index']] = (int) ($row['c' . $count['index']] ?? 0);
                }
            }
        }
    }

    /**
     * @param array<int, array{index: int, fieldname: string}> $counts
     * @param array<int, int> $usedCounts
     */
    private function countRelations(string $table, array $counts, array &$usedCounts): void
    {
        if ($counts === []) {
            return;
        }

        $placeholders = implode(', ', array_fill(0, count($counts), '?'));
        $sql = 'SELECT fieldname, COUNT(DISTINCT src_id) AS used FROM '
            . $this->connection->quoteIdentifier($table)
            . " WHERE ownertype = 'object' AND fieldname IN (" . $placeholders . ') GROUP BY fieldname';

        $rows = $this->connection->executeQuery(
            $sql,
            array_map(static fn (array $count): string => $count['fieldname'], $counts),
        )->fetchAllAssociative();

        $usedByFieldname = [];

        foreach ($rows as $row) {
            $usedByFieldname[(string) $row['fieldname']] = (int) $row['used'];
        }

        foreach ($counts as $count) {
            $usedCounts[$count['index']] = $usedByFieldname[$count['fieldname']] ?? 0;
        }
    }

    /**
     * Counts distinct objects per descriptor on a container table (bricks, field
     * collections, classification store). Each count carries a fieldname filter
     * plus optional extra filters (language, group/key) and a value predicate.
     *
     * @param array<int, array{index: int, condition: string, params: array<int, mixed>}> $counts
     * @param array<int, int> $usedCounts
     */
    private function countContainer(?string $table, array $counts, array &$usedCounts): void
    {
        if ($table === null || $counts === []) {
            return;
        }

        $oidColumn = $this->objectIdColumn($table);

        if ($oidColumn === null) {
            return;
        }

        $selects = [];
        $params = [];
        $fieldnames = [];

        foreach ($counts as $count) {
            $selects[] = 'COUNT(DISTINCT CASE WHEN ' . $count['condition'] . ' THEN '
                . $this->connection->quoteIdentifier($oidColumn) . ' END) AS c' . $count['index'];
            $fieldnames[] = $count['params'][0];
            $params = [...$params, ...$count['params']];
        }

        $sql = 'SELECT ' . implode(', ', $selects) . ' FROM ' . $this->connection->quoteIdentifier($table);

        $fieldnames = array_values(array_unique($fieldnames));

        if (count($fieldnames) === 1) {
            $sql .= ' WHERE fieldname = ?';
            $params[] = $fieldnames[0];
        } elseif (count($fieldnames) > 1) {
            $placeholders = implode(', ', array_fill(0, count($fieldnames), '?'));
            $sql .= ' WHERE fieldname IN (' . $placeholders . ')';
            $params = [...$params, ...$fieldnames];
        }

        $row = $this->connection->executeQuery($sql, $params)->fetchAssociative();

        if ($row === false) {
            return;
        }

        foreach ($counts as $count) {
            $usedCounts[$count['index']] = (int) ($row['c' . $count['index']] ?? 0);
        }
    }

    /**
     * Block children are stored as one PHP-serialized blob per object, so the
     * blob column must be fetched for every matching object and parsed in PHP.
     * The container blob column provides a cheap pre-filter: objects where the
     * column is NULL or an empty blob can never contain a used child value and
     * are filtered away by the WHERE clause before reading blobs.
     *
     * @param array<int, array{index: int, container: string, child: string}> $counts
     * @param array<int, int> $usedCounts
     */
    private function countBlocks(string $table, array $counts, bool $localized, array &$usedCounts): void
    {
        if ($counts === []) {
            return;
        }

        $containers = [];

        foreach ($counts as $count) {
            $containers[$count['container']] ??= [];
            $containers[$count['container']][] = $count;
        }

        $oidColumn = $this->objectIdColumn($table);

        if ($oidColumn === null) {
            return;
        }

        foreach ($containers as $container => $containerCounts) {
            $sql = 'SELECT ' . $this->connection->quoteIdentifier($oidColumn)
                . ', ' . $this->connection->quoteIdentifier($container)
                . ' FROM ' . $this->connection->quoteIdentifier($table)
                . ' WHERE ' . $this->connection->quoteIdentifier($container) . ' IS NOT NULL'
                . ' AND ' . $this->connection->quoteIdentifier($container) . " != ''";

            if ($localized) {
                $sql .= " AND language != ''";
            }

            $rows = $this->connection->executeQuery($sql)->fetchAllAssociative();
            $childIndexes = [];

            foreach ($containerCounts as $count) {
                $childIndexes[(string) $count['child']] ??= $count['index'];
            }

            $usedObjects = [];

            foreach ($rows as $row) {
                $oid = (string) $row[$oidColumn];
                $blob = $row[$container];

                if (!is_string($blob)) {
                    continue;
                }

                $items = @unserialize($blob, ['allowed_classes' => false]);

                if (!is_array($items)) {
                    continue;
                }

                foreach ($childIndexes as $child => $index) {
                    if ($this->blockItemsContainUsedChild($items, $child)) {
                        $usedObjects[$index][$oid] = true;
                    }
                }
            }

            foreach ($childIndexes as $childIndex) {
                $usedCounts[$childIndex] = count($usedObjects[$childIndex] ?? []);
            }
        }
    }

    /**
     * A block blob is a serialized list of block items, each an associative
     * array of child field values. Localized children (Localizedfields inside a
     * block) store the per-language values as an array under the child key, so
     * arrays are checked recursively.
     */
    private function blockItemsContainUsedChild(array $items, string $child): bool
    {
        foreach ($items as $item) {
            if (!is_array($item) || !array_key_exists($child, $item)) {
                continue;
            }

            if ($this->valueIsUsed($item[$child])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Builds a `fieldname = ? AND <filterColumn> = ? ... AND <predicate>`
     * condition for a container descriptor. The leading container fieldname is
     * always the first parameter.
     *
     * @param array<int, array{column: string, value: mixed}> $filters
     * @return array{index: int, condition: string, params: array<int, mixed>}
     */
    private function buildContainerCondition(int $index, string $containerFieldName, string $predicate, array $filters = []): array
    {
        $conditions = ['fieldname = ?'];
        $params = [$containerFieldName];

        foreach ($filters as $filter) {
            $conditions[] = $this->connection->quoteIdentifier($filter['column']) . ' = ?';
            $params[] = $filter['value'];
        }

        $conditions[] = $predicate;

        return [
            'index' => $index,
            'condition' => implode(' AND ', $conditions),
            'params' => $params,
        ];
    }

    private function valueIsUsed(mixed $value): bool
    {
        if ($value === null) {
            return false;
        }

        if (is_array($value) || $value instanceof \Countable) {
            return count($value) > 0;
        }

        if (is_string($value)) {
            return trim($value) !== '';
        }

        return true;
    }

    /**
     * @return array<int, string>
     */
    private function getColumns(string $table): array
    {
        $columns = $this->connection->createSchemaManager()->listTableColumns($table);

        if ($columns === null) {
            return [];
        }

        return array_map(
            static fn (Column $column): string => (string) $column->getName(),
            array_values($columns),
        );
    }

    private function findColumn(array $columns, string $name): ?string
    {
        foreach ($columns as $column) {
            if (strtolower($column) === strtolower($name)) {
                return $column;
            }
        }

        return null;
    }

    /**
     * Resolves the actual object id column of a table. Modern tables use `id`,
     * while class tables vary (`o_id`, `oo_id`, `ooo_id`), so the primary key is
     * used when available.
     */
    private function objectIdColumn(string $table): ?string
    {
        $indexes = $this->connection->createSchemaManager()->listTableIndexes($table);

        if ($indexes !== null && isset($indexes['primary'])) {
            $primaryColumns = $indexes['primary']->getColumns();

            if ($primaryColumns !== []) {
                return $primaryColumns[0];
            }
        }

        $columns = $this->getColumns($table);

        foreach (['id', 'o_id', 'oo_id', 'ooo_id'] as $candidate) {
            $column = $this->findColumn($columns, $candidate);

            if ($column !== null) {
                return $column;
            }
        }

        return null;
    }

    private function resolveTable(string $classId, string $prefix, string $legacyPrefix): ?string
    {
        $id = $this->sanitizeName($classId);

        foreach ([$prefix . $id, $legacyPrefix . $id] as $candidate) {
            if ($this->tableExists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function resolveBrickTable(string $classId, string $brickType): ?string
    {
        $id = $this->sanitizeName($classId);
        $type = $this->sanitizeName($brickType);

        $table = 'object_brick_store_' . $type . '_' . $id;

        if ($this->tableExists($table)) {
            return $table;
        }

        $legacy = 'object_brick_' . $id . '_' . $type;

        return $this->tableExists($legacy) ? $legacy : null;
    }

    private function resolveBrickLocalizedTable(string $classId, string $brickType): ?string
    {
        $table = 'object_brick_localized_' . $this->sanitizeName($brickType) . '_' . $this->sanitizeName($classId);

        return $this->tableExists($table) ? $table : null;
    }

    private function resolveCollectionTable(string $classId, string $collectionType): ?string
    {
        $id = $this->sanitizeName($classId);
        $type = $this->sanitizeName($collectionType);

        $table = 'object_collection_' . $type . '_' . $id;

        if ($this->tableExists($table)) {
            return $table;
        }

        $legacy = 'object_collection_' . $id . '_' . $type;

        return $this->tableExists($legacy) ? $legacy : null;
    }

    private function resolveCollectionLocalizedTable(string $classId, string $collectionType): ?string
    {
        $id = $this->sanitizeName($classId);
        $type = $this->sanitizeName($collectionType);

        $table = 'object_collection_' . $type . '_localized_' . $id;

        if ($this->tableExists($table)) {
            return $table;
        }

        $legacy = 'object_collection_' . $type . '_' . $id . '_localized';

        return $this->tableExists($legacy) ? $legacy : null;
    }

    private function classificationStoreTable(string $classId): ?string
    {
        $table = self::CLASSIFICATION_STORE_TABLE_PREFIX . $this->sanitizeName($classId);

        return $this->tableExists($table) ? $table : null;
    }

    private function tableExists(string $table): bool
    {
        return $this->connection->createSchemaManager()->tablesExist([$table]);
    }

    private function sanitizeName(string $value): string
    {
        return (string) preg_replace('/[^a-zA-Z0-9_]/', '', $value);
    }

    /**
     * Builds the non-empty predicate for a plain value column, keeping the exact
     * semantics of the previous implementation: checkboxes count only explicit
     * `1` values, numeric columns are used when not NULL.
     */
    private function buildPredicate(string $column, string $fieldType): string
    {
        $quoted = $this->connection->quoteIdentifier($column);

        if (in_array($fieldType, self::CHECKBOX_FIELD_TYPES, true)) {
            return $quoted . " = '1'";
        }

        if (in_array($fieldType, self::NUMERIC_FIELD_TYPES, true)) {
            return $quoted . ' IS NOT NULL';
        }

        return $quoted . ' IS NOT NULL AND TRIM(' . $quoted . ") <> ''";
    }

    /**
     * Mirrors buildPredicate, but for brick / collection / classification store
     * value columns that may hold serialized payloads (relations, blocks) or
     * empty-serialized-array markers for unset data.
     */
    private function buildContainerPredicate(string $column, string $fieldType): string
    {
        $quoted = $this->connection->quoteIdentifier($column);

        if (in_array($fieldType, self::RELATION_FIELD_TYPES, true) || $fieldType === self::BLOCK_FIELD_TYPE) {
            return $quoted . ' IS NOT NULL AND ' . $quoted . " != '' AND " . $quoted . " != 'a:0:{}'";
        }

        if (in_array($fieldType, self::CHECKBOX_FIELD_TYPES, true)) {
            return $quoted . " = '1'";
        }

        if (in_array($fieldType, self::NUMERIC_FIELD_TYPES, true)) {
            return $quoted . ' IS NOT NULL';
        }

        return $quoted . ' IS NOT NULL AND TRIM(' . $quoted . ") <> ''";
    }
}