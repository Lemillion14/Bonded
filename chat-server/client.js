let websocket = null;
let reconnectAttempts = 0;
const maxReconnectAttempts = 5;

function connectWebSocket() {
    if (websocket && websocket.readyState === WebSocket.OPEN) {
        return; // Already connected
    }
    
    try {
        websocket = new WebSocket('ws://localhost:3000');
        
        websocket.onopen = function() {
            console.log('WebSocket connection established');
            reconnectAttempts = 0;
            
            // Register user with the WebSocket server
            websocket.send(JSON.stringify({
                type: 'register',
                user_id: currentUserId
            }));
        };
        
        websocket.onmessage = function(event) {
            const data = JSON.parse(event.data);
            console.log('WebSocket message received:', data);
            
            if (data.type === 'incoming_message') {
                // Handle incoming message
                addMessageToChat(data.body, data.sender_id === currentUserId, data.timestamp);
            } else if (data.type === 'error') {
                console.error('WebSocket error:', data.message);
            }
        };
        
        websocket.onerror = function(error) {
            console.error('WebSocket error:', error);
        };
        
        websocket.onclose = function(event) {
            console.log('WebSocket connection closed');
            
            // Attempt to reconnect with exponential backoff
            if (reconnectAttempts < maxReconnectAttempts) {
                const delay = Math.pow(2, reconnectAttempts) * 1000;
                reconnectAttempts++;
                setTimeout(connectWebSocket, delay);
            }
        };
    } catch (error) {
        console.error('Failed to create WebSocket connection:', error);
    }
}

// Initialize WebSocket connection when page loads
document.addEventListener('DOMContentLoaded', function() {
    connectWebSocket();
});