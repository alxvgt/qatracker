<?php

namespace Alxvng\QATracker\DataProvider\Reducer;

trait AverageTopReducerTrait
{
    public function reduceMethod(array $nodes): float
    {
        \rsort($nodes);
        $nodes = \array_splice($nodes, 0, $this->top);
        return round(\array_sum($nodes) / count($nodes), 2);
    }
}
