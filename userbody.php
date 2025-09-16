<?php include 'userheader.php'; ?>

<?php
// Database connection
$host = '127.0.0.1';
$db   = 'dating_project';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Get a random user to display
$stmt = $pdo->query("
    SELECT u.*, up.bio, up.sexual_orientation, up.relationship_goals,
           lh.drinking, lh.smoking, lh.exercise, lh.pets,
           ui.communication, ui.love_languages, ui.education, ui.star_sign
    FROM users u
    LEFT JOIN user_profile up ON u.id = up.user_id
    LEFT JOIN lifestyle_habits lh ON u.id = lh.user_id
    LEFT JOIN user_identity ui ON u.id = ui.user_id
    WHERE u.id != 17  -- Exclude current user (example)
    ORDER BY RAND()
    LIMIT 1
");

$user = $stmt->fetch();

if ($user) {
    // Get user interests
    $stmt = $pdo->prepare("
        SELECT i.name 
        FROM user_interests ui 
        JOIN interests i ON ui.interest_id = i.id 
        WHERE ui.user_id = ?
        LIMIT 5
    ");
    $stmt->execute([$user['id']]);
    $interests = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Get user photos
    $stmt = $pdo->prepare("SELECT url FROM user_photos WHERE user_id = ? ORDER BY position");
    $stmt->execute([$user['id']]);
    $photos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Calculate age from date of birth
    $dob = new DateTime($user['dob']);
    $today = new DateTime();
    $age = $today->diff($dob)->y;
}
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
    <?php if ($user): ?>
    <!-- Left square: User photo -->
<div class="profile-placeholder left">
  <div class="profile-photo-container">
    <?php if (!empty($photos)): ?>
      <img src="<?= htmlspecialchars($photos[0]) ?>" alt="Profile photo" class="profile-photo">
    <?php else: ?>
      <div class="no-photo">No photo available</div>
    <?php endif; ?>
  </div>
</div>
    
    <!-- Right square: User details -->
    <div class="profile-placeholder right">
      <div class="user-details">
        <h2><?= htmlspecialchars($user['first_name']) ?>, <?= $age ?></h2>
        <?php if ($user['show_gender']): ?>
          <p class="gender"><?= htmlspecialchars($user['gender']) ?></p>
        <?php endif; ?>
        
        <div class="bio-section">
          <p class="bio"><?= htmlspecialchars($user['bio'] ?? 'No bio available') ?></p>
        </div>
        
        <?php if (!empty($interests)): ?>
          <div class="interests">
            <h3>Interests</h3>
            <div class="interest-tags">
              <?php foreach ($interests as $interest): ?>
                <span class="interest-tag"><?= htmlspecialchars($interest) ?></span>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
        
        <div class="lifestyle">
          <h3>Lifestyle</h3>
          <div class="lifestyle-details">
            <?php if ($user['drinking']): ?><span>🍷 <?= htmlspecialchars($user['drinking']) ?></span><?php endif; ?>
            <?php if ($user['smoking']): ?><span>🚬 <?= htmlspecialchars($user['smoking']) ?></span><?php endif; ?>
            <?php if ($user['exercise']): ?><span>💪 <?= htmlspecialchars($user['exercise']) ?></span><?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <?php else: ?>
      <div class="profile-placeholder left"></div>
      <div class="profile-placeholder right">
        <p>No profiles found</p>
      </div>
    <?php endif; ?>
  </div>

  <div class="action-buttons">
    <!-- Back -->
    <button class="action-button" type="button" aria-label="Back">
      <img src="./images/back.png" alt="Back icon" class="action-button-icon">
    </button>

    <!-- Pass -->
    <button class="action-button" type="button" aria-label="Pass">
      <img src="./images/cross.png" alt="Pass icon" class="action-button-icon">
    </button>

    <!-- Like -->
    <button class="action-button" type="button" aria-label="Like">
      <img src="./images/love-3.png" alt="Like icon" class="action-button-icon">
    </button>

    <!-- Favourite -->
    <button class="action-button" type="button" aria-label="Favourite">
      <img src="./images/favourite.png" alt="Favourite icon" class="action-button-icon">
    </button>
  </div>
</section>

</main>
</body>
</html>