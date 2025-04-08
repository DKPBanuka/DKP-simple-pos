<?php
// modules/ui/header.php

// Ensure this file is included by the application, not accessed directly
// Although less critical for header/footer, it's good practice.
if (!defined('APP_RUNNING')) {
    // Note: Depending on include order, config.php might not be loaded yet.
    // So we might not have access to constants here if accessed directly.
    // It's primarily the calling script's job to prevent direct access.
    // echo "Access Denied."; // Avoid outputting anything here ideally.
    // exit;
}

// Assume config.php (for SITE_TITLE) and modules/auth/functions.php (for is_logged_in, get_current_user)
// have already been included by the script that includes this header.

// Get the title for the page. The including script should define $page_title before including this header.
$page_title_display = isset($page_title) ? htmlspecialchars($page_title) . ' - ' : '';
$site_title_display = defined('SITE_TITLE') ? htmlspecialchars(SITE_TITLE) : 'POS System';

?>
<!DOCTYPE html>
<html lang="si"> <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title_display . $site_title_display; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Basic Navigation Bar Styling (move to style.css later) */
        .navbar {
            background-color: #333;
            overflow: hidden;
            padding: 10px 0;
        }
        .navbar ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex; /* Use flexbox for horizontal layout */
            justify-content: center; /* Center items */
        }
         .navbar ul li {
            /* float: left; remove float when using flexbox */
            margin: 0 15px; /* Spacing between items */
         }
        .navbar ul li a {
            display: block;
            color: white;
            text-align: center;
            padding: 10px 12px;
            text-decoration: none;
            border-radius: 4px;
        }
        .navbar ul li a:hover {
            background-color: #555;
        }
        .navbar .user-info {
            float: right; /* Keep user info to the right */
            color: #f2f2f2;
            padding: 10px 20px;
            font-size: 0.9em;
        }
         /* Clearfix for float */
        .navbar::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>

<header>
    <nav class="navbar">
        <ul>
            <?php if (function_exists('is_logged_in') && is_logged_in()): ?>
                <li><a href="billing.php">බිල්පත් (Billing)</a></li>
                <li><a href="manage_products.php">භාණ්ඩ කළමනාකරණය (Products)</a></li>
                <li><a href="sales_history.php">විකුණුම් ඉතිහාසය (Sales History)</a></li>
                <li><a href="logout.php">ඉවත්වන්න (Logout)</a></li>
            <?php else: ?>
                <li><a href="login.php">පිවිසීම (Login)</a></li>
            <?php endif; ?>
        </ul>
         <?php if (function_exists('is_logged_in') && is_logged_in() && function_exists('get_current_user')): ?>
            <div class="user-info">
                ලොග් වී ඇත්තේ: <?php echo htmlspecialchars(get_current_user()); ?>
            </div>
        <?php endif; ?>
    </nav>
</header>

<main class="container">
    <?php
    // Display a page title heading if $page_title is set
    if (isset($page_title)) {
        echo "<h1>" . htmlspecialchars($page_title) . "</h1>";
    }
    ?>