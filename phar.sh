#!/bin/bash

composer dump-autoload --no-dev --profile
php create-phar.php