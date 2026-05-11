const express = require('express');
const path = require('path');
const cors = require('cors');
const { exec } = require('child_process');

const app = express();
const PORT = 3000;

app.use(cors());

// Intercept PHP requests
app.get('/*.php', (req, res) => {
    const scriptPath = path.join(__dirname, 'public', req.path);
    exec(`php-cgi ${scriptPath}`, (err, stdout, stderr) => {
        if (err) {
            console.error(err);
            return res.status(500).send("Erreur serveur interne lors de l'exécution de PHP.");
        }
        // php-cgi outputs headers followed by body. Split by \r\n\r\n
        const parts = stdout.split('\r\n\r\n');
        const body = parts.slice(1).join('\r\n\r\n');
        res.send(body || stdout);
    });
});

app.use(express.static(path.join(__dirname, 'public')));

app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

app.listen(PORT, () => {
  console.log(`Serveur Node sur http://localhost:${PORT}`);
});
