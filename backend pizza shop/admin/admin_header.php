<?php
   if(isset($message)){
      foreach($message as $messages){ ?>
         <div class="message">
            <span><?= $messages ?></span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
      <?php }
   } ?>

<header class="header">
   <section class="flex">
      <a href="admin_page.php" class="logo">Admin<span>Panel</span></a>
      <nav class="navbar">
         <a href="admin_page.php">home</a>
         <a href="admin_products.php?action=show">products</a>
         <a href="admin_orders.php">orders</a>
         <a href="admin_accounts.php?action=show">admin</a>
         <a href="users_accounts.php">user</a>
      </nav>
      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>
      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `admin and users` WHERE id = ?");
            $select_profile->execute([$user_admin_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC); ?>
         <p><?= $fetch_profile['name']; ?></p>
         <a href="admin_profile_update.php" class="btn">update profile</a>
         <a href="logout.php" class="delete-btn">logout</a>
         <div class="flex-btn">
            <a href="../index.php" class="option-btn">login</a>
            <a href="admin_register.php" class="option-btn">register</a>
         </div>
      </div>
   </section>

</header>