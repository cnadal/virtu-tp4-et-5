# TP4 - Rendu 
## Ryssen - Schrader

## Table of Contents
- [Introduction](#Introduction) 
- [Apache php](#Apache) Port 89
- [mysql](#mysql)
- [phpmyadmin](#phpmyadmin) Port 82
- [Tag et publication](#tag-et-publication)
---

## Introduction
Tout les services qui suivent sont mis sur le network bridge qui permet de relier les conteneurs entre eux.

### Apache
Installation d'un service apache-php en version 8.2 avec nano, git et pdo pour mysql. Ouverture sur le potr 89.

### mysql 
Installation d'une base de donnée mysql en version 9.2 . <br> Avec la configuration d'un fichier d'environnement

### phpmyadmin
Installation de phpMyAdmin sur le port 82 avec une fichier de configuration pma.env .

### Tag et publication
Tout d'abord on fait les tags

> ### Pour Docker hub
> -> docker tag mysql:9.2 steban1705/mysql:1.0<br>
> -> docker tag phpmyadmin:latest steban1705/pma:1.0<br>
> -> docker tag web89tp4:latest steban1705/apache:1.0

> ### Pour harbinfo
> -> docker tag mysql:9.2 harbinfo.iutmontp.univ-montp2.fr/schraders/mysql:1.0<br>
> -> docker tag phpmyadmin:latest harbinfo.iutmontp.univ-montp2.fr/schraders/pma:1.0<br>
> -> docker tag web89tp4:latest harbinfo.iutmontp.univ-montp2.fr/schraders/apache:1.0

Ensuite on se log soit sur docker hub soit sur harb info puis on push à l'aide de ```docker push leTag:version```
>### Exemple
>docker login (puis faire connexion)<br>
>docker push steban1705/mysql:1.0

>### Exemple2
>docker login harbinfo.iutmontp.univ-montp2.fr (puis faire connexion)<br>
>docker push harbinfo.iutmontp.univ-montp2.fr/schraders/apache:1.0