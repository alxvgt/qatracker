<?php


namespace Alxvng\QATracker\DataProvider;

use SimpleXMLElement;

abstract class AbstractXpathReducerProvider extends AbstractXpathProvider implements ReducerProviderInterface
{
    /**
     * @return float
     */
    public function fetchData(): float
    {
        $xml = new SimpleXMLElement(file_get_contents($this->inputFilePath));

        $nodes = $xml->xpath($this->xpathQuery);

        return $this->reduceMethod($nodes);
    }
}