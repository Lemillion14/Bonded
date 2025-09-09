
<?php
// Remove a match and related messages
function unmatch($user_id, $other_user_id) {
    $conn = getDbConnection();
    // Find the match id
    $stmt = $conn->prepare('SELECT id FROM matches WHERE (user1_id = :u1 AND user2_id = :u2) OR (user1_id = :u2 AND user2_id = :u1)');
    $stmt->bindParam(':u1', $user_id);
    $stmt->bindParam(':u2', $other_user_id);
    $stmt->execute();
    $match = $stmt->fetch(PDO::FETCH_ASSOC);
    if($match) {
        $match_id = $match['id'];
        // Delete messages
        $delMsg = $conn->prepare('DELETE FROM messages WHERE match_id = :match_id');
        $delMsg->bindParam(':match_id', $match_id);
        $delMsg->execute();
        // Delete match
        $delMatch = $conn->prepare('DELETE FROM matches WHERE id = :match_id');
        $delMatch->bindParam(':match_id', $match_id);
        $delMatch->execute();
        // Optionally, delete swipes between these users
        $delSwipes = $conn->prepare('DELETE FROM swipes WHERE (swiper_id = :u1 AND swiped_id = :u2) OR (swiper_id = :u2 AND swiped_id = :u1)');
        $delSwipes->bindParam(':u1', $user_id);
        $delSwipes->bindParam(':u2', $other_user_id);
        $delSwipes->execute();
        return true;
    }
    return false;
}

session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';

// Helper to get DB connection
function getDbConnection() {
    $database = new Database();
    return $database->getConnection();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserById($id) {
    $conn = getDbConnection();
    $stmt = $conn->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function getMatches($user_id) {
    $conn = getDbConnection();
    $stmt = $conn->prepare('SELECT u.* FROM users u
        INNER JOIN matches m ON (u.id = m.user1_id OR u.id = m.user2_id)
        WHERE (m.user1_id = :uid OR m.user2_id = :uid) AND u.id != :uid');
    $stmt->bindParam(':uid', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function getPotentialMatches($user_id) {
    $conn = getDbConnection();
    $stmt = $conn->prepare('SELECT * FROM users WHERE id != :uid AND id NOT IN (
        SELECT target_id FROM swipes WHERE swiper_id = :uid
    )');
    $stmt->bindParam(':uid', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function calculateAge($birthdate) {
    $birthDate = new DateTime($birthdate);
    $today = new DateTime();
    $age = $today->diff($birthDate);
    return $age->y;
}

function sendMessage($user_id, $match_id, $message) {
    $conn = getDbConnection();
    $stmt = $conn->prepare('INSERT INTO messages (match_id, sender_id, message, sent_at) VALUES (:match_id, :sender_id, :message, NOW())');
    $stmt->bindParam(':match_id', $match_id);
    $stmt->bindParam(':sender_id', $user_id);
    $stmt->bindParam(':message', $message);
    return $stmt->execute();
}

function getMessages($user_id, $match_id) {
    $conn = getDbConnection();
    $stmt = $conn->prepare('SELECT * FROM messages WHERE match_id = :match_id ORDER BY sent_at ASC');
    $stmt->bindParam(':match_id', $match_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function uploadImage($file, $target_dir) {
    $target_file = $target_dir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if image file is actual image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return false;
    }
    
    // Generate unique filename
    $new_filename = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $new_filename;
    }
    return false;
}
?>