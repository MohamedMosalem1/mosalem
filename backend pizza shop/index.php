<?php
include 'config.php';
session_start();

if(isset($_SESSION['user_admin_id'])){
   $user_admin_id = $_SESSION['user_admin_id'];
} else {
   $user_admin_id = '';
};

// part 1 :
if(isset($_GET['logout'])){
   session_unset();
   session_destroy();
   session_start();
   $_SESSION['success_message'] = 'logged out!';
   header('location:index.php');
   exit();
}

// part 2 :
if ( isset($_REQUEST['login']) && $_SERVER['REQUEST_METHOD'] === 'POST' && $_REQUEST['login'] ) {
   if ( isset($_POST['email'], $_POST['pass']) && !empty($_POST['email']) && !empty($_POST['pass']) ) {
      $email_1 = filter_var(trim( $_POST['email'], FILTER_SANITIZE_EMAIL) );
      $hashed_pass = sha1(string: $_POST['pass']);
      if ( !filter_var($email_1, FILTER_VALIDATE_EMAIL) ) {
         $message[] = 'Invalid email format!';
      } elseif ( preg_match('/\s/', $email_1) ) {
         $message[] = 'Email must not contain spaces!';
      } elseif ( !filter_var($email_1, FILTER_VALIDATE_EMAIL) ) {
         $message[] = 'Invalid email format!';
      } elseif ( strlen($email_1) > 50 ) {
         $message[] = 'Email must be less than 50 characters long!';
      } elseif ( strlen($email_1) < 5 ) {
         $message[] = 'Email must be at least 5 characters long!';
      }
      if ( empty($message) ) {
         try {
            $select_user = $conn->prepare("SELECT * FROM `admin and users` WHERE email = ? AND password = ?");
            $select_user->execute([$email_1, $hashed_pass]);
            $row = $select_user->fetch(PDO::FETCH_ASSOC);
            if ( $select_user -> rowCount() > 0) {
               if ( $email_1 == $row['email'] ) {
                  if ( $hashed_pass == $row['password'] ) {
                     if ( $row['status'] == 'user') {
                        $_SESSION['user_admin_id'] = $row['id'];
                        $_SESSION['success_message'] = "You are now logged in " . $row['name'] . " is user.";
                        header('location:index.php');
                        exit();
                     } elseif ( $row['status'] == 'admin' ) {
                        $_SESSION['user_admin_id'] = $row['id'];
                        $_SESSION['success_message'] = "You are now logged in " . $row['name'] . " is admin.";
                        header('location:index.php');
                        exit();
                     } else {
                        $message[] = "Not Access denied: accounts not found here.";
                     }
                  } else {
                     $message[] = 'incorrect password!';
                  }
               } else {
                  $message[] = 'incorrect email!';
               }
            } else {
               $message[] = 'Not Access denied: accounts not found here.';
            }
         } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            $errors[] = 'Database error: ' . $e->getMessage();
         }
      } else {
         $message[] = 'Founded errors!';
      }
   } else {
      $message[] = 'Please fill in both email and password!';
   }
}

// part 3 :
if( isset($_REQUEST['register']) && $_SERVER['REQUEST_METHOD'] === 'POST' && $_REQUEST['register'] ){
   if (isset( $_POST['name'], $_POST['email'], $_POST['pass'], $_POST['cpass'])
      && !empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['pass']) && !empty($_POST['cpass']) ) {
      $name = htmlspecialchars( $_POST['name'] );
      $email = htmlspecialchars( $_POST['email'] );
      $pass = htmlspecialchars( $_POST['pass'] );
      $cpass = htmlspecialchars( $_POST['cpass'] );
      @$name_1 = filter_var(trim($name, FILTER_SANITIZE_STRING));
      $email_1 = filter_var(trim($email, FILTER_SANITIZE_EMAIL));
      @$pass_1 = filter_var($pass, FILTER_SANITIZE_STRING);
      $pass_hash = sha1($pass_1);
      $cpass_hash = sha1($cpass);
      if ( empty($name_1) || empty($email_1) || empty($pass_hash) || empty($cpass_hash) ) {
         $message[] = 'Please fill in both email and password!';
      } elseif ( !filter_var($email_1, FILTER_VALIDATE_EMAIL) ) {
         $message[] = 'Invalid email format!';
      } elseif ( preg_match('/\s/', $email_1) ) {
         $message[] = 'Email must not contain spaces!';
      } elseif ( !filter_var($email_1, FILTER_VALIDATE_EMAIL) ) {
         $message[] = 'Invalid email format!';
      } elseif ( strlen($email_1) > 50 ) {
         $message[] = 'Email must be less than 50 characters long!';
      } elseif ( strlen($email_1) < 5 ) {
         $message[] = 'Email must be at least 5 characters long!';
      } elseif ( strlen($name_1) > 20 ) {
         $message[] = 'Name must be less than 20 characters long!';
      } elseif ( strlen($name_1) < 5 ) {
         $message[] = 'Name must be at least 5 characters long!';
      } elseif ( preg_match('/\s/', $name_1) ) {
         $message[] = 'Name must not contain spaces!';
      } elseif ( strlen($pass_1) > 20 ) {
         $message[] = 'Password must be less than 20 characters long!';
      } elseif ( strlen($pass_1) < 5 ) {
         $message[] = 'Password must be at least 5 characters long!';
      } elseif ( preg_match('/\s/', $pass_1) ) {
         $message[] = 'Password must not contain spaces!';
      } elseif ( strlen($cpass) > 20 ) {
         $message[] = 'Confirm Password must be less than 20 characters long!';
      } elseif ( strlen($cpass) < 5 ) {
         $message[] = 'Confirm Password must be at least 5 characters long!';
      } elseif ( preg_match('/\s/', $cpass) ) {
         $message[] = 'Confirm Password must not contain spaces!';
      }
      if ( empty($message) ) {
         try {
            $select_user = $conn->prepare("SELECT * FROM `admin and users` WHERE name = ? AND email = ? AND status = 'user'");
            $select_user -> execute([$name_1, $email_1]);
            if( $select_user -> rowCount() > 0 ){
               $message[] = 'username or email already exists!';
            } else {
               if ( $pass_1 != $cpass ){
                  $message[] = 'confirm password not matched!';
               } else {
                  $pass_hash = sha1($pass_1);
                  $cpass_hash = sha1($cpass);
                  $insert_user = $conn->prepare("INSERT INTO `admin and users`(name, email, password, status) VALUES(?,?,?,?)");
                  $insert_user->execute([$name, $email, $cpass_hash, 'user']);
                  $_SESSION['success_message'] = 'Registered successfully, login now please!';
                  $message[] = $_SESSION['success_message'];
                  header('location:index.php');
                  exit();
               }
            }
         } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            $errors[] = 'Database error: ' . $e->getMessage();
         }
      } else {
         $message[] = 'Founded errors!';
      }
   } else {
      $message[] = 'Please fill in both email and password!';
   }
}

// part 4 :
if(isset($_GET['delete_cart_item'])){
   $delete_cart_id = intval ( $_GET['delete_cart_item'] );
   $delete_cart_item = $conn -> prepare("DELETE FROM `cart` WHERE id = ?");
   $delete_cart_item -> execute([$delete_cart_id]);
   $_SESSION['success_message'] = 'cart item deleted!';
   header('location:index.php');
   exit();
}

// part 5 :
if ( isset($_POST['update_qty']) ){
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['qty'];
   $qty = filter_var($qty, FILTER_SANITIZE_NUMBER_INT);
   $update_qty = $conn -> prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
   $update_qty -> execute ( [$qty, $cart_id] );
   $_SESSION['success_message'] = 'cart quantity updated!';
   header('location:index.php');
   exit();
}

// part 6 :
if(isset($_POST['add_to_cart'])){
   if( $user_admin_id == '' ){
      $message[] = 'please login first!';
   } else {
      $pid = $_POST['pid'];
      $name = $_POST['name'];
      $price = $_POST['price'];
      $image = $_POST['image'];
      $qty = $_POST['qty'];
      $qty = filter_var($qty, FILTER_SANITIZE_NUMBER_INT);
      $select_cart = $conn -> prepare("SELECT * FROM `cart` WHERE user_id = ? AND id = ?");
      $select_cart -> execute([$user_admin_id, $pid]);
      if ( $select_cart -> rowCount() > 0 ) {
         $message[] = 'already added to cart';
      } else {
         $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
         $insert_cart -> execute([$user_admin_id, $pid, $name, $price, $qty, $image]);
         $message[] = 'added to cart!';
      }
   }
}

// part 7 :
if (isset($_POST['order'])) {
   if ($user_admin_id == '') {
      $message[] = 'please login first!';
   } else {
      $name = $_POST['name'];
      @$name = filter_var($name, FILTER_SANITIZE_STRING);
      $number = $_POST['number'];
      $number = filter_var($number, FILTER_SANITIZE_NUMBER_INT);
      $address = 'flat no.'.$_POST['flat'].', '.$_POST['street'].' - '.$_POST['pin_code'];
      $method = $_POST['method'];
      @$method = filter_var($method, FILTER_SANITIZE_STRING);
      $total_price = $_POST['total_price'];
      $total_products = $_POST['total_products'];
      $count_products = $_POST['count_products'];
      $discount_code = $_POST['discount_code'];
      $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart->execute([$user_admin_id]);
      if ($select_cart->rowCount() > 0) {
         $select_discount = $conn->prepare("SELECT * FROM `discounts` WHERE user_id = ?");
         $select_discount->execute([$user_admin_id]);
         if ($select_discount->rowCount() > 0) {
            $fetch_discount = $select_discount->fetch(PDO::FETCH_ASSOC);
            if ($fetch_discount['code'] == $discount_code) {
               $total_price = $total_price - $fetch_discount['discount_value'];
               $update_discount = $conn->prepare("DELETE FROM `discounts` WHERE code = ? AND user_id = ?");
               $update_discount->execute([$discount_code, $user_admin_id]);
               $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, method, address, total_products, count_products, total_price, sale) VALUES(?,?,?,?,?,?,?,?)");
               $insert_order->execute([$user_admin_id, $name, $number, $method, $address, $total_products, $count_products, $total_price, 'yes']);
               $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
               $delete_cart->execute([$user_admin_id]);
               $message[] = 'discount code applied!';
               $message[] = 'order placed successfully!';
            } else {
               $message[] = 'invalid discount code!';
            }
         } else {
            $message[] = 'you have no discount code!';
         }
         $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, method, address, total_products, count_products, total_price, sale) VALUES(?,?,?,?,?,?,?,?)");
         $insert_order->execute([$user_admin_id, $name, $number, $method, $address, $total_products, $count_products, $total_price, 'no']);
         $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
         $delete_cart->execute([$user_admin_id]);
         $message[] = 'order placed successfully!';
      }else{
         $message[] = 'your cart empty!';
      }
   }
} ?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Complete Responsive Pizza Shop Website Design</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
<!-- message  -->
<?php if(isset($message)){
         foreach($message as $messages){ ?>
            <div class="message">
               <span><?= $messages ?></span>
               <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            </div>
         <?php }
   }  elseif (isset($_SESSION['success_message'])) { ?>
            <div class="message">
               <span><?= $_SESSION['success_message'] ?></span>
               <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            </div>
            <?php unset($_SESSION['success_message']); } ?>
<!-- end message  -->

<!-- navbar section starts  -->
<header class="header">
   <section class="flex">
      <a href="#home" class="logo"><span>P</span>izza.</a>
      <nav class="navbar">
         <a href="#home">home</a>
         <a href="#about">about</a>
         <a href="#menu">menu</a>
         <a href="#order">order</a>
         <a href="#faq">faq</a>
         <?php
            $admin_or_user = $conn->prepare("SELECT * FROM `admin and users` WHERE id = ?");
            $admin_or_user -> execute([$user_admin_id]);
            $fetch_admin_or_user = $admin_or_user -> fetch(PDO::FETCH_ASSOC);
            if ( $admin_or_user -> rowCount() > 0 ) {
               if ( $fetch_admin_or_user ['status'] == 'admin' ) { ?>
                     <a href="admin/admin_page.php">dahboard admin</a>
            <?php } elseif ( $fetch_admin_or_user ['status'] == 'user' ) { ?>
                     <a href="user/user_page.php">dahboard user</a>
            <?php } } ?>
      </nav>
      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="order-btn" class="fas fa-box"></div>
         <?php
            $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $count_cart_items -> execute([$user_admin_id]);
            $total_cart_items = $count_cart_items -> rowCount(); ?>
         <div id="cart-btn" class="fas fa-shopping-cart"><span>(<?= $total_cart_items; ?>)</span></div>
      </div>
   </section>
</header>
<!-- navbar section ends -->

<!-- user and admin account and cart starts -->
<div class="user-account">
   <section>
      <div id="close-account"><span>close</span></div>
      <div class="user">
         <?php
         $select_user = $conn->prepare("SELECT * FROM `admin and users` WHERE id = ?");
         $select_user -> execute([$user_admin_id]);
         $select = $select_user -> fetch(PDO::FETCH_ASSOC);
         if ( $select_user -> rowCount() > 0) {
            echo '<p>welcome ! <span>' . htmlspecialchars($select['name']) . '</span></p>';
            echo '<a href="index.php?logout" class="btn">logout</a>';
         } else { ?>
            <div class="flex">
               <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                  <h3>login now</h3>
                  <p>default username = <span>mosalem</span> & password = <span>123</span></p>
                  <input type="email" name="email" required class="box" placeholder="enter your email" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
                  <input type="password" name="pass" required class="box" placeholder="enter your password" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
                  <input type="submit" value="login now" name="login" class="btn">
               </form>
               <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                  <h3>register now</h3>
                  <input type="text" name="name" oninput="this.value = this.value.replace(/\s/g, '')" required class="box" placeholder="enter your username" maxlength="20">
                  <input type="email" name="email" required class="box" placeholder="enter your email" maxlength="50">
                  <input type="password" name="pass" required class="box" placeholder="enter your password" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
                  <input type="password" name="cpass" required class="box" placeholder="confirm your password" maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
                  <input type="submit" value="register now" name="register" class="btn">
               </form>
            </div>
         <?php } ?>
      </div>
      <div class="display-orders">
         <?php
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $select_cart -> execute([$user_admin_id]);
            if ( $select_cart -> rowCount() > 0 ) {
               while ( $fetch_cart = $select_cart -> fetch(PDO::FETCH_ASSOC) ) {
                  echo '<p>' . htmlspecialchars($fetch_cart['name']) . ' <span>(' . $fetch_cart['price'] . ' x ' . $fetch_cart['quantity'] . ')</span></p>';
               }
            } else {
               echo '<p><span>your cart is empty!</span></p>';
            } ?>
      </div>
   </section>
</div>
<!-- user and admin account and cart ends -->

<!-- my orders starts -->
<div class="my-orders">
   <section>
      <div id="close-orders"><span>close</span></div>
      <h3 class="title"> my orders </h3>
      <?php
         $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ? AND payment_status = 'pending' ORDER BY placed_on DESC LIMIT 10");
         $select_orders -> execute([$user_admin_id]);
         if ( $select_orders -> rowCount() > 0 ) {
            while ( $fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) { ?>
               <div class="box">
                  <p> placed on : <span><?= $fetch_orders['placed_on']; ?></span> </p>
                  <p> name : <span><?= $fetch_orders['name']; ?></span> </p>
                  <p> number : <span><?= $fetch_orders['number']; ?></span> </p>
                  <p> address : <span><?= $fetch_orders['address']; ?></span> </p>
                  <p> payment method : <span><?= $fetch_orders['method']; ?></span> </p>
                  <p> total_orders : <span><?= $fetch_orders['total_products']; ?></span> </p>
                  <p> total price : <span>$<?= $fetch_orders['total_price']; ?>/-</span> </p>
                  <p> payment status : <span style="color:red" > <?= $fetch_orders['payment_status']; ?> </span> </p>
               </div>
            <?php }
         } else {
            echo '<p class="empty">nothing ordered yet!</p>';
         } ?>
   </section>
</div>
<!-- my orders ends -->

<!-- shopping cart starts -->
<div class="shopping-cart">
   <section>
      <div id="close-cart"><span>close</span></div>
      <?php
         $grand_total = 0;
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart -> execute([$user_admin_id]);
         if ( $select_cart -> rowCount() > 0 ) {
            while ( $fetch_cart = $select_cart -> fetch(PDO::FETCH_ASSOC) ) {
               $sub_total = ( $fetch_cart['price'] * $fetch_cart['quantity'] );
               $grand_total += $sub_total; ?>
               <div class="box">
                  <a href="index.php?delete_cart_item=<?= $fetch_cart['id']; ?>" class="fas fa-times" onclick="return confirm('delete this cart item?');"></a>
                  <img src="admin/uploaded_img/<?= $fetch_cart['image']; ?>" alt="">
                  <div class="content">
                     <p> <?= $fetch_cart['name']; ?> <span>(<?= $fetch_cart['price']; ?> x <?= $fetch_cart['quantity']; ?>)</span></p>
                     <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                        <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
                        <!-- <input type="hidden" name="cart_id" value="<?= $fetch_cart['quantity']; ?>"> -->
                        <input type="number" name="qty" class="qty" min="1" max="99" value="<?= $fetch_cart['quantity']; ?>" onkeypress="if(this.value.length == 2) return false;">
                        <button type="submit" class="fas fa-edit" name="update_qty"></button>
                     </form>
                  </div>
               </div>
            <?php }
         } else {
            echo '<p class="empty"><span>your cart is empty!</span></p>';
         } ?>
      <div class="cart-total"> grand total : <span>$<?= $grand_total; ?>/-</span></div>
      <a href="#order" class="btn">order now</a>
   </section>
</div>
<!-- shopping cart ends -->

<!-- scroll Pizza starts -->
<div class="home-bg">
   <section class="home" id="home">
      <div class="slide-container">
         <div class="slide active">
            <div class="image">
               <img src="images/home-img-1.png" alt="">
            </div>
            <div class="content">
               <h3>homemade Pepperoni Pizza</h3>
               <div class="fas fa-angle-left" onclick="prev()"></div>
               <div class="fas fa-angle-right" onclick="next()"></div>
            </div>
         </div>
         <div class="slide">
            <div class="image">
               <img src="images/home-img-2.png" alt="">
            </div>
            <div class="content">
               <h3>Pizza With Mushrooms</h3>
               <div class="fas fa-angle-left" onclick="prev()"></div>
               <div class="fas fa-angle-right" onclick="next()"></div>
            </div>
         </div>
         <div class="slide">
            <div class="image">
               <img src="images/home-img-3.png" alt="">
            </div>
            <div class="content">
               <h3>Mascarpone And Mushrooms</h3>
               <div class="fas fa-angle-left" onclick="prev()"></div>
               <div class="fas fa-angle-right" onclick="next()"></div>
            </div>
         </div>
      </div>
   </section>
</div>
<!-- scroll Pizza ends -->

<!-- about section starts  -->
<section class="about" id="about">
   <h1 class="heading">about us</h1>
   <div class="box-container">
      <div class="box">
         <img src="images/about-1.svg" alt="">
         <h3>made with love</h3>
         <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Illum quae amet beatae magni numquam facere sit. Tempora vel laboriosam repudiandae!</p>
         <a href="#menu" class="btn">our menu</a>
      </div>
      <div class="box">
         <img src="images/about-2.svg" alt="">
         <h3>30 minutes delivery</h3>
         <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Illum quae amet beatae magni numquam facere sit. Tempora vel laboriosam repudiandae!</p>
         <a href="#menu" class="btn">our menu</a>
      </div>
      <div class="box">
         <img src="images/about-3.svg" alt="">
         <h3>share with freinds</h3>
         <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Illum quae amet beatae magni numquam facere sit. Tempora vel laboriosam repudiandae!</p>
         <a href="#menu" class="btn">our menu</a>
      </div>
   </div>
</section>
<!-- about section ends -->

<!-- menu section starts  -->
<section id="menu" class="menu">
   <h1 class="heading">our menu</h1>
   <div class="box-container">
      <?php
         $select_products = $conn->prepare("SELECT * FROM `products`");
         $select_products -> execute();
         if ( $select_products -> rowCount() > 0 ){
            while( $fetch_products = $select_products -> fetch(PDO::FETCH_ASSOC) ){ ?>
               <div class="box">
                  <div class="price">$<?= $fetch_products['price'] ?>/-</div>
                  <img src="admin/uploaded_img/<?= $fetch_products['image'] ?>" alt="">
                  <div class="name"><?= $fetch_products['name'] ?></div>
                  <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                     <input type="hidden" name="pid" value="<?= $fetch_products['id'] ?>">
                     <input type="hidden" name="name" value="<?= $fetch_products['name'] ?>">
                     <input type="hidden" name="price" value="<?= $fetch_products['price'] ?>">
                     <input type="hidden" name="image" value="<?= $fetch_products['image'] ?>">
                     <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
                     <input type="submit" class="btn" name="add_to_cart" value="add to cart">
                  </form>
               </div>
            <?php }
         } else {
            echo '<p class="empty">no products added yet!</p>';
         } ?>
   </div>
</section>
<!-- menu section ends -->

<!-- order section starts -->
<section class="order" id="order">
   <h1 class="heading">order now</h1>
   <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
      <div class="display-orders">
      <?php
            $grand_total = 0;
            $count_products = 0;
            $cart_item = [];
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $select_cart -> execute([$user_admin_id]);
            if( $select_cart -> rowCount() > 0 ){
               while( $fetch_cart = $select_cart -> fetch(PDO::FETCH_ASSOC) ){
                  $count_products += $fetch_cart['quantity'];
                  $sub_total = ( $fetch_cart['price'] * $fetch_cart['quantity'] );
                  $grand_total += $sub_total;
                  $cart_item [] = $fetch_cart['name'].' ( '.$fetch_cart['price'].' x '.$fetch_cart['quantity'].' ) - ';
                  $total_products = implode($cart_item);
                  echo '<p>'.$fetch_cart['name'].' <span>('.$fetch_cart['price'].' x '.$fetch_cart['quantity'].')</span></p>';
                  echo '<p class="total">Grand Total: <span>' . "$" . number_format($sub_total, 2) . '</span></p>';
               }
               echo '<p class="total">Products Total: <span>' . " $" . $total_products. '</span></p>';
               echo '<p class="total">Counts Total: <span>' . $count_products. '</span></p>';
            } else {
               echo '<p class="empty"><span>your cart is empty!</span></p>';
            } ?>
      </div>
      <div class="grand-total"> grand total : <span>$<?= $grand_total; ?>/-</span></div>
      <input type="hidden" name="total_products" value="<?= $total_products; ?>">
      <input type="hidden" name="count_products" value="<?= $count_products; ?>">
      <input type="hidden" name="total_price" value="<?= $grand_total; ?>">
      <div class="flex">
         <div class="inputBox">
            <span>your name :</span>
            <input type="text" name="name" class="box" required placeholder="enter your name" maxlength="20">
         </div>
         <div class="inputBox">
            <span>your number :</span>
            <input type="number" name="number" class="box" required placeholder="enter your number" min="0" max="9999999999" onkeypress="if(this.value.length == 10) return false;">
         </div>
         <div class="inputBox">
            <span>payment method</span>
            <select name="method" class="box">
               <option disabled selected>--select payment method--</option>
               <option value="cash on delivery">cash on delivery</option>
               <option value="credit card">credit card</option>
               <option value="paytm">paytm</option>
               <option value="paypal">paypal</option>
            </select>
         </div>
         <div class="inputBox">
            <span>address line 01 :</span>
            <input type="text" name="flat" class="box" required placeholder="e.g. flat no." maxlength="50">
         </div>
         <div class="inputBox">
            <span>address line 02 :</span>
            <input type="text" name="street" class="box" required placeholder="e.g. street name." maxlength="50">
         </div>
         <div class="inputBox">
            <span>pin code :</span>
            <input type="number" name="pin_code" class="box" required placeholder="e.g. 123456" min="0" max="999999" onkeypress="if(this.value.length == 6) return false;">
         </div>
         <div class="inputBox">
            <span>discount code :</span>
            <input type="text" name="discount_code" class="box" placeholder="d.c. ( optional )" min="0" max="999999" onkeypress="if(this.value.length == 50) return false;">
         </div>
      </div>
      <input type="submit" value="order now" class="btn" name="order">
   </form>
</section>
<!-- order section ends -->

<!-- faq section starts  -->
<section class="faq" id="faq">
   <h1 class="heading">FAQ</h1>
   <div class="accordion-container">
      <div class="accordion active">
         <div class="accordion-heading">
            <span>how does it work?</span>
            <i class="fas fa-angle-down"></i>
         </div>
         <p class="accrodion-content">
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Officiis, quas. Quidem minima veniam accusantium maxime, doloremque iusto deleniti veritatis quos.
         </p>
      </div>
      <div class="accordion">
         <div class="accordion-heading">
            <span>how long does it take for delivery?</span>
            <i class="fas fa-angle-down"></i>
         </div>
         <p class="accrodion-content">
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Officiis, quas. Quidem minima veniam accusantium maxime, doloremque iusto deleniti veritatis quos.
         </p>
      </div>
      <div class="accordion">
         <div class="accordion-heading">
            <span>can I order for huge parties?</span>
            <i class="fas fa-angle-down"></i>
         </div>
         <p class="accrodion-content">
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Officiis, quas. Quidem minima veniam accusantium maxime, doloremque iusto deleniti veritatis quos.
         </p>
      </div>
      <div class="accordion">
         <div class="accordion-heading">
            <span>how much protein it contains?</span>
            <i class="fas fa-angle-down"></i>
         </div>
         <p class="accrodion-content">
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Officiis, quas. Quidem minima veniam accusantium maxime, doloremque iusto deleniti veritatis quos.
         </p>
      </div>
      <div class="accordion">
         <div class="accordion-heading">
            <span>is it cooked with oil?</span>
            <i class="fas fa-angle-down"></i>
         </div>
         <p class="accrodion-content">
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Officiis, quas. Quidem minima veniam accusantium maxime, doloremque iusto deleniti veritatis quos.
         </p>
      </div>
   </div>
</section>
<!-- faq section ends -->

<!-- footer section starts  -->
<section class="footer">
   <div class="box-container">
      <div class="box">
         <i class="fas fa-phone"></i>
         <h3>phone number</h3>
         <p>+123-456-7890</p>
         <p>+111-222-3333</p>
      </div>
      <div class="box">
         <i class="fas fa-map-marker-alt"></i>
         <h3>our address</h3>
         <p>mumbai, india - 400104</p>
      </div>
      <div class="box">
         <i class="fas fa-clock"></i>
         <h3>opening hours</h3>
         <p>00:09am to 00:10pm</p>
      </div>
      <div class="box">
         <i class="fas fa-envelope"></i>
         <h3>email address</h3>
         <p>shaikhanas@gmail.com</p>
         <p>anasbhai@gmail.com</p>
      </div>
   </div>
   <div class="credit">
      &copy; copyright @ <?= date('Y'); ?> by <span>mr. Mohamed Mosalem</span> | all rights reserved!
   </div>
</section>
<!-- footer section ends -->
<!-- custom js file link  -->
<script src="js/script.js"></script>
</body>
</html>