#!/bin/bash
composer install --no-dev --profile
composer dump-autoload --no-dev --profile
php bin/build-phar
chmod +x release/*
