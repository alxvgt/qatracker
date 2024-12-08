<?php

namespace Alxvng\QATracker\Configuration;

use Alxvng\QATracker\DataProvider\Model\AbstractDataSerie;
use Alxvng\QATracker\Root\Root;
use RuntimeException;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Yaml\Yaml;

class Configuration
{
    public static function exampleConfigPath(): string
    {
        return Root::internal() . '/.qatracker.dist/config.yaml';
    }

    public static function load(?string $configPath = null): array
    {
        $configPath = $configPath ?? Root::getConfigPath();

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
                $config = array_merge_recursive($config, Yaml::parseFile($baseDir . '/' . $import['resource']));
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
        $isValid = $isValid ?: $isValid || static::validateStandardProvider($id, $provider);
        $isValid = $isValid ?: $isValid || static::validatePercentProvider($id, $provider);

        if (!$isValid) {
            throw new RuntimeException('Your providers configuration is not valid');
        }
    }

    protected static function validateChart(string $id, array $chart): void
    {
        if (!$id) {
            throw new RuntimeException('You must defined an id for your provider');
        }

        // TODO implement configuration validation for this part
    }

    protected static function validateStandardProvider(string $id, array $provider): bool
    {
        if (!AbstractDataSerie::isStandard($provider)) {
            return false;
        }

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

    protected static function validatePercentProvider(string $id, array $provider): bool
    {
        if (!AbstractDataSerie::isPercent($provider)) {
            return false;
        }

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

    public static function tmpDir(): string
    {
        return self::load()['qatracker']['tmpDir'] ?? '/tmp/qatracker';
    }

    public static function install(): array
    {
        return self::load()['qatracker']['tools'] ?? [];
    }

    public static function analyze(): array
    {
        return self::load()['qatracker']['analyze'] ?? [];
    }

    public static function getGeneratedDir(): string
    {
        return Root::getConfigDir() . '/generated';
    }

    public static function getReportDir(): string
    {
        return self::getGeneratedDir() . '/report';
    }

    public static function getReportFilename(): string
    {
        return self::getReportDir() . '/index.html';
    }

    public static function getDataSeries():array
    {
        return self::load()['qatracker']['dataSeries'];
    }
    public static function getCharts():array
    {
        return self::load()['qatracker']['charts'];
    }

    public static function version(): string
    {
        return '0.6.0';
    }
}
