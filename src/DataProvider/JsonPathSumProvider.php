<?php

declare(strict_types=1);

namespace Alxvng\QATracker\DataProvider;

use Alxvng\QATracker\DataProvider\Reducer\SumReducerTrait;

class JsonPathSumProvider extends AbstractJsonPathReducerProvider
{
    use SumReducerTrait;
}
