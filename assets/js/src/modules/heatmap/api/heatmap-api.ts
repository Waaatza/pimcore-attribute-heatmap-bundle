import { api, getPrefix } from '@pimcore/studio-ui-bundle/api'

export type AttributeUsageState = 'unused' | 'partiallyUsed' | 'used' | 'notAnalyzable'

export interface ClassListItem {
  id: string
  name: string
  objectCount: number
}

export interface ClassItemCollection {
  items: ClassListItem[]
  totalItems: number
}

export interface HeatmapClassInfo {
  classId: string
  name: string
  objectCount: number
}

export interface HeatmapAttribute {
  name: string
  title: string
  fieldType: string
  group: string
  usageState: AttributeUsageState
  usedCount: number | null
  totalCount: number
  usageRatio: number | null
}

export interface HeatmapUsageSummary {
  total: number
  used: number
  partiallyUsed: number
  unused: number
  notAnalyzable: number
}

export interface AttributeHeatmapResult {
  classInfo: HeatmapClassInfo
  attributes: HeatmapAttribute[]
  usageSummary: HeatmapUsageSummary
}

const attributeHeatmapApi = api.injectEndpoints({
  endpoints: (build) => ({
    attributeHeatmapGetClasses: build.query<ClassItemCollection, void>({
      query: () => ({
        url: `${getPrefix()}/bundle/attribute-heatmap/classes`,
        method: 'GET'
      })
    }),
    attributeHeatmapAnalyze: build.query<AttributeHeatmapResult, { classId: string }>({
      query: (args) => ({
        url: `${getPrefix()}/bundle/attribute-heatmap/classes/${args.classId}/heatmap`,
        method: 'GET'
      })
    })
  })
})

export const {
  useAttributeHeatmapGetClassesQuery,
  useAttributeHeatmapAnalyzeQuery
} = attributeHeatmapApi