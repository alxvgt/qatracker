#!/bin/bash

composer install && \
echo 'phar.readonly=0' > /tmp/docker-php-phar-readonly.ini && \
gosu root mv -f /tmp/docker-php-phar-readonly.ini  /etc/php/8.3/cli/conf.d/docker-php-phar-readonly.ini && \
bin/build-phar