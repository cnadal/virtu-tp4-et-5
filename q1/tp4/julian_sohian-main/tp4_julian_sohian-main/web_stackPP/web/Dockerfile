FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    nano \
    git

RUN docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html

CMD ["apache2-foreground"]
