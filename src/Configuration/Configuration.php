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

        if (!isset($config['qatracker']['series'])) {
            throw new RuntimeException('You must define one serie at least a path qatracker[series] in the config file');
        }

        $series = $config['qatracker']['series'];
        foreach ($series as $serie) {
            $name = $serie['name'] ?? null;
            if (!$name) {
                throw new RuntimeException('You must defined a name for your data serie');
            }

            $provider = $serie['provider'] ?? null;
            if (!$provider) {
                throw new RuntimeException(sprintf('You must defined a provider class for your data serie "%s"',
                    $name));
            }
            if (!class_exists($provider)) {
                throw new RuntimeException(sprintf('You must defined a valid provider class for your data serie, got "%s"',
                    $provider));
            }

            $arguments = $serie['arguments'] ?? null;
            if (!$arguments || !is_array($arguments)) {
                throw new RuntimeException(sprintf('You must defined a valid provider arguments for your data serie "%s"',
                    $name));
            }
        }

        return $config;
    }
}