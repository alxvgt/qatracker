<?php

namespace App\Configuration;

use RuntimeException;
use Symfony\Component\Yaml\Yaml;

class Configuration
{
    public const EXAMPLE_CONFIG_PATH = __DIR__.'/../../.qatracker.dist/config.yaml';

    /**
     * @param string $configPath
     * @return mixed
     */
    public static function load(string $configPath)
    {
        if (!file_exists($configPath)) {
            $exampleConfig = file_get_contents(static::EXAMPLE_CONFIG_PATH);
            throw new RuntimeException(sprintf("File %s does not exists. You should run :\n#> touch .qatracker/config.yaml\n\nNow edit this file to put your custom configuration, example : \n%s",
                $configPath, $exampleConfig));
        }

        $config = Yaml::parseFile($configPath);

        if (!isset($config['qatracker']['providers'])) {
            throw new RuntimeException('You must define at least one provider in the config file');
        }

        if (!isset($config['qatracker']['charts'])) {
            throw new RuntimeException('You must define at least one chart in the config file');
        }

        $providers = $config['qatracker']['providers'];
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

        $class = $provider['class'] ?? null;
        if (!$class) {
            throw new RuntimeException(sprintf('You must defined a class for your provider "%s"',
                $id));
        }
        if (!class_exists($class)) {
            throw new RuntimeException(sprintf('You must defined a valid class for your provider, got "%s"',
                $class));
        }

        $arguments = $provider ?? null;
        if (!$arguments || !is_array($arguments)) {
            throw new RuntimeException(sprintf('You must defined valid arguments for your provider "%s"',
                $id));
        }
    }

    protected static function validateChart(array $chart)
    {
        // TODO implement configuration validation for this part
        return;
    }
}