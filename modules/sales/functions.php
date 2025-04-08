<?php
// modules/sales/functions.php

if (!defined('APP_RUNNING')) { die("Access Denied."); }

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../data_handling/functions.php';

/**
 * Retrieves all sales records from the data file.
 * Sorts sales by timestamp descending (newest first).
 *
 * @return array List of sales records.
 */
function get_sales(): array {
    $sales = read_data(SALES_FILE);
    // Sort sales by timestamp, newest first
    usort($sales, function($a, $b) {
        return ($b['timestamp'] ?? 0) <=> ($a['timestamp'] ?? 0);
    });
    return $sales;
}

/**
 * Adds a new sale record to the data file.
 *
 * @param array $items An array of items sold (e.g., [['id' => 'P1', 'name' => 'Apple', 'price' => 0.5, 'quantity' => 2], ...]).
 * @param float $total_amount The total amount for the sale.
 * @param string $sold_by The username of the cashier/user who made the sale.
 * @return bool True on success, false on failure.
 */
function add_sale(array $items, float $total_amount, string $sold_by): bool {
    if (empty($items) || $total_amount < 0) {
        return false; // Cannot record empty or negative sale
    }

    $sales = read_data(SALES_FILE); // Read existing sales

    // Generate a unique sale ID (using timestamp and a random element)
    $sale_id = time() . '-' . bin2hex(random_bytes(4));

    // Get current timestamp
    $timestamp = time(); // Unix timestamp
    $datetime_string = date('Y-m-d H:i:s', $timestamp); // Human-readable date

    // Create the new sale record
    $new_sale = [
        'sale_id' => $sale_id,
        'timestamp' => $timestamp,
        'datetime' => $datetime_string,
        'items' => $items, // Array of items with id, name, price, quantity
        'total_amount' => $total_amount,
        'sold_by' => $sold_by
    ];

    // Add the new sale to the beginning of the array (optional, could just append)
    // array_unshift($sales, $new_sale); // Add to beginning
    $sales[] = $new_sale; // Add to end (simpler)


    // Write the updated sales list back to the file
    return write_data(SALES_FILE, $sales);
}

?>