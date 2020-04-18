<?php


namespace Alxvng\QATracker\DataProvider;

use Alxvng\QATracker\DataProvider\Reducer\CountReducerTrait;

class JsonPathCountProvider extends AbstractJsonPathReducerProvider
{
    use CountReducerTrait;
}