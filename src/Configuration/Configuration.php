<?php

namespace Alxvng\QATracker\Configuration;

use Alxvng\QATracker\DataProvider\Model\AbstractDataSerie;
use Alxvng\QATracker\Root\Root;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

class Configuration
{
    public static function exampleConfigPath(): string
    {
        return Root::internal().'/.qatracker.dist/config.yaml';
    }

    /**
     * @param string $configPath
     *
     * @return mixed
     */
    public static function load(string $configPath)
    {
        if (!file_exists($configPath)) {
            $exampleConfig = file_get_contents(static::exampleConfigPath());
            throw new RuntimeException(sprintf("File %s does not exists. You should run :\n#> touch .qatracker/config.yaml\n\nNow edit this file to put your custom configuration, example : \n%s", $configPath, $exampleConfig));
        }

        $baseDir = dirname($configPath);

        $rootConfig = Yaml::parseFile($configPath);

        if (!$rootConfig) {
            throw new RuntimeException(sprintf('No yaml can be parsed from your config file %s', $configPath));
        }

        $config = [];
        if (isset($rootConfig['imports'])) {
            foreach ($rootConfig['imports'] as $import) {
                $config = array_merge_recursive($config, Yaml::parseFile($baseDir.'/'.$import['resource']));
            }
        }
        $config = array_merge_recursive($config, $rootConfig);

        if (!isset($config['qatracker'])) {
            throw new RuntimeException('You must define the root key \'qatracker\' in the config file');
        }

        if (!isset($config['qatracker']['dataSeries'])) {
            throw new RuntimeException('You must define at least one provider in the config file');
        }

        if (!isset($config['qatracker']['charts'])) {
            throw new RuntimeException('You must define at least one chart in the config file');
        }

        $providers = $config['qatracker']['dataSeries'];
        foreach ($providers as $id => $provider) {
            static::validateProvider($id, $provider);
        }

        $charts = $config['qatracker']['charts'];
        foreach ($charts as $id => $chart) {
            static::validateChart($id, $chart);
        }

        $config = static::addIds($config);

        return $config;
    }

    protected static function validateProvider(string $id, array $provider): void
    {
        if (!$id) {
            throw new RuntimeException('You must defined an id for your provider');
        }

        $isValid = false;
        $isValid = $isValid ?: $isValid || static::validateStandardProvider($provider);
        $isValid = $isValid ?: $isValid || static::validatePercentProvider($provider);

        if (!$isValid) {
            throw new RuntimeException('Your providers configuration is not valid');
        }
    }

    protected static function validateChart(string $id, array $chart)
    {
        if (!$id) {
            throw new RuntimeException('You must defined an id for your provider');
        }

        // TODO implement configuration validation for this part
    }

    protected static function validateStandardProvider(array $provider): bool
    {
        if (!AbstractDataSerie::isStandard($provider)) {
            return false;
        }

        $id = $provider['id'];

        $class = $provider['class'] ?? null;
        if (!$class) {
            throw new RuntimeException(sprintf('You must defined a class for your provider "%s"', $id));
        }
        if (!class_exists($class)) {
            throw new RuntimeException(sprintf('You must defined a valid class for your provider, got "%s"', $class));
        }

        $arguments = $provider['arguments'] ?? null;
        if (!$arguments || !is_array($arguments)) {
            throw new RuntimeException(sprintf('You must defined valid arguments for your provider "%s"', $id));
        }

        return true;
    }

    protected static function validatePercentProvider(array $provider): bool
    {
        if (!AbstractDataSerie::isPercent($provider)) {
            return false;
        }

        $id = $provider['id'];

        $percentProvider = $provider['provider'] ?? null;
        if (!is_string($percentProvider)) {
            throw new RuntimeException(sprintf('You must defined the \'provider\' key for your percent provider "%s"', $id));
        }

        $totalPercentProvider = $provider['totalPercentProvider'] ?? null;
        if (!is_string($totalPercentProvider)) {
            throw new RuntimeException(sprintf('You must defined the \'totalPercentProvider\' key for your percent provider "%s"', $id));
        }

        return true;
    }

    protected static function addIds(array $config): array
    {
        $dataSeries = [];
        if (isset($config['qatracker']['dataSeries'])) {
            $dataSeries = $config['qatracker']['dataSeries'];
        }

        foreach ($dataSeries as $id => &$dataSerie) {
            $dataSerie['id'] = $id;
        }
        $config['qatracker']['dataSeries'] = $dataSeries;

        $charts = [];
        if (isset($config['qatracker']['charts'])) {
            $charts = $config['qatracker']['charts'];
        }

        foreach ($charts as $id => &$chart) {
            $chart['id'] = $id;
        }
        $config['qatracker']['charts'] = $charts;

        return $config;
    }
}
