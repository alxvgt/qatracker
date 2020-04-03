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
    ->ignoreDotFiles(false)
    ->exclude(TrackCommand::BASE_DIR.'.dist/'.TrackCommand::GENERATED_DIR)
    ->exclude(TrackCommand::BASE_DIR)
    ->exclude('docker')
    ->exclude('docs')
    ->exclude('tests')
    ->notName('*.loc')
    ->notName(['*.png', '*.jpg', '*.jpeg'])
    ->notName('*.sh')
    ->notName('*.phar')
    ->notName('*.phar.gz')
    ->notName('composer.*')
    ->notName('.gitignore')
    ->notName('create-phar.php')
    ->notContains('PHPUnit\Framework\TestCase')
    ;

$p->buildFromIterator($finder->getIterator(), __DIR__);
$p->setDefaultStub('qatracker.php', '/qatracker.php');
$p->compress(Phar::GZ);

echo "\n";
echo "\n== Archived files ==\n";

$children = $p->getChildren();
/** @var PharFileInfo $child */
foreach ($children as $child) {
    echo '> '.$child->getFilename()."\n";
}

echo "\n";
echo sprintf(
    'Phar archive : "%s" (%s) successfully created',
    $pharFile,
    human_filesize(filesize($pharFile))
);
echo "\n";
echo "\n";


/** ---------------------------------- */

/**
 * @param     $bytes
 * @param int $decimals
 * @return string
 */
function human_filesize($bytes, $decimals = 2)
{
    $sz = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);

    return sprintf("%.{$decimals}f", $bytes / (1024 ** $factor)).@$sz[$factor];
}