const express = require("express");
const mysql = require("mysql2/promise");

const app = express();

const PORT = Number(process.env.PORT || 3000);
const GARAGE_DATABASES = ["garageM", "garageP"];
const MYSQL_CONNECT_RETRIES = Number(process.env.MYSQL_CONNECT_RETRIES || 20);
const MYSQL_CONNECT_RETRY_DELAY_MS = Number(process.env.MYSQL_CONNECT_RETRY_DELAY_MS || 1500);

const MYSQL_CONFIG = {
    host: process.env.MYSQL_HOST || "mysql",
    port: Number(process.env.MYSQL_PORT || 3306),
    user: process.env.MYSQL_USER || "root",
    password: process.env.MYSQL_ROOT_PASSWORD || process.env.MYSQL_PASSWORD || "root",
};

function htmlEscape(value) {
    return String(value)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}

function randomCar() {
    const marques = ["Renault", "Peugeot", "Citroen", "Toyota", "Ford", "BMW"];
    const modeles = ["Clio", "208", "C3", "Yaris", "Focus", "Serie 1"];
    const couleurs = ["rouge", "bleu", "noir", "blanc", "gris", "vert"];

    return {
        marque: marques[Math.floor(Math.random() * marques.length)],
        modele: modeles[Math.floor(Math.random() * modeles.length)],
        couleur: couleurs[Math.floor(Math.random() * couleurs.length)],
        annee: 2008 + Math.floor(Math.random() * 19),
        prix: 7000 + Math.floor(Math.random() * 38001),
    };
}

function sleep(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}

function isRetryableMySqlError(error) {
    const code = error && error.code ? String(error.code) : "";
    return code === "ECONNREFUSED" || code === "PROTOCOL_CONNECTION_LOST" || code === "ETIMEDOUT";
}

async function createMySqlConnectionWithRetry(config) {
    let lastError = null;

    for (let attempt = 1; attempt <= MYSQL_CONNECT_RETRIES; attempt += 1) {
        try {
            return await mysql.createConnection(config);
        } catch (error) {
            lastError = error;
            if (!isRetryableMySqlError(error) || attempt === MYSQL_CONNECT_RETRIES) {
                throw error;
            }
            await sleep(MYSQL_CONNECT_RETRY_DELAY_MS);
        }
    }

    throw lastError;
}

async function withMySqlAdmin(action) {
    const connection = await createMySqlConnectionWithRetry(MYSQL_CONFIG);
    try {
        return await action(connection);
    } finally {
        await connection.end();
    }
}

async function withMySqlDb(database, action) {
    const connection = await createMySqlConnectionWithRetry({ ...MYSQL_CONFIG, database });
    try {
        return await action(connection);
    } finally {
        await connection.end();
    }
}

async function mysqlEnsureDatabasesAndTables() {
    await withMySqlAdmin(async (admin) => {
        for (const db of GARAGE_DATABASES) {
            const safeDb = `\`${db.replaceAll("`", "``")}\``;
            await admin.query(`CREATE DATABASE IF NOT EXISTS ${safeDb} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci`);
        }
    });

    for (const db of GARAGE_DATABASES) {
        await withMySqlDb(db, async (conn) => {
            await conn.query(`
                CREATE TABLE IF NOT EXISTS voitures (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    marque VARCHAR(80) NOT NULL,
                    modele VARCHAR(80) NOT NULL,
                    couleur VARCHAR(40) NOT NULL,
                    annee INT NOT NULL,
                    prix DECIMAL(10,2) NOT NULL,
                    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            `);
        });
    }
}

async function mysqlInsertRandomCars() {
    const inserted = {};
    for (const db of GARAGE_DATABASES) {
        await withMySqlDb(db, async (conn) => {
            const count = 2 + Math.floor(Math.random() * 5);
            for (let i = 0; i < count; i += 1) {
                const car = randomCar();
                await conn.execute(
                    "INSERT INTO voitures (marque, modele, couleur, annee, prix) VALUES (?, ?, ?, ?, ?)",
                    [car.marque, car.modele, car.couleur, car.annee, car.prix]
                );
            }
            inserted[db] = count;
        });
    }
    return inserted;
}

async function mysqlListCars() {
    const results = {};
    for (const db of GARAGE_DATABASES) {
        await withMySqlDb(db, async (conn) => {
            const [rows] = await conn.query("SELECT id, marque, modele, couleur, annee, prix, cree_le FROM voitures ORDER BY id ASC");
            results[db] = rows;
        });
    }
    return results;
}

function renderHome() {
    return `<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Garage Node</title>
  <style>
    body { font-family: sans-serif; margin: 2rem; }
    ul { line-height: 1.8; }
  </style>
</head>
<body>
  <h1>Garage Express</h1>
  <ul>
        <li><a href="/creationBDD">/creationBDD</a></li>
    <li><a href="/ajoutVoituresGarage">/ajoutVoituresGarage</a></li>
    <li><a href="/listeVoitures">/listeVoitures</a></li>
  </ul>
    <p>SGBD utilise: MySQL</p>
</body>
</html>`;
}

function renderCreatePage(reports) {
    const items = reports
        .map((r) => {
            if (r.error) {
                return `<li><strong>${htmlEscape(r.driver)}</strong>: erreur - ${htmlEscape(r.error)}</li>`;
            }
            return `<li><strong>${htmlEscape(r.driver)}</strong>: bases et tables pretes</li>`;
        })
        .join("\n");

    return `<!doctype html>
<html lang="fr"><head><meta charset="utf-8" /><title>creationBDD</title></head>
<body>
  <h1>Creation des BDD</h1>
  <ul>${items}</ul>
  <p><a href="/">Retour accueil</a></p>
</body></html>`;
}

function renderInsertPage(reports) {
    const blocks = reports
        .map((r) => {
            if (r.error) {
                return `<h2>${htmlEscape(r.driver)}</h2><p style="color:#b00">Erreur: ${htmlEscape(r.error)}</p>`;
            }
            const rows = Object.entries(r.inserted)
                .map(([db, count]) => `<li>${htmlEscape(db)}: ${Number(count)} tuple(s) ajoute(s)</li>`)
                .join("");
            return `<h2>${htmlEscape(r.driver)}</h2><ul>${rows}</ul>`;
        })
        .join("\n");

    return `<!doctype html>
<html lang="fr"><head><meta charset="utf-8" /><title>ajoutVoituresGarage</title></head>
<body>
  <h1>Ajout aleatoire de voitures</h1>
  ${blocks}
  <p><a href="/listeVoitures">Voir listeVoitures</a></p>
</body></html>`;
}

function renderListPage(reports) {
    const blocks = reports
        .map((r) => {
            if (r.error) {
                return `<h2>${htmlEscape(r.driver)}</h2><p style="color:#b00">Erreur: ${htmlEscape(r.error)}</p>`;
            }

            const byDb = Object.entries(r.rows)
                .map(([db, rows]) => {
                    if (!rows.length) {
                        return `<h3>${htmlEscape(db)}</h3><p>Aucun tuple dans voitures.</p>`;
                    }

                    const bodyRows = rows
                        .map(
                            (row) => `<tr>
<td>${Number(row.id)}</td>
<td>${htmlEscape(row.marque)}</td>
<td>${htmlEscape(row.modele)}</td>
<td>${htmlEscape(row.couleur)}</td>
<td>${Number(row.annee)}</td>
<td>${Number(row.prix).toFixed(2)}</td>
<td>${htmlEscape(row.cree_le)}</td>
</tr>`
                        )
                        .join("\n");

                    return `<h3>${htmlEscape(db)}</h3>
<table border="1" cellpadding="6" cellspacing="0">
  <thead><tr><th>ID</th><th>Marque</th><th>Modele</th><th>Couleur</th><th>Annee</th><th>Prix</th><th>Cree le</th></tr></thead>
  <tbody>${bodyRows}</tbody>
</table>`;
                })
                .join("\n");

            return `<h2>${htmlEscape(r.driver)}</h2>${byDb}`;
        })
        .join("\n");

    return `<!doctype html>
<html lang="fr"><head><meta charset="utf-8" /><title>listeVoitures</title></head>
<body>
  <h1>Liste des voitures</h1>
  ${blocks}
  <p><a href="/ajoutVoituresGarage">Ajouter des tuples</a></p>
</body></html>`;
}

app.get("/", (req, res) => {
    res.type("html").send(renderHome());
});

app.get("/creationBDD", async (req, res) => {
    const reports = [{ driver: "mysql", error: null }];

    try {
        await mysqlEnsureDatabasesAndTables();
    } catch (error) {
        reports[0].error = error.message;
    }

    res.type("html").send(renderCreatePage(reports));
});

app.get("/ajoutVoituresGarage", async (req, res) => {
    const reports = [{ driver: "mysql", inserted: {}, error: null }];

    try {
        await mysqlEnsureDatabasesAndTables();
        reports[0].inserted = await mysqlInsertRandomCars();
    } catch (error) {
        reports[0].error = error.message;
    }

    res.type("html").send(renderInsertPage(reports));
});

app.get("/listeVoitures", async (req, res) => {
    const reports = [{ driver: "mysql", rows: {}, error: null }];

    try {
        await mysqlEnsureDatabasesAndTables();
        reports[0].rows = await mysqlListCars();
    } catch (error) {
        reports[0].error = error.message;
    }

    res.type("html").send(renderListPage(reports));
});

app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
});