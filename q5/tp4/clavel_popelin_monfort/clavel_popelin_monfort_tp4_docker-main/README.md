# CLAVEL_POPELIN_MONFORT_TP4_DOCKER

# TP4 Virtualisation sous Docker - compose, build, publication

Table des matières :

- [Partie 1 : web stackMP](#partie1)
  - [Partie 1.a](#partie1.a)
  - [Partie 1.b](#partie1.b)
  - [Partie 1.c](#partie1.c)
  - [Partie 1.d](#partie1.d)
  - [Partie 1.e](#partie1.e)
  - [Partie 1.f](#partie1.f)
- [Partie 2 : web_stackPP avec postgres/pgadmin4](#partie2)
- [Partie 3 : Etude d’une stack web existante](#partie3)
  - [Partie 3.1](#partie3.1)
  - [Partie 3.2](#partie3.2)
  - [Partie 3.3](#partie3.3)
  - [Partie 3.4](#partie3.4)
- [Partie 4 : Est-il possible d’exécuter ce Dockerfile depuis un fichier docker-compose.yml](#partie4)
- [Partie 5 : HTTPS](#partie5)

- Tableau des services :

|  Service   | port externe | port interne | connexion |   Network   |   volume   |
|:----------:|:------------:|:------------:|:---------:|:-----------:|:----------:|
|   Apache   |     80     |      80      |     -     |  web_stack  | web files  |
|   mysql    |      -     |     3306     | root/root |  web_stack  | mysql_data |
| phpmyadmin |     80     |      80      | root/root |  web_stack  |     -      |


---
<a id="partie1"></a>
# Partie 1 : web_stackMP

<a id="partie1.a"></a>
## a)

https://gitlabinfo.iutmontp.univ-montp2.fr/clavelt/clavel_popelin_monfort_tp4_docker

---

<a id="partie1.b"></a>
## b)

Lancement de la stack :

```shell
docker compose build
docker compose up -d
```

Arrêt :

```shell
docker compose down
```

<a id="partie1.c"></a>
## c)

Fichier [docker-compose.yml](web_stackMP/docker-compose.yml)

Fichier [dockerFile](web_stackMP/dockerfile)

<a id="partie1.d"></a>
## d)

Fichier [var.env](web_stackMP/var.env)

Test :
```shell
 docker compose up -d
 ```

<a id="partie1.e"></a>
## e)

### Tag et publication des images

Connexion docker hub :
```bash
docker login
```

Tag :
```bash
docker tag web_stackmp-web votrelogin/apache:1.0
docker tag mysql:9.2 votrelogin/mysql:1.0
docker tag phpmyadmin/phpmyadmin votrelogin/phpmyadmin:1.0
```
Push :
```bash
docker push votrelogin/apache:1.0
docker push votrelogin/mysql:1.0
docker push votrelogin/phpmyadmin:1.0
```
Même principe pour harbinfo :
```bash
docker login harbinfo
docker push harbinfo_login/apache:1.0
docker push harbinfo_login/mysql:1.0
docker push harbinfo_login/phpmyadmin:1.0
```

<a id="partie1.f"></a>
## f)

Pull depuis Docker Hub
```bash
docker login
docker pull votrelogin/apache:1.0
docker run -p 80:80 votrelogin/apache:1.0
```

Variables d’environnement à utiliser si nécessaire.

<a id="partie1.g"></a>
## g)

Pull depuis Harbinfo
```bash
docker login harbinfo
docker pull harbinfo_login/apache:1.0
docker run -p 80:80 harbinfo_login/apache:1.0
```
<a id="partie2"></a>
# Partie 2 : web_stackPP avec postgres/pgadmin4

### Deuxième stack : web_stackPP

Création du dossier :

```bash
mkdir web_stackPP
cd web_stackPP
```

Services :

- apache + php 8.2
- postgres
- pgadmin4

Commandes identiques :

```bash
docker compose build
docker compose up -d
docker tag
docker push
docker pull
```

```bash
docker compose build web
docker tag web_stackpp-web votrelogin/apache:1.0
docker tag postgres:16-alpine votrelogin/postgres:1.0
docker tag dpage/pgadmin4:latest votrelogin/pgadmin:1.0
```

```bash
docker push votrelogin/apache:1.0
docker push votrelogin/postgres:1.0
docker push votrelogin/pgadmin:1.0
```

Tag et Push pour Harbinfo
```bash
docker login harbinfo
docker push votrelogin/apache:1.0
docker push votrelogin/postgres:1.0
docker push votrelogin/pgadmin:1.0
```

<a id="partie3"></a>
# Partie 3 : Etude d’une stack web existante

<a id="partie3.1"></a>
## 1. Quelle commande permet de builder cette image ? 
Le build génère des erreurs (à corriger éventuellement) ?
```bash
docker build -t mon-imagephp:8.3
```

<a id="partie3.2"></a>
## 2. Quelle commande permet de run l’image build ?
```bash
docker run -d -p 80:80 --name mon_conteneur_php mon-image-php:8.3
```

<a id="partie3.3"></a>
## 3. Quelles sont les fonctionnalités installées ?

- Apache (serveur web)
- PHP avec extensions :
    - PDO
    - PostgreSQL support
- Node.js / npm
- outils système :
    - wget
    - nano
    - unzip
- bibliothèques :
    - libpq-dev
    - libsqlite3-dev
    - libaio-dev
- Xdebug pour le debug PHP

<a id="partie4"></a>
## 4. Est-il possible d’exécuter ce Dockerfile depuis un fichier docker-compose.yml

Oui.

Il est possible d'exécuter ce Dockerfile via un fichier `docker-compose.yml`
en utilisant la directive `build`.

Exemple :

```yaml
services:
  web:
    build: .
    ports:
      - "80:80"
```
<a id="partie5"></a>
# 5. HTTPS 

<a id="partie5.code"></a>
### Code ajouté

```dockerfile
# Enable SSL module
RUN a2enmod ssl

# Generate self-signed certificate
RUN mkdir /etc/apache2/ssl && \
    openssl req -x509 -nodes -days 365 \
    -newkey rsa:2048 \
    -keyout /etc/apache2/ssl/apache.key \
    -out /etc/apache2/ssl/apache.crt \
    -subj "/C=FR/ST=Occitanie/L=Montpellier/O=IUT/CN=localhost"

# Configure Apache SSL site
RUN echo "<VirtualHost *:443>
    ServerName localhost
    DocumentRoot /var/www/html

    SSLEngine on
    SSLCertificateFile /etc/apache2/ssl/apache.crt
    SSLCertificateKeyFile /etc/apache2/ssl/apache.key

    <Directory /var/www/html>
        AllowOverride All
    </Directory>
</VirtualHost>" > /etc/apache2/sites-available/default-ssl.conf

# Enable SSL site
RUN a2ensite default-ssl

# Expose HTTPS port
EXPOSE 443
```
<a id="partie5.test"></a>
## Test

### Builder l’image
```bash
docker build -t web_https .
```

### Lancer le conteneur
```bash
docker run -p 80:80 -p 443:443 web_https
```

### URLs pour tester


HTTP :

http://localhost

HTTPS :

https://localhost

Le navigateur affichera : 
> Connexion non sécurisée

C’est normal, car le certificat est auto-signé.

### Via docker-compose

Dans docker-compose.yml, il faut juste exposer le port :
```yaml
services:
  web:
    build: .
    ports:
      - "80:80"
      - "443:443"
```

Puis lancer :

```bash
docker compose up --build
```