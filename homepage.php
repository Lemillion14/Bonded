<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include translations
include_once 'translations.php';
?>
<?php include 'header.php'; ?>

<!-- Hero Section -->
<section id="hero" class="hero-section">
  <div class="container" style="padding-top: 250px;">
    <h1 class="hero-title"><?php echo t('Connect With People.'); ?></h1>
    <a href="#" class="btn btn-primary create-account-btn"><?php echo t('Create account'); ?></a>
  </div>
</section>

<!-- Testimonials Section -->
<section id="testimonials" class="testimonials-section">
    <div class="container">
        <div class="testimonials-grid">

            <article class="testimonial-card">
                <div class="testimonial-avatar">
                    <img src="images/couple.png" alt="Avatar of Oak & Thet">
                </div>
                <h2 class="testimonial-name">Oak & Thet</h2>
                <p class="testimonial-text"><?php echo t('testimonial1'); ?></p>
                <a href="#" class="read-more"><?php echo t('Continue Reading'); ?> <img src="images/right-arrow.png" alt="Arrow right"></a>
            </article>

            <article class="testimonial-card">
                <div class="testimonial-avatar">
                    <img src="images/couple.png" alt="Avatar of Naing & Kay">
                </div>
                <h2 class="testimonial-name">Naing & Kay</h2>
                <p class="testimonial-text"><?php echo t('testimonial2'); ?></p>
                <a href="#" class="read-more"><?php echo t('Continue Reading'); ?> <img src="images/right-arrow.png" alt="Arrow right"></a>
            </article>

            <article class="testimonial-card">
                <div class="testimonial-avatar">
                    <img src="images/couple.png" alt="Avatar of Thaw & Moe">
                </div>
                <h2 class="testimonial-name">Thaw & Moe</h2>
                <p class="testimonial-text"><?php echo t('testimonial3'); ?></p>
                <a href="#" class="read-more"><?php echo t('Continue Reading'); ?> <img src="images/right-arrow.png" alt="Arrow right"></a>
            </article>

        </div>
    </div>
</section>

<!-- Events Section -->
<section id="events" class="events-section">
    <div class="container">
        <div class="glass-card event-card-content">
            <div class="event-image" style="margin-left: 20px;">
                <img src="images/0d99723e1749dd4630aad5bb6b012fee.png" alt="A couple looking at each other affectionately">
            </div>
            <div class="event-text">
                <h2 class="event-title" style="padding-left: 60px;"><?php echo t('Start the chat in person'); ?></h2>
                <p class="event-description" style="padding-left: 60px;"><?php echo t('eventDescription'); ?></p>
                <a href="#" class="btn btn-secondary" style="font-size: 15px; margin-left: 60px;"><?php echo t('Meet in person'); ?></a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="features-section">
    <div class="container">
        <div class="features-grid">
            <div class="glass-card feature-card">
                <h3><?php echo t('ForU Date'); ?></h3>
                <p><?php echo t("feature1Description"); ?></p>
                <a href="#" class="feature-link"><?php echo t('Find your person'); ?></a>
            </div>
            <div class="glass-card feature-card">
                <h3><?php echo t('BFF'); ?></h3>
                <p><?php echo t('feature2Description'); ?></p>
                <a href="#" class="feature-link"><?php echo t('Find your people'); ?></a>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>