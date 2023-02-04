#syntax=docker/dockerfile:1.4

##
## Base image
##

FROM php:8.1.15-cli-alpine3.17 AS base

RUN apk add --update \
    alpine-conf \
    autoconf \
    curl-dev \
    icu-data-full \
    icu-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    && \
    apk add --update --virtual .build-deps \
      build-base \
      linux-headers \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
       intl \
       opcache \
       sockets \
    && pecl install \
        mongodb \
        pcov \
    && docker-php-ext-enable  \
        mongodb \
        pcov \
    && apk del .build-deps

ENV EXCEPTION_IGNORE_ARGS=Off \
    ERROR_REPORTING=E_ALL \
    DISPLAY_ERRORS=On \
    DISPLAY_STARTUP_ERRORS=On \
    LOG_ERRORS=On \
    OPCACHE_ENABLE=1 \
    PCOV_ENABLE=0 \
    PCOV_DIRECTORY="/app/src"

WORKDIR /app

RUN setup-timezone -z "Europe/Moscow"

RUN rm $PHP_INI_DIR/conf.d/*

COPY .docker/php/conf.d/ $PHP_INI_DIR/conf.d/

# Composer section
ENV COMPOSER_HOME /var/composer

RUN mkdir ${COMPOSER_HOME}

COPY --link --from=composer:2.5.1 /usr/bin/composer /usr/bin/composer

COPY --link --from=spiralscout/roadrunner:2.12.2 /usr/bin/rr /usr/bin/rr

##
## Dev image
##

FROM base AS dev

ENV APP_ENV=dev \
    OPCACHE_ENABLE=0 \
    PCOV_ENABLE=1 \
    XDEBUG_MODE=debug

## Install xdebug
RUN apk add --update --virtual .build-deps \
      build-base \
      linux-headers \
    && pecl install xdebug-3.2.0 \
    && docker-php-ext-enable xdebug \
    && apk del .build-deps

COPY .docker/php/conf.d.dev/xdebug.ini $PHP_INI_DIR/conf.d/

COPY . .

RUN composer validate --no-check-all --strict && composer install --prefer-dist --optimize-autoloader

RUN chown -R www-data:www-data . /var/composer

USER www-data

CMD ["/usr/bin/rr", "serve", "-d", "-c", ".rr.dev.yaml"]

##
## Test image
##

FROM base AS test

ENV APP_ENV=test \
    PCOV_ENABLE=1

## Psalm and Infection need it
RUN apk add --update git

COPY . .

RUN composer validate --no-check-all --strict && composer install --prefer-dist --optimize-autoloader

RUN chown -R www-data:www-data . /var/composer

USER www-data

CMD ["/usr/bin/rr", "serve", "-d", "-c", ".rr.test.yaml"]

##
## Prod image
##

FROM base AS prod

ENV APP_ENV=prod \
    EXCEPTION_IGNORE_ARGS=On \
    ERROR_REPORTING="E_ALL & ~E_DEPRECATED & ~E_STRICT" \
    DISPLAY_ERRORS=Off \
    DISPLAY_STARTUP_ERRORS=Off

COPY . .

RUN composer validate --no-check-all --strict && composer install --no-dev --prefer-dist --optimize-autoloader

RUN chown -R www-data:www-data . /var/composer

USER www-data

CMD ["/usr/bin/rr", "serve", "-d", "-c", ".rr.yaml"]
