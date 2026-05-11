# TP5 - Projet final d’architecture Docker

## 1. Présentation du projet

Ce projet a pour objectif de déployer une infrastructure Docker Compose complète et sécurisée avec :

- un reverse proxy **Nginx** ;
- un serveur web applicatif **Apache/PHP** ;
- une base de données **MySQL master** ;
- une base de données **MySQL slave** pour la réplication ;
- un serveur d’authentification **Keycloak** ;
- une base **PostgreSQL** pour Keycloak ;
- un serveur de messagerie de test **Mailpit**.

L’ensemble de l’infrastructure est lancé avec une seule commande Docker Compose.

---

## 2. Architecture réseau

L’infrastructure repose sur **3 réseaux custom bridge** :

- **net_public** : zone exposée
- **net_db** : zone des bases de données applicatives
- **net_auth** : zone dédiée à l’authentification

### Répartition des services

- **proxy_nginx**
    - réseau : `net_public`
    - rôle : point d’entrée unique de la stack, accessible depuis la machine hôte sur le port 80

- **web_app**
    - réseaux : `net_public`, `net_db`
    - rôle : application PHP accessible via le reverse proxy

- **mysql_master**
    - réseau : `net_db`
    - rôle : reçoit les écritures

- **mysql_slave**
    - réseau : `net_db`
    - rôle : réplique les données du master

- **keycloak_auth**
    - réseaux : `net_public`, `net_auth`
    - rôle : authentification des utilisateurs

- **postgres_keycloak**
    - réseau : `net_auth`
    - rôle : base de données de Keycloak

- **mail_test**
    - réseau : `net_public`
    - rôle : interception des mails de test via Mailpit

---

## 3. Explication des flux réseau

### Accès utilisateur

L’utilisateur accède au site via :

- `http://tp5.local/index.php`

Le navigateur envoie la requête vers **proxy_nginx**, qui redirige ensuite le trafic vers **web_app**.

### Flux entre les conteneurs

- **proxy_nginx -> web_app**
    - communication HTTP interne sur `net_public`

- **web_app -> mysql_master**
    - lecture/écriture sur la base principale via `mysql_master:3306`

- **web_app -> mysql_slave**
    - lecture de la base répliquée via `mysql_slave:3306`

- **web_app -> keycloak_auth**
    - redirection utilisateur vers Keycloak pour l’authentification

- **keycloak_auth -> postgres_keycloak**
    - stockage des realms, clients, utilisateurs et mots de passe chiffrés

- **web_app -> mail_test**
    - envoi de mails de test via Mailpit

### Résumé logique

- le client n’accède jamais directement aux bases MySQL ;
- l’accès passe toujours par le reverse proxy ;
- la base métier est isolée sur `net_db` ;
- l’authentification est isolée sur `net_auth`.

---

## 4. Domaine local

Le projet utilise le domaine local :

- `tp5.local`

Il faut ajouter dans le fichier `hosts` de la machine :

### Sous Linux / macOS

Modifier le fichier :

```bash
sudo nano /etc/hosts
```

Puis ajouter la ligne :

```text
127.0.0.1 tp5.local
```

Vérification :

```bash
ping tp5.local
```

### Sous Windows

Modifier le fichier :

```text
c:\windows\System32\Drivers\etc\hosts
```

Puis ajouter la ligne :

```text
127.0.0.1 tp5.local
```

Vérification :

```bash
ping tp5.local
```

---

## 5. Commande de déploiement

Depuis le dossier du projet :

```bash
docker compose up -d --build
```

Cette commande :

- build l’image du serveur web à partir du `Dockerfile` ;
- lance tous les conteneurs de la stack ;
- crée les réseaux si nécessaire ;
- monte les volumes persistants.

Pour arrêter la stack :

```bash
docker compose down
```

---

## 6. Authentification

L’authentification du projet est gérée par Keycloak.

Fonctionnement :

- l’utilisateur ouvre `http://tp5.local/index.php` ;
- il est redirigé vers Keycloak s’il n’est pas connecté ;
- après connexion, il revient sur l’application ;
- il peut alors accéder aux pages :
  - `master.php`
  - `slave.php`
  - `mailpit.php`

---

## 7. Pages disponibles

### Page d’accueil

- `http://tp5.local/index.php`

### Base master

- `http://tp5.local/master.php`

Affiche les voitures de la base MySQL master.

### Base slave

- `http://tp5.local/slave.php`

Affiche les voitures de la base MySQL slave.

### Mailpit

- `http://tp5.local/mailpit.php`

Permet d’accéder à l’interface Mailpit après authentification.

---

## 8. Mode opératoire exact pour lier le master et le slave MySQL

### 8.1 Création de l’utilisateur de réplication sur le master

Connexion au conteneur master :

```bash
docker exec -it mysql_master mysql -uroot -p
```

Dans MySQL :

```sql
CREATE USER 'repl'@'%' IDENTIFIED BY 'replpass';
GRANT REPLICATION SLAVE, REPLICATION CLIENT ON *.* TO 'repl'@'%';
FLUSH PRIVILEGES;
SHOW BINARY LOG STATUS;
```

Noter les valeurs retournées dans :

- `File`
- `Position`

Exemple :

```text
mysql-bin.000001
157
```

Quitter MySQL :

```bash
exit
```

### 8.2 Configuration du slave

Connexion au conteneur slave :

```bash
docker exec -it mysql_slave mysql -uroot -p
```

Dans MySQL :

```sql
STOP REPLICA;
RESET REPLICA ALL;
```

Puis configurer la source de réplication :

```sql
CHANGE REPLICATION SOURCE TO
  SOURCE_HOST='mysql_master',
  SOURCE_USER='repl',
  SOURCE_PASSWORD='replpass',
  SOURCE_PORT=3306,
  SOURCE_LOG_FILE='mysql-bin.000001',
  SOURCE_LOG_POS=157,
  GET_SOURCE_PUBLIC_KEY=1;
```

Remplacer `SOURCE_LOG_FILE` et `SOURCE_LOG_POS` par les vraies valeurs obtenues sur le master.

Démarrer la réplication :

```sql
START REPLICA;
SHOW REPLICA STATUS\G
```

La réplication est correcte si les deux lignes suivantes sont à `Yes` :

```text
Replica_IO_Running: Yes
Replica_SQL_Running: Yes
```

Quitter MySQL :

```bash
exit
```

---

## 9. Création de la base métier et test de synchronisation

Connexion au master :

```bash
docker exec -it mysql_master mysql -uroot -p
```

Création de la base, de l’utilisateur applicatif et de la table :

```sql
CREATE DATABASE garage;
CREATE USER 'appuser'@'%' IDENTIFIED BY 'apppass';
GRANT ALL PRIVILEGES ON garage.* TO 'appuser'@'%';
FLUSH PRIVILEGES;

USE garage;

CREATE TABLE voitures (
  id INT AUTO_INCREMENT PRIMARY KEY,
  marque VARCHAR(50),
  modele VARCHAR(50),
  annee INT
);

INSERT INTO voitures (marque, modele, annee) VALUES
('Renault', 'Clio', 2018),
('Peugeot', '208', 2020),
('Citroen', 'C3', 2019);

SELECT * FROM voitures;
```

Quitter :

```bash
exit
```

Vérification sur le slave :

```bash
docker exec -it mysql_slave mysql -uroot -p -e "USE garage; SELECT * FROM voitures;"
```

---

## 10. Test demandé par le sujet

Le test final consiste à vérifier que la synchronisation fonctionne bien entre les deux bases.

Suppression d’un tuple dans le master :

```bash
docker exec -it mysql_master mysql -uroot -p -e "USE garage; DELETE FROM voitures WHERE id=1;"
```

Ensuite :

- recharger `http://tp5.local/master.php` ;
- recharger `http://tp5.local/slave.php`.

La ligne supprimée doit disparaître dans les deux pages.

---

## 11. Structure du projet

```text
tp5/
├── docker-compose.yml
├── Dockerfile
├── nginx.conf
├── .gitignore
├── .env / var.env
├── mysql/
│   ├── master.cnf
│   └── slave.cnf
└── src/
    ├── index.php
    ├── config.php
    ├── login.php
    ├── callback.php
    ├── auth.php
    ├── logout.php
    ├── master.php
    ├── slave.php
    └── mailpit.php
```

---

## 12. Fichiers principaux

- `docker-compose.yml` : définition complète de la stack
- `Dockerfile` : construction de l’image PHP/Apache
- `nginx.conf` : configuration du reverse proxy
- `mysql/master.cnf` : configuration MySQL master
- `mysql/slave.cnf` : configuration MySQL slave
- `.gitignore` : exclusion des fichiers sensibles et locaux
- `src/` : code PHP de l’application

---

## 13. Commandes utiles

Voir les conteneurs lancés :

```bash
docker ps
```

Voir les logs d’un conteneur :

```bash
docker logs proxy_nginx
docker logs web_app
docker logs mysql_master
docker logs mysql_slave
docker logs keycloak_auth
docker logs postgres_keycloak
docker logs mail_test
```

Voir les réseaux :

```bash
docker network ls
docker network inspect net_public
docker network inspect net_db
docker network inspect net_auth
```
