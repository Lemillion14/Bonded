<?php
require 'db.php';

header('Content-Type: application/json');

if (!isset($_GET['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'No email provided']);
    exit;
}

$email = trim($_GET['email']);

$conn = new mysqli("localhost", "root", "", "dating_app");
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'DB connection failed']);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['status' => 'taken']);
} else {
    echo json_encode(['status' => 'available']);
}

$stmt->close();
$conn->close();
