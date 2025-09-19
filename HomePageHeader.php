<?php /* header.php */ ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>BONDED – Auth</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- Your custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="antialiased">

  <!-- Header -->
  <header class="w-full">
    <nav class="mx-6 my-1 max-w-6xl px-6 md:px-4 flex items-center justify-between">
      <a href="/Dating_app/" class="logo-text select-none">BONDED</a>
      <ul class="site-nav hidden md:flex items-center gap-6 text-[16px]">
        <li><a href="/Dating_app/" class="transition-colors nav">Home</a></li>
        <li><a href="#" class="transition-colors nav">Download</a></li>
        <li><a href="#" class="transition-colors nav">About</a></li>
        <li><a href="#" class="transition-colors nav">Contact</a></li>
        <li class="ml-6">
          <!-- Login button -->
          <button onclick="window.location.href='/Dating_Web/login.php'" 
                  class="btn btn-ghost py-2 px-3">
            Login
          </button>
        </li>
        <li>
          <!-- Register button -->
          <button onclick="window.location.href='/Dating_Web/Registeration.php'" 
                  class="btn btn-pink py-2 px-3">
            Register
          </button>
        </li>
      </ul>
    </nav>
  </header>
