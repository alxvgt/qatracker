<?php


namespace App\DataProvider;

use JsonException;

abstract class AbstractJsonPathProvider implements DataProviderInterface
{
    public const MIME_TYPES = [
        'application/json',
        'text/plain',
    ];

    protected string $inputFilePath;
    protected string $jsonPathQuery;

    /**
     * XpathProvider constructor.
     * @param string $inputFilePath
     * @param string $xpathQuery
     */
    public function __construct(string $inputFilePath, string $xpathQuery)
    {
        $this->inputFilePath = $inputFilePath;
        $this->jsonPathQuery = $xpathQuery;

        if (!file_exists($this->inputFilePath)) {
            throw new \RuntimeException(sprintf('Unable to find file at %s', $this->inputFilePath));
        }

        if (!in_array(mime_content_type($this->inputFilePath), static::MIME_TYPES, true)) {
            throw new \RuntimeException(
                sprintf(
                    'The file %s (%s) must have one the mime types : %s',
                    $this->inputFilePath,
                    mime_content_type($this->inputFilePath),
                    implode(',', static::MIME_TYPES)
                ));
        }

        try {
            json_decode(file_get_contents($this->inputFilePath), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new \RuntimeException(sprintf(
                'The file %s seems does not contain valid json data',
                $this->inputFilePath
            ));
        }
    }
}