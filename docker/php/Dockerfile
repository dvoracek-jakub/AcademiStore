FROM php:8.3.7-apache
COPY docker/php/php-development.ini /usr/local/etc/php/php.ini

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libicu-dev \
    libpng-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-ext-install iconv \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install curl \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-enable intl

# The wait-for-it script
COPY docker/wait-for-it.sh /usr/local/bin/wait-for-it.sh
RUN chmod +x /usr/local/bin/wait-for-it.sh

# Context for next ops. Also jump directly into this path when exec bash.
WORKDIR /var/www/html/

# DB Evolution script/s
ARG DBEVO_LOG
ARG DBEVO_SQL_PATH
ARG DBEVO_LOGFILE
COPY docker/postgres/dbevo.php /tmp/
RUN mkdir ${DBEVO_SQL_PATH}
COPY docker/postgres/sql/ ${DBEVO_SQL_PATH}

# Get Composer
ENV COMPOSER_ROOT_VERSION=1.0.0
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY src/composer.json /var/www/html
RUN cd /var/www/html && composer update --no-interaction

# Apache
RUN a2enmod rewrite
COPY docker/apache/academistore.conf /etc/apache2/sites-available/000-default.conf

# Misc
RUN if [ "$DBEVO_LOG" = "true" ]; then touch ${DBEVO_LOGFILE} && chmod 0777 ${DBEVO_LOGFILE} ; fi
RUN mkdir -p /var/www/html/temp && chmod -R 777 /var/www/html/temp

# Use wait-for-it.sh to wait for PostgreSQL to be ready, then start the PHP script and Apache
CMD ["sh", "-c", "/usr/local/bin/wait-for-it.sh postgres:5432 -- php /tmp/dbevo.php && apache2-foreground"]