# TP5 - Architecture Docker Complète

## Flux Réseaux
L'architecture est découpée en 3 réseaux bridgés isolés :
- `net_public` : Expose le proxy (Nginx), Keycloak et Mailpit pour que l'utilisateur puisse y accéder (internet).
- `net_db` : Isolé. Seule l'application Web (`web_app`) peut communiquer avec `mysql_master` et `mysql_slave`.
- `net_auth` : Isolé. Utilisé uniquement par `keycloak_auth` pour sa de base de données logicielle `postgres_keycloak`.

## Déploiement
1. Ajouter le domaine  historique dans votre machine locale `127.0.0.1 tp5.local` (`/etc/hosts`).
2. Lancer la commande de déploiement (en fond) :
```sh
docker compose up -d --build
```

## Activer la réplication MySQL (Master -> Slave)

1. Récupérer l'état du master :
```sh
docker exec mysql_master mysql -u root -prootpwd -e "SHOW BINARY LOG STATUS\G"
```
*Notez le `File` (ex: mysql-bin.000001) et la `Position`.*

2. Configurer le slave :
Entrez dans le shell du slave : `docker exec -it mysql_slave mysql -u root -prootpwd`
Lancez ensuite la requête :
```sql
CHANGE REPLICATION SOURCE TO 
SOURCE_HOST='mysql_master',
SOURCE_USER='replicator',
SOURCE_PASSWORD='replpwd',
SOURCE_LOG_FILE='mysql-bin.00000X', -- Remplacer X
SOURCE_LOG_POS=XXX, -- Remplacer XXX
GET_SOURCE_PUBLIC_KEY=1;

START REPLICA;
SHOW REPLICA STATUS\G
```

Une fois validé, la page `http://tp5.local/slave.php` remontera exactement les mêmes voitures que le master !

## Authentification Keycloak

Une fois les conteneurs lancés, vous devez initialiser Keycloak (création du Realm, du Client et de l'utilisateur de test). Pour cela, lancez le script suivant depuis la racine du projet :

```sh
./setup_keycloak.sh
```

Une fois le script exécuté, vous pourrez vous connecter à l'application web (interface d'accueil) avec les identifiants suivants générés automatiquement :
- **Nom d'utilisateur :** `test`
- **Mot de passe :** `testpwd`

*(Note : L'interface d'administration de Keycloak reste accessible sur `http://localhost:8080` avec les identifiants `admin` / `admin` définis dans votre fichier d'environnement `.env`)*
