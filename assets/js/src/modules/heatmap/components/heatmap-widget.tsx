import React, { useEffect, useMemo, useState } from 'react'
import {
  Alert,
  Content,
  Flex,
  Header,
  Select,
  Spin,
  Tag,
  Tooltip
} from '@pimcore/studio-ui-bundle/components'
import {
  useAttributeHeatmapAnalyzeQuery,
  useAttributeHeatmapGetClassesQuery,
  type AttributeUsageState,
  type HeatmapAttribute
} from '../api/heatmap-api'

interface StateMeta {
  color: string
  background: string
  label: string
}

const STATE_META: Record<AttributeUsageState, StateMeta> = {
  used: { color: '#15803d', background: '#f0fdf4', label: 'Used' },
  partiallyUsed: { color: '#b45309', background: '#fffbeb', label: 'Partially used' },
  unused: { color: '#b91c1c', background: '#fef2f2', label: 'Unused' },
  notAnalyzable: { color: '#4b5563', background: '#f3f4f6', label: 'Not analyzable' }
}

const formatRatio = (ratio: number | null): string => {
  if (ratio === null) {
    return 'n/a'
  }

  return `${Math.round(ratio * 100)}%`
}

const HeatmapTile: React.FC<{ attribute: HeatmapAttribute }> = ({ attribute }) => {
  const meta = STATE_META[attribute.usageState]
  const tooltipTitle = [
    attribute.name,
    `Type: ${attribute.fieldType}`,
    `Used: ${attribute.usedCount ?? 'n/a'} of ${attribute.totalCount} objects`
  ].join('\n')

  return (
    <Tooltip title={ tooltipTitle }>
      <div
        style={ {
          width: 150,
          minWidth: 150,
          padding: '8px 10px',
          borderRadius: 8,
          background: meta.background,
          border: `1px solid ${meta.color}`,
          boxSizing: 'border-box'
        } }
      >
        <div
          style={ {
            fontSize: 12,
            lineHeight: '16px',
            color: 'rgba(0, 0, 0, 0.65)',
            whiteSpace: 'nowrap',
            textOverflow: 'ellipsis',
            overflow: 'hidden'
          } }
        >
          { attribute.title }
        </div>
        <div
          style={ {
            fontSize: 16,
            fontWeight: 600,
            color: meta.color,
            marginTop: 2
          } }
        >
          { formatRatio(attribute.usageRatio) }
        </div>
      </div>
    </Tooltip>
  )
}

export const HeatmapWidget: React.FC = (): React.JSX.Element => {
  const [selectedClassId, setSelectedClassId] = useState<string | null>(null)

  const classesQuery = useAttributeHeatmapGetClassesQuery()
  const heatmapQuery = useAttributeHeatmapAnalyzeQuery(
    selectedClassId ? { classId: selectedClassId } : { classId: '' },
    { skip: selectedClassId === null }
  )

  useEffect(() => {
    if (selectedClassId === null && classesQuery.data?.items.length) {
      const first = classesQuery.data.items[0]
      if (first) {
        setSelectedClassId(first.id)
      }
    }
  }, [classesQuery.data, selectedClassId])

  const groups = useMemo(() => {
    const grouped = new Map<string, HeatmapAttribute[]>()

    if (heatmapQuery.data) {
      for (const attribute of heatmapQuery.data.attributes) {
        const key = attribute.group
        const entries = grouped.get(key) ?? []
        entries.push(attribute)
        grouped.set(key, entries)
      }
    }

    return [...grouped.entries()]
  }, [heatmapQuery.data])

  const loading = classesQuery.isFetching || heatmapQuery.isFetching
  const summary = heatmapQuery.data?.usageSummary

  return (
    <Content
      padded
      padding={ { top: 'small', x: 'medium', bottom: 'medium' } }
    >
      <Header title="Attribute Heatmap" />

      <div style={ { marginBottom: '1rem' } }>
        { classesQuery.isError && (
          <Alert
            type="error"
            showIcon
            message="The list of data object classes could not be loaded."
          />
        ) }

        { classesQuery.data && (
          <Select
            placeholder="Select a data object class"
            loading={ classesQuery.isFetching }
            options={ classesQuery.data.items.map((classItem) => ({
              label: `${classItem.name} (${classItem.objectCount} objects)`,
              value: classItem.id
            })) }
            value={ selectedClassId }
            onChange={ (value: unknown): void => setSelectedClassId(value === undefined ? null : String(value)) }
            style={ { width: 320 } }
          />
        ) }
      </div>

      { heatmapQuery.isError && (
        <Alert
          type="error"
          showIcon
          message={ `The analysis for class "${selectedClassId ?? ''}" failed.` }
        />
      ) }

      { heatmapQuery.data && summary && (
        <Flex justify="space-between" align="center" style={ { marginBottom: '1rem' } }>
          <Flex gap="small">
            <Tag color="green">{ `Used: ${summary.used}` }</Tag>
            <Tag color="orange">{ `Partially used: ${summary.partiallyUsed}` }</Tag>
            <Tag color="red">{ `Unused: ${summary.unused}` }</Tag>
            <Tag>{ `Not analyzable: ${summary.notAnalyzable}` }</Tag>
          </Flex>
          { loading && <Spin size="small" /> }
        </Flex>
      ) }

      <div
        style={ {
          display: 'flex',
          gap: '1rem',
          marginBottom: '0.75rem',
          fontSize: 12,
          color: 'rgba(0, 0, 0, 0.45)'
        } }
      >
        { Object.entries(STATE_META).map(([state, meta]) => (
          <Flex key={ state } gap="x-small" align="center">
            <span
              style={ {
                display: 'inline-block',
                width: 12,
                height: 12,
                borderRadius: 3,
                background: meta.color
              } }
            />
            <span>{ meta.label }</span>
          </Flex>
        )) }
      </div>

      { heatmapQuery.isLoading && !heatmapQuery.data && (
        <Flex justify="center" align="center" style={ { padding: '3rem 0' } }>
          <Spin />
        </Flex>
      ) }

      { heatmapQuery.data && groups.length === 0 && (
        <Alert type="info" showIcon message="No attributes found for this class." />
      ) }

      { groups.map(([group, attributes]) => (
        <div key={ group } style={ { marginBottom: '1rem' } }>
          <Header title={ group }>
            <span style={ { fontSize: 12, color: 'rgba(0, 0, 0, 0.45)' } }>
              { `${attributes.length} attributes` }
            </span>
          </Header>
          <div style={ { display: 'flex', flexWrap: 'wrap', gap: '0.5rem', marginTop: '0.5rem' } }>
            { attributes.map((attribute) => (
              <HeatmapTile key={ attribute.name } attribute={ attribute } />
            )) }
          </div>
        </div>
      )) }
    </Content>
  )
}