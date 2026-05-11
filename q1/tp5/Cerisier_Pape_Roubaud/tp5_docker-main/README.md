# TP5 — Architecture Docker Sécurisée

**Module** : R4.A.08 - Outils de la virtualisation  
**Binôme** : Cerisier — Pape-Roubaud

---

## 1. Schéma des flux réseaux

```
  [Navigateur client]
        │ HTTP :80 / HTTPS :443
        ▼
┌───────────────────┐     net_public
│   proxy_nginx     │ ──────────────────────────────────────►
│  (reverse proxy)  │                                        │
└────────┬──────────┘                                        │
         │ HTTP interne                                      │
         ▼                                                   │
┌───────────────────┐  net_public + net_db                   │
│     web_app       │ ── SQL ──────► mysql_master (net_db)   │
│  (PHP / Apache)   │ ── SQL ──────► mysql_slave  (net_db)   │
│                   │ ── SMTP 1025► mail_test     (net_pub)  │
└───────────────────┘                                        │
                                                             ▼
                                              ┌─────────────────────────┐
                                              │     keycloak_auth       │
                                              │  (SSO / OIDC)  net_pub  │
                                              │         + net_auth      │
                                              └──────────┬──────────────┘
                                                         │ JDBC
                                                         ▼
                                              ┌─────────────────────────┐
                                              │   postgres_keycloak     │
                                              │   (net_auth seulement)  │
                                              └─────────────────────────┘
```

| Réseau     | Rôle                               | Conteneurs                                      |
|------------|------------------------------------|-------------------------------------------------|
| net_public | Zone exposée (trafic HTTP entrant)  | proxy_nginx, web_app, keycloak_auth, mail_test  |
| net_db     | Zone BDD métier (isolée)           | web_app, mysql_master, mysql_slave, mysql_setup |
| net_auth   | Zone identité (isolée)             | keycloak_auth, postgres_keycloak                |

---

## 2. Prérequis

Éditer le fichier `C:\Windows\System32\drivers\etc\hosts` (Windows) ou `/etc/hosts` (Linux/Mac) et y ajouter :

```
127.0.0.1 tp5.local
```

Puis vérifier avec : `ping tp5.local`

### Fichier `.env`

Copier le fichier d'exemple et l'adapter si nécessaire (les valeurs par défaut fonctionnent) :

```bash
cp .env.exemple .env
```

---

## 3. Commande de déploiement

```bash
docker compose up -d --build
```

Cette **unique commande** suffit à déployer l'intégralité de l'infrastructure :

| Ce qui est automatisé                          | Par quel mécanisme                                  |
|------------------------------------------------|-----------------------------------------------------|
| Construction de l'image PHP/Apache + msmtp     | `Dockerfile` + `docker compose build`               |
| Ordonnancement des services (Postgres → KC...) | `depends_on` avec `healthcheck`                     |
| Réplication MySQL Master → Slave               | Service `mysql_setup` (conteneur one-shot)          |
| Configuration Keycloak (Realm, Client, User)   | `--import-realm` + fichier `keycloak/realm-tp5.json`|

---

## 4. Mode opératoire pour la réplication MySQL

La réplication est configurée **automatiquement** par le conteneur `mysql_setup` au premier démarrage.

Si vous devez la reconfigurer manuellement (ex : après `docker compose down -v`) :

**Sur le Master :**
```bash
docker exec -it mysql_master mysql -u root -prootpassword
```
```sql
CREATE USER IF NOT EXISTS 'replicator'@'%' IDENTIFIED BY 'repl_password';
GRANT REPLICATION SLAVE ON *.* TO 'replicator'@'%';
FLUSH PRIVILEGES;
SHOW BINARY LOG STATUS\G
```

**Sur le Slave** (remplacer `<File>` et `<Position>` par les valeurs ci-dessus) :
```bash
docker exec -it mysql_slave mysql -u root -prootpassword
```
```sql
STOP REPLICA;
CHANGE REPLICATION SOURCE TO
  SOURCE_HOST='mysql_master',
  SOURCE_USER='replicator',
  SOURCE_PASSWORD='repl_password',
  SOURCE_LOG_FILE='<File>',
  SOURCE_LOG_POS=<Position>,
  GET_SOURCE_PUBLIC_KEY=1;
START REPLICA;
SHOW REPLICA STATUS\G
```

Les lignes `Replica_IO_Running: Yes` et `Replica_SQL_Running: Yes` confirment le succès.

---

## 5. Accès aux services

| Service           | URL                          | Identifiants                  |
|-------------------|------------------------------|-------------------------------|
| Application web   | http://tp5.local             | testuser / testpassword (SSO) |
| Base Master       | http://tp5.local/master.php  | —                             |
| Base Slave        | http://tp5.local/slave.php   | —                             |
| Boîte mail        | http://tp5.local/mailpit.php | —                             |
| Mailpit (UI)      | http://tp5.local:8025        | —                             |
| Keycloak Admin    | http://tp5.local:8080        | admin / admin                 |
