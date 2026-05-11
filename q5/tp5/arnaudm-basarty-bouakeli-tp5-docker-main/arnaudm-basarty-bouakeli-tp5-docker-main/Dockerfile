FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    nano \
    git \
    libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

RUN curl -OL https://github.com/composer/composer/releases/download/2.9.5/composer.phar \
    && mv composer.phar /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer

COPY ./app/ /var/www/html/

EXPOSE 80