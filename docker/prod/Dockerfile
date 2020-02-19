# Build project
FROM currency-converter/toolkit:latest

WORKDIR /var/www/html

COPY ./application .

RUN composer install --no-dev

# Build container
FROM php:7.4-fpm-alpine

COPY --from=0 /var/www/html /var/www/html
COPY ./docker/prod/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./docker/prod/php-fpm/php.ini /usr/local/etc/php/php.ini