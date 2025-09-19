<?php
session_start();
require 'db.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$newEmail = trim($data['email'] ?? '');

if(!filter_var($newEmail, FILTER_VALIDATE_EMAIL)){
    echo json_encode(['success'=>false,'message'=>'Invalid email']);
    exit;
}

if(!isset($_SESSION['email'])){
    echo json_encode(['success'=>false,'message'=>'Session expired']);
    exit;
}

$oldEmail = $_SESSION['email'];

// Generate new OTP
$otp = rand(100000, 999999);
$expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

$conn = new mysqli("localhost","root","","dating_app");
if($conn->connect_error){
    echo json_encode(['success'=>false,'message'=>'DB connection failed']);
    exit;
}

$stmt = $conn->prepare("UPDATE users SET email=?, otp=?, otp_expirey=? WHERE email=?");
$stmt->bind_param("siss", $newEmail, $otp, $expiry, $oldEmail);

if (!$stmt->execute()) {
    echo json_encode(['success'=>false,'message'=>'Database error: '.$stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}

if ($stmt->affected_rows == 0) {
    echo json_encode(['success'=>false,'message'=>'Email update failed: user not found or email unchanged']);
    $stmt->close();
    $conn->close();
    exit;
}

$stmt->close();
$conn->close();

$_SESSION['email'] = $newEmail;

// Send new OTP
$mail = new PHPMailer(true);
try{
    $mail->isSMTP();
    $mail->Host='smtp.gmail.com';
    $mail->SMTPAuth=true;
    $mail->Username='nyimarn2003@gmail.com';
    $mail->Password='mzaybfwossewtmti';
    $mail->SMTPSecure=PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port=587;

    $mail->setFrom('nyimarn2003@gmail.com','Dating App');
    $mail->addAddress($newEmail);
    $mail->isHTML(true);
    $mail->Subject='Your New OTP Code';
    $mail->Body="Hi,<br><br>Your new OTP is: <b>$otp</b><br>Valid for 5 minutes.";

    $mail->send();
    echo json_encode(['success'=>true,'message'=>'Email updated and OTP sent']);
}catch(Exception $e){
    echo json_encode(['success'=>false,'message'=>'Mailer error: '.$mail->ErrorInfo]);
}
