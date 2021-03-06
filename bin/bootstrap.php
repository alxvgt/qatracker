#!/usr/bin/env php
<?php

function includeIfExists(string $file): bool
{
    return file_exists($file) && include $file;
}

if (
    !includeIfExists(__DIR__ . '/../autoload.php') && // run from composer bin directory as a package
    !includeIfExists(__DIR__ . '/../../../autoload.php') && // run from package bin directory as a package
    !includeIfExists(__DIR__ . '/../vendor/autoload.php') // run as local project
) {
    fwrite(STDERR, 'Install dependencies using Composer and ensure he autoload file has been generated.' . PHP_EOL);
    exit(1);
}