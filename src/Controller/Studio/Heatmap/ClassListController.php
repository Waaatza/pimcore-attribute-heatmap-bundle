<?php
declare(strict_types=1);

namespace Pimcore\Bundle\AttributeHeatmapBundle\Controller\Studio\Heatmap;

use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use Pimcore\Bundle\AttributeHeatmapBundle\OpenApi\Config\Prefix;
use Pimcore\Bundle\AttributeHeatmapBundle\OpenApi\Config\Tags;
use Pimcore\Bundle\AttributeHeatmapBundle\Schema\Heatmap\ClassItemCollection;
use Pimcore\Bundle\AttributeHeatmapBundle\Service\Studio\Classes\ClassServiceInterface;
use Pimcore\Bundle\AttributeHeatmapBundle\Util\Constant\PermissionConstants;
use Pimcore\Bundle\StudioBackendBundle\Controller\AbstractApiController;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\DefaultResponses;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\SuccessResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
final class ClassListController extends AbstractApiController
{
    private const string ROUTE = Prefix::BUNDLE . '/classes';

    public function __construct(
        SerializerInterface $serializer,
        private readonly ClassServiceInterface $classService,
    ) {
        parent::__construct($serializer);
    }

    #[Route('/classes', name: 'pimcore_studio_api_bundle_attribute_heatmap_class_list', methods: ['GET'])]
    #[IsGranted(PermissionConstants::DATA_OBJECTS)]
    #[Get(
        path: self::ROUTE,
        operationId: 'bundle_attribute_heatmap_class_list_get_collection',
        description: 'bundle_attribute_heatmap_class_list_get_description',
        summary: 'bundle_attribute_heatmap_class_list_get_summary',
        tags: [Tags::AttributeHeatmap->value]
    )]
    #[SuccessResponse(
        description: 'bundle_attribute_heatmap_class_list_get_success_response',
        content: new JsonContent(ref: ClassItemCollection::class)
    )]
    #[DefaultResponses]
    public function getClasses(): JsonResponse
    {
        return $this->jsonResponse($this->classService->getClasses());
    }
}