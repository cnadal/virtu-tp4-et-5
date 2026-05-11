# TP5 — Projet final d'architecture Docker

Module R4.A.08 — Outils de la virtualisation

---

## Architecture et flux réseau

```
Internet / Navigateur
        │
        ▼
  ┌─────────────┐
  │ proxy_nginx │  ports 80/443  ── net_public
  └──────┬──────┘
         │
    ┌────┴──────────────┐
    │                   │
    ▼                   ▼
┌─────────┐      ┌───────────────┐
│ web_app │      │ keycloak_auth │  ── net_public + net_auth
└────┬────┘      └───────┬───────┘
     │  net_db           │  net_auth
     │                   ▼
 ┌───┴──────┐    ┌──────────────────┐
 │ mysql_   │    │ postgres_keycloak│  ── net_auth (isolé)
 │ master   │    └──────────────────┘
 └──────────┘
 ┌──────────┐
 │ mysql_   │  ── net_db (réplication depuis master)
 │ slave    │
 └──────────┘
 ┌──────────┐
 │ mail_test│  ── net_public (API interne utilisée par web_app)
 └──────────┘
```

| Réseau     | Services connectés                              | Rôle                          |
|------------|--------------------------------------------------|-------------------------------|
| net_public | proxy_nginx, web_app, keycloak_auth, mail_test  | Zone exposée au trafic web    |
| net_db     | web_app, mysql_master, mysql_slave               | Zone base de données métier   |
| net_auth   | keycloak_auth, postgres_keycloak                 | Zone authentification isolée  |

---

## Pré-requis
Ajouter le domaine local dans `/etc/hosts` (Linux/Mac) ou `C:\Windows\System32\Drivers\etc\hosts` (Windows).
```bash
sudo nano /etc/hosts
```
Ajouter la ligne suivante dans le fichier :
```
127.0.0.1  tp5.local
```

Vérification :

```bash
ping -c 1 tp5.local
```

---

## Déploiement

```bash
docker compose up -d --build
```

Attendre ~60s que Keycloak démarre (il initialise sa base PostgreSQL).
Pour suivre les logs : `docker compose logs -f keycloak_auth`

### URLs disponibles

| URL                          | Description                          |
|------------------------------|--------------------------------------|
| http://tp5.local/            | Application (authentification SSO)   |
| http://tp5.local/master.php  | Voitures depuis MySQL Master         |
| http://tp5.local/slave.php   | Voitures depuis MySQL Slave          |
| http://tp5.local/mailpit.php | Interface Mailpit (mails interceptés)|

### Compte de test Keycloak

- **Utilisateur :** `testuser`
- **Mot de passe :** `Test1234!`
- **Realm :** `tp5` (importé automatiquement au démarrage)

### Console d'administration Keycloak

Accessible via nginx : http://tp5.local/admin/
Ou directement sur le conteneur si port exposé.

- **Admin :** `admin` / `Admin2024!`

---

## Configuration de la réplication MySQL (Master → Slave)

À exécuter **une seule fois** après le premier `docker compose up`.

### Étape 1 — Récupérer la position du binlog sur le Master

```bash
docker exec -it mysql_master mysql -u root -pSuperRoot2024! -e "SHOW BINARY LOG STATUS\G"
```

Notez les valeurs `File` et `Position` (exemple : `mysql-bin.000003`, `157`).

### Étape 2 — Configurer le Slave

```bash
docker exec -it mysql_slave mysql -u root -pSuperRoot2024! -e "
STOP REPLICA;
CHANGE REPLICATION SOURCE TO
  SOURCE_HOST='mysql_master',
  SOURCE_USER='replicator',
  SOURCE_PASSWORD='ReplicaPass2024!',
  SOURCE_LOG_FILE='mysql-bin.000003',
  SOURCE_LOG_POS=157;
START REPLICA;
"
```

> Remplacez `mysql-bin.000003` et `157` par les valeurs de l'étape 1.

### Étape 3 — Vérifier la réplication

```bash
docker exec -it mysql_slave mysql -u root -pSuperRoot2024! -e "SHOW REPLICA STATUS\G"
```

Vérifier :
- `Replica_IO_Running: Yes`
- `Replica_SQL_Running: Yes`

---

## Test de la synchronisation

1. Ouvrez http://tp5.local/master.php et http://tp5.local/slave.php côte à côte.
2. Supprimez une voiture sur le master :

```bash
docker exec -it mysql_master mysql -u root -pSuperRoot2024! garage \
  -e "DELETE FROM voitures WHERE id = 1;"
```

3. Rafraîchissez slave.php : la voiture doit avoir disparu.

---

## Test Mailpit

Depuis le conteneur `web_app` (ou tout autre conteneur sur `net_public`) :

```bash
docker exec -it web_app php -r "
mail('test@example.com', 'Sujet test', 'Corps du message', [], '-f sender@tp5.local');
"
```

Puis rendez-vous sur http://tp5.local/mailpit.php pour voir le message intercepté.

---

## Arrêt et nettoyage

```bash
# Arrêt des conteneurs
docker compose down

# Suppression des volumes (données perdues)
docker compose down -v

# Suppréssion du domaine local dans `/etc/hosts` (Linux/Mac) ou `C:\Windows\System32\Drivers\etc\hosts` (Windows).
sudo nano /etc/hosts
```
retirez simplement la ligne ajoutée au début du TP :
```
127.0.0.1  tp5.local
```