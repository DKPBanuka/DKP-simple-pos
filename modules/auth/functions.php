<?php
// modules/auth/functions.php

// Ensure this file is included by the application, not accessed directly
if (!defined('APP_RUNNING')) {
    die("Access Denied.");
}

// Include necessary files. Ensure config is included first if its constants are needed.
// Assuming data_handling functions might use constants from config.php indirectly via file paths.
require_once __DIR__ . '/../../config.php'; // Go up two directories to reach config.php
require_once __DIR__ . '/../data_handling/functions.php'; // Go up one directory then into data_handling

/**
 * Verifies user credentials against the stored user data.
 * Assumes passwords in users.json are hashed using password_hash().
 *
 * @param string $username The username entered by the user.
 * @param string $password The password entered by the user.
 * @return string|false The username if credentials are valid, otherwise false.
 */
function verify_user(string $username, string $password) {
    $users = read_data(USERS_FILE); // Read users from users.json

    if (empty($users)) {
        // error_log("User data file is empty or could not be read: " . USERS_FILE);
        return false; // No users found or error reading file
    }

    foreach ($users as $user) {
        // Check if username exists and matches (case-sensitive)
        if (isset($user['username']) && $user['username'] === $username) {
            // Verify the provided password against the stored hash
            if (isset($user['password_hash']) && password_verify($password, $user['password_hash'])) {
                // Password is correct
                return $user['username']; // Return the username on successful verification
            } else {
                // Found username, but password doesn't match
                return false;
            }
        }
    }

    // Username not found in the loop
    return false;
}

/**
 * Checks if a user is currently logged in by checking session variables.
 *
 * @return bool True if the user is logged in, false otherwise.
 */
function is_logged_in(): bool {
    // Check if the session variable 'logged_in' is set and is true
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * If the user is not logged in, redirects them to the login page and stops script execution.
 * This function should be called at the beginning of pages that require authentication.
 */
function require_login(): void {
    if (!is_logged_in()) {
        // If not logged in, redirect to login.php
        // Make sure no output has been sent before this header call
        header('Location: login.php');
        exit; // Stop script execution immediately after redirection
    }
}

/**
 * Logs the user out by destroying the session.
 */
function logout_user(): void {
    // Unset all session variables
    $_SESSION = [];

    // If using session cookies, delete the cookie as well
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finally, destroy the session.
    session_destroy();
}



?>