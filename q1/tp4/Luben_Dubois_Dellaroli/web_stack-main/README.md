# Web Stack

## Stack MySQL + Php

Les services déployés sont
- Un SGBD MySQL, avec son mot de passe root stocké dans un fichier `.env`
- Un serveur Web Apache2 avec Composer, avec le mot de passe de la base de donnée passé en variable d'environnement.
- Un serveur Web PhpMyAdmin pour faciliter l'accès et la consultation de la base de donnée.

Les services sont ordonnés avec `depends_on`, les conteneurs `web` et `phpmyadmin` attendent que `mysql` soit démarré

Pour démarrer la stack, il suffit d'effectuer la commande suivante en étant dans le dossier `stack_mysql_php`

```bash
docker compose up -d --build
```

Pour push l'image que nous avons réalisé sur un registry, on peut effectuer

```bash
# Pour construire l'image
docker compose build

# Pour se connecter au registry (passer l'étape si déja fait)
docker login # pour docker hub
docker login harbinfo.iutmontp.univ-montp2.fr # pour harbor du dep info

# On retaggue l'image pour que son tag corresponde a harbor du dep info
docker tag webapachemysql:latest harbinfo.iutmontp.univ-montp2.fr/lubenb/webapachemysql:latest

# Pour pousser l'image sur un registry
docker push webapachemysql:latest # pour docker hub
docker push harbinfo.iutmontp.univ-montp2.fr/lubenb/webapachemysql:latest # pour harbor info

```

## Stack PostgreSQL + Php

Les services déployés sont
- Un SGBD PostgreSQL, avec son mot de passe root stocké dans un fichier `.env`
- Un serveur Web Apache2 avec Composer, avec le mot de passe de la base de donnée passé en variable d'environnement.
- Un serveur Web pgAdmin4 pour faciliter l'accès et la consultation de la base de donnée.

Les services sont ordonnés avec `depends_on`, les conteneurs `web` et `pgadmin4` attendent que `postgres` soit démarré

Pour démarrer la stack, il suffit d'effectuer la commande suivante en étant dans le dossier `stack_postgres_php`

```bash
docker compose up -d --build
```

Pour push l'image que nous avons réalisé sur un registry, on peut effectuer

```bash
# Pour construire l'image
docker compose build

# Pour se connecter au registry (passer l'étape si déja fait)
docker login # pour docker hub
docker login harbinfo.iutmontp.univ-montp2.fr # pour harbor du dep info

# On retaggue l'image pour que son tag corresponde a harbor du dep info
docker tag webapachepostgres:latest harbinfo.iutmontp.univ-montp2.fr/lubenb/webapachepostgres:latest

# Pour pousser l'image sur un registry
docker push webapachepostgres:latest # pour docker hub
docker push harbinfo.iutmontp.univ-montp2.fr/lubenb/webapachepostgres:latest # pour harbor info

```

## Stack MySQL + NodeJS

Les services déployés sont
- Un SGBD MySQL, avec son mot de passe root stocké dans un fichier `.env`
- Un serveur Web NodeJS (Express), avec le mot de passe de la base de donnée passé en variable d'environnement.
- Un serveur Web PhpMyAdmin pour faciliter l'accès et la consultation de la base de donnée.

Les services sont ordonnés avec `depends_on` et un `healthcheck` sur MySQL, les conteneurs `nodejs` et `phpmyadmin` attendent que `mysql` soit prêt

Pour démarrer la stack, il suffit d'effectuer la commande suivante en étant dans le dossier `stack_nodejs`

```bash
docker compose up -d --build
```

Pour push l'image que nous avons réalisé sur un registry, on peut effectuer

```bash
# Pour construire l'image
docker compose build

# Pour se connecter au registry (passer l'étape si déja fait)
docker login # pour docker hub
docker login harbinfo.iutmontp.univ-montp2.fr # pour harbor du dep info

# L'image de nodejs est nommee automatiquement par docker compose
docker tag stack_nodejs-nodejs:latest harbinfo.iutmontp.univ-montp2.fr/lubenb/stack_nodejs-nodejs:latest

# Pour pousser l'image sur un registry
docker push stack_nodejs-nodejs:latest # pour docker hub
docker push harbinfo.iutmontp.univ-montp2.fr/lubenb/stack_nodejs-nodejs:latest # pour harbor info

```

## Étude du Dockerfile fourni

Dockerfile fourni

```Dockerfile
# Use the official PHP image with Apache
FROM php:8.3-apache

# Set the ServerName to localhost
RUN echo "ServerName localhost" | tee -a /etc/apache2/apache2.conf
ENV TZ=Europe/Paris

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install necessary PHP extensions
RUN apt-get update && \
apt-get install -y \
libpq-dev \
tzdata \
libsqlite3-dev \
libaio-dev \
unzip \
nano \
acl \
wget && \
docker-php-ext-install pdo pdo_mysql pdo_pgsql pdo_sqlite

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install npm
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.1/install.sh | bash && \
export NVM_DIR="$HOME/.nvm" && \
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" && \
[ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"&& \
nvm install --lts

RUN echo "date.timezone = Europe/Paris" > $PHP_INI_DIR/conf.d/timezone.ini

# Install Xdebug
RUN pecl install xdebug && \
docker-php-ext-enable xdebug

# Configure Xdebug
RUN echo "zend_extension=xdebug.so" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
echo "xdebug.start_with_request=trigger" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# PHP Ini development
RUN cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/conf.d/php.ini

# Set up the working directory
WORKDIR /var/www/html

# Copy the local project files to the container
# COPY . /var/www/html
# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
```

1. Il faut faire `docker build . -t <nom de l'image>`. Le build ne génère pas d'erreurs.

2. Il faut faire `docker run <nom de l'image>`

3. 
* tzdata pour les time zones
* unzip pour extraire les fichiers
* nano pour éditer les fichiers
* acl pour les autorisations
* wget pour effectuer des requêtes internet
* pdo pour la connexion à la base de données
* pdo_mysql pour la connexion à la base de données mysql
* pdo_pgsql et libpq-dev pour la connexion aux bases de données postgresql
* pdo_sqlite et libsqlite3-dev pour la connexion aux bases de données sqlite
* composer pour gérer les dépendances PHP
* nvm pour gérer les versions de node.js installées
* xdebug pour débugger du PHP

4. Oui.

5. Il faut ajouter les certificats dans un dossier et ajouter la ligne de configuration dans apache.