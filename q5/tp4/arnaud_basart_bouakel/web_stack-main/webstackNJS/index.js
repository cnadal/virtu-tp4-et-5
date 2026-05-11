const http = require('http');
const fs = require('fs').promises;

const server = http.createServer(async (req, res) => {
    console.log('🔍', req.url, req.method);

    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

    if (req.method === 'OPTIONS') {
        res.writeHead(204);
        res.end();
        return;
    }

    if (req.url === '/' || req.url === '/favicon.ico') {
        try {
            const data = await fs.readFile('/app/index.html');
            res.writeHead(200, {'Content-Type': 'text/html'});
            res.end(data);
        } catch (err) {
            console.error('HTML ERR:', err);
            res.writeHead(500, {'Content-Type': 'text/plain'});
            res.end('index.html manquant');
        }
        return;
    }

    if (req.url.match(/^\/web_stackNJS\/.*\.php/)) {
        const phpPath = req.url.replace('/web_stackNJS/', '');
        console.log('→ PHP:', phpPath);

        const options = {
            hostname: 'php',
            port: 80,
            path: `/${phpPath}`,
            method: req.method,
            headers: {
                'Host': 'php',
                'User-Agent': req.headers['user-agent'] || 'Node.js Proxy'
            }
        };

        const proxyReq = http.request(options, (proxyRes) => {
            console.log(`PHP ${proxyRes.statusCode}: ${phpPath}`);
            res.writeHead(proxyRes.statusCode, proxyRes.headers);
            proxyRes.pipe(res);
        });

        proxyReq.on('error', (err) => {
            console.error('PHP ERROR:', err.message);
            res.writeHead(503, {'Content-Type': 'text/plain'});
            res.end('PHP service indisponible');
        });

        req.pipe(proxyReq);
        return;
    }

    res.writeHead(404, {'Content-Type': 'text/plain'});
    res.end('404 - Page non trouvée');
});
server.listen(3000, () => {
    console.log('Node.js prêt: http://localhost:3000');
});