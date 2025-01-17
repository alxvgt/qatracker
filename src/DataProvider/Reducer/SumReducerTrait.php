<?php

declare(strict_types=1);

namespace Alxvng\QATracker\DataProvider\Reducer;

use RuntimeException;

trait SumReducerTrait
{
    public function reduceMethod(array $nodes): float
    {
        $sum = 0;
        foreach ($nodes as $node) {
            if (!is_numeric((string) $node)) {
                throw new RuntimeException(sprintf('The result of must be a numeric value, got "%s"', $node));
            }

            $sum += (float) $node;
        }

        return round($sum, 2);
    }
}
