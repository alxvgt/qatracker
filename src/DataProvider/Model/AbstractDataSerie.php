<?php


namespace App\DataProvider\Model;


use App\Command\TrackCommand;
use JsonException;

abstract class AbstractDataSerie
{
    public const PROVIDERS_DIR = 'providers-data';
    public const DATE_FORMAT = 'YmdHis';

    protected string $storageFilePath;
    protected string $id;
    protected string $slug;
    protected array $data = [];

    /**
     * @param array $providerConfig
     * @return bool
     */
    public static function isStandard(array $providerConfig): bool
    {
        return isset($providerConfig['class'], $providerConfig['arguments']);
    }

    /**
     * @param array $providerConfig
     * @return bool
     */
    public static function isPercent(array $providerConfig): bool
    {
        return isset($providerConfig['provider'], $providerConfig['totalPercentProvider']);
    }

    /**
     * @param $value
     */
    public function addData($value): void
    {
        $trackDate = TrackCommand::getTrackDate();
        $this->data[$trackDate->format(static::DATE_FORMAT)] = $value;
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
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

        file_put_contents($this->getStorageFilePath(), json_encode($this->data, JSON_THROW_ON_ERROR, 512));
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

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

    /**
     * @return string
     */
    public function getStorageFilePath(): string
    {
        return $this->storageFilePath;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    abstract public function collect(): void;
}