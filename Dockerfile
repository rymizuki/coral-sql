FROM php:7.3

RUN apt-get update \
 && apt-get install -y git libzip-dev \
 && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php \
 && mv composer.phar /usr/local/bin/composer \
 && composer config -g repos.packagist composer https://packagist.jp \
 && composer global require hirak/prestissimo

WORKDIR /usr/local/docker/app

RUN composer install
