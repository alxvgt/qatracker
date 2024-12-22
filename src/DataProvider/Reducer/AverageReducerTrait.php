<?php

declare(strict_types=1);

namespace Alxvng\QATracker\DataProvider\Reducer;

use function array_sum;

trait AverageReducerTrait
{
    public function reduceMethod(array $nodes): float
    {
        return round(array_sum($nodes) / count($nodes), 2);
    }
}
