<?php
require_once 'db.php';
require_once 'vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $userConnections;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->userConnections = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        
        if (!$data) return;
        
        switch ($data['type']) {
            case 'auth':
                $this->userConnections[$data['user_id']] = $from;
                echo "User {$data['user_id']} authenticated\n";
                break;
                
            case 'message':
                $recipientId = $this->getRecipientId($data['match_id'], $data['sender_id']);
                
                if (isset($this->userConnections[$recipientId])) {
                    $this->userConnections[$recipientId]->send(json_encode([
                        'type' => 'message',
                        'sender_id' => $data['sender_id'],
                        'match_id' => $data['match_id'],
                        'message' => $data['message'],
                        'timestamp' => $data['timestamp']
                    ]));
                }
                
                // Save message to database
                $this->saveMessage($data['match_id'], $data['sender_id'], $data['message']);
                break;
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        $userId = array_search($conn, $this->userConnections, true);
        if ($userId !== false) {
            unset($this->userConnections[$userId]);
        }
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
    
    protected function getRecipientId($matchId, $senderId) {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT CASE WHEN user1_id = ? THEN user2_id ELSE user1_id END as recipient_id
            FROM matches WHERE id = ?
        ");
        $stmt->execute([$senderId, $matchId]);
        
        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['recipient_id'];
        }
        return null;
    }
    
    protected function saveMessage($matchId, $senderId, $message) {
        global $pdo;
        $stmt = $pdo->prepare("
            INSERT INTO messages (match_id, sender_id, body, sent_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$matchId, $senderId, $message]);
    }
}

// Run the server
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8080
);

$server->run();