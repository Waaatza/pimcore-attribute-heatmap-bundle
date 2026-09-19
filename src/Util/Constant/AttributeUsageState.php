<?php
declare(strict_types=1);

namespace Pimcore\Bundle\AttributeHeatmapBundle\Util\Constant;

/**
 * @internal
 */
enum AttributeUsageState: string
{
    case UNUSED = 'unused';
    case PARTIALLY_USED = 'partiallyUsed';
    case USED = 'used';
    case NOT_ANALYZABLE = 'notAnalyzable';
}