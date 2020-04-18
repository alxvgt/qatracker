<?php


namespace Alxvng\QATracker\DataProvider;

use SimpleXMLElement;

class XpathProvider extends AbstractXpathProvider
{
    /**
     * @return float
     */
    public function fetchData(): float
    {
        $xml = new SimpleXMLElement(file_get_contents($this->inputFilePath));

        if (!empty($this->namespaceParameters) &&
            isset($this->namespaceParameters[static::NS_PREFIX], $this->namespaceParameters[static::NS_VALUE])
        ) {
            $xml->registerXPathNamespace($this->namespaceParameters[static::NS_PREFIX],
                $this->namespaceParameters[static::NS_VALUE]);
        }

        $result = $xml->xpath($this->xpathQuery);
        $result = reset($result);

        if (!is_numeric((string)$result)) {
            throw new \RuntimeException(sprintf('The result of must be a numeric value, got "%s"', $result));
        }

        return (float)$result;
    }
}