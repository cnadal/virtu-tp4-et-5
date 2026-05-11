TP4 : Virtualisation sous Docker - compose, build, publicationTP4 : Virtualisation sous Docker - compose, build,
publication-

1. Quelle commande permet de builder cette image ? Le build génère des erreurs (à
   corriger éventuellement)?

   docker build .

2. Quelle commande permet de run l’image build ?

   docker images
   docker run -d -p 8080:80 -v $(pwd):/var/www/html (id de l'image)


3. Quelles sont les fonctionnalités installées ?

    - Serveur web
    - Bases de données
    - Composer
    - Node.js + npm
    - Xdebug
    - mod_rewrite

4. Est-il possible d’exécuter ce Dockerfile depuis un fichier docker-compose.yml ?

   Oui, build une image anonyme.

Https ?
● Trouver le moyen d’ajouter dans le dockerfile de la question précédente le support de https.
○ Quel code ajoutez-vous ?

    RUN a2enmod ssl && openssl req -x509 -newkey rsa:2048 
      -keyout /etc/ssl/serveurApache.key -out /etc/ssl/serveurApache.crt -days 365 -nodes -subj "/CN=localhost"

   Active SSL Apache + génère certificat 2048 bits pour localhost avec les clés , valide 1 an sans mot de passe.

● Testez votre solution.

    docker build test .
    docker run -d -p 8080:80 -p 443:443 test

○ Donnez l’url ou les urls.
HTTP:  http://localhost:8080
HTTPS: https://localhost:443