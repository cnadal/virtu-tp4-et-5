FROM php:8.2-apache

RUN apt-get update && apt-get install -y nano git libpq-dev

RUN docker-php-ext-install pdo pdo_pgsql

COPY listeVoitures.php /var/www/html/
COPY ajoutVoituresGarage.php /var/www/html/
COPY creationBDD.php /var/www/html/
