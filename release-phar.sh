#!/bin/bash
composer dump-autoload --no-dev --profile
php bin/build-phar
