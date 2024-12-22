<?php

declare(strict_types=1);

namespace Alxvng\QATracker\DataProvider\Model;

use Alxvng\QATracker\DataProvider\DataProviderInterface;
use DateTimeImmutable;
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
     * @throws JsonException
     */
    public function __construct(array $config, string $baseDir, string $generatedDir)
    {
        $this->slug = (string) u($config['id'])->kebab()->lower();

        $storageDir = $generatedDir.'/'.static::PROVIDERS_DIR;
        $this->storageFilePath = $storageDir.'/'.$this->getSlug().'.json';

        $this->id = $config['id'];
        $this->class = $config['class'];
        $this->arguments = $config['arguments'];

        $this->load();
        $this->baseDir = $baseDir;
    }

    /**
     * @throws JsonException
     */
    public function collect(DateTimeImmutable $trackDate, bool $reset): void
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
