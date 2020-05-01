<?php

namespace Alxvng\QATracker\DataProvider\Model;

use DateTime;
use JsonException;
use RuntimeException;

abstract class AbstractDataSerie
{
    public const PROVIDERS_DIR = 'providers-data';
    public const DATE_FORMAT = 'YmdHis';

    protected string $storageFilePath;
    protected string $id;
    protected string $slug;
    protected array $data = [];

    public static function isStandard(array $providerConfig): bool
    {
        return isset($providerConfig['class'], $providerConfig['arguments']);
    }

    public static function isPercent(array $providerConfig): bool
    {
        return isset($providerConfig['provider'], $providerConfig['totalPercentProvider']);
    }

    /**
     * @param $value
     * @param DateTime $trackDate
     */
    public function addData($value, DateTime $trackDate): void
    {
        $this->data[$trackDate->format(static::DATE_FORMAT)] = round($value, 2);
        $data = $this->data;
        ksort($data);
        $this->data = $data;
    }

    /**
     * @throws JsonException
     */
    public function save()
    {
        $dir = dirname($this->getStorageFilePath());
        if (!is_dir($dir) && !mkdir($dir, 0777, true)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

        file_put_contents($this->getStorageFilePath(), json_encode($this->data, JSON_THROW_ON_ERROR, 512));
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getStorageFilePath(): string
    {
        return $this->storageFilePath;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getData(): array
    {
        return $this->data;
    }

    abstract public function collect(DateTime $trackDate): void;

    /**
     * @throws JsonException
     */
    protected function load()
    {
        if (!file_exists($this->getStorageFilePath())) {
            return;
        }

        $this->data = json_decode(file_get_contents($this->getStorageFilePath()), true, 512, JSON_THROW_ON_ERROR);
    }
}
