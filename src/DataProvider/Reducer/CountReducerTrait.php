<?php


namespace App\DataProvider\Reducer;


trait CountReducerTrait
{
    /**
     * @param array $nodes
     * @return int
     */
    protected function reduceMethod(array $nodes): int
    {
        return count($nodes);
    }
}