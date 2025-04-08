<?php
// billing.php (in root directory)

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/modules/auth/functions.php';
require_once __DIR__ . '/modules/products/functions.php';
require_once __DIR__ . '/modules/sales/functions.php';

// Require login for this page
require_login();

$page_title = 'Billing / New Sale'; // For header.php
$error_message = '';
$success_message = '';
$current_user = get_current_user(); // Get username for recording sale

// --- Initialize or retrieve the cart from session ---
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // Initialize empty cart array
}
$cart = &$_SESSION['cart']; // Use reference for easier modification

// --- Handle Cart Actions (POST/GET requests) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {

    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    $product_id = $_POST['product_id'] ?? $_GET['product_id'] ?? '';

    // --- Add Item to Cart ---
    if ($action === 'add' && !empty($product_id)) {
        $product = get_product_by_id($product_id);
        if ($product) {
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            if ($quantity <= 0) $quantity = 1; // Ensure positive quantity

            // Check if item already in cart
            if (isset($cart[$product_id])) {
                // Update quantity
                $cart[$product_id]['quantity'] += $quantity;
            } else {
                // Add new item
                $cart[$product_id] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => (float)$product['price'],
                    'quantity' => $quantity
                ];
            }
            $success_message = htmlspecialchars($product['name']) . " added to cart.";
            // Redirect to avoid form resubmission on refresh (optional but good practice)
            header("Location: billing.php?success=1"); // Add success flag
            exit;
        } else {
            $error_message = "Product with ID " . htmlspecialchars($product_id) . " not found.";
            header("Location: billing.php?error=1"); // Add error flag
            exit;
        }
    }

    // --- Update Cart Item Quantity ---
    elseif ($action === 'update' && !empty($product_id)) {
         $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
         if (isset($cart[$product_id])) {
            if ($quantity > 0) {
                $cart[$product_id]['quantity'] = $quantity;
                $success_message = "Cart updated.";
                header("Location: billing.php?success=2");
                exit;
            } else {
                // If quantity is 0 or less, remove the item
                unset($cart[$product_id]);
                $success_message = "Item removed from cart.";
                 header("Location: billing.php?success=3");
                 exit;
            }
         }
    }

    // --- Remove Item from Cart ---
     elseif ($action === 'remove' && !empty($product_id)) {
        if (isset($cart[$product_id])) {
            unset($cart[$product_id]);
            $success_message = "Item removed from cart.";
            header("Location: billing.php?success=3");
            exit;
        }
    }

    // --- Clear Cart ---
    elseif ($action === 'clear') {
        $cart = []; // Reset cart session variable
        $success_message = "Cart cleared.";
        header("Location: billing.php?success=4");
        exit;
    }

    // --- Finalize Sale ---
    elseif ($action === 'finalize') {
        if (empty($cart)) {
            $error_message = "Cannot finalize empty cart.";
             header("Location: billing.php?error=2");
             exit;
        } else {
            $total_amount = 0;
            $sale_items = [];
            foreach ($cart as $item) {
                 $item_total = $item['price'] * $item['quantity'];
                 $total_amount += $item_total;
                 $sale_items[] = $item; // Prepare items array for saving
            }

            if (add_sale($sale_items, $total_amount, $current_user)) {
                // Clear the cart after successful sale
                $cart = [];
                $_SESSION['last_sale_total'] = $total_amount; // Store total for success message
                // Redirect to prevent resubmission, possibly show success
                header("Location: billing.php?finalized=1");
                exit;
            } else {
                $error_message = "Error recording sale. Could not write to data file.";
                 header("Location: billing.php?error=3");
                 exit;
            }
        }
    }
}

// Check for messages passed via GET parameters after redirect
if(isset($_GET['success'])) $success_message = "Action successful!"; // Generic success
if(isset($_GET['error'])) $error_message = "An error occurred."; // Generic error
if(isset($_GET['finalized'])) {
    $last_total = $_SESSION['last_sale_total'] ?? 0;
    $success_message = "Sale finalized successfully! Total: " . number_format($last_total, 2);
    unset($_SESSION['last_sale_total']);
}


// --- Fetch products for the dropdown/selection ---
$products = get_products();

// --- Calculate Cart Total ---
$cart_total = 0;
foreach ($cart as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}

// --- Include Header ---
require_once __DIR__ . '/modules/ui/header.php';
?>

<?php if (!empty($success_message)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>
<?php if (!empty($error_message)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
<?php endif; ?>

<div class="billing-layout">

    <section class="product-selection">
        <h2>Select Products</h2>
        <form action="billing.php" method="post" class="add-item-form">
            <input type="hidden" name="action" value="add">
            <div>
                <label for="product_id">Product:</label>
                <select id="product_id" name="product_id" required>
                    <option value="">-- Select Product --</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo htmlspecialchars($product['id'] ?? ''); ?>">
                            <?php echo htmlspecialchars($product['name'] ?? 'N/A'); ?>
                            (<?php echo number_format((float)($product['price'] ?? 0), 2); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" value="1" min="1" required>
            </div>
            <button type="submit">Add to Cart</button>
        </form>
    </section>

    <section class="current-cart">
        <h2>Current Sale Cart</h2>
        <?php if (empty($cart)): ?>
            <p>Cart is empty.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $item_id => $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <form action="billing.php" method="post" style="display:inline;">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item_id); ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="0" style="width: 60px;" required>
                                <button type="submit" class="button-small">Update</button>
                            </form>
                        </td>
                        <td><?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        <td>
                            <form action="billing.php" method="post" style="display:inline;">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item_id); ?>">
                                <button type="submit" class="button-danger button-small">Remove</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" style="text-align:right;">Total:</th>
                        <th><?php echo number_format($cart_total, 2); ?></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>

            <div class="cart-actions">
                <form action="billing.php" method="post" style="display:inline;">
                     <input type="hidden" name="action" value="clear">
                     <button type="submit" class="button-danger" onclick="return confirm('Are you sure you want to clear the entire cart?');">Clear Cart</button>
                 </form>
                 <form action="billing.php" method="post" style="display:inline; margin-left: 10px;">
                     <input type="hidden" name="action" value="finalize">
                     <button type="submit" class="button-primary" onclick="return confirm('Finalize this sale?');">Finalize Sale</button>
                 </form>
            </div>

        <?php endif; ?>
    </section>

</div> <?php
// --- Include Footer ---
require_once __DIR__ . '/modules/ui/footer.php';
?>