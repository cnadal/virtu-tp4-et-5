# Documentation Docker Stacks — web_stackMP & web_stackPP

## Table des matières

1. [Présentation du projet](#1-présentation-du-projet)
2. [Stack web_stackMP — Apache + MySQL + PhpMyAdmin](#2-stack-web_stackmp)
    - 2.1 [Tableau des services](#21-tableau-des-services)
    - 2.2 [Structure des fichiers](#22-structure-des-fichiers)
    - 2.3 [Lancer la stack](#23-lancer-la-stack)
3. [Stack web_stackPP — Apache + PostgreSQL + PgAdmin4](#3-stack-web_stackpp)
    - 3.1 [Tableau des services](#31-tableau-des-services-1)
    - 3.2 [Structure des fichiers](#32-structure-des-fichiers-1)
    - 3.3 [Lancer la stack](#33-lancer-la-stack-1)
4. [Stack web_stackNJS — NodeJS](#4-stack-web_stacknjs)
    - 4.1 [Tableau des services](#41-tableau-des-services)
    - 4.2 [Structure des fichiers](#42-structure-des-fichiers)
    - 4.3 [Lancer la stack](#43-lancer-la-stack)
5. [Stack HTTPS — Serveur web sécurisé](#5-stack-https)
    - 5.1 [Tableau des services](#51-tableau-des-services)
    - 5.2 [Structure des fichiers](#52-structure-des-fichiers)
    - 5.3 [Lancer la stack](#53-lancer-la-stack)
6. [Variables d'environnement](#6-variables-denvironnement)
7. [Commandes Docker utiles](#7-commandes-docker-utiles)
8. [Publication des images sur les registries](#8-publication-des-images-sur-les-registries)
    - 8.1 [Docker Hub](#81-docker-hub)
    - 8.2 [Harbinfo](#82-harbinfo-registry-iut)
9. [Récupérer et exécuter les images](#9-récupérer-et-exécuter-les-images)

---

## 1. Présentation du projet

Ce projet regroupe quatre environnements Docker indépendants :

- **web_stackMP** : serveur web Apache/PHP 8.2, base de données MySQL 9.2 et interface PhpMyAdmin.
- **web_stackPP** : serveur web Apache/PHP 8.2, base de données PostgreSQL et interface PgAdmin4.
- **web_stackNJS** : serveur web NodeJS embarquant sa propre base de données MariaDB et PhpMyAdmin.
- **https** : serveur web Apache/PHP 8.3 permettant les connexions chiffrées via certificat SSL auto-signé.

Chaque stack web_stack repose sur un fichier `var.env` pour les variables d'environnement, et gère ses dépendances internes finement.

---

## 2. Stack web_stackMP

### 2.1 Tableau des services

| Service        | Image / Build       | Port ext. | Port int. | Connexion                                               | Réseau    | Volume(s)                        |
|----------------|---------------------|:---------:|:---------:|---------------------------------------------------------|-----------|----------------------------------|
| `web_mp`       | Build (Dockerfile)  | `8080`    | `80`      | —                                                       | web_stack | `web_mp_data` → /var/www/html    |
| `db_mysql`     | `mysql:9.2`         | `3306`    | `3306`    | User : `dbuser` / Pass : `dbpassword` / DB : `mydb`    | web_stack | `db_mysql_data` → /var/lib/mysql |
| `pma`          | `phpmyadmin:latest` | `8081`    | `80`      | Host : `db_mysql` / User : `dbuser` / Pass : `dbpassword` | web_stack | —                             |

### 2.2 Structure des fichiers

```
web_stackMP/
├── bd/
│   ├── creationBDD.php          # Script de création BD
│   └── garageM.sql              # Export/Schéma SQL
├── docker-compose.yml   # Orchestration des services
├── Dockerfile           # Image Apache + PHP 8.2 + PDO MySQL
├── var.env              # Variables d'environnement
└── www/
    └── index.php        # Page de test PHP
```

### 2.3 Lancer la stack

```bash
cd web_stackMP

# Construire les images et démarrer les conteneurs
docker compose --env-file var.env up -d --build

# Vérifier l'état des conteneurs
docker compose ps

# Consulter les logs
docker compose logs -f

# Arrêter la stack
docker compose down

# Arrêter et supprimer les volumes
docker compose down -v
```

**Accès aux services :**

| Service    | URL                    |
|------------|------------------------|
| Apache/PHP | http://localhost:8080  |
| PhpMyAdmin | http://localhost:8081  |
| MySQL      | localhost:3306         |

---

## 3. Stack web_stackPP

### 3.1 Tableau des services

| Service        | Image / Build           | Port ext. | Port int. | Connexion                                                      | Réseau    | Volume(s)                                      |
|----------------|-------------------------|:---------:|:---------:|----------------------------------------------------------------|-----------|------------------------------------------------|
| `web_pp`       | Build (Dockerfile)      | `8082`    | `80`      | —                                                              | web_stack | `web_pp_data` → /var/www/html                  |
| `db_postgres`  | `postgres:latest`       | `5432`    | `5432`    | User : `dbuser` / Pass : `dbpassword` / DB : `mydb`           | web_stack | `db_postgres_data` → /var/lib/postgresql/data  |
| `pgadmin`      | `dpage/pgadmin4:latest` | `8083`    | `80`      | Email : `admin@admin.com` / Pass : `adminpassword`            | web_stack | `pgadmin_data` → /var/lib/pgadmin              |

### 3.2 Structure des fichiers

```
web_stackPP/
├── bd/
│   ├── creationBDD.php          # Script de création BD
│   └── garageP.sql              # Export/Schéma SQL
├── docker-compose.yml   # Orchestration des services
├── Dockerfile           # Image Apache + PHP 8.2 + PDO PostgreSQL
├── var.env              # Variables d'environnement
└── www/
    └── index.php        # Page de test PHP
```

### 3.3 Lancer la stack

```bash
cd web_stackPP

docker compose --env-file var.env up -d --build
docker compose ps
docker compose logs -f
docker compose down
docker compose down -v
```

**Accès aux services :**

| Service    | URL                    |
|------------|------------------------|
| Apache/PHP | http://localhost:8082  |
| PgAdmin4   | http://localhost:8083  |
| PostgreSQL | localhost:5432         |

---

## 4. Stack web_stackNJS — NodeJS

### 4.1 Tableau des services

| Service      | Image / Build        | Port ext. | Port int. | Connexion                                              | Réseau    | Volume(s)                     |
|--------------|----------------------|:---------:|:---------:|--------------------------------------------------------|-----------|-------------------------------|
| `web_njs`    | Build (Dockerfile)   | `8082`    | `3000`    | —                                                      | web_stack | — (Les fichiers sont copiés)  |
| `db_mariadb` | `mariadb:10.11`      | `3307`    | `3306`    | User : `dbuser` / Pass : `dbpassword` / DB : `njsdb`   | web_stack | `db_mariadb_data` → /var/lib/mysql |
| `pma_njs`    | `phpmyadmin:latest`  | `8083`    | `80`      | Host : `db_mariadb` / User : `root` / Pass : `rootpassword` | web_stack | —                             |

Cette stack déploie un serveur Node.js via Express couplé à sa propre base de données MariaDB et son interface PhpMyAdmin. Elle est entièrement indépendante pour son fonctionnement, bien qu'elle rejoigne le réseau `web_stack`.

### 4.2 Structure des fichiers

```
web_stackNJS/
├── bd/
│   ├── creationBDD.js           # Script de création API
│   └── garageM.sql              # Export/Schéma SQL
├── docker-compose.yml           # Orchestration des services
├── Dockerfile                   # Image Node 20 + fichiers app
├── ajoutVoituresGarage.js       # Script d'ajout API
├── listeVoitures.js             # Script de lecture API
├── package.json                 # Dépendances Node (express, mysql2, cors)
├── server.js                    # Serveur Express principal
├── var.env                      # Variables d'environnement
└── www/
    ├── index.html               # Page d'accueil générée JavaScript
    ├── page_ajout.html          # Page d'appel AJAX (Ajout)
    ├── page_creation.html       # Page d'appel AJAX (Création BD)
    └── page_liste.html          # Page d'appel AJAX (Lecture BD)
```

### 4.3 Lancer la stack

**Pré-requis** : Assurez-vous d'abord que le réseau central `web_stack` existe et que la base de données est lancée (`docker compose up -d` depuis le dossier `web_stackMP`).

```bash
cd web_stackNJS

# Construire l'image et démarrer le serveur Node
docker compose --env-file var.env up -d --build --remove-orphans

# Consulter les logs
docker compose logs -f

# Arrêter la stack
docker compose down
```

**Accès aux services :**

| Service       | URL                    |
|---------------|------------------------|
| Serveur Node  | http://localhost:8082  |

---

## 5. Stack HTTPS — Serveur web sécurisé

### 5.1 Tableau des services

| Service       | Image / Build      | Port ext.   | Port int. | Connexion                                     | Réseau  | Volume(s)                   |
|---------------|--------------------|:-----------:|:---------:|-----------------------------------------------|---------|-----------------------------|
| `mon-app-php` | Build (Dockerfile) | `8080/8443` | `80/443`  | Résolution : `host.docker.internal:host-gateway` | bridge  | `./:/var/www/html`          |

Cette stack met en œuvre un certificat SSL auto-signé sur un serveur Apache local. Elle écoute simultanément sur les ports 80 (HTTP basique) et 443 (HTTPS sécurisé).

### 5.2 Structure des fichiers

```
https/
├── docker-compose.yml           # Orchestration du conteneur unique avec mapping des ports
├── Dockerfile                   # Image PHP 8.3 Apache + mod_ssl + génération SSL + redirection
└── www/                         # Scripts PHP
```

### 5.3 Lancer la stack

```bash
cd https

# Construire l'image avec SSL local et la démarrer
docker compose up -d --build

# Arrêter la stack
docker compose down
```

**Accès aux services :**

| URL Testée                | Résultat attendu                                                       |
|---------------------------|------------------------------------------------------------------------|
| http://localhost:8080     | Page accessible en HTTP non sécurisé                                    |
| https://localhost:8443    | Accès validé (avec avertissement normal lié au certificat auto-signé)   |

---

## 6. Variables d'environnement

Chaque stack dispose de son propre fichier `var.env`, placé à la racine du dossier correspondant.

### web_stackMP

```env
# MySQL
MYSQL_ROOT_PASSWORD=rootpassword
MYSQL_DATABASE=mydb
MYSQL_USER=dbuser
MYSQL_PASSWORD=dbpassword

# PhpMyAdmin
PMA_HOST=db_mysql
PMA_PORT=3306
PMA_USER=dbuser
PMA_PASSWORD=dbpassword

# Ports externes
WEB_MP_PORT=8080
MYSQL_PORT=3306
PMA_PORT_EXT=8081
```

### web_stackPP

```env
# PostgreSQL
POSTGRES_DB=mydb
POSTGRES_USER=dbuser
POSTGRES_PASSWORD=dbpassword

# PgAdmin4
PGADMIN_DEFAULT_EMAIL=admin@admin.com
PGADMIN_DEFAULT_PASSWORD=adminpassword

# Ports externes
WEB_PP_PORT=8082
POSTGRES_PORT=5432
PGADMIN_PORT=8083
```

### web_stackNJS

```env
# MariaDB
MARIADB_ROOT_PASSWORD=rootpassword
MARIADB_DATABASE=njsdb
MARIADB_USER=dbuser
MARIADB_PASSWORD=dbpassword

# PhpMyAdmin
PMA_HOST=db_mariadb
PMA_PORT=3306
PMA_USER=root
PMA_PASSWORD=rootpassword

# Ports externes
WEB_NJS_PORT=8082
DB_PORT=3307
PMA_PORT_EXT=8083
```

> **Note :** Ne jamais versionner un fichier `.env` contenant des mots de passe réels. En production, utiliser `.gitignore` et un gestionnaire de secrets.

---

## 7. Commandes Docker utiles

```bash
# Images et conteneurs
docker images
docker ps
docker ps -a

# Accès au shell d'un conteneur
docker exec -it web_mp bash
docker exec -it db_mysql bash
docker exec -it db_postgres bash

# Logs d'un service
docker logs web_mp
docker logs db_mysql

# Inspection réseau et volumes
docker network inspect web_stack_MP
docker volume inspect db_mysql_data
```

---

## 8. Publication des images sur les registries

Remplacer `votrelogin` par l'identifiant Docker Hub ou Harbinfo selon le registry cible.

### 8.1 Docker Hub

```bash
docker login
```

**Tagging — web_stackMP**

```bash
docker tag web_stackmp-web_mp:latest votrelogin/apache:1.0
docker tag mysql:9.2                 votrelogin/mysql:1.0
docker tag phpmyadmin:latest         votrelogin/phpmyadmin:1.0
```

**Push — web_stackMP**

```bash
docker push votrelogin/apache:1.0
docker push votrelogin/mysql:1.0
docker push votrelogin/phpmyadmin:1.0
```

**Tagging — web_stackPP**

```bash
docker tag web_stackpp-web_pp:latest votrelogin/apache_pp:1.0
docker tag postgres:latest           votrelogin/postgres:1.0
docker tag dpage/pgadmin4:latest     votrelogin/pgadmin4:1.0
```

**Push — web_stackPP**

```bash
docker push votrelogin/apache_pp:1.0
docker push votrelogin/postgres:1.0
docker push votrelogin/pgadmin4:1.0
```

**Tagging & Push — web_stackNJS & https**

```bash
# NodeJS Stack
docker tag web_stacknjs-web_njs:latest votrelogin/node_app:1.0
docker push votrelogin/node_app:1.0

# HTTPS Stack
docker tag https-mon-app-php:latest votrelogin/apache_https:1.0
docker push votrelogin/apache_https:1.0
```

---

### 8.2 Harbinfo (Registry IUT)

```bash
docker login harbinfo.iut.exemple.fr
```

**Tagging — web_stackMP**

```bash
docker tag web_stackmp-web_mp:latest harbinfo.iut.exemple.fr/votrelogin/apache:1.0
docker tag mysql:9.2                 harbinfo.iut.exemple.fr/votrelogin/mysql:1.0
docker tag phpmyadmin:latest         harbinfo.iut.exemple.fr/votrelogin/phpmyadmin:1.0
```

**Push — web_stackMP**

```bash
docker push harbinfo.iut.exemple.fr/votrelogin/apache:1.0
docker push harbinfo.iut.exemple.fr/votrelogin/mysql:1.0
docker push harbinfo.iut.exemple.fr/votrelogin/phpmyadmin:1.0
```

**Tagging — web_stackPP**

```bash
docker tag web_stackpp-web_pp:latest harbinfo.iut.exemple.fr/votrelogin/apache_pp:1.0
docker tag postgres:latest           harbinfo.iut.exemple.fr/votrelogin/postgres:1.0
docker tag dpage/pgadmin4:latest     harbinfo.iut.exemple.fr/votrelogin/pgadmin4:1.0
```

**Push — web_stackPP**

```bash
docker push harbinfo.iut.exemple.fr/votrelogin/apache_pp:1.0
docker push harbinfo.iut.exemple.fr/votrelogin/postgres:1.0
docker push harbinfo.iut.exemple.fr/votrelogin/pgadmin4:1.0
```

---

## 9. Récupérer et exécuter les images

### Depuis Docker Hub

```bash
docker login

docker pull votrelogin/apache:1.0
docker pull votrelogin/mysql:1.0
docker pull votrelogin/phpmyadmin:1.0

# Dans docker-compose.yml, remplacer "build:" par "image: votrelogin/apache:1.0"
docker compose --env-file var.env up -d
```

### Depuis Harbinfo

```bash
docker login harbinfo.iut.exemple.fr

docker pull harbinfo.iut.exemple.fr/votrelogin/apache:1.0
docker pull harbinfo.iut.exemple.fr/votrelogin/mysql:1.0
docker pull harbinfo.iut.exemple.fr/votrelogin/phpmyadmin:1.0

docker compose --env-file var.env up -d
```

Penser à toujours passer `--env-file var.env` pour que les variables soient correctement injectées dans les conteneurs.

---

*Documentation rédigée dans le cadre du TP Docker — IUT*