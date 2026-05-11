const express = require('express');
const mysql = require('mysql2/promise');
const path = require('path');

const app = express();
const PORT = 80;

// Servir les fichiers statiques HTML/CSS/JS depuis le dossier 'public'
app.use(express.static('public'));
app.use(express.json());

// Configuration de la connexion à la base de données (via variables d'environnement)
const dbConfig = {
    host: 'db', // Correspond au nom du service db dans docker-compose
    user: process.env.MYSQL_USER,
    password: process.env.MYSQL_PASSWORD,
    database: process.env.MYSQL_DATABASE,
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
};

let pool;

async function getDB() {
    if (!pool) {
        pool = mysql.createPool(dbConfig);
    }
    return pool;
}

// ======================================
// ROUTE 1 : Création de la table Voitures
// ======================================
app.get('/web_stackNJS/creationBDD.php', async (req, res) => {
    try {
        const db = await getDB();
        await db.query(`
            CREATE TABLE IF NOT EXISTS Voitures (
                id INT AUTO_INCREMENT PRIMARY KEY,
                marque VARCHAR(255) NOT NULL,
                modele VARCHAR(255) NOT NULL,
                annee INT
            )
        `);
        res.json({ success: true, message: "Table 'Voitures' créée ou déjà existante avec succès dans la base 'garage_db'." });
    } catch (err) {
        console.error("Erreur création table:", err);
        res.status(500).json({ success: false, error: "Erreur serveur : " + err.message });
    }
});

// ======================================
// ROUTE 2 : Ajout de voitures
// ======================================
app.post('/web_stackNJS/ajoutVoituresGarage.php', async (req, res) => {
    try {
        const db = await getDB();
        const voitures = [
            ['Peugeot', '208', 2020],
            ['Renault', 'Clio', 2021],
            ['Citroen', 'C3', 2019],
            ['Audi', 'A3', 2022],
            ['BMW', 'Série 1', 2023]
        ];
        
        await db.query(
            "INSERT INTO Voitures (marque, modele, annee) VALUES ?", 
            [voitures]
        );
        res.json({ success: true, message: "5 voitures factices ajoutées au garage avec succès !" });
    } catch (err) {
        console.error("Erreur ajout voitures:", err);
        res.status(500).json({ success: false, error: "Erreur serveur : " + err.message });
    }
});

// ======================================
// ROUTE 3 : Liste des voitures
// ======================================
app.get('/web_stackNJS/listeVoitures.php', async (req, res) => {
    try {
        const db = await getDB();
        const [rows] = await db.query("SELECT * FROM Voitures");
        res.json({ success: true, voitures: rows });
    } catch (err) {
        console.error("Erreur liste voitures:", err);
        res.status(500).json({ success: false, error: "Erreur serveur : " + err.message });
    }
});

app.listen(PORT, () => {
    console.log(`Serveur Node démarré et en écoute sur le port ${PORT}`);
});
