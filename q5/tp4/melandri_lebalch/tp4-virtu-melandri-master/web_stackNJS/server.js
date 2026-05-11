const express = require('express');
const path = require('path');
const cors = require('cors');

const app = express();
const port = 3000;

app.use(cors());
app.use(express.static(path.join(__dirname, 'www')));

// Importation des endpoints comme s'ils étaient des scripts autonomes (similaire au PHP)
const creationBDD = require('./bd/creationBDD');
const ajoutVoituresGarage = require('./ajoutVoituresGarage');
const listeVoitures = require('./listeVoitures');

// On mappe les anciennes URLs demandées (même avec le .php à la fin !) 
// vers nos anciennes fonctions pour ne pas que vous ayez à changer vos tests !
app.get('/web_stackNJS/creationBDD.php', creationBDD);
app.get('/web_stackNJS/ajoutVoituresGarage.php', ajoutVoituresGarage);
app.get('/web_stackNJS/listeVoitures.php', listeVoitures);

// On ajoute les chemins normaux aussi par sécurité
app.get('/creationBDD.php', creationBDD);
app.get('/ajoutVoituresGarage.php', ajoutVoituresGarage);
app.get('/listeVoitures.php', listeVoitures);

app.listen(port, () => {
  console.log(`Serveur web Node.js démarré sur http://localhost:${port}/`);
});
