FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    nano \
    git \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql \
    && apt-get clean

RUN a2enmod rewrite && service apache2 restart

COPY listeVoitures.php /var/www/html/
COPY ajoutVoituresGarage.php /var/www/html/
RUN mkdir -p /var/www/html/bd
COPY creationBDD.php /var/www/html/bd/
