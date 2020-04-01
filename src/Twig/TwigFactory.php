<?php


namespace App\Twig;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigFactory
{
    public const TEMPLATE_DIR = __DIR__.'/../../templates';

    /**
     * @var Environment
     */
    protected static $environment;

    /**
     * @return Environment
     */
    public static function getTwig(): Environment
    {
        if (!static::$environment) {
            $loader = new FilesystemLoader(static::TEMPLATE_DIR);
            static::$environment = new Environment($loader);
        }

        return static::$environment;
    }
}