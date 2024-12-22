<?php

declare(strict_types=1);

namespace Alxvng\QATracker\Twig;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigFactory
{
    public const TEMPLATE_DIR = __DIR__.'/../../templates';

    /**
     * @var Environment
     */
    protected static $environment;

    public static function getTwig(): Environment
    {
        if (!static::$environment) {
            $loader = new FilesystemLoader(static::TEMPLATE_DIR);
            static::$environment = new Environment($loader);
        }

        return static::$environment;
    }
}
