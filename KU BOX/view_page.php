<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};

if(isset($_POST['add_to_wishlist'])){

   $pid = $_POST['pid'];
   $pid = filter_var($pid, FILTER_SANITIZE_STRING);
   $p_name = $_POST['p_name'];
   $p_name = filter_var($p_name, FILTER_SANITIZE_STRING);
   $p_price = $_POST['p_price'];
   $p_price = filter_var($p_price, FILTER_SANITIZE_STRING);
   $p_image = $_POST['p_image'];
   $p_image = filter_var($p_image, FILTER_SANITIZE_STRING);

   $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
   $check_wishlist_numbers->execute([$p_name, $user_id]);

   $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
   $check_cart_numbers->execute([$p_name, $user_id]);

   if($check_wishlist_numbers->rowCount() > 0){
      $message[] = 'already added to wishlist!';
   }elseif($check_cart_numbers->rowCount() > 0){
      $message[] = 'already added to cart!';
   }else{
      $insert_wishlist = $conn->prepare("INSERT INTO `wishlist`(user_id, pid, name, price, image) VALUES(?,?,?,?,?)");
      $insert_wishlist->execute([$user_id, $pid, $p_name, $p_price, $p_image]);
      $message[] = 'added to wishlist!';
   }

}

if(isset($_POST['add_to_cart'])){

   $pid = $_POST['pid'];
   $pid = filter_var($pid, FILTER_SANITIZE_STRING);
   $p_name = $_POST['p_name'];
   $p_name = filter_var($p_name, FILTER_SANITIZE_STRING);
   $p_price = $_POST['p_price'];
   $p_price = filter_var($p_price, FILTER_SANITIZE_STRING);
   $p_image = $_POST['p_image'];
   $p_image = filter_var($p_image, FILTER_SANITIZE_STRING);
   $p_qty = $_POST['p_qty'];
   $p_qty = filter_var($p_qty, FILTER_SANITIZE_STRING);

   $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
   $check_cart_numbers->execute([$p_name, $user_id]);

   if($check_cart_numbers->rowCount() > 0){
      $message[] = 'already added to cart!';
   }else{

      $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
      $check_wishlist_numbers->execute([$p_name, $user_id]);

      if($check_wishlist_numbers->rowCount() > 0){
         $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE name = ? AND user_id = ?");
         $delete_wishlist->execute([$p_name, $user_id]);
      }

      $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
      $insert_cart->execute([$user_id, $pid, $p_name, $p_price, $p_qty, $p_image]);
      $message[] = 'added to cart!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>quick view</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>
<section class="quick-view">
   <h1 class="title">quick view</h1>

   <?php
   $pid = $_GET['pid'];
   $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
   $select_products->execute([$pid]);
   if ($select_products->rowCount() > 0) {
      while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
         // Check movie_type to determine the data source
         $movieType = $fetch_products['movie_type'];

         if ($movieType === 'new') {
            // Fetch movie details from the database
            $movieName = $fetch_products['name'];
            $movieOverview = $fetch_products['details'];
            $movieReleaseDate = $fetch_products['release_date'];
            $movieTrailer = $fetch_products['trailer'];
            $movieId = $fetch_products['id'];

            // Fetch crew details from the "crew" database
            $select_crew = $conn->prepare("SELECT * FROM `crew` WHERE movie_id = ?");
            $select_crew->execute([$movieId]);

            // Display movie details
            ?>
            <form action="" class="box" method="POST">
               <div class="price">$<span><?= $fetch_products['price']; ?></span>/-</div>
               <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
               <div class="name"><?= $fetch_products['name']; ?></div>
               <div class="details">
               <strong>Title:</strong> <?= $fetch_products['name']; ?><br>
                     <strong>Genre:</strong> <?=$fetch_products['category']; ?><br>
                     <strong>Overview:</strong> <?= $fetch_products['details']; ?><br>
                     <strong>Release Date:</strong> <?= $fetch_products['release_date']; ?><br>
                     <strong>Production:</strong> <span class="production-address" data-address="<?= $fetch_products['address']; ?>"><?= $fetch_products['production']; ?></span><br>

                     <strong>Rating:</strong> <?= $fetch_products['rating']; ?><br>
                     <strong>Runtime:</strong> <?= $fetch_products['duration'];?><br>
                     
                     <strong>Trailer:</strong> <a href="<?= $fetch_products['trailer']; ?>" target="_blank" class="btn trailer-btn">Watch Trailer</a><br>

                  <strong>Actors:</strong><br>
                  <?php
                  while ($fetch_crew = $select_crew->fetch(PDO::FETCH_ASSOC)) {
                     if ($fetch_crew['type'] === 'actor') {
                        $actorName = $fetch_crew['name'];
                        $actorDOB = $fetch_crew['dob'];
                        ?>
                        <div>
                           <span class="crew-name" data-dob="<?= $actorDOB; ?>"><?= $actorName; ?></span>
                        </div>
                        <?php
                     }
                  }
                  ?>
                  <br><br>
                  <strong>Producers:</strong><br>
                  <?php
                  $select_crew->execute([$movieId]); // Reset cursor position
                  while ($fetch_crew = $select_crew->fetch(PDO::FETCH_ASSOC)) {
                     if ($fetch_crew['type'] === 'producer') {
                        $producerName = $fetch_crew['name'];
                        $producerDOB = $fetch_crew['dob'];
                        ?>
                        <div>
                           <span class="crew-name" data-dob="<?= $producerDOB; ?>"><?= $producerName; ?></span>
                        </div>
                        <?php
                     }
                  }
                  ?>
                  <br>
                  <strong>Directors:</strong><br>
                  <?php
                  $select_crew->execute([$movieId]); // Reset cursor position
                  while ($fetch_crew = $select_crew->fetch(PDO::FETCH_ASSOC)) {
                     if ($fetch_crew['type'] === 'director') {
                        $directorName = $fetch_crew['name'];
                        $directorDOB = $fetch_crew['dob'];

                        ?>
                        <div>
                           <span class="crew-name" data-dob="<?= $directorDOB; ?>"><?= $directorName; ?></span>
                        </div>
                        <?php
                     }
                  }
                  ?>
                  
                 
               </div>
               <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
               <input type="hidden" name="p_name" value="<?= $fetch_products['name']; ?>">
               <input type="hidden" name="p_price" value="<?= $fetch_products['price']; ?>">
               <input type="hidden" name="p_image" value="<?= $fetch_products['image']; ?>">
               <?php
               $availability = $fetch_products['availability'];
               if ($availability === "Available") {
               ?>
                  <input type="number" min="1" value="1" name="p_qty" class="qty">

                  <input type="submit" value="add to wishlist" class="option-btn" name="add_to_wishlist">
                  <input type="submit" value="add to cart" class="btn" name="add_to_cart">

               <?php
               } else {
               ?>
                  <input type="submit" value="add to wishlist" class="option-btn" name="add_to_wishlist">

                  <input type="submit" value="Not Available" class="cancel-btn" disabled>
               <?php
               }
               ?>
            </form>
         <?php
         } else {
            // Get movie details from TMDb API
            $movieName = $fetch_products['name'];
            $apiKey = '5e7ae9ad7ec9a2362c54b1fda331f945'; // Replace with your actual API key
            $apiUrl = "https://api.themoviedb.org/3/search/movie?api_key=$apiKey&query=" . urlencode($movieName);
   
            $response = file_get_contents($apiUrl);
            $result = json_decode($response, true);
   
            if ($result['total_results'] > 0) {
               $movie = $result['results'][0]; // Get the first movie from the results
   
               // Display movie details
               ?>
               <form action="" class="box" method="POST">
                  <div class="price">$<span><?= $fetch_products['price']; ?></span>/-</div>
                  <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
                  <div class="name"><?= $fetch_products['name']; ?></div>
                  <div class="details">
                     <strong>Title:</strong> <?= $movie['title']; ?><br>
                     <strong>Genre:</strong> <?=$fetch_products['category']; ?><br>
                     <strong>Overview:</strong> <?= $movie['overview']; ?><br>
                     <strong>Release Date:</strong> <?= $movie['release_date']; ?><br>
                     <strong>Rating:</strong> <?= $movie['vote_average']; ?><br>
                     <strong>Runtime:</strong> <?= $fetch_products['duration'];?><br>


                     <strong>Actors:</strong><br>
                     <?php
                     // Get movie credits from TMDb API
                     $movieId = $movie['id'];
                     $creditsUrl = "https://api.themoviedb.org/3/movie/$movieId/credits?api_key=$apiKey";
                     $creditsResponse = file_get_contents($creditsUrl);
                     $creditsResult = json_decode($creditsResponse, true);
   
                     if (isset($creditsResult['cast']) && count($creditsResult['cast']) > 0) {
                        $cast = $creditsResult['cast'];
                        $limit = 5; // Display up to 5 actors
                        $count = min(count($cast), $limit); // Handle cases where there are fewer actors than the limit
   
                        for ($i = 0; $i < $count; $i++) {
                           $actor = $cast[$i];
                           $actorName = $actor['name'];
                           $actorProfilePath = $actor['profile_path'];
                           $actorImage = "https://image.tmdb.org/t/p/w185/$actorProfilePath";
                           ?>
                           <a href="https://www.themoviedb.org/person/<?= $actor['id']; ?>" target="_blank">
                              <img src="<?= $actorImage; ?>" alt="<?= $actorName; ?>" title="<?= $actorName; ?>" class="actor-image">
                           </a>
                           <?php
                        }
                     }
                     ?>
                     <br><br>
                     <strong>Producers:</strong><br>
                     <?php
if (isset($creditsResult['crew']) && count($creditsResult['crew']) > 0) {
    $productionCompanies = $creditsResult['crew'];

    // Sort the production companies based on their popularity in descending order
    usort($productionCompanies, function($a, $b) {
        return $b['popularity'] - $a['popularity'];
    });

    // Get the top 3 producers
    $topProducers = array_slice($productionCompanies, 0, 3);

    foreach ($topProducers as $company) {
        $companyName = $company['name'];
        ?>
        <a href="https://www.themoviedb.org/person/<?= $company['id']; ?>" target="_blank" class="producer"><?= $companyName; ?></a><br>
        <?php
    }
}
?>

                     
                     <br>
                     <strong>Directors:</strong><br>
                     <?php
                     // Get movie crew from TMDb API
                     if (isset($creditsResult['crew']) && count($creditsResult['crew']) > 0) {
                        $crew = $creditsResult['crew'];
   
                        foreach ($crew as $crewMember) {
                           if ($crewMember['job'] === 'Director') {
                              $directorName = $crewMember['name'];
                              ?>
                              <a href="https://www.themoviedb.org/person/<?= $crewMember['id']; ?>" target="_blank" class="director"><?= $directorName; ?></a><br>
                              <?php
                           }
                        }
                     }
                     ?>
                     <br>
                     <strong>Trailers:</strong><br>
                     <?php
                     // Get movie videos from TMDb API
                     $videosUrl = "https://api.themoviedb.org/3/movie/$movieId/videos?api_key=$apiKey";
                     $videosResponse = file_get_contents($videosUrl);
                     $videosResult = json_decode($videosResponse, true);
   
                     if (isset($videosResult['results']) && count($videosResult['results']) > 0) {
                        $trailers = $videosResult['results'];
   
                        foreach ($trailers as $trailer) {
                           if ($trailer['type'] === 'Trailer') {
                              $trailerName = $trailer['name'];
                              $trailerKey = $trailer['key'];
                              $trailerUrl = "https://www.youtube.com/watch?v=$trailerKey";
                              ?>
                              <a href="<?= $trailerUrl; ?>" target="_blank" class="btn trailer-btn"><?= $trailerName; ?></a>
                              <?php
                           }
                        }
                     } else {
                        echo "No trailers found.";
                     }
                     
                     ?>
                  </div>
                  <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
                  <input type="hidden" name="p_name" value="<?= $fetch_products['name']; ?>">
                  <input type="hidden" name="p_price" value="<?= $fetch_products['price']; ?>">
                  <input type="hidden" name="p_image" value="<?= $fetch_products['image']; ?>">
                  <?php
            $availability = $fetch_products['availability'];
            if ($availability === "Available") {
         ?>
               <input type="number" min="1" value="1" name="p_qty" class="qty">
   
            <input type="submit" value="add to wishlist" class="option-btn" name="add_to_wishlist">
            <input type="submit" value="add to cart" class="btn" name="add_to_cart">
            
         <?php
            } else {
         ?>
                  <input type="submit" value="add to wishlist" class="option-btn" name="add_to_wishlist">
   
            <input type="submit" value="Not Available" class="cancel-btn" disabled>
         <?php
            }
         ?>
      </form>
      <?php
   
            } else {
               // Movie not found in TMDb API
               ?>
               <form action="" class="box" method="POST">
                  <div class="price">$<span><?= $fetch_products['price']; ?></span>/-</div>
                  <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
                  <div class="name"><?= $fetch_products['name']; ?></div>
                  <div class="details">Movie details not found.</div>
                  <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
                  <input type="hidden" name="p_name" value="<?= $fetch_products['name']; ?>">
                  <input type="hidden" name="p_price" value="<?= $fetch_products['price']; ?>">
                  <input type="hidden" name="p_image" value="<?= $fetch_products['image']; ?>">
                  <input type="number" min="1" value="1" name="p_qty" class="qty">
                  <input type="submit" value="add to wishlist" class="option-btn" name="add_to_wishlist">
                  <input type="submit" value="add to cart" class="btn" name="add_to_cart">
                  $availability = $fetch_products['availability'];
               </form>
            <?php
            }
         }}
      } else {
         echo '<p class="empty">no products added yet!</p>';
      }
      ?>
   </section>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
      // Add click event listener to crew member names
      var crewNames = document.getElementsByClassName("crew-name");
   for (var i = 0; i < crewNames.length; i++) {
      crewNames[i].addEventListener("click", function() {
         var dob = this.getAttribute("data-dob");
         alert("Date of Birth: " + dob); // Display DOB (you can customize this as needed)
      });
   }

   $(document).ready(function() {
  // Handle click on production address
  $(".production-address").click(function() {
    var address = $(this).data("address");
    // Perform action with the production address, e.g., display address in an alert
    if (address) {
      alert("Address: " + address);
    }
  });
});


$(document).ready(function() {
  // Handle click on actor image or name
  $(".actor-image, .actor").click(function() {
    var actorName = $(this).data("actor-name");
    // Perform action with actor name, e.g., display actor details
    if (actorName) {
      window.open("https://www.themoviedb.org/person/" + encodeURIComponent(actorName));
    }
  });

  // Handle click on producer
  $(".producer").click(function() {
    var producerName = $(this).data("producer-name");
    // Perform action with producer name, e.g., display producer details
    if (producerName) {
      window.open("https://www.themoviedb.org/search?query=" + encodeURIComponent(producerName));
    }
  });

  // Handle click on director
  $(".director").click(function() {
    var directorName = $(this).data("director-name");
    // Perform action with director name, e.g., display director details
    if (directorName) {
      window.open("https://www.themoviedb.org/search?query=" + encodeURIComponent(directorName));
    }
  });
});


</script>








<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>