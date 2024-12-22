<?php

declare(strict_types=1);

namespace Alxvng\QATracker\DataProvider;

use Alxvng\QATracker\DataProvider\Reducer\CountReducerTrait;

class JsonPathCountProvider extends AbstractJsonPathReducerProvider
{
    use CountReducerTrait;
}
