# TP5 - Infrastructure Docker - R4.A.08

## Flux réseau

- On accède uniquement à `proxy_nginx` (ports 80/443) via `net_public`.
- `proxy_nginx` redirige le trafic vers `web_app`, qui est aussi sur `net_public`.
- `web_app` communique avec `mysql_master` et `mysql_slave` via `net_db`.
- `keycloak_auth` est accessible depuis `net_public` et communique avec `postgres_keycloak` via `net_auth`.
- `mail_test` est accessible sur le port 8025 via `net_public`.
- Les bases de données ne sont jamais exposées directement à l'extérieur.

---

## Déploiement

```bash
docker compose up -d --build
```

---

## Accès aux services

| Service | URL | Identifiants |
|---------|-----|--------------|
| Application web | http://localhost | |
| Keycloak Admin | http://localhost:8080 | admin / admin123 |
| Mailpit | http://localhost:8025 | |
| MySQL | mysql_master:3306 | user / mySql123 |
| PostgreSQL | postgres_keycloak:5432 | userP / userP123 |

---

## Réplication MySQL (Master → Slave)

**1. Créer l'utilisateur de réplication :**

```bash
docker exec -it mysql_master mysql -u root -prootMySql123 \
  -e "CREATE USER 'userR'@'%' IDENTIFIED BY 'userR123';
      GRANT REPLICATION SLAVE ON *.* TO 'userR'@'%';
      FLUSH PRIVILEGES;"
```

**2. Récupérer la position du binlog :**

```bash
docker exec -it mysql_master mysql -u root -prootMySql123 -e "SHOW MASTER STATUS\G"
```

**3. Configurer le slave** (remplacer `File` et `Position` par les valeurs obtenues) :

```bash
docker exec -it mysql_slave mysql -u root -prootMySql123 \
  -e "CHANGE MASTER TO
        MASTER_HOST='mysql_master',
        MASTER_USER='userR',
        MASTER_PASSWORD='userR123',
        MASTER_LOG_FILE='mysql-bin.000003',
        MASTER_LOG_POS=157;
      START SLAVE;"
```

**4. Vérifier :**

```bash
docker exec -it mysql_slave mysql -u root -prootMySql123 -e "SHOW SLAVE STATUS\G"
```

`Slave_IO_Running` et `Slave_SQL_Running` doivent être à `Yes`.

---

## Arrêt

```bash
docker compose down
```