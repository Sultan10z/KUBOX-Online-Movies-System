<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:login.php');
   exit;
};

if (isset($_POST['add_product'])) {
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);
   $category = $_POST['category'];
   $category = filter_var($category, FILTER_SANITIZE_STRING);
   $details = $_POST['details'];
   $details = filter_var($details, FILTER_SANITIZE_STRING);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/' . $image;
   $duration = $_POST['duration'];

   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_products->execute([$name]);
   $movie_type = $_POST['movie-type'];


   if ($select_products->rowCount() > 0) {
      $message[] = 'Product name already exists!';
   } else {
      if ($_POST['movie-type'] === 'new') {
         $release_date = $_POST['release_date'];
         $rating = $_POST['rating'];
         $production = $_POST['production'];
         $address = $_POST['address'];
         $trailer = $_POST['trailer'];


         $insert_products = $conn->prepare("INSERT INTO `products`(name, category, details,release_date, price, image, duration, production, rating, address, trailer,movie_type) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
         $insert_products->execute([$name, $category, $details,$release_date, $price, $image, $duration, $production, $rating, $address, $trailer,$movie_type]);

         if ($insert_products) {
            if ($image_size > 200000000) {
               $message[] = 'Image size is too large!';
            } else {
               move_uploaded_file($image_tmp_name, $image_folder);
               $message[] = 'New product added!';
            }

            // Insert crew members
            $crewTypes = $_POST['crew_type'];
            $crewNames = $_POST['crew_name'];
            $crewDOBs = $_POST['crew_dob'];

            $movieId = $conn->lastInsertId();
            $insert_crew = $conn->prepare("INSERT INTO `crew`(name, dob, type, movie_id) VALUES (?,?,?,?)");

            for ($i = 0; $i < count($crewTypes); $i++) {
               $crewType = filter_var($crewTypes[$i], FILTER_SANITIZE_STRING);
               $crewName = filter_var($crewNames[$i], FILTER_SANITIZE_STRING);
               $crewDOB = filter_var($crewDOBs[$i], FILTER_SANITIZE_STRING);

               $insert_crew->execute([$crewName, $crewDOB, $crewType, $movieId]);
            }
         }

} else{
   $insert_products = $conn->prepare("INSERT INTO `products`(name, category, details, price,duration, image, movie_type) VALUES(?,?,?,?,?,?,?)");
   $insert_products->execute([$name, $category, $details, $price,$duration, $image, $movie_type]);

   if ($insert_products) {
       if ($image_size > 200000000) {
           $message[] = 'Image size is too large!';
       } else {
           move_uploaded_file($image_tmp_name, $image_folder);
           $message[] = 'Existing product added!';
       }
   }
}

      



   }
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];

   // Check if the product id exists in the 'crew' table
   $select_crew_movie = $conn->prepare("SELECT COUNT(*) FROM `crew` WHERE movie_id = ?");
   $select_crew_movie->execute([$delete_id]);
   $crew_count = $select_crew_movie->fetchColumn();

   if ($crew_count > 0) {
      // Delete crew members from 'crew' table
      $delete_crew = $conn->prepare("DELETE FROM `crew` WHERE movie_id = ?");
      $delete_crew->execute([$delete_id]);
   }

   // Delete product from 'products' table
   $delete_products = $conn->prepare("DELETE FROM `products` WHERE id = ?");
   $delete_products->execute([$delete_id]);

   header('location:admin_products.php');
   exit;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Products</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">


</head>

<body>

   <?php include 'admin_header.php'; ?>

   <section class="add-products">

      <h1 class="title">Add New Movie</h1>

      <form action="" method="POST" enctype="multipart/form-data">
         <div class="flex">
            <div class="inputBox">
               <input type="text" name="name" class="box" required placeholder="Enter movie name">
               <select name="category" class="box" required>
                  <option value="" selected disabled>Select genre</option>
                  <option value="action">Action</option>
                  <option value="comedy">Comedy</option>
                  <option value="drama">Drama</option>
                  <option value="fantasy">Fantasy</option>
                  <option value="horror">Horror</option>
                  <option value="mystery">Mystery</option>
                  <option value="romance">Romance</option>
                  <option value="thriller">Thriller</option>
                  <option value="animation">Animation</option>
                  <option value="adventure">Adnventure</option>

               </select>
            </div>
            <div class="inputBox">
               <input type="number" min="0" name="price" class="box" required placeholder="Enter product price">
               <input type="file" name="image" required class="box" accept="image/jpg, image/jpeg, image/png">
               <input type="text" name="duration" class="box" required placeholder="Enter movie duration">

            </div>
         </div>
         <textarea name="details" class="box" required placeholder="Enter product details" cols="30" rows="10"></textarea>

         <div class="flex">
            <div class="inputBox">
               <h1 for="movie-type">Movie Type:</h1>
               <select name="movie-type" id="movie-type" class="box" required>
                  <option value="" selected disabled>Select movie type</option>
                  <option value="new">New</option>
                  <option value="existing">Existing</option>
               </select>
            </div>
         </div>

         <div id="movie-details" style="display: none;">
         
            <div class="flex">
               <div class="inputBox">
                  <input type="date" name="release_date" placeholder="Enter the release date" required class="box">
                  <input type="text" name="rating" class="box" required placeholder="Enter movie rating">
               </div>
               <div class="inputBox">
                  <input type="text" name="production" class="box" required placeholder="Enter production">
                  <input type="text" name="address" class="box" required placeholder="Enter production address">
                  <input type="text" name="trailer" class="box" required placeholder="Enter trailer URL">

               </div>

            </div>

            <div class="crew-section">
               <h2>Add Crew Members</h2>
               <div id="crew-container">
                  <div class="crew-row">
                     <div class="inputBox">
                        <select name="crew_type[]" class="box" required>
                           <option value="" selected disabled>Select crew type</option>
                           <option value="actor">Actor</option>
                           <option value="director">Director</option>
                           <option value="producer">Producer</option>
                        </select>
                     </div>
                     <div class="inputBox">
                        <input type="text" name="crew_name[]" class="box" required placeholder="Enter crew name">
                     </div>
                     <div class="inputBox">
                     <input type="date" name="crew_dob[]"  placeholder="Enter crew member date of birth" required class="box">

                     </div>
                     <div class="inputBox">
                        <button type="button" class="btn btn-remove-crew" onclick="removeCrewMember(this)">Remove</button>
                     </div>
                  </div>
               </div>
               <button type="button" class="btn" onclick="addCrewMember()">Add Crew Member</button>
            </div>
         </div>

         <input type="submit" class="btn" value="Add Product" name="add_product">
      </form>

   </section>

   <section class="show-products">

      <h1 class="title">Products Added</h1>

      <div class="box-container">

         <?php
         $show_products = $conn->prepare("SELECT * FROM `products`");
         $show_products->execute();
         if ($show_products->rowCount() > 0) {
            while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
         ?>
               <div class="box">
                  <div class="price">$<?= $fetch_products['price']; ?>/-</div>
                  <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
                  <div class="name"><?= $fetch_products['name']; ?></div>
                  <div class="cat"><?= $fetch_products['category']; ?></div>
                  <div class="details"><?= $fetch_products['details']; ?></div>
                  <div class="flex-btn">
                     <a href="admin_update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">update</a>
                     <a href="admin_products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Delete this product?');">delete</a>
                  </div>
               </div>
         <?php
            }
         } else {
            echo '<p class="empty">No products added yet!</p>';
         }
         ?>

      </div>

   </section>

   <script>
      function addCrewMember() {
         const crewContainer = document.getElementById('crew-container');
         const crewRow = document.createElement('div');
         crewRow.className = 'crew-row';
         crewRow.innerHTML = `
            <div class="inputBox">
               <select name="crew_type[]" class="box" required>
                  <option value="" selected disabled>Select crew type</option>
                  <option value="actor">Actor</option>
                  <option value="director">Director</option>
                  <option value="producer">Producer</option>
               </select>
            </div>
            <div class="inputBox">
               <input type="text" name="crew_name[]" class="box" required placeholder="Enter crew name">
            </div>
            <div class="inputBox">
               <input type="text" name="crew_dob[]" class="box" required placeholder="Enter crew DOB">
            </div>
            <div class="inputBox">
               <button type="button" class="btn btn-remove-crew" onclick="removeCrewMember(this)">Remove</button>
            </div>
         `;
         crewContainer.appendChild(crewRow);
      }

      function removeCrewMember(button) {
         const crewRow = button.closest('.crew-row');
         crewRow.remove();
      }

      window.addEventListener('DOMContentLoaded', function() {
         const typeSelect = document.getElementById('movie-type');
         const movieDetails = document.getElementById('movie-details');
         const durationInput = document.querySelector('input[name="duration"]');
         const ratingInput = document.querySelector('input[name="rating"]');
         const productionInput = document.querySelector('input[name="production"]');
         const addressInput = document.querySelector('input[name="address"]');
         const trailerInput = document.querySelector('input[name="trailer"]');
         const releaseDate = document.querySelector('input[name="release_date"]');

         const crewRows = document.querySelectorAll('.crew-row');

         // Check initial value
         if (typeSelect.value === 'existing') {
            movieDetails.style.display = 'none';
            durationInput.required = true;
            ratingInput.required = false;
            productionInput.required = false;
            addressInput.required = false;
            trailerInput.required = false;
            releaseDate.required=false;
            crewRows.forEach(row => {
               const crewTypeInput = row.querySelector('select[name="crew_type[]"]');
               const crewNameInput = row.querySelector('input[name="crew_name[]"]');
               const crewDobInput = row.querySelector('input[name="crew_dob[]"]');

               crewTypeInput.required = false;
               crewNameInput.required = false;
               crewDobInput.required = false;
            });
         }

         // Event listener for change
         typeSelect.addEventListener('change', function() {
            if (this.value === 'new') {
               movieDetails.style.display = 'block';
               durationInput.required = true;
               ratingInput.required = true;
               productionInput.required = true;
               addressInput.required = true;
               trailerInput.required = true;
               
               crewRows.forEach(row => {
                  const crewTypeInput = row.querySelector('select[name="crew_type[]"]');
                  const crewNameInput = row.querySelector('input[name="crew_name[]"]');
                  const crewDobInput = row.querySelector('input[name="crew_dob[]"]');

                  crewTypeInput.required = true;
                  crewNameInput.required = true;
                  crewDobInput.required = true;
               });
            } else {
               movieDetails.style.display = 'none';
               durationInput.required = true;
               ratingInput.required = false;
               productionInput.required = false;
               addressInput.required = false;
               trailerInput.required = false;
               releaseDate.required=false;

               crewRows.forEach(row => {
                  const crewTypeInput = row.querySelector('select[name="crew_type[]"]');
                  const crewNameInput = row.querySelector('input[name="crew_name[]"]');
                  const crewDobInput = row.querySelector('input[name="crew_dob[]"]');

                  crewTypeInput.required = false;
                  crewNameInput.required = false;
                  crewDobInput.required = false;
               });
            }
         });
      });

   </script>

</body>

</html>
