<?php

declare(strict_types=1);

namespace Alxvng\QATracker\DataProvider\Reducer;

trait CountReducerTrait
{
    public function reduceMethod(array $nodes): float
    {
        return count($nodes);
    }
}
