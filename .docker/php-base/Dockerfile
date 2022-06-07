#syntax=docker/dockerfile:1.4

FROM spiralscout/roadrunner:2.9.4 as rr
FROM php:8.1.6-cli-alpine3.15

RUN apk add --no-cache --update \
    libzip-dev curl-dev libxml2-dev icu-dev oniguruma-dev autoconf postgresql-dev alpine-conf \
    && \
    apk add --update --no-cache --virtual .build-deps \
       g++ \
       gcc \
       gnupg \
       libgcc \
       make \
       alpine-sdk \
    && docker-php-ext-install pdo_pgsql sockets \
    && apk del .build-deps

WORKDIR ${WORKDIR}

RUN setup-timezone -z "Europe/Moscow"

# Reset all configs
RUN rm $PHP_INI_DIR/conf.d/*

COPY .docker/php-base/conf.d/ $PHP_INI_DIR/conf.d/

# Composer section
ENV COMPOSER_HOME /var/composer
ENV PHP_MEMORY_LIMIT 512
ENV PHP_MAX_EXECUTION_TIME 300

COPY --from=composer:2.3.5 /usr/bin/composer /usr/bin/composer

RUN mkdir -p /var/composer/cache && chmod 0777 /var/composer/cache
RUN chown -R www-data:www-data /var/composer/cache

RUN chown www-data:www-data /var/www/
RUN chown www-data:www-data /usr/local/bin/

# Copy RoadRunner
COPY --from=rr /usr/bin/rr /usr/bin/rr

USER root
