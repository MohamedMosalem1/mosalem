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
      <a href="../index.php" class="logo">User<span>Panel</span></a>
      <nav class="navbar">
         <a href="user_page.php">home</a>
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
         <a href="user_profile_update.php" class="btn">update profile</a>
         <a href="logout.php" class="delete-btn">logout</a>
      </div>
   </section>

</header>