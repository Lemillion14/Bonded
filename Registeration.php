<?php
session_start();
require 'db.php'; // mysqli connection

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Composer autoload

// Initialize variables
$name = $email = $password = $confirm_password = "";
$name_error = $email_error = $password_error = $confirm_error = $terms_error = "";
$general_error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['full_name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $terms_accepted = isset($_POST['terms']);

    // Validation
    if (empty($name)) $name_error = "Full Name is required.";
    elseif (strlen($name) < 3) $name_error = "Full Name must be at least 3 characters.";

    if (empty($email)) $email_error = "Email is required.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $email_error = "Invalid email format.";

    if (empty($password)) $password_error = "Password is required.";
    elseif (strlen($password) < 6) $password_error = "Password must be at least 6 characters.";
    elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $password_error = "Password must contain uppercase, lowercase, and a number.";
    }

    if ($password !== $confirm_password) $confirm_error = "Passwords do not match.";
    if (!$terms_accepted) $terms_error = "You must agree to Terms and Conditions.";

    // Proceed if no errors
    if (empty($name_error) && empty($email_error) && empty($password_error) && empty($confirm_error) && empty($terms_error)) {
        
        // Check if email exists
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $email_error = "An account with this email already exists.";
        } else {
            $password_hashed = password_hash($password, PASSWORD_BCRYPT);
            $otp = rand(100000, 999999);
            $otp_expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (first_name,email,password_hash,otp,otp_expiry,status) VALUES (?, ?, ?, ?, ?, 0)");
            $stmt->bind_param("sssss", $name, $email, $password_hashed, $otp, $otp_expiry);

            if ($stmt->execute()) {
                $_SESSION['email'] = $email;
                $_SESSION['otp_attempts'] = 0;

                // Send OTP via PHPMailer
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'nyimarn2003@gmail.com'; // replace with your Gmail
                    $mail->Password = 'mzaybfwossewtmti';   // Gmail app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('nyimarn2003@gmail.com', 'BONDED');
                    $mail->addAddress($email, $name);
                    $mail->isHTML(true);
                    $mail->Subject = 'Your OTP Code for BONDED';
                    $mail->Body = "<p>Hi $name,</p><p>Your OTP is: <b>$otp</b></p><p>Valid for 5 minutes.</p>";
                    $mail->AltBody = "Hi $name, Your OTP is: $otp (valid for 5 minutes)";

                    $mail->send();

                    header("Location: OTP_vertification.php");
                    exit();
                } catch (Exception $e) {
                    $general_error = "Could not send OTP email. Please check your SMTP settings. Error: " . $mail->ErrorInfo;
                    error_log("PHPMailer error: " . $mail->ErrorInfo);
                }
            } else {
                $general_error = "Database error: " . $stmt->error;
            }
            $stmt->close();
        }
        $stmt_check->close();
    }
}
?>
<!-- HTML remains the same until the form -->
<?php include("header.php"); ?>

<main class="w-full">
  <section class="hero-frame">
    <div class="hero-bg" aria-hidden="true"></div>

    <div class="relative screen-center px-4">
      <div class="glass w-full max-w-[420px] p-6">
        <h1 class="text-[22px] leading-7 text-gray-800 mb-1">
          Create your <span class="font-bold" style="color:var(--brand)">BONDED</span> account
        </h1>
        <p class="text-[12px] text-gray-500 mb-6">Sign up and start building real bonds.</p>

        <!-- Display general errors -->
        <?php if (!empty($general_error)): ?>
          <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
            <?= $general_error ?>
            <p class="mt-1">Please check your email settings or try again later.</p>
          </div>
        <?php endif; ?>

        <form class="space-y-3" action="" method="post" novalidate>
          <!-- Form fields remain the same -->
          <div>
            <label for="full_name" class="block text-[13px] mb-1 text-gray-700">Full Name</label>
            <input id="full_name" class="input" type="text" name="full_name" placeholder="e.g. John Doe" value="<?= htmlspecialchars($name) ?>" required />
            <small class="text-red-500"><?= $name_error ?? '' ?></small>
          </div>

          <div>
            <label for="email" class="block text-[13px] mb-1 text-gray-700">Email</label>
            <input id="email" class="input" type="email" name="email" placeholder="example@gmail.com" value="<?= htmlspecialchars($email) ?>" required />
            <small class="text-red-500"><?= $email_error ?? '' ?></small>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label for="password" class="block text-[13px] mb-1 text-gray-700">Password</label>
              <input id="password" class="input" type="password" name="password" placeholder="min. 6 characters" required />
              <small class="text-red-500"><?= $password_error ?? '' ?></small>
            </div>
            <div>
              <label for="confirm_password" class="block text-[13px] mb-1 text-gray-700">Confirm Password</label>
              <input id="confirm_password" class="input" type="password" name="confirm_password" placeholder="re-enter password" required />
              <small class="text-red-500"><?= $confirm_error ?? '' ?></small>
            </div>
          </div>

          <div class="text-[11px] leading-4 text-gray-600">
            <label class="inline-flex items-start gap-2 select-none">
              <input type="checkbox" name="terms" class="mt-[2px]" required />
              <span>I agree to the Terms and Conditions and Privacy Policy</span>
            </label>
            <small class="text-red-500"><?= $terms_error ?? '' ?></small>
          </div>

         <div class="flex items-center gap-x-9 pt-1">
  <?php if (isset($_SESSION['verified']) && $_SESSION['verified'] === true): ?>
    <a href="login.php" class="btn btn-ghost flex-1 text-center">Back to Login</a>
    <?php unset($_SESSION['verified']); // clear it so it doesn’t show again ?>
  <?php else: ?>
    <button type="submit" class="btn btn-pink flex-1">Register</button>
  <?php endif; ?>
</div>

          </div>
        </form>

        <!-- Additional troubleshooting tips -->
        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-800">
          <p class="font-medium">Not receiving OTP emails?</p>
          <ul class="list-disc pl-5 mt-1 space-y-1">
            <li>Check your spam or junk folder</li>
            <li>Ensure you entered the correct email address</li>
            <li>Try again in a few minutes</li>
          </ul>
        </div>

        <div class="flex items-center gap-3 my-5">
          <div class="divider-line flex-1"></div>
          <span class="text-xs" style="color:#7c7c7c;">OR</span>
          <div class="divider-line flex-1"></div>
        </div>

        <div class="flex items-center justify-center gap-x-5 gap-y-2 text-gray-500">
          <a href="#" class="icon" aria-label="Instagram"> ... </a>
          <a href="#" class="icon" aria-label="Google"> ... </a>
        </div>

        <?php include __DIR__ . '/partials/footer-links.php'; ?>
      </div>
    </div>
  </section>
</main>

<?php include("footer.php"); ?>