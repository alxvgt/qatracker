<?php


namespace Alxvng\QATracker\DataProvider\Reducer;


trait CountReducerTrait
{
    /**
     * @param array $nodes
     * @return float
     */
    public function reduceMethod(array $nodes): float
    {
        return count($nodes);
    }
}