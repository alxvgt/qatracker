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
        if (!file_exists($this->inputFilePath)) {
            throw new \RuntimeException(sprintf('Unable to find file at %s', $this->inputFilePath));
        }

        $xml = new SimpleXMLElement(file_get_contents($this->inputFilePath));

        $nodes = $xml->xpath($this->xpathQuery);

        $result = 0;
        foreach ($nodes as $node) {
            $result = $this->reduceMethod($result, (int)$node);
        }

        return $result;
    }

    /**
     * @param int $result
     * @param int $node
     * @return int
     */
    abstract protected function reduceMethod(int $result, int $node): int;
}