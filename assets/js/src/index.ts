import { type IAbstractPlugin } from '@pimcore/studio-ui-bundle'
import { AttributeHeatmapModule } from './modules/heatmap'

export const AttributeHeatmapExtension: IAbstractPlugin = {
  name: 'Attribute Heatmap Studio Extension',

  onStartup({ moduleSystem }): void {
    moduleSystem.registerModule(AttributeHeatmapModule)
  }
}