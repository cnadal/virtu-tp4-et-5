#!/bin/bash
# Script d'initialisation du MySQL Master
# Exécuté automatiquement par docker-entrypoint au premier démarrage

set -e

echo "[init-master] Création de la table voitures..."
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" "${MYSQL_DATABASE}" <<-EOSQL
    CREATE TABLE IF NOT EXISTS voitures (
        id      INT AUTO_INCREMENT PRIMARY KEY,
        marque  VARCHAR(100) NOT NULL,
        modele  VARCHAR(100) NOT NULL,
        annee   INT,
        couleur VARCHAR(50)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    INSERT INTO voitures (marque, modele, annee, couleur) VALUES
        ('Peugeot',  '308',    2022, 'Bleu'),
        ('Renault',  'Clio',   2021, 'Rouge'),
        ('Citroën',  'C3',     2023, 'Blanc'),
        ('Toyota',   'Yaris',  2022, 'Noir'),
        ('Volkswagen','Golf',  2020, 'Gris');
EOSQL

echo "[init-master] Création de l'utilisateur de réplication..."
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" <<-EOSQL
    CREATE USER IF NOT EXISTS '${MYSQL_REPLICATION_USER}'@'%'
        IDENTIFIED WITH mysql_native_password BY '${MYSQL_REPLICATION_PASSWORD}';
    GRANT REPLICATION SLAVE ON *.* TO '${MYSQL_REPLICATION_USER}'@'%';
    FLUSH PRIVILEGES;
EOSQL

echo "[init-master] Initialisation terminée."
