FROM php:7.3

COPY ./docker-dev/php.ini /usr/local/etc/php

RUN apt-get update \
 && apt-get install -y git libzip-dev \
 && docker-php-ext-install zip
RUN pecl install xdebug \
 && docker-php-ext-enable xdebug

WORKDIR /usr/local/docker/app

COPY ./composer.json /usr/local/docker/app
COPY ./composer.lock /usr/local/docker/app

RUN curl -sS https://getcomposer.org/installer | php \
 && mv composer.phar /usr/local/bin/composer \
 && composer config -g repos.packagist composer https://packagist.jp

COPY ./src /usr/local/docker/app/src
COPY ./tests /usr/local/docker/app/tests
COPY ./codeception.yml /usr/local/docker/app

RUN composer install
