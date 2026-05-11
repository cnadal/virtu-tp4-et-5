## Table des matières
- [Web Stack MP (MySQL / PHPMyAdmin)](#web-stack-mp-mysql--phpmyadmin)
- [Web Stack PP (Postgres / PgAdmin4)](#web-stack-pp-postgres--pgadmin4)
- [Web Stack NJS (NodeJS)](#web-stack-njs-nodejs)
- [Étude d'une stack web existante](#étude-dune-stack-web-existante)

---

## Web Stack MP (MySQL / PHPMyAdmin)
Les services installés dans le dossier `web_stackMP` sont :

| Nom du service | Port externe | Port interne | Réseau | Volume(s) |
| :--- | :--- | :--- | :--- | :--- |
| **mysql** | `3306` | `3306` | `pontMP` | `./mysql1/mysql:/var/lib/mysql`<br>`./mysql1/custom.cnf:/etc/mysql/conf.d/custom.cnf` |
| **apache** | `82` | `80` | `pontMP` | Aucun |
| **phpmyadmin** | `89` | `80` | `pontMP` | `./web89/html:/var/www/html`<br>`./web89/apache/sites_enabled:/etc/apache2/sites_enabled`<br>`./web89/php/custom-php.ini:/usr/local/etc/php/conf.d/custom-php.ini` |

### Lancer la stack
1. Ouvrez un terminal dans le dossier `web_stackMP`.
2. Exécutez la commande suivante pour construire les images et démarrer le conteneur en arrière-plan :
```bash
docker-compose up -d --build
```
Pour arrêter la stack : `docker-compose down`

### Publier les images
**Sur Docker Hub :**
```bash
docker tag web_stackmp-apache:latest prudhommet/apache:1.0
docker tag web_stackmp-mysql:latest prudhommet/mysql:1.0
docker tag web_stackmp-phpmyadmin:latest prudhommet/phpmyadmin:1.0

docker push prudhommet/apache:1.0
docker push prudhommet/mysql:1.0
docker push prudhommet/phpmyadmin:1.0
```

**Sur Harbinfo :**
```bash
docker tag web_stackmp-apache:latest harbinfo.iutmontp.univ-montp2.fr/prudhommet/apache:1.0
docker tag web_stackmp-mysql:latest harbinfo.iutmontp.univ-montp2.fr/prudhommet/mysql:1.0
docker tag web_stackmp-phpmyadmin:latest harbinfo.iutmontp.univ-montp2.fr/prudhommet/phpmyadmin:1.0

docker push harbinfo.iutmontp.univ-montp2.fr/prudhommet/apache:1.0
docker push harbinfo.iutmontp.univ-montp2.fr/prudhommet/mysql:1.0
docker push harbinfo.iutmontp.univ-montp2.fr/prudhommet/phpmyadmin:1.0
```

*(Pensez à utiliser `docker login` avant de push)*

---

## Web Stack PP (Postgres / PgAdmin4)

Les services installés dans le dossier `web_stackPP` sont :

| Nom du service | Port externe | Port interne | Informations de connexion | Réseau | Volume(s) |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **postgres** | `5432` | `5432` | postgres / secret (selon vos v. d'env.) | `pontPP` (bridge) | `./pg_data:/var/lib/postgresql/data` |
| **apache** | `83` | `80` | [http://localhost:83](http://localhost:83) | `pontPP` (bridge) | Aucun (copie via le Dockerfile avec `COPY`) |
| **pgadmin4** | `5050` | `80` | [http://localhost:5050](http://localhost:5050)<br>admin@admin.com / secret | `pontPP` (bridge) | `./pgadmin_data:/var/lib/pgadmin` |

### Lancer la stack
1. Ouvrez un terminal dans le dossier `web_stackPP`.
2. Exécutez la commande : `docker-compose up -d --build`

### Publier les images
De la même manière que pour MP, taggez vos images (`prudhommet/postgres:1.0`, etc.) puis instanciez un `docker push` vers le Docker Hub et le registry Harbinfo.

---
**Tester les scripts sur la Web Stack MP :**
- [http://localhost/web_stackMP/creationBDD.php](http://localhost/web_stackMP/creationBDD.php) *(modifiez le port de l'URL si vous utilisez :82)*
- [http://localhost/web_stackMP/ajoutVoituresGarage.php](http://localhost/web_stackMP/ajoutVoituresGarage.php)
- [http://localhost/web_stackMP/listeVoitures.php](http://localhost/web_stackMP/listeVoitures.php)

**Tester les scripts sur la Web Stack PP :**
- [http://localhost/web_stackPP/creationBDD.php](http://localhost/web_stackPP/creationBDD.php) *(modifiez le port de l'URL si vous utilisez :83)*
- [http://localhost/web_stackPP/ajoutVoituresGarage.php](http://localhost/web_stackPP/ajoutVoituresGarage.php)
- [http://localhost/web_stackPP/listeVoitures.php](http://localhost/web_stackPP/listeVoitures.php)

---

## Web Stack NJS (NodeJS)

Dans le dossier `web_stackNJS`, la stack contient :

| Nom du service | Port externe | Port interne | Informations de connexion | Réseau | Volume(s) |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **web** (`web_stack_njs`) | `8080` | `3000` | [http://localhost:8080](http://localhost:8080) | `default` (bridge) | `./public:/usr/src/app/public`<br>`./server.js:/usr/src/app/server.js` |

### Lancer la stack
Dans le dossier `web_stackNJS`, exécutez la commande correspondante :
```bash
docker-compose up -d --build
```
*Pour arrêter la stack NodeJS : `docker-compose down`*

### Consignes implémentées (AJAX)
La stack propose des pages web faisant des appels AJAX sur les routes suivantes de votre API :
- [http://localhost/web_stackNJS/creationBDD.php](http://localhost/web_stackNJS/creationBDD.php)
- [http://localhost/web_stackNJS/ajoutVoituresGarage.php](http://localhost/web_stackNJS/ajoutVoituresGarage.php)
- [http://localhost/web_stackNJS/listeVoitures.php](http://localhost/web_stackNJS/listeVoitures.php)

### Publier l'image
**Sur Docker Hub :**
```bash
docker tag web_stacknjs-web:latest prudhommet/web_stacknjs-web:latest
docker push prudhommet/web_stacknjs-web:latest
```

**Sur Harbinfo :**
```bash
docker tag web_stacknjs-web:latest harbinfo.iutmontp.univ-montp2.fr/prudhommet/web_stacknjs-web:latest
docker push harbinfo.iutmontp.univ-montp2.fr/prudhommet/web_stacknjs-web:latest
```

---

## Étude d'une stack web existante

**1. Quelle commande permet de builder cette image ? Le build génère des erreurs ?**
```bash
docker build -t stack-existante .
```


**2. Quelle commande permet de run l'image build ?**
```bash
docker run -d -p 80:80 --name conteneur-web stack-existante
```

**3. Quelles sont les fonctionnalités installées ?**
- Apache2 avec réécriture (`mod_rewrite`) et serveur PHP 8.3.
- PHP PDO (MySQL, PostgreSQL, SQLite).
- Les commandes sytèmes : nano, unzip, wget.
- Les gestionnaires de paquets : Composer pour PHP, et NVM avec NPM/NodeJS.
- L'outil de débogage Xdebug, directement configuré pour l'hôte Docker sur le port 9003.

**4. Est-il possible d'exécuter ce Dockerfile depuis un fichier docker-compose.yml ?**
Oui, il suffit de définir un service de type :
```yaml
services:
  web:
    build: .
    ports:
      - "80:80"
```

### Support HTTPS
Pour configurer HTTPS dans ce Dockerfile, on active le module `ssl` et on expose le port 443. Code supplémentaire ajouté à la fin du Dockerfile :

```dockerfile
RUN a2enmod ssl && a2ensite default-ssl.conf

EXPOSE 443
```
Après avoir regénéré l'image et redémarré le conteneur en mappant les ports (ex: `-p 80:80 -p 443:443`), on accède au site via l'URL :
**[https://localhost](https://localhost)**
