<?php
include 'config.php';
session_start();

if (isset($_SESSION['user_admin_id'])) {
    $user_admin_id = $_SESSION['user_admin_id'];
} else {
    header('location:../index.php');
    exit();
} 

$allowed_actions = ['total_orders', 'total_prices', 'show_items_per_order', 'delete_order', 'total_pendings', 'total_completes', 'free_gift'];
$action = (isset($_GET['action']) && in_array($_GET['action'], $allowed_actions)) ? htmlspecialchars($_GET['action'])  : 'Manage'; 
$total_cancels = 0; ?>

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

    <?php if ( $action == 'total_orders' ) : ?>
        <section class="orders">
            <h1 class="heading">your orders</h1>
            <div class="box-container">
                <?php
                $total_prices = 0;
                $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
                $select_orders->execute([$user_admin_id]);
                if ($select_orders->rowCount() > 0) {
                    while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                        $total_prices += $fetch_orders['total_price']; ?>
                        <div class="box">
                            <p> order name : <span style="color: <?php if ($fetch_orders['payment_status'] == 'pending') {
                                echo 'red';
                            } else {
                                echo 'green';
                            }; ?>"><?= $fetch_orders['name_order']; ?></span> </p>
                            <p> placed on : <span><?= $fetch_orders['placed_on']; ?></span> </p>
                            <p> name : <span><?= $fetch_orders['name']; ?></span> </p>
                            <p> number : <span><?= $fetch_orders['number']; ?></span> </p>
                            <p> address : <span><?= $fetch_orders['address']; ?></span> </p>
                            <p> show items : <span><?= $fetch_orders['total_products']; ?></span> </p>
                            <p> total products : <span><?= $fetch_orders['count_products']; ?></span> </p>
                            <p> total price : <span>$<?= $fetch_orders['total_price']; ?>/-</span> </p>
                            <p> payment method : <span><?= $fetch_orders['method']; ?></span> </p>
                            <p> payment status : <span style="color: <?php if ($fetch_orders['payment_status'] == 'pending') {
                                echo 'red';
                            } else {
                                echo 'green';
                            }; ?>"><?= $fetch_orders['payment_status']; ?> </span> </p>
                            <?php if ($fetch_orders['payment_status'] == 'pending') { ?>
                                <a href="user_order_details.php?action=delete_order" onclick="return confirm('delete this order?');" class="delete-btn">delete order</a>
                            <?php } ?>
                        </div>
                <?php } ?>
                        <div class="box">
                            <p>Total Prices Ordered = <span>$<?= $total_prices; ?>/-</span></p>
                        </div>
                <?php } else {
                    echo '<p class="empty">nothing ordered yet!</p>';
                } ?>
            </div>
        </section>
    <?php elseif ( $action == 'total_prices' ) : ?>
        <section class="orders">
            <h1 class="heading">Total Spent</h1>
            <div class="box-container">
                <?php
                $total_prices = 0;
                $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
                $select_orders->execute([$user_admin_id]);
                if ($select_orders->rowCount() > 0) {
                    while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) { 
                        $total_prices += $fetch_orders['total_price']; ?>
                        <div class="box">
                            <p> Order : <span style="color: <?php if ($fetch_orders['payment_status'] == 'pending') {
                                echo 'red';
                            } else {
                                echo 'green';
                            }; ?>"><?= $fetch_orders['name_order']; ?></span> </p>
                            <p> payment status : <span style="color: <?php if ($fetch_orders['payment_status'] == 'pending') {
                                echo 'red';
                            } else {
                                echo 'green';
                            }; ?>"><?= $fetch_orders['payment_status']; ?> </span> </p>
                            <p> total price : <span>$<?= $fetch_orders['total_price']; ?>/-</span> </p>
                            <?php if ($fetch_orders['payment_status'] == 'pending') { ?>
                                <a href="user_order_details.php?action=delete_order" onclick="return confirm('delete this order?');" class="delete-btn">delete order</a>
                            <?php } ?>
                        </div>
                <?php } ?>
                        <div class="box">
                            <p>Total Prices Ordered = <span>$<?= $total_prices; ?>/-</span></p>
                        </div>
                <?php } else {
                    echo '<p class="empty">nothing ordered yet!</p>';
                } ?>
            </div>
        </section>
    <?php elseif ( $action == 'show_items_per_order' ) : ?>
        <section class="orders">
            <h1 class="heading">Total Items</h1>
            <div class="box-container">
                <?php
                $total_prices = 0;
                $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
                $select_orders->execute([$user_admin_id]);
                if ($select_orders->rowCount() > 0) {
                    while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                        $total_prices += $fetch_orders['total_price']; ?>
                        <div class="box">
                            <p> Order : <span style="color: <?php if ($fetch_orders['payment_status'] == 'pending') {
                                echo 'red';
                            } else {
                                echo 'green';
                            }; ?>"><?= $fetch_orders['name_order']; ?></span> </p>
                            <p> payment status : <span style="color: <?php if ($fetch_orders['payment_status'] == 'pending') {
                                echo 'red';
                            } else {
                                echo 'green';
                            }; ?>"><?= $fetch_orders['payment_status']; ?> </span> </p>
                            <p> total price : <span>$<?= $fetch_orders['total_price']; ?>/-</span> </p>
                            <p> show items : <span><?= $fetch_orders['total_products']; ?></span> </p>
                            <p> total products : <span><?= $fetch_orders['count_products']; ?></span> </p>
                            <?php if ($fetch_orders['payment_status'] == 'pending') { ?>
                                <a href="user_order_details.php?action=delete_order" onclick="return confirm('delete this order?');" class="delete-btn">delete order</a>
                            <?php } ?>
                        </div>
                <?php } ?>
                        <div class="box">
                            <p>Total Prices Ordered = <span>$<?= $total_prices; ?>/-</span></p>
                        </div>
                <?php } else {
                    echo '<p class="empty">nothing ordered yet!</p>';
                } ?>
            </div>
        </section>
    <?php elseif ( $action == 'delete_order' ) : ?>
        <section class="orders">
            <h1 class="heading">placed orders</h1>
            <div class="box-container">
                <?php
                $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
                $select_orders->execute([$user_admin_id]);
                if ($select_orders->rowCount() > 0) {
                    while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                        if ($fetch_orders['payment_status'] == 'pending') {
                            $delete_orders = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
                            $delete_orders->execute([$fetch_orders['id']]);
                            $total_cancels ++;
                            echo '<p class="empty">order deleted!</p>';
                            header('location:user_page.php');
                            exit();
                        }
                    }
                } else {
                    echo '<p class="empty">nothing ordered yet!</p>';
                } ?>
            </div>
        </section>

    <?php elseif ( $action == 'total_pendings' ) : ?>
        <section class="orders">
            <h1 class="heading">Total Pendings</h1>
            <div class="box-container">
                <?php
                $total_prices = 0;
                $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
                $select_orders->execute([$user_admin_id]);
                if ($select_orders->rowCount() > 0) {
                    while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                        if ($fetch_orders['payment_status'] == 'pending') {
                        $total_prices += $fetch_orders['total_price']; ?>
                        <div class="box">
                            <p> Order : <span style="color: <?php if ($fetch_orders['payment_status'] == 'pending') {
                                echo 'red';
                            } else {
                                echo 'green';
                            }; ?>"><?= $fetch_orders['name_order']; ?></span> </p>
                            <p> payment status : <span style="color: <?php if ($fetch_orders['payment_status'] == 'pending') {
                                echo 'red';
                            } else {
                                echo 'green';
                            }; ?>"><?= $fetch_orders['payment_status']; ?> </span> </p>
                            <p> total price : <span>$<?= $fetch_orders['total_price']; ?>/-</span> </p>
                            <p> show items : <span><?= $fetch_orders['total_products']; ?></span> </p>
                            <p> total products : <span><?= $fetch_orders['count_products']; ?></span> </p>
                            <?php if ($fetch_orders['payment_status'] == 'pending') { ?>
                                <a href="user_order_details.php?action=delete_order" onclick="return confirm('delete this order?');" class="delete-btn">delete order</a>
                            <?php } ?>
                        </div>
                <?php } } ?>
                        <div class="box">
                            <p>Total Prices Ordered = <span>$<?= $total_prices; ?>/-</span></p>
                        </div>
                <?php } else {
                    echo '<p class="empty">nothing ordered yet!</p>';
                } ?>
            </div>
        </section>
    <?php elseif ( $action == 'total_completes' ) : ?>
        <section class="orders">
            <h1 class="heading">Total Completes</h1>
            <div class="box-container">
                <?php
                $total_prices = 0;
                $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
                $select_orders->execute([$user_admin_id]);
                if ($select_orders->rowCount() > 0) {
                    while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                        if ($fetch_orders['payment_status'] == 'completed') {
                        $total_prices += $fetch_orders['total_price']; ?>
                        <div class="box">
                            <p> Order : <span style="color: <?php if ($fetch_orders['payment_status'] == 'pending') {
                                echo 'red';
                            } else {
                                echo 'green';
                            }; ?>"><?= $fetch_orders['name_order']; ?></span> </p>
                            <p> payment status : <span style="color: <?php if ($fetch_orders['payment_status'] == 'pending') {
                                echo 'red';
                            } else {
                                echo 'green';
                            }; ?>"><?= $fetch_orders['payment_status']; ?> </span> </p>
                            <p> total price : <span>$<?= $fetch_orders['total_price']; ?>/-</span> </p>
                            <p> show items : <span><?= $fetch_orders['total_products']; ?></span> </p>
                            <p> total products : <span><?= $fetch_orders['count_products']; ?></span> </p>
                            <?php if ($fetch_orders['payment_status'] == 'pending') { ?>
                                <a href="user_order_details.php?action=delete_order" onclick="return confirm('delete this order?');" class="delete-btn">delete order</a>
                            <?php } ?>
                        </div>
                <?php } } ?>
                        <div class="box">
                            <p>Total Prices Ordered = <span>$<?= $total_prices; ?>/-</span></p>
                        </div>
                <?php } else {
                    echo '<p class="empty">nothing ordered yet!</p>';
                } ?>
            </div>
        </section>
    <?php elseif ( $action == 'free_gift' ) : ?>
        <section class="orders">
            <h1 class="heading">Free Gifts</h1>
            <div class="box-container">
                <div class="box">
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? AND payment_status = 'completed'");
                    $stmt->execute([$user_admin_id]);
                    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $total_items = 0;
                    $total_price = 0;
                    $discount_percent = 20;
                    foreach ($orders as $order) {
                        $total_items += $order['count_products'];
                        $total_price += $order['total_price'];
                        $name = $order['name'];
                    }
                    $step_items = 100;
                    $step_price = 5000;
                    $milestone_items = floor($total_items / $step_items) * $step_items;
                    $milestone_price = floor($total_price / $step_price) * $step_price;
                    if ($milestone_items >= $step_items || $milestone_price >= $step_price) {
                        $checkCode = $conn->prepare("SELECT * FROM discounts WHERE user_id = ? AND (milestone_products = ? OR milestone_prices = ?)");
                        $checkCode->execute([$user_admin_id, $milestone_items, $milestone_price]);
                        if ($checkCode->rowCount() == 0) {
                            $discount_value = ($total_price * $discount_percent) / 100;
                            $price_after_discount = $total_price - $discount_value;
                            do {
                                $code = $name . rand(1000, 9999) . uniqid();
                                $checkUnique = $conn->prepare("SELECT id FROM discounts WHERE code = ?");
                                $checkUnique->execute([$code]);
                            } while ($checkUnique->rowCount() > 0);
                            $stmt = $conn->prepare("
                                INSERT INTO discounts  (code, discount_value, price_after_discount,
                                                        user_id, milestone_products, milestone_prices)
                                VALUES (?, ?, ?, ?, ?, ?) ");
                            $stmt->execute([
                                $code, $discount_value, $price_after_discount,
                                $user_admin_id, $milestone_items, $milestone_price ]);
                            $color = 'green';
                            echo "<p style='color:$color'><b>🎉 Original Price: $total_price</b></p>";
                            echo "<p style='color:$color'><b>Discount ($discount_percent%): $discount_value</b></p>";
                            echo "<p style='color:$color'><b>Price After Discount: $price_after_discount</b></p>";
                            echo "<p style='color:$color'><b>🎁 Congrats! You've reached $milestone_items items or total price $milestone_price EGP.<br>Your discount code is: <span style='background:#eee;padding:3px 8px;border-radius:5px;'>$code</span></b></p>";
                        } else {
                            $existing = $checkCode->fetch(PDO::FETCH_ASSOC);
                            echo "<p style='color:blue'><b>🎁 You already have a discount code: {$existing['code']}</b></p>";
                        }
                    } else {
                        $next_items = ceil($total_items / $step_items) * $step_items;
                        $next_price = ceil($total_price / $step_price) * $step_price;
                        $remaining_items = $next_items - $total_items;
                        $remaining_price = $next_price - $total_price;
                        echo "<p style='color:red'><b>🛍️ Buy $remaining_items more items or spend EGP $remaining_price more to get a 20% discount!</b></p>";
                    } ?>
                    <!-- <p><b style="color: <?= $color ?>;"><?= $message; ?></b></p> -->
                </div>
            </div>
        </section>
    <?php endif; ?>

    <script src="js/admin_script.js"></script>
</body>
</html>