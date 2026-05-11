# TP5 — Infrastructure Docker Multi-Services

## 1. Flux Réseau

```
                        ┌─────────────────────────────────────┐
                        │           MACHINE HÔTE               │
                        │  /etc/hosts : 127.0.0.1  tp5.local  │
                        │               127.0.0.1  mail.local  │
                        └──────────────┬──────────────────────┘
                                       │
                             Port 80 (HTTP)
                                       │
                         ┌─────────────▼─────────────┐
                         │        proxy_nginx          │
                         │   (Reverse Proxy Nginx)     │
                         │  Réseaux : net_public        │
                         └──────┬──────────────┬───────┘
                                │              │
              server_name: tp5.local   server_name: mail.local
                                │              │
               ┌────────────────▼─┐     ┌──────▼──────────────┐
               │    web_app        │     │    mail_test         │
               │  (PHP 8.4/Apache) │     │   (Mailpit)          │
               │  Réseaux :        │     │  Réseaux : net_public│
               │  net_public,      │     │  SMTP :  1025 (int)  │
               │  net_db           │     │  UI   :  8025 (int)  │
               └──┬────────────┬──┘     └──────────────────────┘
                  │            │
          net_db  │         /auth/
                  │            │
     ┌────────────▼──┐  ┌──────▼───────────────┐
     │  mysql_master  │  │    keycloak_auth       │
     │  (MySQL 9.2)   │  │  (Quay.io Keycloak)   │
     │  server-id=1   │  │  Réseaux :            │
     │  log_bin ON    │  │  net_public, net_auth  │
     └────────┬───────┘  └──────────┬────────────┘
              │ réplication               │
              │ binaire               net_auth
              │                           │
     ┌────────▼───────┐   ┌───────────────▼──────────┐
     │  mysql_slave   │   │    postgres_keycloak       │
     │  (MySQL 9.2)   │   │   (PostgreSQL 15)          │
     │  server-id=2   │   │   Réseaux : net_auth       │
     │  replicate ON  │   └──────────────────────────┘
     └────────────────┘

Réseaux Docker (bridge isolés) :
  net_public  → proxy_nginx, web_app, keycloak_auth, mail_test
  net_db      → web_app, mysql_master, mysql_slave
  net_auth    → keycloak_auth, postgres_keycloak
```

**Flux entrant** : L'utilisateur contacte `tp5.local:80` → Nginx route vers `web_app:80`.  
**Authentification** : `web_app` redirige vers `/auth/` → Nginx proxy vers `keycloak_auth:8080/auth/`.  
**Base de données** : `web_app` écrit sur `mysql_master` ; lit depuis `mysql_slave` (réplication binaire).  
**Mail** : PHP envoie via SMTP vers `mail_test:1025` ; l'interface web est accessible sur `mail.local`.

---

## 2. Commande de Déploiement

```bash
docker compose up -d --build
```

> Prérequis : créer un fichier `.env` à la racine avec les variables suivantes :
>
> ```env
> MYSQL_ROOT_PASSWORD=rootpassword
> MYSQL_DATABASE=app_db
> MYSQL_USER=app_user
> MYSQL_PASSWORD=app_password
> KC_DB_NAME=keycloak
> KC_DB_USER=keycloak
> KC_DB_PASSWORD=keycloak_password
> KC_ADMIN_USER=admin
> KC_ADMIN_PASSWORD=admin
> KEYCLOAK_CLIENT_SECRET=<secret_copié_depuis_keycloak>
> ```

---

## 3. Mode Opératoire — Liaison MySQL Master / Slave

Après le démarrage des conteneurs, exécuter les commandes suivantes dans l'ordre.

### Étape 1 — Créer l'utilisateur de réplication sur le Master et relever sa position

```bash
docker exec -it mysql_master mysql -uroot -prootpassword -e "
  CREATE USER 'repl_user'@'%' IDENTIFIED WITH mysql_native_password BY 'repl_password';
  GRANT REPLICATION SLAVE ON *.* TO 'repl_user'@'%';
  FLUSH PRIVILEGES;
  SHOW MASTER STATUS\G"
```

La commande retourne une sortie de ce type :

```
*************************** 1. row ***************************
             File: mysql-bin.000003
         Position: 157
     Binlog_Do_DB: app_db
```

**Noter les valeurs `File` et `Position`** (elles varient à chaque démarrage).

### Étape 2 — Pointer le Slave vers le Master

Remplacer `<FILE>` et `<POSITION>` par les valeurs relevées ci-dessus :

```bash
docker exec -it mysql_slave mysql -uroot -prootpassword -e "
  CHANGE REPLICATION SOURCE TO
    SOURCE_HOST='mysql_master',
    SOURCE_USER='repl_user',
    SOURCE_PASSWORD='repl_password',
    SOURCE_LOG_FILE='<FILE>',
    SOURCE_LOG_POS=<POSITION>;
  START REPLICA;"
```

### Étape 3 — Vérifier l'état de la réplication

```bash
docker exec -it mysql_slave mysql -uroot -prootpassword -e "SHOW REPLICA STATUS\G"
```

Les deux champs suivants doivent afficher `Yes` pour confirmer que la réplication est active :

```
Replica_IO_Running: Yes
Replica_SQL_Running: Yes
```
