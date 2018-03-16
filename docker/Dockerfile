FROM php:7.2.3-cli-alpine

RUN apk add --no-cache --virtual .my-deps g++ make autoconf &&\
    apk add --no-cache git bash openssh-client &&\
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer && \
    apk del .my-deps

COPY docker/php.ini /usr/local/etc/php/conf.d

WORKDIR /app