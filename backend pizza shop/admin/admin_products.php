<?php
include 'config.php';
session_start();
$user_admin_id = $_SESSION['user_admin_id'] ?? null;

if (!$user_admin_id) {
   header('location:../index.php');
   exit;
}

$allowed_actions = ['add', 'show', 'edit', 'delete', 'update'];
$action = $_GET['action'] ?? '';
$action = in_array($action, $allowed_actions) ? $action : '';

if (isset($_POST['add_product'])) {
   $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_INT);
   $image_name = time() . '_' . basename($_FILES['image']['name']);
   $image_type = $_FILES['image']['type'];
   $image_size = $_FILES['image']['size'];
   $image_tmp = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/' . $image_name;
   $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
   $select_product = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_product->execute([$name]);
   if ($select_product->rowCount() > 0) {
      $message[] = 'Product name already exists!';
   } elseif (!in_array($image_type, $allowed_types)) {
      $message[] = 'Invalid image type!';
   } elseif ($image_size > 2000000) {
      $message[] = 'Image size is too large!';
   } else {
      $insert_product = $conn->prepare("INSERT INTO `products`(name, price, image) VALUES(?, ?, ?)");
      $insert_product->execute([$name, $price, $image_name]);
      move_uploaded_file($image_tmp, $image_folder);
      $message[] = 'New product added!';
   }
}

if (isset($_POST['update_product'])) {
   $pid = intval($_POST['pid']);
   $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_INT);
   $old_image = $_POST['old_image'];
   $update_product = $conn->prepare("UPDATE `products` SET name = ?, price = ? WHERE id = ?");
   $update_product->execute([$name, $price, $pid]);
   $message[] = 'Product updated successfully!';
   if (!empty($_FILES['image']['name'])) {
      $image_name = time() . '_' . basename($_FILES['image']['name']);
      $image_type = $_FILES['image']['type'];
      $image_size = $_FILES['image']['size'];
      $image_tmp = $_FILES['image']['tmp_name'];
      $image_folder = 'uploaded_img/' . $image_name;
      $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
      if (!in_array($image_type, $allowed_types)) {
         $message[] = 'Invalid image type!';
      } elseif ($image_size > 2000000) {
         $message[] = 'Image size is too large!';
      } else {
         $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
         $update_image->execute([$image_name, $pid]);
         move_uploaded_file($image_tmp, $image_folder);
         if (!empty($old_image) && file_exists('uploaded_img/' . $old_image)) {
            unlink('uploaded_img/' . $old_image);
         }
         $message[] = 'Image updated successfully!';
      }
   }
}

if (isset($_GET['delete'])) {
   $delete_id = intval($_GET['delete']);
   $select_image = $conn->prepare("SELECT image FROM `products` WHERE id = ?");
   $select_image->execute([$delete_id]);
   $image_data = $select_image->fetch(PDO::FETCH_ASSOC);
   if (!empty($image_data['image']) && file_exists('uploaded_img/' . $image_data['image'])) {
      unlink('uploaded_img/' . $image_data['image']);
   }
   $conn->prepare("DELETE FROM `products` WHERE id = ?")->execute([$delete_id]);
   $conn->prepare("DELETE FROM `cart` WHERE pid = ?")->execute([$delete_id]);
   header('location:admin_products.php?action=show');
   exit;
} ?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Products</title>
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<?php if ($action == 'add') : ?>
<section class="add-products">
   <h1 class="heading">Add Product</h1>
   <form method="post" enctype="multipart/form-data">
      <input type="text" name="name" class="box" required maxlength="100" placeholder="Enter product name">
      <input type="number" name="price" class="box" required min="0" max="9999999999" placeholder="Enter product price">
      <input type="file" name="image" class="box" required accept="image/jpg, image/jpeg, image/png">
      <input type="submit" name="add_product" value="Add Product" class="btn">
   </form>
</section>

<?php elseif ($action == 'show') : ?>
<section class="show-products">
   <h1 class="heading">Products List</h1>
   <div class="box-container">
      <?php
         $stmt = $conn->prepare("SELECT * FROM `products` ORDER BY id DESC");
         $stmt->execute();
         if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
               <div class="box">
                  <div class="price">$<span><?= $row['price']; ?></span></div>
                  <img src="uploaded_img/<?= $row['image']; ?>" alt="">
                  <div class="name"><?= htmlspecialchars($row['name']); ?></div>
                  <div class="flex-btn">
                     <a href="admin_products.php?action=update&update=<?= $row['id']; ?>" class="option-btn">Update</a>
                     <a href="admin_products.php?delete=<?= $row['id']; ?>" class="delete-btn" onclick="return confirm('Delete this product?');">Delete</a>
                  </div>
               </div>
      <?php endwhile;
         } else {
            echo '<p class="empty">No products added yet!</p>';
         }
      ?>
   </div>
</section>

<?php elseif ($action == 'update' && isset($_GET['update'])) :
   $update_id = intval($_GET['update']);
   $stmt = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
   $stmt->execute([$update_id]);
   if ($stmt->rowCount() > 0) :
      $product = $stmt->fetch(PDO::FETCH_ASSOC); ?>
      <section class="update-product">
         <h1 class="heading">Update Product</h1>
         <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="pid" value="<?= $product['id']; ?>">
            <input type="hidden" name="old_image" value="<?= $product['image']; ?>">
            <img src="uploaded_img/<?= $product['image']; ?>" alt="">
            <input type="text" name="name" class="box" required maxlength="100" value="<?= htmlspecialchars($product['name']); ?>">
            <input type="number" name="price" class="box" required min="0" max="9999999999" value="<?= $product['price']; ?>">
            <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
            <div class="flex-btn">
               <input type="submit" name="update_product" value="Update Product" class="btn">
               <a href="admin_products.php?action=show" class="option-btn">Go Back</a>
            </div>
         </form>
      </section>
   <?php else : ?>
      <p class="empty">No product found!</p>
   <?php endif;
endif; ?>

<script src="js/admin_script.js"></script>
</body>
</html>
