FROM php:7.3.4-apache-stretch

RUN useradd delain

COPY . /home/delain/delain/web/
COPY ./docker/apache2.conf /etc/apache2/apache2.conf
COPY ./docker/000-default.conf /etc/apache2/sites-available
COPY ./docker/conf.php /home/delain/delain/web/www/includes/conf.php

RUN chown -R delain: /etc/apache2/sites-available
RUN apt-get update
RUN apt-get install -y libpq-dev libz-dev libmemcached-dev
RUN docker-php-ext-install pdo pdo_pgsql
RUN pecl install memcached
RUN  docker-php-ext-enable memcached

