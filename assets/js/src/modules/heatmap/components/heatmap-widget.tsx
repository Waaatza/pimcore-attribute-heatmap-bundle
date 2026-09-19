import React, { useEffect, useMemo, useState } from 'react'
import {
  Alert,
  Content,
  Flex,
  Header,
  Progress,
  Select,
  Tag,
  Tooltip
} from '@pimcore/studio-ui-bundle/components'
import {
  useAttributeHeatmapGetClassesQuery,
  type AttributeUsageState,
  type HeatmapAttribute
} from '../api/heatmap-api'
import { useHeatmapStream, type HeatmapPhase } from '../hooks/use-heatmap-stream'
import { useStyles } from './heatmap-widget.styles'

const PHASE_LABELS: Record<HeatmapPhase, string> = {
  idle: '',
  collect: 'Collecting attributes…',
  count: 'Counting objects…',
  objects: 'Analysing object values…',
  hydrate: 'Preparing result…',
  done: 'Done'
}

interface StateMeta {
  label: string
}

const STATE_META: Record<AttributeUsageState, StateMeta> = {
  used: { label: 'Used' },
  partiallyUsed: { label: 'Partially used' },
  unused: { label: 'Unused' },
  notAnalyzable: { label: 'Not analyzable' }
}

const formatRatio = (ratio: number | null): string => {
  if (ratio === null) {
    return 'n/a'
  }

  return `${Math.round(ratio * 100)}%`
}

const STATE_TILE_CLASS: Record<AttributeUsageState, keyof ReturnType<typeof useStyles>['styles']> = {
  used: 'tileUsed',
  partiallyUsed: 'tilePartiallyUsed',
  unused: 'tileUnused',
  notAnalyzable: 'tileNotAnalyzable'
}

const STATE_VALUE_CLASS: Record<AttributeUsageState, keyof ReturnType<typeof useStyles>['styles']> = {
  used: 'valueUsed',
  partiallyUsed: 'valuePartiallyUsed',
  unused: 'valueUnused',
  notAnalyzable: 'valueNotAnalyzable'
}

const STATE_DOT_CLASS: Record<AttributeUsageState, keyof ReturnType<typeof useStyles>['styles']> = {
  used: 'dotUsed',
  partiallyUsed: 'dotPartiallyUsed',
  unused: 'dotUnused',
  notAnalyzable: 'dotNotAnalyzable'
}

const HeatmapTile: React.FC<{ attribute: HeatmapAttribute }> = ({ attribute }) => {
  const { styles } = useStyles()
  const tooltipTitle = [
    attribute.name,
    `Type: ${attribute.fieldType}`,
    `Used: ${attribute.usedCount ?? 'n/a'} of ${attribute.totalCount} objects`
  ].join('\n')

  return (
    <Tooltip title={ tooltipTitle }>
      <div className={ `${styles.tile} ${styles[STATE_TILE_CLASS[attribute.usageState]]}` }>
        <div className={ styles.tileHeader }>
          <span className={ styles.tileType }>
            { attribute.fieldType }
          </span>
          <span className={ `${styles.tileValue} ${styles[STATE_VALUE_CLASS[attribute.usageState]]}` }>
            { formatRatio(attribute.usageRatio) }
          </span>
        </div>
        <div className={ styles.tileTitle }>
          { attribute.title }
        </div>
      </div>
    </Tooltip>
  )
}

export const HeatmapWidget: React.FC = (): React.JSX.Element => {
  const { styles } = useStyles()
  const [selectedClassId, setSelectedClassId] = useState<string | null>(null)

  const classesQuery = useAttributeHeatmapGetClassesQuery()
  const heatmapStream = useHeatmapStream(selectedClassId)

  const progress = heatmapStream.progress
  const heatmapData = heatmapStream.data
  const loading = classesQuery.isFetching || heatmapStream.isFetching

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

    if (heatmapData) {
      for (const attribute of heatmapData.attributes) {
        const key = attribute.group
        const entries = grouped.get(key) ?? []
        entries.push(attribute)
        grouped.set(key, entries)
      }
    }

    return [...grouped.entries()]
  }, [heatmapData])

  const summary = heatmapData?.usageSummary

  return (
    <Content
      padded
      padding={ { top: 'small', x: 'medium', bottom: 'medium' } }
    >
      <Header title="Attribute Heatmap" />

      <div className={ styles.section }>
        { classesQuery.isError && (
          <Alert
            type="error"
            showIcon
            message="The list of data object classes could not be loaded."
          />
        ) }

        { classesQuery.data && (
          <Select
            className={ styles.select }
            placeholder="Select a data object class"
            loading={ classesQuery.isFetching }
            options={ classesQuery.data.items.map((classItem) => ({
              label: `${classItem.name} (${classItem.objectCount} objects)`,
              value: classItem.id
            })) }
            value={ selectedClassId }
            onChange={ (value: unknown): void => setSelectedClassId(value === undefined ? null : String(value)) }
          />
        ) }
      </div>

      { heatmapStream.error && (
        <Alert
          type="error"
          showIcon
          message={ heatmapStream.error }
        />
      ) }

      { heatmapData && summary && (
        <Flex justify="space-between" align="center" className={ styles.section }>
          <Flex gap="small">
            <Tag color="green">{ `Used: ${summary.used}` }</Tag>
            <Tag color="orange">{ `Partially used: ${summary.partiallyUsed}` }</Tag>
            <Tag color="red">{ `Unused: ${summary.unused}` }</Tag>
            <Tag>{ `Not analyzable: ${summary.notAnalyzable}` }</Tag>
          </Flex>
          { loading && (
            <Progress
              className={ styles.progressSummary }
              percent={ progress }
              status="active"
              showInfo={ false }
            />
          ) }
        </Flex>
      ) }

      <div className={ styles.legend }>
        { Object.entries(STATE_META).map(([state, meta]) => (
          <Flex key={ state } gap="small" align="center">
            <span className={ `${styles.legendDot} ${styles[STATE_DOT_CLASS[state as AttributeUsageState]]}` } />
            <span>{ meta.label }</span>
          </Flex>
        )) }
      </div>

      { loading && !heatmapData && (
        <Flex vertical justify="center" align="center" gap="small" className={ styles.section }>
          <Progress
            className={ styles.progressLarge }
            percent={ progress }
            status="active"
            showInfo
          />
          <span className={ styles.phaseCaption }>
            { PHASE_LABELS[heatmapStream.phase] }
          </span>
        </Flex>
      ) }

      { heatmapData && groups.length === 0 && (
        <Alert type="info" showIcon message="No attributes found for this class." />
      ) }

      { groups.map(([group, attributes]) => (
        <div key={ group } className={ styles.group }>
          <Header title={ group }>
            <span className={ styles.groupCount }>
              { `${attributes.length} attributes` }
            </span>
          </Header>
          <div className={ styles.tiles }>
            { attributes.map((attribute) => (
              <HeatmapTile key={ attribute.name } attribute={ attribute } />
            )) }
          </div>
        </div>
      )) }
    </Content>
  )
}