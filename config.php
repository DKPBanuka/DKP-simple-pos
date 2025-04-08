<?php
// config.php

// --- Basic Settings ---
define('SITE_TITLE', 'Simple Grocery POS');

// --- Paths ---
// Define the root directory of the application
define('APP_ROOT', __DIR__); // Gets the directory where config.php resides

// Define the path to the data directory relative to the app root
define('DATA_DIR', APP_ROOT . '/data');

// Define paths to data files
define('PRODUCTS_FILE', DATA_DIR . '/products.json');
define('SALES_FILE', DATA_DIR . '/sales.json');
define('USERS_FILE', DATA_DIR . '/users.json');

// --- Session Management ---
// Set session cookie parameters (optional but good practice)
// session_set_cookie_params([
//     'lifetime' => 3600, // 1 hour
//     'path' => '/',
//     'domain' => '', // Set your domain if needed
//     'secure' => isset($_SERVER['HTTPS']), // Send only over HTTPS
//     'httponly' => true, // Prevent JS access to cookie
//     'samesite' => 'Lax' // CSRF protection
// ]);

// Start the session if it hasn't been started already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- Error Reporting (Development vs Production) ---
// For development: show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
// For production: log errors, don't display them
// error_reporting(E_ALL);
// ini_set('display_errors', 0);
// ini_set('log_errors', 1);
// ini_set('error_log', APP_ROOT . '/error.log'); // Ensure this file is writable by the server

// --- Timezone ---
date_default_timezone_set('Asia/Colombo'); // Set to your desired timezone

// --- Security ---
// A simple constant to check if core files are included correctly
define('APP_RUNNING', true);

?>