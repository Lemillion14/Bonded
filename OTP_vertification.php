<?php
session_start();
require 'db.php';

// Redirect if user hasn't just signed up
if (!isset($_SESSION['email'])) {
    header("Location: Registeration.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "for_u_app");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$error = "";

// Handle OTP verification
// Handle OTP verification
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['email'];
    $enteredOtp = isset($_POST['otp']) ? trim($_POST['otp']) : '';

    if (empty($enteredOtp) || strlen($enteredOtp) !== 6) {
        $error = "Please enter the full 6-digit OTP.";
    } else {
        $stmt = $conn->prepare("SELECT id, otp, otp_expiry FROM users WHERE email = ? AND status = 0 LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($userId, $dbOtp, $dbExpiry);
        $stmt->fetch();
        $stmt->close();

        if (!$userId) {
            $error = "Account not found or already verified.";
        } elseif (strtotime($dbExpiry) < time()) {
            $error = "Your OTP has expired. Please request a new one.";
        } elseif ($enteredOtp === strval($dbOtp)) {
            // ✅ update user status
            $update = $conn->prepare("UPDATE users SET status = 1, otp = NULL, otp_expiry = NULL WHERE email = ?");
            $update->bind_param("s", $email);
            $update->execute();
            $update->close();

            // ✅ mark as verified
            $_SESSION['verified'] = true;

            // clear session email so it doesn’t stick
            unset($_SESSION['email']);

            // ✅ redirect back to Registeration.php with success flag
            header("Location: Registeration.php?verified=1");
            exit();
        } else {
            $error = "Invalid OTP. Please try again.";
        }
    }
}


?>

<?php include("header.php"); ?>

<style>
/* Background image styling */
.hero-frame {
  position: relative;
  min-height: 100vh;
  background: url('images/bg.jpg') no-repeat center center fixed;
  background-size: cover;
  display: flex;
  align-items: center;
  justify-content: center;
}
.hero-bg {
  position: absolute;
  inset: 0;
  background: rgba(255,255,255,0.7); /* glass overlay */
}
.glass {
  position: relative;
  background: rgba(255, 255, 255, 0.85);
  border-radius: 16px;
  box-shadow: 0 4px 30px rgba(0,0,0,0.1);
  backdrop-filter: blur(8px);
}
</style>

<main class="w-full">
  <section class="hero-frame">
    <div class="hero-bg" aria-hidden="true"></div>

    <div class="relative screen-center px-4">
      <div class="glass w-full max-w-[420px] p-6">
        <h1 class="text-[22px] leading-7 text-gray-800 mb-1">
          Verify <span class="font-bold" style="color:var(--brand)">OTP</span>
        </h1>
        <p class="text-[12px] text-gray-500 mb-4">
          We sent a 6-digit code to 
          <span id="otp-email" class="font-medium text-gray-700">
              <?= htmlspecialchars($_SESSION['email']); ?>
          </span>.
        </p>

        <?php if($error): ?>
          <div id="otp-message" class="mt-4 p-3 rounded-md text-[13px] border border-red-200 bg-red-50 text-red-700">
              <?= htmlspecialchars($error) ?>
          </div>
        <?php else: ?>
          <div id="otp-message" class="hidden mt-4 p-3 rounded-md text-[13px]"></div>
        <?php endif; ?>

        <!-- Update Email (inline toggle) -->
        <div class="mb-4">
          <button id="toggle-email-edit" type="button" class="tiny-link underline">Update email</button>
          <form id="email-edit-form" class="hidden mt-2 space-x-2" action="#" method="post" onsubmit="return false;">
            <input id="new-email" type="email" class="input" placeholder="Enter new email" style="max-width: 240px;">
            <button type="button" id="save-email" class="btn btn-ghost">Resend</button>
          </form>
        </div>

        <!-- OTP Inputs -->
        <form class="space-y-4" action="" method="post" novalidate>
          <div class="flex items-center justify-between gap-2">
            <input class="otp input text-center !w-10 !px-0" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
            <input class="otp input text-center !w-10 !px-0" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
            <input class="otp input text-center !w-10 !px-0" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
            <input class="otp input text-center !w-10 !px-0" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
            <input class="otp input text-center !w-10 !px-0" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
            <input class="otp input text-center !w-10 !px-0" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
          </div>

          <div class="flex items-center gap-x-3 pt-1">
            <a href="/Dating_app/login.php" class="btn btn-ghost flex-1 text-center" id="back-btn">Back</a>
            <button type="button" class="btn btn-pink flex-1" id="verify-btn">Verify Code</button>
          </div>
        </form>

        <!-- Resend with countdown -->
        <div class="mt-4 text-center text-[12px] text-gray-600">
          <button id="resend-btn" class="tiny-link underline disabled:opacity-50" disabled>Resend code (30s)</button>
        </div>
      </div>
      <?php include __DIR__ . '/partials/footer-links.php'; ?>
    </div>
    
  </section>
</main>

<?php include("footer.php"); ?>

<script>
// Auto-advance OTP inputs
const inputs = Array.from(document.querySelectorAll('.otp'));
inputs.forEach((inp, idx) => {
  inp.addEventListener('input', (e) => {
    e.target.value = e.target.value.replace(/\D/g,'');
    if (e.target.value && idx < inputs.length - 1) inputs[idx + 1].focus();
  });
  inp.addEventListener('keydown', (e) => {
    if (e.key === 'Backspace' && !inp.value && idx > 0) inputs[idx - 1].focus();
  });
});

// Resend countdown
let remaining = 30;
const resendBtn = document.getElementById('resend-btn');
const tick = () => {
  if (remaining > 0) {
    resendBtn.textContent = `Resend code (${remaining--}s)`;
    resendBtn.disabled = true;
    setTimeout(tick, 1000);
  } else {
    resendBtn.textContent = 'Resend code';
    resendBtn.disabled = false;
  }
};
tick();
resendBtn.addEventListener('click', () => {
  remaining = 30;
  tick();
  showMsg('A new code was sent to your email.', true);
});

// Verify button
document.getElementById('verify-btn').addEventListener('click', () => {
  const code = inputs.map(i => i.value).join('');
  if (code.length === 6) {
    let hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = 'otp';
    hidden.value = code;
    document.querySelector('form').appendChild(hidden);
    document.querySelector('form').submit();
  } else {
    showMsg('Please enter all 6 digits.', false);
  }
});

// Update email UI
const toggleBtn = document.getElementById('toggle-email-edit');
const form = document.getElementById('email-edit-form');
const saveEmail = document.getElementById('save-email');
const displayEmail = document.getElementById('otp-email');
toggleBtn.addEventListener('click', () => form.classList.toggle('hidden'));
saveEmail.addEventListener('click', () => {
  const v = document.getElementById('new-email').value.trim();
  if (v) {
    displayEmail.textContent = v;
    form.classList.add('hidden');
    showMsg('Email updated (demo).', true);
  }
});

function showMsg(text, ok){
  const box = document.getElementById('otp-message');
  box.textContent = text;

  // Remove old classes
  box.classList.remove(
    'hidden',
    'border-red-200','text-red-700','bg-red-50',
    'border-green-200','text-green-700','bg-green-50'
  );

  // Add base border
  box.classList.add('border');

  // Add conditional classes
  if(ok){
    box.classList.add('border-green-200','text-green-700','bg-green-50');
  } else {
    box.classList.add('border-red-200','text-red-700','bg-red-50');
  }
}

resendBtn.addEventListener('click', () => {
  remaining = 30;
  tick();

  fetch('resend_otp.php', {method:'POST'})
    .then(res => res.json())
    .then(data => {
      if(data.success) showMsg(data.message,true);
      else showMsg(data.message,false);
    })
    .catch(()=> showMsg('Failed to resend OTP.', false));
});
saveEmail.addEventListener('click', () => {
  const newEmail = document.getElementById('new-email').value.trim();
  if (!newEmail) return showMsg('Please enter a valid email.', false);

  // Update email in session + database
  fetch('update_email.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({email: newEmail})
  })
  .then(res => res.json())
  .then(data => {
    if(data.success){
      document.getElementById('otp-email').textContent = newEmail;
      showMsg('Email updated.', true);

      // Resend OTP to new email
      return fetch('resend_otp.php', {method:'POST'});
    } else {
      throw new Error(data.message);
    }
  })
  .then(res => res.json())
  .then(data => {
    if(data.success) showMsg(data.message,true);
    else showMsg(data.message,false);
  })
  .catch(err => showMsg(err.message || 'Failed.', false));
});



</script>
