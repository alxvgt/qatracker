<?php

declare(strict_types=1);

namespace Alxvng\QATracker\DataProvider;

use Alxvng\QATracker\DataProvider\Reducer\AverageReducerTrait;

class XpathAverageProvider extends AbstractXpathReducerProvider
{
    use AverageReducerTrait;
}
