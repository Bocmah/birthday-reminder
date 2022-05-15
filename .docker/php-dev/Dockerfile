#syntax=docker/dockerfile:1.4

ARG PHP_BASE_IMAGE

FROM ${PHP_BASE_IMAGE}

WORKDIR /app

COPY . .

RUN --mount=type=cache,target=/var/composer,uid=82,gid=82,mode=777 \
    composer validate --no-check-all --strict && composer install --prefer-dist --optimize-autoloader

RUN chown -R www-data:www-data .

USER www-data

CMD ["/usr/bin/rr", "serve", "-d", "-c", ".rr.yaml"]
