<?php
// mark_as_read.php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['match_id'])) {
    http_response_code(400);
    exit;
}

$userId = $_SESSION['user_id'];
$matchId = $_GET['match_id'];

try {
    $stmt = $pdo->prepare("
        UPDATE messages 
        SET read_at = NOW() 
        WHERE match_id = ? AND sender_id != ? AND read_at IS NULL
    ");
    $stmt->execute([$matchId, $userId]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>