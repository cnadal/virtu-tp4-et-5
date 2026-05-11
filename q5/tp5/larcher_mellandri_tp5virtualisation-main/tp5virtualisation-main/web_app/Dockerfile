FROM php:8.2-apache

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Installer extensions PHP
RUN docker-php-ext-install pdo_mysql mysqli

# Activer mod_rewrite
RUN a2enmod rewrite

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le dossier de travail
WORKDIR /var/www/html

# Copier les fichiers du projet
COPY ./src/ /var/www/html/

# Installer les dépendances PHP (génère /vendor + autoload)
RUN composer install --no-interaction --optimize-autoloader

# Donner les permissions (important)
RUN chown -R www-data:www-data /var/www/html

# Lancer Apache
CMD ["apache2-foreground"]