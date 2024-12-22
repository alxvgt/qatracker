<?php

declare(strict_types=1);

namespace Alxvng\QATracker\DataProvider\Reducer;

use function sort;

trait PercentileReducerTrait
{
    public function reduceMethod(array $nodes): float
    {
        sort($nodes);
        $index = ($this->percentile / 100) * (count($nodes) - 1);

        return $nodes[$index];
    }
}
