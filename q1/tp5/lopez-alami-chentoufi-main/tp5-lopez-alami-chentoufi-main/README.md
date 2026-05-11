Schéma explicatif du réseau : 

![Texte alternatif](/Diagramme-architecture-Docker)






Démarche :
0) Assurez vous d'avoir sur votre pc docker, si ce n'est pas le cas allez le télécharger via le lien suivant : https://docs.docker.com/desktop/setup/install/linux/ubuntu/
1) Clonez le projet sur gitlab
2) Ouvrez un terminal et exectuez sudo nano /etc/hosts
3) Tout en haut du fichier après les deux déclaration d'adresses IP qui coresspondent au adresse IP de base de votre PC, ajoutez a la ligne '127.0.0.1 tp5.local'
3) Placez vous maintenant a la racine du projet cloné avec la commande 'cd'
4) Exécute 'docker compose up -d'
5) Félicitation, Vous pouvez maintenant vous rendre sur http://tp5.local et http://localhost:8025







Le mode opératoire exact pour lier le master et le slave MySQL (cette section a pour but explicative. Elle n'est normalement pas requise pour la démarche) :

1) Entrez dans le bash du serveur mysql : 
docker exec -it mysql_master mysql -u root -p
Ils vont demander le mot de pass, il faut saisir celui présent dans le fichier
.env dans le ligne MYSQL_ROOT_PASSWORD=... (Dans mon cas c'est 'root') 

2) Exécutez cette commande pour créer l'utilisateur, le lier au SLAVE et 
lui donner des permissions:
CREATE USER 'replica'@'%' IDENTIFIED BY 'password';
GRANT REPLICATION SLAVE ON *.* TO 'replica'@'%';
FLUSH PRIVILEGES;
Exécuter ensuite cette commande et retenez les section 'File' et 'Position' 
pour plus tard:
SHOW MASTER STATUS;

Remarque : Atention cette commande risque de ne pas marcher si la version 
de mysql utilisé est > 8.0. Donc si ca ne marche pas 
(erreur de syntaxe invalide), modifier dans le ficher docker-compose.yml
la section 'image: mysql:9.2' par 'image: mysql:8.0'.

3) Exécutez :
CHANGE MASTER TO
MASTER_HOST='mysql_master',
MASTER_USER='replica',
MASTER_PASSWORD='password',
MASTER_LOG_FILE='laPartieFileApresAvoirExecuteSHOW_MASTER_STATUS',
MASTER_LOG_POS=laPartiePositionApresAvoirExecuteSHOW_MASTER_STATUS;

START SLAVE;

Ey exécutez cette commande pour voir si tout est ok :
SHOW SLAVE STATUS\G;

Remarque : Il est possible que la commande CHANGE MASTER TO... ne marche pas 
car depuis cette syntaxe est l'ancienne syntaxe valable pour une version
MySQL < 8, a partir de MySQL 8, la nouvelle syntaxe est :
CHANGE REPLICATION SOURCE TO
SOURCE_HOST='mysql_master',
SOURCE_USER='replica',
SOURCE_PASSWORD='password',
SOURCE_LOG_FILE='laPartieFileApresAvoirExecuteSHOW_MASTER_STATUS',
SOURCE_LOG_POS=laPartiePositionApresAvoirExecuteSHOW_MASTER_STATUS;

Ensuite : START REPLICA;
Enfin : SHOW REPLICA STATUS\G; pour vérifier que tous est bon.
