<?php


namespace App\DataProvider;

use App\DataProvider\Reducer\AverageReducerTrait;

class JsonPathAverageProvider extends AbstractJsonPathReducerProvider
{
    use AverageReducerTrait;
}