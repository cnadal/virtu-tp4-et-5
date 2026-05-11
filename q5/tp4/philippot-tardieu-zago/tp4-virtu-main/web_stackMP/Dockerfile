FROM php:8.2-apache

RUN apt-get update -y && apt-get install -y \
    nano \
    git \
    libpq-dev \
    zip \
    && docker-php-ext-install pdo_mysql pgsql \
    && apt-get clean -y

RUN a2enmod rewrite && service apache2 restart
