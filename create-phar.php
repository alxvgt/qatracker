#!/usr/bin/env php
<?php
// The php.ini setting phar.readonly must be set to 0
$pharFile = 'trackqa.phar';

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
$p->setDefaultStub('track.php', '/track.php');

// plus - compressing it into gzip
$p->compress(Phar::GZ);

echo "$pharFile successfully created";
echo '';