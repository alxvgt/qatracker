<?php

namespace Alxvng\QATracker\DataProvider\Reducer;

trait MedianReducerTrait
{
    public function reduceMethod(array $nodes): float
    {
        return $nodes[(count($nodes)/2)];
    }
}
