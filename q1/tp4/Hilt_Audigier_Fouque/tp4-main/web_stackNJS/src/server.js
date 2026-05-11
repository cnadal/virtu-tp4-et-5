const express = require('express');
const mysql = require('mysql2/promise');
const path = require('path');

const app = express();
const port = process.env.PORT || 3000;

const dbConfig = {
    host: process.env.DB_HOST || 'mysql',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || 'password',
    database: process.env.DB_NAME || 'garage_db'
};

app.use(express.static('public'));
app.use(express.json());

app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

app.get('/creationBDD.php', async (req, res) => {
    try {
        const connection = await mysql.createConnection({
            host: dbConfig.host,
            user: dbConfig.user,
            password: dbConfig.password
        });
        await connection.query(`CREATE DATABASE IF NOT EXISTS \`${dbConfig.database}\``);
        await connection.query(`USE \`${dbConfig.database}\``);
        await connection.query(`CREATE TABLE IF NOT EXISTS voitures (
            id INT AUTO_INCREMENT PRIMARY KEY,
            marque VARCHAR(255) NOT NULL,
            modele VARCHAR(255) NOT NULL,
            annee INT
        )`);
        await connection.end();
        res.send("Base de données créée avec succès.");
    } catch (err) {
        res.status(500).send("Erreur lors de la création de la BDD : " + err.message);
    }
});

app.get('/ajoutVoituresGarage.php', async (req, res) => {
    try {
        const connection = await mysql.createConnection(dbConfig);
        await connection.query("INSERT INTO voitures (marque, modele, annee) VALUES ('Tesla', 'Model 3', 2023), ('Renault', 'Zoe', 2022)");
        await connection.end();
        res.send("Voitures ajoutées au garage.");
    } catch (err) {
        res.status(500).send("Erreur lors de l'ajout : " + err.message);
    }
});

app.get('/listeVoitures.php', async (req, res) => {
    try {
        const connection = await mysql.createConnection(dbConfig);
        const [rows] = await connection.query("SELECT * FROM voitures");
        await connection.end();
        res.json(rows);
    } catch (err) {
        res.status(500).send("Erreur lors de la récupération : " + err.message);
    }
});

app.listen(port, () => {
    console.log(`Serveur Node.js démarré sur http://localhost:${port}`);
});
