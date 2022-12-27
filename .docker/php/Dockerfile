#syntax=docker/dockerfile:1.4

##
## Base image
##

FROM php:8.1.7-cli-alpine3.16 AS base

RUN apk add --no-cache --update \
    alpine-conf \
    autoconf \
    curl-dev \
    icu-data-full \
    icu-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    && \
    apk add --update --no-cache --virtual .build-deps \
       alpine-sdk\
       gcc \
       gnupg \
       g++ \
       libgcc \
       make \
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

# Reset all configs
RUN rm $PHP_INI_DIR/conf.d/*

COPY .docker/php/conf.d/ $PHP_INI_DIR/conf.d/

# Composer section
ENV COMPOSER_HOME /var/composer

COPY --from=composer:2.3.7 /usr/bin/composer /usr/bin/composer

RUN mkdir -p ${COMPOSER_HOME}/cache \
    && chmod 0777 ${COMPOSER_HOME}/cache  \
    && chown -R www-data:www-data ${COMPOSER_HOME}/cache

RUN chown www-data:www-data /var/www/ /usr/local/bin/

# Copy RoadRunner
COPY --from=spiralscout/roadrunner:2.10.5 /usr/bin/rr /usr/bin/rr

##
## Dev image
##

FROM base AS dev

ENV APP_ENV=dev \
    OPCACHE_ENABLE=0 \
    PCOV_ENABLE=1

COPY . .

RUN --mount=type=cache,target=/var/composer,uid=82,gid=82,mode=777 \
    composer validate --no-check-all --strict && composer install --prefer-dist --optimize-autoloader

RUN chown -R www-data:www-data .

USER www-data

CMD ["/usr/bin/rr", "serve", "-d", "-c", ".rr.dev.yaml"]

##
## Test image
##

FROM base AS test

ENV APP_ENV=test \
    PCOV_ENABLE=1

## Psalm and Infection need it
RUN apk add git

COPY . .

RUN --mount=type=cache,target=/var/composer,uid=82,gid=82,mode=777 \
    composer validate --no-check-all --strict && composer install --prefer-dist --optimize-autoloader

RUN chown -R www-data:www-data .

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

RUN --mount=type=cache,target=/var/composer,uid=82,gid=82,mode=777 \
    composer validate --no-check-all --strict && composer install --no-dev --prefer-dist --optimize-autoloader

RUN chown -R www-data:www-data .

USER www-data

CMD ["/usr/bin/rr", "serve", "-d", "-c", ".rr.yaml"]
