<?php


namespace App\DataProvider;

use Flow\JSONPath\JSONPath;

abstract class AbstractJsonPathReducerProvider extends AbstractJsonPathProvider
{
    /**
     * @return int
     */
    public function fetchData(): int
    {
        $data = json_decode(file_get_contents($this->inputFilePath), true, 512, JSON_THROW_ON_ERROR);
        $jsonFinder = new JSONPath($data);

        $nodes = $jsonFinder->find($this->jsonPathQuery);

        return $this->reduceMethod(iterator_to_array($nodes));
    }

    /**
     * @param array $nodes
     * @return int
     */
    abstract protected function reduceMethod(array $nodes): int;
}