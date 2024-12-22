<?php

declare(strict_types=1);

namespace Alxvng\QATracker\DataProvider;

use Alxvng\QATracker\DataProvider\Reducer\MaxReducerTrait;

class JsonPathMaxProvider extends AbstractJsonPathReducerProvider
{
    use MaxReducerTrait;
}
