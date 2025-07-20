<?php
include 'config.php';
session_start();

$user_admin_id = $_SESSION['user_admin_id'];

if(!isset($user_admin_id)){
   header('location:../index.php');
};

if(isset($_POST['update'])){
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_EMAIL);
   $update_profile_name = $conn->prepare("UPDATE `admin and users` SET name = ? AND email = ? WHERE id = ?");
   $update_profile_name->execute([$name, $email, $user_admin_id]);
   $prev_pass = $_POST['prev_pass'];
   $old_pass = sha1($_POST['old_pass']);
   @$old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);
   $new_pass = sha1($_POST['new_pass']);
   @$new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
   $confirm_pass = sha1($_POST['confirm_pass']);
   @$confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_STRING);
   $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
   if ($old_pass != $empty_pass) {
      if ($old_pass != $prev_pass) {
         $message[] = 'old password not matched!';
      } elseif($new_pass != $confirm_pass) {
         $message[] = 'confirm password not matched!';
      } else {
         if ($new_pass != $empty_pass) {
            $update_admin_pass = $conn->prepare("UPDATE `admin and users` SET password = ? WHERE id = ?");
            $update_admin_pass->execute([$confirm_pass, $user_admin_id]);
            $message[] = 'password updated successfully!';
         } else {
            $message[] = 'please enter a new password!';
         }
      }
   } else {
      $message[] = 'please enter old password';
   }
} ?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>admin profile update</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'user_header.php' ?>

<?php
   $select_profile_name = $conn->prepare("SELECT * FROM `admin and users` WHERE id = ? ");
   $select_profile_name->execute([$user_admin_id]);
   $update = $select_profile_name->fetch((PDO::FETCH_ASSOC));
?>

<section class="form-container">
   <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
      <h3>update profile</h3>
      <input type="hidden" name="prev_pass" value="<?= $update['password']; ?>">
      <input type="text" name="name" value="<?= $update['name']; ?>" required placeholder="enter your username" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="email" name="email" value="<?= $update['email']; ?>" required placeholder="enter your email" maxlength="50"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="old_pass" placeholder="enter old password" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="new_pass" placeholder="enter new password" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="confirm_pass" placeholder="confirm new password" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="update now" class="btn" name="update">
   </form>
</section>
<script src="js/admin_script.js"></script>
</body>
</html>