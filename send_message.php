<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

$output = ['success' => false, 'error' => 'Unknown error'];

$postData = json_decode(file_get_contents('php://input'), true);

if (!isset($_SESSION['user_id']) || !isset($postData['match_id']) || !isset($postData['message'])) {
    $output['error'] = 'Missing parameters or not logged in';
    echo json_encode($output);
    exit;
}

$userId = $_SESSION['user_id'];
$matchId = $postData['match_id'];
$body = trim($postData['message']);

try {
    // Verify the user has access to this match
    $stmt = $pdo->prepare("SELECT id FROM matches WHERE id = ? AND (user1_id = ? OR user2_id = ?)");
    $stmt->execute([$matchId, $userId, $userId]);

    if ($stmt->rowCount() === 0) {
        $output['error'] = 'Access denied';
        echo json_encode($output);
        exit;
    }

    // Insert the message
    $stmt = $pdo->prepare("INSERT INTO messages (match_id, sender_id, body) VALUES (?, ?, ?)");
    $stmt->execute([$matchId, $userId, $body]);

    $output = [
        'success' => true,
        'message_id' => $pdo->lastInsertId(),
        'body' => $body,
        'sender_id' => $userId,
        'sent_at' => date('Y-m-d H:i:s')
    ];

    echo json_encode($output);
} catch (PDOException $e) {
    $output['error'] = 'Database error: ' . $e->getMessage();
    echo json_encode($output);
}
