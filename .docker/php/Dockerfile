FROM php:7.4

RUN apt-get update && apt-get install -y --no-install-recommends \
    apt-utils \
    git zip unzip \
    libpq-dev \
    && docker-php-ext-install mysqli pdo_mysql pcntl

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

COPY ./ /var/www
WORKDIR /var/www

CMD ["php", "-a"]