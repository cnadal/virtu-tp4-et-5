FROM php:8.2-apache
 
RUN apt-get update && apt-get install -y \
    nano \
    git \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql \
    && apt-get clean
 
RUN a2enmod rewrite && service apache2 restart
 
RUN mkdir -p /var/www/html/web_stackPP/bd
COPY listeVoitures.php /var/www/html/web_stackPP/
COPY ajoutVoituresGarage.php /var/www/html/web_stackPP/
COPY creationBDD.php /var/www/html/web_stackPP/bd/
 
RUN curl -OL https://github.com/composer/composer/releases/download/2.9.5/composer.phar \
    && mv composer.phar /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer
