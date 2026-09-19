<?php
declare(strict_types=1);

namespace Pimcore\Bundle\AttributeHeatmapBundle\OpenApi\Attribute\Parameter\Path;

use Attribute;
use OpenApi\Attributes\PathParameter;
use OpenApi\Attributes\Schema;

#[Attribute(Attribute::TARGET_METHOD)]
final class ClassIdParameter extends PathParameter
{
    public function __construct(
        string $name = 'classId',
        Schema $schema = new Schema(type: 'string', example: 'pokemon'),
    ) {
        parent::__construct(
            name: $name,
            description: 'Class ID of the data object class to analyze',
            in: 'path',
            required: true,
            schema: $schema,
        );
    }
}