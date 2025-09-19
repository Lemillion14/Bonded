// server.js
const WebSocket = require('ws');
const mysql = require('mysql2/promise');

// Map userId → WebSocket
const userConnections = new Map();

// MySQL connection pool
const db = mysql.createPool({
    host: 'localhost',
    user: 'root',
    password: '', // your password
    database: 'for_u_app',
});

const wss = new WebSocket.Server({ port: 3000 }, () => {
    console.log('WebSocket server running on ws://localhost:3000');
});

wss.on('connection', (ws) => {
    let currentUserId = null;

    ws.on('message', async (message) => {
        const data = JSON.parse(message);

        if (data.type === 'register') {
            currentUserId = data.user_id;
            userConnections.set(currentUserId, ws);
            console.log(`User ${currentUserId} connected`);
            return;
        }

        if (data.type === 'chat_message') {
            const { target_id, match_id, body } = data;

            // Save message to DB
            await db.query(
                'INSERT INTO messages (match_id, sender_id, body) VALUES (?, ?, ?)',
                [match_id, currentUserId, body]
            );

            // Send to target if online
            if (userConnections.has(target_id)) {
                const targetWs = userConnections.get(target_id);
                targetWs.send(JSON.stringify({
                    type: 'incoming_message',
                    sender_id: currentUserId,
                    match_id,
                    body
                }));
            }
        }
    });

    ws.on('close', () => {
        if (currentUserId) {
            userConnections.delete(currentUserId);
            console.log(`User ${currentUserId} disconnected`);
        }
    });

    ws.on('error', (err) => console.error('WebSocket error:', err));
});
