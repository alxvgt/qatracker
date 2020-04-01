<?php


namespace App\DataProvider\Reducer;


trait AverageReducerTrait
{
    /**
     * @param array $nodes
     * @return int
     */
    protected function reduceMethod(array $nodes): int
    {
        $sum  =0;
        foreach ($nodes as $node) {
            if (!is_numeric((string)$node)) {
                throw new \RuntimeException(sprintf('The result of must be a numeric value, got "%s"', $node));
            }

            $sum += (int)$node;
        }
        return $sum / count($nodes);
    }
}