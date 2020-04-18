<?php


namespace Alxvng\QATracker\DataProvider\Finder;

use Alxvng\QATracker\DataProvider\Model\AbstractDataSerie;

class ProviderFinder
{
    /**
     * @param string $id
     * @param array  $providersStack
     * @return AbstractDataSerie
     */
    public static function findById(string $id, array $providersStack): AbstractDataSerie
    {
        if (!isset($providersStack[$id])) {
            throw new \RuntimeException(sprintf('Unable to find provider for id "%s"', $id,));
        }

        return $providersStack[$id];
    }
}