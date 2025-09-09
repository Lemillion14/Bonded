<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('t')) {
    include_once 'translations.php';
}

// Language settings
$available_languages = ['en' => 'English', 'es' => 'Español', 'fr' => 'Français', 'de' => 'Deutsch'];
$default_language = 'en';

// Get current language from session, cookie, or default
if (isset($_GET['lang']) && array_key_exists($_GET['lang'], $available_languages)) {
    // Language change requested
    $current_language = $_GET['lang'];
    $_SESSION['language'] = $current_language;
    setcookie('language', $current_language, time() + (30 * 24 * 60 * 60), '/');
} elseif (isset($_SESSION['language'])) {
    // Language set in session
    $current_language = $_SESSION['language'];
} elseif (isset($_COOKIE['language']) && array_key_exists($_COOKIE['language'], $available_languages)) {
    // Language set in cookie
    $current_language = $_COOKIE['language'];
    $_SESSION['language'] = $current_language;
} else {
    // Default language
    $current_language = $default_language;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('siteName'); ?></title>
    <link rel="stylesheet" href="pxcode.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="page-wrapper">
        <div class="background-image-container">
            <img src="./images/bg.png" alt="Abstract background" class="background-image">
        </div>

        <!-- Header -->
        <header id="header" class="site-header">
            <a href="#" class="logo"><?php echo t('siteName'); ?></a>
            <nav class="main-nav">
                <a href="#features"><?php echo t('Subscriptions'); ?></a>
                <a href="#events"><?php echo t('Learn'); ?></a>
                <a href="#"><?php echo t('Support'); ?></a>
                <a href="#"><?php echo t('Download'); ?></a>
            </nav>
            <div class="header-actions">
                <div class="language-switcher">
                    <button class="language-btn">
                        <img src="images/globe.png" alt="Globe icon" class="lang-icon globe">
                        <img src="images/down.png" alt="Dropdown arrow" class="lang-icon arrow">
                    </button>
                    
                    <div class="language-options">
                        <?php 
                        $available_languages = ['en' => 'English', 'es' => 'Español', 'fr' => 'Français', 'de' => 'Deutsch'];
                        $current_language = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
                        foreach ($available_languages as $code => $name): ?>
                            <a href="?lang=<?php echo $code; ?>" class="language-option <?php echo $code == $current_language ? 'active' : ''; ?>" data-lang="<?php echo $code; ?>">
                                <?php echo $name; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <a href="#" class="btn btn-secondary login-btn"><?php echo t('Log in'); ?></a>
            </div>
        </header>
        <script>
document.addEventListener('DOMContentLoaded', function() {
    const languageBtn = document.querySelector('.language-btn');
    const languageOptions = document.querySelector('.language-options');
    
    // Toggle language options visibility
    if (languageBtn) {
        languageBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            languageOptions.classList.toggle('active');
        });
    }
    
    // Close language options when clicking outside
    document.addEventListener('click', function(e) {
        if (languageOptions && !languageOptions.contains(e.target) && e.target !== languageBtn) {
            languageOptions.classList.remove('active');
        }
    });
});
</script>