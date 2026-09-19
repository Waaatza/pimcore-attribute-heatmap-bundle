<?php
declare(strict_types=1);

namespace Pimcore\Bundle\AttributeHeatmapBundle\Controller\Studio\Heatmap;

use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use Pimcore\Bundle\AttributeHeatmapBundle\OpenApi\Config\Prefix;
use Pimcore\Bundle\AttributeHeatmapBundle\OpenApi\Config\Tags;
use Pimcore\Bundle\AttributeHeatmapBundle\OpenApi\Attribute\Parameter\Path\ClassIdParameter;
use Pimcore\Bundle\AttributeHeatmapBundle\Schema\Heatmap\AttributeHeatmapResult;
use Pimcore\Bundle\AttributeHeatmapBundle\Service\Studio\Heatmap\HeatmapServiceInterface;
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
final class AnalyzeController extends AbstractApiController
{
    private const string ROUTE = Prefix::BUNDLE . '/classes/{classId}/heatmap';

    public function __construct(
        SerializerInterface $serializer,
        private readonly HeatmapServiceInterface $heatmapService,
    ) {
        parent::__construct($serializer);
    }

    #[Route('/classes/{classId}/heatmap', name: 'pimcore_studio_api_bundle_attribute_heatmap_analyze', methods: ['GET'])]
    #[IsGranted(PermissionConstants::DATA_OBJECTS)]
    #[Get(
        path: self::ROUTE,
        operationId: 'bundle_attribute_heatmap_get_result',
        description: 'bundle_attribute_heatmap_analyze_get_description',
        summary: 'bundle_attribute_heatmap_analyze_get_summary',
        tags: [Tags::AttributeHeatmap->value]
    )]
    #[ClassIdParameter]
    #[SuccessResponse(
        description: 'bundle_attribute_heatmap_analyze_get_success_response',
        content: new JsonContent(ref: AttributeHeatmapResult::class)
    )]
    #[DefaultResponses]
    public function analyze(string $classId): JsonResponse
    {
        return $this->jsonResponse($this->heatmapService->analyze($classId));
    }
}