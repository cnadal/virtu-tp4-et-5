CREATE DATABASE IF NOT EXISTS tp5_db;
USE tp5_db;

CREATE TABLE IF NOT EXISTS voitures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marque VARCHAR(50) NOT NULL,
    modele VARCHAR(50) NOT NULL,
    annee INT NOT NULL
);

INSERT INTO voitures (marque, modele, annee) VALUES ('Renault', 'Clio', 2020), ('Peugeot', '208', 2021);

CREATE USER IF NOT EXISTS 'replicator'@'%' IDENTIFIED BY 'replpwd';
GRANT REPLICATION SLAVE ON *.* TO 'replicator'@'%';
FLUSH PRIVILEGES;
