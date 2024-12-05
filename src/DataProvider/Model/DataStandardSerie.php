<?php

namespace Alxvng\QATracker\DataProvider\Model;

use Alxvng\QATracker\DataProvider\DataProviderInterface;
use DateTime;
use JsonException;
use function Symfony\Component\String\u;

class DataStandardSerie extends AbstractDataSerie
{
    protected string $class;
    protected array $arguments;
    private string $baseDir;

    /**
     * DataProvider constructor.
     *
     * @param array $config
     * @param string $baseDir
     * @param string $generatedDir
     *
     * @throws JsonException
     */
    public function __construct(array $config, string $baseDir, string $generatedDir)
    {
        $this->slug = u($config['id'])->kebab()->lower();

        $storageDir = $generatedDir . '/' . static::PROVIDERS_DIR;
        $this->storageFilePath = $storageDir . '/' . $this->getSlug() . '.json';

        $this->id = $config['id'];
        $this->class = $config['class'];
        $this->arguments = $config['arguments'];

        $this->load();
        $this->baseDir = $baseDir;
    }

    /**
     * @throws JsonException
     */
    public function collect(DateTime $trackDate, bool $reset): void
    {
        if ($reset) {
            $this->reset();
        }

        $provider = $this->getInstance();
        $value = $provider->fetchData();
        $this->addData($value, $trackDate);
        $this->save();
    }

    public function getInstance(): DataProviderInterface
    {
        $providerClass = $this->getClass();

        return new $providerClass($this->baseDir, ...$this->getArguments());
    }

    /**
     * @return mixed|string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return array|mixed
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
