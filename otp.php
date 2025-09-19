<?php /* otp.php (UI only) */ ?>
<?php include("header.php"); ?>

<main class="w-full">
  <section class="hero-frame">
    <div class="hero-bg" aria-hidden="true"></div>

    <div class="relative screen-center px-4">
      <div class="glass w-full max-w-[420px] p-6">
        <h1 class="text-[22px] leading-7 text-gray-800 mb-1">
          Verify <span class="font-bold" style="color:var(--brand)">OTP</span>
        </h1>
        <p class="text-[12px] text-gray-500 mb-4">
          We sent a 6‑digit code to <span id="otp-email" class="font-medium text-gray-700">example@gmail.com</span>.
        </p>

        <!-- Update Email (inline toggle) -->
        <div class="mb-4">
          <button id="toggle-email-edit" type="button" class="tiny-link underline">Update email</button>
          <form id="email-edit-form" class="hidden mt-2 space-x-2" action="#" method="post" onsubmit="return false;">
            <input id="new-email" type="email" class="input" placeholder="Enter new email" style="max-width: 240px;">
            <button type="button" id="save-email" class="btn btn-ghost">Resend</button>
            <!-- <button type="button" id="cancel-email" class="btn btn-ghost">x</button> -->
          </form>
        </div>

        <!-- OTP Inputs -->
        <form class="space-y-4" action="#" method="post" novalidate onsubmit="return false;">
          <div class="flex items-center justify-between gap-2">
            <input class="otp input text-center !w-10 !px-0" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
            <input class="otp input text-center !w-10 !px-0" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
            <input class="otp input text-center !w-10 !px-0" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
            <input class="otp input text-center !w-10 !px-0" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
            <input class="otp input text-center !w-10 !px-0" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
            <input class="otp input text-center !w-10 !px-0" maxlength="1" inputmode="numeric" pattern="[0-9]*" />
          </div>

          <div class="flex items-center gap-x-3 pt-1">
            <a href="/Dating_app/index.php" class="btn btn-ghost flex-1 text-center" id="back-btn">Back</a>
            <button type="button" class="btn btn-pink flex-1" id="verify-btn">Verify Code</button>
          </div>
        </form>

        <!-- Resend with countdown -->
        <div class="mt-4 text-center text-[12px] text-gray-600">
          <button id="resend-btn" class="tiny-link underline disabled:opacity-50" disabled>Resend code (30s)</button>
        </div>

        <!-- Fake success/error states -->
        <div id="otp-message" class="hidden mt-4 p-3 rounded-md text-[13px]"></div>
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

// Verify button (UI only)
document.getElementById('verify-btn').addEventListener('click', () => {
  const code = inputs.map(i => i.value).join('');
  if (code.length === 6) {
    showMsg('Code verified (demo). Proceeding...', true);
  } else {
    showMsg('Please enter all 6 digits.', false);
  }
});

// Update email UI
const toggleBtn = document.getElementById('toggle-email-edit');
const form = document.getElementById('email-edit-form');
// const cancelEmail = document.getElementById('cancel-email');
const saveEmail = document.getElementById('save-email');
const displayEmail = document.getElementById('otp-email');
toggleBtn.addEventListener('click', () => {
  form.classList.toggle('hidden');
});
// cancelEmail.addEventListener('click', () => {
//   form.classList.add('hidden');
// });
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
  box.classList.remove('hidden');
  box.classList.remove('border-red-200','text-red-700','bg-red-50','border-green-200','text-green-700','bg-green-50');
  if(ok){
    box.classList.add('border-green-200','text-green-700','bg-green-50');
  }else{
    box.classList.add('border-red-200','text-red-700','bg-red-50');
  }
  box.classList.add('border');
}
</script>
