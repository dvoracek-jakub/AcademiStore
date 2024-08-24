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

# Copy the wait-for-it.sh script into the container
COPY wait-for-it.sh /usr/local/bin/wait-for-it.sh
RUN chmod +x /usr/local/bin/wait-for-it.sh

# DB Evolution script/s
COPY postgres/dbevo.php /usr/local/bin/
RUN mkdir /tmp/store-init-sql/
COPY postgres/sql/ /tmp/store-init-sql/

RUN a2enmod rewrite

# Rewrite apache default config with our virutal
COPY apache/academistore.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html/

# Use wait-for-it.sh to wait for PostgreSQL to be ready, then start the PHP script and Apache
CMD ["sh", "-c", "/usr/local/bin/wait-for-it.sh postgres:5432 -- php /usr/local/bin/dbevo.php && apache2-foreground"]