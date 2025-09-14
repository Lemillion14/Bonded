<?php include 'userheader.php'; ?>

<?php
// Bonded — static PHP landing page split for a PHP project
// Place this file at public/index.php and the stylesheet at assets/styles.css
?>
<aside class="sidebar">
  <div class="toggle-switch">
    <button class="toggle-button">Matches</button>
    <button class="toggle-button active">Conversations</button>
  </div>
</aside>

<section class="content-panel">
  <h1 class="content-title">Bonded</h1>

  <div class="profile-viewer">
    <div class="profile-placeholder left"></div>
    <div class="profile-placeholder right"></div>
  </div>

  <div class="action-buttons">
    <!-- Back -->
    <button class="action-button" type="button" aria-label="Back">
      <img src="./images/back.png"
        alt="Back icon" class="action-button-icon">
    </button>

    <!-- Pass -->
    <button class="action-button" type="button" aria-label="Pass">
      <img src="./images/cross.png"
        alt="Pass icon" class="action-button-icon">
    </button>

    <!-- Like -->
    <button class="action-button" type="button" aria-label="Like">
      <img src="./images/love-3.png"
        alt="Like icon" class="action-button-icon">
    </button>

    <!-- Favourite -->
    <button class="action-button" type="button" aria-label="Favourite">
      <img src="./images/favourite.png"
        alt="Favourite icon" class="action-button-icon">
    </button>
  </div>
</section>
</main>
</body>

</html>