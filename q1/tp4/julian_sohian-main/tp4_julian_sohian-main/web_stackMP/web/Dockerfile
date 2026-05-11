FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    nano \
    git

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

RUN mkdir -p /var/www/html/web_stackMP

COPY stackmp/ /var/www/html/web_stackMP/

CMD ["apache2-foreground"]