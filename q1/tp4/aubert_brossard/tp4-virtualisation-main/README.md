# TP4 — Virtualisation sous Docker

**Aubert Florian et Brossard Elliot**

## Table des matières

1. [web_stackMP — Apache / MySQL / phpMyAdmin](#web_stackmp--apache--mysql--phpmyadmin)
2. [web_stackPP — Apache / PostgreSQL / pgAdmin4](#web_stackpp--apache--postgresql--pgadmin4)
3. [Avec des données c'est mieux !](#avec-des-données-cest-mieux-)
4. [Étude d'une stack web existante](#étude-dune-stack-web-existante)

---

## web_stackMP — Apache / MySQL / phpMyAdmin

### Services

| Service     | Nom du conteneur | Port externe | Port interne | Réseau    | Volume(s)                        | Infos de connexion                              |
|-------------|------------------|:------------:|:------------:|-----------|----------------------------------|-------------------------------------------------|
| apache      | web85            | 85           | 80           | web_stack | —                                | http://localhost:85                             |
| mysql       | mysql3306        | 3306         | 3306         | web_stack | `./mysql1/db/custom.cnf`         | user: `root` / mdp: `RootPassword123`           |
| phpmyadmin  | pma_mysql86      | 86           | 80           | web_stack | —                                | http://localhost:86                             |

> Réseau `web_stack` de type **bridge**.

### Lancer la stack

```bash
docker compose up --build -d
```

### Taguer et publier les images

#### Docker Hub

```bash
docker tag mysql:9.2 aubertf/mysql-mp:1.0
docker tag web_stackmp-web85:latest aubertf/apache-mp:1.0
docker tag phpmyadmin:latest aubertf/phpmyadmin-mp:1.0

docker push aubertf/mysql-mp:1.0
docker push aubertf/apache-mp:1.0
docker push aubertf/phpmyadmin-mp:1.0
```

#### harbinfo

```bash
docker login harbinfo.iutmontp.univ-montp2.fr

docker tag mysql:9.2 harbinfo.iutmontp.univ-montp2.fr/aubertf/mysql:1.0
docker tag web_stackmp-web85:latest harbinfo.iutmontp.univ-montp2.fr/aubertf/apache:1.0
docker tag phpmyadmin harbinfo.iutmontp.univ-montp2.fr/aubertf/phpmyadmin:1.0

docker push harbinfo.iutmontp.univ-montp2.fr/aubertf/mysql:1.0
docker push harbinfo.iutmontp.univ-montp2.fr/aubertf/apache:1.0
docker push harbinfo.iutmontp.univ-montp2.fr/aubertf/phpmyadmin:1.0
```

### Pull et exécution depuis Docker Hub

```bash
docker login

docker pull aubertf/apache-mp:1.0
docker run -d -p 85:80 --env-file .env aubertf/apache-mp:1.0

docker pull aubertf/mysql-mp:1.0
docker run -d -p 3306:3306 --env-file .env aubertf/mysql-mp:1.0

docker pull aubertf/phpmyadmin-mp:1.0
docker run -d -p 86:80 --env-file .env aubertf/phpmyadmin-mp:1.0
```

### Pull et exécution depuis harbinfo

```bash
docker login harbinfo.iutmontp.univ-montp2.fr

docker pull harbinfo.iutmontp.univ-montp2.fr/aubertf/apache:1.0
docker run -d -p 85:80 --env-file .env harbinfo.iutmontp.univ-montp2.fr/aubertf/apache:1.0

docker pull harbinfo.iutmontp.univ-montp2.fr/aubertf/mysql:1.0
docker run -d -p 3306:3306 --env-file .env harbinfo.iutmontp.univ-montp2.fr/aubertf/mysql:1.0

docker pull harbinfo.iutmontp.univ-montp2.fr/aubertf/phpmyadmin:1.0
docker run -d -p 86:80 --env-file .env harbinfo.iutmontp.univ-montp2.fr/aubertf/phpmyadmin:1.0
```

---

## web_stackPP — Apache / PostgreSQL / pgAdmin4

### Services

| Service    | Nom du conteneur  | Port externe | Port interne | Réseau    | Volume(s)       | Infos de connexion                                                |
|------------|-------------------|:------------:|:------------:|-----------|-----------------|-------------------------------------------------------------------|
| apache     | web91_pp          | 91           | 80           | web_stack | —               | http://localhost:91                                               |
| postgres   | postgres5432      | 5432         | 5432         | web_stack | `postgres_data` | user: `root` / mdp: `RootPassword123`                             |
| pgadmin4   | pgadmin_92        | 92           | 80           | web_stack | `pgadmin_data`  | http://localhost:92 — email: `admin@admin.com` / `RootPassword123`|

> Réseau `web_stack` de type **bridge**.  
> Les volumes `postgres_data` et `pgadmin_data` sont nommés et gérés par Docker.

### Lancer la stack

```bash
docker compose up --build -d
```

### Connexion à pgAdmin4

1. Aller sur http://localhost:92
2. Se connecter avec `admin@admin.com` / `RootPassword123`
3. Ajouter un serveur manuellement :
   - **Host** : `postgres`
   - **Port** : `5432`
   - **Database** : `garage_db`
   - **Username** : `root`
   - **Password** : `RootPassword123`

### Taguer et publier les images

#### Docker Hub

```bash
docker tag postgres:latest aubertf/postgres-pp:1.0
docker tag web_stackpp-web91 aubertf/apache-pp:1.0
docker tag dpage/pgadmin4:latest aubertf/pgadmin-pp:1.0

docker push aubertf/postgres-pp:1.0
docker push aubertf/apache-pp:1.0
docker push aubertf/pgadmin-pp:1.0
```

#### harbinfo

```bash
docker login harbinfo.iutmontp.univ-montp2.fr

docker tag web_stackpp-web91 harbinfo.iutmontp.univ-montp2.fr/aubertf/web_stackpp/apache:1.0
docker tag postgres:latest harbinfo.iutmontp.univ-montp2.fr/aubertf/web_stackpp/postgresql:1.0
docker tag dpage/pgadmin4:latest harbinfo.iutmontp.univ-montp2.fr/aubertf/web_stackpp/pgadmin:1.0

docker push harbinfo.iutmontp.univ-montp2.fr/aubertf/web_stackpp/apache:1.0
docker push harbinfo.iutmontp.univ-montp2.fr/aubertf/web_stackpp/postgresql:1.0
docker push harbinfo.iutmontp.univ-montp2.fr/aubertf/web_stackpp/pgadmin:1.0
```

### Pull et exécution depuis Docker Hub

```bash
docker login

docker pull aubertf/apache-pp:1.0
docker run -d -p 91:80 --env-file .env aubertf/apache-pp:1.0

docker pull aubertf/postgres-pp:1.0
docker run -d -p 5432:5432 --env-file .env aubertf/postgres-pp:1.0

docker pull aubertf/pgadmin-pp:1.0
docker run -d -p 92:80 --env-file .env aubertf/pgadmin-pp:1.0
```

### Pull et exécution depuis harbinfo

```bash
docker login harbinfo.iutmontp.univ-montp2.fr

docker pull harbinfo.iutmontp.univ-montp2.fr/aubertf/web_stackpp/apache:1.0
docker run -d -p 91:80 --env-file .env harbinfo.iutmontp.univ-montp2.fr/aubertf/web_stackpp/apache:1.0

docker pull harbinfo.iutmontp.univ-montp2.fr/aubertf/web_stackpp/postgresql:1.0
docker run -d -p 5432:5432 --env-file .env harbinfo.iutmontp.univ-montp2.fr/aubertf/web_stackpp/postgresql:1.0

docker pull harbinfo.iutmontp.univ-montp2.fr/aubertf/web_stackpp/pgadmin:1.0
docker run -d -p 92:80 --env-file .env harbinfo.iutmontp.univ-montp2.fr/aubertf/web_stackpp/pgadmin:1.0
```

---

## Avec des données c'est mieux !

### web_stackMP — MySQL / phpMyAdmin

Les fichiers PHP suivants ont été ajoutés dans le conteneur Apache via `COPY` dans le Dockerfile (sans volume) :

| Fichier                   | Chemin dans le conteneur                        | URL d'accès                                                     |
|---------------------------|-------------------------------------------------|-----------------------------------------------------------------|
| `creationBDD.php`         | `/var/www/html/web_stackMP/bd/creationBDD.php`  | http://localhost:85/web_stackMP/bd/creationBDD.php              |
| `ajoutVoituresGarage.php` | `/var/www/html/web_stackMP/ajoutVoituresGarage.php` | http://localhost:85/web_stackMP/ajoutVoituresGarage.php     |
| `listeVoitures.php`       | `/var/www/html/web_stackMP/listeVoitures.php`   | http://localhost:85/web_stackMP/listeVoitures.php               |

Les scripts PHP se connectent au service `mysql` (host Docker interne) avec les credentials définis dans le `.env`.

### web_stackPP — PostgreSQL / pgAdmin4

Même structure, les fichiers PHP adaptés pour `pdo_pgsql` sont copiés dans le conteneur Apache via `COPY` :

| Fichier                   | Chemin dans le conteneur                        | URL d'accès                                                     |
|---------------------------|-------------------------------------------------|-----------------------------------------------------------------|
| `creationBDD.php`         | `/var/www/html/web_stackPP/bd/creationBDD.php`  | http://localhost:91/web_stackPP/bd/creationBDD.php              |
| `ajoutVoituresGarage.php` | `/var/www/html/web_stackPP/ajoutVoituresGarage.php` | http://localhost:91/web_stackPP/ajoutVoituresGarage.php     |
| `listeVoitures.php`       | `/var/www/html/web_stackPP/listeVoitures.php`   | http://localhost:91/web_stackPP/listeVoitures.php               |

---

## Étude d'une stack web existante

### 1. Commande pour builder l'image

```bash
docker build -t mon_image_php .
```

Le build peut générer une erreur sur l'installation de `nvm`/`npm` car le script d'installation de `nvm` modifie le `~/.bashrc` mais ces modifications ne sont pas disponibles dans les layers suivants du Dockerfile (chaque `RUN` est un shell indépendant). Pour corriger, il faut sourcer `nvm` dans le même `RUN` que son utilisation, ce qui est déjà fait dans le Dockerfile avec `export NVM_DIR` et le chaînage `&&`.

### 2. Commande pour run l'image buildée

```bash
docker run -d -p 80:80 mon_image_php
```

Pour monter un projet local :

```bash
docker run -d -p 80:80 -v ./mon_projet:/var/www/html mon_image_php
```

### 3. Fonctionnalités installées

| Fonctionnalité       | Détail                                                                 |
|----------------------|------------------------------------------------------------------------|
| **Base**             | PHP 8.3 + Apache                                                       |
| **Extensions PHP**   | `pdo`, `pdo_mysql`, `pdo_pgsql`, `pdo_sqlite`                         |
| **Outils système**   | `nano`, `unzip`, `wget`, `acl`, `tzdata`                               |
| **Composer**         | Gestionnaire de dépendances PHP                                        |
| **npm / Node.js**    | Installés via `nvm` (version LTS)                                      |
| **Xdebug**           | Mode `debug`, port `9003`, déclenchement sur requête (`trigger`)       |
| **Apache**           | `mod_rewrite` activé, `ServerName localhost`                           |
| **Timezone**         | `Europe/Paris` (PHP + système)                                         |
| **PHP ini**          | Configuration de développement activée (`php.ini-development`)        |

### 4. Exécution depuis un docker-compose.yml

Oui, ce Dockerfile peut être utilisé depuis un `docker-compose.yml` avec une section `build` :

```yaml
services:
  web:
    build:
      context: .
      dockerfile: Dockerfile
    ports:
      - 80:80
    volumes:
      - ./mon_projet:/var/www/html
```

### HTTPS — Ajout du support SSL

Pour ajouter le support HTTPS, il faut activer le module SSL d'Apache, activer le virtual host par défaut SSL et générer un certificat auto-signé. Voici le code à ajouter dans le Dockerfile, après le `RUN a2enmod rewrite` :

```dockerfile
# Activer SSL
RUN a2enmod ssl \
    && a2ensite default-ssl

# Générer un certificat auto-signé
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/apache-selfsigned.key \
    -out /etc/ssl/certs/apache-selfsigned.crt \
    -subj "/CN=localhost"

# Exposer le port HTTPS
EXPOSE 443
```

Les URLs d'accès après rebuild :

| Protocole | URL                        |
|-----------|----------------------------|
| HTTP      | http://localhost:80        |
| HTTPS     | https://localhost:443      |

> Le navigateur affichera un avertissement de sécurité car le certificat est auto-signé (non signé par une autorité de certification reconnue). Il faut cliquer sur "Avancer quand même" pour accéder au site.
