FROM php:8.2-apache
RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN apt-get update && apt-get install -y nano git libpq-dev

RUN a2enmod rewrite

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html