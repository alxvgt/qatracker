#!/bin/bash
set -x

vendor/bin/php-cs-fixer fix -v --config=./.php-cs-fixer.dist.php
vendor/bin/simple-phpunit --colors=always tests