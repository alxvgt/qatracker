<?php

declare(strict_types=1);

namespace Alxvng\QATracker\DataProvider\Finder;

use Alxvng\QATracker\DataProvider\Model\AbstractDataSerie;
use RuntimeException;

class ProviderFinder
{
    public static function findById(string $id, array $providersStack): AbstractDataSerie
    {
        if (!isset($providersStack[$id])) {
            throw new RuntimeException(sprintf('Unable to find data serie for id "%s"', $id));
        }

        return $providersStack[$id];
    }
}
