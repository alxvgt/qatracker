<?php

namespace Alxvng\QATracker\DataProvider\Model;

use Alxvng\QATracker\DataProvider\DataProviderInterface;
use DateTime;
use JsonException;
use Symfony\Component\String\Slugger\AsciiSlugger;
use function Symfony\Component\String\u;

class DataStandardSerie extends AbstractDataSerie
{
    protected string $class;
    protected array $arguments;

    /**
     * DataProvider constructor.
     *
     * @param array  $config
     * @param string $generatedDir
     *
     * @throws JsonException
     */
    public function __construct(array $config, string $generatedDir)
    {
        $slugger = new AsciiSlugger();
        $this->slug = u($slugger->slug($config['id']))->lower();

        $storageDir = $generatedDir.'/'.static::PROVIDERS_DIR;
        $this->storageFilePath = $storageDir.'/'.$this->getSlug().'.json';

        $this->id = $config['id'];
        $this->class = $config['class'];
        $this->arguments = $config['arguments'];

        $this->load();
    }

    /**
     * @param DateTime $trackDate
     *
     * @throws JsonException
     */
    public function collect(DateTime $trackDate): void
    {
        $provider = $this->getInstance();
        $value = $provider->fetchData();
        $this->addData($value, $trackDate);
        $this->save();
    }

    public function getInstance(): DataProviderInterface
    {
        $providerClass = $this->getClass();

        return new $providerClass(...$this->getArguments());
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
