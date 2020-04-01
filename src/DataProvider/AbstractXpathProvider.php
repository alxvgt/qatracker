<?php


namespace App\DataProvider;

abstract class AbstractXpathProvider implements DataProviderInterface
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

        if (!file_exists($this->inputFilePath)) {
            throw new \RuntimeException(sprintf('Unable to find file at %s', $this->inputFilePath));
        }
    }

    /**
     * @return int
     */
    abstract public function fetchData(): int;
}