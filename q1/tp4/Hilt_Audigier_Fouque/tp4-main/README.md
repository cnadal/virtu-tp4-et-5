# TP4 Virtualisation
Ruddy Audigier / Raphael Fouque / Pascal Hilt

## Table des matières
- [Services mis en place](#services-mis-en-place)
- [Utilisation](#utilisation)
- [Publication](#publication)

## Services mis en place

| Service    | Port externe | Port interne | Informations de connexion         | Réseau    | Volume(s)                      |
|------------|-------------:|-------------:|-----------------------------------|-----------|--------------------------------|
| web90      | 90           | 80           | http://localhost:90               | web_stack | ./web90/html:/var/www/html     |
| mysql      | 3307         | 3306         | root / mdpRoot23                  | web_stack | mysql_data:/var/lib/mysql      |
| phpmyadmin | 83           | 80           | serveur = mysql                   | web_stack | aucun                          |
| **NJS**    |              |              |                                   |           |                                |
| web_njs    | 3001         | 3000         | http://localhost:3001             | web_stack | aucun                          |
| mysql_njs  | 3308         | 3306         | root / password                   | web_stack | njs_mysql_data:/var/lib/mysql  |

## Utilisation

Build de l'image web :

```bash
docker compose build
```

Lancement de la stack :

```bash
docker compose up -d
```

Vérification des conteneurs :

```bash
docker ps
```

Arrêt de la stack : (stoppe les conteneurs sans les supprimer)

```bash
docker compose stop
```
ou (arrête et supprime les conteneurs, les réseaux et les volumes associés)

```bash
docker compose down
```

Accès aux services :

- site web : http://localhost:90
- phpMyAdmin : http://localhost:83

Connexion à phpMyAdmin :

- utilisateur : root
- mot de passe : mdpRoot23
- serveur : mysql

## Publication

### Docker Hub

Connexion au registre Docker Hub :

```bash
docker login
```

Tag des images :

```bash
docker tag web90 votrelogin/apache:1.0
docker tag mysql:9.2 votrelogin/mysql:1.0
docker tag phpmyadmin:latest votrelogin/phpmyadmin:1.0
```

Publication des images :

```bash
docker push votrelogin/apache:1.0
docker push votrelogin/mysql:1.0
docker push votrelogin/phpmyadmin:1.0
```

### Harbinfo

Connexion au registre Harbinfo :

```bash
docker login harbinfo.iutmontp.univ-montp2.fr
```

Tag des images :

```bash
docker tag web90 harbinfo.iutmontp.univ-montp2.fr/votrelogin/apache:1.0
docker tag mysql:9.2 harbinfo.iutmontp.univ-montp2.fr/votrelogin/mysql:1.0
docker tag phpmyadmin:latest harbinfo.iutmontp.univ-montp2.fr/votrelogin/phpmyadmin:1.0
```

Publication des images :

```bash
docker push harbinfo.iutmontp.univ-montp2.fr/votrelogin/apache:1.0
docker push harbinfo.iutmontp.univ-montp2.fr/votrelogin/mysql:1.0
docker push harbinfo.iutmontp.univ-montp2.fr/votrelogin/phpmyadmin:1.0
```