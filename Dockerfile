FROM php:7.4-fpm

COPY /www /var/www

WORKDIR /var/www

RUN apt-get update \
    && apt-get install -y wget git unzip libpq-dev libicu-dev libpng-dev libzip-dev libjpeg-dev libfreetype6-dev\
    && docker-php-ext-install sqlite3 \
    && docker-php-ext-install pdo_pgsql \
    && docker-php-ext-install pgsql \
    && docker-php-ext-install zip \
    && docker-php-ext-install gd \
    && docker-php-ext-enable pgsql \
    zip \
    nano \
    unzip \
    curl \
    cron

ADD ./docker/php/php.ini /usr/local/etc/php/php.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

COPY --chown=www:www . /var/www

RUN composer install --prefer-dist --no-interaction

COPY .env.example .env

USER www

EXPOSE 9000

RUN  chmod 777 -R  db

USER root

ENTRYPOINT ["sh", "./start.sh"]

