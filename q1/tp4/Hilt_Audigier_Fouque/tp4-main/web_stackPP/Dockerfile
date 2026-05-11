FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    nano \
    git \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

RUN mkdir -p /var/www/html/web_stackPP/bd

COPY listeVoitures.php ajoutVoituresGarage.php creationBDD.php /var/www/html/web_stackPP/
