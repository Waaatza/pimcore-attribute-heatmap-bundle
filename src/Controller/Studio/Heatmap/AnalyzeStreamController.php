<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Controller\Studio\Heatmap;

use OpenApi\Attributes\Get;
use Watza\AttributeHeatmapBundle\OpenApi\Attribute\Parameter\Path\ClassIdParameter;
use Watza\AttributeHeatmapBundle\OpenApi\Config\Prefix;
use Watza\AttributeHeatmapBundle\OpenApi\Config\Tags;
use Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\HeatmapServiceInterface;
use Watza\AttributeHeatmapBundle\Util\Constant\PermissionConstants;
use Pimcore\Bundle\StudioBackendBundle\Controller\AbstractApiController;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\Content\MediaType;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\DefaultResponses;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\SuccessResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

/**
 * @internal
 */
final class AnalyzeStreamController extends AbstractApiController
{
    private const string ROUTE = Prefix::BUNDLE . '/classes/{classId}/heatmap/stream';

    public function __construct(
        SerializerInterface $serializer,
        private readonly HeatmapServiceInterface $heatmapService,
    ) {
        parent::__construct($serializer);
    }

    #[Route(
        '/classes/{classId}/heatmap/stream',
        name: 'pimcore_studio_api_bundle_attribute_heatmap_analyze_stream',
        methods: ['GET']
    )]
    #[IsGranted(PermissionConstants::DATA_OBJECTS)]
    #[Get(
        path: self::ROUTE,
        operationId: 'bundle_attribute_heatmap_analyze_stream',
        description: 'bundle_attribute_heatmap_analyze_stream_description',
        summary: 'bundle_attribute_heatmap_analyze_stream_summary',
        tags: [Tags::AttributeHeatmap->value]
    )]
    #[ClassIdParameter]
    #[SuccessResponse(
        description: 'bundle_attribute_heatmap_analyze_stream_success_response',
        content: [new MediaType('text/event-stream')]
    )]
    #[DefaultResponses]
    public function analyzeStream(string $classId): StreamedResponse
    {
        return new StreamedResponse(
            function () use ($classId): void {
                @set_time_limit(0);

                try {
                    $result = $this->heatmapService->analyze(
                        $classId,
                        function (int $percent, string $phase): void {
                            $this->writeSseEvent('progress', json_encode(
                                ['percent' => $percent, 'phase' => $phase],
                                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                            ));
                        },
                    );

                    $this->writeSseEvent(
                        'result',
                        $this->serializer->serialize($result, 'json', [
                            AbstractNormalizer::IGNORED_ATTRIBUTES => ['childrenByRef'],
                        ])
                    );
                } catch (Throwable $exception) {
                    $this->writeSseEvent('error', json_encode(
                        ['message' => $exception->getMessage()],
                        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                    ));
                }
            },
            200,
            [
                'Content-Type' => 'text/event-stream; charset=utf-8',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no',
            ],
        );
    }

    private function writeSseEvent(string $event, string $data): void
    {
        echo 'event: ' . $event . "\n";

        foreach (preg_split('/\r\n|\r|\n/', $data) as $line) {
            echo 'data: ' . $line . "\n";
        }

        echo "\n";
        @ob_flush();
        flush();
    }
}