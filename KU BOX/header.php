<?php

if (isset($message)) {
   foreach ($message as $message) {
      echo '
      <div class="message">
         <span>' . $message . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}

?>

<header class="header">

   <div class="flex">

      <a href="home.php" class="logo">KU BOX<span>.</span></a>

      <nav class="navbar">
         <a href="home.php">home</a>
         <a href="shop.php">movies</a>
         <a href="orders.php">orders</a>
         <a href="about.php">about</a>
         <?php
         $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
         $select_profile->execute([$user_id]);
         $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         if ($fetch_profile && $fetch_profile['id'] != 1003) {
            echo '<a href="contact.php">contact</a>';
         }
         ?>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <?php
         $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
         $select_profile->execute([$user_id]);
         $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         if ($fetch_profile && $fetch_profile['id'] != 1003) {
            echo '<div id="user-btn" class="fas fa-user" "></div>';
         }
         ?>
         <a href="search_page.php" class="fas fa-search"></a>
         <?php
         $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $count_cart_items->execute([$user_id]);
         $count_wishlist_items = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
         $count_wishlist_items->execute([$user_id]);
         if ($fetch_profile && $fetch_profile['id'] != 1003) {
            echo '<a href="wishlist.php"><i class="fas fa-heart"></i><span>(' . $count_wishlist_items->rowCount() . ')</span></a>';
         }
         ?>
         <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>(<?= $count_cart_items->rowCount(); ?>)</span></a>
      </div>

      <div class="profile">
         <?php
         if ($fetch_profile) {
            ?>
            <img src="uploaded_img/<?= $fetch_profile['image']; ?>" alt="">
            <p><?= $fetch_profile['name']; ?></p>
            <a href="user_profile_update.php" class="btn">update profile</a>
            <a href="logout.php" class="delete-btn">logout</a>
            <div class="flex-btn">
               <?php
               if ($fetch_profile['user_type'] !== "guest") {
                  echo '<a href="login.php" class="option-btn">login</a>';
                  echo '<a href="register.php" class="option-btn">register</a>';
               }
               ?>
            </div>
            <?php
         }
         ?>
      </div>

   </div>

</header>
