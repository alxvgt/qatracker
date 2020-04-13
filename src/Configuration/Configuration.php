<?php

namespace App\Configuration;

use App\DataProvider\Model\AbstractDataSerie;
use App\Root\Root;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

class Configuration
{
    /**
     * @return string
     */
    public static function exampleConfigPath(): string
    {
        return Root::internal().'/.qatracker.dist/config.yaml';
    }

    /**
     * @param string $configPath
     * @return mixed
     */
    public static function load(string $configPath)
    {
        if (!file_exists($configPath)) {
            $exampleConfig = file_get_contents(static::exampleConfigPath());
            throw new RuntimeException(sprintf("File %s does not exists. You should run :\n#> touch .qatracker/config.yaml\n\nNow edit this file to put your custom configuration, example : \n%s",
                $configPath, $exampleConfig));
        }

        $config = Yaml::parseFile($configPath);

        if (!isset($config['qatracker']['dataSeries'])) {
            throw new RuntimeException('You must define at least one provider in the config file');
        }

        if (!isset($config['qatracker']['charts'])) {
            throw new RuntimeException('You must define at least one chart in the config file');
        }

        $providers = $config['qatracker']['dataSeries'];
        foreach ($providers as $provider) {
            static::validateProvider($provider);
        }

        $charts = $config['qatracker']['charts'];
        foreach ($charts as $chart) {
            static::validateChart($chart);
        }

        return $config;
    }

    /**
     * @param $provider
     */
    protected static function validateProvider(array $provider): void
    {
        $id = $provider['id'] ?? null;
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

    protected static function validateChart(array $chart)
    {
        // TODO implement configuration validation for this part
        return;
    }

    /**
     * @param array $provider
     * @return bool
     */
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
            throw new RuntimeException(sprintf('You must defined a valid class for your provider, got "%s"',
                $class));
        }

        $arguments = $provider['arguments'] ?? null;
        if (!$arguments || !is_array($arguments)) {
            throw new RuntimeException(sprintf('You must defined valid arguments for your provider "%s"',
                $id));
        }

        return true;
    }

    /**
     * @param array $provider
     * @return bool
     */
    protected static function validatePercentProvider(array $provider): bool
    {
        if (!AbstractDataSerie::isPercent($provider)) {
            return false;
        }

        return true;
    }
}