const mysql = require('mysql2/promise');
const fs = require('fs');
const path = require('path');

module.exports = async (req, res) => {
    // Enable CORS
    res.header("Access-Control-Allow-Origin", "*");

    try {
        const connection = await mysql.createConnection({
            host: 'db_mariadb',               // Nom du conteneur BD interne
            user: 'root',
            password: 'rootpassword',
            multipleStatements: true
        });

        const sqlPath = path.join(__dirname, 'garageM.sql');
        const sql = fs.readFileSync(sqlPath, 'utf8');

        await connection.query(sql);
        await connection.end();

        res.send("Base de données 'garageM' et table 'Voitures' créées/initialisées avec succès depuis le fichier SQL en Node.JS !");
    } catch (error) {
        console.error(error);
        res.status(500).send("Erreur de création : " + error.message);
    }
};
