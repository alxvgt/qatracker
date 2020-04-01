<?php


namespace App\DataProvider;

use SimpleXMLElement;

abstract class AbstractXpathReducerProvider extends AbstractXpathProvider
{
    /**
     * @return int
     */
    public function fetchData(): int
    {
        $xml = new SimpleXMLElement(file_get_contents($this->inputFilePath));

        $nodes = $xml->xpath($this->xpathQuery);

        return $this->reduceMethod($nodes);
    }

    /**
     * @param array $nodes
     * @return int
     */
    abstract protected function reduceMethod(array $nodes): int;
}