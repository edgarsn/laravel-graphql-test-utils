FROM php:8.3-cli-alpine

COPY --from=mlocati/php-extension-installer:2.7 /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
      pcov

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/

COPY ../ /app

WORKDIR /app
