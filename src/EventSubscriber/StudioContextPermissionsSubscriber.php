<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\EventSubscriber;

use Watza\AttributeHeatmapBundle\Util\Constant\PermissionConstants;
use Pimcore\Bundle\StudioBackendBundle\Perspective\Model\ContextPermissionData;
use Pimcore\Bundle\StudioBackendBundle\Perspective\Service\ContextPermissionsServiceInterface;
use Pimcore\Bundle\StudioBackendBundle\Perspective\Util\Constant\ContextPermissionGroups;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @internal
 */
final readonly class StudioContextPermissionsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ContextPermissionsServiceInterface $permissionsService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'addContextPermissions',
        ];
    }

    public function addContextPermissions(): void
    {
        $this->permissionsService->add(
            new ContextPermissionData(
                PermissionConstants::ATTRIBUTE_HEATMAP,
                ContextPermissionGroups::DATA_MANAGEMENT->value
            )
        );
    }
}