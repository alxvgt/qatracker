<?php

declare(strict_types=1);

namespace Alxvng\QATracker\DataProvider\Model;

use Alxvng\QATracker\DataProvider\Finder\ProviderFinder;
use DateTimeImmutable;
use JsonException;
use RuntimeException;

use function Symfony\Component\String\u;

class DataPercentSerie extends AbstractDataSerie
{
    protected AbstractDataSerie $dataSerie;
    protected AbstractDataSerie $totalPercentDataSerie;

    /**
     * DataProvider constructor.
     *
     * @throws JsonException
     */
    public function __construct(array $config, string $generatedDir, array $dataSeriesStack)
    {
        $this->slug = (string) u($config['id'])->kebab()->lower();

        $storageDir = $generatedDir.'/'.static::PROVIDERS_DIR;
        $this->storageFilePath = $storageDir.'/'.$this->getSlug().'.json';

        $this->id = $config['id'];

        $this->dataSerie = ProviderFinder::findById($config['provider'], $dataSeriesStack);
        $this->totalPercentDataSerie = ProviderFinder::findById($config['totalPercentProvider'], $dataSeriesStack);

        $this->validate();
        $this->load();
    }

    /**
     * @throws JsonException
     */
    public function collect(DateTimeImmutable $trackDate, bool $reset): void
    {
        if ($reset) {
            $this->reset();
        }

        $dataSerie = $this->getDataSerie();

        $totalPercentProvider = $this->getTotalPercentDataSerie();

        $value = $dataSerie->getInstance()->fetchData();
        $total = $totalPercentProvider->getInstance()->fetchData();
        $percent = $value * 100 / $total;

        $this->addData($percent, $trackDate);
        $this->save();
    }

    public function getDataSerie(): AbstractDataSerie
    {
        return $this->dataSerie;
    }

    public function getTotalPercentDataSerie(): AbstractDataSerie
    {
        return $this->totalPercentDataSerie;
    }

    protected function validate(): void
    {
        if (!$this->dataSerie instanceof DataStandardSerie) {
            throw new RuntimeException(sprintf('Unable to collect data. %s expect data serie, got %s', DataStandardSerie::class, get_class($this->dataSerie)));
        }

        if ($this->dataSerie->getId() === $this->getId()) {
            throw new RuntimeException('You can\'t refer the data serie itself.');
        }

        if (!$this->totalPercentDataSerie instanceof DataStandardSerie) {
            throw new RuntimeException(sprintf('Unable to collect data. %s expect data serie, got %s', DataStandardSerie::class, get_class($this->totalPercentDataSerie)));
        }

        if ($this->totalPercentDataSerie->getId() === $this->getId()) {
            throw new RuntimeException('You can\'t refer the data serie itself.');
        }
    }
}
