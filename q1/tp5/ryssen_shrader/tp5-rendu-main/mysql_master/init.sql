CREATE DATABASE IF NOT EXISTS app_db;
USE app_db;

CREATE TABLE IF NOT EXISTS voitures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marque VARCHAR(50) NOT NULL,
    modele VARCHAR(50) NOT NULL,
    annee INT NOT NULL,
    couleur VARCHAR(30) NOT NULL
);

INSERT INTO voitures (marque, modele, annee, couleur) VALUES
('Peugeot', '208', 2023, 'Bleu'),
('Renault', 'Clio', 2022, 'Rouge'),
('Citroen', 'C3', 2021, 'Blanc'),
('Volkswagen', 'Golf', 2023, 'Noir'),
('Toyota', 'Yaris', 2022, 'Gris');
