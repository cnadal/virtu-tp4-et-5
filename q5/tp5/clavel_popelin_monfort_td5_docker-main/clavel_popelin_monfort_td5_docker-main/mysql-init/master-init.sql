CREATE DATABASE IF NOT EXISTS db;
USE db;

CREATE TABLE IF NOT EXISTS voitures (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        immatriculation VARCHAR(20) NOT NULL
    );

INSERT INTO voitures (immatriculation) VALUES
                                           ('AA-123-BB'),
                                           ('BB-456-CC'),
                                           ('CC-789-DD');

-- Utilise IF NOT EXISTS pour éviter de bloquer le slave
CREATE USER IF NOT EXISTS 'replica'@'%' IDENTIFIED WITH caching_sha2_password BY 'replica_pass';
GRANT REPLICATION SLAVE ON *.* TO 'replica'@'%';
FLUSH PRIVILEGES;