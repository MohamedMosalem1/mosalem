<?php
include 'config.php';
session_start();
$user_admin_id = $_SESSION['user_admin_id'];

if (!isset($user_admin_id)) {
   header('location:../index.php');
}

$allowed_actions = ['pending', 'completed', 'sale_yes', 'sale_no'];
if (isset($_GET['action']) && in_array($_GET['action'], $allowed_actions)) {
   $action = $_GET['action'];
} else {
   $action = '';
}

if(isset($_POST['update_payment'])){
   $order_id = $_POST['order_id'];
   $payment_status = $_POST['payment_status'];
   $update_payment = $conn -> prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $update_payment -> execute([$payment_status, $order_id]);
   $message[] = 'payment status updated!';
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:admin_orders.php');
} ?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>orders</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php' ?>

<section class="orders">
<h1 class="heading">placed orders</h1>
<div class="box-container">
   <?php
      if ($action == 'pending') {
         $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
         $select_orders -> execute(['pending']);
      } elseif ($action == 'completed') {
         $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
         $select_orders -> execute(['completed']);
      } elseif ($action == 'sale_yes') {
         $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE sale = 'yes'");
         $select_orders -> execute();
      } elseif ($action == 'sale_no') {
         $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE sale = 'no'");
         $select_orders -> execute();
      } else {
         $select_orders = $conn->prepare("SELECT * FROM `orders`");
         $select_orders -> execute();
      }
      if ( $select_orders -> rowCount() > 0 ) {
         while ( $fetch_orders = $select_orders -> fetch(PDO::FETCH_ASSOC) ) { ?>
            <div class="box">
               <p> placed on : <span><?= $fetch_orders['placed_on']; ?></span> </p>
               <p> name : <span><?= $fetch_orders['name']; ?></span> </p>
               <p> number : <span><?= $fetch_orders['number']; ?></span> </p>
               <p> address : <span><?= $fetch_orders['address']; ?></span> </p>
               <p> total products : <span><?= $fetch_orders['total_products']; ?></span> </p>
               <p> total price : <span><?= $fetch_orders['total_price']; ?></span> </p>
               <p> payment method : <span><?= $fetch_orders['method']; ?></span> </p>
               <form action="" method="post">
                  <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                  <select name="payment_status" class="select">
                     <option selected disabled><?= $fetch_orders['payment_status']; ?></option>
                     <option value="pending">pending</option>
                     <option value="completed">completed</option>
                  </select>
                  <div class="flex-btn">
                     <input type="submit" value="update" class="option-btn" name="update_payment">
                     <a href="admin_orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('delete this order?');">delete</a>
                  </div>
               </form>
            </div>
         <?php }
      } else {
         echo '<p class="empty">no orders placed yet!</p>';
      } ?>
</div>
</section>
<script src="js/admin_script.js"></script>
</body>
</html>