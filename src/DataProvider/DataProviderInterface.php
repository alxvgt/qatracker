<?php

declare(strict_types=1);

namespace Alxvng\QATracker\DataProvider;

interface DataProviderInterface
{
    public function fetchData(): float;
}
