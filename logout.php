<?php
// logout.php

require_once __DIR__ . '/config.php'; // Needs config to start session if not already started
require_once __DIR__ . '/modules/auth/functions.php'; // Needs logout_user function

// Call the logout function which handles session destruction
logout_user();

// Redirect the user to the login page after logout
header('Location: login.php');
exit;

// No HTML output needed.
?>