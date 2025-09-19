<?php
session_start();
require 'db.php';

$errorEmail = "";
$errorPassword = "";
$generalError = "";

// Only run on POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validate inputs
    if (empty($email)) {
        $errorEmail = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorEmail = "Invalid email format.";
    }
    

    if (empty($password)) {
        $errorPassword = "Password is required.";
    }

    // If no errors, check DB
    if (!$errorEmail && !$errorPassword) {
        $conn = new mysqli("localhost", "root", "", "for_u_app");
        if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

        $stmt = $conn->prepare("SELECT id, password_hash, status FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashedPassword, $status);
            $stmt->fetch();

            if ($status == 0) {
                $generalError = "Please verify your email before logging in.";
            } elseif (password_verify($password, $hashedPassword)) {
                $_SESSION['user_id'] = $id;
                header("Location: dashboard.php");
                exit();
            } else {
                $generalError = "Incorrect password.";
            }
        } else {
            $generalError = "User not found.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>
<?php include("header.php"); ?>

<main class="w-full">
  <section class="hero-frame relative min-h-screen flex items-center justify-center">
    <!-- Background Image -->
    <div class="hero-bg absolute top-0 left-0 w-full h-full bg-cover bg-center z-0" 
         style="background-image: url('img/loginbg.jpg');"></div>

    <!-- Dark Overlay -->
    <div class="absolute top-0 left-0 w-full h-full bg-black bg-opacity-50 z-10"></div>

    <div class="relative screen-center px-4 z-20">
      <div class="glass w-full max-w-[360px] p-6">
        <h1 class="text-[22px] leading-7 text-gray-800 mb-9">
          Start a Lasting <span class="font-bold" style="color:var(--brand)">Bond</span> Today.
        </h1>

        <?php if ($generalError): ?>
            <p class="text-red-500 text-xs mb-2"><?= htmlspecialchars($generalError) ?></p>
        <?php endif; ?>

        <form class="space-y-3" action="" method="post" novalidate>
          <div>
            <label for="email" class="block text-[13px] mb-1 text-gray-700">Email</label>
            <input id="email" name="email" class="input" type="email" placeholder="example@gmail.com" 
                   value="<?= htmlspecialchars($email ?? '') ?>" required />
            <?php if ($errorEmail): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errorEmail) ?></p>
            <?php endif; ?>
          </div>

          <div>
            <label for="password" class="block text-[13px] mb-1 text-gray-700">Password</label>
            <input id="password" name="password" class="input" type="password" placeholder="enter your password" required />
            <?php if ($errorPassword): ?>
                <p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errorPassword) ?></p>
            <?php endif; ?>
          </div>

          <div class="text-[11px] leading-4 text-gray-600">
            <label class="inline-flex items-start gap-2 select-none">
              <input type="checkbox" class="mt-[2px]" required />
              <span>I agree to the Terms and Conditions and Privacy Policy</span>
            </label>
          </div>

          <div class="flex items-center gap-x-9 pt-1">
            <a href="/Dating_app/register.php" class="btn btn-ghost flex-1 text-center">Register</a>
              <button type="submit" class="btn btn-pink flex-1">Log in</button>
          </div>
          
        </form>
      </div>

      <div class="flex items-center gap-3 my-5">
        <div class="divider-line flex-1"></div>
        <span class="text-xs" style="color:#7c7c7c;">OR</span>
        <div class="divider-line flex-1"></div>
      </div>

      <div class="flex items-center justify-center gap-x-5 gap-y-2 text-gray-500">
        <a href="#" class="icon" aria-label="Instagram">...</a>
        <a href="#" class="icon" aria-label="Google">...</a>
      </div>
      <?php include __DIR__ . '/partials/footer-links.php'; ?>
    </div>
  </section>
</main>

<?php include("footer.php"); ?>
