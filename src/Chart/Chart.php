<?php

namespace Alxvng\QATracker\Chart;

use Alxvng\QATracker\DataProvider\Model\AbstractDataSerie;
use Alxvng\QATracker\DataProvider\Model\DataStandardSerie;
use RuntimeException;

class Chart
{
    protected array $providers;
    protected array $graphSettings;
    protected string $type;

    /**
     * Chart constructor.
     *
     * @param array $config
     * @param array $providersStack
     */
    public function __construct(array $config, array $providersStack)
    {
        $this->type = $config['type'];
        $this->graphSettings = $config['graphSettings'];
        $this->providers = array_intersect_key($providersStack, array_flip($config['dataSeries']));

        if (empty($this->providers)) {
            throw new RuntimeException('You must define at least one valid data serie for chart.');
        }
    }

    public function getProviders(): array
    {
        return $this->providers;
    }

    public function getChartValues(): array
    {
        $values = [];
        $serieNames = \array_keys($this->providers);
        /**
         * @var srting $serieName
         * @var DataStandardSerie $serie
         */
        foreach ($this->providers as $serieName => $serie) {
            foreach ($serie->getData() as $date => $metric) {
                $values[$date][0] = $date;
                $values[$date][array_search($serieName, $serieNames)+1] = $metric;
            }
        }

        return \array_values($values);
    }

    public function getChartStructure(): array
    {
        $firstMeasure = $this->getChartValues();
        $firstMeasure = \reset($firstMeasure);
        return [
            'structure' => [
                'key' => 0,
                'value' => range(1, (count($firstMeasure) - 1)),
            ],
        ];
    }

    public function getFirstProvider(): AbstractDataSerie
    {
        $providers = $this->getProviders();

        return reset($providers);
    }

    /**
     * @return array|mixed
     */
    public function getGraphSettings()
    {
        return $this->graphSettings;
    }

    /**
     * @return mixed|string
     */
    public function getType()
    {
        return $this->type;
    }
}
