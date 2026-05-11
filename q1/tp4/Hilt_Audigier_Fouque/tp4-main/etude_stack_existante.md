# Étude d'une stack web

1. Build
Pour builder l'image :
`docker build -t stack_php .`
Le build peut planter sur les paquets `apt` (souvent des mises à jour qui manquent) ou sur l'install de NVM (les scripts de téléchargement buggent parfois).

2. Run
Pour lancer l'image buildée :
`docker run -d -p 8080:80 test_image`

3. Fonctionnalités
les fonctionnalités installées :
- Apache avec PHP 8.3.
- Les extensions de BDD : pdo pour mysql, postgres et sqlite.
- Des outils de base : git, nano, wget, unzip.
- Composer (PHP) et npm (via NVM).
- Xdebug pour le déshabillage de code (port 9003).
- Timezone sur Paris.

4. Docker-compose et HTTPS
Oui on peut l'appeler direct depuis un compose avec un simple `build: .` dans un service.

HTTPS :
Pour ajouter le HTTPS, il faut activer le SSL dans le Dockerfile :
dockerfile :
    -> Activer ssl dans apache
    RUN a2enmod ssl && a2ensite default-ssl
    EXPOSE 443

Test URL :
Après avoir mappé le port 443 sur le 8443 (car le 80 est déjà pris) :
- https://localhost:8443/
En testant sur le navigateur, on a une alerte de sécurité ("Votre connexion n'est pas privée") parce qu'on utilise le certificat auto-signé par défaut d'Apache. Mais en cliquant sur "Paramètres avancés" et "Continuer", le serveur répond bien en HTTPS.
