FROM php:8.2-apache AS web

RUN apt-get update && apt-get install -y \
    nano \
    git \
    zip \
    unzip \
    libcurl4-openssl-dev \
    curl \
    --no-install-recommends \
 && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql curl

RUN a2enmod rewrite

RUN curl -fsSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY src/ /var/www/html/

