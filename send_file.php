<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

if (!isset($_FILES['file']) || !isset($_POST['match_id'])) {
    echo json_encode(['success' => false, 'error' => 'No file or match_id']);
    exit;
}

$userId = $_SESSION['user_id'];
$matchId = (int)$_POST['match_id'];

$uploadDir = "uploads/";
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$fileName = time() . "_" . basename($_FILES["file"]["name"]);
$targetFile = $uploadDir . $fileName;

if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
    $type = strpos($_FILES["file"]["type"], "image") === 0 ? "image" : "video";

    $stmt = $pdo->prepare("INSERT INTO messages (match_id, sender_id, body, type) VALUES (?, ?, ?, ?)");
    $stmt->execute([$matchId, $userId, $targetFile, $type]);

    // Return relative path (browser can load it directly)
    echo json_encode(['success' => true, 'file_url' => $targetFile]);
} else {
    echo json_encode(['success' => false, 'error' => 'Upload failed']);
}
