<?php
include 'config.php';
session_start();

$user_admin_id = $_SESSION['user_admin_id'] ?? null;

if (!$user_admin_id) {
   header('location:../index.php');
   exit;
}

$allowed_actions = ['add', 'show'];
$action = in_array($_GET['action'] ?? '', $allowed_actions) ? $_GET['action'] : 'show';

if (isset($_GET['delete'])) {
   $delete_id = intval($_GET['delete']);
   if ($delete_id == $user_admin_id) {
      $error = "You cannot delete your own admin account!";
   } else {
      $check = $conn->prepare("SELECT * FROM `admin and users` WHERE id = ? AND status = 'admin'");
      $check->execute([$delete_id]);
      if ($check->rowCount() > 0) {
         $delete_admin = $conn->prepare("DELETE FROM `admin and users` WHERE id = ?");
         $delete_admin->execute([$delete_id]);
         $message = "Admin account deleted successfully!";
         header('Location: admin_accounts.php?action=show');
         exit;
      } else {
         $error = "Account not found or not an admin.";
      }
   }
} ?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Admin Accounts</title>
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="accounts">
   <h1 class="heading">Admin Accounts</h1>

   <?php if (!empty($message)) echo "<p class='message success'>{$message}</p>"; ?>
   <?php if (!empty($error)) echo "<p class='message error'>{$error}</p>"; ?>

   <div class="box-container">
      <?php if ($action == 'show'): ?>
         <?php
            $stmt = $conn->prepare("SELECT * FROM `admin and users` WHERE status = 'admin' ORDER BY id DESC");
            $stmt->execute();
            if ($stmt->rowCount() > 0):
               while ($admin = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                  <div class="box">
                     <p>User ID: <span><?= $admin['id']; ?></span></p>
                     <p>Username: <span><?= htmlspecialchars($admin['name']); ?></span></p>
                     <div class="flex-btn">
                        <?php if ($admin['id'] != $user_admin_id): ?>
                           <a href="?delete=<?= $admin['id']; ?>" onclick="return confirm('Delete this admin account?');" class="delete-btn">Delete</a>
                        <?php endif; ?>
                        <?php if ($admin['id'] == $user_admin_id): ?>
                           <a href="admin_profile_update.php?update=<?= $admin['id']; ?>" class="option-btn">Update</a>
                        <?php endif; ?>
                     </div>
                  </div>
               <?php endwhile;
            else:
               echo '<p class="empty">No admin accounts found.</p>';
            endif;
         ?>
      <?php endif; ?>
   </div>
</section>

<script src="js/admin_script.js"></script>
</body>
</html>
