import React, { useEffect, useMemo, useState } from 'react'
import {
  Alert,
  Content,
  Flex,
  Header,
  Progress,
  Select,
  Tag,
  Tabs,
  Tooltip
} from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import {
  useAttributeHeatmapGetClassesQuery,
  type AttributeUsageState,
  type HeatmapAttribute
} from '../api/heatmap-api'
import { useHeatmapStream, type HeatmapPhase } from '../hooks/use-heatmap-stream'
import { HeatmapChart } from './heatmap-chart'
import { useStyles } from './heatmap-widget.styles'

const PHASE_LABELS: Record<HeatmapPhase, string> = {
  idle: '',
  collect: 'attribute-heatmap.phase.collect',
  count: 'attribute-heatmap.phase.count',
  objects: 'attribute-heatmap.phase.objects',
  hydrate: 'attribute-heatmap.phase.hydrate',
  done: 'attribute-heatmap.phase.done'
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
  const { t } = useTranslation()
  const tooltipTitle = [
    attribute.name,
    t('attribute-heatmap.type', { type: attribute.fieldType }),
    t('attribute-heatmap.usage', {
      used: attribute.usedCount ?? 'n/a',
      total: attribute.totalCount
    })
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
  const { t } = useTranslation()
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
  const legendItems: Array<{ state: AttributeUsageState, label: string }> = [
    { state: 'used', label: t('attribute-heatmap.state.used') },
    { state: 'partiallyUsed', label: t('attribute-heatmap.state.partially-used') },
    { state: 'unused', label: t('attribute-heatmap.state.unused') },
    { state: 'notAnalyzable', label: t('attribute-heatmap.state.not-analyzable') }
  ]

  return (
    <Content
      padded
      padding={ { top: 'small', x: 'medium', bottom: 'medium' } }
    >
      <Header title={ t('attribute-heatmap.navigation.title') } />

      <div className={ styles.section }>
        { classesQuery.isError && (
          <Alert
            type="error"
            showIcon
            message={ t('attribute-heatmap.class-list.error') }
          />
        ) }

        { classesQuery.data && (
          <Select
            className={ styles.select }
            placeholder={ t('attribute-heatmap.class-select.placeholder') }
            loading={ classesQuery.isFetching }
            options={ classesQuery.data.items.map((classItem) => ({
              label: `${classItem.name} (${classItem.objectCount} ${t('attribute-heatmap.objects')})`,
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
            <Tag color="green">{ t('attribute-heatmap.summary.used', { count: summary.used }) }</Tag>
            <Tag color="orange">
              { t('attribute-heatmap.summary.partially-used', { count: summary.partiallyUsed }) }
            </Tag>
            <Tag color="red">{ t('attribute-heatmap.summary.unused', { count: summary.unused }) }</Tag>
            <Tag>
              { t('attribute-heatmap.summary.not-analyzable', { count: summary.notAnalyzable }) }
            </Tag>
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

      { loading && !heatmapData && (
        <Flex vertical justify="center" align="center" gap="small" className={ styles.section }>
          <Progress
            className={ styles.progressLarge }
            percent={ progress }
            status="active"
            showInfo
          />
          <span className={ styles.phaseCaption }>
            { t(PHASE_LABELS[heatmapStream.phase]) }
          </span>
        </Flex>
      ) }

      { heatmapData && (
        <Tabs
          items={ [
            {
              key: 'heatmap',
              label: t('attribute-heatmap.tab.heatmap'),
              children: (
                <>
                  <div className={ styles.legend }>
                    { legendItems.map(({ state, label }) => (
                      <Flex key={ state } gap="small" align="center">
                        <span
                          className={ `${styles.legendDot} ${styles[STATE_DOT_CLASS[state]]}` }
                        />
                        <span>{ label }</span>
                      </Flex>
                    )) }
                  </div>

                  { groups.length === 0 && (
                    <Alert type="info" showIcon message={ t('attribute-heatmap.empty') } />
                  ) }

                  { groups.map(([group, attributes]) => (
                    <div key={ group } className={ styles.group }>
                      <Header title={ group }>
                        <span className={ styles.groupCount }>
                          { t('attribute-heatmap.group-count', { count: attributes.length }) }
                        </span>
                      </Header>
                      <div className={ styles.tiles }>
                        { attributes.map((attribute) => (
                          <HeatmapTile key={ attribute.name } attribute={ attribute } />
                        )) }
                      </div>
                    </div>
                  )) }
                </>
              )
            },
            {
              key: 'chart',
              label: t('attribute-heatmap.tab.chart'),
              children: <HeatmapChart attributes={ heatmapData.attributes } />
            }
          ] }
        />
      ) }
    </Content>
  )
}
