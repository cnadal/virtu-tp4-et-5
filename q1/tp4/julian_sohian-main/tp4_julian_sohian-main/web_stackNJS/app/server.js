const http = require('http');

http.createServer((req, res) => {
  res.write("Accueil produits");
  res.end();
}).listen(3000);