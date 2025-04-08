<?php
// sales_history.php (in root directory)

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/modules/auth/functions.php';
require_once __DIR__ . '/modules/sales/functions.php';

// Require login for this page
require_login();

$page_title = 'Sales History'; // For header.php

// --- Fetch sales data ---
$sales_records = get_sales(); // Already sorted newest first by the function

// --- Include Header ---
require_once __DIR__ . '/modules/ui/header.php';

?>

<section class="sales-history">
    <?php if (empty($sales_records)): ?>
        <p>No sales records found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Date & Time</th>
                    <th>Items Sold</th>
                    <th>Total Amount</th>
                    <th>Sold By</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales_records as $sale): ?>
                <tr>
                    <td><?php echo htmlspecialchars($sale['sale_id'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($sale['datetime'] ?? 'N/A'); ?></td>
                    <td>
                        <?php if (!empty($sale['items']) && is_array($sale['items'])): ?>
                            <ul>
                                <?php foreach ($sale['items'] as $item): ?>
                                    <li>
                                        <?php echo htmlspecialchars($item['quantity'] ?? 0); ?> x
                                        <?php echo htmlspecialchars($item['name'] ?? 'N/A'); ?>
                                        (@ <?php echo number_format((float)($item['price'] ?? 0), 2); ?>)
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td style="text-align: right;"><?php echo number_format((float)($sale['total_amount'] ?? 0), 2); ?></td>
                     <td><?php echo htmlspecialchars($sale['sold_by'] ?? 'N/A'); ?></td>
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