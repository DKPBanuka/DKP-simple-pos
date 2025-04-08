<?php
// modules/data_handling/functions.php

// Ensure this file is included by the application, not accessed directly
if (!defined('APP_RUNNING')) {
    die("Access Denied."); // Stop execution if accessed directly
}

/**
 * Reads data from a JSON file.
 *
 * @param string $filepath The full path to the JSON file.
 * @return array An array of data, or an empty array if the file doesn't exist or is empty/invalid.
 */
function read_data(string $filepath): array {
    if (!file_exists($filepath)) {
        // If the file doesn't exist, return an empty array
        return [];
    }

    $json_data = file_get_contents($filepath);
    if ($json_data === false || $json_data === '') {
        // If reading failed or file is empty, return an empty array
        return [];
    }

    $data = json_decode($json_data, true); // Decode JSON into an associative array (true)

    if (json_last_error() !== JSON_ERROR_NONE) {
        // If JSON decoding failed, log the error (optional) and return empty array
        // error_log("JSON decode error in file: " . $filepath . " - Error: " . json_last_error_msg());
        return [];
    }

    // Ensure we always return an array, even if the JSON root was null or something else
    return is_array($data) ? $data : [];
}

/**
 * Writes data to a JSON file.
 * This function overwrites the existing file content.
 * Uses file locking to prevent race conditions during writes.
 *
 * @param string $filepath The full path to the JSON file.
 * @param array $data The PHP array data to write.
 * @return bool True on success, False on failure.
 */
function write_data(string $filepath, array $data): bool {
    // Encode the PHP array into a JSON string with pretty printing for readability
    $json_data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    if ($json_data === false) {
        // If encoding failed
        // error_log("JSON encode error for file: " . $filepath);
        return false;
    }

    // Attempt to open the file for writing ('w' creates the file if it doesn't exist,
    // and truncates it to zero length if it does exist)
    $handle = fopen($filepath, 'w');
    if (!$handle) {
        // error_log("Could not open file for writing: " . $filepath);
        return false; // Could not open the file
    }

    // Acquire an exclusive lock (prevents other processes from writing)
    if (flock($handle, LOCK_EX)) {
        // Write the JSON data
        $write_result = fwrite($handle, $json_data);

        // Release the lock
        flock($handle, LOCK_UN);

        // Close the file handle
        fclose($handle);

        // Check if write was successful (fwrite returns the number of bytes written or false)
        return $write_result !== false;
    } else {
        // Could not get a lock
        fclose($handle);
        // error_log("Could not acquire file lock for: " . $filepath);
        return false;
    }
}

?>