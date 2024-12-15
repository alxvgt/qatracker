<?php

namespace Alxvng\QATracker\DataProvider;

use Alxvng\QATracker\DataProvider\Reducer\AverageReducerTrait;
use Alxvng\QATracker\DataProvider\Reducer\MaxReducerTrait;
use Alxvng\QATracker\DataProvider\Reducer\MedianReducerTrait;
use Alxvng\QATracker\DataProvider\Reducer\MinReducerTrait;

class JsonPathMedianProvider extends AbstractJsonPathReducerProvider
{
    use MedianReducerTrait;
}
