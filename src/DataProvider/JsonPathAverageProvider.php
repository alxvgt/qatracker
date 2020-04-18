<?php


namespace Alxvng\QATracker\DataProvider;

use Alxvng\QATracker\DataProvider\Reducer\AverageReducerTrait;

class JsonPathAverageProvider extends AbstractJsonPathReducerProvider
{
    use AverageReducerTrait;
}