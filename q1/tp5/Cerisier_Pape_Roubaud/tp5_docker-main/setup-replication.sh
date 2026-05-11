#!/bin/bash
# Script de configuration automatique de la réplication MySQL Master/Slave
# Ce script tourne dans un conteneur one-shot après le démarrage de MySQL

set -e

echo "==> Attente que le Master soit prêt..."
until mysql -h mysql_master -u root -p"${MYSQL_ROOT_PASSWORD}" -e "SELECT 1" &>/dev/null; do
  sleep 2
done
echo "==> Master OK"

echo "==> Attente que le Slave soit prêt..."
until mysql -h mysql_slave -u root -p"${MYSQL_ROOT_PASSWORD}" -e "SELECT 1" &>/dev/null; do
  sleep 2
done
echo "==> Slave OK"

echo "==> Création de l'utilisateur de réplication sur le Master..."
mysql -h mysql_master -u root -p"${MYSQL_ROOT_PASSWORD}" <<EOF
CREATE USER IF NOT EXISTS 'replicator'@'%' IDENTIFIED BY 'repl_password';
GRANT REPLICATION SLAVE ON *.* TO 'replicator'@'%';
FLUSH PRIVILEGES;
EOF

echo "==> Récupération de la position du binlog Master..."
BINLOG_INFO=$(mysql -h mysql_master -u root -p"${MYSQL_ROOT_PASSWORD}" -e "SHOW BINARY LOG STATUS\G" 2>/dev/null)
BINLOG_FILE=$(echo "$BINLOG_INFO" | grep "File:" | awk '{print $2}')
BINLOG_POS=$(echo "$BINLOG_INFO" | grep "Position:" | awk '{print $2}')

echo "==> Binlog: $BINLOG_FILE @ $BINLOG_POS"

echo "==> Configuration de la réplication sur le Slave..."
mysql -h mysql_slave -u root -p"${MYSQL_ROOT_PASSWORD}" <<EOF
STOP REPLICA;
CHANGE REPLICATION SOURCE TO
  SOURCE_HOST='mysql_master',
  SOURCE_USER='replicator',
  SOURCE_PASSWORD='repl_password',
  SOURCE_LOG_FILE='${BINLOG_FILE}',
  SOURCE_LOG_POS=${BINLOG_POS},
  GET_SOURCE_PUBLIC_KEY=1;
START REPLICA;
EOF

echo "==> Vérification du statut..."
sleep 3
mysql -h mysql_slave -u root -p"${MYSQL_ROOT_PASSWORD}" -e "SHOW REPLICA STATUS\G" 2>/dev/null | grep -E "Replica_IO_Running|Replica_SQL_Running"

echo "==> Réplication configurée avec succès !"
