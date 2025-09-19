<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$sender_id = $_SESSION['user_id'];
$match_id = $_POST['match_id'] ?? null;

if (!$match_id || !isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

try {
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileTmp = $_FILES['file']['tmp_name'];
    $fileName = uniqid() . "_" . basename($_FILES['file']['name']);
    $filePath = $uploadDir . $fileName;
    $fileUrl = "uploads/" . $fileName; // relative path

    if (!move_uploaded_file($fileTmp, $filePath)) {
        throw new Exception("Failed to upload file.");
    }

    $mimeType = mime_content_type($filePath);

    // Insert into messages (empty body, just attachment)
    $stmt = $pdo->prepare("INSERT INTO messages (match_id, sender_id, body) VALUES (?, ?, NULL)");
    $stmt->execute([$match_id, $sender_id]);
    $messageId = $pdo->lastInsertId();

    // Insert attachment
    $stmt = $pdo->prepare("INSERT INTO message_attachments (message_id, url, mime_type) VALUES (?, ?, ?)");
    $stmt->execute([$messageId, $fileUrl, $mimeType]);

    echo json_encode([
        'success' => true,
        'message_id' => $messageId,
        'url' => $fileUrl,
        'mime_type' => $mimeType
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
