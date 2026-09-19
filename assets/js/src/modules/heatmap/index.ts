import { type AbstractModule } from '@pimcore/studio-ui-bundle'
import { container, serviceIds } from '@pimcore/studio-ui-bundle/app'
import { type MainNavRegistry } from '@pimcore/studio-ui-bundle/modules/app'
import { type WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { HeatmapWidget } from './components/heatmap-widget'

export const AttributeHeatmapModule: AbstractModule = {
  onInit: (): void => {
    const widgetRegistry = container.get<WidgetRegistry>(serviceIds.widgetManager)
    widgetRegistry.registerWidget({
      name: 'attribute-heatmap',
      component: HeatmapWidget
    })

    const mainNavRegistry = container.get<MainNavRegistry>(serviceIds.mainNavRegistry)
    mainNavRegistry.registerMainNavItem({
      path: 'Data Management/Attribute Heatmap',
      label: 'Attribute Heatmap',
      order: 1000,
      widgetConfig: {
        name: 'Attribute Heatmap',
        id: 'attribute-heatmap',
        component: 'attribute-heatmap',
        config: {
          icon: {
            type: 'name',
            value: 'dashboard'
          }
        }
      }
    })
  }
}