<?php

declare(strict_types=1);

namespace Alxvng\QATracker\DataProvider;

interface ReducerProviderInterface
{
    public function reduceMethod(array $nodes): float;
}
