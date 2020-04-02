<?php


namespace App\DataProvider;

abstract class AbstractXpathProvider implements DataProviderInterface
{
    public const NS_PREFIX = 'prefix';
    public const NS_VALUE = 'namespace';

    protected string $inputFilePath;
    protected string $xpathQuery;
    protected array $namespaceParameters;

    /**
     * XpathProvider constructor.
     * @param string $inputFilePath
     * @param string $xpathQuery
     * @param array  $namespaceParameters
     */
    public function __construct(string $inputFilePath, string $xpathQuery, array $namespaceParameters = [])
    {
        $this->inputFilePath = $inputFilePath;
        $this->xpathQuery = $xpathQuery;
        $this->namespaceParameters = $namespaceParameters;

        if (!file_exists($this->inputFilePath)) {
            throw new \RuntimeException(sprintf('Unable to find file at %s', $this->inputFilePath));
        }


    }
}