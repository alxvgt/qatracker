<?php

namespace Alxvng\QATracker\DataProvider;

use Alxvng\QATracker\DataProvider\Reducer\AverageTopReducerTrait;

class JsonPathAverageTopProvider extends AbstractJsonPathReducerProvider
{
    use AverageTopReducerTrait;

    public function __construct(string $baseDir, string $inputFilePath, string $jsonPathQuery, private int $top)
    {
        parent::__construct($baseDir, $inputFilePath, $jsonPathQuery);
    }
}
