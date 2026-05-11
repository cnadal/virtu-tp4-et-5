const mysql = require('mysql2/promise');

module.exports = async (req, res) => {
    // Enable CORS
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Content-Type", "application/json; charset=UTF-8");

    try {
        const connection = await mysql.createConnection({
            host: 'db_mariadb',
            user: 'root',
            password: 'rootpassword',
            database: 'garageM'
        });

        const [rows] = await connection.query("SELECT * FROM Voitures");
        await connection.end();

        res.json(rows);
    } catch (error) {
        console.error(error);
        res.status(500).json({ error: "Erreur de lecture : " + error.message });
    }
};
