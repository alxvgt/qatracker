<?php


namespace App\DataProvider;

class XpathCountProvider extends AbstractXpathReducerProvider
{
    /**
     * @param int $result
     * @param int $node
     * @return int
     */
    protected function reduceMethod(int $result, $node): int
    {
        return ++$result;
    }
}