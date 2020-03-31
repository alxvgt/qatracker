<?php
namespace App\Configuration;

use RuntimeException;
use Symfony\Component\Yaml\Yaml;

class ConfigurationLoader
{

    /**
     * @param string $configPath
     * @return mixed
     */
    public static function load(string $configPath)
    {
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