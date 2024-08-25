FROM php:8.3.7-apache
COPY docker/php/php-development.ini /usr/local/etc/php/php.ini

RUN apt-get update && apt-get install -y \
    git \
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

# The wait-for-it.sh script 
COPY docker/wait-for-it.sh /usr/local/bin/wait-for-it.sh
RUN chmod +x /usr/local/bin/wait-for-it.sh

# DB Evolution script/s
COPY docker/postgres/dbevo.php /usr/local/bin/
RUN mkdir /tmp/store-init-sql/
COPY docker/postgres/sql/ /tmp/dbevo-sql/

# Get Composer
ENV COMPOSER_ROOT_VERSION=1.0.0
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY src/composer.json /var/www/html
RUN cd /var/www/html && composer update

# Apache
RUN a2enmod rewrite
COPY docker/apache/academistore.conf /etc/apache2/sites-available/000-default.conf

# Context for next ops. Also jump directly into this path when exec bash.
WORKDIR /var/www/html/

# Use wait-for-it.sh to wait for PostgreSQL to be ready, then start the PHP script and Apache
CMD ["sh", "-c", "/usr/local/bin/wait-for-it.sh postgres:5432 -- php /usr/local/bin/dbevo.php && apache2-foreground"]