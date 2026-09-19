import { type AbstractModule } from '@pimcore/studio-ui-bundle'
import { container, serviceIds } from '@pimcore/studio-ui-bundle/app'
import { type IconLibrary } from '@pimcore/studio-ui-bundle/modules/icon-library'
import { type MainNavRegistry } from '@pimcore/studio-ui-bundle/modules/app'
import { type WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { HeatmapIcon } from '../../icons/heatmap-icon'
import { HeatmapWidget } from './components/heatmap-widget'

export const AttributeHeatmapModule: AbstractModule = {
  onInit: (): void => {
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)
    iconLibrary.register({ name: 'heatmap', component: HeatmapIcon })

    const widgetRegistry = container.get<WidgetRegistry>(serviceIds.widgetManager)
    widgetRegistry.registerWidget({
      name: 'attribute-heatmap',
      component: HeatmapWidget
    })

    const mainNavRegistry = container.get<MainNavRegistry>(serviceIds.mainNavRegistry)
    mainNavRegistry.registerMainNavItem({
      path: 'Attribute Heatmap',
      label: 'attribute-heatmap.navigation.title',
      icon: 'heatmap',
      order: 1000,
      perspectivePermission: 'dataManagement.attributeHeatmap',
      widgetConfig: {
        name: 'Attribute Heatmap',
        id: 'attribute-heatmap',
        component: 'attribute-heatmap',
        config: {
          translationKey: 'attribute-heatmap.navigation.title',
          icon: {
            type: 'name',
            value: 'heatmap'
          }
        }
      }
    })
  }
}