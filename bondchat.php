<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bond - Real-Time Chat</title>
    <style>
        :root {
            --primary-color: #e91e63;
            --secondary-color: #f8bbd0;
            --dark-color: #880e4f;
            --light-color: #fce4ec;
            --text-color: #333;
            --light-text: #777;
            --bg-color: #fff;
            --chat-bg: #f5f5f5;
            --border-color: #e0e0e0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f9f9f9;
            color: var(--text-color);
            line-height: 1.6;
        }
        
        .chat-container {
            display: flex;
            max-width: 1200px;
            height: 600px;
            margin: 40px auto;
            background-color: var(--bg-color);
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .matches-sidebar {
            width: 30%;
            background-color: var(--light-color);
            border-right: 1px solid var(--border-color);
            overflow-y: auto;
        }
        
        .chat-header {
            padding: 20px;
            background-color: var(--primary-color);
            color: white;
            text-align: center;
        }
        
        .matches-list {
            padding: 10px;
        }
        
        .match-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .match-item:hover {
            background-color: var(--secondary-color);
        }
        
        .match-item.active {
            background-color: var(--secondary-color);
            border-left: 4px solid var(--primary-color);
        }
        
        .match-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            border: 2px solid var(--primary-color);
        }
        
        .match-info {
            flex: 1;
        }
        
        .match-name {
            font-weight: 600;
            color: var(--text-color);
        }
        
        .match-preview {
            font-size: 0.9em;
            color: var(--light-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }
        
        .match-time {
            font-size: 0.8em;
            color: var(--light-text);
            text-align: right;
        }
        
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .chat-header-main {
            padding: 15px 20px;
            background-color: var(--bg-color);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
        }
        
        .current-match-name {
            font-weight: 600;
            font-size: 1.2em;
        }
        
        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background-color: var(--chat-bg);
        }
        
        .message {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }
        
        .message.sent {
            align-items: flex-end;
        }
        
        .message.received {
            align-items: flex-start;
        }
        
        .message-bubble {
            max-width: 70%;
            padding: 12px 15px;
            border-radius: 18px;
            margin-bottom: 5px;
            position: relative;
        }
        
        .sent .message-bubble {
            background-color: var(--primary-color);
            color: white;
            border-bottom-right-radius: 4px;
        }
        
        .received .message-bubble {
            background-color: var(--bg-color);
            color: var(--text-color);
            border-bottom-left-radius: 4px;
            border: 1px solid var(--border-color);
        }
        
        .message-time {
            font-size: 0.7em;
            color: var(--light-text);
            padding: 0 5px;
        }
        
        .chat-input {
            padding: 15px;
            background-color: var(--bg-color);
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
        }
        
        .message-input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 24px;
            outline: none;
            font-size: 1em;
        }
        
        .send-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-left: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
        }
        
        .send-button:hover {
            background-color: var(--dark-color);
        }
        
        .no-chat-selected {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--light-text);
            text-align: center;
            padding: 20px;
        }
        
        .no-chat-selected i {
            font-size: 3em;
            margin-bottom: 20px;
            color: var(--secondary-color);
        }
        
        .unread-count {
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7em;
            margin-left: auto;
        }
        
        @media (max-width: 768px) {
            .chat-container {
                flex-direction: column;
                height: 100vh;
                margin: 0;
                border-radius: 0;
            }
            
            .matches-sidebar {
                width: 100%;
                height: 40%;
            }
            
            .chat-main {
                width: 100%;
                height: 60%;
            }
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="matches-sidebar">
            <div class="chat-header">
                <h2>Your Matches</h2>
            </div>
            <div class="matches-list" id="matchesList">
                <!-- Matches will be loaded here dynamically -->
                <div class="match-item">
                    <div class="match-info">
                        <div class="match-name">Loading matches...</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="chat-main">
            <div class="chat-header-main">
                <div class="current-match-name">Select a match to start chatting</div>
            </div>
            
            <div class="chat-messages" id="chatMessages">
                <div class="no-chat-selected">
                    <i class="fas fa-comments"></i>
                    <h3>Select a match to start chatting</h3>
                    <p>Your messages will appear here once you select a match</p>
                </div>
            </div>
            
            <div class="chat-input" style="display: none;" id="chatInput">
                <input type="text" class="message-input" id="messageInput" placeholder="Type a message...">
                <button class="send-button" id="sendButton">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageInput = document.getElementById('messageInput');
            const sendButton = document.getElementById('sendButton');
            const chatMessages = document.getElementById('chatMessages');
            const chatInput = document.getElementById('chatInput');
            const matchesList = document.getElementById('matchesList');
            
            let currentMatchId = null;
            let websocket = null;
            
            // Load user's matches
            loadMatches();
            
            // Function to load matches from the server
            function loadMatches() {
                // In a real implementation, this would fetch from your API
                fetch('matches.php')
                    .then(response => response.json())
                    .then(matches => {
                        displayMatches(matches);
                    })
                    .catch(error => {
                        console.error('Error loading matches:', error);
                        matchesList.innerHTML = '<div class="match-item"><div class="match-info"><div class="match-name">Error loading matches</div></div></div>';
                    });
            }
            
            // Display matches in the sidebar
            function displayMatches(matches) {
                if (matches.length === 0) {
                    matchesList.innerHTML = '<div class="match-item"><div class="match-info"><div class="match-name">No matches yet</div></div></div>';
                    return;
                }
                
                matchesList.innerHTML = '';
                matches.forEach(match => {
                    const matchItem = document.createElement('div');
                    matchItem.className = 'match-item';
                    matchItem.dataset.matchId = match.id;
                    
                    matchItem.innerHTML = `
                        <img src="${match.avatar || 'https://randomuser.me/api/portraits/lego/1.jpg'}" alt="Profile" class="match-avatar">
                        <div class="match-info">
                            <div class="match-name">${match.first_name}</div>
                            <div class="match-preview">${match.last_message || 'Start a conversation'}</div>
                        </div>
                        <div class="match-time">${match.last_active || ''}</div>
                        ${match.unread_count > 0 ? `<div class="unread-count">${match.unread_count}</div>` : ''}
                    `;
                    
                    matchItem.addEventListener('click', () => {
                        // Remove active class from all matches
                        document.querySelectorAll('.match-item').forEach(item => {
                            item.classList.remove('active');
                        });
                        
                        // Add active class to clicked match
                        matchItem.classList.add('active');
                        
                        // Load chat with this match
                        loadChat(match.id);
                    });
                    
                    matchesList.appendChild(matchItem);
                });
            }
            
            // Load chat with a specific match
            function loadChat(matchId) {
                currentMatchId = matchId;
                
                // Show chat input
                chatInput.style.display = 'flex';
                
                // Clear chat messages
                chatMessages.innerHTML = '';
                
                // Load message history
                fetch(`message.php?match_id=${matchId}`)
                    .then(response => response.json())
                    .then(messages => {
                        if (messages.length === 0) {
                            chatMessages.innerHTML = `
                                <div class="no-chat-selected">
                                    <i class="fas fa-comment-slash"></i>
                                    <h3>No messages yet</h3>
                                    <p>Send a message to start the conversation</p>
                                </div>
                            `;
                        } else {
                            messages.forEach(message => {
                                addMessageToChat(message.body, message.sender_id === currentUserId, message.sent_at);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error loading messages:', error);
                        chatMessages.innerHTML = '<div class="no-chat-selected"><h3>Error loading messages</h3></div>';
                    });
                
                // Initialize WebSocket connection for real-time messaging
                initializeWebSocket();
            }
            
            // Initialize WebSocket connection
            function initializeWebSocket() {
                // Close existing connection if any
                if (websocket) {
                    websocket.close();
                }
                
                // Create new WebSocket connection
                websocket = new WebSocket('ws://localhost:3000');
                
                websocket.onopen = function(event) {
                    console.log('WebSocket connection established');
                    // Send authentication data
                    websocket.send(JSON.stringify({
                        type: 'auth',
                        user_id: currentUserId,
                        match_id: currentMatchId
                    }));
                };
                
                websocket.onmessage = function(event) {
                    const data = JSON.parse(event.data);
                    
                    if (data.type === 'message') {
                        // Add received message to chat
                        addMessageToChat(data.message, data.sender_id === currentUserId, data.timestamp);
                    }
                };
                
                websocket.onerror = function(error) {
                    console.error('WebSocket error:', error);
                };
                
                websocket.onclose = function(event) {
                    console.log('WebSocket connection closed');
                };
            }
            
            // Add a message to the chat UI
            function addMessageToChat(text, isSent, timestamp) {
                // Remove "no chat selected" message if it's present
                if (chatMessages.querySelector('.no-chat-selected')) {
                    chatMessages.innerHTML = '';
                }
                
                const messageDiv = document.createElement('div');
                messageDiv.className = isSent ? 'message sent' : 'message received';
                
                const time = new Date(timestamp);
                const timeString = time.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                messageDiv.innerHTML = `
                    <div class="message-bubble">${text}</div>
                    <div class="message-time">${timeString}</div>
                `;
                
                chatMessages.appendChild(messageDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
            
            // Send message function
            function sendMessage() {
                const message = messageInput.value.trim();
                if (message && currentMatchId) {
                    // Send message via WebSocket
                    if (websocket && websocket.readyState === WebSocket.OPEN) {
                        websocket.send(JSON.stringify({
                            type: 'message',
                            sender_id: currentUserId,
                            match_id: currentMatchId,
                            message: message,
                            timestamp: new Date().toISOString()
                        }));
                    }
                    
                    // Also send via AJAX for persistence
                    fetch('send_message.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            match_id: currentMatchId,
                            message: message
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            console.error('Error sending message:', data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error sending message:', error);
                    });
                    
                    // Clear input
                    messageInput.value = '';
                }
            }
            
            // Send message on button click
            sendButton.addEventListener('click', sendMessage);
            
            // Send message on Enter key
            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });
        });
        
        // In a real implementation, this would come from your authentication system
        const currentUserId = 1; // This should be the logged-in user's ID
    </script>
</body>
</html>