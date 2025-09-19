<?php
// get_matches.php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            u.id, 
            u.first_name, 
            u.gender, 
            up.url as avatar,
            (SELECT body FROM messages WHERE match_id = m.id ORDER BY sent_at DESC LIMIT 1) as last_message,
            (SELECT sent_at FROM messages WHERE match_id = m.id ORDER BY sent_at DESC LIMIT 1) as last_active,
            (SELECT COUNT(*) FROM messages WHERE match_id = m.id AND sender_id != ? AND read_at IS NULL) as unread_count
        FROM matches m
        JOIN users u ON (m.user1_id = u.id OR m.user2_id = u.id) AND u.id != ?
        LEFT JOIN user_photos up ON u.id = up.user_id AND up.position = 1
        WHERE (m.user1_id = ? OR m.user2_id = ?)
        ORDER BY last_active DESC
    ");
    
    $stmt->execute([$userId, $userId, $userId, $userId]);
    $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($matches);
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Database error']);
}
?>