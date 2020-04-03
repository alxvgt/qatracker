<?php

namespace App\Root;

class Root
{
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
}