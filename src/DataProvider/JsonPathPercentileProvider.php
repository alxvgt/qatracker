<?php

namespace Alxvng\QATracker\DataProvider;

use Alxvng\QATracker\DataProvider\Reducer\PercentileReducerTrait;

class JsonPathPercentileProvider extends AbstractJsonPathReducerProvider
{
    use PercentileReducerTrait;

    public function __construct(string $baseDir, string $inputFilePath, string $jsonPathQuery, private int $percentile)
    {
        parent::__construct($baseDir, $inputFilePath, $jsonPathQuery);
    }
}
