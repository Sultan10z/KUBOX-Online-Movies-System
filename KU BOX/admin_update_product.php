<?php
@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:login.php');
}

if (isset($_POST['update_product'])) {
   $pid = $_POST['pid'];
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);
   $category = $_POST['category'];
   $category = filter_var($category, FILTER_SANITIZE_STRING);
   $details = $_POST['details'];
   $details = filter_var($details, FILTER_SANITIZE_STRING);
   $duration = $_POST['duration'];
   $duration = filter_var($duration, FILTER_SANITIZE_STRING);
   $production = $_POST['production'];
   $production = filter_var($production, FILTER_SANITIZE_STRING);
   $rating = $_POST['rating'];
   $rating = filter_var($rating, FILTER_SANITIZE_STRING);
   $address = $_POST['address'];
   $address = filter_var($address, FILTER_SANITIZE_STRING);
   $availability = $_POST['availability'];
   $availability = filter_var($availability, FILTER_SANITIZE_STRING);

   $update_product = $conn->prepare("UPDATE `products` SET name = ?, category = ?, details = ?, price = ?, duration = ?, production = ?, rating = ?, address = ?, availability = ? WHERE id = ?");
   $update_product->execute([$name, $category, $details, $price, $duration, $production, $rating, $address, $availability, $pid]);


   $message[] = 'Product updated successfully!';

   if (!empty($_FILES['image']['name'])) {
      $image = $_FILES['image']['name'];
      $image = filter_var($image, FILTER_SANITIZE_STRING);
      $image_size = $_FILES['image']['size'];
      $image_tmp_name = $_FILES['image']['tmp_name'];
      $image_folder = 'uploaded_img/' . $image;
      $old_image = $_POST['old_image'];

      if ($image_size > 200000000) {
         $message[] = 'Image size is too large!';
      } else {
         $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
         $update_image->execute([$image, $pid]);

         if ($update_image) {
            move_uploaded_file($image_tmp_name, $image_folder);
            unlink('uploaded_img/' . $old_image);
            $message[] = 'Image updated successfully!';
         }
      }
   }
}

// Add crew member
if (isset($_POST['add_crew_member'])) {
   $movie_id = $_POST['movie_id'];
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $dob = $_POST['dob'];
   $dob = filter_var($dob, FILTER_SANITIZE_STRING);
   $type = $_POST['type'];
   $type = filter_var($type, FILTER_SANITIZE_STRING);

   $insert_crew = $conn->prepare("INSERT INTO `crew` (name, dob, type, movie_id) VALUES (?, ?, ?, ?)");
   $insert_crew->execute([$name, $dob, $type, $movie_id]);

   $message[] = 'Crew member added successfully!';
}



// Function to remove a crew member
if (isset($_GET['remove_crew_member'])) {
   $crew_id = $_GET['remove_crew_member'];
   $delete_crew = $conn->prepare("DELETE FROM `crew` WHERE id = ?");
   $delete_crew->execute([$crew_id]);
   header("Location: admin_update_product.php?update=" . $_GET['update']); // Redirect back to the update page
   exit();
}





// Update crew member details
if (isset($_POST['update_crew_member'])) {
   $crew_id = $_POST['crew_id'];
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $dob = $_POST['dob'];
   $dob = filter_var($dob, FILTER_SANITIZE_STRING);
   $type = $_POST['type'];
   $type = filter_var($type, FILTER_SANITIZE_STRING);

   $update_crew = $conn->prepare("UPDATE `crew` SET name = ?, dob = ?, type = ? WHERE id = ?");
   $update_crew->execute([$name, $dob, $type, $crew_id]);

   $message[] = 'Crew member updated successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Products</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Custom CSS -->
   <link rel="stylesheet" href="css/admin_style.css">
   <style>
      .center-container {
         display: flex;
         flex-direction: column;
         align-items: center;
         text-align: center;
         margin-top: 20px;
      }
   </style>
</head>

<body>
   <?php include 'admin_header.php'; ?>

   <section class="update-product">
      <h1 class="title">Update Product</h1>

      <?php
      $update_id = $_GET['update'];
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
      $select_products->execute([$update_id]);
      if ($select_products->rowCount() > 0) {
         while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
      ?>
            <form action="" method="post" enctype="multipart/form-data">
               <input type="hidden" name="old_image" value="<?= $fetch_products['image']; ?>">
               <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
               <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
               <select name="availability" class="box" required>
   <option value="Available" <?= $fetch_products['availability'] === 'Available' ? 'selected' : ''; ?>>Available</option>
   <option value="Not Available" <?= $fetch_products['availability'] === 'Not Available' ? 'selected' : ''; ?>>Not Available</option>
</select>
               <input type="text" name="name" placeholder="Enter product name" required class="box" value="<?= $fetch_products['name']; ?>">
               <input type="number" name="price" min="0" placeholder="Enter product price" required class="box" value="<?= $fetch_products['price']; ?>">
               <select name="category" class="box" required>
                  <option selected><?= $fetch_products['category']; ?></option>
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
               
               <textarea name="details" required placeholder="Enter product details" class="box" cols="30" rows="10"><?= $fetch_products['details']; ?></textarea>
               <input type="text" name="duration" placeholder="Enter duration" class="box" value="<?= $fetch_products['duration']; ?>">
               <input type="text" name="production" placeholder="Enter production" class="box" value="<?= $fetch_products['production']; ?>">
               <input type="text" name="rating" placeholder="Enter rating" class="box" value="<?= $fetch_products['rating']; ?>">
               <input type="text" name="address" placeholder="Enter address" class="box" value="<?= $fetch_products['address']; ?>">
               <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
               <div class="flex-btn">
                  <input type="submit" class="btn" value="Update Product" name="update_product">
                  <a href="admin_products.php" class="option-btn">Go Back</a>
               </div>
            </form>
      <?php
         }
      } else {
         echo '<p class="empty">No products found!</p>';
      }
      ?>


<h1 class="sub-title">Add Crew Member</h1>
      <!-- Add Crew Member Form -->
      <form action="" method="post">
         <input type="hidden" name="movie_id" value="<?= $update_id ?>">
         <input type="text" name="name" placeholder="Enter crew member name" required class="box">
         <input type="date" name="dob" placeholder="Enter crew member date of birth" required class="box">
         <select name="type" class="box" required>
            <option value="actor">Actor</option>
            <option value="director">Director</option>
            <option value="producer">Producer</option>
         </select>
         <input type="submit" class="btn" value="Add Crew Member" name="add_crew_member">
      </form>

      <h2 class="sub-title">Crew Members</h2>
      <?php
      $select_crew = $conn->prepare("SELECT * FROM `crew` WHERE movie_id = ?");
      $select_crew->execute([$update_id]);
      if ($select_crew->rowCount() > 0) {
         while ($fetch_crew = $select_crew->fetch(PDO::FETCH_ASSOC)) {
      ?>
            <div class="crew-member">
               <h1>Name: <?= $fetch_crew['name']; ?></h1>
               <h2>Date of Birth: <?= $fetch_crew['dob']; ?></h2>
               <h2>Type: <?= $fetch_crew['type']; ?></h2>
               <div class="crew-actions">
                  <button class="edit-btn" onclick="editCrewMember(<?= $fetch_crew['id']; ?>, '<?= $fetch_crew['name']; ?>', '<?= $fetch_crew['dob']; ?>', '<?= $fetch_crew['type']; ?>')"><i class="fas fa-edit"></i> Edit</button>
                  <a href="?update=<?= $update_id; ?>&remove_crew_member=<?= $fetch_crew['id']; ?>" class="remove-btn" onclick="return confirm('Are you sure you want to remove this crew member?')"><i class="fas fa-trash-alt"></i> Remove</a>
               </div>
            </div>
      <?php
         }
      } else {
         echo '<p class="empty">No crew members found!</p>';
      }
      ?>

      <!-- Edit Crew Member Form (Hidden by default) -->
      <form action="" method="post" id="editCrewMemberForm" style="display: none;">
         <input type="hidden" name="crew_id" id="crewId">
         <input type="text" name="name" id="crewName" placeholder="Enter crew member name" required class="box">
         <input type="date" name="dob" id="crewDOB" placeholder="Enter crew member date of birth" required class="box">
         <select name="type" id="crewType" class="box" required>
            <option value="actor">Actor</option>
            <option value="director">Director</option>
            <option value="producer">Producer</option>
         </select>
         <input type="submit" class="btn" value="Update Crew Member" name="update_crew_member">
         <button class="cancel-btn" onclick="cancelEdit()"><i class="fas fa-times"></i> Cancel</button>
      </form>
   </section>

   <script>
      function editCrewMember(id, name, dob, type) {
         document.getElementById("editCrewMemberForm").style.display = "block";
         document.getElementById("crewId").value = id;
         document.getElementById("crewName").value = name;
         document.getElementById("crewDOB").value = dob;
         document.getElementById("crewType").value = type;
      }

      function cancelEdit() {
         document.getElementById("editCrewMemberForm").style.display = "none";
      }
   </script>
</body>
</html>


