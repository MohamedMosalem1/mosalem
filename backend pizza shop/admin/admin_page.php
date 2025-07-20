<?php
include 'config.php';
session_start();

if(isset($_SESSION['user_admin_id'])){
   $user_admin_id = $_SESSION['user_admin_id'];
} else {
   $user_admin_id = '';
};


if( !isset($user_admin_id) ){
   header('location:../index.php');
} ?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>dashboard admin</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php' ?>

<section class="dashboard">
   <h1 class="heading">dashboard</h1>
   <div class="box-container">
      <!-- register admin -->
      <div class="box">
         <h3><?= "register admin" ?></h3>
         <p>Add New Admin</p>
         <a href="admin_register.php" class="option-btn">Register Admin</a>
      </div>
      <!-- total pendings -->
      <div class="box">
         <?php
            $total_pendings = 0;
            $select_pendings = $conn -> prepare("SELECT * FROM `orders` WHERE payment_status = ?");
            $select_pendings -> execute(['pending']);
            if ( $select_pendings -> rowCount() > 0 ) {
               while ( $fetch_pendings = $select_pendings -> fetch(PDO::FETCH_ASSOC) ) {
                  $total_pendings += $fetch_pendings['total_price'];
               }
            } ?>
         <h3>$<?= $total_pendings; ?>/-</h3>
         <p>total pendings</p>
         <a href="admin_orders.php?action=pending" class="btn">see orders</a>
      </div>
      <!-- total completes -->
      <div class="box">
         <?php
            $total_completes = 0;
            $select_completes = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
            $select_completes -> execute(['completed']);
            if ( $select_completes -> rowCount() > 0 ) {
               while ( $fetch_completes = $select_completes -> fetch(PDO::FETCH_ASSOC)) {
                  $total_completes += $fetch_completes['total_price'];
               }
            } ?>
         <h3>$<?= $total_completes; ?>/-</h3>
         <p>completed orders</p>
         <a href="admin_orders.php?action=completed" class="btn">see orders</a>
      </div>
      <!-- total discounts -->
      <div class="box">
         <?php
            $select_orders = $conn->prepare("SELECT * FROM `orders` where sale = 'yes'");
            $select_orders -> execute();
            $number_of_orders = $select_orders -> rowCount() ?>
         <h3><?= $number_of_orders; ?></h3>
         <p>discounted orders</p>
         <a href="admin_orders.php?action=sale_yes" class="btn">see orders</a>
      </div>
      <!-- total no discounts -->
      <div class="box">
         <?php
            $select_orders = $conn->prepare("SELECT * FROM `orders` where sale = 'no'");
            $select_orders -> execute();
            $number_of_orders = $select_orders -> rowCount() ?>
         <h3><?= $number_of_orders; ?></h3>
         <p>no discounted orders</p>
         <a href="admin_orders.php?action=sale_no" class="btn">see orders</a>
      </div>
      <!-- total orders -->
      <div class="box">
         <?php
            $select_orders = $conn->prepare("SELECT * FROM `orders`");
            $select_orders -> execute();
            $number_of_orders = $select_orders -> rowCount() ?>
         <h3><?= $number_of_orders; ?></h3>
         <p>orders placed</p>
         <a href="admin_orders.php" class="btn">see orders</a>
      </div>
      <!-- total products -->
      <div class="box">
         <?php
            $select_products = $conn->prepare("SELECT * FROM `products`");
            $select_products -> execute();
            $number_of_products = $select_products -> rowCount() ?>
         <h3><?= $number_of_products; ?></h3>
         <p>products update</p>
         <a href="admin_products.php?action=show" class="btn">see products</a>
      </div>
      <!-- total products -->
      <div class="box">
         <h3>Add product</h3>
         <p>products update</p>
         <a href="admin_products.php?action=add" class="btn">see products</a>
      </div>
      <!-- total users -->
      <div class="box">
         <?php
            $select_users = $conn->prepare("SELECT * FROM `admin and users` WHERE status = 'admin'");
            $select_users -> execute();
            $number_of_users = $select_users -> rowCount() ?>
         <h3><?= $number_of_users; ?></h3>
         <p>normal users</p>
         <a href="users_accounts.php" class="btn">see users</a>
      </div>
      <!-- total admins -->
      <div class="box">
         <?php
            $select_admins = $conn->prepare("SELECT * FROM `admin and users` WHERE status = 'user'");
            $select_admins->execute();
            $number_of_admins = $select_admins->rowCount() ?>
         <h3><?= $number_of_admins; ?></h3>
         <p>admin users</p>
         <a href="admin_accounts.php" class="btn">see admins</a>
      </div>
   </div>
</section>
<script src="js/admin_script.js"></script>
</body>
</html>