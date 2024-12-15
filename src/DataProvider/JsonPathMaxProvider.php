<?php

namespace Alxvng\QATracker\DataProvider;

use Alxvng\QATracker\DataProvider\Reducer\AverageReducerTrait;
use Alxvng\QATracker\DataProvider\Reducer\MaxReducerTrait;
use Alxvng\QATracker\DataProvider\Reducer\MinReducerTrait;

class JsonPathMaxProvider extends AbstractJsonPathReducerProvider
{
    use MaxReducerTrait;
}
