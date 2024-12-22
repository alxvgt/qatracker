<?php

declare(strict_types=1);

namespace Alxvng\QATracker\DataProvider\Reducer;

use function array_splice;
use function array_sum;
use function rsort;

trait AverageTopReducerTrait
{
    public function reduceMethod(array $nodes): float
    {
        rsort($nodes);
        $nodes = array_splice($nodes, 0, $this->top);

        return round(array_sum($nodes) / count($nodes), 2);
    }
}
