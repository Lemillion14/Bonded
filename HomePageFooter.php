<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('t')) {
    include_once 'translations.php';
}
?>
    <!-- Footer -->
    <footer id="footer" class="site-footer">
      <div class="container">
        <p class="footer-description">
          <?php echo t('footerDescription'); ?>
        </p>
        <hr style="margin-top: 20px; color: #000000;">

        <div class="footer-main">
          <div class="footer-column socials-column">
            <h4><?php echo t('Socials'); ?></h4>
            <div class="social-icons">
              <a href="#"><img src="images/tiktok.png" alt="TikTok"></a>
              <a href="#"><img src="images/instagram.png" alt="Instagram"></a>
              <a href="#"><img src="images/facebook.png" alt="Facebook"></a>
              <a href="#"><img src="images/youtube.png" alt="YouTube"></a>
            </div>
          </div>
          <div class="footer-column careers-column">
            <h4><?php echo t('Careers'); ?></h4>
            <a href="#"><?php echo t('Careers Portal'); ?></a>
            <a href="#"><?php echo t('Tech Blog'); ?></a>
          </div>
          <div class="footer-column app-column">
            <h4><?php echo t('Get the app!'); ?></h4>
          </div>
        </div>
        <hr style="margin-bottom: 20px; color: #000000;">

        <div class="footer-bottom">
          <div class="footer-legal">
            <a href="#"><?php echo t('FAQ'); ?></a> / 
            <a href="#"><?php echo t('Safety tips'); ?></a> / 
            <a href="#"><?php echo t('Terms'); ?></a> / 
            <a href="#"><?php echo t('Cookie Policy'); ?></a> / 
            <span><?php echo t('Privacy settings'); ?></span>
          </div>
          <p class="copyright">© 2025 ForU LLC, <?php echo t('All Rights Reserved'); ?>.</p>
        </div>
      </div>
    </footer>
  </div> <!-- end page-wrapper -->
</body>
</html>