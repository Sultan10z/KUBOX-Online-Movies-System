<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="about">

   <div class="row">

      <div class="box">
         <img src="images/about-img-1.png" alt="">
         <h3>why choose us?</h3>
         <p>Unmatched movie selection, effortless browsing, and secure purchasing – your ultimate destination for cinematic bliss.</p>
           <?php
         $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
         $select_profile->execute([$user_id]);
         $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         if ($fetch_profile && $fetch_profile['id'] != 1003) {
            echo '  <a href="contact.php" class="btn">contact us</a>
            ';
         }
         ?>
      </div>

      <div class="box">
         <img src="images/about-img-2.png" alt="">
         <h3>what we provide?</h3>
         <p>At our website, we provide concise and comprehensive information about available movies for sale.</p>
         <a href="shop.php" class="btn">our shop</a>
      </div>

   </div>

</section>

<section class="reviews">

   <h1 class="title">clients reivews</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/user.png" alt="">
         <p>I stumbled upon this website while looking for a new movie to watch, and I'm so glad I did! The information provided about the movie I purchased was spot-on, and the overall user experience was fantastic. The website's design is sleek and easy to navigate. I highly recommend this site to all movie enthusiasts!</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
         </div>
         <h3>Ahmed</h3>
      </div>

      <div class="box">
         <img src="images/user.png" alt="">
         <p>I've been using this website for a while now, and it has become my go-to source for movie information. The details provided about the movies are comprehensive and accurate. I appreciate the option to read user reviews as well, which helps me make informed decisions before purchasing. Great job!</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
           
         </div>
         <h3>Cinemaniac</h3>
      </div>

      <div class="box">
         <img src="images/user.png" alt="">
         <p>As a movie lover, I'm always on the lookout for new releases, and this website has become my trusted companion. The layout is clean and intuitive, and the movie listings are regularly updated. The added bonus of being able to create a watchlist and receive personalized recommendations takes the user experience to another level.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Abdullah</h3>
      </div>

   

   </div>

</section>









<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>