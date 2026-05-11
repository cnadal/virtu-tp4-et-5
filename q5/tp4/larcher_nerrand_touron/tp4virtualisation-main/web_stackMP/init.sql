USE mysql;
CREATE USER IF NOT EXISTS 'prod_user'@'%' IDENTIFIED BY 'MotDePasse321';
GRANT ALL PRIVILEGES ON *.* TO 'prod_user'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;

CREATE DATABASE IF NOT EXISTS voitures_db;
USE voitures_db;

CREATE TABLE IF NOT EXISTS voitures (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        marque VARCHAR(50) NOT NULL,
    modele VARCHAR(50) NOT NULL,
    annee INT,
    immatriculation VARCHAR(20) UNIQUE,
    couleur VARCHAR(30)
    );

INSERT INTO voitures (marque, modele, annee, immatriculation, couleur) VALUES
                                                                           ('Renault', 'Clio', 2020, 'AB-123-CD', 'Bleu'),
                                                                           ('Peugeot', '308', 2021, 'CD-456-EF', 'Noir'),
                                                                           ('Citroën', 'C3', 2019, 'EF-789-GH', 'Gris'),
                                                                           ('Toyota', 'Corolla', 2022, 'GH-123-IJ', 'Blanc'),
                                                                           ('Volkswagen', 'Golf', 2020, 'IJ-456-KL', 'Rouge');
