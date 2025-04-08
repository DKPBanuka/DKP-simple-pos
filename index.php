<?php
// index.php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/modules/auth/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    // If not logged in, redirect to login page
    header('Location: login.php');
    exit;
} else {
    // If logged in, redirect to the main application page (e.g., billing)
    header('Location: billing.php');
    exit;
}

// No HTML needed here as it always redirects.
?>