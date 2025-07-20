<?php
include 'config.php';
session_start();

if (isset($_SESSION['user_admin_id'])) {
    $user_admin_id = $_SESSION['user_admin_id'];
} else {
    header('location:../index.php');
    exit();
}

// Fetch statistics
$total_orders = 0;
$total_spent = 0;
$total_items = 0;
$total_pendings = 0;
$total_completes = 0;

$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ?");
$stmt->execute([$user_admin_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_orders = count($orders);
foreach ($orders as $order) {
    $total_spent += $order['total_price'];
    $total_items += $order['count_products'];
    $payment_status = $order['payment_status'];
    $order['payment_status'] == "pending" ? $total_pendings++ : $total_pendings;
    $order['payment_status'] == "completed" ? $total_completes++ : $total_completes;
}
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY placed_on DESC LIMIT 1");
$stmt->execute([$user_admin_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($orders as $order) {
    $total_spent_1 = $order['total_price'];
    $total_items_1 = $order['count_products'];
    $placed_on = $order['placed_on'];
    $placed_on = date('d-m-Y', strtotime($placed_on));
    $payment_status_1 = $order['payment_status'];
}
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? AND payment_status = 'completed'");
$stmt->execute([$user_admin_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_items_2 = 0;
$total_spent_2 = 0;
foreach ($orders as $order) {
    $total_spent_2 += $order['total_price'];
    $total_items_2 += $order['count_products'];
} ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'user_header.php'; ?>

    <section class="dashboard">
        <h1 class="heading">Orders Dashboard</h1>
        <div class="box-container">
            <div class="box">
                <h3><?= $total_orders ?></h3>
                <p>Total Orders</p>
                <a href="user_order_details.php?action=total_orders" class="btn">Orders Details</a>
            </div>
            <div class="box">
                <h3>$<?= number_format($total_spent, 2) ?></h3>
                <p>Total Spent</p>
                <a href="user_order_details.php?action=total_prices" class="btn">Orders Details</a>
            </div>
            <div class="box">
                <h3><?= $total_items ?></h3>
                <p>Total Items Ordered</p>
                <a href="user_order_details.php?action=show_items_per_order" class="btn">Orders Details</a>
            </div>
            <div class="box">
                <h3><?= $total_pendings ?></h3>
                <p>Total Pending</p>
                <a href="user_order_details.php?action=total_pendings" class="btn">Orders Details</a>
            </div>
            <div class="box">
                <h3><?= $total_completes ?></h3>
                <p>Total Completed</p>
                <a href="user_order_details.php?action=total_completes" class="btn">Orders Details</a>
            </div>
            <div class="box">
                <?php
                    $step_items = 100;
                    $step_price = 5000;
                    $milestone_items = floor($total_items_2 / $step_items) * $step_items;
                    $milestone_price = floor($total_spent_2 / $step_price) * $step_price;
                    if ($milestone_items >= $step_items || $milestone_price >= $step_price) {
                        $color = 'green';
                        $message = "🎉 You've reached $milestone_items items or total price $milestone_price EGP.";
                    } else {
                        $next_items = ceil($total_items_2 / $step_items) * $step_items;
                        $next_price = ceil($total_spent_2 / $step_price) * $step_price;
                        $remaining_items = $next_items - $total_items_2;
                        $remaining_price = $next_price - $total_spent_2;
                        $color = 'red';
                        $message = "🛍️ Buy $remaining_items more items or spend EGP $remaining_price more to get a 20% discount!";
                    } ?>
                <p><span style="color:<?= $color ?>"><?= $message ?></span></p>
                <a href="user_order_details.php?action=free_gift" class="btn">Orders Details</a>
            </div>
        </div>
    </section>
    <section class="dashboard">
        <h1 class="heading">Last Order</h1>
        <div class="box-container">
                <div class="box">
                    <h3><?= "Total Price = $" . number_format($total_spent_1, 2) ?></h3>
                    <p>Total Items: <?= $total_items_1 ?></p>
                    <!-- <a href="user_orders.php" class="btn">View Orders</a> -->
                </div>
                <div class="box">
                    <h3><?= ucfirst($payment_status_1) ?></h3>
                    <p>Latest Payment Status</p>
                    <a href="user_order_details.php?action=delete_order" onclick="return confirm('Are you sure you want to delete this order?');" class="delete-btn">Delete Order</a>
                </div>
                <div class="box">
                    <h3><?= $placed_on ?></h3>
                    <p>Last Order Date</p>
                    <a href="user_orders.php" class="btn">Order History</a>
                </div>
        </div>
    </section>
    <script src="js/admin_script.js"></script>
</body>
</html>