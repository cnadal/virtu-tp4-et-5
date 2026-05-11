const mysql = require('mysql2/promise');

module.exports = async (req, res) => {
    // Enable CORS
    res.header("Access-Control-Allow-Origin", "*");

    try {
        const connection = await mysql.createConnection({
            host: 'db_mariadb',
            user: 'root',
            password: 'rootpassword',
            database: 'garageM'
        });

        const immat = Math.floor(Math.random() * 90000000) + 10000000;
        const couleurs = ['bleue', 'jaune', 'noire', 'blanche', 'grise'];
        const couleur = couleurs[Math.floor(Math.random() * couleurs.length)];
        const km = Math.floor(Math.random() * 150000) + 10;

        await connection.execute(
            "INSERT INTO Voitures (immatriculation, couleur, km) VALUES (?, ?, ?)",
            [immat, couleur, km]
        );

        await connection.end();
        res.send(`Voiture ajoutée avec succès via Node.js ! (Immatriculation ${immat}, Couleur ${couleur}, KM ${km})`);
    } catch (error) {
        console.error(error);
        res.status(500).send("Erreur lors de l'ajout : " + error.message);
    }
};
