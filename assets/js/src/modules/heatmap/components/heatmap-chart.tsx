import React, { useMemo, useState } from 'react'
import { Alert, Flex, Select, Tooltip } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import type { AttributeUsageState, HeatmapAttribute } from '../api/heatmap-api'
import { useStyles } from './heatmap-chart.styles'

type SortOrder = 'usage-descending' | 'usage-ascending' | 'name'

interface HeatmapChartProps {
  attributes: HeatmapAttribute[]
}

const BAR_CLASS: Record<AttributeUsageState, keyof ReturnType<typeof useStyles>['styles']> = {
  used: 'barUsed',
  partiallyUsed: 'barPartiallyUsed',
  unused: 'barUnused',
  notAnalyzable: 'barNotAnalyzable'
}

const getPercentage = (attribute: HeatmapAttribute): number => {
  return Math.round((attribute.usageRatio ?? 0) * 100)
}

export const HeatmapChart = ({ attributes }: HeatmapChartProps): React.JSX.Element => {
  const { styles } = useStyles()
  const { t } = useTranslation()
  const [selectedGroup, setSelectedGroup] = useState<string>('all')
  const [sortOrder, setSortOrder] = useState<SortOrder>('usage-descending')

  const groups = useMemo((): string[] => {
    return [...new Set(attributes.map((attribute) => attribute.group))].sort((left, right) => (
      left.localeCompare(right)
    ))
  }, [attributes])

  const visibleAttributes = useMemo((): HeatmapAttribute[] => {
    const filtered = selectedGroup === 'all'
      ? [...attributes]
      : attributes.filter((attribute) => attribute.group === selectedGroup)

    return filtered.sort((left, right) => {
      if (sortOrder === 'name') {
        return left.title.localeCompare(right.title)
      }

      const direction = sortOrder === 'usage-descending' ? -1 : 1

      return (getPercentage(left) - getPercentage(right)) * direction
    })
  }, [attributes, selectedGroup, sortOrder])

  return (
    <>
      <Flex gap="small" wrap className={ styles.controls }>
        <Select
          className={ styles.filter }
          aria-label={ t('attribute-heatmap.chart.group-filter') }
          value={ selectedGroup }
          options={ [
            { label: t('attribute-heatmap.chart.all-groups'), value: 'all' },
            ...groups.map((group) => ({ label: group, value: group }))
          ] }
          onChange={ (value: string): void => setSelectedGroup(value) }
        />
        <Select
          className={ styles.filter }
          aria-label={ t('attribute-heatmap.chart.sort') }
          value={ sortOrder }
          options={ [
            {
              label: t('attribute-heatmap.chart.sort.usage-descending'),
              value: 'usage-descending'
            },
            {
              label: t('attribute-heatmap.chart.sort.usage-ascending'),
              value: 'usage-ascending'
            },
            { label: t('attribute-heatmap.chart.sort.name'), value: 'name' }
          ] }
          onChange={ (value: SortOrder): void => setSortOrder(value) }
        />
      </Flex>

      { visibleAttributes.length === 0
        ? (
            <Alert
              className={ styles.empty }
              type="info"
              showIcon
              message={ t('attribute-heatmap.chart.empty') }
            />
          )
        : (
            <div className={ styles.chart }>
              { visibleAttributes.map((attribute) => {
                const percentage = getPercentage(attribute)
                const value = attribute.usageRatio === null ? 'n/a' : `${percentage}%`
                const tooltip = t('attribute-heatmap.usage', {
                  used: attribute.usedCount ?? 'n/a',
                  total: attribute.totalCount
                })

                return (
                  <Tooltip key={ attribute.name } title={ tooltip }>
                    <div className={ `${styles.row} ${styles.responsive}` }>
                      <div className={ styles.label }>
                        <div className={ styles.title }>{ attribute.title }</div>
                        <div className={ styles.meta }>{ `${attribute.group} · ${attribute.fieldType}` }</div>
                      </div>
                      <div
                        className={ styles.track }
                        role="progressbar"
                        aria-label={ attribute.title }
                        aria-valuemin={ 0 }
                        aria-valuemax={ 100 }
                        aria-valuenow={ attribute.usageRatio === null ? undefined : percentage }
                      >
                        <div
                          className={ `${styles.bar} ${styles[BAR_CLASS[attribute.usageState]]}` }
                          style={ { width: `${percentage}%` } }
                        />
                      </div>
                      <span className={ styles.value }>{ value }</span>
                    </div>
                  </Tooltip>
                )
              }) }
            </div>
          ) }
    </>
  )
}
