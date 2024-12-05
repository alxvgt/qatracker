<?php

namespace Alxvng\QATracker\Root;

class Root
{
    public const BASE_DIR = '.qatracker';
    public const CONFIG_FILENAME = 'config.yaml';
    /**
     * @return string
     */
    public static function internal(): string
    {
        return __DIR__;
    }

    /**
     * @return string
     */
    public static function external(): string
    {
        return getcwd();
    }


    public static function getConfigDir(): string
    {
        return self::external() . '/' . static::BASE_DIR;
    }

    public static function getConfigPath(): string
    {
        return self::getConfigDir() . '/' . static::CONFIG_FILENAME;
    }
}