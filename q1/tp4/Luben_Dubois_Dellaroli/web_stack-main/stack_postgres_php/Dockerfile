FROM php:8.2-apache AS web

RUN apt-get update && apt-get install -y \
    nano \
    git \
    libpq-dev \
    zip \
    && docker-php-ext-install pdo_pgsql \
    && apt-get clean

RUN a2enmod rewrite && service apache2 restart

RUN curl -OL https://github.com/composer/composer/releases/download/2.9.5/composer.phar \
    && mv composer.phar /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer