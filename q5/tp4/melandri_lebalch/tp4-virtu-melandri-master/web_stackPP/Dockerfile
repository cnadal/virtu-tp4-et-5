FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nano \
    git \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions including PDO for PostgreSQL
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    zip \
    gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# ── Création du dossier web_stackPP/ ────────────────────────
RUN mkdir -p /var/www/html/web_stackPP

# ── Copie des fichiers PHP → tous dans /var/www/html/web_stackPP/
COPY ./www/listeVoitures.php        /var/www/html/web_stackPP/listeVoitures.php
COPY ./www/ajoutVoituresGarage.php  /var/www/html/web_stackPP/ajoutVoituresGarage.php
COPY ./www/bd/creationBDD.php       /var/www/html/web_stackPP/creationBDD.php

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80