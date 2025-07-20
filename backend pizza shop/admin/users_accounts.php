<?php
include 'config.php';
session_start();
$user_admin_id = $_SESSION['user_admin_id'];
if(!isset($user_admin_id)){
   header('location:../index.php');
};

if(isset($_GET['delete'])){
   $delete_id = intval( $_GET['delete'] );
   $delete_order = $conn->prepare("DELETE FROM `admin and users` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:users_accounts.php');
} ?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>users accounts</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php' ?>

<section class="accounts">
   <h1 class="heading">user accounts</h1>
   <div class="box-container">
   <?php
      $select_accounts = $conn->prepare("SELECT * FROM `admin and users` WHERE status = 'user'");
      $select_accounts->execute();
      if($select_accounts->rowCount() > 0){
         while($fetch_accounts = $select_accounts->fetch(PDO::FETCH_ASSOC)){   ?>
            <div class="box">
               <p> user id : <span><?= $fetch_accounts['id']; ?></span> </p>
               <p> username : <span><?= $fetch_accounts['name']; ?></span> </p>
               <p> email : <span><?= $fetch_accounts['email']; ?></span> </p>
               <a href="users_accounts.php?delete=<?= $fetch_accounts['id']; ?>" onclick="return confirm('delete this account?')" class="delete-btn">delete</a>
            </div>
         <?php }
      } else {
         echo '<p class="empty">no accounts available!</p>';
      } ?>
   </div>
</section>
<script src="js/admin_script.js"></script>
</body>
</html>