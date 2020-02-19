FROM php:7.4-cli-alpine

# install git and bash
RUN apk update && \
    apk upgrade && \
    apk add git bash

# install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer

# install symfony cli tool
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony/bin/symfony /usr/local/bin/symfony
