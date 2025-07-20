<?php
include 'config.php';
session_start();

$user_admin_id = $_SESSION['user_admin_id'];

if(!isset($user_admin_id)){
   header('location:../index.php');
};

if (isset($_POST['register'])) {
   $name = $_POST['name'];
   @$name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   @$email = filter_var($email, FILTER_SANITIZE_EMAIL);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);
   $select_admin = $conn->prepare("SELECT * FROM `admin and users` WHERE email = ?");
   $select_admin -> execute([$email]);
   if ($select_admin->rowCount() > 0) {
      $message[] = 'email already exist!';
   } else {
      if ($pass != $cpass) {
         $message[] = 'confirm password not matched!';
      } else {
         $insert_admin = $conn->prepare("INSERT INTO `admin and users`(name, password, status) VALUES(?,?,?)");
         $insert_admin->execute([$name, $cpass, 'admin']);
         $message[] = 'new admin registered successfully!';
      }
   }
} ?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register admin</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php' ?>

<section class="form-container">
   <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
      <h3>register now</h3>
      <input type="text" name="name" required placeholder="enter your username" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="email" name="email" required placeholder="enter your email" maxlength="50"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="enter your password" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" required placeholder="confirm your password" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="register now" class="btn" name="register">
   </form>
</section>
<script src="js/admin_script.js"></script>
</body>
</html>