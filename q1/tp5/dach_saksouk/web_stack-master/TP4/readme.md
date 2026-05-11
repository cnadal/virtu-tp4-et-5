# Documentation TP4 - Virtualisation sous Docker

Ce dépôt contient les fichiers et la documentation pour le TP4 sur Docker, implémentant différentes architectures web avec Docker Compose.

## Table des Matières
1. [Services mis en place](#services-mis-en-place)
2. [Comment lancer les stacks Docker](#comment-lancer-les-stacks-docker)
3. [Publication des Images (Tags & Push)](#publication-des-images)
4. [Étude d'une stack web existante](#etude-dune-stack-web-existante)

---

## Services mis en place

| Stack | Nom du service | Port Externe | Port Interne | Informations de connexion | Réseau | Volume(s) |
|---|---|---|---|---|---|---|
| **web_stackMP** | `web_mp` | 8081 | 80 | - | `web_stack` | Aucun |
| | `mysql_db` | 3306 | 3306 | `root`:`root` / DB:`garageM` | `web_stack` | `db_data` (`/var/lib/mysql`) |
| | `pma` | 8082 | 80 | Via hôte : `mysql_db` | `web_stack` | Aucun |
| **web_stackPP** | `web_pp` | 8083 | 80 | - | `web_stack` | Aucun |
| | `postgres_db` | 5432 | 5432 | `admin`:`1234` / DB:`garageP` | `web_stack` | `pg_data` (`/var/lib/postgresql/data`)|
| | `pgadmin4` | 8084 | 80 | `admin@admin.com`:`admin` | `web_stack` | Aucun |
| **web_stackNJS**| `web_njs` | 8085 | 3000 | Web frontend (Express) | `web_stack` | Aucun |

---

## Comment lancer les stacks Docker

Pour lancer chaque stack, naviguez dans son répertoire et exécutez `docker compose up -d --build`.

**Pour la stack MySQL (web_stackMP) :**
```bash
cd web_stackMP
docker compose up -d --build
```
> - API Voitures / Application : http://localhost:8081/listeVoitures.php
> - Gestion BDD MySQL (phpMyAdmin) : http://localhost:8082

**Pour la stack PostgreSQL (web_stackPP) :**
```bash
cd ../web_stackPP
docker compose up -d --build
```
> - API Voitures / Application : http://localhost:8083/bd/creationBDD.php
> - Gestion BDD PostgreSQL (PgAdmin4) : http://localhost:8084

**Pour la stack Node.js (web_stackNJS) :**
```bash
cd ../web_stackNJS
docker compose up -d --build
```
> - Application Frontend Node (AJAX) : http://localhost:8085

*Pour arrêter une stack démarrée avec compose, exécutez `docker compose down` dans le même dossier.*

---

## Publication des Images

Voici les commandes pour construire, taguer et publier les images sur Docker Hub ainsi que sur le registre Harbinfo.
*(Pensez à remplacer `votrelogin` par votre vrai nom d'utilisateur).*

### Build d'une image locale (Exemple: web_app de web_stackMP)
```bash
cd web_stackMP
docker build -t image_php_apache .
```

### Publication sur Docker Hub
```bash
# 1. Tag des images créées localement
docker tag image_php_apache votrelogin/apache_php:1.0
docker tag mysql:9.2 votrelogin/mysql:1.0
docker tag phpmyadmin votrelogin/phpmyadmin:1.0

# 2. Login & Publication
docker login
docker push votrelogin/apache_php:1.0
docker push votrelogin/mysql:1.0
docker push votrelogin/phpmyadmin:1.0
```

### Publication sur le registre Harbinfo
```bash
# 1. Tag des images ajoutant harbinfo/ ou l'URL du registre selon la configuration
docker tag image_php_apache harbinfo/votrelogin/apache_php:1.0
docker tag mysql:9.2 harbinfo/votrelogin/mysql:1.0
docker tag phpmyadmin harbinfo/votrelogin/phpmyadmin:1.0

# 2. Login & Publication
docker login harbinfo
docker push harbinfo/votrelogin/apache_php:1.0
docker push harbinfo/votrelogin/mysql:1.0
docker push harbinfo/votrelogin/phpmyadmin:1.0
```

Pour exécuter votre image après l'avoir pull :
```bash
docker pull votrelogin/apache_php:1.0
docker run -d -p 8080:80 \
  -e MYSQL_ROOT_PASSWORD=root \
  votrelogin/apache_php:1.0
```

---

## Étude d'une stack web existante

**1. Quelle commande permet de builder cette image PHP 8.3-apache ? Le build génère-t-il des erreurs ?**
La commande pour builder le Dockerfile fourni est : `docker build -t previ_php_apache .`
Il est possible qu'il y ait des erreurs ou instabilités lors du build selon le contexte car `COPY . /var/www/html` est commenté et l'installation de certains paquets peut nécessiter de configurer le mode non-interactif de `apt`.

**2. Quelle commande permet de lancer (run) l'image buildée ?**
```bash
docker run -d -p 8080:80 -v $(pwd):/var/www/html previ_php_apache
```

**3. Quelles sont les fonctionnalités installées ?**
L'image basée sur PHP 8.3 intègre : Nano, Git, Wget, les extensions PDO (MySQL, PgSQL, SQLite), Composer (gestionnaire de paquets PHP), Node.js (via NVM), npm, Xdebug pour le débogage de code distant, mod_rewrite activé pour Apache et la Timezone paramétrée sur l'Europe.

**4. Est-il possible d'exécuter ce Dockerfile depuis un fichier `docker-compose.yml` ?**
Oui, il suffit d'ajouter une clé `build: .` dans la déclaration de service du `docker-compose.yml`. Cela construira (ou reconstruira) l'image à partir du Dockerfile local.

### Https ?
● **Trouver le moyen d'ajouter le support de HTTPS dans le dockerfile précédent. Quel code ajoutez-vous ?**
Nous devons activer `ssl`, générer un certificat auto-signé à la volée avec `openssl` et activer la configuration par défaut du site HTTPS pour Apache.
On ajoute ce bloc avant l'instruction `EXPOSE` :

```dockerfile
# Installation de OpenSSL et activation du module ssl d'Apache
RUN apt-get update && apt-get install -y openssl && \
    a2enmod ssl && \
    openssl req -x509 -newkey rsa:2048 -keyout /etc/ssl/certs/serveur.key \
    -out /etc/ssl/certs/serveur.crt -days 365 -nodes -subj "/CN=localhost" && \
    sed -i -e 's/SSLCertificateFile.*$/SSLCertificateFile \/etc\/ssl\/certs\/serveur.crt/' /etc/apache2/sites-available/default-ssl.conf && \
    sed -i -e 's/SSLCertificateKeyFile.*$/SSLCertificateKeyFile \/etc\/ssl\/certs\/serveur.key/' /etc/apache2/sites-available/default-ssl.conf && \
    a2ensite default-ssl.conf

EXPOSE 80 443
```

● **Testez votre solution. Donnez l'url ou les urls.**
Après build (`docker build -t apache_https .`), on exécute l'image en forwardant les deux ports réseau :
```bash
docker run -d -p 8080:80 -p 8443:443 apache_https
```
- **URL HTTP:** `http://localhost:8080`
- **URL HTTPS:** `https://localhost:8443`
