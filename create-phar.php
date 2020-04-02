#!/usr/bin/env php
<?php

use App\Command\TrackCommand;
use Symfony\Component\Finder\Finder;

require __DIR__.'/vendor/autoload.php';

// The php.ini setting phar.readonly must be set to 0
$pharFile = 'qatracker.phar';

// clean up
if (file_exists($pharFile)) {
    unlink($pharFile);
}
if (file_exists($pharFile.'.gz')) {
    unlink($pharFile.'.gz');
}

$p = new Phar($pharFile);

$finder = new Finder();
$finder
    ->files()
    ->in(__DIR__)
    ->exclude(TrackCommand::getBaseDir())
    ->exclude(TrackCommand::getBaseDir().'.dist/'.TrackCommand::GENERATED_DIR)
    ->exclude('docker')
    ->exclude('*.loc')
    ->notName('*.phar')
    ->notName('*.phar.gz')
    ->notName('create-phar.php');

$p->buildFromIterator($finder->getIterator(), __DIR__);
$p->setDefaultStub('qatracker.php', '/qatracker.php');
$p->compress(Phar::GZ);

echo "\n";
echo "Phar archive : \"$pharFile\" successfully created";
echo "\n";
echo "\n";
