FROM php:7.4-fpm

ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/
RUN chmod uga+x /usr/local/bin/install-php-extensions && sync && install-php-extensions gd xdebug

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev
RUN docker-php-ext-install zip

ADD https://raw.githubusercontent.com/alxvgt/init-debian/master/init-light.sh /usr/local/bin/
RUN chmod uga+x /usr/local/bin/init-light.sh && bash /usr/local/bin/init-light.sh

ADD https://raw.githubusercontent.com/alxvgt/init-debian/master/init-composer.sh /usr/local/bin/
RUN chmod uga+x /usr/local/bin/init-composer.sh && bash /usr/local/bin/init-composer.sh

ADD https://raw.githubusercontent.com/alxvgt/init-debian/master/init-symfony-cli.sh /usr/local/bin/
RUN chmod uga+x /usr/local/bin/init-symfony-cli.sh && bash /usr/local/bin/init-symfony-cli.sh && mv /root/.symfony/bin/symfony /usr/local/bin/symfony