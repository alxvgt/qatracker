<?php


namespace App\DataProvider;

use Flow\JSONPath\JSONPath;
use Flow\JSONPath\JSONPathException;
use JsonException;
use RuntimeException;

class JsonPathProvider extends AbstractJsonPathProvider
{
    /**
     * @return float
     * @throws JSONPathException
     * @throws JsonException
     */
    public function fetchData(): float
    {
        $data = json_decode(file_get_contents($this->inputFilePath), true, 512, JSON_THROW_ON_ERROR);
        $jsonFinder = new JSONPath($data);

        $nodes = $jsonFinder->find($this->jsonPathQuery);

        $result = reset($nodes);

        if(!is_numeric((string)$result)){
            throw new RuntimeException(sprintf('The result of must be a numeric value, got "%s"', $result));
        }

        return (float)$result;
    }
}