FROM php:8.2-apache

RUN apt-get update && apt-get install -y nano git

RUN docker-php-ext-install pdo pdo_mysql

COPY listeVoitures.php /var/www/html/
COPY ajoutVoituresGarage.php /var/www/html/
COPY creationBDD.php /var/www/html/