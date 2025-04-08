<?php
// modules/ui/footer.php

// Ensure this file is included by the application, not accessed directly
if (!defined('APP_RUNNING')) {
    // echo "Access Denied."; // Avoid outputting anything here ideally.
    // exit;
}

// Get current year for copyright
$current_year = date('Y');
$site_title_display = defined('SITE_TITLE') ? htmlspecialchars(SITE_TITLE) : 'POS System';

?>

    </main> <footer class="site-footer">
        <div class="container">
            <p>&copy; <?php echo $current_year; ?> <?php echo $site_title_display; ?>. All Rights Reserved.</p>
            </div>
    </footer>

    <script src="assets/js/app.js"></script>
    </body>
</html>