<?php


namespace App\DataProvider;

use App\DataProvider\Reducer\SumReducerTrait;

class JsonPathSumProvider extends AbstractJsonPathReducerProvider
{
    use SumReducerTrait;
}