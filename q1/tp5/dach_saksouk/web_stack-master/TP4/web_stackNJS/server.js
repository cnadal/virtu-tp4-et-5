const express = require('express');
const cors = require('cors');
const path = require('path');

const app = express();
const port = 3000;

app.use(cors());
app.use(express.static(path.join(__dirname, 'public')));

app.listen(port, () => {
  console.log(`Serveur Node.js à l'écoute sur le port ${port}`);
});
