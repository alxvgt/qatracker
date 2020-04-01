<?php


namespace App\DataProvider;

use App\DataProvider\Reducer\CountReducerTrait;

class JsonPathCountProvider extends AbstractJsonPathReducerProvider
{
    use CountReducerTrait;
}