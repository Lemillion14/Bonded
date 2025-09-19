<?php
session_start();
require 'db.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Ensure user session exists
if (!isset($_SESSION['email'])) {
    echo json_encode(["success" => false, "message" => "Session expired. Please register again."]);
    exit();
}

$email = $_SESSION['email'];

// Connect DB
$conn = new mysqli("localhost", "root", "", "for_u_app");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit();
}

// Generate new OTP
$otp = rand(100000, 999999);
$expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

// Update in DB
$stmt = $conn->prepare("UPDATE users SET otp=?, otp_expirey=? WHERE email=?");
$stmt->bind_param("iss", $otp, $expiry, $email);
$stmt->execute();
$stmt->close();

// Send mail
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'nyimarn2003@gmail.com'; 
    $mail->Password = 'mzaybfwossewtmti'; // App password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('nyimarn2003@gmail.com', 'BONDED');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Your New OTP Code';
    $mail->Body = "Hi,<br><br>Your new OTP is: <b>$otp</b><br><br>This code is valid for 5 minutes.";

    $mail->send();
    echo json_encode(["success" => true, "message" => "OTP resent successfully."]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Mailer error: {$mail->ErrorInfo}"]);
}

$conn->close();
