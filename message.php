<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

error_reporting(0); // prevent warnings breaking JSON

$output = [];

if (!isset($_SESSION['user_id']) || !isset($_GET['match_id'])) {
    echo json_encode($output);
    exit;
}

$userId = $_SESSION['user_id'];
$matchId = $_GET['match_id'];

try {
    // Verify user access
    $stmt = $pdo->prepare("SELECT id FROM matches WHERE id = ? AND (user1_id = ? OR user2_id = ?)");
    $stmt->execute([$matchId, $userId, $userId]);
    if ($stmt->rowCount() === 0) {
        echo json_encode($output);
        exit;
    }

    // Optional: fetch only messages after a timestamp
    $after = isset($_GET['after']) ? $_GET['after'] : null;
    if ($after) {
        $stmt = $pdo->prepare("
            SELECT m.*, u.first_name as sender_name
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE m.match_id = ? AND m.sent_at > ?
            ORDER BY m.sent_at ASC
        ");
        $stmt->execute([$matchId, $after]);
    } else {
        $stmt = $pdo->prepare("
            SELECT m.*, u.first_name as sender_name
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE m.match_id = ?
            ORDER BY m.sent_at ASC
        ");
        $stmt->execute([$matchId]);
    }

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($messages ?: $output);
} catch (PDOException $e) {
    echo json_encode($output);
}
?>
