<?php


namespace App\Chart;


use App\DataProvider\Model\DataProvider;

class Chart
{
    protected array $providers;
    protected bool $withHistory = true;
    protected array $graphSettings;
    protected string $type;

    /**
     * Chart constructor.
     * @param array $config
     * @param array $providersStack
     */
    public function __construct(array $config, array $providersStack)
    {
        $this->type = $config['type'];
        $this->withHistory = $config['withHistory'];
        $this->graphSettings = $config['graphSettings'];
        $this->providers = array_intersect_key($providersStack, array_flip($config['providers']));
    }

    /**
     * @return array
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * @return bool
     */
    public function withHistory(): bool
    {
        return $this->withHistory;
    }

    /**
     * @return DataProvider
     */
    public function getFirstProvider(): DataProvider
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