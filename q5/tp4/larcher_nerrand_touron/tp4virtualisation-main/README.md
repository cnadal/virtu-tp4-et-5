# TP4 Virtualisation
LARCHER Chloé  
NERRAND Elie  
TOURON Arthur
---

## Table des matières
- [Présentation](#présentation)
- [Structure du projet](#structure-du-projet)
- [Services et configuration](#services-et-configuration)
  - [web_stackMP (MySQL)](#web_stackmp-mysql)
  - [web_stackPP (PostgreSQL)](#web_stackpp-postgresql)
  - [web_stackNJS (Node.js)](#web_stacknjs-nodejs)
- [Commandes utiles](#commandes-utiles)
  - [Lancer les stacks](#lancer-les-stacks)
  - [Construire et pousser les images](#construire-et-pousser-les-images)
  - [Tester les applications](#tester-les-applications)
- [Documentation complémentaire](#documentation-complémentaire)

---

## Présentation
Ce projet contient trois stacks Docker indépendantes, chacune déployant un serveur web avec une base de données et un outil d'administration.

- **web_stackMP** : Serveur Apache + PHP 8.2, base MySQL 9.2, phpMyAdmin.
- **web_stackPP** : Serveur Apache + PHP 8.2, base PostgreSQL 16, pgAdmin 4.
- **web_stackNJS** : Serveur Node.js (Express), base MySQL 9.2, avec des pages web utilisant AJAX.


---

## Structure du projet
```
tp4virtualisation/
├── web_stackMP/
│   ├── php/
│   │   ├── ajoutVoituresGarage.php
│   │   ├── CreationBDD.php
│   │   └── listeVoitures.php
│   ├── docker-compose.yml
│   ├── dockerfile              # Dockerfile inline utilisé dans docker-compose
│   ├── init.sql
│   └── var.env
├── web_stackPP/
│   ├── src/
│   │   ├── bd/
│   │   │   └── CreationBDD.php
│   │   ├── ajoutVoituresGarage.php
│   │   └── listeVoitures.php
│   ├── .env
│   ├── docker-compose.yml
│   └── Dockerfile
├── web_stackNJS/
│   ├── public/
│   │   ├── ajout.html
│   │   ├── creation.html
│   │   ├── index.html
│   │   └── liste.html
│   ├── ajoutVoituresGarage.php
│   ├── creationBDD.php
│   ├── docker-compose.yml
│   ├── listeVoitures.php
│   ├── server.js
│   └── var.env
├── README.md
└── TP4-Virtualisation-Compose-Build-publication.pdf


---

## Services et configuration

### web_stackMP (MySQL)

| Service      | Image                    | Port externe | Port interne | Variables d'environnement | Réseau      |
|--------------|--------------------------|--------------|--------------|---------------------------|-------------|
| web_mp       | php:8.2-apache           | 8080         | 80           | -                         | web_stack   |
| db_mysql     | mysql:9.2                | 3306         | 3306         | var.env                   | web_stack   |
| pma          | phpmyadmin               | 8082         | 80           | var.env                   | web_stack   |

- **Fichier de variables** : `var.env`

### web_stackPP (PostgreSQL)

| Service      | Image                    | Port externe | Port interne | Variables d'environnement | Réseau      |
|--------------|--------------------------|--------------|--------------|---------------------------|-------------|
| web_pp       | Construit avec Dockerfile| 8083         | 80           | .env                      | web_stack   |
| db_postgres  | postgres:16              | 5432         | 5432         | .env                      | web_stack   |
| pgadmin      | dpage/pgadmin4           | 8084         | 80           | .env                      | web_stack   |

- **Fichier de variables** : `.env`

### web_stackNJS (Node.js)

| Service      | Image                    | Port externe | Port interne | Variables d'environnement | Réseau      |
|--------------|--------------------------|--------------|--------------|---------------------------|-------------|
| web_njs      | img_web_njs (build local)| 8085         | 80           | var.env                   | web_stack   |

- **Fichier de variables** : `var.env`

---

## Commandes utiles

### Lancer les stacks

Depuis le dossier racine du projet :
```bash
# Stack MySQL
cd web_stackMP
docker compose up -d

# Stack PostgreSQL
cd ../web_stackPP
docker compose up -d

# Stack Node.js
cd ../web_stackNJS
docker compose up -d
```

### Construire et pousser les images

#### Pour Docker Hub
```bash
# web_stackMP
docker tag web_mp votre-login/apache:1.0
docker tag db_mysql votre-login/mysql:1.0
docker tag pma votre-login/phpmyadmin:1.0
docker push votre-login/apache:1.0
docker push votre-login/mysql:1.0
docker push votre-login/phpmyadmin:1.0

# web_stackPP
docker tag web_pp votre-login/apache-pp:1.0
docker tag db_postgres votre-login/postgres:1.0
docker tag pgadmin votre-login/pgadmin:1.0
docker push votre-login/apache-pp:1.0
docker push votre-login/postgres:1.0
docker push votre-login/pgadmin:1.0

# web_stackNJS
docker tag web_njs votre-login/nodejs:1.0
docker push votre-login/nodejs:1.0
```

#### Pour Harbinfo (IUT registry)
Pareil avec `harbinfo.iut.local` .

### Tester les applications

#### Stack MySQL
- http://localhost:8080/web_stackMP/CreationBDD.php
- http://localhost:8080/web_stackMP/ajoutVoituresGarage.php
- http://localhost:8080/web_stackMP/listeVoitures.php
- phpMyAdmin : http://localhost:8082 (identifiants dans `var.env`)

#### Stack PostgreSQL
- http://localhost:8083/src/bd/CreationBDD.php
- http://localhost:8083/src/ajoutVoituresGarage.php
- http://localhost:8083/src/listeVoitures.php
- pgAdmin : http://localhost:8084 (identifiants dans `.env`)

#### Stack Node.js
- Accueil produits : http://localhost:8085
- AJAX : pages `ajout.html`, `creation.html`, `liste.html` dans `/public`

---
