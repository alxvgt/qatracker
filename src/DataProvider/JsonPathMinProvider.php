<?php

declare(strict_types=1);

namespace Alxvng\QATracker\DataProvider;

use Alxvng\QATracker\DataProvider\Reducer\MinReducerTrait;

class JsonPathMinProvider extends AbstractJsonPathReducerProvider
{
    use MinReducerTrait;
}
