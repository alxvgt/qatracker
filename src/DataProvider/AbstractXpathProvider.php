<?php


namespace App\DataProvider;

use RuntimeException;
use SimpleXMLElement;

abstract class AbstractXpathProvider implements DataProviderInterface
{
    public const MIME_TYPES = [
        'application/xml',
        'text/xml',
        'text/plain',
    ];

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
            throw new RuntimeException(sprintf('Unable to find file at %s', $this->inputFilePath));
        }

        if (!in_array(mime_content_type($this->inputFilePath), static::MIME_TYPES, true)) {
            throw new RuntimeException(
                sprintf(
                    'The file %s (%s) must have one the mime types : %s',
                    $this->inputFilePath,
                    mime_content_type($this->inputFilePath),
                    implode(',', static::MIME_TYPES)
                ));
        }

        try{
            $xml = new SimpleXMLElement(file_get_contents($this->inputFilePath));
        } catch (\Exception $e){
            throw new \RuntimeException(sprintf(
                'The file %s seems does not contain valid xml data',
                $this->inputFilePath
            ));
        }

    }
}