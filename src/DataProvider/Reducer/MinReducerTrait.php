<?php

namespace Alxvng\QATracker\DataProvider\Reducer;

trait MinReducerTrait
{
    public function reduceMethod(array $nodes): float
    {
        foreach ($nodes as $node) {
            if (!is_numeric((string) $node)) {
                throw new \RuntimeException(sprintf('The result of must be a numeric value, got "%s"', $node));
            }
        }

        return min(...$nodes);
    }
}