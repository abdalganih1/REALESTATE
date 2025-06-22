<?php
require_once 'functions.php';

if (!check_login() || !is_admin()) {
    header('Location: login.php');
    exit();
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $price = $_POST['price'];
    $location = $_POST['location'];
    $bedrooms = $_POST['bedrooms'];
    $bathrooms = $_POST['bathrooms'];
    $area = $_POST['area'];
    $floor = $_POST['floor'];
    $parking = $_POST['parking'];
    $description = $_POST['description'];

    $image_url_to_save = 'assets/images/default-property.jpg'; // Default image

    // Check if an image file was uploaded
    if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] === UPLOAD_ERR_OK) {
        $uploaded_path = upload_image($_FILES['image_upload']);
        if ($uploaded_path) {
            $image_url_to_save = $uploaded_path;
        } else {
            $message = 'Error uploading image. Please ensure it\'s a JPG, PNG, or GIF file, and less than 5MB.';
            $message_type = 'warning'; // Use warning as property might still be added with default image
        }
    }

    if (add_property($type, $price, $location, $bedrooms, $bathrooms, $area, $floor, $parking, $description, $image_url_to_save)) {
        // If image upload failed but property added, combine messages
        if ($message_type == 'warning') {
            $message .= '<br>Property added successfully with a default image.';
            $message_type = 'success';
        } else {
            $message = 'Property added successfully!';
            $message_type = 'success';
        }
    } else {
        $message = 'Error adding property to database. Please try again.';
        $message_type = 'danger';
    }
}

// Get property types for the dropdown
$property_types = get_property_types();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title> Add New Property </title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-villa-agency.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet"href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <style>
        .form-container {
            padding: 50px 0;
            min-height: 80vh;
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #1e1e1e;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #f35525;
            border-color: #f35525;
        }
        .btn-primary:hover {
            background-color: #e04a1f;
            border-color: #e04a1f;
        }
    </style>
</head>
<body>
    <div id="js-preloader" class="js-preloader">
      <div class="preloader-inner">
        <span class="dot"></span>
        <div class="dots">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
    </div>
    <div class="sub-header">
      <div class="container">
        <div class="row">
          <div class="col-lg-8 col-md-8">
            <ul class="info">
              <li><i class="fa fa-envelope"></i> info@realestate.com</li>
              <li><i class="fa fa-map"></i> HAMA</li>
            </ul>
          </div>
          <div class="col-lg-4 col-md-4">
            <ul class="social-links">
              <li><a href="#"><i class="fab fa-facebook"></i></a></li>
              <li><a href="https://x.com/minthu" target="_blank"><i class="fab fa-twitter"></i></a></li>
              <li><a href="#"><i class="fab fa-linkedin"></i></a></li>
              <li><a href="#"><i class="fab fa-instagram"></i></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <header class="header-area header-sticky">
      <div class="container">
          <div class="row">
              <div class="col-12">
                  <nav class="main-nav">
                      <a href="index.php" class="logo">
                          <h1>Villa</h1>
                      </a>
                      <ul class="nav">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="properties.php">Properties</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                        <?php if (!check_login()): ?>
                          <li><a href="login.php"><i class="fa fa-user"></i> Login / Register</a></li>
                        <?php else: ?>
                          <?php if (is_admin()): ?>
                              <li><a href="admin_dashboard.php" class="active"><i class="fa fa-cog"></i> Admin Dashboard</a></li>
                          <?php else: ?>
                              <li><a href="my_orders.php"><i class="fa fa-list"></i> My Orders</a></li>
                          <?php endif; ?>
                          <li><a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout (<?php echo $_SESSION['username']; ?>)</a></li>
                        <?php endif; ?>
                    </ul>   
                      <a class='menu-trigger'>
                          <span>Menu</span>
                      </a>
                  </nav>
              </div>
          </div>
      </div>
    </header>

    <div class="container form-container">
        <h2>Add New Property</h2>
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form action="add_property.php" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="type">Property Type</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="">Select Type</option>
                            <?php foreach ($property_types as $type_option): ?>
                                <option value="<?php echo htmlspecialchars($type_option['type_name']); ?>"><?php echo htmlspecialchars($type_option['type_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Price ($)</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" class="form-control" id="location" name="location" placeholder="e.g., HAMA,ALBRNAWE" required>
                    </div>
                    <div class="form-group">
                        <label for="bedrooms">Bedrooms</label>
                        <input type="number" class="form-control" id="bedrooms" name="bedrooms" required>
                    </div>
                    <div class="form-group">
                        <label for="bathrooms">Bathrooms</label>
                        <input type="number" class="form-control" id="bathrooms" name="bathrooms" required>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="area">Area (m2)</label>
                        <input type="number" step="0.01" class="form-control" id="area" name="area" required>
                    </div>
                    <div class="form-group">
                        <label for="floor">Floor</label>
                        <input type="text" class="form-control" id="floor" name="floor" placeholder="e.g., 3 or 25th" required>
                    </div>
                    <div class="form-group">
                        <label for="parking">Parking</label>
                        <input type="text" class="form-control" id="parking" name="parking" placeholder="e.g., 6 spots or 2 cars" required>
                    </div>
                    <div class="form-group">
                        <label for="image_upload">Upload Image</label>
                        <input type="file" class="form-control" id="image_upload" name="image_upload" accept="image/*">
                        <small class="form-text text-muted">Max 5MB (JPG, PNG, GIF). If no image is uploaded, a default will be used.</small>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                    </div>
                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary w-100">Add Property</button>
                </div>
            </div>
        </form>
        <div class="text-center mt-3">
            <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <footer>
      <div class="container">
        <div class="col-lg-12">
          <p>Copyright © 2024 Real Estate Co., Ltd. All rights reserved. 
          </p>
        </div>
      </div>
    </footer>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/isotope.min.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/counter.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>