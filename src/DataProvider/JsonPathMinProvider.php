<?php

namespace Alxvng\QATracker\DataProvider;

use Alxvng\QATracker\DataProvider\Reducer\AverageReducerTrait;
use Alxvng\QATracker\DataProvider\Reducer\MinReducerTrait;

class JsonPathMinProvider extends AbstractJsonPathReducerProvider
{
    use MinReducerTrait;
}
