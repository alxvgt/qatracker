#!/usr/bin/env php
<?php
// The php.ini setting phar.readonly must be set to 0
$pharFile = 'qatracker.phar';

// clean up
if (file_exists($pharFile)) {
    unlink($pharFile);
}
if (file_exists($pharFile . '.gz')) {
    unlink($pharFile . '.gz');
}

// create phar
$p = new Phar($pharFile);

// creating our library using whole directory
$p->buildFromDirectory('app/');

// pointing main file which requires all classes
$p->setDefaultStub('qatracker.php', '/qatracker.php');

// plus - compressing it into gzip
$p->compress(Phar::GZ);

echo "\n";
echo "$pharFile successfully created";
echo "\n";
echo "\n";
