const WebSocket = require('ws');
const http = require('http');

const wss = new WebSocket.Server({ port: 8081 });

let users = {};

// ===============================
// WEBSOCKET CONNECTION
// ===============================
wss.on('connection', function connection(ws) {

    ws.on('message', function incoming(message) {

        try {
            const data = JSON.parse(message);

            // Register user
            if (data.type === 'register') {
                users[data.userId] = ws;
                console.log("User registered:", data.userId);
            }

        } catch (e) {
            console.log("Invalid WS message");
        }
    });

    ws.on('close', () => {
        for (let userId in users) {
            if (users[userId] === ws) {
                delete users[userId];
                console.log("User disconnected:", userId);
            }
        }
    });
});

console.log("WebSocket server running on port 8081");

// ===============================
// HTTP SERVER FOR SYMFONY
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

                const targetSocket = users[data.targetUserId];

                if (targetSocket) {

                    // Forward payload exactly as received
                    targetSocket.send(JSON.stringify(data.payload));

                    console.log("Message sent to user:", data.targetUserId);

                } else {
                    console.log("User not connected:", data.targetUserId);
                }

                res.writeHead(200);
                res.end('OK');

            } catch (e) {
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