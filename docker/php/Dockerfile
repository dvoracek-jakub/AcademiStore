FROM php:8.3.7-apache
COPY php/php-development.ini /usr/local/etc/php/php.ini

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-ext-install iconv \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install curl

RUN a2enmod rewrite