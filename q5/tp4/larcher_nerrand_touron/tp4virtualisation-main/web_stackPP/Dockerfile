FROM php:8.2-apache

# Install system dependencies + PHP extensions
RUN apt-get update && apt-get install -y \
    nano \
    git \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Copie des fichiers dans /var/www/html/web_stackPP/
# => accessible via http://localhost/web_stackPP/...
COPY src/listeVoitures.php /var/www/html/web_stackPP/listeVoitures.php
COPY src/ajoutVoituresGarage.php /var/www/html/web_stackPP/ajoutVoituresGarage.php
COPY src/bd/creationBDD.php /var/www/html/web_stackPP/creationBDD.php

EXPOSE 80

CMD ["apache2-foreground"]