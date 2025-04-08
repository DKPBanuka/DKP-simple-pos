<?php
// manage_products.php (in root directory)

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/modules/auth/functions.php';
require_once __DIR__ . '/modules/products/functions.php';

// Require login for this page
require_login();

$page_title = 'Manage Products'; // For header.php
$error_message = '';
$success_message = '';

// --- Handle Form Submissions (POST requests) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Handle Add Product action
    if ($action === 'add') {
        $id = trim($_POST['product_id'] ?? '');
        $name = trim($_POST['product_name'] ?? '');
        $price_str = trim($_POST['product_price'] ?? '');
        $price = is_numeric($price_str) ? (float)$price_str : -1; // Validate price

        if (empty($id) || empty($name) || $price < 0) {
            $error_message = 'Please provide a valid ID, Name, and non-negative Price.';
        } elseif (get_product_by_id($id)) {
             $error_message = 'Error: Product ID already exists.';
        } else {
            if (add_product($id, $name, $price)) {
                $success_message = 'Product added successfully!';
            } else {
                $error_message = 'Error adding product. Could not write to data file or ID exists.';
            }
        }
    }
    // Handle Delete Product action
    elseif ($action === 'delete') {
        $id_to_delete = $_POST['product_id'] ?? '';
        if (!empty($id_to_delete)) {
            if (delete_product($id_to_delete)) {
                $success_message = 'Product deleted successfully!';
            } else {
                $error_message = 'Error deleting product. Could not write to data file.';
            }
        } else {
             $error_message = 'Error: Product ID missing for deletion.';
        }
    }
    // Add 'edit' action handling here later if needed
    // ...
}


// --- Fetch existing products for display ---
$products = get_products();

// --- Include Header ---
require_once __DIR__ . '/modules/ui/header.php';

?>

<?php if (!empty($success_message)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>
<?php if (!empty($error_message)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
<?php endif; ?>


<section class="add-product-form">
    <h2>Add New Product</h2>
    <form action="manage_products.php" method="post">
        <input type="hidden" name="action" value="add">
        <div>
            <label for="product_id">Product ID:</label>
            <input type="text" id="product_id" name="product_id" required>
        </div>
        <div>
            <label for="product_name">Product Name:</label>
            <input type="text" id="product_name" name="product_name" required>
        </div>
        <div>
            <label for="product_price">Price:</label>
            <input type="number" id="product_price" name="product_price" step="0.01" min="0" required>
        </div>
        <button type="submit">Add Product</button>
    </form>
</section>

<hr>

<section class="product-list">
    <h2>Existing Products</h2>
    <?php if (empty($products)): ?>
        <p>No products found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['id'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($product['name'] ?? 'N/A'); ?></td>
                    <td><?php echo number_format((float)($product['price'] ?? 0), 2); ?></td>
                    <td>
                        <form action="manage_products.php" method="post" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id'] ?? ''); ?>">
                            <button type="submit" class="button-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>
                        </form>
                        </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>


<?php
// --- Include Footer ---
require_once __DIR__ . '/modules/ui/footer.php';
?>