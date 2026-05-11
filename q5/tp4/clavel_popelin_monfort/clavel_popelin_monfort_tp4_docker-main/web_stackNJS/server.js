const express = require('express');
const { exec } = require('child_process');
const path = require('path');
const app = express();

app.get('/web_stackNJS/:file.php', (req, res) => {
    const filePath = path.join(__dirname, req.params.file + '.php');
    exec(`php ${filePath}`, (error, stdout, stderr) => {
        if (error) return res.status(500).send("Erreur PHP : " + stderr);
        res.send(stdout);
    });
});

app.get('/', (req, res) => {
    res.send(`
        <h1>Boutique Node.js</h1>
        <p>Produits : 🚗 Tesla, 🚙 Jeep</p>
        <hr>
        <nav>
            <a href="/test-init">1. Initialiser</a> | 
            <a href="/test-ajout">2. Ajouter</a> | 
            <a href="/test-liste">3. Liste</a>
        </nav>
    `);
});

function generatePage(titre, url) {
    return `<h1>${titre}</h1><button onclick="go()">Lancer</button><div id="r"></div>
            <script>async function go(){ 
                const res = await fetch('${url}'); 
                document.getElementById('r').innerHTML = await res.text(); 
            }</script>
            <br><a href="/">Retour</a>`;
}

app.get('/test-init', (req, res) => res.send(generatePage("Init", "/web_stackNJS/creationBDD.php")));
app.get('/test-ajout', (req, res) => res.send(generatePage("Ajout", "/web_stackNJS/ajoutVoituresGarage.php")));
app.get('/test-liste', (req, res) => res.send(generatePage("Liste", "/web_stackNJS/listeVoitures.php")));

app.listen(3000);