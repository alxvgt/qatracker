<?php


namespace App\DataProvider;

class XpathSumProvider extends AbstractXpathReducerProvider
{
    /**
     * @param int $result
     * @param int $node
     * @return int
     */
    protected function reduceMethod(int $result, int $node): int
    {
        if (!is_numeric((string)$node)) {
            throw new \RuntimeException(sprintf('The result of must be a numeric value, got "%s"', $node));
        }

        return $result + $node;
    }
}