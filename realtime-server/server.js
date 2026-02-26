const WebSocket = require('ws');
const http = require('http');

// ===============================
// WEBSOCKET SERVER
// ===============================
const wss = new WebSocket.Server({ port: 8081 });

let users = {}; // userId -> socket

wss.on('connection', function connection(ws) {

    console.log("New WebSocket connection");

    ws.on('message', function incoming(message) {

        try {
            const data = JSON.parse(message);

            // Register user
            if (data.type === 'register') {
                users[data.userId] = ws;
                ws.userId = data.userId; // attach userId to socket
                console.log("User registered:", data.userId);
            }

        } catch (e) {
            console.log("Invalid WS message");
        }
    });

    ws.on('close', () => {

        if (ws.userId && users[ws.userId]) {
            delete users[ws.userId];
            console.log("User disconnected:", ws.userId);
        }

    });

});

console.log("WebSocket server running on port 8081");

// ===============================
// HTTP BRIDGE FOR SYMFONY
// ===============================
const server = http.createServer((req, res) => {

    if (req.method === 'POST' && req.url === '/notify') {

        let body = '';

        req.on('data', chunk => {
            body += chunk.toString();
        });

        req.on('end', () => {

            try {
                const data = JSON.parse(body);

                // 🔥 BROADCAST TO ALL CONNECTED USERS
                for (let userId in users) {

                    const socket = users[userId];

                    if (socket && socket.readyState === WebSocket.OPEN) {

                        socket.send(JSON.stringify(data.payload));
                        console.log("Message broadcast to user:", userId);

                    }

                }

                res.writeHead(200);
                res.end('OK');

            } catch (e) {
                console.log("Invalid JSON from Symfony");
                res.writeHead(400);
                res.end('Invalid JSON');
            }

        });

    } else {
        res.writeHead(404);
        res.end();
    }

});

server.listen(8082, () => {
    console.log("HTTP bridge running on port 8082");
});