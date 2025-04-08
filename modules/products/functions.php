<?php
// modules/products/functions.php

if (!defined('APP_RUNNING')) { die("Access Denied."); }

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../data_handling/functions.php';

/**
 * Retrieves all products from the data file.
 *
 * @return array List of products.
 */
function get_products(): array {
    return read_data(PRODUCTS_FILE);
}

/**
 * Retrieves a single product by its ID.
 *
 * @param string $id The ID of the product to find.
 * @return array|null The product array if found, otherwise null.
 */
function get_product_by_id(string $id): ?array {
    $products = get_products();
    foreach ($products as $product) {
        // Ensure 'id' key exists before comparing
        if (isset($product['id']) && $product['id'] === $id) {
            return $product;
        }
    }
    return null; // Not found
}

/**
 * Adds a new product to the data file.
 *
 * @param string $id Unique ID for the new product.
 * @param string $name Name of the product.
 * @param float $price Price of the product.
 * @return bool True on success, false if ID already exists or write fails.
 */
function add_product(string $id, string $name, float $price): bool {
    if (empty($id) || empty($name) || $price < 0) {
        return false; // Basic validation failed
    }

    $products = get_products();

    // Check if product ID already exists
    foreach ($products as $product) {
        if (isset($product['id']) && $product['id'] === $id) {
            return false; // ID already exists
        }
    }

    // Create new product array
    $new_product = [
        'id' => $id,
        'name' => $name,
        'price' => $price
        // Add other fields like 'stock' or 'category' here if needed
    ];

    // Add the new product to the list
    $products[] = $new_product;

    // Write the updated list back to the file
    return write_data(PRODUCTS_FILE, $products);
}

/**
 * Updates an existing product.
 *
 * @param string $id The ID of the product to update.
 * @param string $name The new name.
 * @param float $price The new price.
 * @return bool True on success, false if product not found or write fails.
 */
function update_product(string $id, string $name, float $price): bool {
     if (empty($id) || empty($name) || $price < 0) {
        return false; // Basic validation failed
    }

    $products = get_products();
    $found_index = -1;

    // Find the index of the product to update
    foreach ($products as $index => $product) {
        if (isset($product['id']) && $product['id'] === $id) {
            $found_index = $index;
            break;
        }
    }

    if ($found_index !== -1) {
        // Update the product details
        $products[$found_index]['name'] = $name;
        $products[$found_index]['price'] = $price;
        // Update other fields if they exist

        // Write the updated list back
        return write_data(PRODUCTS_FILE, $products);
    }

    return false; // Product not found
}

/**
 * Deletes a product by its ID.
 *
 * @param string $id The ID of the product to delete.
 * @return bool True on success, false if write fails. Returns true even if ID wasn't found (idempotent).
 */
function delete_product(string $id): bool {
    $products = get_products();

    // Filter out the product with the matching ID
    // array_values re-indexes the array numerically after filtering
    $updated_products = array_values(array_filter($products, function($product) use ($id) {
        return !isset($product['id']) || $product['id'] !== $id;
    }));

    // Write the potentially modified list back (even if the ID wasn't found)
    return write_data(PRODUCTS_FILE, $updated_products);
}

?>