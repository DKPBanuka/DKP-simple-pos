<?php
// login.php

// Include configuration and authentication functions
// config.php starts the session
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/modules/auth/functions.php'; // Needs functions like verify_user, is_logged_in

// Check if the user is already logged in. If so, redirect to the main page.
if (is_logged_in()) {
    header('Location: index.php'); // Redirect logged-in users to index.php
    exit;
}

$error_message = ''; // Initialize an empty error message variable

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic trimming of input data
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Basic validation: check if fields are empty
    if (empty($username) || empty($password)) {
        $error_message = 'කරුණාකර පරිශීලක නාමය සහ මුරපදය ඇතුළත් කරන්න.';
    } else {
        // Try to verify the user
        $verified_username = verify_user($username, $password);

        if ($verified_username !== false) {
            // Login successful!
            // Regenerate session ID for security
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $verified_username; // Store the verified username

            // Redirect to the main application page
            header('Location: index.php');
            exit;
        } else {
            // Login failed
            $error_message = 'පරිශීලක නාමය හෝ මුරපදය වැරදියි.';
        }
    }
}

// Include the header (HTML head, basic structure)
// We will create header.php next, for now assume it exists or structure manually
// If header.php doesn't exist yet, you might get an error or warning.
// For now, let's add basic HTML structure here. We'll integrate header.php properly later.

?>
<!DOCTYPE html>
<html lang="si"> <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>පිවිසීම - <?php echo SITE_TITLE; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Basic styling for the login form (can be moved to style.css) */
        body {
            font-family: sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .login-container h1 {
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
        }
        .login-form label {
            display: block;
            margin-bottom: 5px;
            text-align: left;
            color: #555;
            font-weight: bold;
        }
        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Include padding in width */
        }
        .login-form button {
            width: 100%;
            padding: 10px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .login-form button:hover {
            background-color: #4cae4c;
        }
        .error-message {
            color: #d9534f; /* Red color for errors */
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1><?php echo SITE_TITLE; ?> - පිවිසීම</h1>

        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form class="login-form" method="post" action="login.php">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <button type="submit">Login</button>
            </div>
        </form>
    </div>

    </body>
</html>