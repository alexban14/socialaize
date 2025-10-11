FROM php:8.4 as php

RUN apt-get update
RUN apt-get install -y unzip libpq-dev libcurl4-gnutls-dev libzip-dev
RUN docker-php-ext-install pdo pdo_pgsql bcmath zip

RUN pecl install -o -f redis \
	&& rm -rf /tmp/pear \
	&& docker-php-ext-enable redis

WORKDIR /var/www/html
COPY ./ .

COPY --from=composer:2.3.5 /usr/bin/composer /usr/bin/composer

COPY .docker/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

COPY .docker/entrypoint.sh /entrypoint
RUN chmod +x /entrypoint

ENV PORT=8000
ENTRYPOINT ["/entrypoint"]
