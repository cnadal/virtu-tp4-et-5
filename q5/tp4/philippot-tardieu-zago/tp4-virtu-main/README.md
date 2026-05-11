# Composition du groupe

Philippot Ewen

Tardieu Paul

Zago Nicolas

# Table des matières

# Services

## Mysql (MP)

### Dans le dossier web_stackMP

| Nom du service | Port externe | Port interne | Infos de connexion                               | réseau | volume(s) en interne                   | volume(s) en externe                         |
| :------------- | ------------ | ------------ | ------------------------------------------------ | ------ | -------------------------------------- | -------------------------------------------- |
| Apache         | 89           | 80           | navigateur sur localhost:89                      | bridge | ./html                                 | /var/www/html                                |
| Mysql          | 336O6         | 3306         | par un pdo de apache ou avec un client cli mysql | bridge | ./garageM.sql | /docker-entrypoint-initdb.d/garageM.sql |
| Phpmyadmin     | 82           | 80           | navigateur sur localhost:82                      | bridge |

## Postgresql (PP)

### Dans le dossier web_stackPP

| Nom du service | Port externe | Port interne | Infos de connexion                               | réseau | volume(s) en interne       | volume(s) en externe |
| :------------- | ------------ | ------------ | ------------------------------------------------ | ------ | -------------------------- | -------------------- |
| Apache         | 90           | 80           | navigateur sur localhost:90                      | bridge | ./html, ./php.ini                     | /var/www/html, /etc/php.ini       |
| Postgresql     | 5432         | 5432         | par un pdo de apache ou avec un client cli pgsql | bridge | ./Voitures.sql         | /docker-entrypoint-initdb.d/Voitures.sql  |
| Postgresadmin  | 91           | 80           | navigateur sur localhost:91                      | bridge |  |     |

## NodeJS (NJS)

### Dans le dossier web_stackNJS

| Nom du service | Port externe | Port interne | Infos de connexion                               | réseau | volume(s) en interne       | volume(s) en externe |
| :------------- | ------------ | ------------ | ------------------------------------------------ | ------ | -------------------------- | -------------------- |
| NodeJS         | 3000           | 3000           | navigateur sur localhost:3000                    | bridge |                      |         |
| Mysql     | 3306         | 33606         | par un pdo de apache ou avec un client cli pgsql | bridge | ./garageM.sql   | /docker-entrypoint-initdb.d/garageM.sql  |
| Apache  | 93           | 80           | appel api, surtout par node                      | bridge | ./php | /var/www/html   |

# Lancer les stack

## Mysql (MP)

```bash
cd web_stackMP
docker compose up -d
```

## Postgresql (PP)

```bash
cd web_stackPP
docker compose up -d
```

## NodeJS (NJS)

```bash
cd web_stackNJS
docker compose up -d
```

# Taguer et publier les images

Remarque : les tags des images docker de mysql et pgsl ne respectent pas la convention données, car les commandes ont été faites avant.

## Mysql (MP)

### Docker hub

```bash
docker tag apache-web89 yvaniak/virtu-apache:1.0
```

```bash
docker tag mysql:9.2 yvaniak/virtu-mysql:1.0
```

```bash
docker tag phpmyadmin yvaniak/virtu-phpmyadmin:1.0
```

```bash
docker push yvaniak/virtu-apache:1.0
```

```bash
docker push yvaniak/virtu-mysql:1.0
```

```bash
docker push yvaniak/virtu-phpmyadmin:1.0
```

### Harbinfo

```bash
docker tag apache-web89 harbinfo.iutmontp.univ-montp2.fr/philippote/virtu-apache:1.0
```

```bash
docker tag mysql:9.2 harbinfo.iutmontp.univ-montp2.fr/philippote/virtu-mysql:1.0
```

```bash
docker tag phpmyadmin harbinfo.iutmontp.univ-montp2.fr/philippote/virtu-phpmyadmin:1.0
```

```bash
docker push harbinfo.iutmontp.univ-montp2.fr/philippote/virtu-mysql:1.0
```

```bash
docker push harbinfo.iutmontp.univ-montp2.fr/philippote/virtu-phpmyadmin:1.0
```

```bash
docker push harbinfo.iutmontp.univ-montp2.fr/philippote/virtu-apache:1.0
```

## Postgresql (PP)

Remarque : beaucoup des layers des images pushés n'ont pas besoin d'être vraiment envoyés car elles sont reprises des images de base ou d'autres projets, en étant mises en commun

### Docker hub

```bash
docker tag apache-web90 yvaniak/pp-apache:1.0
```

```bash
docker tag postgres yvaniak/pp-postgres:1.0
```

```bash
docker tag dpage/pgadmin4 yvaniak/pp-pga:1.0
```

```bash
docker push yvaniak/pp-apache:1.0
```

```bash
docker push yvaniak/pp-postgres:1.0
```

```bash
docker push yvaniak/pp-pga:1.0
```

### Harbinfo

```bash
docker tag apache-web90 harbinfo.iutmontp.univ-montp2.fr/philippote/pp-apache:1.0
```

```bash
docker tag postgre harbinfo.iutmontp.univ-montp2.fr/philippote/pp-postgres:1.0
```

```bash
docker tag dpage/pgadmin4 harbinfo.iutmontp.univ-montp2.fr/philippote/pp-pga:1.0
```

```bash
docker push harbinfo.iutmontp.univ-montp2.fr/philippote/pp-apache:1.0
```

```bash
docker push harbinfo.iutmontp.univ-montp2.fr/philippote/pp-postgres:1.0
```

```bash
docker push harbinfo.iutmontp.univ-montp2.fr/philippote/pp-pga:1.0
```

## NodeJS (NJS)

### Docker hub

```bash
docker tag web_njs yvaniak/njs-web_njs:1.0
```

```bash
docker tag db_mysql yvaniak/njs-db_mysql:1.0
```

```bash
docker tag web_mp yvaniak/njs-web_mp:1.0
```

```bash
docker push yvaniak/njs-web_njs:1.0
```

```bash
docker push yvaniak/njs-db_mysql:1.0
```

```bash
docker push yvaniak/njs-web_mp:1.0
```

### Harbinfo

```bash
docker tag web_njs harbinfo.iutmontp.univ-montp2.fr/philippote/njs-web_njs:1.0
```

```bash
docker tag db_mysql harbinfo.iutmontp.univ-montp2.fr/philippote/njs-db_mysql:1.0
```

```bash
docker tag web_mp harbinfo.iutmontp.univ-montp2.fr/philippote/njs-web_mp:1.0
```

```bash
docker push harbinfo.iutmontp.univ-montp2.fr/philippote/njs-web_njs:1.0
```

```bash
docker push harbinfo.iutmontp.univ-montp2.fr/philippote/njs-db_mysql:1.0
```

```bash
docker push harbinfo.iutmontp.univ-montp2.fr/philippote/njs-web_mp:1.0
```

# Etude d’une stack web existante

## 1. Quelle commande permet de builder cette image ? Le build génère des erreurs (à corriger éventuellement)?

La commande permettant de builder est (elle permet également de tag l'image en webstackexistante:latest) : 

```bash
docker build . -t webstackexistante
```

Il y a une erreur dans le dockerfile, l'instruction COPY permettant la copie des fichiers du dossier courant dans le conteneur était commentée.

En la décommentant, on permet aux fichier du dossier courant d'être copié dans le conteneur, la rendent utile.

Car si on la laissait décomenttée, le conteneur n'aurait aucun fichier source, ne pouvont donc servir à rien.

## 2. Quelle commande permet de run l’image build ?

La commande permettant de run l'image est :

```bash
docker run webstackexistante
```
## 3. Quelles sont les fonctionnalités installées ?

Voici une liste des fonctionnalités installées, en plus d'apache et php déjà dans l'image php:8.3-apache :

- Les pdo pour mysql, pgsql et sqlite, pour pouvoir effectuer des requêtes SQL sur ces différents SGDB
- Composer, le gestionnaire de paquets de php
- npm, le gestionnaire de paquets de javascript
- xdebug, un debugueur php, permettant nottament l'analyse de coverage des tests

## 4. Est-il possible d’exécuter ce Dockerfile depuis un fichier docker-compose.yml ?

Oui, c'est possible, avec un fichier compose comme celui-ci :

```yaml
services:
  web_existante:
   build: .
   ports:
   - "80:80"
```

# HTTPS ?

## Trouver le moyen d’ajouter dans le dockerfile de la question précédente le support de https.

### Quel code ajoutez-vous ?

On ajoute ce code : 

```dockerfile
RUN mkdir -p /usr/local/apache2/conf/ssl && \
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/localhost.key \
    -out /etc/ssl/certs/localhost.crt \
    -subj "/C=FR/ST=Occitanie/L=Sete/O=IUT/CN=localhost"

RUN cat > /etc/apache2/sites-available/my-ssl.conf <<EOF 
<VirtualHost *:443>
    DocumentRoot "/var/www/html"
    ServerName localhost

    SSLEngine on
    SSLCertificateFile "/etc/ssl/certs/localhost.crt"
    SSLCertificateKeyFile "/etc/ssl/private/localhost.key"
</VirtualHost>
EOF

RUN a2enmod ssl && \
    a2enmod rewrite && \
    a2dissite 000-default default-ssl && \
    a2ensite my-ssl

EXPOSE 80 443
```

La première commande RUN crée une clé ssl.

La seconde créée un fichier de config pour apache, concernant le ssl

La troisième configure apache en tant que tel, il active le ssl, active le module mod_rewrite, désactive la config par defaut et active la config créée plus haut.

## Testez votre solution.

### Donnez l’url ou les urls

On a testé sur la partie d'avant, la stack web existante, utilisant un port 80, et du coup maintenant 443

l'url est : https://localhost/