<?php
declare(strict_types=1);

namespace Watza\AttributeHeatmapBundle\Service\Studio\Heatmap\Usage;

use Countable;
use Pimcore\Model\DataObject\Data\Consent;
use Pimcore\Model\DataObject\Data\InputQuantityValue;
use Pimcore\Model\DataObject\Data\QuantityValue;

final readonly class FieldUsageResolver implements FieldUsageResolverInterface
{
    public function isUsed(mixed $value): bool
    {
        if ($value === null) {
            return false;
        }

        if (is_array($value) || $value instanceof Countable) {
            return count($value) > 0;
        }

        if (is_string($value)) {
            return trim($value) !== '';
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return true;
        }

        if ($value instanceof Consent) {
            return $value->getConsent() !== null;
        }

        if ($value instanceof QuantityValue || $value instanceof InputQuantityValue) {
            return $value->getValue() !== null;
        }

        return true;
    }
}