<?php


namespace Alxvng\QATracker\DataProvider;

use Alxvng\QATracker\DataProvider\Reducer\SumReducerTrait;

class JsonPathSumProvider extends AbstractJsonPathReducerProvider
{
    use SumReducerTrait;
}