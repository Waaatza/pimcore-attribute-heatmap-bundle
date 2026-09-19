<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Usage;

use Doctrine\DBAL\Connection;
use Throwable;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Model\AttributeDescriptor;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Value\DirectValueProvider;
use Pimcore\Model\DataObject\ClassDefinition;

/**
 * Counts "attribute used" occurrences directly on the class data tables via SQL
 * aggregates. This avoids loading and hydrating every single object, which is
 * orders of magnitude faster for large classes. Descriptors that cannot be
 * resolved to a plain column (bricks, field collections, classification store,
 * blocks, relations with array payloads) are left for the object-based loop.
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

    private const array LOOP_FIELD_TYPES = [
        'block',
        'localizedfields',
        'objectbricks',
        'fieldcollections',
        'classificationstore',
    ];

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

    public function __construct(private Connection $connection)
    {
    }

    public function count(string $classId, ClassDefinition $definition, array $descriptors): SqlUsageResult
    {
        $usedCounts = array_fill(0, count($descriptors), 0);
        $remainingIndexes = [];

        $dataTable = $this->resolveTable($classId, self::DATA_TABLE_PREFIX, self::DATA_TABLE_LEGACY_PREFIX);
        $localizedTable = $this->resolveTable($classId, self::LOCALIZED_TABLE_PREFIX, self::LOCALIZED_TABLE_LEGACY_PREFIX);

        $dataColumns = $dataTable !== null ? $this->getColumns($dataTable) : [];
        $localizedColumns = $localizedTable !== null ? $this->getColumns($localizedTable) : [];

        $directCounts = [];
        $localizedCounts = [];
        $relationCounts = [];

        foreach ($descriptors as $index => $descriptor) {
            $provider = $descriptor->getValueProvider();

            if (!$descriptor->getAnalyzable() || !$provider instanceof DirectValueProvider) {
                $remainingIndexes[] = $index;

                continue;
            }

            $fieldType = $descriptor->getFieldType();
            $language = $provider->getLanguage();

            if ($language === null && in_array($fieldType, self::RELATION_FIELD_TYPES, true)) {
                $relationCounts[] = [
                    'index' => $index,
                    'fieldname' => $provider->getFieldName(),
                ];

                continue;
            }

            if (in_array($fieldType, self::LOOP_FIELD_TYPES, true)) {
                $remainingIndexes[] = $index;

                continue;
            }

            $columns = $language !== null ? $localizedColumns : $dataColumns;

            $column = $this->findColumn($columns, $provider->getFieldName());

            if ($column === null) {
                $remainingIndexes[] = $index;

                continue;
            }

            $count = [
                'index' => $index,
                'predicate' => $this->buildPredicate($column, $fieldType),
            ];

            if ($language !== null) {
                $localizedCounts[] = [...$count, 'language' => $language];

                continue;
            }

            $directCounts[] = $count;
        }

        if ($dataTable !== null) {
            $this->countDirect($dataTable, $directCounts, $usedCounts);
        }

        if ($localizedTable !== null) {
            $this->countLocalized($localizedTable, $localizedCounts, $usedCounts);
        }

        $relationsTable = $this->resolveTable($classId, self::RELATIONS_TABLE_PREFIX, self::RELATIONS_TABLE_PREFIX);

        if ($relationsTable !== null) {
            $this->countRelations($relationsTable, $relationCounts, $usedCounts);
        }

        return new SqlUsageResult($usedCounts, $remainingIndexes);
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
     * @return array<int, string>
     */
    private function getColumns(string $table): array
    {
        $statement = $this->connection->executeQuery(
            'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?',
            [$table],
        );

        return $statement->fetchFirstColumn();
    }

    private function resolveTable(string $classId, string $prefix, string $legacyPrefix): ?string
    {
        $id = (string) preg_replace('/[^a-zA-Z0-9_]/', '', $classId);

        foreach ([$prefix . $id, $legacyPrefix . $id] as $candidate) {
            if ($this->tableExists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function tableExists(string $table): bool
    {
        try {
            $this->connection->executeQuery(
                'SELECT 1 FROM ' . $this->connection->quoteIdentifier($table) . ' LIMIT 1',
            )->fetchFirstColumn();

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @param array<int, string> $columns
     */
    private function findColumn(array $columns, string $fieldName): ?string
    {
        foreach ($columns as $column) {
            if (mb_strtolower($column) === mb_strtolower($fieldName)) {
                return $column;
            }
        }

        return null;
    }

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
}