<footer class="footer">
   <section class="box-container">
      <div class="box">
         <h3>quick links</h3>
         <a href="home.php"> <i class="fas fa-angle-right"></i> home</a>
         <a href="shop.php"> <i class="fas fa-angle-right"></i> shop</a>
         <a href="about.php"> <i class="fas fa-angle-right"></i> about</a>
         <a href="contact.php"> <i class="fas fa-angle-right"></i> contact</a>
      </div>

      <div class="box">
         <h3>extra links</h3>
         <a href="cart.php"> <i class="fas fa-angle-right"></i> cart</a>
         <?php
         $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $count_cart_items->execute([$user_id]);
         $count_wishlist_items = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
         $count_wishlist_items->execute([$user_id]);
         if ($fetch_profile && $fetch_profile['id'] != 1003) {
            echo '<a href="wishlist.php"> <i class="fas fa-angle-right"></i> wishlist</a>';
         }
         ?>
         
         <a href="login.php"> <i class="fas fa-angle-right"></i> login</a>
         <a href="register.php"> <i class="fas fa-angle-right"></i> register</a>
      </div>

      <div class="box">
         <h3>contact info</h3>
         <p> <i class="fas fa-envelope"></i> 100060746@ku.ac.ae (Sultan) </p>
         <p> <i class="fas fa-envelope"></i> 100060365@ku.ac.ae (Alanood)</p>
         <p> <i class="fas fa-envelope"></i> ku.box.project@gmail.com </p>
         <p> <i class="fas fa-map-marker-alt"></i> Abu Dhabi, UAE </p>
      </div>

      <div class="box">
         <h3>follow us</h3>
         <a href="#"> <i class="fab fa-facebook-f"></i> facebook </a>
         <a href="#"> <i class="fab fa-twitter"></i> twitter </a>
         <a href="https://www.instagram.com/ku__box/" target="_blank"> <i class="fab fa-instagram"></i> instagram </a>
      </div>
   </section>
   <p class="credit"> &copy; copyright @ <?= date('Y'); ?> by <span>KU BOX</span> | all rights reserved! </p>
</footer>
