<?php


namespace Alxvng\QATracker\DataProvider\Reducer;


trait AverageReducerTrait
{
    /**
     * @param array $nodes
     * @return float
     */
    public function reduceMethod(array $nodes): float
    {
        $sum  =0;
        foreach ($nodes as $node) {
            if (!is_numeric((string)$node)) {
                throw new \RuntimeException(sprintf('The result of must be a numeric value, got "%s"', $node));
            }

            $sum += (float)$node;
        }
        return round($sum / count($nodes), 2);
    }
}