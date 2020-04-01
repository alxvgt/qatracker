<?php


namespace App\DataProvider;

use SimpleXMLElement;

class XpathProvider implements DataProviderInterface
{
    protected string $inputFilePath;
    protected string $xpathQuery;

    /**
     * XpathProvider constructor.
     * @param string $inputFilePath
     * @param string $xpathQuery
     */
    public function __construct(string $inputFilePath, string $xpathQuery)
    {
        $this->inputFilePath = $inputFilePath;
        $this->xpathQuery = $xpathQuery;
    }

    /**
     * @return int
     */
    public function fetchData(): int
    {
        if (!file_exists($this->inputFilePath)) {
            throw new \RuntimeException(sprintf('Unable to find file at %s', $this->inputFilePath));
        }

        $xml = new SimpleXMLElement(file_get_contents($this->inputFilePath));

        $result = $xml->xpath($this->xpathQuery);
        $result = reset($result);

        if(!is_numeric((string)$result)){
            throw new \RuntimeException(sprintf('The result of must be a numeric value, got "%s"', $result));
        }

        return (int)$result;
    }
}